<?php
/**
 * UI-14 Halaman Beranda (FR-01).
 * @var \App\Entity\Produk[] $produkUnggulan  maks. 8 produk aktif terlaris (atau terbaru)
 * @var \App\Entity\Kategori[] $kategoriPopuler maks. 6 kategori terlaris
 */
$sorotan = $produkUnggulan[0] ?? null;
?>
<!-- HERO -->
<section class="wadah pt-space-xl lg:pt-space-2xl">
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl lg:gap-space-2xl items-center">
    <div class="lg:col-span-6 flex flex-col items-start gap-space-lg">
      <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sage text-on-sage font-caption text-[11.5px] normal-case tracking-normal">
        <span class="material-symbols-outlined text-[16px]">verified</span>Perabot kayu solid · Karya pengrajin UMKM
      </span>
      <h1 class="font-display text-headline-lg-mobile sm:text-headline-lg lg:text-display text-ink">
        Dirancang untuk Kenyamanan.<br>
        <span class="italic text-primary-hover">Dibuat untuk Kehidupan.</span>
      </h1>
      <p class="font-body-lg text-body-lg text-secondary max-w-xl">Temukan meja, kursi, lemari, dan rak untuk setiap sudut rumah Anda — pesan langsung dari katalog dan bayar dengan cara yang Anda pilih.</p>
      <div class="flex flex-wrap items-center gap-space-md">
        <a href="<?= e(url('katalog')) ?>" class="btn-primer">Belanja Sekarang</a>
        <a href="<?= e(waLink('Halo Toko Abimanyu, saya ingin bertanya tentang produk perabot rumah.')) ?>" target="_blank" rel="noopener" class="btn-sekunder"><span class="material-symbols-outlined text-[18px]">chat</span>Tanya via WhatsApp</a>
      </div>
    </div>
    <div class="lg:col-span-6">
      <?php if ($sorotan): ?>
      <a href="<?= e(url('produk/' . $sorotan->getIdProduk())) ?>" class="relative block w-full aspect-[4/3] rounded-3xl overflow-hidden bg-surface-muted border border-border-warm group">
        <div class="w-full h-full group-hover:scale-[1.02] transition-transform duration-700">
          <?= \App\Core\View::partial('partials/foto-produk', ['foto' => $sorotan->getFotoUrl(), 'kategori' => $sorotan->getNamaKategori(), 'alt' => $sorotan->getNama(), 'ikonKelas' => 'text-[120px]']) ?>
        </div>
        <span class="absolute top-4 left-4 bg-surface-card/90 backdrop-blur-sm px-3 py-1 rounded-full border border-border-warm font-caption text-caption text-ink">Produk Unggulan</span>
        <div class="absolute bottom-4 left-4 right-4 sm:right-auto sm:w-[300px] bg-surface-card/95 backdrop-blur-md rounded-2xl p-space-md border border-border-warm shadow-melayang">
          <span class="eyebrow text-[10.5px]"><?= e($sorotan->getNamaKategori()) ?> · <?= e($sorotan->getRuangan()) ?></span>
          <p class="font-display text-[19px] text-ink leading-snug mt-0.5"><?= e($sorotan->getNama()) ?></p>
          <div class="flex items-center justify-between mt-2">
            <span class="angka font-bold text-ink"><?= rupiah($sorotan->getHarga()) ?></span>
            <span class="font-label-sm text-label-sm text-primary flex items-center gap-1">Detail <span class="material-symbols-outlined text-[16px]">arrow_forward</span></span>
          </div>
        </div>
      </a>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php if ($kategoriPopuler): ?>
<!-- KATEGORI POPULER (ListKategori) -->
<section class="wadah pt-space-3xl">
  <div class="flex items-end justify-between pb-space-lg gap-4 border-b border-border-warm">
    <div>
      <span class="eyebrow">Kategori Populer</span>
      <h2 class="font-display text-headline-md text-ink mt-1">Kategori Ruang &amp; Furnitur</h2>
    </div>
    <a href="<?= e(url('katalog')) ?>" class="font-label-sm text-label-sm text-secondary hover:text-ink flex items-center gap-1 shrink-0">Semua Kategori <span class="material-symbols-outlined text-[16px]">arrow_forward</span></a>
  </div>
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-space-md pt-space-lg">
    <?php foreach ($kategoriPopuler as $k): ?>
      <a href="<?= e(url('katalog', ['kategori' => $k->getIdKategori()])) ?>" class="flex flex-col items-center justify-center gap-space-sm p-space-md rounded-2xl bg-surface-card border border-border-warm hover:border-primary hover:shadow-melayang transition-all group text-center">
        <span class="w-12 h-12 rounded-xl bg-surface-subtle border border-border-warm flex items-center justify-center group-hover:bg-sage transition-colors">
          <span class="material-symbols-outlined text-primary text-[26px]"><?= e(ikonKategori($k->getNamaKategori())) ?></span>
        </span>
        <span class="font-label-sm text-label-sm text-ink leading-tight"><?= e($k->getNamaKategori()) ?></span>
        <span class="font-caption text-[11px] text-secondary normal-case tracking-normal"><?= $k->getJumlahProduk() ?> produk</span>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- PRODUK UNGGULAN (ListUnggulan) -->
<section class="wadah pt-space-3xl">
  <div class="flex flex-col md:flex-row md:items-end justify-between mb-space-lg gap-space-md">
    <div>
      <span class="eyebrow">Pilihan Pelanggan</span>
      <h2 class="font-display text-headline-md text-ink mt-1">Produk Unggulan</h2>
      <p class="font-body-sm text-body-sm text-secondary mt-1">Produk yang paling banyak dipesan pelanggan Toko Abimanyu.</p>
    </div>
    <a href="<?= e(url('katalog')) ?>" class="font-label-sm text-label-sm text-ink border-b border-primary pb-0.5 flex items-center gap-1 w-fit">Lihat Semua Katalog <span class="material-symbols-outlined text-[16px]">arrow_forward</span></a>
  </div>
  <?php if ($produkUnggulan): ?>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-gutter-desktop">
      <?php foreach ($produkUnggulan as $p): ?>
        <?= \App\Core\View::partial('partials/kartu-produk', ['p' => $p]) ?>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="kartu p-space-xl text-center text-secondary">Belum ada produk yang ditampilkan.</p>
  <?php endif; ?>
</section>

<!-- KEUNGGULAN LAYANAN -->
<section class="wadah pt-space-3xl">
  <div class="kartu p-space-lg grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-lg">
    <?php foreach ([
        ['chair', 'Perabot Kayu Solid UMKM', 'Pilihan kursi, meja, lemari, dan rak dengan dimensi serta material yang jelas.'],
        ['inventory_2', 'Stok Terpantau Real-Time', 'Stok diperbarui otomatis saat checkout agar ketersediaan barang selalu akurat.'],
        ['payments', 'Transfer, QRIS, atau COD', 'Pilih metode pembayaran favorit Anda langsung pada formulir pemesanan.'],
        ['chat', 'Hubungi via WhatsApp', 'Tanyakan produk, ongkos kirim, atau konfirmasi pesanan langsung kepada admin.'],
    ] as [$ikon, $judul, $teks]): ?>
      <div class="flex items-start gap-3">
        <span class="w-10 h-10 shrink-0 rounded-full bg-sage text-on-sage flex items-center justify-center"><span class="material-symbols-outlined text-[20px]"><?= $ikon ?></span></span>
        <div>
          <p class="font-display text-[17px] text-ink"><?= $judul ?></p>
          <p class="font-body-sm text-[13px] text-secondary mt-0.5"><?= $teks ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
