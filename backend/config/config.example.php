<?php
/**
 * Berkas konfigurasi Toko Abimanyu (SKPL 4.1.B "Konfigurasi").
 *
 * Salin berkas ini menjadi config.php lalu sesuaikan nilainya.
 * Berkas ini berada di luar direktori publik (public/) sehingga
 * tidak dapat diakses langsung dari browser (NFR-05).
 */
return [
    // ---------------------------------------------------------------
    // Aplikasi
    // ---------------------------------------------------------------
    'app' => [
        'nama'        => 'Toko Abimanyu',
        // 'development' menampilkan detail error; 'production' menyembunyikannya (NFR-11).
        'env'         => 'development',
        // Kosongkan agar dideteksi otomatis. Isi bila perlu, mis. 'https://tokoabimanyu.id'
        'base_url'    => '',
        'timezone'    => 'Asia/Jakarta',
        // true = seluruh akses HTTP dialihkan ke HTTPS (aktifkan di hosting yang sudah ber-SSL).
        'force_https' => false,
    ],

    // ---------------------------------------------------------------
    // Basis data MySQL / MariaDB (PDO)
    // ---------------------------------------------------------------
    'db' => [
        // Nilai dapat ditimpa variabel lingkungan DB_HOST, DB_NAME, DB_USER, DB_PASS (opsional).
        'host'     => getenv('DB_HOST') ?: '127.0.0.1',
        'port'     => (int) (getenv('DB_PORT') ?: 3306),
        'database' => getenv('DB_NAME') ?: 'toko_abimanyu',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') !== false ? (string) getenv('DB_PASS') : '',
        'charset'  => 'utf8mb4',
    ],

    // ---------------------------------------------------------------
    // Nilai operasional (FR-29, FR-43, NFR-05)
    // ---------------------------------------------------------------
    'toko' => [
        'ambang_stok_menipis'     => 5,   // FR-29: stok < nilai ini = Stok Menipis
        'batas_waktu_bayar_jam'   => 24,  // FR-43: Transfer Bank & QRIS
        'sesi_admin_menit'        => 30,  // NFR-05: sesi admin berakhir tanpa aktivitas
        'katalog_per_halaman'     => 12,  // FR-02
        'admin_per_halaman'       => 20,  // NFR-07
        'maks_rekomendasi'        => 10,  // Algoritma #1 langkah 10
        'batas_pesanan'           => 3,   // FR-31: maks. pesanan ...
        'batas_pesanan_menit'     => 10,  // ... per 10 menit per sesi/alamat IP
        'maks_ukuran_unggah_mb'   => 2,   // FR-28, FR-33
    ],

    // Nomor WhatsApp admin: format internasional tanpa tanda plus (FR-09)
    'whatsapp' => '6281234567890',

    // Data tujuan pembayaran (FR-32). Gambar QRIS ditaruh di public/assets/img/
    'pembayaran' => [
        'bank_nama'      => 'BCA',
        'bank_lengkap'   => 'Bank Central Asia',
        'nomor_rekening' => '1234567890',
        'atas_nama'      => 'Toko Abimanyu',
        'qris_gambar'    => 'assets/img/qris.png',
        'qris_nama'      => 'Toko Abimanyu',
    ],

    // Pilihan atribut rekomendasi yang tampil sebagai dropdown (FR-19, UI-9, UI-19).
    // Tambahkan nilai baru di sini bila mitra menjual jenis produk baru.
    'atribut' => [
        'ruangan'  => ['Ruang Tamu', 'Ruang Makan', 'Kamar Tidur', 'Dapur', 'Ruang Kerja', 'Teras & Taman'],
        'gaya'     => ['Minimalis', 'Modern', 'Klasik', 'Japandi', 'Rustik', 'Skandinavia'],
        'material' => ['Kayu Jati', 'Kayu Mahoni', 'Kayu Pinus', 'Rotan', 'Besi', 'Kayu & Besi'],
        'ukuran'   => ['Kecil', 'Sedang', 'Besar'],
    ],

    // Lokasi penyimpanan berkas
    'path' => [
        // Foto produk (publik): public/uploads/produk/
        'upload_produk' => dirname(__DIR__, 2) . '/public/uploads/produk',
        // Bukti pembayaran (privat, di luar public/): backend/storage/bukti/
        'upload_bukti'  => dirname(__DIR__) . '/storage/bukti',
        'log'           => dirname(__DIR__) . '/storage/logs/app.log',
        'ratelimit'     => dirname(__DIR__) . '/storage/ratelimit',
    ],
];
