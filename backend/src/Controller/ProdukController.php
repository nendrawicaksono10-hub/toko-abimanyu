<?php
namespace App\Controller;

use App\Core\Config;
use App\Core\ImageUploader;
use App\Core\Paginator;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Entity\Kategori;
use App\Entity\Produk;
use App\Repository\KategoriRepository;
use App\Repository\ProdukRepository;

/**
 * ProdukController
 * - Pelanggan: Beranda (FR-01), Katalog/Pencarian/Filter/Urutan (FR-02..04, FR-44), Detail (FR-05, FR-45)
 * - Admin: CRUD produk + foto (FR-13, FR-28, FR-42, FR-45), kategori (FR-14), atribut rekomendasi (FR-19)
 */
class ProdukController
{
    private ProdukRepository $produkRepo;
    private KategoriRepository $kategoriRepo;

    public function __construct()
    {
        $this->produkRepo = new ProdukRepository();
        $this->kategoriRepo = new KategoriRepository();
    }

    // ==================================================================
    // PELANGGAN
    // ==================================================================

    /** UI-14 Halaman Beranda. */
    public function getBeranda(): void
    {
        Response::view('pelanggan/beranda/index', [
            'title'           => 'Beranda',
            'produkUnggulan'  => $this->produkRepo->getProdukUnggulan(8),
            'kategoriPopuler' => $this->kategoriRepo->getKategoriPopuler(6),
        ]);
    }

    /** UI-15 Halaman Katalog Produk (katalog, pencarian, filter, urutan, pagination). */
    public function getKatalog(): void
    {
        $kataKunci = mb_substr(Request::query('q'), 0, 100);
        $urutan = Request::query('urut', 'terbaru');
        if (!array_key_exists($urutan, ProdukRepository::URUTAN)) {
            $urutan = 'terbaru'; // nilai di luar daftar memakai urutan bawaan
        }

        $idKategori = null;
        $kategoriAktif = null;
        if (isset($_GET['kategori']) && $_GET['kategori'] !== '') {
            $idKategori = Request::queryInt('kategori');
            $kategoriAktif = $idKategori ? $this->kategoriRepo->findById($idKategori) : null;
            if ($kategoriAktif === null) {
                Response::notFound('Kategori yang Anda pilih tidak ditemukan.');
            }
        }

        $total = $this->produkRepo->hitungAktif($kataKunci, $idKategori);
        $pg = new Paginator($total, (int) Config::get('toko.katalog_per_halaman', 12), Request::queryInt('hal'));
        $produk = $this->produkRepo->search($kataKunci, $idKategori, $urutan, $pg->perHalaman, $pg->offset);

        Response::view('pelanggan/katalog/index', [
            'title'         => 'Katalog',
            'produk'        => $produk,
            'kategori'      => $this->kategoriRepo->getAll(true),
            'kategoriAktif' => $kategoriAktif,
            'kataKunci'     => $kataKunci,
            'urutan'        => $urutan,
            'pg'            => $pg,
            'totalAktif'    => $this->produkRepo->hitungAktif(),
        ]);
    }

    /** UI-16 Halaman Detail Produk (PBI-13 / FR-05). */
    public function getDetail(int $id): void
    {
        $produk = $this->produkRepo->findById($id, true);
        if ($produk === null) {
            Response::notFound('Produk tidak ditemukan.');
        }
        Response::view('pelanggan/produk/detail', [
            'title'  => $produk->getNama(),
            'produk' => $produk,
        ]);
    }

    // ==================================================================
    // ADMIN - PRODUK (UI-8, UI-9)
    // ==================================================================

    /** UI-8 Halaman Manajemen Produk (20 baris per halaman). */
    public function getDaftarProduk(): void
    {
        $kataKunci = mb_substr(Request::query('q'), 0, 100);
        $total = $this->produkRepo->hitungSemua($kataKunci);
        $pg = new Paginator($total, (int) Config::get('toko.admin_per_halaman', 20), Request::queryInt('hal'));
        Response::view('admin/produk/index', [
            'title'     => 'Manajemen Produk',
            'produk'    => $this->produkRepo->getAll($kataKunci, $pg->perHalaman, $pg->offset),
            'kataKunci' => $kataKunci,
            'pg'        => $pg,
            'jumlah'    => $this->produkRepo->hitungProduk(),
        ], 'admin');
    }

    /** UI-9 Form Data Produk (mode tambah). */
    public function formTambah(): void
    {
        $this->tampilkanForm(null);
    }

    /** UI-9 Form Data Produk (mode ubah). */
    public function formUbah(int $id): void
    {
        $produk = $this->produkRepo->findById($id);
        if ($produk === null) {
            Response::notFound('Produk tidak ditemukan.');
        }
        $this->tampilkanForm($produk);
    }

    private function tampilkanForm(?Produk $produk): void
    {
        $kategori = $this->kategoriRepo->getAll();
        if (!$kategori) {
            Session::flash('peringatan', 'Tambahkan minimal satu kategori sebelum menambah produk.');
            Response::redirect('/admin/kategori');
        }
        Response::view('admin/produk/form', [
            'title'    => $produk ? 'Ubah Produk' : 'Tambah Produk',
            'produk'   => $produk,
            'kategori' => $kategori,
            'atribut'  => $this->opsiAtribut($produk),
            'old'      => Session::pullOld(),
            'errors'   => Session::pullErrors(),
        ], 'admin');
    }

    /** POST /admin/produk - tambah produk. */
    public function tambah(): void
    {
        $this->simpanProduk(null);
    }

    /** POST /admin/produk/{id} - ubah produk. */
    public function ubah(int $id): void
    {
        $lama = $this->produkRepo->findById($id);
        if ($lama === null) {
            Response::notFound('Produk tidak ditemukan.');
        }
        $this->simpanProduk($lama);
    }

    /**
     * Validasi input produk (FR-20, UI-9).
     */
    public function validasiInput(array $data, bool $atributSaja = false): Validator
    {
        $v = new Validator($data);
        $atr = Config::get('atribut');
        if (!$atributSaja) {
            $v->required('nama', 'Nama produk')->maxLength('nama', 150, 'Nama produk');
        }
        $v->required('idKategori', 'Kategori');
        if ($v->value('idKategori') !== '' && !$this->kategoriRepo->findById((int) $v->value('idKategori'))) {
            $v->add('idKategori', 'Kategori tidak valid.');
        }
        $v->required('ruangan', 'Ruangan')->in('ruangan', $this->gabungOpsi('ruangan'), 'Ruangan');
        $v->required('gaya', 'Gaya/Desain')->in('gaya', $this->gabungOpsi('gaya'), 'Gaya/Desain');
        $v->required('material', 'Material')->in('material', $this->gabungOpsi('material'), 'Material');
        $v->required('ukuran', 'Kategori ukuran')->in('ukuran', $atr['ukuran'], 'Kategori ukuran');
        $v->required('harga', 'Harga')->number('harga', 'Harga', 0, false, 9999999999.99);
        if (!$atributSaja) {
            foreach (['panjang' => 'Panjang', 'lebar' => 'Lebar', 'tinggi' => 'Tinggi'] as $k => $label) {
                $v->required($k, $label)->number($k, $label, 0, true, 9999.99);
            }
            $v->required('stok', 'Stok')->integer('stok', 'Stok', 0, 1000000);
            $v->maxLength('deskripsi', 5000, 'Deskripsi');
        }
        return $v;
    }

    private function simpanProduk(?Produk $lama): void
    {
        $data = $_POST;
        $v = $this->validasiInput($data);
        $kembali = $lama ? '/admin/produk/' . $lama->getIdProduk() . '/ubah' : '/admin/produk/tambah';

        // Validasi foto utama (PBI-11 / FR-28): opsional, JPG/PNG/WebP maks. 2 MB.
        $fotoUtama = Request::file('foto_utama');
        $adaFotoUtama = $fotoUtama && $fotoUtama['error'] !== UPLOAD_ERR_NO_FILE;
        if ($adaFotoUtama && ($err = ImageUploader::validate($fotoUtama, 'Foto utama'))) {
            $v->add('foto_utama', $err);
        }

        if ($v->fails()) {
            Session::flashInput($data);
            Session::flashErrors($v->errors());
            Session::flash('gagal', 'Data produk belum valid. Periksa kembali isian yang ditandai.');
            Response::redirect($kembali);
        }

        $dir = (string) Config::get('path.upload_produk');
        $berkasBaru = null;
        try {
            $p = $lama ?? new Produk();
            $p->setNama($v->value('nama'));
            $p->setIdKategori((int) $v->value('idKategori'));
            $p->setRuangan($v->value('ruangan'));
            $p->setGaya($v->value('gaya'));
            $p->setMaterial($v->value('material'));
            $p->setUkuran($v->value('ukuran'));
            $p->setPanjang((float) Validator::toNumber($v->value('panjang')));
            $p->setLebar((float) Validator::toNumber($v->value('lebar')));
            $p->setTinggi((float) Validator::toNumber($v->value('tinggi')));
            $p->setHarga((float) Validator::toNumber($v->value('harga')));
            $p->setStok((int) $v->value('stok'));
            $p->setDeskripsi($v->value('deskripsi') !== '' ? $v->value('deskripsi') : null);

            $fotoLama = $p->getFotoUrl();
            if ($adaFotoUtama) {
                $berkasBaru = ImageUploader::store($fotoUtama, $dir);
                $p->setFotoUrl($berkasBaru);
            }

            $this->produkRepo->beginTransaction();
            if ($lama) {
                $this->produkRepo->update($p);
                $id = (int) $p->getIdProduk();
            } else {
                $p->setAktif(true);
                $id = $this->produkRepo->save($p);
            }
            $this->produkRepo->commit();

            if ($adaFotoUtama && $fotoLama && $fotoLama !== $p->getFotoUrl()) {
                ImageUploader::delete($fotoLama, $dir);
            }
        } catch (\Throwable $e) {
            $this->produkRepo->rollback();
            if ($berkasBaru !== null) {
                ImageUploader::delete($berkasBaru, $dir);
            }
            \App\Core\ErrorHandler::log($e);
            Session::flashInput($data);
            Session::flash('gagal', 'Penyimpanan data gagal dilakukan. Data sebelumnya tetap dipertahankan.');
            Response::redirect($kembali);
        }

        $pesan = 'Produk "' . $p->getNama() . '" berhasil ' . ($lama ? 'diperbarui.' : 'ditambahkan.');
        Session::flash('sukses', $pesan);
        $ambang = (int) Config::get('toko.ambang_stok_menipis', 5);
        if ($p->getStok() < $ambang) {
            Session::flash('peringatan', 'Stok Menipis: ' . $p->getNama() . ' tersisa ' . $p->getStok() . ' unit (ambang ' . $ambang . ').');
        }
        Response::redirect($lama ? '/admin/produk/' . $id . '/ubah' : '/admin/produk');
    }

    /** POST /admin/produk/{id}/hapus - PBI-10 & PBI-52 */
    public function hapus(int $id): void
    {
        $p = $this->produkRepo->findById($id);
        if ($p === null) {
            Response::notFound('Produk tidak ditemukan.');
        }
        // PBI-10 & PBI-52: produk yang sudah pernah dipesan tidak dapat dihapus, arahkan ke Nonaktifkan.
        if ($this->produkRepo->punyaRiwayatPesanan($id)) {
            Session::flash('gagal', 'Produk "' . $p->getNama() . '" tidak dapat dihapus karena sudah tercatat pada riwayat pesanan. Gunakan tombol Nonaktifkan agar tidak tampil di katalog.');
            Response::back('/admin/produk');
        }
        try {
            $this->produkRepo->delete($id);
        } catch (\PDOException $e) {
            \App\Core\ErrorHandler::log($e);
            Session::flash('gagal', 'Produk gagal dihapus. Data tetap dipertahankan.');
            Response::back('/admin/produk');
        }
        $dir = (string) Config::get('path.upload_produk');
        ImageUploader::delete($p->getFotoUrl(), $dir);
        Session::flash('sukses', 'Produk "' . $p->getNama() . '" berhasil dihapus.');
        Response::redirect('/admin/produk');
    }

    /** POST /admin/produk/{id}/aktif - PBI-52 / FR-42 nonaktifkan / aktifkan kembali. */
    public function ubahStatusAktif(int $id): void
    {
        $p = $this->produkRepo->findById($id);
        if ($p === null) {
            Response::notFound('Produk tidak ditemukan.');
        }
        $aktif = Request::post('aktif') === '1';
        $this->produkRepo->updateAktif($id, $aktif);
        Session::flash('sukses', 'Produk "' . $p->getNama() . '" ' . ($aktif ? 'diaktifkan kembali dan tampil di katalog.' : 'dinonaktifkan dan tidak lagi tampil di katalog.'));
        Response::back('/admin/produk');
    }

    // ==================================================================
    // ADMIN - KATEGORI (UI-10)
    // ==================================================================

    /** UI-10 Halaman Manajemen Kategori. */
    public function kelolaKategori(): void
    {
        $edit = null;
        if ($id = Request::queryInt('ubah')) {
            $edit = $this->kategoriRepo->findById($id);
        }
        Response::view('admin/kategori/index', [
            'title'    => 'Manajemen Kategori',
            'kategori' => $this->kategoriRepo->getAll(),
            'edit'     => $edit,
            'old'      => Session::pullOld(),
            'errors'   => Session::pullErrors(),
        ], 'admin');
    }

    /** POST /admin/kategori - simpan kategori baru / perubahan. */
    public function simpanKategori(): void
    {
        $id = (int) Request::post('idKategori');
        $v = new Validator($_POST);
        $v->required('namaKategori', 'Nama kategori')->maxLength('namaKategori', 100, 'Nama kategori');
        $nama = preg_replace('/\s+/u', ' ', $v->value('namaKategori'));
        if (!$v->fails() && $this->kategoriRepo->namaDipakai($nama, $id ?: null)) {
            $v->add('namaKategori', 'Nama kategori "' . $nama . '" sudah digunakan kategori lain.');
        }
        if ($id && !$this->kategoriRepo->findById($id)) {
            Response::notFound('Kategori tidak ditemukan.');
        }
        if ($v->fails()) {
            Session::flashInput($_POST);
            Session::flashErrors($v->errors());
            Session::flash('gagal', $v->firstError());
            Response::redirect('/admin/kategori' . ($id ? '?ubah=' . $id : ''));
        }
        $k = new Kategori();
        $k->setNamaKategori($nama);
        try {
            if ($id) {
                $k->setIdKategori($id);
                $this->kategoriRepo->update($k);
            } else {
                $this->kategoriRepo->save($k);
            }
        } catch (\PDOException $e) {
            \App\Core\ErrorHandler::log($e);
            Session::flash('gagal', 'Kategori gagal disimpan. Silakan coba lagi.');
            Response::redirect('/admin/kategori');
        }
        Session::flash('sukses', 'Kategori "' . $nama . '" berhasil ' . ($id ? 'diperbarui.' : 'ditambahkan.'));
        Response::redirect('/admin/kategori');
    }

    /** POST /admin/kategori/{id}/hapus */
    public function hapusKategori(int $id): void
    {
        $k = $this->kategoriRepo->findById($id);
        if ($k === null) {
            Response::notFound('Kategori tidak ditemukan.');
        }
        $tujuan = null;
        if ($k->getJumlahProduk() > 0) {
            // Produk tidak ikut terhapus: wajib dipindahkan ke kategori lain lebih dulu.
            $idTujuan = (int) Request::post('pindahKe');
            $tujuan = $idTujuan && $idTujuan !== $id ? $this->kategoriRepo->findById($idTujuan) : null;
            if ($tujuan === null) {
                Session::flash('gagal', 'Kategori Tidak Dapat Dihapus: "' . $k->getNamaKategori() . '" masih digunakan oleh ' . $k->getJumlahProduk() . ' produk. Pilih kategori tujuan untuk memindahkan produk tersebut.');
                Response::redirect('/admin/kategori');
            }
        }
        try {
            $this->kategoriRepo->delete($id, $tujuan?->getIdKategori());
        } catch (\PDOException $e) {
            \App\Core\ErrorHandler::log($e);
            Session::flash('gagal', 'Kategori gagal dihapus.');
            Response::redirect('/admin/kategori');
        }
        Session::flash('sukses', 'Kategori "' . $k->getNamaKategori() . '" berhasil dihapus.'
            . ($tujuan ? ' ' . $k->getJumlahProduk() . ' produk dipindahkan ke "' . $tujuan->getNamaKategori() . '".' : ''));
        Response::redirect('/admin/kategori');
    }

    // ==================================================================
    // ADMIN - ATRIBUT REKOMENDASI (UI-19, FR-19)
    // ==================================================================

    public function formAtribut(): void
    {
        $daftar = $this->produkRepo->getDaftarRingkas();
        $id = Request::queryInt('produk') ?? (int) ($daftar[0]['idProduk'] ?? 0);
        $produk = $id ? $this->produkRepo->findById($id) : null;
        Response::view('admin/atribut/index', [
            'title'    => 'Atribut Rekomendasi',
            'daftar'   => $daftar,
            'produk'   => $produk,
            'kategori' => $this->kategoriRepo->getAll(),
            'atribut'  => $this->opsiAtribut($produk),
            'old'      => Session::pullOld(),
            'errors'   => Session::pullErrors(),
        ], 'admin');
    }

    /** POST /admin/atribut/{id} */
    public function updateAtribut(int $id): void
    {
        $p = $this->produkRepo->findById($id);
        if ($p === null) {
            Response::notFound('Produk tidak ditemukan.');
        }
        $v = $this->validasiInput($_POST, true);
        if ($v->fails()) {
            Session::flashInput($_POST);
            Session::flashErrors($v->errors());
            Session::flash('gagal', 'Atribut belum valid: ' . $v->firstError());
            Response::redirect('/admin/atribut?produk=' . $id);
        }
        try {
            $this->produkRepo->updateAtribut(
                $id, (int) $v->value('idKategori'), $v->value('ruangan'), $v->value('gaya'),
                $v->value('material'), $v->value('ukuran'), (float) Validator::toNumber($v->value('harga'))
            );
        } catch (\PDOException $e) {
            \App\Core\ErrorHandler::log($e);
            Session::flash('gagal', 'Atribut gagal disimpan. Data sebelumnya tetap dipertahankan.');
            Response::redirect('/admin/atribut?produk=' . $id);
        }
        Session::flash('sukses', 'Atribut rekomendasi "' . $p->getNama() . '" berhasil disimpan.');
        Response::redirect('/admin/atribut?produk=' . $id);
    }

    // ------------------------------------------------------------------

    /** Opsi dropdown atribut = daftar konfigurasi (+ nilai lama produk bila berbeda). */
    private function opsiAtribut(?Produk $p): array
    {
        $atr = Config::get('atribut');
        foreach (['ruangan', 'gaya', 'material'] as $k) {
            $atr[$k] = $this->gabungOpsi($k);
            $getter = 'get' . ucfirst($k);
            if ($p && $p->$getter() !== '' && !in_array($p->$getter(), $atr[$k], true)) {
                $atr[$k][] = $p->$getter();
            }
        }
        return $atr;
    }

    /** Pilihan sah = daftar konfigurasi + nilai yang sudah ada di basis data. */
    private function gabungOpsi(string $kolom): array
    {
        $opsi = (array) Config::get('atribut.' . $kolom, []);
        foreach ($this->produkRepo->nilaiAtribut($kolom) as $nilai) {
            if (!in_array($nilai, $opsi, true)) {
                $opsi[] = $nilai;
            }
        }
        return $opsi;
    }
}
