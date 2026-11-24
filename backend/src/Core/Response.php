<?php
namespace App\Core;

/**
 * Utilitas respons HTTP: HTML, redirect, JSON standar {status, data, message}.
 */
final class Response
{
    public static function html(string $html, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: text/html; charset=utf-8');
        echo $html;
        exit;
    }

    public static function view(string $template, array $data = [], ?string $layout = 'pelanggan', int $code = 200): never
    {
        self::html(View::render($template, $data, $layout), $code);
    }

    public static function redirect(string $path): never
    {
        $target = preg_match('#^https?://#', $path) ? $path : url($path);
        header('Location: ' . $target, true, 303);
        exit;
    }

    /** Redirect kembali ke halaman sebelumnya (fallback ke $fallback). */
    public static function back(string $fallback = '/'): never
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? '';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if ($ref !== '' && parse_url($ref, PHP_URL_HOST) === $host) {
            header('Location: ' . $ref, true, 303);
            exit;
        }
        self::redirect($fallback);
    }

    /** Format JSON standar untuk permintaan AJAX (SKPL 5.4). */
    public static function json(string $status, mixed $data = null, string $message = '', int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => $status, 'data' => $data, 'message' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function notFound(string $pesan = 'Halaman yang Anda cari tidak ditemukan.'): never
    {
        if (Request::wantsJson()) {
            self::json('error', null, $pesan, 404);
        }
        $isAdmin = str_starts_with(currentPath(), '/admin');
        self::view('errors/404', ['title' => 'Tidak Ditemukan', 'pesan' => $pesan, 'isAdmin' => $isAdmin], 'polos', 404);
    }
}
