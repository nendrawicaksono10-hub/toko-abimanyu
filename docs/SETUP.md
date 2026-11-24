# Panduan Setup Toko Abimanyu (Progres Sprint 1–3)

Dokumen ini menjelaskan langkah instalasi dan pengujian website **Toko Abimanyu** di komputer lokal (XAMPP atau server bawaan PHP).

---

## 1. Yang perlu diinstal

| Perangkat lunak | Wajib? | Untuk apa | Catatan |
|---|---|---|---|
| **XAMPP 8.2.x** (atau 8.1+) | **Wajib** | Apache + PHP + MySQL/MariaDB + phpMyAdmin | Alternatif: Laragon. |
| Browser modern (Chrome/Firefox/Edge) | Wajib | Membuka website | |
| Text editor (VS Code) | Disarankan | Mengubah konfigurasi/kode | |
| Node.js 18+ | Opsional | **Hanya** bila mengubah class Tailwind di template | CSS jadi sudah disertakan di `public/assets/css/app.css` |

Persyaratan PHP: versi **8.1 atau lebih baru** dengan ekstensi `pdo_mysql`, `mbstring`, `fileinfo`, dan `gd`.

---

## 2. Letakkan folder proyek

1. Clone repositori Git atau ekstrak arsip ZIP dari GitHub.
2. Letakkan folder **`toko-abimanyu`** ke dalam folder `htdocs` XAMPP:
   - Windows: `C:\xampp\htdocs\toko-abimanyu`
   - macOS: `/Applications/XAMPP/htdocs/toko-abimanyu`
   - Linux: `/opt/lampp/htdocs/toko-abimanyu`

Pastikan isi folder langsung berupa `backend/`, `frontend/`, `public/`, `docs/` (bukan `toko-abimanyu/toko-abimanyu/...`).

---

## 3. Aktifkan ekstensi PHP yang dibutuhkan

1. Buka **XAMPP Control Panel** → baris *Apache* → tombol **Config** → **PHP (php.ini)**.
2. Pastikan baris berikut aktif (hapus tanda `;` di depannya bila ada):
   ```ini
   extension=gd
   extension=mbstring
   extension=fileinfo
   extension=pdo_mysql
   ```
3. Simpan, lalu **Stop** dan **Start** ulang Apache.

---

## 4. Jalankan Apache dan MySQL

Di XAMPP Control Panel klik **Start** pada **Apache** dan **MySQL** sampai keduanya aktif.

---

## 5. Buat database & impor tabel

1. Buka `http://localhost/phpmyadmin`.
2. Klik **New/Baru** → nama database: **`toko_abimanyu`** → collation **`utf8mb4_unicode_ci`** → **Create**.
3. Pilih database `toko_abimanyu` → tab **Import** → pilih berkas `backend/database/schema.sql` → **Go/Kirim**.
4. Ulangi tab **Import** untuk berkas `backend/database/seed.sql` (akun admin awal, 8 kategori, 32 produk contoh).

Hasilnya 9 tabel sesuai rancangan basis data (`PBI-02`): `admin, kategori, produk, foto_produk, pesanan, detail_pesanan, pembayaran, riwayat_pembayaran, riwayat_status_pesanan`.

---

## 6. Periksa konfigurasi

Buka `backend/config/config.php` (atau salin dari `config.example.php` bila belum ada). Untuk XAMPP standar, nilai bawaan sudah sesuai:

```php
'db' => [
    'host'     => '127.0.0.1',
    'port'     => 3306,
    'database' => 'toko_abimanyu',
    'username' => 'root',
    'password' => '',
],
```

Sesuaikan juga bila diperlukan:
- `whatsapp` — nomor WA admin, format `628xxxxxxxxxx`
- `toko.ambang_stok_menipis` — ambang peringatan stok menipis (bawaan `5`)
- `atribut` — pilihan dropdown ruangan / gaya / material / ukuran

---

## 7. Buka & uji fitur Sprint 1–3

| Halaman | URL |
|---|---|
| Toko (pelanggan) | `http://localhost/toko-abimanyu/` |
| Panel admin | `http://localhost/toko-abimanyu/admin/login` |

Login admin awal: **`admin` / `Abimanyu#2026`**.

Alur pengujian Sprint 1–3:
1. **Pelanggan:** Beranda → Katalog (cari, filter kategori, urutkan, pindah halaman) → Detail Produk → **Tambah ke Keranjang** → **Keranjang** (ubah jumlah/hapus) → **Checkout** (isi data, pilih metode bayar, centang Kebijakan Privasi) → **Buat Pesanan** → muncul kode `ORD-nnnnnn` & tombol konfirmasi WhatsApp, stok produk berkurang otomatis.
2. **Admin:** Login → **Produk** (tambah + unggah foto utama, ubah, nonaktifkan/aktifkan, hapus) → **Kategori** (tambah/ubah/hapus) → **Stok** (perbarui stok & lihat peringatan stok menipis) → **Atribut Rekomendasi** → **Ubah Password** → **Logout**.

---

## 8. Alternatif: server bawaan PHP (tanpa Apache)

```bash
cd toko-abimanyu
php -S localhost:8000 -t public public/router.php
```

Buka `http://localhost:8000/`.

---

## 9. Mengubah tampilan Tailwind CSS (opsional)

```bash
cd toko-abimanyu/frontend
npm install
npm run dev    # pantau perubahan CSS
npm run build  # kompilasi ke public/assets/css/app.css
```

---

## 10. Masalah umum

| Gejala | Penyebab & solusi |
|---|---|
| Semua halaman selain beranda **404 Not Found** | `mod_rewrite` mati atau `AllowOverride None`. Di `C:\xampp\apache\conf\httpd.conf` pastikan `LoadModule rewrite_module` aktif dan `<Directory "C:/xampp/htdocs">` memakai `AllowOverride All`, lalu restart Apache. |
| Halaman "Layanan Sedang Gangguan" | MySQL belum dijalankan, nama database salah, atau kredensial di `backend/config/config.php` belum sesuai. |
| "Terlalu banyak pesanan dalam waktu singkat" | Proteksi anti-spam (`PBI-25`, maks. 3 pesanan/10 menit). Tunggu 10 menit atau hapus isi `backend/storage/ratelimit/` saat pengujian. |
| Unggah foto ditolak | Hanya JPG/PNG/WebP maks. 2 MB; jenis berkas diperiksa dari isi berkas (`finfo` + `getimagesize`). |
