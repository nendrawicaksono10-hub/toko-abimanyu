<?php
/**
 * Bootstrap aplikasi: autoload, konfigurasi, error handler, sesi.
 * Dipanggil oleh public/index.php (front controller).
 */

declare(strict_types=1);

define('BACKEND_PATH', __DIR__);
define('ROOT_PATH', dirname(__DIR__));

// Autoload kelas aplikasi: App\Controller\ProdukController -> src/Controller/ProdukController.php
spl_autoload_register(function (string $class) {
    if (str_starts_with($class, 'App\\')) {
        $file = __DIR__ . '/src/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});

require_once __DIR__ . '/src/Core/helpers.php';

use App\Core\Config;
use App\Core\ErrorHandler;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;

Config::load(__DIR__ . '/config/config.php');
date_default_timezone_set((string) Config::get('app.timezone', 'Asia/Jakarta'));
ErrorHandler::register();
View::setBasePath(ROOT_PATH . '/frontend/views');

// Alihkan HTTP ke HTTPS bila diaktifkan (NFR-05).
if (Config::get('app.force_https') && !Request::isHttps() && PHP_SAPI !== 'cli') {
    header('Location: https://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '/'), true, 301);
    exit;
}

// Header keamanan dasar.
if (PHP_SAPI !== 'cli') {
    header_remove('X-Powered-By'); // jangan umumkan versi PHP
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
    // CSP: hanya skrip dari situs sendiri (tidak ada skrip inline) -> meredam XSS.
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; "
        . "img-src 'self' data: blob:; font-src 'self'; connect-src 'self'; object-src 'none'; "
        . "base-uri 'self'; form-action 'self'; frame-ancestors 'self'");
    if (Request::isHttps()) {
        header('Strict-Transport-Security: max-age=31536000');
    }
    Session::start();
}
