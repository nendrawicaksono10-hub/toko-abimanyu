<?php
namespace App\Controller;

use App\Core\Config;
use App\Core\Paginator;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Repository\ProdukRepository;

/**
 * StokController - FR-15 Manajemen Stok, FR-24 Peringatan Stok Menipis, FR-29 Ambang dari konfigurasi.
 * Algoritma #3 Cek Stok Menipis.
 */
class StokController
{
    private ProdukRepository $produkRepo;
    private int $ambangBatas;

    public function __construct()
    {
        $this->produkRepo = new ProdukRepository();
        $this->ambangBatas = (int) Config::get('toko.ambang_stok_menipis', 5);
    }

    /** UI-6 Halaman Manajemen Stok. */
    public function getDaftarStok(): void
    {
        $q = mb_substr(Request::query('q'), 0, 100);
        $filter = Request::query('status');
        if ($filter === 'menipis' || $filter === 'habis') {
            $daftar = array_values(array_filter(
                $this->getDaftarStokMenipis(),
                fn($p) => $filter === 'habis' ? $p->getStok() === 0 : $p->getStok() > 0
            ));
            if ($q !== '') {
                $daftar = array_values(array_filter($daftar, fn($p) => cocokKataKunci($q, $p->getNama(), (string) $p->getNamaKategori(), (string) $p->getMaterial(), (string) $p->getRuangan(), (string) $p->getGaya())));
            }
            $pg = new Paginator(count($daftar), max(1, count($daftar)), 1);
        } else {
            $filter = '';
            $total = $this->produkRepo->hitungSemua($q);
            $pg = new Paginator($total, (int) Config::get('toko.admin_per_halaman', 20), Request::queryInt('hal'));
            $daftar = $this->produkRepo->getAll($q, $pg->perHalaman, $pg->offset, 'p.stok ASC, p.nama');
        }
        Response::view('admin/stok/index', [
            'title'   => 'Manajemen Stok',
            'daftar'  => $daftar,
            'q'       => $q,
            'filter'  => $filter,
            'pg'      => $pg,
            'ambang'  => $this->ambangBatas,
            'menipis' => $this->getDaftarStokMenipis(),
        ], 'admin');
    }

    public function validasiInput(array $data): Validator
    {
        $v = new Validator($data);
        $v->required('stok', 'Jumlah stok')->integer('stok', 'Jumlah stok', 0, 1000000);
        return $v;
    }

    /** POST /admin/stok/{id} */
    public function updateStok(int $id): void
    {
        $p = $this->produkRepo->findById($id);
        if ($p === null) {
            Response::notFound('Produk tidak ditemukan.');
        }
        $v = $this->validasiInput($_POST);
        if ($v->fails()) {
            Session::flash('gagal', $p->getNama() . ': ' . $v->firstError());
            Response::back('/admin/stok');
        }
        $stok = (int) $v->value('stok');
        try {
            $this->produkRepo->updateStok($id, $stok);
        } catch (\PDOException $e) {
            \App\Core\ErrorHandler::log($e);
            Session::flash('gagal', 'Stok gagal diperbarui. Data sebelumnya tetap dipertahankan.');
            Response::back('/admin/stok');
        }
        Session::flash('sukses', 'Stok "' . $p->getNama() . '" diperbarui menjadi ' . $stok . ' unit.');
        $status = $this->cekStokMenipis($id, $stok);
        if ($status !== 'Aman') {
            Session::flash('peringatan', ($status === 'Habis' ? 'Stok Habis: ' : 'Stok Menipis: ') . $p->getNama() . ' tersisa ' . $stok . ' unit (ambang batas ' . $this->ambangBatas . ').');
        }
        Response::back('/admin/stok');
    }

    /** Algoritma #3 langkah 2. */
    public function cekStokMenipis(int $idProduk, int $jumlahStokBaru): string
    {
        if ($jumlahStokBaru === 0) {
            return 'Habis';
        }
        return $jumlahStokBaru < $this->ambangBatas ? 'Stok Menipis' : 'Aman';
    }

    /** Algoritma #3 langkah 3 (Query #4): dihitung dari stok terkini, tidak disimpan. */
    public function getDaftarStokMenipis(): array
    {
        return $this->produkRepo->getStokMenipis($this->ambangBatas);
    }
}
