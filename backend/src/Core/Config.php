<?php
namespace App\Core;

/**
 * Pembaca berkas konfigurasi (backend/config/config.php).
 * Contoh: Config::get('toko.ambang_stok_menipis', 5)
 */
final class Config
{
    private static array $data = [];

    public static function load(string $file): void
    {
        if (!is_file($file)) {
            throw new \RuntimeException('Berkas konfigurasi tidak ditemukan. Salin config.example.php menjadi config.php.');
        }
        self::$data = require $file;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::$data;
        foreach (explode('.', $key) as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }
        return $value;
    }
}
