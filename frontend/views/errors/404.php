<?php /** Halaman 404 ramah pengguna (NFR-11). */ $isAdmin = $isAdmin ?? false; ?>
<div class="min-h-screen flex items-center justify-center px-margin py-space-2xl bg-surface-card">
  <div class="max-w-lg text-center flex flex-col items-center gap-space-md">
    <span class="eyebrow">Kesalahan 404</span>
    <h1 class="font-display text-headline-lg md:text-display text-ink">Tidak Ditemukan</h1>
    <p class="font-body-lg text-body-lg text-secondary"><?= e($pesan ?? 'Halaman yang Anda cari tidak ditemukan.') ?></p>
    <div class="flex flex-wrap justify-center gap-3 pt-2">
      <?php if ($isAdmin): ?>
        <a href="<?= e(url('admin/produk')) ?>" class="btn-primer">Kembali ke Panel Admin</a>
      <?php else: ?>
        <a href="<?= e(url('katalog')) ?>" class="btn-primer">Lihat Katalog</a>
        <a href="<?= e(url('/')) ?>" class="btn-sekunder">Ke Beranda</a>
      <?php endif; ?>
    </div>
  </div>
</div>
