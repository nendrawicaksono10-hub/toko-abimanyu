<?php
namespace App\Core;

/**
 * Pengelolaan sesi PHP yang aman (NFR-05):
 * cookie HttpOnly + SameSite (+ Secure bila HTTPS), ID sesi diperbarui setelah login,
 * dan sesi admin berakhir setelah N menit tanpa aktivitas (nilai dari konfigurasi).
 */
final class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_name('ABIMANYUSESSID');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => Request::isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    /** Hapus seluruh data sesi (logout). */
    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    // ---------------- Flash message (ditampilkan sekali) ----------------

    /** @param string $type sukses|gagal|info|peringatan */
    public static function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }

    public static function pullFlash(): array
    {
        $f = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $f;
    }

    /** Simpan input lama agar form tidak kosong saat validasi gagal. */
    public static function flashInput(array $input): void
    {
        $_SESSION['_old'] = $input;
    }

    public static function pullOld(): array
    {
        $o = $_SESSION['_old'] ?? [];
        unset($_SESSION['_old']);
        return $o;
    }

    public static function flashErrors(array $errors): void
    {
        $_SESSION['_errors'] = $errors;
    }

    public static function pullErrors(): array
    {
        $e = $_SESSION['_errors'] ?? [];
        unset($_SESSION['_errors']);
        return $e;
    }
}
