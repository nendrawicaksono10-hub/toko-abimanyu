<?php
/**
 * UI-10 Halaman Manajemen Kategori (FR-14).
 * @var \App\Entity\Kategori[] $kategori
 * @var ?\App\Entity\Kategori $edit
 * @var array $old, $errors
 */
$nama = $old['namaKategori'] ?? ($edit?->getNamaKategori() ?? '');
$terpakai = count(array_filter($kategori, fn($k) => $k->getJumlahProduk() > 0));
?>
<?= \App\Core\View::partial('partials/admin-judul', [
    'eyebrow' => 'Inventaris & Taksonomi', 'judul' => 'Manajemen Kategori',
    'deskripsi' => 'Atur kategori produk yang tampil sebagai filter katalog dan pilihan pada form produk.',
    'aksi' => \App\Core\View::partial('partials/tab-inventaris', ['aktif' => 'kategori']),
]) ?>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start">
  <!-- TblKategori -->
  <section class="xl:col-span-8 kartu overflow-hidden">
    <div class="p-space-lg border-b border-border-warm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div><h2 class="font-headline-sm text-headline-sm text-primary">Daftar Kategori</h2><p class="font-body-sm text-body-sm text-secondary"><?= count($kategori) ?> kategori · <?= $terpakai ?> dipakai produk</p></div>
      <span class="chip bg-surface-subtle border-border-warm text-secondary"><span class="material-symbols-outlined text-[14px]">shield</span>Hapus kategori terpakai: produk dipindah dulu ke kategori lain</span>
    </div>
    <div class="overflow-x-auto">
      <table class="tabel min-w-[520px]">
        <thead><tr><th class="w-12">No</th><th>Nama Kategori</th><th>Jumlah Produk</th><th class="text-right">Aksi</th></tr></thead>
        <tbody>
          <?php foreach ($kategori as $i => $k): ?>
            <tr class="<?= $edit && $edit->getIdKategori() === $k->getIdKategori() ? 'bg-surface-subtle' : '' ?>">
              <td class="text-secondary"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></td>
              <td class="font-label-md text-label-md text-primary"><span class="inline-flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-secondary"><?= e(ikonKategori($k->getNamaKategori())) ?></span><?= e($k->getNamaKategori()) ?></span></td>
              <td><span class="chip border-border-warm text-on-surface"><?= $k->getJumlahProduk() ?> produk</span></td>
              <td>
                <div class="flex items-center justify-end gap-2">
                  <!-- BtnUbahKategori -->
                  <a href="<?= e(url('admin/kategori', ['ubah' => $k->getIdKategori()])) ?>" class="btn-sekunder btn-kecil px-3"><span class="material-symbols-outlined text-[16px]">edit</span>Ubah</a>
                  <!-- BtnHapusKategori -->
                  <?php if ($k->getJumlahProduk() === 0): ?>
                  <form method="post" action="<?= e(url('admin/kategori/' . $k->getIdKategori() . '/hapus')) ?>" data-konfirmasi="Hapus kategori &quot;<?= e($k->getNamaKategori()) ?>&quot;?">
                    <?= csrf_field() ?>
                    <button class="p-2 rounded-full text-secondary hover:text-error hover:bg-error-container" aria-label="Hapus <?= e($k->getNamaKategori()) ?>"><span class="material-symbols-outlined text-[20px]">delete</span></button>
                  </form>
                  <?php else: ?>
                  <!-- Kategori masih dipakai: pindahkan produknya ke kategori lain, lalu hapus -->
                  <details class="relative">
                    <summary class="list-none cursor-pointer p-2 rounded-full text-secondary hover:text-error hover:bg-error-container" aria-label="Hapus <?= e($k->getNamaKategori()) ?>" title="Pindahkan <?= $k->getJumlahProduk() ?> produk lalu hapus"><span class="material-symbols-outlined text-[20px]">delete</span></summary>
                    <form method="post" action="<?= e(url('admin/kategori/' . $k->getIdKategori() . '/hapus')) ?>" class="absolute right-0 top-full mt-2 z-20 w-72 kartu shadow-modal p-space-md flex flex-col gap-3 text-left" data-konfirmasi="Pindahkan <?= $k->getJumlahProduk() ?> produk lalu hapus kategori &quot;<?= e($k->getNamaKategori()) ?>&quot;?">
                      <?= csrf_field() ?>
                      <p class="font-body-sm text-body-sm text-secondary">"<?= e($k->getNamaKategori()) ?>" masih dipakai <?= $k->getJumlahProduk() ?> produk. Pindahkan produk tersebut ke:</p>
                      <select name="pindahKe" required class="input py-2">
                        <option value="">Pilih kategori tujuan</option>
                        <?php foreach ($kategori as $lain): if ($lain->getIdKategori() === $k->getIdKategori()) continue; ?>
                          <option value="<?= $lain->getIdKategori() ?>"><?= e($lain->getNamaKategori()) ?></option>
                        <?php endforeach; ?>
                      </select>
                      <button class="btn-bahaya btn-kecil w-full"><span class="material-symbols-outlined text-[16px]">delete</span>Pindahkan &amp; Hapus</button>
                    </form>
                  </details>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$kategori): ?><tr><td colspan="4" class="text-center text-secondary py-space-xl">Belum ada kategori.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- Form tambah/ubah -->
  <form method="post" action="<?= e(url('admin/kategori')) ?>" class="xl:col-span-4 kartu-muted p-space-lg flex flex-col gap-space-md" novalidate>
    <?= csrf_field() ?>
    <?php if ($edit): ?><input type="hidden" name="idKategori" value="<?= $edit->getIdKategori() ?>"><?php endif; ?>
    <div class="flex items-center justify-between">
      <h2 class="font-title-md text-title-md text-primary"><?= $edit ? 'Ubah Kategori' : 'Tambah Kategori Baru' ?></h2>
      <span class="chip bg-surface-card border-border-warm text-secondary"><?= $edit ? 'Mode Ubah' : 'Mode Tambah' ?></span>
    </div>
    <div>
      <label for="namaKategori" class="label">Nama Kategori <span class="text-error">*</span></label>
      <input id="namaKategori" name="namaKategori" type="text" maxlength="100" required value="<?= e($nama) ?>" class="input<?= isset($errors['namaKategori']) ? ' input-galat' : '' ?>" placeholder="mis. Meja, Kursi, Lemari, Rak" <?= $edit ? 'autofocus' : '' ?>>
      <?= isset($errors['namaKategori']) ? '<p class="pesan-galat">' . e($errors['namaKategori']) . '</p>' : '<p class="font-caption text-caption text-secondary normal-case tracking-normal mt-1">Tidak boleh sama dengan kategori lain.</p>' ?>
    </div>
    <div class="flex gap-3">
      <!-- BtnSimpanKategori -->
      <button type="submit" class="btn-primer flex-1"><span class="material-symbols-outlined text-[18px]">save</span>Simpan</button>
      <?php if ($edit): ?><a href="<?= e(url('admin/kategori')) ?>" class="btn-sekunder">Batal</a><?php endif; ?>
    </div>
  </form>
</div>
