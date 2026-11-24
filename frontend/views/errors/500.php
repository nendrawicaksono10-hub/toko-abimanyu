<?php /** Halaman kesalahan umum - tanpa detail teknis di produksi (NFR-11). */ ?>
<div class="min-h-screen flex items-center justify-center px-margin py-space-2xl bg-surface-card">
  <div class="max-w-lg text-center flex flex-col items-center gap-space-md">
    <span class="material-symbols-outlined text-[48px] text-secondary">construction</span>
    <h1 class="font-display text-headline-lg-mobile md:text-headline-lg text-ink"><?= e($title ?? 'Terjadi Kesalahan') ?></h1>
    <p class="font-body-lg text-body-lg text-secondary"><?= e($pesan ?? '') ?></p>
    <?php if (!empty($detail)): ?>
      <pre class="w-full text-left whitespace-pre-wrap text-[12px] bg-surface-muted border border-border-warm rounded-xl p-4 text-on-surface-variant">[Mode development] <?= e($detail) ?></pre>
    <?php endif; ?>
    <a href="<?= e(url('/')) ?>" class="btn-primer mt-2">Kembali ke Beranda</a>
  </div>
</div>
