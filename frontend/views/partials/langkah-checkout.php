<?php /** Judul + indikator langkah checkout. Variabel: $langkah (1|2), $label */ ?>
<div class="pt-space-xl pb-space-md flex flex-col md:flex-row md:items-center justify-between gap-3">
  <nav class="flex items-center gap-2 font-caption text-caption text-secondary normal-case tracking-normal" aria-label="Breadcrumb">
    <a href="<?= e(url('/')) ?>" class="hover:text-ink">Beranda</a><span class="text-outline-variant">/</span>
    <span class="text-ink font-semibold"><?= e($label) ?></span>
  </nav>
  <ol class="flex items-center gap-2 p-1.5 rounded-full bg-surface-card border border-border-warm w-fit font-label-sm text-label-sm">
    <?php foreach ([1 => 'Data Pemesanan', 2 => 'Pembayaran & Konfirmasi'] as $n => $t): ?>
      <?php if ($n > 1): ?><li class="text-outline" aria-hidden="true"><span class="material-symbols-outlined text-[16px]">arrow_forward</span></li><?php endif; ?>
      <li class="flex items-center gap-2 px-3 py-1.5 rounded-full <?= $n === $langkah ? 'bg-primary text-on-primary' : ($n < $langkah ? 'text-primary' : 'text-secondary') ?>" <?= $n === $langkah ? 'aria-current="step"' : '' ?>>
        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[11px] <?= $n === $langkah ? 'bg-on-primary/20' : 'border border-border-warm' ?>"><?= $n < $langkah ? '✓' : $n ?></span><?= $t ?>
      </li>
    <?php endforeach; ?>
  </ol>
</div>
