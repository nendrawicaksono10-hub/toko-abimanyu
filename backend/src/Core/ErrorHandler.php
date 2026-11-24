<?php
namespace App\Core;

/**
 * Penanganan kesalahan terpusat (NFR-11):
 * detail kesalahan dicatat pada log server, pengguna hanya melihat pesan umum.
 */
final class ErrorHandler
{
    public static function register(): void
    {
        // display_errors selalu mati: pesan mentah PHP dapat membocorkan path & query.
        // Detail kesalahan hanya diperlihatkan di halaman error untuk akses lokal (lihat handle()).
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
        error_reporting(E_ALL);

        set_error_handler(function (int $no, string $str, string $file, int $line) {
            if (!(error_reporting() & $no)) {
                return false;
            }
            // Peringatan "deprecated" (mis. pada PHP 8.4/8.5 yang lebih baru) tidak boleh
            // menghentikan aplikasi: cukup dicatat di log.
            if ($no === E_DEPRECATED || $no === E_USER_DEPRECATED) {
                @error_log(sprintf("[%s] Deprecated: %s di %s:%d\n", date('Y-m-d H:i:s'), $str, $file, $line), 3, (string) Config::get('path.log'));
                return true;
            }
            // Pesan dari pustaka pihak ketiga (vendor/) ditangani bawaan PHP, tidak dijadikan exception.
            if (str_contains(str_replace('\\', '/', $file), '/vendor/')) {
                return false;
            }
            throw new \ErrorException($str, 0, $no, $file, $line);
        });

        set_exception_handler([self::class, 'handle']);
    }

    public static function log(\Throwable $e): void
    {
        $file = (string) Config::get('path.log');
        $dir = dirname($file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $msg = sprintf(
            "[%s] %s: %s di %s:%d\nURL: %s %s\n%s\n\n",
            date('Y-m-d H:i:s'), get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(),
            $_SERVER['REQUEST_METHOD'] ?? '-', $_SERVER['REQUEST_URI'] ?? '-', $e->getTraceAsString()
        );
        @error_log($msg, 3, $file);
    }

    public static function handle(\Throwable $e): void
    {
        self::log($e);
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        // Detail teknis hanya untuk mode development DAN akses dari komputer itu sendiri,
        // sehingga pengunjung dari HP/jaringan lain tidak pernah melihat detail error.
        $dev = Config::get('app.env') === 'development' && in_array(Request::ip(), ['127.0.0.1', '::1'], true);
        $isDb = $e instanceof \PDOException && in_array((int) $e->getCode(), [2002, 1045, 1049, 2006], true);
        $code = $isDb ? 503 : 500;

        if (Request::wantsJson()) {
            Response::json('error', null, 'Terjadi kesalahan pada sistem. Silakan coba beberapa saat lagi.', $code);
        }
        $data = [
            'title' => $isDb ? 'Layanan Sedang Gangguan' : 'Terjadi Kesalahan',
            'pesan' => $isDb
                ? 'Sistem sedang tidak dapat terhubung ke basis data. Silakan coba beberapa saat lagi.'
                : 'Maaf, terjadi kesalahan pada sistem. Silakan coba beberapa saat lagi.',
            'detail' => $dev ? get_class($e) . ': ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')' : null,
        ];
        try {
            Response::view('errors/500', $data, 'polos', $code);
        } catch (\Throwable) {
            http_response_code($code);
            echo '<h1>' . e($data['title']) . '</h1><p>' . e($data['pesan']) . '</p>';
        }
    }
}
