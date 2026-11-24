<?php
namespace App\Controller;

use App\Core\AksesPesanan;
use App\Core\Config;
use App\Core\Csrf;
use App\Core\ErrorHandler;
use App\Core\RateLimiter;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Entity\DetailPesanan;
use App\Entity\Pesanan;
use App\Repository\DetailPesananRepository;
use App\Repository\PesananRepository;
use App\Repository\ProdukRepository;

/**
 * PesananController (Progres Sprint 3)
 * - Checkout & validasi pemesanan + persetujuan privasi (PBI-22, PBI-54)
 * - Simpan pesanan & kurangi stok secara atomik dalam transaksi (PBI-23)
 * - Halaman konfirmasi pesanan ORD-nnnnnn & anti-spam (PBI-24, PBI-25)
 * - Tautan WhatsApp ke admin (PBI-26)
 */
class PesananController
{
    public const METODE_PEMBAYARAN = ['Transfer Bank', 'QRIS', 'COD'];

    private PesananRepository $pesananRepo;
    private DetailPesananRepository $detailRepo;
    private ProdukRepository $produkRepo;

    public function __construct()
    {
        $this->pesananRepo = new PesananRepository();
        $this->detailRepo = new DetailPesananRepository();
        $this->produkRepo = new ProdukRepository();
    }

    /** GET /checkout - UI-2 Form Data Pemesanan. */
    public function bukaCheckout(): void
    {
        $isi = (new KeranjangController())->getIsiKeranjang();
        if ($isi['items'] === []) {
            Session::flash('info', 'Keranjang belanja masih kosong. Tambahkan produk terlebih dahulu.');
            Response::redirect('/keranjang');
        }
        if ($isi['adaMasalah']) {
            Session::flash('gagal', 'Ada produk di keranjang yang tidak tersedia atau stoknya tidak mencukupi. Perbarui keranjang sebelum checkout.');
            Response::redirect('/keranjang');
        }
        Response::view('pelanggan/checkout/form', [
            'title'     => 'Checkout',
            'isi'       => $isi,
            'old'       => Session::pullOld(),
            'errors'    => Session::pullErrors(),
            'formToken' => Csrf::formToken('checkout'),
        ]);
    }

    /** Validasi input pemesanan (FR-20, FR-41). */
    public function validasiInput(array $data): Validator
    {
        $v = new Validator($data);
        $v->required('nama', 'Nama lengkap')->maxLength('nama', 100, 'Nama lengkap');
        $v->required('kontak', 'Nomor WhatsApp');
        if ($v->value('kontak') !== '' && normalisasiKontak($v->value('kontak')) === null) {
            $v->add('kontak', 'Nomor WhatsApp harus berupa angka 8–15 digit (contoh: 081234567890).');
        }
        $v->required('alamat', 'Alamat pengiriman')->maxLength('alamat', 1000, 'Alamat pengiriman');
        $v->required('metode', 'Metode pembayaran')->in('metode', self::METODE_PEMBAYARAN, 'Metode pembayaran');
        if (($data['setuju'] ?? '') !== '1') {
            $v->add('setuju', 'Anda perlu menyetujui Kebijakan Privasi untuk melanjutkan pesanan.');
        }
        return $v;
    }

    /** FR-22: total dihitung dari harga terkini basis data. */
    public function hitungTotalOtomatis(array $item, array $produk): float
    {
        $total = 0.0;
        foreach ($item as $id => $jumlah) {
            $total += $produk[$id]->getHarga() * $jumlah;
        }
        return round($total, 2);
    }

    public function formatKodePesanan(int $id): string
    {
        return kodePesanan($id);
    }

    /** POST /checkout - Algoritma #2 Membuat Pesanan dan Mengurangi Stok Otomatis. */
    public function buatPesanan(): void
    {
        $data = $_POST;
        $tolakSpam = function (): never {
            Session::flash('gagal', 'Pesanan tidak dapat diproses saat ini. Silakan coba beberapa saat lagi.');
            Response::redirect('/checkout');
        };

        // Honeypot anti-spam (PBI-25 / FR-31)
        if (trim((string) ($data['website'] ?? '')) !== '') {
            $tolakSpam();
        }
        // Cegah pengiriman form ganda dengan token sekali pakai
        if (!Csrf::consumeFormToken('checkout', (string) ($data['_form'] ?? ''))) {
            $terakhir = (int) Session::get('pesanan_terakhir', 0);
            if ($terakhir && AksesPesanan::punya($terakhir)) {
                Response::redirect('/pesanan/' . $terakhir . '/berhasil');
            }
            $tolakSpam();
        }
        // Batas frekuensi pesanan per sesi/alamat IP (PBI-25 / FR-31)
        $limiter = new RateLimiter('pesanan', (int) Config::get('toko.batas_pesanan', 3), 60 * (int) Config::get('toko.batas_pesanan_menit', 10));
        if ($limiter->terlampaui()) {
            Session::flashInput($data);
            Session::flash('gagal', 'Terlalu banyak pesanan dalam waktu singkat. Silakan coba beberapa saat lagi.');
            Response::redirect('/checkout');
        }

        // Validasi kelengkapan & format
        $v = $this->validasiInput($data);
        if ($v->fails()) {
            Session::flashInput($data);
            Session::flashErrors($v->errors());
            Session::flash('gagal', 'Pesanan gagal: data pemesanan belum lengkap atau tidak valid.');
            Response::redirect('/checkout');
        }
        $kontak = (string) normalisasiKontak($v->value('kontak'));

        // Pemeriksaan awal stok & ketersediaan dari basis data
        $item = KeranjangController::keranjang();
        if (!$item) {
            Session::flash('gagal', 'Keranjang belanja kosong.');
            Response::redirect('/keranjang');
        }
        ksort($item); // urutkan berdasarkan idProduk untuk menghindari deadlock
        $produk = $this->produkRepo->getHargaByIds(array_keys($item));
        foreach ($item as $id => $jumlah) {
            $p = $produk[$id] ?? null;
            if ($p === null || !$p->getAktif() || $jumlah > $p->getStok() || $jumlah < 1) {
                $nama = $p ? $p->getNama() : 'Salah satu produk';
                Session::flash('gagal', 'Pesanan gagal: stok tidak mencukupi atau produk tidak tersedia (' . $nama . '). Silakan perbarui keranjang.');
                Response::redirect('/keranjang');
            }
        }

        $total = $this->hitungTotalOtomatis($item, $produk);

        // Satu transaksi basis data: simpan pesanan + detail + kurangi stok atomik (PBI-23)
        $pdo = \App\Core\Database::get();
        try {
            $pdo->beginTransaction();

            $pesanan = new Pesanan();
            $pesanan->setNamaPelanggan($v->value('nama'));
            $pesanan->setKontak($kontak);
            $pesanan->setAlamat($v->value('alamat'));
            $pesanan->setStatus('Menunggu Konfirmasi');
            $pesanan->setTotalHarga($total);
            $idPesanan = $this->pesananRepo->save($pesanan);

            foreach ($item as $id => $jumlah) {
                $d = new DetailPesanan();
                $d->setIdPesanan($idPesanan);
                $d->setIdProduk($id);
                $d->setNamaProduk($produk[$id]->getNama());
                $d->setNamaKategori($produk[$id]->getNamaKategori());
                $d->setJumlah($jumlah);
                $d->hitungSubtotal($produk[$id]->getHarga());
                $this->detailRepo->save($d);
            }

            foreach ($item as $id => $jumlah) {
                if (!$this->produkRepo->kurangiStokAtomik($id, $jumlah)) {
                    $pdo->rollBack();
                    Session::flash('gagal', 'Pesanan gagal: stok "' . $produk[$id]->getNama() . '" tidak mencukupi untuk jumlah yang diminta. Silakan perbarui keranjang.');
                    Response::redirect('/keranjang');
                }
            }
            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            ErrorHandler::log($e);
            Session::flashInput($data);
            Session::flash('gagal', 'Pesanan gagal diproses. Silakan coba beberapa saat lagi.');
            Response::redirect('/checkout');
        }

        $limiter->catat();
        KeranjangController::kosongkan();
        AksesPesanan::beri($idPesanan, $kontak);
        Session::set('pesanan_terakhir', $idPesanan);
        Session::set('metode_pesanan_' . $idPesanan, $v->value('metode'));
        Response::redirect('/pesanan/' . $idPesanan . '/berhasil');
    }

    /** GET /pesanan/{id}/berhasil - UI-3 Notifikasi Pesanan Berhasil (PBI-24). */
    public function berhasil(int $id): void
    {
        if (!AksesPesanan::punya($id) || !($pesanan = $this->pesananRepo->findById($id))) {
            Response::redirect('/katalog');
        }
        $items = $this->detailRepo->findByPesanan($id);
        $metode = (string) Session::get('metode_pesanan_' . $id, 'Transfer Bank');
        Response::view('pelanggan/checkout/berhasil', [
            'title'    => 'Pesanan Berhasil',
            'pesanan'  => $pesanan,
            'items'    => $items,
            'metode'   => $metode,
            'tautanWA' => $this->buatTautanWA($pesanan, $items),
        ]);
    }

    /** FR-09 (PBI-26): tautan wa.me dengan pesan otomatis berisi ID & ringkasan pesanan. */
    public function buatTautanWA(Pesanan $pesanan, array $items): string
    {
        $baris = [];
        foreach ($items as $d) {
            /** @var DetailPesanan $d */
            $baris[] = '- ' . $d->getNamaProduk() . ' x' . $d->getJumlah() . ' = ' . rupiah($d->getSubtotal());
        }
        $pesan = "Halo Toko Abimanyu, saya ingin menanyakan/mengonfirmasi pesanan {$pesanan->getKodePesanan()}.\n"
            . "Nama: {$pesanan->getNamaPelanggan()}\n"
            . "Ringkasan pesanan:\n" . implode("\n", $baris) . "\n"
            . 'Total: ' . rupiah($pesanan->getTotalHarga());
        return waLink($pesan);
    }

    /** GET /kebijakan-privasi (PBI-54 / FR-41). */
    public function kebijakanPrivasi(): void
    {
        Response::view('pelanggan/halaman/kebijakan-privasi', ['title' => 'Kebijakan Privasi']);
    }
}
