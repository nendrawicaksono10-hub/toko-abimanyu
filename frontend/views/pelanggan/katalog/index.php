<?php
/**
 * UI-15 Halaman Katalog Produk (FR-02, FR-03, FR-04, FR-44).
 * @var \App\Entity\Produk[] $produk
 * @var \App\Entity\Kategori[] $kategori
 * @var ?\App\Entity\Kategori $kategoriAktif
 * @var string $kataKunci, $urutan
 * @var \App\Core\Paginator $pg
 */
$idKat = $kategoriAktif?->getIdKategori();
$param = ['q' => $kataKunci, 'kategori' => $idKat, 'urut' => $urutan !== 'terbaru' ? $urutan : null];
$labelUrut = ['terbaru' => 'Terbaru', 'termurah' => 'Harga Termurah', 'termahal' => 'Harga Termahal'];
?>
<div class="wadah pt-space-xl pb-space-xl">
  <section class="flex flex-col gap-1 mb-space-lg pb-space-lg border-b border-border-warm">
    <nav class="flex items-center gap-2 font-caption text-caption text-secondary normal-case tracking-normal mb-1" aria-label="Breadcrumb">
      <a href="<?= e(url('/')) ?>" class="hover:text-ink">Beranda</a><span class="text-outline-variant">•</span>
      <span class="text-primary font-semibold">Katalog Perabot</span>
    </nav>
    <div class="flex flex-wrap items-center gap-3">
      <h1 class="font-display text-headline-lg-mobile md:text-headline-lg text-ink"><?= $kategoriAktif ? e($kategoriAktif->getNamaKategori()) : 'Katalog Perabot Rumah' ?></h1>
      <span class="inline-flex items-center px-3 py-1 rounded-full bg-surface-subtle border border-border-warm font-caption text-caption text-primary normal-case tracking-normal"><?= $totalAktif ?> produk</span>
    </div>
    <p class="font-body-lg text-body-lg text-secondary max-w-3xl">Telusuri seluruh koleksi perabot kami. Cari berdasarkan nama, kategori, material, atau ruangan, lalu urutkan sesuai kebutuhan Anda.</p>
  </section>

  <!-- Toolbar: TxtCariProduk, DdlKategori, DdlUrutan -->
  <section class="kartu p-space-md sm:p-5 shadow-sm mb-space-xl flex flex-col gap-4">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
      <form id="cari" action="<?= e(url('katalog')) ?>" method="get" role="search" class="relative flex-1 max-w-xl flex gap-2">
        <?php if ($idKat): ?><input type="hidden" name="kategori" value="<?= $idKat ?>"><?php endif; ?>
        <?php if ($urutan !== 'terbaru'): ?><input type="hidden" name="urut" value="<?= e($urutan) ?>"><?php endif; ?>
        <label for="q" class="sr-only">Cari produk</label>
        <div class="relative flex-1">
          <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-outline pointer-events-none">search</span>
          <input id="q" name="q" type="search" maxlength="100" value="<?= e($kataKunci) ?>" placeholder="Cari nama perabot, material, atau ruangan…" class="w-full pl-10 pr-4 py-2.5 bg-surface border border-border-warm rounded-full font-body-sm text-body-sm placeholder:text-secondary focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
        </div>
        <button type="submit" class="btn-primer btn-kecil">Cari</button>
      </form>
      <!-- DdlKategori + DdlUrutan: dropdown agar toolbar lebih ringkas (revisi dosen) -->
      <form method="get" action="<?= e(url('katalog')) ?>" class="flex flex-col sm:flex-row sm:items-center gap-2 md:self-auto" data-auto-submit aria-label="Filter katalog">
        <?php if ($kataKunci !== ''): ?><input type="hidden" name="q" value="<?= e($kataKunci) ?>"><?php endif; ?>
        <label for="kategori" class="sr-only">Kategori</label>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-outline pointer-events-none">category</span>
          <select id="kategori" name="kategori" class="input py-2.5 pl-10 w-full sm:w-auto sm:min-w-56 rounded-full font-label-sm text-label-sm bg-surface">
            <option value="">Kategori: Semua Perabot (<?= $totalAktif ?>)</option>
            <?php foreach ($kategori as $k): if ($k->getJumlahProduk() === 0 && $k->getIdKategori() !== $idKat) continue; ?>
              <option value="<?= $k->getIdKategori() ?>" <?= $k->getIdKategori() === $idKat ? 'selected' : '' ?>>Kategori: <?= e($k->getNamaKategori()) ?> (<?= $k->getJumlahProduk() ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <label for="urut" class="sr-only">Urutkan</label>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-outline pointer-events-none">swap_vert</span>
          <select id="urut" name="urut" class="input py-2.5 pl-10 w-full sm:w-auto rounded-full font-label-sm text-label-sm bg-surface">
            <?php foreach ($labelUrut as $val => $lab): ?>
              <option value="<?= $val ?>" <?= $urutan === $val ? 'selected' : '' ?>>Urutkan: <?= $lab ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <noscript><button class="btn-sekunder btn-kecil">Terapkan</button></noscript>
      </form>
    </div>
    <div class="flex flex-wrap items-center justify-between gap-2 border-t border-border-warm pt-3 font-body-sm text-body-sm text-secondary">
      <span class="flex flex-wrap items-center gap-2">
        <?php if ($pg->total > 0): ?>Menampilkan <strong class="text-primary"><?= $pg->dari() ?>–<?= $pg->sampai() ?></strong> dari <?= $pg->total ?> produk<?php else: ?>0 produk<?php endif; ?>
        <span class="text-outline-variant">•</span>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-surface-subtle text-primary font-caption text-caption normal-case tracking-normal"><?= $kategoriAktif ? e($kategoriAktif->getNamaKategori()) : 'Semua Kategori' ?><?= $kataKunci !== '' ? ' · “' . e($kataKunci) . '”' : '' ?></span>
      </span>
      <?php if ($kataKunci !== '' || $idKat || $urutan !== 'terbaru'): ?>
        <a href="<?= e(url('katalog')) ?>" class="underline font-label-sm text-label-sm hover:text-ink">Reset Filter</a>
      <?php endif; ?>
    </div>
  </section>

  <!-- ListProduk -->
  <?php if ($produk): ?>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-gutter-desktop">
      <?php foreach ($produk as $p): ?>
        <?= \App\Core\View::partial('partials/kartu-produk', ['p' => $p]) ?>
      <?php endforeach; ?>
    </div>
    <div class="mt-space-2xl">
      <?= \App\Core\View::partial('partials/pagination', ['pg' => $pg, 'path' => 'katalog', 'query' => $param]) ?>
    </div>
  <?php else: ?>
    <!-- LblPesanKosong -->
    <div class="kartu p-space-2xl text-center flex flex-col items-center gap-3">
      <span class="material-symbols-outlined text-[44px] text-outline">search_off</span>
      <h2 class="font-headline-sm text-headline-sm text-ink">Produk tidak ditemukan</h2>
      <p class="font-body-md text-body-md text-secondary max-w-md">Coba kata kunci lain atau pilih kategori “Semua Perabot” untuk melihat seluruh koleksi kami.</p>
      <div class="flex flex-wrap justify-center gap-3 mt-2">
        <a href="<?= e(url('katalog')) ?>" class="btn-primer">Tampilkan Semua Produk</a>
      </div>
    </div>
  <?php endif; ?>

  <!-- Bantuan WhatsApp -->
  <section class="mt-space-3xl rounded-3xl bg-surface-subtle border border-border-warm p-space-lg md:p-space-2xl flex flex-col md:flex-row md:items-center justify-between gap-space-lg">
    <div class="max-w-xl">
      <span class="eyebrow">Bantuan Pelanggan</span>
      <h2 class="font-headline-sm text-headline-sm md:text-headline-md text-ink mt-1">Bingung Memilih Perabot?</h2>
      <p class="font-body-sm text-body-sm text-secondary mt-2">Hubungi admin Toko Abimanyu melalui WhatsApp untuk konsultasi ukuran, material, atau ketersediaan produk.</p>
    </div>
    <div class="flex flex-wrap gap-3">
      <a href="<?= e(waLink('Halo Toko Abimanyu, saya butuh bantuan memilih perabot.')) ?>" target="_blank" rel="noopener" class="btn-primer"><span class="material-symbols-outlined text-[18px]">chat</span>Tanya via WhatsApp</a>
    </div>
  </section>
</div>
