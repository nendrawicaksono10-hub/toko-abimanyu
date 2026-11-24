<?php
/**
 * Foto produk atau gambar pengganti (placeholder) bila foto tidak tersedia (NFR-11).
 * Variabel: $foto (nama berkas|null), $kategori (string), $alt, $kelas (kelas <img>), $ikonKelas
 * Gambar yang gagal dimuat diganti placeholder oleh core.js (atribut data-fallback), tanpa JS inline (CSP).
 */
$src = fotoProduk($foto ?? null);
if ($src): ?>
  <img src="<?= e($src) ?>" alt="<?= e($alt ?? '') ?>" loading="lazy" data-fallback class="<?= $kelas ?? 'w-full h-full object-cover' ?>">
<?php else: ?>
  <div class="w-full h-full flex flex-col items-center justify-center gap-2 bg-surface-subtle text-outline" role="img" aria-label="<?= e($alt ?? 'Foto belum tersedia') ?>">
    <span class="material-symbols-outlined <?= $ikonKelas ?? 'text-[56px]' ?>"><?= e(ikonKategori($kategori ?? '')) ?></span>
  </div>
<?php endif;
