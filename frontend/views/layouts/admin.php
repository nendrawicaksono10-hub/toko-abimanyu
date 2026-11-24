<?php
/**
 * Layout panel admin (sidebar hijau tua + topbar) — Progres Sprint 1–3:
 * Produk, Kategori, Stok (dengan badge stok menipis), Atribut Rekomendasi, Ubah Password, Logout.
 */
try {
    $ambangStok = (int) config('toko.ambang_stok_menipis', 5);
    $badgeStok = count((new \App\Repository\ProdukRepository())->getStokMenipis($ambangStok));
} catch (\Throwable) {
    $badgeStok = 0;
}
$grup = [
    'Katalog & Inventaris' => [
        ['/admin/produk', 'Produk', 'chair', false, 0],
        ['/admin/kategori', 'Kategori', 'category', false, 0],
        ['/admin/stok', 'Stok', 'inventory_2', false, $badgeStok],
        ['/admin/atribut', 'Atribut Rekomendasi', 'auto_awesome', false, 0],
    ],
];
$link = function (string $path, string $label, string $ikon, bool $exact, int $badge): string {
    $on = $exact ? isActive($path, true) : isActive($path);
    $h = '<a href="' . e(url($path)) . '" class="flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-xl transition-all '
       . ($on ? 'bg-primary-hover text-on-primary font-semibold shadow-sm' : 'text-on-forest-muted hover:bg-primary hover:text-on-primary') . '"'
       . ($on ? ' aria-current="page"' : '') . '>'
       . '<span class="flex items-center gap-3"><span class="material-symbols-outlined text-[20px]">' . $ikon . '</span><span class="font-body-md text-body-md">' . e($label) . '</span></span>';
    if ($badge > 0) {
        $h .= '<span class="min-w-5 h-5 px-1.5 rounded-full bg-warning text-on-primary flex items-center justify-center font-caption text-[11px]">' . $badge . '</span>';
    }
    return $h . '</a>';
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?= \App\Core\View::partial('partials/head', ['title' => ($title ?? '') . ' · Admin']) ?>
</head>
<body class="bg-surface font-body-md text-body-md text-on-surface antialiased">

<div id="sidebar-latar" class="fixed inset-0 bg-ink/30 z-40 hidden lg:hidden" data-tutup-sidebar></div>
<aside id="sidebar" class="fixed left-0 top-0 h-screen w-72 bg-forest text-on-primary z-50 flex flex-col justify-between overflow-y-auto -translate-x-full lg:translate-x-0 transition-transform duration-200">
  <div class="p-space-lg">
    <div class="flex items-center justify-between gap-2 pb-space-lg border-b border-on-primary/10">
      <a href="<?= e(url('admin/produk')) ?>" class="flex items-center gap-3">
        <span class="w-10 h-10 rounded-full bg-primary-hover flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">chair</span></span>
        <span>
          <span class="block font-display text-[21px] leading-tight">Toko Abimanyu</span>
          <span class="block font-caption text-[10.5px] text-on-forest-muted uppercase tracking-[0.16em]">Panel Admin</span>
        </span>
      </a>
      <button type="button" class="lg:hidden p-1 text-on-forest-muted" data-tutup-sidebar aria-label="Tutup menu"><span class="material-symbols-outlined">close</span></button>
    </div>
    <nav aria-label="Menu admin">
      <?php foreach ($grup as $judulGrup => $items): ?>
        <p class="pt-space-lg pb-2 px-3.5 font-caption text-[10.5px] text-on-forest-muted uppercase tracking-[0.16em]"><?= e($judulGrup) ?></p>
        <div class="flex flex-col gap-1">
          <?php foreach ($items as $it): ?><?= $link(...$it) ?><?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </nav>
  </div>
  <div class="p-space-lg border-t border-on-primary/10 flex flex-col gap-1">
    <?= $link('/admin/password', 'Ubah Password', 'lock_reset', false, 0) ?>
    <form method="post" action="<?= e(url('admin/logout')) ?>">
      <?= csrf_field() ?>
      <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-on-forest-muted hover:bg-error hover:text-on-primary transition-colors">
        <span class="material-symbols-outlined text-[20px]">logout</span><span class="font-body-md text-body-md">Logout</span>
      </button>
    </form>
  </div>
</aside>

<div class="lg:pl-72 min-h-screen flex flex-col">
  <header class="sticky top-0 h-16 bg-surface/90 backdrop-blur-xl border-b border-border-warm z-30 px-margin lg:px-space-2xl flex items-center justify-between gap-3">
    <div class="flex items-center gap-3 min-w-0">
      <button type="button" class="lg:hidden w-9 h-9 -ml-1 rounded-full flex items-center justify-center hover:bg-surface-subtle" data-buka-sidebar aria-label="Buka menu"><span class="material-symbols-outlined">menu</span></button>
      <span class="font-display text-[20px] text-ink truncate">Halo, <?= e(adminLogin()) ?></span>
      <span class="hidden md:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-muted font-caption text-[11px] text-on-surface-variant normal-case tracking-normal"><span class="material-symbols-outlined text-[15px]">calendar_today</span><?= e(tanggalIndo(date('Y-m-d'), false)) ?></span>
    </div>
    <div class="flex items-center gap-3">
      <a href="<?= e(url('/')) ?>" target="_blank" rel="noopener" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-surface-subtle border border-border-warm text-on-surface-variant hover:text-ink font-body-sm text-body-sm"><span class="material-symbols-outlined text-[18px]">storefront</span>Kunjungi Toko</a>
      <span class="w-9 h-9 rounded-full bg-primary flex items-center justify-center" title="<?= e(adminLogin()) ?>"><span class="material-symbols-outlined text-on-primary text-[18px]">person</span></span>
    </div>
  </header>

  <main class="flex-1 w-full px-margin lg:px-space-2xl py-space-xl">
    <div class="flex flex-col w-full gap-space-xl max-w-[1400px] mx-auto">
      <?php if (\App\Core\Session::get('pw_bawaan')): ?>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 py-3 rounded-2xl bg-warning-container border border-warning-container text-warning" role="alert">
          <p class="flex items-start gap-2 font-body-sm text-body-sm"><span class="material-symbols-outlined text-[20px]">gpp_maybe</span><span><strong>Keamanan:</strong> akun ini masih memakai password bawaan instalasi. Segera ganti agar panel admin tidak dapat dimasuki orang lain.</span></p>
          <a href="<?= e(url('admin/password')) ?>" class="btn-primer btn-kecil shrink-0">Ganti Password</a>
        </div>
      <?php endif; ?>
      <?= \App\Core\View::partial('partials/flash') ?>
      <?= $content ?>
    </div>
  </main>
</div>
</body>
</html>
