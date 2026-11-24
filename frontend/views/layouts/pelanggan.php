<?php
/**
 * Layout halaman pelanggan (header NavUtama + footer) - desain Earthy Warm Editorial.
 * Variabel: $title, $content
 */
$menu = [
    ['/', 'Beranda', true],
    ['/katalog', 'Katalog', false],
];
$aktif = fn($m) => $m[2] ? isActive($m[0], true) : (isActive($m[0]) || ($m[0] === '/katalog' && isActive('/produk')));
$jml = jumlahKeranjang();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?= \App\Core\View::partial('partials/head', ['title' => $title ?? '']) ?>
</head>
<body class="bg-surface font-body-md text-on-surface antialiased min-h-screen flex flex-col">
<a href="#konten" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-[60] btn-primer">Lewati ke konten</a>

<header class="fixed top-0 inset-x-0 z-50 bg-surface/90 backdrop-blur-md border-b border-border-warm">
  <div class="wadah h-16 lg:h-20 flex items-center justify-between gap-space-md">
    <a href="<?= e(url('/')) ?>" class="flex items-center gap-2.5 shrink-0" aria-label="Toko Abimanyu - Beranda">
      <span class="w-9 h-9 rounded-full bg-primary text-on-primary flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">chair</span></span>
      <span class="flex flex-col leading-none">
        <span class="font-display text-[21px] lg:text-[23px] text-ink tracking-tight">Toko Abimanyu</span>
        <span class="hidden sm:block font-caption text-[10px] text-secondary uppercase tracking-[0.18em] mt-1">Perabot Rumah UMKM</span>
      </span>
    </a>

    <nav class="hidden md:flex items-center gap-1 p-1.5 rounded-full bg-surface-subtle/80 border border-border-warm" aria-label="Navigasi utama">
      <?php foreach ($menu as $m): $on = $aktif($m); ?>
        <a href="<?= e(url($m[0])) ?>" class="px-4 py-2 rounded-full font-label-md text-label-md transition-all <?= $on ? 'bg-primary-hover text-on-primary shadow-sm' : 'text-on-surface-variant hover:text-ink' ?>" <?= $on ? 'aria-current="page"' : '' ?>><?= e($m[1]) ?></a>
      <?php endforeach; ?>
    </nav>

    <div class="flex items-center gap-1 sm:gap-2">
      <form action="<?= e(url('katalog')) ?>" method="get" role="search" class="relative hidden lg:flex items-center">
        <span class="material-symbols-outlined absolute left-3 text-[20px] text-outline pointer-events-none">search</span>
        <label for="cari-header" class="sr-only">Cari produk</label>
        <input id="cari-header" name="q" type="search" maxlength="100" placeholder="Cari perabot kayu, kursi..." value="<?= e($_GET['q'] ?? '') ?>" class="w-48 xl:w-60 pl-10 pr-4 py-2 bg-surface-card border border-border-warm rounded-full font-body-sm text-body-sm placeholder:text-secondary focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
      </form>
      <a href="<?= e(url('katalog')) ?>#cari" class="lg:hidden w-10 h-10 rounded-full flex items-center justify-center text-on-surface-variant hover:bg-surface-subtle" aria-label="Cari produk"><span class="material-symbols-outlined text-[22px]">search</span></a>
      <a href="<?= e(url('keranjang')) ?>" class="relative w-10 h-10 rounded-full flex items-center justify-center text-on-surface-variant hover:text-ink hover:bg-surface-subtle transition-colors" aria-label="Keranjang belanja (<?= $jml ?> item)">
        <span class="material-symbols-outlined text-[23px]">shopping_bag</span>
        <span data-keranjang-count class="absolute top-1 right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-primary text-on-primary font-caption text-[10px] flex items-center justify-center leading-none shadow-sm <?= $jml ? '' : 'hidden' ?>"><?= $jml ?></span>
      </a>
      <button type="button" class="md:hidden w-10 h-10 rounded-full flex items-center justify-center hover:bg-surface-subtle" data-toggle="#menu-mobile" aria-label="Buka menu" aria-expanded="false">
        <span class="material-symbols-outlined text-[24px]">menu</span>
      </button>
    </div>
  </div>
  <nav id="menu-mobile" class="md:hidden hidden border-t border-border-warm bg-surface" aria-label="Navigasi utama (seluler)">
    <div class="wadah py-3 flex flex-col gap-1">
      <?php foreach ($menu as $m): $on = $aktif($m); ?>
        <a href="<?= e(url($m[0])) ?>" class="px-4 py-2.5 rounded-full font-label-md text-label-md <?= $on ? 'bg-primary-hover text-on-primary' : 'text-on-surface-variant' ?>"><?= e($m[1]) ?></a>
      <?php endforeach; ?>
    </div>
  </nav>
</header>

<main id="konten" class="flex-1 w-full pt-16 lg:pt-20">
  <?= \App\Core\View::partial('partials/flash', ['flashClass' => 'wadah pt-space-md']) ?>
  <?= $content ?>
</main>

<footer class="mt-space-3xl bg-surface-subtle border-t border-border-warm">
  <div class="wadah py-space-2xl grid grid-cols-1 md:grid-cols-12 gap-space-xl">
    <div class="md:col-span-4 flex flex-col gap-3">
      <span class="flex items-center gap-2.5">
        <span class="w-9 h-9 rounded-full bg-primary text-on-primary flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">chair</span></span>
        <span class="font-display text-[22px] text-ink">Toko Abimanyu</span>
      </span>
      <p class="font-body-sm text-body-sm text-secondary max-w-sm">Perabot rumah kayu solid karya pengrajin UMKM. Pesan langsung dari katalog, bayar via transfer bank, QRIS, atau di tempat (COD).</p>
    </div>
    <div class="md:col-span-2 flex flex-col gap-2.5">
      <span class="font-display text-[17px] text-ink">Belanja</span>
      <a href="<?= e(url('/')) ?>" class="font-body-sm text-body-sm text-secondary hover:text-ink">Beranda</a>
      <a href="<?= e(url('katalog')) ?>" class="font-body-sm text-body-sm text-secondary hover:text-ink">Katalog Produk</a>
      <a href="<?= e(url('keranjang')) ?>" class="font-body-sm text-body-sm text-secondary hover:text-ink">Keranjang Belanja</a>
    </div>
    <div class="md:col-span-2 flex flex-col gap-2.5">
      <span class="font-display text-[17px] text-ink">Informasi</span>
      <a href="<?= e(url('checkout')) ?>" class="font-body-sm text-body-sm text-secondary hover:text-ink">Checkout</a>
      <a href="<?= e(url('kebijakan-privasi')) ?>" class="font-body-sm text-body-sm text-secondary hover:text-ink">Kebijakan Privasi</a>
    </div>
    <div class="md:col-span-4 flex flex-col gap-3">
      <span class="font-display text-[17px] text-ink">Bantuan Pelanggan</span>
      <p class="font-body-sm text-body-sm text-secondary">Tanyakan produk, ongkos kirim, atau pesanan langsung kepada admin.</p>
      <a href="<?= e(waLink('Halo Toko Abimanyu, saya ingin bertanya tentang produk.')) ?>" target="_blank" rel="noopener" class="btn-primer btn-kecil w-full sm:w-fit"><span class="material-symbols-outlined text-[18px]">chat</span>Hubungi via WhatsApp</a>
      <span class="eyebrow mt-2">Metode pembayaran</span>
      <div class="flex flex-wrap gap-2">
        <?php foreach (['Transfer Bank', 'QRIS', 'COD'] as $m): ?>
          <span class="px-2.5 py-1 rounded-lg border border-border-warm bg-surface-card font-caption text-[11px] text-on-surface-variant"><?= $m ?></span>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="wadah pb-space-xl">
    <div class="pt-space-lg border-t border-border-warm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
      <p class="font-caption text-caption text-secondary normal-case tracking-normal">© <?= date('Y') ?> Toko Abimanyu · Produk UMKM Indonesia</p>
      <a href="<?= e(url('kebijakan-privasi')) ?>" class="font-caption text-caption text-secondary normal-case tracking-normal hover:text-ink">Kebijakan Privasi</a>
    </div>
  </div>
</footer>
<div id="toast" class="fixed bottom-5 left-1/2 -translate-x-1/2 z-[70] hidden"></div>
</body>
</html>
