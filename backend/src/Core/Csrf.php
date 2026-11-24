<?php
namespace App\Core;

/**
 * Token CSRF per sesi untuk setiap form POST (NFR-05, FR-31).
 */
final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    /** Ganti token (dipanggil saat login/logout agar token lama tidak berlaku lagi). */
    public static function rotate(): void
    {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(self::token(), ENT_QUOTES) . '">';
    }

    public static function valid(): bool
    {
        $sent = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        return is_string($sent) && !empty($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $sent);
    }

    /**
     * Token sekali pakai untuk mencegah pengiriman form ganda (double submit).
     */
    public static function formToken(string $form): string
    {
        $t = bin2hex(random_bytes(16));
        $_SESSION['_form_tokens'][$form] = $t;
        return $t;
    }

    public static function consumeFormToken(string $form, string $sent): bool
    {
        $t = $_SESSION['_form_tokens'][$form] ?? null;
        if ($t === null || !hash_equals($t, $sent)) {
            return false;
        }
        unset($_SESSION['_form_tokens'][$form]);
        return true;
    }
}
