<?php
/** Notifikasi (Alert/Toast): sukses, gagal, info, peringatan. */
$flash = $flash ?? \App\Core\Session::pullFlash();
if (!$flash) return;
$gaya = [
    'sukses'     => ['check_circle', 'bg-sage/70 border-sage text-on-sage', 'Berhasil'],
    'gagal'      => ['error', 'bg-error-container/60 border-error-container text-on-error-container', 'Gagal'],
    'peringatan' => ['warning', 'bg-warning-container border-warning-container text-warning', 'Peringatan'],
    'info'       => ['info', 'bg-surface-subtle border-border-warm text-on-surface', 'Informasi'],
];
?>
<div class="flex flex-col gap-2 <?= $flashClass ?? '' ?>" role="status" aria-live="polite">
  <?php foreach ($flash as $f): [$ikon, $kelas, $judul] = $gaya[$f['type']] ?? $gaya['info']; ?>
    <div class="flex items-start gap-3 px-4 py-3 rounded-2xl border <?= $kelas ?>" data-flash>
      <span class="material-symbols-outlined text-[20px] mt-0.5"><?= $ikon ?></span>
      <div class="flex-1 min-w-0">
        <p class="font-label-md text-label-md"><?= e($judul) ?></p>
        <p class="font-body-sm text-body-sm opacity-90"><?= e($f['message']) ?></p>
      </div>
      <button type="button" class="p-1 rounded-full opacity-60 hover:opacity-100" data-tutup-flash aria-label="Tutup">
        <span class="material-symbols-outlined text-[18px]">close</span>
      </button>
    </div>
  <?php endforeach; ?>
</div>
