<?php /** Elemen <head> bersama. Variabel: $title */ ?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e(($title ?? '') !== '' ? $title . ' · Toko Abimanyu' : 'Toko Abimanyu') ?></title>
<meta name="description" content="Toko Abimanyu - perabot rumah kayu solid karya UMKM. Katalog dan pemesanan online.">
<meta name="csrf-token" content="<?= e(\App\Core\Csrf::token()) ?>">
<link rel="icon" href="<?= e(asset('assets/img/favicon.svg')) ?>" type="image/svg+xml">
<link rel="preload" href="<?= e(url('assets/fonts/material-symbols-outlined-latin-400-normal.woff2')) ?>" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
<?php foreach (['core', 'keranjang', 'unggah'] as $js): ?>
<script src="<?= e(asset('assets/js/' . $js . '.js')) ?>" defer></script>
<?php endforeach; ?>
