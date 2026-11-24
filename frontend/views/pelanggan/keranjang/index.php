<?php
/**
 * UI-1 Halaman Keranjang Belanja (FR-06).
 * @var array $isi {items, total, jumlahUnit, adaMasalah}
 */
$items = $isi['items'];
?>
<div class="wadah">
  <?= \App\Core\View::partial('partials/langkah-checkout', ['langkah' => 1, 'label' => 'Keranjang Belanja']) ?>

  <div class="pb-space-xl border-b border-border-warm mb-space-xl">
    <span class="eyebrow">Langkah 1 dari 2</span>
    <h1 class="font-display text-headline-lg-mobile md:text-headline-lg text-ink">Keranjang Belanja</h1>
    <p class="font-body-md text-body-md text-secondary mt-1">Tinjau perabot pilihan Anda sebelum melanjutkan ke data pemesanan.</p>
  </div>

  <?php if (!$items): ?>
    <div class="kartu-muted p-space-2xl text-center flex flex-col items-center gap-3">
      <span class="material-symbols-outlined text-[48px] text-outline">shopping_bag</span>
      <h2 class="font-headline-sm text-headline-sm text-ink">Keranjang Anda masih kosong</h2>
      <p class="font-body-md text-body-md text-secondary">Tambahkan produk dari katalog untuk mulai berbelanja.</p>
      <a href="<?= e(url('katalog')) ?>" class="btn-primer mt-2">Lihat Katalog</a>
    </div>
  <?php else: ?>
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-start" data-keranjang>
    <!-- TblKeranjang -->
    <section class="lg:col-span-8 flex flex-col gap-space-md">
      <div class="kartu p-space-md sm:p-space-lg flex flex-col gap-space-md">
      <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 rounded-xl bg-surface-subtle flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">shopping_bag</span></span>
          <div><h2 class="font-display text-headline-sm text-ink">Daftar Perabot Dipilih</h2><p class="font-body-sm text-body-sm text-secondary">Harga & stok diperiksa ulang dari data terkini.</p></div>
        </div>
        <span class="chip bg-surface-subtle border-border-warm text-on-surface-variant normal-case tracking-normal" data-jumlah-produk><?= count($items) ?> produk</span>
      </div>
      <div class="flex flex-col gap-3">
        <?php foreach ($items as $it): $p = $it['produk']; $id = $it['idProduk']; ?>
          <div class="p-space-md rounded-2xl bg-surface-subtle border border-border-warm flex gap-space-md" data-item="<?= $id ?>">
            <div class="w-20 h-20 sm:w-24 sm:h-24 shrink-0 rounded-xl overflow-hidden border border-border-warm bg-surface-subtle">
              <?= \App\Core\View::partial('partials/foto-produk', ['foto' => $p?->getFotoUrl(), 'kategori' => $p?->getNamaKategori(), 'alt' => $p?->getNama(), 'ikonKelas' => 'text-[36px]']) ?>
            </div>
            <div class="flex-1 min-w-0 flex flex-col sm:flex-row sm:items-start justify-between gap-3">
              <div class="min-w-0">
                <?php if ($p): ?>
                  <span class="eyebrow text-[11px]"><?= e($p->getRuangan()) ?></span>
                  <h3 class="font-title-md text-[16px] text-ink leading-snug"><a href="<?= e(url('produk/' . $id)) ?>" class="hover:underline underline-offset-4"><?= e($p->getNama()) ?></a></h3>
                  <p class="font-body-sm text-[13px] text-secondary">Dimensi: <?= e($p->getDimensi()) ?></p>
                  <p class="font-label-sm text-label-sm text-ink mt-1"><?= rupiah($p->getHarga()) ?> / unit</p>
                <?php else: ?>
                  <h3 class="font-title-md text-[16px] text-secondary">Produk tidak tersedia</h3>
                <?php endif; ?>
                <p class="pesan-galat <?= $it['masalah'] ? '' : 'hidden' ?>" data-masalah><?= e($it['masalah']) ?></p>
              </div>
              <div class="flex sm:flex-col items-center sm:items-end justify-between gap-2 shrink-0">
                <p class="font-label-md text-label-md text-ink" data-subtotal><?= rupiah($it['subtotal']) ?></p>
                <div class="flex items-center gap-3">
                  <?php if ($p && $p->getAktif()): ?>
                  <!-- BtnUbahJumlah -->
                  <form method="post" action="<?= e(url('keranjang/ubah')) ?>" data-ubah-jumlah class="flex items-center rounded-full border border-border-warm" data-stepper>
                    <?= csrf_field() ?>
                    <input type="hidden" name="idProduk" value="<?= $id ?>">
                    <button type="button" class="w-8 h-8 flex items-center justify-center text-secondary hover:text-ink" data-step="-1" aria-label="Kurangi"><span class="material-symbols-outlined text-[16px]">remove</span></button>
                    <input name="jumlah" type="number" inputmode="numeric" min="1" max="<?= max(1, $p->getStok()) ?>" value="<?= $it['jumlah'] ?>" aria-label="Jumlah" class="w-10 text-center bg-transparent font-label-sm text-label-sm focus:outline-none">
                    <button type="button" class="w-8 h-8 flex items-center justify-center text-secondary hover:text-ink" data-step="1" aria-label="Tambah"><span class="material-symbols-outlined text-[16px]">add</span></button>
                    <noscript><button class="px-2 font-caption text-caption underline">Perbarui</button></noscript>
                  </form>
                  <?php else: ?>
                    <span class="font-label-sm text-label-sm text-secondary"><?= $it['jumlah'] ?> unit</span>
                  <?php endif; ?>
                  <!-- BtnHapusItem -->
                  <form method="post" action="<?= e(url('keranjang/hapus')) ?>" data-hapus-item>
                    <?= csrf_field() ?>
                    <input type="hidden" name="idProduk" value="<?= $id ?>">
                    <button type="submit" class="font-label-sm text-label-sm text-secondary hover:text-error underline underline-offset-4">Hapus</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      </div>
      <a href="<?= e(url('katalog')) ?>" class="font-label-md text-label-md text-ink flex items-center gap-1 w-fit"><span class="material-symbols-outlined text-[18px]">arrow_back</span> Lanjut Belanja</a>
    </section>

    <!-- Ringkasan -->
    <aside class="lg:col-span-4 kartu p-space-lg shadow-sm flex flex-col gap-space-md lg:sticky lg:top-28">
      <h2 class="font-display text-headline-sm text-ink">Ringkasan Belanja</h2>
      <div class="flex justify-between font-body-sm text-body-sm">
        <span class="text-secondary">Jumlah barang</span><span><span data-total-unit><?= $isi['jumlahUnit'] ?></span> unit</span>
      </div>
      <div class="pt-space-md border-t border-border-warm flex items-end justify-between gap-3">
        <span class="font-body-sm text-body-sm text-secondary">Total Belanja</span>
        <!-- LblTotal -->
        <span class="angka text-[24px] leading-tight font-bold text-ink" data-total><?= rupiah($isi['total']) ?></span>
      </div>
      <p class="font-caption text-caption text-secondary normal-case tracking-normal">Total belum termasuk ongkos kirim. Ongkos kirim dan jadwal pengiriman dikonfirmasi admin melalui WhatsApp.</p>
      <p class="pesan-galat <?= $isi['adaMasalah'] ? '' : 'hidden' ?>" data-peringatan-checkout>Perbarui item yang bermasalah sebelum checkout.</p>
      <!-- BtnCheckout -->
      <a href="<?= e(url('checkout')) ?>" class="btn-primer w-full <?= $isi['adaMasalah'] ? 'opacity-40 pointer-events-none' : '' ?>" data-btn-checkout <?= $isi['adaMasalah'] ? 'aria-disabled="true"' : '' ?>>
        Checkout <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
      </a>
    </aside>
  </div>
  <?php endif; ?>
</div>
