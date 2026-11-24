<?php
/** Judul halaman admin. Variabel: $eyebrow (jejak/breadcrumb), $judul, $deskripsi, $aksi (HTML opsional) */
?>
<section class="flex flex-col lg:flex-row lg:items-end justify-between gap-space-lg">
  <div class="flex flex-col gap-1.5 max-w-3xl">
    <nav class="flex items-center gap-2 font-caption text-caption uppercase tracking-wider text-secondary" aria-label="Breadcrumb">
      <span>Panel Pengelola</span>
      <?php if (!empty($eyebrow)): ?><span class="text-outline-variant">/</span><span class="text-primary"><?= e($eyebrow) ?></span><?php endif; ?>
    </nav>
    <h1 class="font-display text-headline-lg-mobile md:text-headline-lg text-ink"><?= e($judul) ?></h1>
    <?php if (!empty($deskripsi)): ?><p class="font-body-md text-body-md text-secondary"><?= e($deskripsi) ?></p><?php endif; ?>
  </div>
  <?php if (!empty($aksi)): ?><div class="flex flex-wrap items-center gap-3 shrink-0"><?= $aksi ?></div><?php endif; ?>
</section>
