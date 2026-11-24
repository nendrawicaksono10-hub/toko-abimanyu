# Toko Abimanyu — Website E-Commerce & Sistem Rekomendasi Produk (Progres Sprint 1–3)

Implementasi dokumen **SKPL-DPPL SKPL-0001 Revisi C** (Website E-Commerce & Sistem Rekomendasi Produk UMKM Perabotan Rumah) pada tahap penyelesaian **Sprint 1 hingga Sprint 3**.

| Lapisan    | Teknologi |
|------------|-----------|
| Backend    | PHP 8.1+ native (tanpa framework), PDO + prepared statement, pola **Boundary – Controller – Repository – Entity** |
| Basis data | MySQL 8.0 / MariaDB 10.4+ (InnoDB, utf8mb4) — 9 tabel sesuai kamus data |
| Frontend   | HTML (template PHP) + **Tailwind CSS v4** (dikompilasi jadi 1 file CSS) + JavaScript murni |

> **Mulai cepat (XAMPP, tanpa Composer / Node.js):**
> 1. Clone repositori ini atau klik **Code → Download ZIP**, lalu letakkan folder `toko-abimanyu/` di dalam `htdocs` XAMPP.
> 2. Buat database `toko_abimanyu` di phpMyAdmin, lalu impor `backend/database/schema.sql` diikuti `backend/database/seed.sql`.
> 3. Buka `http://localhost/toko-abimanyu/` (konfigurasi bawaan XAMPP di `backend/config/config.php` sudah disertakan).
>
> Panduan lengkap: baca **[docs/SETUP.md](docs/SETUP.md)**.

---

## Status Progres Backlog

### Selesai (Sprint 1 – Sprint 3)

- **Sprint 1 — Fondasi, Autentikasi & Kategori (`PBI-01` s.d. `PBI-09`):**
  - Struktur proyek berlapis (`backend/`, `frontend/`, `public/`, `docs/`), konfigurasi di luar `public/`.
  - Skema basis data 9 tabel (`schema.sql`) + data awal admin, 8 kategori, dan 32 produk contoh (`seed.sql`).
  - Koneksi PDO prepared statement + penanganan error terpusat (`ErrorHandler`, halaman `404` & `500`).
  - Layout responsif HP/tablet/desktop (`pelanggan`, `admin`, `polos`).
  - Login admin (`password_verify`, regenerasi sesi, rate limit), proteksi halaman admin + token CSRF, dan logout admin.
  - Manajemen kategori admin (tambah, ubah, hapus dengan validasi kategori terpakai).
- **Sprint 2 — Katalog & Manajemen Produk (`PBI-10` s.d. `PBI-17`):**
  - Manajemen produk admin (tambah, ubah, hapus dengan perlindungan riwayat pesanan) + unggah foto utama (JPG/PNG/WebP maks. 2 MB, validasi isi berkas, nama acak).
  - Halaman beranda (produk unggulan & kategori populer), katalog produk aktif, detail produk, pencarian produk, dan filter per kategori.
  - Ubah password admin dengan verifikasi password lama & penyimpanan hash.
- **Sprint 3 — Stok, Keranjang & Checkout (`PBI-18` s.d. `PBI-26`, `PBI-52` s.d. `PBI-54`):**
  - Manajemen stok produk admin + peringatan stok menipis berdasarkan ambang konfigurasi (`ambang_stok_menipis = 5`).
  - Pengelolaan atribut rekomendasi (`ruangan`, `gaya`, `material`, `ukuran`, dan dimensi P × L × T) pada form produk & halaman `/admin/atribut`.
  - Fitur nonaktifkan/aktifkan kembali produk (`PBI-52`).
  - Pagination (12 produk/halaman di katalog, 20 baris/halaman di admin) & urutan katalog (`Terbaru`, `Harga Termurah`, `Harga Termahal`) (`PBI-53`).
  - Keranjang belanja berbasis Session PHP dengan validasi harga & stok dari database.
  - Form checkout + pilihan metode pembayaran (`Transfer Bank`, `QRIS`, `COD`), persetujuan Kebijakan Privasi & normalisasi format nomor WA (`PBI-54`), anti-spam (honeypot, CSRF, token sekali pakai, rate limiter), transaksi atomik pengurangan stok (`BEGIN/COMMIT/ROLLBACK`), halaman konfirmasi kode `ORD-nnnnnn`, dan tombol konfirmasi WhatsApp otomatis.

### Direncanakan pada Sprint Berikutnya (Sprint 4 – Sprint 6)
- **Sprint 4:** Pencatatan pembayaran otomatis (`PBI-27`), halaman instruksi & unggah bukti bayar (`PBI-28`–`PBI-30`), manajemen pesanan admin & cek status pelanggan (`PBI-31`–`PBI-34`, `PBI-55`–`PBI-56`).
- **Sprint 5:** Verifikasi pembayaran admin (`PBI-36`), pengembalian stok pembatalan (`PBI-37`), mesin rekomendasi rule-based (`PBI-38`–`PBI-40`), dashboard admin (`PBI-42`), dan galeri 5 foto produk (`PBI-57`).
- **Sprint 6:** Laporan penjualan & ekspor PDF/Excel (`PBI-43`, `PBI-58`), optimasi performa, pengujian menyeluruh, backup otomatis, dan deployment hosting.

---

## Login admin awal

| URL | Username | Password |
|-----|----------|----------|
| `http://localhost/toko-abimanyu/admin/login` | `admin` | `Abimanyu#2026` |

Segera ganti password lewat menu **Ubah Password** setelah login pertama.

---

## Struktur folder

```
toko-abimanyu/
├── backend/                     ← BACKEND (PHP) — tidak bisa diakses dari browser
│   ├── bootstrap.php            ← autoload, konfigurasi, sesi, error handler
│   ├── routes.php               ← daftar URL Sprint 1–3 → Controller
│   ├── config/
│   │   ├── config.example.php   ← templat konfigurasi
│   │   └── config.php           ← konfigurasi bawaan XAMPP
│   ├── database/
│   │   ├── schema.sql           ← DDL 9 tabel + indeks (PBI-02)
│   │   └── seed.sql             ← data awal: admin, 8 kategori, 32 produk contoh
│   ├── src/
│   │   ├── Core/                ← Router, Database, Session, Csrf, Validator, ImageUploader, Paginator, RateLimiter, ...
│   │   ├── Entity/              ← Admin, Kategori, Produk, Pesanan, DetailPesanan
│   │   ├── Repository/          ← AdminRepository, KategoriRepository, ProdukRepository, PesananRepository, DetailPesananRepository
│   │   └── Controller/          ← AuthController, ProdukController, StokController, KeranjangController, PesananController
│   ├── storage/
│   │   ├── bukti/               ← disiapkan untuk Sprint 4
│   │   ├── logs/                ← log kesalahan server (`app.log`)
│   │   └── ratelimit/           ← jejak anti-spam checkout & login
│   └── .htaccess                ← memblokir akses langsung browser
│
├── frontend/                    ← FRONTEND (tampilan)
│   ├── views/                   ← template HTML/PHP (lapisan Boundary)
│   │   ├── layouts/             ← kerangka halaman pelanggan, admin, polos
│   │   ├── partials/            ← komponen bersama (kartu produk, pagination, badge, flash, ...)
│   │   ├── pelanggan/           ← beranda, katalog, produk, keranjang, checkout, halaman
│   │   ├── admin/               ← auth, produk, kategori, stok, atribut
│   │   └── errors/              ← 404 & 500
│   ├── tailwind/
│   │   └── app.css              ← sumber CSS + token desain
│   ├── package.json             ← perintah build Tailwind (opsional, pengembangan)
│   ├── package-lock.json
│   └── .htaccess                ← memblokir akses langsung browser
│
├── public/                      ← DOCUMENT ROOT (satu-satunya folder yang dibuka web server)
│   ├── index.php                ← front controller
│   ├── router.php               ← router untuk `php -S` (pengembangan)
│   ├── .htaccess
│   ├── assets/
│   │   ├── css/app.css          ← hasil kompilasi Tailwind (siap pakai)
│   │   ├── js/                  ← core.js, keranjang.js, unggah.js
│   │   ├── fonts/               ← font di-host sendiri (tanpa CDN)
│   │   └── img/                 ← favicon.svg, qris.png
│   └── uploads/
│       ├── .htaccess            ← mematikan eksekusi skrip di folder unggahan
│       └── produk/              ← foto utama produk unggahan admin
│
├── docs/
│   ├── SETUP.md                 ← panduan instalasi lokal (XAMPP & PHP built-in server)
│   └── FITUR-DAN-KERUNUTAN.md   ← peta PBI/FR/NFR Sprint 1–3 → file kode
├── .editorconfig
├── .gitattributes
├── .gitignore
├── .htaccess                    ← agar folder proyek bisa langsung ditaruh di htdocs
└── README.md
```

Detail keterkaitan PBI & kebutuhan SKPL → kode: lihat **[docs/FITUR-DAN-KERUNUTAN.md](docs/FITUR-DAN-KERUNUTAN.md)**.
