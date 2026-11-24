# Peta Backlog Sprint 1–3 & Kerunutan SKPL → Kode

Singkatan path: `C/` = `backend/src/Controller/`, `R/` = `backend/src/Repository/`, `V/` = `frontend/views/`.

## Backlog Selesai (Sprint 1 – Sprint 3)

| PBI | Sprint | FR/NFR | Fitur | Berkas Utama |
|---|---|---|---|---|
| PBI-01 | Sprint 1 | NFR-06 | Struktur folder berlapis & konfigurasi di luar `public/` | `backend/`, `frontend/`, `public/`, `backend/config/config.example.php` |
| PBI-02 | Sprint 1 | Subbab 4.6, NFR-07 | Skema 9 tabel + indeks + data awal admin, kategori, produk | `backend/database/schema.sql`, `backend/database/seed.sql` |
| PBI-03 | Sprint 1 | NFR-05, NFR-11 | Koneksi PDO prepared statement & penanganan error terpusat | `backend/src/Core/Database.php`, `ErrorHandler.php`, `V/errors/404.php`, `500.php` |
| PBI-04 | Sprint 1 | NFR-01, NFR-09 | Tampilan dasar responsif (HP, tablet, desktop) | `V/layouts/pelanggan.php`, `admin.php`, `polos.php`, `public/assets/css/app.css` |
| PBI-06 | Sprint 1 | FR-11 | Login admin (`password_verify`, regenerasi sesi) | `C/AuthController::formLogin`, `login`, `R/AdminRepository`, `V/admin/auth/login.php` |
| PBI-07 | Sprint 1 | NFR-05 | Proteksi halaman admin (wajib login) & token CSRF | `backend/src/Core/Router.php`, `Csrf.php`, `Session.php`, `C/AuthController::cekSesi` |
| PBI-08 | Sprint 1 | FR-26 | Logout admin | `C/AuthController::logout` |
| PBI-09 | Sprint 1 | FR-14 | Kelola kategori (tambah, ubah, hapus) | `C/ProdukController::kelolaKategori/simpanKategori/hapusKategori`, `R/KategoriRepository`, `V/admin/kategori/index.php` |
| PBI-10 | Sprint 2 | FR-13 | Kelola produk (tambah, ubah, hapus dengan proteksi riwayat) | `C/ProdukController`, `R/ProdukRepository`, `V/admin/produk/index.php`, `form.php` |
| PBI-11 | Sprint 2 | FR-28 | Unggah foto utama produk (validasi isi, nama acak, no-exec) | `backend/src/Core/ImageUploader.php`, `public/uploads/.htaccess`, `public/assets/js/unggah.js` |
| PBI-12 | Sprint 2 | FR-02 | Halaman katalog produk aktif | `C/ProdukController::getKatalog`, `R/ProdukRepository::search`, `V/pelanggan/katalog/index.php` |
| PBI-13 | Sprint 2 | FR-05 | Halaman detail produk | `C/ProdukController::getDetail`, `V/pelanggan/produk/detail.php` |
| PBI-14 | Sprint 2 | FR-01 | Halaman beranda (produk unggulan & kategori populer) | `C/ProdukController::getBeranda`, `V/pelanggan/beranda/index.php` |
| PBI-15 | Sprint 2 | FR-03 | Pencarian produk aman (parameter terikat) | `R/ProdukRepository::search`, `R/Repository::filterKataKunci` |
| PBI-16 | Sprint 2 | FR-04 | Filter produk per kategori (bekerja bersama pencarian) | `C/ProdukController::getKatalog`, `V/pelanggan/katalog/index.php` |
| PBI-17 | Sprint 2 | FR-27 | Ubah password admin | `C/AuthController::formUbahPassword`, `ubahPassword`, `V/admin/auth/password.php` |
| PBI-18 | Sprint 3 | FR-15 | Kelola stok produk | `C/StokController`, `V/admin/stok/index.php` |
| PBI-19 | Sprint 3 | FR-24, FR-29 | Peringatan stok menipis (ambang dari konfigurasi) | `C/StokController::getDaftarStokMenipis`, `R/ProdukRepository::getStokMenipis`, `V/layouts/admin.php` |
| PBI-20 | Sprint 3 | FR-19, NFR-11 | Atribut rekomendasi & dimensi di form produk & halaman atribut | `C/ProdukController::formAtribut`, `updateAtribut`, `V/admin/produk/form.php`, `V/admin/atribut/index.php` |
| PBI-21 | Sprint 3 | FR-06 | Keranjang belanja di Session PHP | `C/KeranjangController`, `V/pelanggan/keranjang/index.php`, `public/assets/js/keranjang.js` |
| PBI-22 | Sprint 3 | FR-07, FR-20, FR-32, NFR-11 | Form checkout & pilihan metode pembayaran (Transfer, QRIS, COD) | `C/PesananController::bukaCheckout`, `validasiInput`, `V/pelanggan/checkout/form.php` |
| PBI-23 | Sprint 3 | FR-21, FR-22, FR-23 | Simpan pesanan & kurangi stok atomik (`BEGIN/COMMIT/ROLLBACK`) | `C/PesananController::buatPesanan`, `R/PesananRepository`, `R/DetailPesananRepository`, `R/ProdukRepository::kurangiStokAtomik` |
| PBI-24 | Sprint 3 | FR-21 | Halaman konfirmasi pesanan (`ORD-nnnnnn`) | `C/PesananController::berhasil`, `V/pelanggan/checkout/berhasil.php` |
| PBI-25 | Sprint 3 | FR-31 | Anti-spam checkout (honeypot, CSRF, token ganda, rate limit) | `C/PesananController::buatPesanan`, `backend/src/Core/RateLimiter.php` |
| PBI-26 | Sprint 3 | FR-09 | Tombol chat WhatsApp ke admin (tautan `wa.me` ter-encode) | `C/PesananController::buatTautanWA`, `waLink()` di `Core/helpers.php` |
| PBI-52 | Sprint 3 | FR-13, FR-42 | Nonaktifkan & aktifkan kembali produk | `C/ProdukController::ubahStatusAktif`, `hapus`, `R/ProdukRepository::updateAktif` |
| PBI-53 | Sprint 3 | FR-02, FR-44, NFR-07 | Pagination (12 katalog / 20 admin) & urutan katalog daftar putih | `backend/src/Core/Paginator.php`, `R/ProdukRepository::URUTAN`, `V/partials/pagination.php` |
| PBI-54 | Sprint 3 | FR-41, FR-08 | Persetujuan Kebijakan Privasi & normalisasi format nomor WA | `C/PesananController::kebijakanPrivasi`, `normalisasiKontak()`, `V/pelanggan/halaman/kebijakan-privasi.php` |

## Backlog Berikutnya (Sprint 4 – Sprint 6 — Belum Diimplementasikan)

- **Sprint 4:** `PBI-27`–`PBI-35`, `PBI-55`, `PBI-56` (Pembayaran otomatis, instruksi & unggah bukti pembayaran, riwayat bukti, kelola pesanan admin, cek status pelanggan, batas waktu bayar 24 jam, linimasa status).
- **Sprint 5:** `PBI-36`–`PBI-42`, `PBI-57` (Verifikasi pembayaran admin, pengembalian stok saat batal, mesin rekomendasi rule-based, dashboard admin, galeri 5 foto produk, pengujian keamanan).
- **Sprint 6:** `PBI-43`–`PBI-51`, `PBI-58` (Laporan penjualan + ekspor PDF/Excel, optimasi performa, pengujian menyeluruh & SUS, backup otomatis, deployment hosting).
