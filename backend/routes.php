<?php
/**
 * Daftar rute aplikasi (Progres Sprint 1–3).
 * Parameter ketiga true = halaman admin (wajib login).
 * Seluruh POST otomatis diperiksa token CSRF-nya oleh Router.
 */

use App\Controller\AuthController;
use App\Controller\KeranjangController;
use App\Controller\PesananController;
use App\Controller\ProdukController;
use App\Controller\StokController;
use App\Core\Response;

/** @var \App\Core\Router $r */

// ---------------- Pelanggan (tanpa login) ----------------
$r->get('/',                      [ProdukController::class, 'getBeranda']);       // PBI-14 (FR-01)
$r->get('/katalog',               [ProdukController::class, 'getKatalog']);       // PBI-12, 15, 16, 53 (FR-02..04, FR-44)
$r->get('/produk/{id}',           [ProdukController::class, 'getDetail']);        // PBI-13 (FR-05)

$r->get('/keranjang',             [KeranjangController::class, 'index']);         // PBI-21 (FR-06)
$r->post('/keranjang/tambah',     [KeranjangController::class, 'tambahItem']);
$r->post('/keranjang/ubah',       [KeranjangController::class, 'ubahJumlah']);
$r->post('/keranjang/hapus',      [KeranjangController::class, 'hapusItem']);

$r->get('/checkout',              [PesananController::class, 'bukaCheckout']);    // PBI-22, 54 (FR-07, FR-41)
$r->post('/checkout',             [PesananController::class, 'buatPesanan']);     // PBI-23, 25 (FR-21..23, FR-31)
$r->get('/pesanan/{id}/berhasil', [PesananController::class, 'berhasil']);        // PBI-24, 26 (FR-21, FR-09)
$r->get('/kebijakan-privasi',     [PesananController::class, 'kebijakanPrivasi']);// PBI-54 (FR-41)

// ---------------- Admin: autentikasi ----------------
$r->get('/admin/login',           [AuthController::class, 'formLogin']);          // PBI-06 (FR-11)
$r->post('/admin/login',          [AuthController::class, 'login']);
$r->post('/admin/logout',         [AuthController::class, 'logout'], true);       // PBI-08 (FR-26)
$r->get('/admin/password',        [AuthController::class, 'formUbahPassword'], true); // PBI-17 (FR-27)
$r->post('/admin/password',       [AuthController::class, 'ubahPassword'], true);

// ---------------- Admin: operasional Sprint 1–3 (wajib login) ----------------
$r->get('/admin',                 fn() => Response::redirect('/admin/produk'), true);

$r->get('/admin/produk',          [ProdukController::class, 'getDaftarProduk'], true); // PBI-10, 53 (FR-13)
$r->get('/admin/produk/tambah',   [ProdukController::class, 'formTambah'], true);
$r->post('/admin/produk',         [ProdukController::class, 'tambah'], true);          // PBI-10, 11, 20
$r->get('/admin/produk/{id}/ubah',[ProdukController::class, 'formUbah'], true);
$r->post('/admin/produk/{id}',    [ProdukController::class, 'ubah'], true);
$r->post('/admin/produk/{id}/hapus', [ProdukController::class, 'hapus'], true);
$r->post('/admin/produk/{id}/aktif', [ProdukController::class, 'ubahStatusAktif'], true); // PBI-52 (FR-42)

$r->get('/admin/kategori',        [ProdukController::class, 'kelolaKategori'], true);  // PBI-09 (FR-14)
$r->post('/admin/kategori',       [ProdukController::class, 'simpanKategori'], true);
$r->post('/admin/kategori/{id}/hapus', [ProdukController::class, 'hapusKategori'], true);

$r->get('/admin/stok',            [StokController::class, 'getDaftarStok'], true);     // PBI-18, 19 (FR-15, FR-24, FR-29)
$r->post('/admin/stok/{id}',      [StokController::class, 'updateStok'], true);

$r->get('/admin/atribut',         [ProdukController::class, 'formAtribut'], true);     // PBI-20 (FR-19)
$r->post('/admin/atribut/{id}',   [ProdukController::class, 'updateAtribut'], true);
