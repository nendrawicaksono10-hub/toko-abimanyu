<?php
namespace App\Core;

/**
 * Pembungkus sederhana untuk data permintaan HTTP.
 */
final class Request
{
    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    /** Permintaan AJAX (fetch) yang mengharapkan JSON. */
    public static function wantsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return str_contains($accept, 'application/json') || strtolower($xhr) === 'xmlhttprequest';
    }

    /** Ambil string dari POST/GET, sudah di-trim. */
    public static function str(string $key, string $default = '', ?array $source = null): string
    {
        $source ??= $_POST + $_GET;
        $v = $source[$key] ?? $default;
        return is_string($v) ? trim($v) : $default;
    }

    public static function post(string $key, string $default = ''): string
    {
        return self::str($key, $default, $_POST);
    }

    public static function query(string $key, string $default = ''): string
    {
        return self::str($key, $default, $_GET);
    }

    /** Bilangan bulat positif dari query, atau null jika tidak valid. */
    public static function queryInt(string $key): ?int
    {
        $v = $_GET[$key] ?? null;
        if (!is_string($v) || !preg_match('/^\d{1,9}$/', $v)) {
            return null;
        }
        return (int) $v;
    }

    public static function file(string $key): ?array
    {
        return isset($_FILES[$key]) && is_array($_FILES[$key]) ? $_FILES[$key] : null;
    }

    /** Normalisasi input <input type="file" multiple> menjadi daftar berkas. */
    public static function files(string $key): array
    {
        $f = $_FILES[$key] ?? null;
        if (!$f || !is_array($f['name'] ?? null)) {
            return [];
        }
        $out = [];
        foreach ($f['name'] as $i => $name) {
            if (($f['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $out[] = [
                'name' => $name, 'type' => $f['type'][$i], 'tmp_name' => $f['tmp_name'][$i],
                'error' => $f['error'][$i], 'size' => $f['size'][$i],
            ];
        }
        return $out;
    }

    public static function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public static function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
            || (($_SERVER['SERVER_PORT'] ?? '') == 443);
    }
}
