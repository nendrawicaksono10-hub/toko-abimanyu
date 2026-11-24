<?php
/**
 * UI-6 Halaman Manajemen Stok (FR-15, FR-24, FR-29).
 * @var \App\Entity\Produk[] $daftar
 * @var \App\Entity\Produk[] $menipis
 * @var string $q, $filter
 * @var int $ambang
 * @var \App\Core\Paginator $pg
 */
$totalHabis = count(array_filter($menipis, fn($p) => $p->getStok() === 0));
$tab = ['' => 'Semua', 'menipis' => 'Stok Menipis', 'habis' => 'Habis'];
?>
<?= \App\Core\View::partial('partials/admin-judul', [
    'eyebrow' => 'Inventaris & Taksonomi', 'judul' => 'Manajemen Stok',
    'deskripsi' => 'Perbarui jumlah stok setiap produk. Produk dengan stok di bawah ambang batas ditandai Stok Menipis.',
    'aksi' => \App\Core\View::partial('partials/tab-inventaris', ['aktif' => 'stok']),
]) ?>

<section class="grid grid-cols-1 md:grid-cols-3 gap-4">
  <div class="p-5 rounded-xl border border-border-warm flex items-center gap-4"><span class="w-10 h-10 rounded-full bg-surface-subtle flex items-center justify-center"><span class="material-symbols-outlined">tune</span></span><div><p class="eyebrow">Ambang Batas</p><p class="font-title-lg text-title-lg">&lt; <?= $ambang ?> unit</p><p class="font-caption text-caption text-secondary normal-case tracking-normal">Diatur pada berkas konfigurasi</p></div></div>
  <a href="<?= e(url('admin/stok', ['status' => 'menipis'])) ?>" class="p-5 rounded-xl border border-border-warm hover:border-primary flex items-center gap-4"><span class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center"><span class="material-symbols-outlined">warning</span></span><div><p class="eyebrow">Stok Menipis</p><p class="font-title-lg text-title-lg"><?= count($menipis) - $totalHabis ?> produk</p></div></a>
  <a href="<?= e(url('admin/stok', ['status' => 'habis'])) ?>" class="p-5 rounded-xl border border-border-warm hover:border-primary flex items-center gap-4"><span class="w-10 h-10 rounded-full bg-error-container text-on-error-container flex items-center justify-center"><span class="material-symbols-outlined">block</span></span><div><p class="eyebrow">Stok Habis</p><p class="font-title-lg text-title-lg"><?= $totalHabis ?> produk</p></div></a>
</section>

<section class="kartu overflow-hidden">
  <div class="p-space-md border-b border-border-warm flex flex-col lg:flex-row lg:items-center justify-between gap-3 bg-surface-subtle">
    <div class="flex gap-2 flex-wrap">
      <?php foreach ($tab as $val => $lab): ?>
        <a href="<?= e(url('admin/stok', ['status' => $val, 'q' => $q])) ?>" class="chip px-4 py-1.5 font-label-sm text-label-sm <?= $filter === $val ? 'bg-primary text-on-primary border-primary' : 'bg-surface-card border-border-warm hover:border-primary' ?>"><?= $lab ?></a>
      <?php endforeach; ?>
    </div>
    <form method="get" action="<?= e(url('admin/stok')) ?>" class="flex gap-2" role="search">
      <?php if ($filter): ?><input type="hidden" name="status" value="<?= e($filter) ?>"><?php endif; ?>
      <input name="q" type="search" value="<?= e($q) ?>" maxlength="100" placeholder="Cari nama produk…" class="input py-2 w-56" aria-label="Cari produk">
      <button class="btn-sekunder btn-kecil">Cari</button>
    </form>
  </div>
  <!-- TblStok -->
  <div class="overflow-x-auto">
    <table class="tabel min-w-[760px]">
      <thead><tr><th>Produk</th><th>Kategori</th><th class="text-center">Stok Saat Ini</th><th>Status Stok</th><th>Perbarui Stok</th></tr></thead>
      <tbody>
        <?php foreach ($daftar as $p): $s = $p->getStok(); $st = statusStok($s); ?>
          <tr class="<?= $st !== 'Aman' ? 'bg-error-container/20' : '' ?>">
            <td>
              <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-lg bg-surface-subtle border border-border-warm flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-[18px]"><?= e(ikonKategori($p->getNamaKategori())) ?></span></span>
                <div><p class="font-label-md text-label-md text-primary"><?= e($p->getNama()) ?></p><p class="font-caption text-caption text-secondary">ID #<?= $p->getIdProduk() ?><?= $p->getAktif() ? '' : ' · nonaktif' ?></p></div>
              </div>
            </td>
            <td><?= e($p->getNamaKategori()) ?></td>
            <td class="text-center font-title-md text-title-md"><?= $s ?></td>
            <td>
              <!-- BadgeStokMenipis -->
              <span class="chip <?= $st === 'Aman' ? 'border-border-warm text-on-surface' : 'bg-error-container text-on-error-container border-error-container' ?>"><span class="w-1.5 h-1.5 rounded-full bg-current"></span><?= $st ?></span>
            </td>
            <td>
              <!-- TxtJumlahStok + BtnSimpanStok -->
              <form method="post" action="<?= e(url('admin/stok/' . $p->getIdProduk())) ?>" class="flex items-center gap-2">
                <?= csrf_field() ?>
                <div class="flex items-center rounded-full border border-border-warm bg-surface-card" data-stepper>
                  <button type="button" class="w-8 h-9 flex items-center justify-center text-secondary hover:text-primary" data-step="-1" data-min="0" aria-label="Kurangi"><span class="material-symbols-outlined text-[16px]">remove</span></button>
                  <input name="stok" type="number" min="0" step="1" inputmode="numeric" value="<?= $s ?>" class="w-16 text-center bg-transparent font-label-sm text-label-sm focus:outline-none" aria-label="Jumlah stok <?= e($p->getNama()) ?>">
                  <button type="button" class="w-8 h-9 flex items-center justify-center text-secondary hover:text-primary" data-step="1" aria-label="Tambah"><span class="material-symbols-outlined text-[16px]">add</span></button>
                </div>
                <button class="btn-primer btn-kecil px-4"><span class="material-symbols-outlined text-[16px]">done</span>Simpan</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$daftar): ?><tr><td colspan="5" class="text-center text-secondary py-space-xl">Tidak ada produk pada daftar ini.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pg->totalHalaman > 1): ?>
    <div class="px-space-md py-3 border-t border-border-warm bg-surface-subtle"><?= \App\Core\View::partial('partials/pagination', ['pg' => $pg, 'path' => 'admin/stok', 'query' => ['q' => $q, 'status' => $filter]]) ?></div>
  <?php endif; ?>
</section>
