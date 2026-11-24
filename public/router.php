<?php
/**
 * Router untuk server bawaan PHP (pengembangan lokal saja):
 *   php -S localhost:8000 -t public public/router.php
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;
if ($path !== '/' && is_file($file)) {
    // Jangan jalankan skrip di folder unggahan.
    if (str_starts_with($path, '/uploads/') && preg_match('/\.php\d?$|\.phtml$/i', $path)) {
        http_response_code(403);
        exit;
    }
    return false;
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
