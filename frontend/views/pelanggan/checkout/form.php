<?php
/**
 * UI-2 Form Data Pemesanan (FR-07, FR-32, FR-41).
 * @var array $isi, $old, $errors
 * @var string $formToken token sekali pakai (cegah double submit)
 */
$err = fn($k) => isset($errors[$k]) ? '<p class="pesan-galat">' . e($errors[$k]) . '</p>' : '';
$cls = fn($k) => isset($errors[$k]) ? ' input-galat' : '';
$metodeLama = $old['metode'] ?? '';
$metode = [
    'Transfer Bank' => ['account_balance', 'Transfer ke rekening ' . config('pembayaran.bank_nama') . ' a.n. ' . config('pembayaran.atas_nama') . ', lalu unggah bukti transfer.'],
    'QRIS'          => ['qr_code_2', 'Pindai kode QRIS toko dengan aplikasi m-banking atau dompet digital, lalu unggah bukti pembayaran.'],
    'COD'           => ['local_shipping', 'Bayar di tempat saat barang diterima. Admin akan mengonfirmasi pesanan via WhatsApp.'],
];
?>
<div class="wadah">
  <?= \App\Core\View::partial('partials/langkah-checkout', ['langkah' => 1, 'label' => 'Checkout']) ?>

  <div class="pb-space-xl border-b border-border-warm mb-space-xl">
    <h1 class="font-display text-headline-lg-mobile md:text-headline-lg text-ink">Data Pemesanan</h1>
    <p class="font-body-md text-body-md text-secondary mt-1">Lengkapi data penerima dan pilih metode pembayaran.</p>
  </div>

  <form method="post" action="<?= e(url('checkout')) ?>" class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-start" data-cegah-ganda novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="_form" value="<?= e($formToken) ?>">
    <!-- Honeypot anti-spam (FR-31): harus kosong -->
    <div class="absolute -left-[9999px] w-px h-px overflow-hidden" aria-hidden="true">
      <label for="website">Jangan diisi</label>
      <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
    </div>

    <div class="lg:col-span-7 flex flex-col gap-space-lg">
      <section class="kartu p-space-lg md:p-space-xl flex flex-col gap-space-md">
        <div class="flex items-start justify-between gap-3 pb-space-md border-b border-border-warm">
          <div>
            <h2 class="font-display text-headline-sm text-ink">Data Pemesan &amp; Alamat Pengiriman</h2>
            <p class="font-body-sm text-body-sm text-secondary">Nomor WhatsApp dipakai untuk konfirmasi dan cek status pesanan.</p>
          </div>
          <span class="material-symbols-outlined text-secondary">local_shipping</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
          <div>
            <label for="nama" class="label">Nama Lengkap <span class="text-error">*</span></label>
            <input id="nama" name="nama" type="text" maxlength="100" required autocomplete="name" value="<?= old($old, 'nama') ?>" class="input<?= $cls('nama') ?>" placeholder="Nama penerima">
            <?= $err('nama') ?>
          </div>
          <div>
            <label for="kontak" class="label">Nomor WhatsApp <span class="text-error">*</span></label>
            <div class="flex">
              <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-border-warm bg-surface-subtle font-label-sm text-label-sm text-secondary">+62</span>
              <input id="kontak" name="kontak" type="tel" inputmode="tel" maxlength="20" required autocomplete="tel" value="<?= old($old, 'kontak') ?>" class="input rounded-l-none<?= $cls('kontak') ?>" placeholder="0812 3456 7890">
            </div>
            <?= $err('kontak') ?: '<p class="font-caption text-caption text-secondary normal-case tracking-normal mt-1">Boleh diawali 0, 62, atau +62.</p>' ?>
          </div>
        </div>
        <div>
          <label for="alamat" class="label">Alamat Lengkap Pengiriman <span class="text-error">*</span></label>
          <textarea id="alamat" name="alamat" rows="3" maxlength="1000" required autocomplete="street-address" class="input<?= $cls('alamat') ?>" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota, kode pos"><?= old($old, 'alamat') ?></textarea>
          <?= $err('alamat') ?>
        </div>
      </section>

      <!-- DdlMetodePembayaran -->
      <section class="kartu p-space-lg md:p-space-xl flex flex-col gap-space-md">
        <div class="flex items-start justify-between gap-3 pb-space-md border-b border-border-warm">
          <div>
            <h2 class="font-display text-headline-sm text-ink">Metode Pembayaran <span class="text-error">*</span></h2>
            <p class="font-body-sm text-body-sm text-secondary">Pembayaran dilakukan manual dan diverifikasi oleh admin.</p>
          </div>
          <span class="material-symbols-outlined text-secondary">account_balance_wallet</span>
        </div>
        <fieldset class="flex flex-col gap-3">
          <legend class="sr-only">Pilih metode pembayaran</legend>
          <?php foreach ($metode as $val => [$ikon, $ket]): ?>
            <label class="flex items-start gap-3 p-space-md rounded-xl border cursor-pointer transition-colors has-[:checked]:border-primary has-[:checked]:bg-surface-subtle border-border-warm hover:border-primary">
              <input type="radio" name="metode" value="<?= e($val) ?>" class="mt-1 accent-[#2b3528] w-4 h-4" <?= $metodeLama === $val ? 'checked' : '' ?> required>
              <span class="material-symbols-outlined text-[22px] text-ink"><?= $ikon ?></span>
              <span class="flex-1">
                <span class="block font-label-md text-label-md text-ink"><?= $val === 'COD' ? 'Bayar di Tempat (COD)' : e($val) ?></span>
                <span class="block font-body-sm text-[13px] text-secondary mt-0.5"><?= e($ket) ?></span>
              </span>
            </label>
          <?php endforeach; ?>
        </fieldset>
        <?= $err('metode') ?>
      </section>
    </div>

    <!-- Ringkasan pesanan -->
    <aside class="lg:col-span-5 kartu p-space-lg shadow-sm flex flex-col gap-space-md lg:sticky lg:top-28">
      <h2 class="font-display text-headline-sm text-ink">Ringkasan Pesanan</h2>
      <ul class="flex flex-col divide-y divide-border-warm">
        <?php foreach ($isi['items'] as $it): $p = $it['produk']; ?>
          <li class="py-3 flex items-center gap-3">
            <div class="w-12 h-12 rounded-lg overflow-hidden border border-border-warm shrink-0 bg-surface-card">
              <?= \App\Core\View::partial('partials/foto-produk', ['foto' => $p->getFotoUrl(), 'kategori' => $p->getNamaKategori(), 'alt' => $p->getNama(), 'ikonKelas' => 'text-[22px]']) ?>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-label-sm text-label-sm text-ink truncate"><?= e($p->getNama()) ?></p>
              <p class="font-caption text-caption text-secondary normal-case tracking-normal"><?= $it['jumlah'] ?> × <?= rupiah($p->getHarga()) ?></p>
            </div>
            <span class="font-label-sm text-label-sm text-ink"><?= rupiah($it['subtotal']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
      <div class="pt-space-md border-t border-border-warm flex items-end justify-between">
        <span class="font-body-sm text-body-sm text-secondary">Total Pembayaran</span>
        <span class="angka text-[24px] leading-tight font-bold text-ink"><?= rupiah($isi['total']) ?></span>
      </div>
      <p class="font-caption text-caption text-secondary normal-case tracking-normal">Total dihitung ulang dari harga terkini saat pesanan dibuat dan belum termasuk ongkos kirim.</p>

      <!-- ChkPersetujuan -->
      <label class="flex items-start gap-3 p-3 rounded-xl bg-surface-card border <?= isset($errors['setuju']) ? 'border-error' : 'border-border-warm' ?>">
        <input type="checkbox" name="setuju" value="1" class="cek mt-0.5" <?= ($old['setuju'] ?? '') === '1' ? 'checked' : '' ?> required>
        <span class="font-body-sm text-[13px] text-on-surface">Saya menyetujui penggunaan data pribadi (nama, kontak, alamat) untuk memproses pesanan sesuai <a href="<?= e(url('kebijakan-privasi')) ?>" target="_blank" rel="noopener" class="underline font-semibold">Kebijakan Privasi</a>.</span>
      </label>
      <?= $err('setuju') ?>

      <div class="flex flex-col gap-2">
        <button type="submit" class="btn-primer w-full" data-tombol-kirim>Buat Pesanan <span class="material-symbols-outlined text-[18px]">arrow_forward</span></button>
        <a href="<?= e(url('keranjang')) ?>" class="btn-sekunder w-full">Batal</a>
      </div>
    </aside>
  </form>
</div>
