<?php
/**
 * Navigasi halaman (NavHalaman). Mempertahankan parameter query lain.
 * Variabel: $pg (Paginator), $path (string), $query (array parameter lain)
 */
if ($pg->totalHalaman <= 1) return;
$link = fn(int $h) => url($path, array_merge($query ?? [], ['hal' => $h > 1 ? $h : null]));
?>
<nav class="flex items-center justify-center gap-2 flex-wrap" aria-label="Navigasi halaman">
  <?php if ($pg->halaman > 1): ?>
    <a href="<?= e($link($pg->halaman - 1)) ?>" class="btn-sekunder btn-kecil"><span class="material-symbols-outlined text-[16px]">arrow_back</span>Sebelumnya</a>
  <?php else: ?>
    <span class="btn-sekunder btn-kecil opacity-40 pointer-events-none"><span class="material-symbols-outlined text-[16px]">arrow_back</span>Sebelumnya</span>
  <?php endif; ?>
  <?php foreach ($pg->nomor() as $n): ?>
    <?php if ($n === '…'): ?>
      <span class="px-1 text-secondary">…</span>
    <?php elseif ($n === $pg->halaman): ?>
      <span class="w-9 h-9 rounded-full bg-primary text-on-primary flex items-center justify-center font-label-sm text-label-sm" aria-current="page"><?= $n ?></span>
    <?php else: ?>
      <a href="<?= e($link($n)) ?>" class="w-9 h-9 rounded-full border border-border-warm hover:border-primary flex items-center justify-center font-label-sm text-label-sm"><?= $n ?></a>
    <?php endif; ?>
  <?php endforeach; ?>
  <?php if ($pg->halaman < $pg->totalHalaman): ?>
    <a href="<?= e($link($pg->halaman + 1)) ?>" class="btn-sekunder btn-kecil">Berikutnya<span class="material-symbols-outlined text-[16px]">arrow_forward</span></a>
  <?php else: ?>
    <span class="btn-sekunder btn-kecil opacity-40 pointer-events-none">Berikutnya<span class="material-symbols-outlined text-[16px]">arrow_forward</span></span>
  <?php endif; ?>
</nav>
