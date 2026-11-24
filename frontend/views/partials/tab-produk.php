<?php /** Tab "Daftar Produk | Formulir Input Produk". Variabel: $aktif ('daftar'|'form'), $jumlah */ ?>
<div class="flex items-center p-1 rounded-full border border-border-warm bg-surface-subtle w-fit">
  <a href="<?= e(url('admin/produk')) ?>" class="flex items-center gap-2 px-5 py-2 rounded-full font-label-md text-label-md <?= $aktif === 'daftar' ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:text-primary' ?>">
    <span class="material-symbols-outlined text-[18px]">view_list</span>Daftar Produk<?php if (isset($jumlah)): ?><span class="px-2 py-0.5 rounded-full text-[11px] <?= $aktif === 'daftar' ? 'bg-surface-card/20' : 'bg-surface-card' ?>"><?= $jumlah ?></span><?php endif; ?>
  </a>
  <a href="<?= e(url('admin/produk/tambah')) ?>" class="flex items-center gap-2 px-5 py-2 rounded-full font-label-md text-label-md <?= $aktif === 'form' ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:text-primary' ?>">
    <span class="material-symbols-outlined text-[18px]">edit_note</span>Formulir Produk
  </a>
</div>
