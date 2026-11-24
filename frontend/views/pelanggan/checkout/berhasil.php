<?php
/**
 * UI-3 Notifikasi Konfirmasi Pesanan (PBI-24, PBI-26 / FR-21, FR-09).
 * @var \App\Entity\Pesanan $pesanan
 * @var \App\Entity\DetailPesanan[] $items
 * @var string $metode
 * @var string $tautanWA
 */
$cod = $metode === 'COD';
?>
<div class="wadah">
  <?= \App\Core\View::partial('partials/langkah-checkout', ['langkah' => 2, 'label' => 'Pesanan Berhasil']) ?>
</div>
<div class="fixed inset-0 z-[55] bg-ink/40 backdrop-blur-[2px] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="judul-berhasil">
  <div class="w-full max-w-lg bg-surface-card rounded-3xl border border-border-warm shadow-[0_8px_30px_rgba(0,0,0,0.08)] overflow-hidden max-h-[92vh] flex flex-col">
    <div class="p-space-lg md:p-space-xl flex flex-col items-center text-center gap-3 border-b border-border-warm bg-surface-subtle">
      <span class="w-14 h-14 rounded-full bg-primary text-on-primary flex items-center justify-center"><span class="material-symbols-outlined text-[30px]">check</span></span>
      <h1 id="judul-berhasil" class="font-headline-sm text-headline-sm md:text-headline-md text-ink">Pesanan Berhasil</h1>
      <p class="font-body-sm text-body-sm text-secondary">Simpan nomor pesanan berikut sebagai referensi konfirmasi dengan admin.</p>
      <div class="flex items-center gap-2 mt-1">
        <span class="px-4 py-2 rounded-full bg-primary text-on-primary font-label-md text-label-md tracking-wider" data-salin-teks><?= e($pesanan->getKodePesanan()) ?></span>
        <button type="button" class="btn-sekunder btn-kecil" data-salin="<?= e($pesanan->getKodePesanan()) ?>"><span class="material-symbols-outlined text-[16px]">content_copy</span>Salin</button>
      </div>
    </div>
    <div class="p-space-lg md:p-space-xl flex flex-col gap-3 overflow-y-auto">
      <dl class="grid grid-cols-2 gap-y-2 font-body-sm text-body-sm">
        <dt class="text-secondary">Nama pemesan</dt><dd class="text-right text-ink"><?= e($pesanan->getNamaPelanggan()) ?></dd>
        <dt class="text-secondary">Metode pembayaran</dt><dd class="text-right text-ink"><?= e($metode) ?></dd>
        <dt class="text-secondary">Status pesanan</dt><dd class="text-right"><?= \App\Core\View::partial('partials/badge', ['status' => $pesanan->getStatus()]) ?></dd>
        <dt class="text-secondary">Total</dt><dd class="text-right font-label-md text-label-md text-ink"><?= rupiah($pesanan->getTotalHarga()) ?></dd>
      </dl>
      <p class="font-caption text-caption text-secondary normal-case tracking-normal">
        <?= $cod ? 'Pembayaran dilakukan saat barang diterima. Admin akan menghubungi Anda melalui WhatsApp untuk konfirmasi pengiriman.'
                 : 'Silakan hubungi admin via WhatsApp untuk konfirmasi pembayaran dan pengiriman pesanan Anda.' ?>
      </p>
      <div class="flex flex-col gap-2 pt-2">
        <a href="<?= e($tautanWA) ?>" target="_blank" rel="noopener" class="btn-primer w-full"><span class="material-symbols-outlined text-[18px]">chat</span>Konfirmasi via WhatsApp</a>
        <a href="<?= e(url('katalog')) ?>" class="btn-sekunder w-full">Kembali ke Katalog</a>
      </div>
    </div>
  </div>
</div>
<div class="h-[60vh]"></div>
