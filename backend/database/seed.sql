-- =====================================================================
-- Data awal & data contoh Toko Abimanyu
-- Jalankan SETELAH schema.sql. Akun admin awal: admin / Abimanyu#2026
-- (WAJIB diganti melalui menu Ubah Password setelah login pertama)
-- =====================================================================
SET NAMES utf8mb4;

INSERT INTO admin (username, password) VALUES
  ('admin', '$2y$10$PygHHwlV76.DQM9JK3p2IeKya96nMvTv/E1Tr/NPzsv7Wrn0CGjp.');

INSERT INTO kategori (idKategori, namaKategori) VALUES
  (1, 'Kursi'),
  (2, 'Meja'),
  (3, 'Lemari'),
  (4, 'Rak'),
  (5, 'Tempat Tidur'),
  (6, 'Sofa'),
  (7, 'Nakas & Bufet'),
  (8, 'Bangku');

INSERT INTO produk (idProduk, idKategori, nama, ruangan, gaya, material, ukuran, panjang, lebar, tinggi, harga, stok, deskripsi, fotoUrl, aktif) VALUES
  (1, 1, 'Kursi Makan Kayu Jati', 'Ruang Makan', 'Minimalis', 'Kayu Jati', 'Kecil', 45, 48, 90, 450000, 3, 'Kursi Makan Kayu Jati untuk ruang makan bergaya minimalis, dibuat dari kayu jati solid pilihan dengan finishing natural doff yang menonjolkan keindahan serat kayu. Dimensi 45 x 48 x 90 cm.', NULL, 1),
  (2, 2, 'Meja Makan Jati Minimalis 4 Kursi', 'Ruang Makan', 'Minimalis', 'Kayu Jati', 'Sedang', 160, 80, 75, 2450000, 12, 'Meja Makan Jati Minimalis 4 Kursi untuk ruang makan bergaya minimalis, dibuat dari kayu jati solid pilihan dengan finishing natural doff yang menonjolkan keindahan serat kayu. Dimensi 160 x 80 x 75 cm.', NULL, 1),
  (3, 2, 'Meja Tamu Minimalis', 'Ruang Tamu', 'Minimalis', 'Kayu Jati', 'Kecil', 100, 50, 45, 850000, 5, 'Meja Tamu Minimalis untuk ruang tamu bergaya minimalis, dibuat dari kayu jati solid pilihan dengan finishing natural doff yang menonjolkan keindahan serat kayu. Dimensi 100 x 50 x 45 cm.', NULL, 1),
  (4, 3, 'Lemari Pakaian 2 Pintu', 'Kamar Tidur', 'Klasik', 'Kayu Jati', 'Besar', 100, 55, 190, 1750000, 2, 'Lemari Pakaian 2 Pintu untuk kamar tidur bergaya klasik, dibuat dari kayu jati solid pilihan dengan finishing natural doff yang menonjolkan keindahan serat kayu. Dimensi 100 x 55 x 190 cm.', NULL, 1),
  (5, 1, 'Casa Lounge Chair', 'Ruang Tamu', 'Japandi', 'Kayu Jati', 'Sedang', 70, 78, 80, 1299000, 5, 'Casa Lounge Chair untuk ruang tamu bergaya japandi, dibuat dari kayu jati solid pilihan dengan finishing natural doff yang menonjolkan keindahan serat kayu. Dimensi 70 x 78 x 80 cm.', NULL, 1),
  (6, 7, 'Milo Sideboard Kayu', 'Ruang Makan', 'Modern', 'Kayu Mahoni', 'Sedang', 140, 40, 75, 2449000, 4, 'Milo Sideboard Kayu untuk ruang makan bergaya modern, menggunakan kayu mahoni berkualitas dengan warna hangat dan serat halus. Dimensi 140 x 40 x 75 cm.', NULL, 1),
  (7, 6, 'Nawa Modular Sofa Single', 'Ruang Tamu', 'Modern', 'Kayu & Besi', 'Besar', 190, 85, 80, 1850000, 6, 'Nawa Modular Sofa Single untuk ruang tamu bergaya modern, memadukan rangka besi kokoh dengan permukaan kayu solid. Dimensi 190 x 85 x 80 cm.', NULL, 1),
  (8, 2, 'Meja Kerja Arsitek Jati', 'Ruang Kerja', 'Minimalis', 'Kayu Jati', 'Sedang', 120, 60, 75, 1620000, 8, 'Meja Kerja Arsitek Jati untuk ruang kerja bergaya minimalis, dibuat dari kayu jati solid pilihan dengan finishing natural doff yang menonjolkan keindahan serat kayu. Dimensi 120 x 60 x 75 cm.', NULL, 1),
  (9, 7, 'Nakas Kamar Tidur Abadi', 'Kamar Tidur', 'Skandinavia', 'Kayu Pinus', 'Kecil', 45, 40, 55, 520000, 15, 'Nakas Kamar Tidur Abadi untuk kamar tidur bergaya skandinavia, menggunakan kayu pinus ringan dengan warna terang yang cocok untuk hunian bergaya Skandinavia. Dimensi 45 x 40 x 55 cm.', NULL, 1),
  (10, 4, 'Rak Buku Geometris Minimalis', 'Ruang Kerja', 'Minimalis', 'Kayu Mahoni', 'Besar', 120, 30, 150, 1150000, 0, 'Rak Buku Geometris Minimalis untuk ruang kerja bergaya minimalis, menggunakan kayu mahoni berkualitas dengan warna hangat dan serat halus. Dimensi 120 x 30 x 150 cm.', NULL, 1),
  (11, 8, 'Bangku Taman Kayu Solid', 'Teras & Taman', 'Rustik', 'Kayu Jati', 'Sedang', 120, 45, 45, 780000, 9, 'Bangku Taman Kayu Solid untuk teras & taman bergaya rustik, dibuat dari kayu jati solid pilihan dengan finishing natural doff yang menonjolkan keindahan serat kayu. Dimensi 120 x 45 x 45 cm.', NULL, 1),
  (12, 8, 'Bangku Panjang Kayu Solid', 'Ruang Makan', 'Minimalis', 'Kayu Jati', 'Sedang', 120, 35, 45, 890000, 8, 'Bangku Panjang Kayu Solid untuk ruang makan bergaya minimalis, dibuat dari kayu jati solid pilihan dengan finishing natural doff yang menonjolkan keindahan serat kayu. Dimensi 120 x 35 x 45 cm.', NULL, 1),
  (13, 1, 'Kursi Rotan Teras Lengkung', 'Teras & Taman', 'Rustik', 'Rotan', 'Kecil', 60, 62, 80, 675000, 11, 'Kursi Rotan Teras Lengkung untuk teras & taman bergaya rustik, dianyam tangan dari rotan alami oleh pengrajin lokal, ringan dan sejuk dipakai. Dimensi 60 x 62 x 80 cm.', NULL, 1),
  (14, 1, 'Kursi Bar Besi Industrial', 'Dapur', 'Modern', 'Besi', 'Kecil', 40, 40, 75, 395000, 20, 'Kursi Bar Besi Industrial untuk dapur bergaya modern, berangka besi kokoh dengan lapisan powder coating anti karat. Dimensi 40 x 40 x 75 cm.', NULL, 1),
  (15, 2, 'Meja Makan Bundar Skandinavia', 'Ruang Makan', 'Skandinavia', 'Kayu Pinus', 'Sedang', 100, 100, 75, 1390000, 7, 'Meja Makan Bundar Skandinavia untuk ruang makan bergaya skandinavia, menggunakan kayu pinus ringan dengan warna terang yang cocok untuk hunian bergaya Skandinavia. Dimensi 100 x 100 x 75 cm.', NULL, 1),
  (16, 2, 'Meja Konsol Ruang Tamu', 'Ruang Tamu', 'Klasik', 'Kayu Mahoni', 'Kecil', 110, 35, 80, 980000, 6, 'Meja Konsol Ruang Tamu untuk ruang tamu bergaya klasik, menggunakan kayu mahoni berkualitas dengan warna hangat dan serat halus. Dimensi 110 x 35 x 80 cm.', NULL, 1),
  (17, 3, 'Lemari Dapur Gantung', 'Dapur', 'Minimalis', 'Kayu Pinus', 'Sedang', 120, 35, 70, 1150000, 10, 'Lemari Dapur Gantung untuk dapur bergaya minimalis, menggunakan kayu pinus ringan dengan warna terang yang cocok untuk hunian bergaya Skandinavia. Dimensi 120 x 35 x 70 cm.', NULL, 1),
  (18, 3, 'Lemari Buku Kaca 3 Pintu', 'Ruang Kerja', 'Klasik', 'Kayu Jati', 'Besar', 150, 40, 200, 3250000, 3, 'Lemari Buku Kaca 3 Pintu untuk ruang kerja bergaya klasik, dibuat dari kayu jati solid pilihan dengan finishing natural doff yang menonjolkan keindahan serat kayu. Dimensi 150 x 40 x 200 cm.', NULL, 1),
  (19, 4, 'Rak Dinding Mengambang', 'Ruang Tamu', 'Minimalis', 'Kayu Jati', 'Kecil', 80, 20, 4, 185000, 25, 'Rak Dinding Mengambang untuk ruang tamu bergaya minimalis, dibuat dari kayu jati solid pilihan dengan finishing natural doff yang menonjolkan keindahan serat kayu. Dimensi 80 x 20 x 4 cm.', NULL, 1),
  (20, 4, 'Rak Sepatu Rotan 3 Susun', 'Ruang Tamu', 'Japandi', 'Rotan', 'Kecil', 80, 30, 60, 345000, 14, 'Rak Sepatu Rotan 3 Susun untuk ruang tamu bergaya japandi, dianyam tangan dari rotan alami oleh pengrajin lokal, ringan dan sejuk dipakai. Dimensi 80 x 30 x 60 cm.', NULL, 1),
  (21, 4, 'Rak Dapur Besi 4 Susun', 'Dapur', 'Modern', 'Besi', 'Sedang', 80, 40, 150, 560000, 4, 'Rak Dapur Besi 4 Susun untuk dapur bergaya modern, berangka besi kokoh dengan lapisan powder coating anti karat. Dimensi 80 x 40 x 150 cm.', NULL, 1),
  (22, 5, 'Tempat Tidur Jati Queen 160', 'Kamar Tidur', 'Klasik', 'Kayu Jati', 'Besar', 210, 170, 110, 4850000, 3, 'Tempat Tidur Jati Queen 160 untuk kamar tidur bergaya klasik, dibuat dari kayu jati solid pilihan dengan finishing natural doff yang menonjolkan keindahan serat kayu. Dimensi 210 x 170 x 110 cm.', NULL, 1),
  (23, 5, 'Dipan Minimalis Japandi 120', 'Kamar Tidur', 'Japandi', 'Kayu Jati', 'Sedang', 205, 125, 35, 2650000, 5, 'Dipan Minimalis Japandi 120 untuk kamar tidur bergaya japandi, dibuat dari kayu jati solid pilihan dengan finishing natural doff yang menonjolkan keindahan serat kayu. Dimensi 205 x 125 x 35 cm.', NULL, 1),
  (24, 5, 'Tempat Tidur Anak Pinus 90', 'Kamar Tidur', 'Skandinavia', 'Kayu Pinus', 'Sedang', 200, 95, 90, 1950000, 6, 'Tempat Tidur Anak Pinus 90 untuk kamar tidur bergaya skandinavia, menggunakan kayu pinus ringan dengan warna terang yang cocok untuk hunian bergaya Skandinavia. Dimensi 200 x 95 x 90 cm.', NULL, 1),
  (25, 6, 'Sofa Rotan Dua Dudukan', 'Teras & Taman', 'Japandi', 'Rotan', 'Sedang', 135, 70, 75, 2150000, 4, 'Sofa Rotan Dua Dudukan untuk teras & taman bergaya japandi, dianyam tangan dari rotan alami oleh pengrajin lokal, ringan dan sejuk dipakai. Dimensi 135 x 70 x 75 cm.', NULL, 1),
  (26, 6, 'Sofa Bed Lipat Minimalis', 'Ruang Kerja', 'Minimalis', 'Kayu & Besi', 'Besar', 180, 90, 85, 2750000, 7, 'Sofa Bed Lipat Minimalis untuk ruang kerja bergaya minimalis, memadukan rangka besi kokoh dengan permukaan kayu solid. Dimensi 180 x 90 x 85 cm.', NULL, 1),
  (27, 7, 'Bufet TV Jati 180', 'Ruang Tamu', 'Minimalis', 'Kayu Jati', 'Besar', 180, 45, 50, 2350000, 5, 'Bufet TV Jati 180 untuk ruang tamu bergaya minimalis, dibuat dari kayu jati solid pilihan dengan finishing natural doff yang menonjolkan keindahan serat kayu. Dimensi 180 x 45 x 50 cm.', NULL, 1),
  (28, 2, 'Meja Rias Cermin Oval', 'Kamar Tidur', 'Klasik', 'Kayu Mahoni', 'Sedang', 90, 45, 150, 1480000, 9, 'Meja Rias Cermin Oval untuk kamar tidur bergaya klasik, menggunakan kayu mahoni berkualitas dengan warna hangat dan serat halus. Dimensi 90 x 45 x 150 cm.', NULL, 1),
  (29, 8, 'Bangku Kaki Ranjang Rotan', 'Kamar Tidur', 'Japandi', 'Rotan', 'Kecil', 100, 40, 45, 620000, 12, 'Bangku Kaki Ranjang Rotan untuk kamar tidur bergaya japandi, dianyam tangan dari rotan alami oleh pengrajin lokal, ringan dan sejuk dipakai. Dimensi 100 x 40 x 45 cm.', NULL, 1),
  (30, 1, 'Kursi Kerja Kayu Ergonomis', 'Ruang Kerja', 'Modern', 'Kayu & Besi', 'Kecil', 55, 55, 95, 730000, 16, 'Kursi Kerja Kayu Ergonomis untuk ruang kerja bergaya modern, memadukan rangka besi kokoh dengan permukaan kayu solid. Dimensi 55 x 55 x 95 cm.', NULL, 1),
  (31, 2, 'Meja Teras Bundar Jati', 'Teras & Taman', 'Rustik', 'Kayu Jati', 'Kecil', 70, 70, 72, 690000, 20, 'Meja Teras Bundar Jati untuk teras & taman bergaya rustik, dibuat dari kayu jati solid pilihan dengan finishing natural doff yang menonjolkan keindahan serat kayu. Dimensi 70 x 70 x 72 cm.', NULL, 1),
  (32, 3, 'Lemari Arsip Klasik Ukir', 'Ruang Kerja', 'Klasik', 'Kayu Jati', 'Besar', 90, 45, 180, 2950000, 2, 'Lemari Arsip Klasik Ukir untuk ruang kerja bergaya klasik, dibuat dari kayu jati solid pilihan dengan finishing natural doff yang menonjolkan keindahan serat kayu. Dimensi 90 x 45 x 180 cm.', NULL, 0);
