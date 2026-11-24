<?php /** Badge status pesanan/pembayaran. Variabel: $status, $kecil (bool) */ ?>
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border <?= badgeStatus($status) ?> font-caption text-caption whitespace-nowrap">
  <span class="w-1.5 h-1.5 rounded-full bg-current"></span><?= e($status) ?>
</span>
