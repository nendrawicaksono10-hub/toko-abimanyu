<?php /** Layout polos (login admin & halaman kesalahan). */ ?>
<!DOCTYPE html>
<html lang="id">
<head>
<?= \App\Core\View::partial('partials/head', ['title' => $title ?? '']) ?>
</head>
<body class="bg-surface font-body-md text-on-surface antialiased min-h-screen">
<?= $content ?>
</body>
</html>
