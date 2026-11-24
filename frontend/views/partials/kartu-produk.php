<?php
/** Kartu produk katalog (ListProduk / ListUnggulan). Variabel: $p (Produk) */
$ambang = (int) config('toko.ambang_stok_menipis', 5);
$stok = $p->getStok();
[$label, $gayaStok] = $stok <= 0
    ? ['Habis', 'bg-error-container text-on-error-container']
    : ($stok < $ambang ? ["Stok menipis: {$stok}", 'bg-warning-container text-warning'] : ["Tersedia: {$stok}", 'bg-sage text-success']);
$detail = url('produk/' . $p->getIdProduk());
?>
<article class="group flex flex-col bg-surface-card border border-border-warm rounded-3xl p-3 sm:p-4 hover:shadow-melayang hover:-translate-y-1 transition-all duration-300">
  <a href="<?= e($detail) ?>" class="relative block w-full aspect-[4/3] rounded-xl overflow-hidden bg-surface-subtle mb-3">
    <div class="w-full h-full group-hover:scale-105 transition-transform duration-500">
      <?= \App\Core\View::partial('partials/foto-produk', ['foto' => $p->getFotoUrl(), 'kategori' => $p->getNamaKategori(), 'alt' => $p->getNama()]) ?>
    </div>
    <span class="absolute top-2.5 left-2.5 backdrop-blur-md bg-surface-card/90 text-ink font-caption text-[11px] px-2.5 py-1 rounded-full border border-border-warm shadow-sm"><?= e($p->getMaterial()) ?></span>
  </a>
  <div class="flex flex-col flex-1">
    <span class="font-caption text-[11px] uppercase tracking-wider text-secondary"><?= e($p->getNamaKategori()) ?></span>
    <h3 class="font-display text-[18px] leading-snug text-ink mt-0.5 line-clamp-2">
      <a href="<?= e($detail) ?>" class="hover:text-primary-hover"><?= e($p->getNama()) ?></a>
    </h3>
    <p class="font-body-sm text-[12.5px] text-secondary mt-1"><?= e($p->getRuangan()) ?> • <?= e($p->getDimensi()) ?></p>
    <div class="mt-auto pt-3">
      <div class="pt-3 border-t border-border-warm flex flex-wrap items-end justify-between gap-2">
        <div>
          <span class="block font-caption text-[10px] text-secondary uppercase">Harga</span>
          <span class="angka font-sans text-[16px] sm:text-[18px] font-bold text-ink"><?= rupiah($p->getHarga()) ?></span>
        </div>
        <span class="inline-flex items-center px-2 py-0.5 rounded-md font-caption text-[11px] <?= $gayaStok ?>"><?= e($label) ?></span>
      </div>
      <div class="flex gap-2 mt-3">
        <a href="<?= e($detail) ?>" class="flex-1 inline-flex items-center justify-center py-2 px-3 rounded-full border border-border-warm bg-surface-card hover:bg-surface-subtle font-label-sm text-label-sm text-ink transition-colors">Lihat Detail</a>
        <?php if ($stok > 0): ?>
          <form method="post" action="<?= e(url('keranjang/tambah')) ?>" data-tambah-keranjang>
            <?= csrf_field() ?><input type="hidden" name="idProduk" value="<?= $p->getIdProduk() ?>"><input type="hidden" name="jumlah" value="1"><input type="hidden" name="kembali" value="1">
            <button type="submit" class="w-9 h-9 rounded-full bg-primary text-on-primary flex items-center justify-center hover:bg-primary-hover transition-colors shadow-sm" title="Tambah ke keranjang" aria-label="Tambah <?= e($p->getNama()) ?> ke keranjang"><span class="material-symbols-outlined text-[18px]">add_shopping_cart</span></button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</article>
