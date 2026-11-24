<?php
namespace App\Core;

/**
 * Validasi & penyimpanan unggahan gambar (FR-28, FR-33, NFR-05):
 * - tipe diperiksa dari ISI berkas (finfo + getimagesize), bukan ekstensi
 * - hanya JPG, PNG, WebP; ukuran maks. 2 MB (konfigurasi)
 * - nama berkas diacak
 * - batas resolusi (mencegah "decompression bomb") dan, bila ekstensi GD aktif,
 *   gambar digambar ulang (re-encode) sehingga metadata/EXIF & sisipan kode ikut terbuang
 */
final class ImageUploader
{
    private const MAKS_PIKSEL = 24_000_000;

    private const MIME = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    /**
     * Validasi satu berkas. Mengembalikan pesan kesalahan atau null bila valid.
     */
    public static function validate(?array $file, string $label = 'Berkas'): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return "{$label} wajib dipilih.";
        }
        $maxMb = (int) Config::get('toko.maks_ukuran_unggah_mb', 2);
        if (in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            return "{$label} melebihi ukuran maksimal {$maxMb} MB.";
        }
        if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            return "{$label} gagal diunggah. Silakan coba lagi.";
        }
        if ($file['size'] > $maxMb * 1024 * 1024) {
            return "{$label} melebihi ukuran maksimal {$maxMb} MB.";
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset(self::MIME[$mime])) {
            return "{$label} harus berupa gambar JPG, PNG, atau WebP.";
        }
        $info = @getimagesize($file['tmp_name']);
        if ($info === false || ($info[0] ?? 0) < 1) {
            return "{$label} rusak atau bukan gambar yang valid.";
        }
        if (($info[0] * $info[1]) > self::MAKS_PIKSEL) {
            return "{$label} terlalu besar resolusinya (maks. sekitar 6000 x 4000 piksel).";
        }
        return null;
    }

    /** Simpan berkas yang sudah tervalidasi. Mengembalikan nama berkas acak. */
    public static function store(array $file, string $dir): string
    {
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('Direktori unggahan tidak dapat dibuat.');
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $name = bin2hex(random_bytes(16)) . '.' . self::MIME[$mime];
        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
            throw new \RuntimeException('Berkas tidak dapat disimpan.');
        }
        @chmod($dir . '/' . $name, 0644);
        self::encodeUlang($dir . '/' . $name, $mime);
        return $name;
    }

    /** Gambar ulang berkas dengan GD (opsional; dilewati bila GD tidak tersedia). */
    private static function encodeUlang(string $path, string $mime): void
    {
        if (!function_exists('imagecreatefromstring')) {
            return;
        }
        $img = @imagecreatefromstring((string) file_get_contents($path));
        if ($img === false) {
            return;
        }
        $ok = match ($mime) {
            'image/jpeg' => @imagejpeg($img, $path, 88),
            'image/png'  => (imagesavealpha($img, true) && @imagepng($img, $path, 6)),
            'image/webp' => function_exists('imagewebp') ? @imagewebp($img, $path, 85) : true,
            default      => true,
        };
        unset($img); // imagedestroy() tidak diperlukan sejak PHP 8 (deprecated di 8.5)
        if (!$ok) {
            ErrorHandler::log(new \RuntimeException('Gagal re-encode gambar: ' . basename($path)));
        }
    }

    public static function delete(?string $name, string $dir): void
    {
        if ($name) {
            $path = $dir . '/' . basename($name);
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }
}
