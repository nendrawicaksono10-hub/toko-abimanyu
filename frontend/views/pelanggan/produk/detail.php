<?php
/**
 * UI-16 Halaman Detail Produk (PBI-13 / FR-05).
 * @var \App\Entity\Produk $produk
 */
$fotoUtama = fotoProduk($produk->getFotoUrl());
$stok = $produk->getStok();
$ambang = (int) config('toko.ambang_stok_menipis', 5);
$diKeranjang = (\App\Controller\KeranjangController::keranjang())[$produk->getIdProduk()] ?? 0;
$spes = [
    'Kategori'   => $produk->getNamaKategori(),
    'Ruangan'    => $produk->getRuangan(),
    'Gaya'       => $produk->getGaya(),
    'Material'   => $produk->getMaterial(),
    'Ukuran'     => $produk->getUkuran(),
    'Dimensi (P x L x T)' => $produk->getDimensi(),
    'Stok'       => $stok > 0 ? $stok . ' unit' : 'Habis',
];
?>
<div class="wadah pt-space-lg">
  <!-- LnkBreadcrumb -->
  <div class="flex items-center justify-between gap-3 py-space-md mb-space-lg border-b border-border-warm">
    <nav class="flex flex-wrap items-center gap-2 font-caption text-caption text-secondary normal-case tracking-normal" aria-label="Breadcrumb">
      <span class="material-symbols-outlined text-[16px]">home</span>
      <a href="<?= e(url('/')) ?>" class="hover:text-ink">Beranda</a><span class="text-outline-variant">/</span>
      <a href="<?= e(url('katalog')) ?>" class="hover:text-ink">Katalog</a><span class="text-outline-variant">/</span>
      <a href="<?= e(url('katalog', ['kategori' => $produk->getIdKategori()])) ?>" class="hover:text-ink"><?= e($produk->getNamaKategori()) ?></a><span class="text-outline-variant">/</span>
      <span class="text-ink font-semibold"><?= e($produk->getNama()) ?></span>
    </nav>
    <a href="<?= e(url('katalog')) ?>" class="hidden sm:flex items-center gap-1 font-label-sm text-label-sm text-secondary hover:text-ink shrink-0"><span class="material-symbols-outlined text-[16px]">arrow_back</span>Kembali ke Katalog</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl lg:gap-space-2xl">
    <!-- Foto Utama Produk -->
    <div class="lg:col-span-7 flex flex-col gap-space-md">
      <div class="relative aspect-square sm:aspect-[4/3] rounded-3xl overflow-hidden bg-surface-muted border border-border-warm">
        <?php if ($fotoUtama): ?>
          <img src="<?= e($fotoUtama) ?>" alt="<?= e($produk->getNama()) ?>" class="w-full h-full object-cover">
        <?php else: ?>
          <?= \App\Core\View::partial('partials/foto-produk', ['foto' => null, 'kategori' => $produk->getNamaKategori(), 'alt' => 'Foto produk belum tersedia', 'ikonKelas' => 'text-[140px]']) ?>
        <?php endif; ?>
        <?php if ($stok > 0 && $stok < $ambang): ?>
          <span class="absolute top-4 left-4 chip bg-warning-container text-warning border-warning-container">Sisa <?= $stok ?> unit</span>
        <?php elseif ($stok <= 0): ?>
          <span class="absolute top-4 left-4 chip bg-error-container text-on-error-container border-error-container">Habis</span>
        <?php endif; ?>
      </div>
    </div>
    <!-- Informasi produk -->
    <div class="lg:col-span-5 flex flex-col gap-space-lg">
      <div>
        <span class="eyebrow"><?= e($produk->getNamaKategori()) ?> · <?= e($produk->getRuangan()) ?></span>
        <h1 class="font-display text-headline-lg-mobile md:text-headline-lg text-ink mt-1"><?= e($produk->getNama()) ?></h1>
      </div>

      <div class="kartu p-space-md sm:p-space-lg">
        <p class="angka text-[28px] font-bold text-ink leading-none"><?= rupiah($produk->getHarga()) ?></p>
        <div class="flex flex-wrap items-center justify-between gap-2 mt-3 pt-3 border-t border-border-warm font-body-sm text-body-sm">
          <!-- LblStok -->
          <span class="flex items-center gap-2 <?= $stok <= 0 ? 'text-error' : 'text-success' ?>">
            <span class="w-2 h-2 rounded-full bg-current"></span>
            <?= $stok <= 0 ? 'Stok habis' : 'Tersedia ' . $stok . ' unit' ?>
          </span>
          <?php if ($stok > 0 && $stok < $ambang): ?><span class="text-warning">Sisa stok hanya <?= $stok ?> unit</span><?php endif; ?>
        </div>
      </div>

      <?php if ($produk->getDeskripsi()): ?>
        <p class="font-body-md text-body-md text-secondary whitespace-pre-line"><?= e($produk->getDeskripsi()) ?></p>
      <?php endif; ?>

      <!-- TblSpesifikasi -->
      <div class="kartu p-space-md sm:p-space-lg">
        <h2 class="eyebrow text-ink mb-space-md">Spesifikasi Produk</h2>
        <dl class="grid grid-cols-2 gap-x-space-lg gap-y-space-md">
          <?php foreach ($spes as $k => $v): ?>
            <div class="<?= $k === 'Dimensi (P x L x T)' ? 'col-span-2 sm:col-span-1' : '' ?>">
              <dt class="font-caption text-[10.5px] uppercase tracking-wider text-secondary"><?= e($k) ?></dt>
              <dd class="font-body-sm text-body-sm text-ink mt-0.5"><?= e($v) ?></dd>
            </div>
          <?php endforeach; ?>
        </dl>
      </div>

      <!-- StpJumlah + BtnKeranjang -->
      <form method="post" action="<?= e(url('keranjang/tambah')) ?>" data-tambah-keranjang class="flex flex-col gap-3">
        <?= csrf_field() ?>
        <input type="hidden" name="idProduk" value="<?= $produk->getIdProduk() ?>">
        <input type="hidden" name="kembali" value="1">
        <div class="kartu-muted rounded-2xl p-space-md flex items-center justify-between gap-3">
          <div>
            <span class="block eyebrow text-[10.5px] mb-1.5">Jumlah pesanan</span>
            <div class="flex items-center rounded-full border border-border-warm bg-surface-card" data-stepper>
              <button type="button" class="w-9 h-9 flex items-center justify-center text-secondary hover:text-ink" data-step="-1" aria-label="Kurangi"><span class="material-symbols-outlined text-[18px]">remove</span></button>
              <label for="jumlah" class="sr-only">Jumlah</label>
              <input id="jumlah" name="jumlah" type="number" inputmode="numeric" min="1" max="<?= max(1, $stok) ?>" value="1" class="w-10 text-center bg-transparent font-label-md text-label-md focus:outline-none" <?= $stok <= 0 ? 'disabled' : '' ?>>
              <button type="button" class="w-9 h-9 flex items-center justify-center text-secondary hover:text-ink" data-step="1" aria-label="Tambah"><span class="material-symbols-outlined text-[18px]">add</span></button>
            </div>
          </div>
          <div class="text-right">
            <span class="block eyebrow text-[10.5px]">Harga satuan</span>
            <span class="angka text-[18px] font-bold text-ink"><?= rupiah($produk->getHarga()) ?></span>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <button type="submit" class="btn-primer" <?= $stok <= 0 ? 'disabled' : '' ?>>
            <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span><?= $stok <= 0 ? 'Stok Habis' : 'Tambah ke Keranjang' ?>
          </button>
          <a href="<?= e(waLink('Halo Toko Abimanyu, saya ingin bertanya tentang produk "' . $produk->getNama() . '".')) ?>" target="_blank" rel="noopener" class="btn-sekunder">
            <span class="material-symbols-outlined text-[18px]">chat</span>Tanya via WhatsApp
          </a>
        </div>
        <?php if ($diKeranjang): ?>
          <p class="font-caption text-caption text-secondary normal-case tracking-normal"><?= $diKeranjang ?> unit produk ini sudah ada di <a href="<?= e(url('keranjang')) ?>" class="underline">keranjang</a>.</p>
        <?php endif; ?>
        <p class="font-caption text-caption text-secondary normal-case tracking-normal">Ongkos kirim dikonfirmasi admin melalui WhatsApp setelah pesanan dibuat.</p>
      </form>
    </div>
  </div>
</div>
