<?php
namespace App\Controller;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Entity\Produk;
use App\Repository\ProdukRepository;

/**
 * Use case #6 Mengelola Keranjang Belanja (FR-06).
 * Keranjang disimpan pada session PHP ($_SESSION['keranjang'] = [idProduk => jumlah]);
 * harga dan stok selalu diperiksa ulang dari basis data.
 */
class KeranjangController
{
    private ProdukRepository $produkRepo;

    public function __construct()
    {
        $this->produkRepo = new ProdukRepository();
    }

    /** UI-1 Halaman Keranjang Belanja. */
    public function index(): void
    {
        $isi = $this->getIsiKeranjang();
        Response::view('pelanggan/keranjang/index', [
            'title' => 'Keranjang Belanja',
            'isi'   => $isi,
        ]);
    }

    /**
     * Isi keranjang beserta harga & stok terkini.
     * @return array{items: array, total: float, jumlahUnit: int, adaMasalah: bool}
     */
    public function getIsiKeranjang(): array
    {
        $keranjang = self::keranjang();
        $produk = $this->produkRepo->getHargaByIds(array_keys($keranjang));
        $items = [];
        $total = 0.0;
        $unit = 0;
        $adaMasalah = false;

        foreach ($keranjang as $id => $jumlah) {
            /** @var Produk|null $p */
            $p = $produk[$id] ?? null;
            $masalah = null;
            if ($p === null) {
                $masalah = 'Produk sudah tidak tersedia. Hapus dari keranjang untuk melanjutkan.';
            } elseif (!$p->getAktif()) {
                $masalah = 'Produk sedang tidak tersedia. Hapus dari keranjang untuk melanjutkan.';
            } elseif ($p->getStok() <= 0) {
                $masalah = 'Stok habis. Hapus dari keranjang untuk melanjutkan.';
            } elseif ($jumlah > $p->getStok()) {
                $masalah = 'Stok tidak mencukupi (tersisa ' . $p->getStok() . ' unit). Kurangi jumlah.';
            }
            $subtotal = $p ? $p->getHarga() * $jumlah : 0.0;
            if ($masalah === null) {
                $total += $subtotal;
                $unit += $jumlah;
            } else {
                $adaMasalah = true;
            }
            $items[] = ['idProduk' => $id, 'produk' => $p, 'jumlah' => $jumlah, 'subtotal' => $subtotal, 'masalah' => $masalah];
        }
        return ['items' => $items, 'total' => $total, 'jumlahUnit' => $unit, 'adaMasalah' => $adaMasalah];
    }

    public function hitungTotal(): float
    {
        return $this->getIsiKeranjang()['total'];
    }

    /** POST /keranjang/tambah */
    public function tambahItem(): void
    {
        $id = (int) Request::post('idProduk');
        $jumlah = $this->jumlahValid(Request::post('jumlah', '1'));
        if ($jumlah === null) {
            $this->gagal('Jumlah harus berupa bilangan bulat minimal 1.');
        }
        $p = $id > 0 ? $this->produkRepo->findById($id, true) : null;
        if ($p === null) {
            $this->gagal('Produk tidak ditemukan atau sedang tidak tersedia.');
        }
        $keranjang = self::keranjang();
        $baru = ($keranjang[$id] ?? 0) + $jumlah;
        if ($baru > $p->getStok()) {
            $this->gagal('Stok tidak mencukupi.' . ($p->getStok() > 0 ? ' Tersisa ' . $p->getStok() . ' unit' . (isset($keranjang[$id]) ? ', ' . $keranjang[$id] . ' sudah di keranjang.' : '.') : ''));
        }
        $keranjang[$id] = $baru;
        $this->simpan($keranjang);
        $this->sukses($p->getNama() . ' ditambahkan ke keranjang.', '/keranjang');
    }

    /** POST /keranjang/ubah */
    public function ubahJumlah(): void
    {
        $id = (int) Request::post('idProduk');
        $keranjang = self::keranjang();
        if (!isset($keranjang[$id])) {
            $this->gagal('Item tidak ada di keranjang.');
        }
        $jumlah = $this->jumlahValid(Request::post('jumlah'));
        if ($jumlah === null) {
            $this->gagal('Jumlah harus berupa bilangan bulat minimal 1.');
        }
        $p = $this->produkRepo->findById($id, true);
        if ($p === null) {
            $this->gagal('Produk sudah tidak tersedia.');
        }
        if ($jumlah > $p->getStok()) {
            $this->gagal('Stok tidak mencukupi. Tersisa ' . $p->getStok() . ' unit.');
        }
        $keranjang[$id] = $jumlah;
        $this->simpan($keranjang);
        $this->sukses('Jumlah diperbarui.', '/keranjang');
    }

    /** POST /keranjang/hapus */
    public function hapusItem(): void
    {
        $id = (int) Request::post('idProduk');
        $keranjang = self::keranjang();
        unset($keranjang[$id]);
        $this->simpan($keranjang);
        $this->sukses('Item dihapus dari keranjang.', '/keranjang');
    }

    // ------------------------------------------------------------------

    /** @return array<int,int> */
    public static function keranjang(): array
    {
        $k = Session::get('keranjang', []);
        return is_array($k) ? $k : [];
    }

    public static function kosongkan(): void
    {
        Session::forget('keranjang');
    }

    private function simpan(array $keranjang): void
    {
        Session::set('keranjang', $keranjang);
    }

    private function jumlahValid(string $v): ?int
    {
        if (!preg_match('/^\d{1,4}$/', trim($v))) {
            return null;
        }
        $n = (int) $v;
        return $n >= 1 ? $n : null;
    }

    private function sukses(string $pesan, string $redirect): never
    {
        if (Request::wantsJson()) {
            $isi = $this->getIsiKeranjang();
            $items = [];
            foreach ($isi['items'] as $it) {
                $items[$it['idProduk']] = [
                    'jumlah' => $it['jumlah'], 'subtotal' => rupiah($it['subtotal']), 'masalah' => $it['masalah'],
                ];
            }
            Response::json('success', [
                'jumlahKeranjang' => jumlahKeranjang(),
                'total'           => rupiah($isi['total']),
                'jumlahUnit'      => $isi['jumlahUnit'],
                'adaMasalah'      => $isi['adaMasalah'],
                'kosong'          => $isi['items'] === [],
                'items'           => $items,
            ], $pesan);
        }
        Session::flash('sukses', $pesan);
        Request::post('kembali') === '1' ? Response::back($redirect) : Response::redirect($redirect);
    }

    private function gagal(string $pesan): never
    {
        if (Request::wantsJson()) {
            Response::json('error', ['jumlahKeranjang' => jumlahKeranjang()], $pesan, 422);
        }
        Session::flash('gagal', $pesan);
        Response::back('/keranjang');
    }
}
