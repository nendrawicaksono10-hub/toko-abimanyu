<?php /** Tab Kategori | Stok. Variabel: $aktif */ ?>
<div class="flex items-center p-1 rounded-full border border-border-warm bg-surface-subtle w-fit">
  <a href="<?= e(url('admin/kategori')) ?>" class="flex items-center gap-2 px-5 py-2 rounded-full font-label-md text-label-md <?= $aktif === 'kategori' ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:text-primary' ?>"><span class="material-symbols-outlined text-[18px]">category</span>Kelola Kategori</a>
  <a href="<?= e(url('admin/stok')) ?>" class="flex items-center gap-2 px-5 py-2 rounded-full font-label-md text-label-md <?= $aktif === 'stok' ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:text-primary' ?>"><span class="material-symbols-outlined text-[18px]">warehouse</span>Kontrol Stok</a>
</div>
