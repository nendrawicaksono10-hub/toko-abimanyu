-- =====================================================================
-- Toko Abimanyu - Skema Basis Data (SKPL-0001 Revisi C, subbab 4.6)
-- MySQL 8.0 / MariaDB 10.4+ | Engine InnoDB | utf8mb4_unicode_ci
-- Sembilan tabel: kategori, produk, foto_produk, pesanan, detail_pesanan,
-- pembayaran, riwayat_pembayaran, riwayat_status_pesanan, admin
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS riwayat_status_pesanan;
DROP TABLE IF EXISTS riwayat_pembayaran;
DROP TABLE IF EXISTS pembayaran;
DROP TABLE IF EXISTS detail_pesanan;
DROP TABLE IF EXISTS pesanan;
DROP TABLE IF EXISTS foto_produk;
DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS admin;

SET FOREIGN_KEY_CHECKS = 1;

-- Tabel 4.11 Kamus Data - kategori
CREATE TABLE kategori (
  idKategori   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  namaKategori VARCHAR(100) NOT NULL,
  PRIMARY KEY (idKategori),
  UNIQUE KEY uq_kategori_nama (namaKategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel 4.12 Kamus Data - produk
CREATE TABLE produk (
  idProduk   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  idKategori INT UNSIGNED NOT NULL,
  nama       VARCHAR(150) NOT NULL,
  ruangan    VARCHAR(50)  NOT NULL,
  gaya       VARCHAR(50)  NOT NULL,
  material   VARCHAR(50)  NOT NULL,
  ukuran     VARCHAR(20)  NOT NULL,
  panjang    DECIMAL(6,2) NOT NULL,
  lebar      DECIMAL(6,2) NOT NULL,
  tinggi     DECIMAL(6,2) NOT NULL,
  harga      DECIMAL(12,2) NOT NULL,
  stok       INT UNSIGNED NOT NULL DEFAULT 0,
  deskripsi  TEXT NULL,
  fotoUrl    VARCHAR(255) NULL,
  aktif      TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (idProduk),
  CONSTRAINT chk_produk_harga CHECK (harga >= 0),
  CONSTRAINT fk_produk_kategori FOREIGN KEY (idKategori)
    REFERENCES kategori (idKategori) ON DELETE RESTRICT ON UPDATE CASCADE,
  FULLTEXT KEY ft_produk_nama (nama),
  KEY idx_produk_rekomendasi (ruangan, harga),
  KEY idx_produk_stok (stok),
  KEY idx_produk_aktif_harga (aktif, harga)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel 4.18 Kamus Data - foto_produk (foto tambahan, maks. 4 per produk)
CREATE TABLE foto_produk (
  idFoto   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  idProduk INT UNSIGNED NOT NULL,
  fotoUrl  VARCHAR(255) NOT NULL,
  urutan   TINYINT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (idFoto),
  KEY idx_foto_produk (idProduk, urutan),
  CONSTRAINT fk_foto_produk FOREIGN KEY (idProduk)
    REFERENCES produk (idProduk) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel 4.13 Kamus Data - pesanan
CREATE TABLE pesanan (
  idPesanan     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  tanggal       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  namaPelanggan VARCHAR(100) NOT NULL,
  kontak        VARCHAR(20)  NOT NULL,
  alamat        TEXT NOT NULL,
  status        ENUM('Menunggu Konfirmasi','Diproses','Dikirim','Selesai','Dibatalkan')
                NOT NULL DEFAULT 'Menunggu Konfirmasi',
  totalHarga    DECIMAL(14,2) NOT NULL,
  PRIMARY KEY (idPesanan),
  KEY idx_pesanan_tanggal_status (tanggal, status),
  KEY idx_pesanan_kontak (kontak)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel 4.14 Kamus Data - detail_pesanan
CREATE TABLE detail_pesanan (
  idDetail  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  idPesanan INT UNSIGNED NOT NULL,
  -- NULL bila produk sudah dihapus admin; riwayat tetap utuh lewat kolom salinan di bawah.
  idProduk  INT UNSIGNED NULL,
  jumlah    INT UNSIGNED NOT NULL,
  subtotal  DECIMAL(14,2) NOT NULL,
  -- Salinan (snapshot) data produk saat transaksi, agar riwayat pesanan & laporan
  -- tidak berubah/hilang ketika produk diubah atau dihapus.
  namaProduk   VARCHAR(150) NOT NULL DEFAULT '',
  namaKategori VARCHAR(100) NULL,
  hargaSatuan  DECIMAL(14,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (idDetail),
  KEY idx_detail_produk (idProduk),
  CONSTRAINT chk_detail_jumlah CHECK (jumlah > 0),
  CONSTRAINT fk_detail_pesanan FOREIGN KEY (idPesanan)
    REFERENCES pesanan (idPesanan) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_detail_produk FOREIGN KEY (idProduk)
    REFERENCES produk (idProduk) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel 4.16 Kamus Data - pembayaran (1:1 dengan pesanan)
CREATE TABLE pembayaran (
  idPembayaran      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  idPesanan         INT UNSIGNED NOT NULL,
  metode            ENUM('Transfer Bank','QRIS','COD') NOT NULL,
  statusPembayaran  ENUM('Belum Dibayar','Menunggu Verifikasi','Lunas','Ditolak')
                    NOT NULL DEFAULT 'Belum Dibayar',
  namaPengirim      VARCHAR(100) NULL,
  tanggalBayar      DATE NULL,
  buktiUrl          VARCHAR(255) NULL,
  tanggalVerifikasi DATETIME NULL,
  alasanPenolakan   VARCHAR(255) NULL,
  PRIMARY KEY (idPembayaran),
  UNIQUE KEY uq_pembayaran_pesanan (idPesanan),
  KEY idx_pembayaran_status (statusPembayaran),
  CONSTRAINT fk_pembayaran_pesanan FOREIGN KEY (idPesanan)
    REFERENCES pesanan (idPesanan) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel 4.17 Kamus Data - riwayat_pembayaran (tambah-saja)
CREATE TABLE riwayat_pembayaran (
  idRiwayat       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  idPembayaran    INT UNSIGNED NOT NULL,
  namaPengirim    VARCHAR(100) NULL,
  tanggalBayar    DATE NULL,
  buktiUrl        VARCHAR(255) NOT NULL,
  waktuKirim      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  hasil           ENUM('Menunggu Verifikasi','Lunas','Ditolak') NOT NULL DEFAULT 'Menunggu Verifikasi',
  catatanAdmin    VARCHAR(255) NULL,
  waktuVerifikasi DATETIME NULL,
  PRIMARY KEY (idRiwayat),
  KEY idx_riwayat_pembayaran (idPembayaran, waktuKirim),
  CONSTRAINT fk_riwayat_pembayaran FOREIGN KEY (idPembayaran)
    REFERENCES pembayaran (idPembayaran) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel 4.19 Kamus Data - riwayat_status_pesanan (tambah-saja)
CREATE TABLE riwayat_status_pesanan (
  idRiwayatStatus INT UNSIGNED NOT NULL AUTO_INCREMENT,
  idPesanan       INT UNSIGNED NOT NULL,
  statusLama      ENUM('Menunggu Konfirmasi','Diproses','Dikirim','Selesai','Dibatalkan') NOT NULL,
  statusBaru      ENUM('Menunggu Konfirmasi','Diproses','Dikirim','Selesai','Dibatalkan') NOT NULL,
  waktu           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  oleh            VARCHAR(50) NOT NULL,
  PRIMARY KEY (idRiwayatStatus),
  KEY idx_riwayat_status (idPesanan, waktu),
  CONSTRAINT fk_riwayat_status_pesanan FOREIGN KEY (idPesanan)
    REFERENCES pesanan (idPesanan) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel 4.15 Kamus Data - admin
CREATE TABLE admin (
  username VARCHAR(50)  NOT NULL,
  password VARCHAR(255) NOT NULL COMMENT 'Hash password_hash() (bcrypt)',
  PRIMARY KEY (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
