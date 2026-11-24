<?php
/**
 * Front controller Toko Abimanyu.
 * Seluruh permintaan diarahkan ke berkas ini (lihat .htaccess / router.php).
 */

require dirname(__DIR__) . '/backend/bootstrap.php';

$r = new App\Core\Router();
require BACKEND_PATH . '/routes.php';
$r->dispatch(App\Core\Request::method(), currentPath());
