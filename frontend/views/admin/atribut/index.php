<?php
/**
 * UI-19 Halaman Pengelolaan Atribut Rekomendasi (FR-19).
 * @var array $daftar produk ringkas
 * @var ?\App\Entity\Produk $produk
 * @var \App\Entity\Kategori[] $kategori
 * @var array $atribut, $old, $errors
 */
$bobot = [
    'ruangan'   => 3,
    'kebutuhan' => 3,
    'gaya'      => 2,
    'material'  => 2,
    'ukuran'    => 1,
];
$total = array_sum($bobot);
$v = fn($k, $getter) => array_key_exists($k, $old) ? (string) $old[$k] : ($produk ? (string) $produk->$getter() : '');
$err = fn($k) => isset($errors[$k]) ? '<p class="pesan-galat">' . e($errors[$k]) . '</p>' : '';
$select = function ($name, $label, $opsi, $getter, $b) use ($v, $err, $errors) {
    $val = $v($name, $getter);
    $h = '<div><div class="flex items-center justify-between mb-1.5"><label for="' . $name . '" class="label mb-0">' . e($label) . ' <span class="text-error">*</span></label>'
       . '<span class="chip ' . ($b >= 3 ? 'bg-primary text-on-primary border-primary' : 'bg-surface-subtle border-border-warm text-secondary') . '"><span class="material-symbols-outlined text-[13px]">bolt</span>Bobot +' . $b . '</span></div>'
       . '<select id="' . $name . '" name="' . $name . '" required class="input' . (isset($errors[$name]) ? ' input-galat' : '') . '"><option value="">Pilih</option>';
    foreach ($opsi as $o) $h .= '<option value="' . e($o) . '"' . ($o === $val ? ' selected' : '') . '>' . e($o) . '</option>';
    return $h . '</select>' . $err($name) . '</div>';
};
?>
<?= \App\Core\View::partial('partials/admin-judul', [
    'eyebrow' => 'Mesin Rekomendasi', 'judul' => 'Atribut Rekomendasi Produk',
    'deskripsi' => 'Lengkapi atribut kategori, ruangan, gaya, material, ukuran, dan harga yang dipakai sistem rekomendasi (rule-based scoring).',
]) ?>

<!-- DdlPilihProduk + ringkasan -->
<section class="p-space-lg rounded-xl bg-surface-subtle border border-border-warm grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-end">
  <form method="get" action="<?= e(url('admin/atribut')) ?>" class="lg:col-span-5" data-auto-submit>
    <label for="produk" class="label flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">search</span>Pilih Target Produk Katalog</label>
    <select id="produk" name="produk" class="input py-3">
      <?php foreach ($daftar as $d): ?>
        <option value="<?= (int) $d['idProduk'] ?>" <?= $produk && (int) $d['idProduk'] === $produk->getIdProduk() ? 'selected' : '' ?>>#<?= (int) $d['idProduk'] ?> · <?= e($d['nama']) ?><?= $d['aktif'] ? '' : ' (nonaktif)' ?></option>
      <?php endforeach; ?>
    </select>
    <noscript><button class="btn-sekunder btn-kecil mt-2">Tampilkan</button></noscript>
  </form>
  <?php if ($produk): ?>
  <div class="lg:col-span-7 grid grid-cols-3 gap-3">
    <?php foreach (['Kategori' => $produk->getNamaKategori(), 'Stok' => $produk->getStok() . ' unit', 'Harga' => rupiah($produk->getHarga())] as $l => $n): ?>
      <div class="p-3 rounded-xl bg-surface-card border border-border-warm"><p class="eyebrow text-[11px]"><?= $l ?></p><p class="font-label-md text-label-md truncate"><?= e($n) ?></p></div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<?php if (!$produk): ?>
  <p class="kartu-muted p-space-xl text-center text-secondary">Belum ada produk. Tambahkan produk terlebih dahulu.</p>
<?php else: ?>
<div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start">
  <form method="post" action="<?= e(url('admin/atribut/' . $produk->getIdProduk())) ?>" class="xl:col-span-7 kartu p-space-lg md:p-space-xl flex flex-col gap-space-lg" novalidate>
    <?= csrf_field() ?>
    <div class="flex items-start justify-between gap-3 pb-space-md border-b border-border-warm">
      <div><h2 class="font-title-lg text-title-lg text-primary">Matriks Parameter Rekomendasi</h2><p class="font-body-sm text-body-sm text-secondary"><?= e($produk->getNama()) ?></p></div>
      <span class="chip bg-surface-subtle border-border-warm text-secondary uppercase">Rule-based</span>
    </div>
    <div>
      <div class="flex items-center justify-between mb-1.5"><label for="idKategori" class="label mb-0">Kategori Produk <span class="text-error">*</span></label><span class="chip bg-primary text-on-primary border-primary"><span class="material-symbols-outlined text-[13px]">bolt</span>Kebutuhan +<?= $bobot['kebutuhan'] ?></span></div>
      <select id="idKategori" name="idKategori" required class="input<?= isset($errors['idKategori']) ? ' input-galat' : '' ?>">
        <?php $vk = $v('idKategori', 'getIdKategori'); foreach ($kategori as $k): ?>
          <option value="<?= $k->getIdKategori() ?>" <?= (string) $k->getIdKategori() === $vk ? 'selected' : '' ?>><?= e($k->getNamaKategori()) ?></option>
        <?php endforeach; ?>
      </select>
      <p class="font-caption text-caption text-secondary normal-case tracking-normal mt-1">Nama kategori dan nama produk dicocokkan dengan kata kunci “kebutuhan perabot” pelanggan.</p>
      <?= $err('idKategori') ?>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
      <?= $select('ruangan', 'Target Ruangan', $atribut['ruangan'], 'getRuangan', $bobot['ruangan']) ?>
      <?= $select('gaya', 'Gaya / Estetika Desain', $atribut['gaya'], 'getGaya', $bobot['gaya']) ?>
      <?= $select('material', 'Material Pokok', $atribut['material'], 'getMaterial', $bobot['material']) ?>
      <?= $select('ukuran', 'Kategori Ukuran', $atribut['ukuran'], 'getUkuran', $bobot['ukuran']) ?>
    </div>
    <div>
      <div class="flex items-center justify-between mb-1.5"><label for="harga" class="label mb-0">Harga Satuan (Rp) <span class="text-error">*</span></label><span class="chip bg-surface-subtle border-border-warm text-secondary">Filter budget</span></div>
      <input id="harga" name="harga" type="number" min="0" step="1" required value="<?= e(array_key_exists('harga', $old) ? $old['harga'] : (string) (int) $produk->getHarga()) ?>" class="input<?= isset($errors['harga']) ? ' input-galat' : '' ?>">
      <p class="font-caption text-caption text-secondary normal-case tracking-normal mt-1">Dipakai sebagai penyaring rentang budget pelanggan (bukan komponen skor).</p>
      <?= $err('harga') ?>
    </div>
    <div class="flex flex-wrap gap-3 pt-space-md border-t border-border-warm">
      <!-- BtnSimpanAtribut -->
      <button type="submit" class="btn-primer"><span class="material-symbols-outlined text-[18px]">save</span>Simpan Atribut Rekomendasi</button>
      <!-- BtnResetAtribut: kembalikan isian ke nilai tersimpan tanpa menyimpan -->
      <button type="reset" class="btn-sekunder"><span class="material-symbols-outlined text-[18px]">restart_alt</span>Reset ke Nilai Tersimpan</button>
    </div>
  </form>

  <!-- PnlBobot -->
  <aside class="xl:col-span-5 kartu-muted p-space-lg flex flex-col gap-space-md">
    <div class="flex items-start gap-3">
      <span class="w-9 h-9 rounded-full bg-primary text-on-primary flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-[18px]">calculate</span></span>
      <div><h2 class="font-title-md text-title-md text-primary">Aturan Pembobotan Skor</h2><p class="font-body-sm text-[12.5px] text-secondary">Algoritma #1 — rule-based scoring</p></div>
    </div>
    <ul class="flex flex-col gap-2">
      <?php foreach (['ruangan' => 'Kesesuaian Ruangan', 'kebutuhan' => 'Kesesuaian Kebutuhan (nama/kategori)', 'gaya' => 'Kesesuaian Gaya', 'material' => 'Kesesuaian Material', 'ukuran' => 'Kesesuaian Ukuran'] as $k => $l): ?>
        <li class="flex items-center justify-between px-4 py-3 rounded-xl bg-surface-card border border-border-warm font-body-sm text-body-sm"><span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-primary"></span><?= $l ?></span><span class="font-label-md text-label-md">+<?= $bobot[$k] ?> poin</span></li>
      <?php endforeach; ?>
    </ul>
    <div class="px-4 py-3 rounded-xl bg-surface-card border border-primary flex items-center justify-between"><span class="eyebrow text-primary">Total skor maksimum</span><span class="font-title-lg text-title-lg"><?= $total ?> poin</span></div>
    <p class="font-body-sm text-[12.5px] text-secondary flex gap-2"><span class="material-symbols-outlined text-[18px]">filter_alt</span><span><strong class="text-primary">Filter anggaran:</strong> produk di luar rentang budget atau berstok 0 tidak dinilai. Persentase kecocokan = skor ÷ total bobot kriteria yang diisi pelanggan × 100. Kriteria sama dibandingkan tanpa membedakan huruf besar/kecil.</span></p>
  </aside>
</div>
<?php endif; ?>
