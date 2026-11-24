<?php
/**
 * Fungsi bantu global yang dipakai controller dan template.
 */

use App\Core\Config;
use App\Core\Csrf;

/** Escape output untuk mencegah XSS (NFR-05). */
function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Base path aplikasi, mis. '' atau '/toko-abimanyu/public'. */
function basePath(): string
{
    static $base = null;
    if ($base === null) {
        $cfg = (string) Config::get('app.base_url', '');
        if ($cfg !== '') {
            $base = rtrim((string) parse_url($cfg, PHP_URL_PATH), '/');
        } else {
            $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
            $base = rtrim($dir === '.' ? '' : $dir, '/');
            // Proyek diletakkan utuh di htdocs dan diakses tanpa "/public" (lihat .htaccess akar).
            $uri = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
            if (str_ends_with($base, '/public') && !str_starts_with($uri, $base)) {
                $base = substr($base, 0, -7);
            }
        }
    }
    return $base;
}

function url(string $path = '/', array $query = []): string
{
    $u = basePath() . '/' . ltrim($path, '/');
    if ($query) {
        $query = array_filter($query, fn($v) => $v !== null && $v !== '');
        if ($query) {
            $u .= '?' . http_build_query($query);
        }
    }
    return $u === '' ? '/' : $u;
}

function asset(string $path): string
{
    $file = dirname(__DIR__, 3) . '/public/' . ltrim($path, '/');
    $v = is_file($file) ? '?v=' . filemtime($file) : '';
    return basePath() . '/' . ltrim($path, '/') . $v;
}

/** Path permintaan saat ini relatif terhadap base path. */
function currentPath(): string
{
    $uri = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $base = basePath();
    if ($base !== '' && str_starts_with($uri, $base)) {
        $uri = substr($uri, strlen($base));
    }
    if (str_starts_with($uri, '/index.php')) {
        $uri = substr($uri, 10);
    }
    $uri = '/' . trim($uri, '/');
    return $uri;
}

function isActive(string $prefix, bool $exact = false): bool
{
    $p = currentPath();
    return $exact ? $p === $prefix : ($p === $prefix || str_starts_with($p, rtrim($prefix, '/') . '/'));
}

function csrf_field(): string
{
    return Csrf::field();
}

function config(string $key, mixed $default = null): mixed
{
    return Config::get($key, $default);
}

/** Format rupiah: Rp 1.250.000 */
function rupiah(float|int|string|null $n): string
{
    return 'Rp ' . number_format((float) $n, 0, ',', '.');
}

/** FR-21: ID pesanan ditampilkan dengan format ORD-nnnnnn. */
function kodePesanan(int $id): string
{
    return 'ORD-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
}

/** Ambil angka ID dari "ORD-000123" atau "123". Null jika format tidak valid. */
function parseKodePesanan(string $kode): ?int
{
    $kode = strtoupper(trim($kode));
    if (preg_match('/^(?:ORD-?)?0*(\d{1,9})$/', $kode, $m)) {
        $id = (int) $m[1];
        return $id > 0 ? $id : null;
    }
    return null;
}

/**
 * FR-08: normalisasi nomor kontak ke format 62xxxxxxxxxx.
 * Menerima 0812..., +62812..., 62812..., 812... (boleh berisi spasi, titik, tanda hubung).
 * Mengembalikan null jika bukan angka 8-15 digit.
 */
function normalisasiKontak(string $kontak): ?string
{
    $k = preg_replace('/[\s\-\.\(\)]/', '', trim($kontak));
    if ($k === null || $k === '') {
        return null;
    }
    if (str_starts_with($k, '+')) {
        $k = substr($k, 1);
    }
    if (!preg_match('/^\d{8,15}$/', $k)) {
        return null;
    }
    if (str_starts_with($k, '62')) {
        $n = $k;
    } elseif (str_starts_with($k, '0')) {
        $n = '62' . substr($k, 1);
    } else {
        $n = '62' . $k;
    }
    return strlen($n) <= 20 ? $n : null;
}

/** Tampilan kontak: +62 812-3456-7890 */
function formatKontak(string $kontak): string
{
    $rest = str_starts_with($kontak, '62') ? substr($kontak, 2) : $kontak;
    $parts = str_split($rest, 4);
    return '+62 ' . implode('-', $parts);
}

const BULAN_ID = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
const BULAN_PANJANG_ID = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

/** 18 Mei 2026, 14.32 WIB */
function tanggalIndo(?string $datetime, bool $denganJam = true, bool $bulanPanjang = false): string
{
    if (!$datetime) {
        return '-';
    }
    $t = strtotime($datetime);
    if ($t === false) {
        return '-';
    }
    $bulan = ($bulanPanjang ? BULAN_PANJANG_ID : BULAN_ID)[(int) date('n', $t)];
    $s = date('j', $t) . ' ' . $bulan . ' ' . date('Y', $t);
    if ($denganJam) {
        $s .= ', ' . date('H.i', $t) . ' WIB';
    }
    return $s;
}

/** Nilai input lama (setelah validasi gagal). */
function old(array $old, string $key, mixed $default = ''): string
{
    return e($old[$key] ?? $default);
}

/** URL foto produk; null jika tidak ada atau berkas hilang (dipakai placeholder). */
function fotoProduk(?string $fotoUrl): ?string
{
    if (!$fotoUrl) {
        return null;
    }
    $file = Config::get('path.upload_produk') . '/' . basename($fotoUrl);
    return is_file($file) ? url('uploads/produk/' . rawurlencode(basename($fotoUrl))) : null;
}

/** Ikon Material Symbols berdasarkan nama kategori (untuk placeholder & kartu kategori). */
function ikonKategori(?string $nama): string
{
    $n = mb_strtolower((string) $nama);
    $map = [
        'sofa' => 'weekend', 'kursi' => 'chair', 'bangku' => 'chair_alt', 'meja' => 'table_restaurant',
        'lemari' => 'door_sliding', 'rak' => 'shelves', 'tidur' => 'bed', 'kasur' => 'bed',
        'nakas' => 'countertops', 'lampu' => 'light', 'cermin' => 'filter_frames', 'buffet' => 'countertops',
        'bufet' => 'countertops', 'dapur' => 'kitchen', 'taman' => 'deck', 'dekor' => 'potted_plant',
    ];
    foreach ($map as $kata => $ikon) {
        if (str_contains($n, $kata)) {
            return $ikon;
        }
    }
    return 'chair';
}

/** FR-09: tautan WhatsApp https://wa.me/<nomor>?text=<pesan ter-encode> */
function waLink(string $pesan = '', ?string $nomor = null): string
{
    $nomor ??= (string) Config::get('whatsapp');
    $u = 'https://wa.me/' . preg_replace('/\D/', '', $nomor);
    return $pesan !== '' ? $u . '?text=' . rawurlencode($pesan) : $u;
}

/** Kelas badge status pesanan/pembayaran (monokrom). */
function badgeStatus(string $status): string
{
    return match ($status) {
        'Lunas', 'Selesai'                 => 'bg-sage text-success border-sage',
        'Diproses', 'Dikirim'              => 'bg-surface-subtle text-primary border-border-warm',
        'Menunggu Verifikasi', 'Menunggu Konfirmasi' => 'bg-warning-container text-warning border-warning-container',
        'Ditolak', 'Dibatalkan'            => 'bg-error-container text-on-error-container border-error-container',
        default                            => 'bg-surface-muted text-on-surface-variant border-border-warm',
    };
}

/** Status stok berdasarkan ambang batas konfigurasi (Algoritma #3). */
function statusStok(int $stok): string
{
    if ($stok <= 0) {
        return 'Habis';
    }
    return $stok < (int) Config::get('toko.ambang_stok_menipis', 5) ? 'Stok Menipis' : 'Aman';
}

/** Jumlah item (total kuantitas) pada keranjang sesi, untuk ikon keranjang (UI-14). */
function jumlahKeranjang(): int
{
    $k = $_SESSION['keranjang'] ?? [];
    return is_array($k) ? (int) array_sum($k) : 0;
}

/** Username admin yang sedang login. */
function adminLogin(): ?string
{
    return $_SESSION['admin'] ?? null;
}

/**
 * Pecah kata kunci pencarian menjadi token huruf kecil (FR-03).
 * Spasi berlebih diabaikan, maks. 6 kata, tiap kata maks. 40 karakter.
 * @return string[]
 */
function tokenPencarian(string $kataKunci): array
{
    $kata = preg_split('/\s+/u', mb_strtolower(trim($kataKunci), 'UTF-8'), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $kata = array_map(fn($k) => mb_substr($k, 0, 40), $kata);
    return array_slice(array_values(array_unique($kata)), 0, 6);
}

/** Versi PHP dari pencarian toleran: true bila setiap token ada di salah satu teks. */
function cocokKataKunci(string $kataKunci, string ...$teks): bool
{
    $gabung = mb_strtolower(implode(' ', $teks), 'UTF-8');
    foreach (tokenPencarian($kataKunci) as $t) {
        if (!str_contains($gabung, $t)) {
            return false;
        }
    }
    return true;
}
