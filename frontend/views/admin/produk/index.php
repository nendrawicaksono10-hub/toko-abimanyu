<?php
/**
 * UI-8 Halaman Manajemen Produk (FR-13, FR-42) - 20 baris per halaman.
 * @var \App\Entity\Produk[] $produk
 * @var string $kataKunci
 * @var \App\Core\Paginator $pg
 * @var array $jumlah {total, aktif, nonaktif}
 */
$ambang = (int) config('toko.ambang_stok_menipis', 5);
?>
<?= \App\Core\View::partial('partials/admin-judul', [
    'eyebrow' => 'Katalog Produk', 'judul' => 'Manajemen Produk',
    'deskripsi' => 'Tambah, ubah, hapus, dan nonaktifkan produk beserta foto, dimensi, stok, dan atribut rekomendasinya.',
    'aksi' => \App\Core\View::partial('partials/tab-produk', ['aktif' => 'daftar', 'jumlah' => $jumlah['total']]),
]) ?>

<section class="p-space-md rounded-xl bg-surface-subtle border border-border-warm flex flex-col md:flex-row md:items-center justify-between gap-3">
  <!-- TxtCariProduk -->
  <form method="get" action="<?= e(url('admin/produk')) ?>" class="flex-1 max-w-xl flex gap-2" role="search">
    <div class="relative flex-1">
      <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-secondary">search</span>
      <input name="q" type="search" maxlength="100" value="<?= e($kataKunci) ?>" placeholder="Cari nama produk…" class="input pl-10" aria-label="Cari produk">
    </div>
    <button class="btn-sekunder btn-kecil">Cari</button>
  </form>
  <!-- BtnTambahProduk -->
  <a href="<?= e(url('admin/produk/tambah')) ?>" class="btn-primer btn-kecil w-fit"><span class="material-symbols-outlined text-[18px]">add</span>Tambah Produk</a>
</section>

<!-- TblProduk -->
<section class="kartu overflow-hidden">
  <div class="overflow-x-auto">
    <table class="tabel min-w-[980px]">
      <thead>
        <tr><th>Foto</th><th>Nama Produk</th><th>Kategori &amp; Ruangan</th><th>Material &amp; Dimensi</th><th class="text-right">Harga</th><th class="text-center">Stok</th><th>Status</th><th class="text-right">Aksi</th></tr>
      </thead>
      <tbody>
        <?php foreach ($produk as $p): $s = $p->getStok(); ?>
          <tr class="<?= $p->getAktif() ? '' : 'opacity-60' ?>">
            <td><div class="w-14 h-14 rounded-lg overflow-hidden border border-border-warm bg-surface-subtle"><?= \App\Core\View::partial('partials/foto-produk', ['foto' => $p->getFotoUrl(), 'kategori' => $p->getNamaKategori(), 'alt' => $p->getNama(), 'ikonKelas' => 'text-[24px]']) ?></div></td>
            <td class="max-w-[240px]"><p class="font-label-md text-label-md text-primary"><?= e($p->getNama()) ?></p><p class="font-caption text-caption text-secondary">ID #<?= $p->getIdProduk() ?></p></td>
            <td><p><?= e($p->getNamaKategori()) ?></p><p class="text-secondary text-[12.5px]"><?= e($p->getRuangan()) ?></p></td>
            <td><p><?= e($p->getMaterial()) ?></p><p class="text-secondary text-[12.5px]"><?= e($p->getDimensi()) ?></p></td>
            <td class="text-right font-label-sm text-label-sm whitespace-nowrap"><?= rupiah($p->getHarga()) ?></td>
            <td class="text-center">
              <span class="chip <?= $s === 0 ? 'bg-error-container text-on-error-container border-error-container' : ($s < $ambang ? 'bg-error-container/50 text-on-error-container border-error-container' : 'border-border-warm text-on-surface') ?>">
                <?= $s ?> unit<?= $s === 0 ? ' (Habis)' : ($s < $ambang ? ' (Menipis)' : '') ?>
              </span>
            </td>
            <td><span class="chip <?= $p->getAktif() ? 'bg-surface-muted border-border-warm text-primary' : 'bg-surface-card border-primary text-secondary' ?>"><?= $p->getAktif() ? 'Aktif' : 'Nonaktif' ?></span></td>
            <td>
              <div class="flex items-center justify-end gap-2">
                <a href="<?= e(url('admin/produk/' . $p->getIdProduk() . '/ubah')) ?>" class="btn-sekunder btn-kecil px-3">Ubah</a>
                <!-- BtnNonaktifkan -->
                <form method="post" action="<?= e(url('admin/produk/' . $p->getIdProduk() . '/aktif')) ?>">
                  <?= csrf_field() ?><input type="hidden" name="aktif" value="<?= $p->getAktif() ? '0' : '1' ?>">
                  <button class="btn-sekunder btn-kecil px-3" title="<?= $p->getAktif() ? 'Sembunyikan dari katalog' : 'Tampilkan kembali di katalog' ?>"><?= $p->getAktif() ? 'Nonaktifkan' : 'Aktifkan' ?></button>
                </form>
                <!-- BtnHapusProduk -->
                <form method="post" action="<?= e(url('admin/produk/' . $p->getIdProduk() . '/hapus')) ?>" data-konfirmasi="Hapus produk &quot;<?= e($p->getNama()) ?>&quot;? Produk yang sudah pernah dipesan tidak dapat dihapus dan perlu dinonaktifkan.">
                  <?= csrf_field() ?>
                  <button class="p-2 rounded-full text-secondary hover:text-error hover:bg-error-container" aria-label="Hapus <?= e($p->getNama()) ?>"><span class="material-symbols-outlined text-[20px]">delete</span></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$produk): ?>
          <tr><td colspan="8" class="text-center text-secondary py-space-xl"><?= $kataKunci !== '' ? 'Tidak ada produk yang cocok dengan “' . e($kataKunci) . '”.' : 'Belum ada produk. Klik Tambah Produk untuk memulai.' ?></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="px-space-md py-3 border-t border-border-warm flex flex-col md:flex-row md:items-center justify-between gap-3 bg-surface-subtle">
    <p class="font-body-sm text-body-sm text-secondary">Menampilkan <strong class="text-primary"><?= $pg->dari() ?>–<?= $pg->sampai() ?></strong> dari <strong class="text-primary"><?= $pg->total ?></strong> produk</p>
    <?= \App\Core\View::partial('partials/pagination', ['pg' => $pg, 'path' => 'admin/produk', 'query' => ['q' => $kataKunci]]) ?>
  </div>
</section>
