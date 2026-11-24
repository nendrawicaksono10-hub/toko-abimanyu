<?php
/**
 * UI-9 Form Data Produk (PBI-10, PBI-11, PBI-20 / FR-13, FR-19, FR-28).
 * @var ?\App\Entity\Produk $produk null = mode tambah
 * @var \App\Entity\Kategori[] $kategori
 * @var array $atribut, $old, $errors
 */
$ubah = $produk !== null;
$angka = fn($n) => rtrim(rtrim(number_format((float) $n, 2, '.', ''), '0'), '.');
$nilai = function (string $k, string $getter) use ($old, $produk, $angka) {
    if (array_key_exists($k, $old)) return (string) $old[$k];
    if (!$produk) return '';
    $v = $produk->$getter();
    return is_float($v) ? $angka($v) : (string) $v;
};
$err = fn($k) => isset($errors[$k]) ? '<p class="pesan-galat">' . e($errors[$k]) . '</p>' : '';
$cls = fn($k) => isset($errors[$k]) ? ' input-galat' : '';
$select = function (string $name, string $label, array $opsi, string $getter, string $bobot) use ($nilai, $err, $cls) {
    $v = $nilai($name, $getter);
    $h = '<div><label for="' . $name . '" class="label flex items-center gap-1">' . e($label) . ' <span class="text-error">*</span><span class="ml-auto font-caption text-[11px] text-secondary">' . $bobot . '</span></label>';
    $h .= '<select id="' . $name . '" name="' . $name . '" class="input' . $cls($name) . '" required><option value="">Pilih ' . e(mb_strtolower($label)) . '</option>';
    foreach ($opsi as $o) $h .= '<option value="' . e($o) . '"' . ($o === $v ? ' selected' : '') . '>' . e($o) . '</option>';
    return $h . '</select>' . $err($name) . '</div>';
};
$fotoUtama = $produk ? fotoProduk($produk->getFotoUrl()) : null;
$aksi = $ubah ? url('admin/produk/' . $produk->getIdProduk()) : url('admin/produk');
?>
<?= \App\Core\View::partial('partials/admin-judul', [
    'eyebrow' => 'Katalog Produk', 'judul' => $ubah ? 'Ubah Produk' : 'Tambah Produk',
    'deskripsi' => $ubah ? 'Perbarui data, foto, dan atribut rekomendasi "' . $produk->getNama() . '".' : 'Lengkapi data produk baru. Kolom bertanda * wajib diisi.',
    'aksi' => \App\Core\View::partial('partials/tab-produk', ['aktif' => 'form']),
]) ?>

<form method="post" action="<?= e($aksi) ?>" enctype="multipart/form-data" class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start" data-cegah-ganda novalidate>
  <?= csrf_field() ?>
  <input type="hidden" name="MAX_FILE_SIZE" value="<?= (int) config('toko.maks_ukuran_unggah_mb', 2) * 1024 * 1024 ?>">

  <div class="xl:col-span-7 flex flex-col gap-space-lg">
    <section class="kartu p-space-lg flex flex-col gap-space-md">
      <h2 class="font-title-md text-title-md text-primary pb-3 border-b border-border-warm">Informasi Produk</h2>
      <div>
        <label for="nama" class="label">Nama Produk <span class="text-error">*</span></label>
        <input id="nama" name="nama" type="text" maxlength="150" required value="<?= e($nilai('nama', 'getNama')) ?>" class="input<?= $cls('nama') ?>" placeholder="mis. Kursi Makan Kayu Jati">
        <?= $err('nama') ?>
      </div>
      <div>
        <label for="idKategori" class="label">Kategori <span class="text-error">*</span></label>
        <select id="idKategori" name="idKategori" required class="input<?= $cls('idKategori') ?>">
          <option value="">Pilih kategori</option>
          <?php $vk = $nilai('idKategori', 'getIdKategori'); foreach ($kategori as $k): ?>
            <option value="<?= $k->getIdKategori() ?>" <?= (string) $k->getIdKategori() === $vk ? 'selected' : '' ?>><?= e($k->getNamaKategori()) ?></option>
          <?php endforeach; ?>
        </select>
        <?= $err('idKategori') ?>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
        <div>
          <label for="harga" class="label">Harga Satuan (Rp) <span class="text-error">*</span></label>
          <input id="harga" name="harga" type="number" min="0" step="1" inputmode="numeric" required value="<?= e($nilai('harga', 'getHarga')) ?>" class="input<?= $cls('harga') ?>" placeholder="450000">
          <?= $err('harga') ?>
        </div>
        <div>
          <label for="stok" class="label">Stok (unit) <span class="text-error">*</span></label>
          <input id="stok" name="stok" type="number" min="0" step="1" inputmode="numeric" required value="<?= e($nilai('stok', 'getStok')) ?>" class="input<?= $cls('stok') ?>" placeholder="10">
          <?= $err('stok') ?>
        </div>
      </div>
      <div>
        <span class="label">Dimensi (cm) <span class="text-error">*</span></span>
        <div class="grid grid-cols-3 gap-space-md">
          <?php foreach (['panjang' => ['Panjang', 'getPanjang'], 'lebar' => ['Lebar', 'getLebar'], 'tinggi' => ['Tinggi', 'getTinggi']] as $k => [$l, $g]): ?>
            <div>
              <label for="<?= $k ?>" class="font-caption text-caption text-secondary"><?= $l ?></label>
              <input id="<?= $k ?>" name="<?= $k ?>" type="number" min="0.01" step="0.01" required value="<?= e($nilai($k, $g)) ?>" class="input<?= $cls($k) ?>">
              <?= $err($k) ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div>
        <label for="deskripsi" class="label">Deskripsi <span class="text-secondary font-normal">(opsional)</span></label>
        <textarea id="deskripsi" name="deskripsi" rows="5" maxlength="5000" class="input<?= $cls('deskripsi') ?>" placeholder="Ceritakan material, finishing, dan keunggulan produk."><?= e($nilai('deskripsi', 'getDeskripsi')) ?></textarea>
        <?= $err('deskripsi') ?>
      </div>
    </section>

    <section class="kartu p-space-lg flex flex-col gap-space-md">
      <div class="pb-3 border-b border-border-warm flex items-center justify-between gap-3">
        <h2 class="font-title-md text-title-md text-primary">Atribut Rekomendasi</h2>
        <span class="chip bg-surface-subtle border-border-warm text-secondary">Rule-based scoring</span>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
        <?= $select('ruangan', 'Ruangan', $atribut['ruangan'], 'getRuangan', 'bobot +3') ?>
        <?= $select('gaya', 'Gaya/Desain', $atribut['gaya'], 'getGaya', 'bobot +2') ?>
        <?= $select('material', 'Material', $atribut['material'], 'getMaterial', 'bobot +2') ?>
        <?= $select('ukuran', 'Kategori Ukuran', $atribut['ukuran'], 'getUkuran', 'bobot +1') ?>
      </div>
      <p class="font-caption text-caption text-secondary normal-case tracking-normal">Nama produk dan kategori dipakai untuk mencocokkan “kebutuhan perabot” pelanggan (bobot +3). Pilihan dropdown dapat ditambah melalui berkas konfigurasi.</p>
    </section>
  </div>

  <div class="xl:col-span-5 flex flex-col gap-space-lg xl:sticky xl:top-24">
    <section class="kartu p-space-lg flex flex-col gap-space-md">
      <div class="pb-3 border-b border-border-warm">
        <h2 class="font-title-md text-title-md text-primary">Foto Utama Produk</h2>
        <p class="font-body-sm text-[12.5px] text-secondary">JPG/PNG/WebP, maks. <?= (int) config('toko.maks_ukuran_unggah_mb', 2) ?> MB.</p>
      </div>
      <!-- FileFoto + ImgPratinjau -->
      <div>
        <span class="label">Foto Utama <span class="text-secondary font-normal">(tampil di katalog &amp; detail)</span></span>
        <label class="relative flex flex-col items-center justify-center gap-2 aspect-[4/3] rounded-xl border-2 border-dashed <?= isset($errors['foto_utama']) ? 'border-error' : 'border-border-warm' ?> hover:border-primary bg-surface-subtle cursor-pointer overflow-hidden" data-unggah>
          <img data-pratinjau src="<?= e($fotoUtama ?? '') ?>" alt="Pratinjau foto utama" class="<?= $fotoUtama ? '' : 'hidden' ?> absolute inset-0 w-full h-full object-cover">
          <span class="relative material-symbols-outlined text-[28px] text-secondary <?= $fotoUtama ? 'hidden' : '' ?>" data-ikon-unggah>add_photo_alternate</span>
          <span class="relative font-label-sm text-label-sm px-3 py-1 rounded-full bg-surface-card/90 border border-border-warm" data-nama-berkas><?= $fotoUtama ? 'Klik untuk mengganti foto utama' : 'Pilih foto utama' ?></span>
          <input name="foto_utama" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only" data-input-gambar data-maks-mb="<?= (int) config('toko.maks_ukuran_unggah_mb', 2) ?>">
        </label>
        <?= $err('foto_utama') ?>
      </div>
    </section>

    <div class="flex gap-3">
      <!-- BtnSimpanProduk / BtnBatalProduk -->
      <button type="submit" class="btn-primer flex-1" data-tombol-kirim><span class="material-symbols-outlined text-[18px]">save</span>Simpan</button>
      <a href="<?= e(url('admin/produk')) ?>" class="btn-sekunder">Batal</a>
    </div>
  </div>
</form>
