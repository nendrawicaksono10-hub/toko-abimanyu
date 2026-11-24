<?php
/**
 * UI-18 Halaman Login Admin (FR-11).
 * MsgGagalLogin tidak menyebutkan apakah username atau password yang salah.
 */
$flash = \App\Core\Session::pullFlash();
?>
<div class="min-h-screen flex flex-col">
<header class="border-b border-border-warm bg-surface/90">
  <div class="wadah h-16 flex items-center justify-between">
    <a href="<?= e(url('/')) ?>" class="flex items-center gap-2.5"><span class="w-9 h-9 rounded-full bg-primary text-on-primary flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">chair</span></span><span class="font-display text-[21px] text-ink">Toko Abimanyu</span></a>
    <a href="<?= e(url('/')) ?>" class="font-label-sm text-label-sm text-secondary hover:text-ink flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">arrow_back</span>Kembali ke Beranda Toko</a>
  </div>
</header>
<div class="flex-1 flex items-center justify-center px-margin py-space-2xl">
  <div class="w-full max-w-md bg-surface-card border border-border-warm rounded-3xl shadow-modal p-space-lg sm:p-space-xl flex flex-col gap-space-lg">
    <div class="text-center flex flex-col items-center gap-2">
      <span class="w-14 h-14 rounded-2xl bg-sage text-on-sage flex items-center justify-center"><span class="material-symbols-outlined text-[28px]">lock</span></span>
      <span class="chip bg-surface-subtle border-border-warm text-on-surface-variant uppercase mt-2"><span class="w-1.5 h-1.5 rounded-full bg-primary"></span>Panel Admin UMKM</span>
      <h1 class="font-display text-headline-lg-mobile text-ink mt-1">Toko Abimanyu</h1>
      <p class="font-title-md text-title-md text-ink">Masuk ke Panel Admin</p>
      <p class="font-body-sm text-body-sm text-secondary">Kelola produk, stok, pesanan, pembayaran, dan laporan penjualan.</p>
    </div>

    <?php if (!empty($gagal)): ?>
      <div class="flex items-start gap-3 px-4 py-3 rounded-xl bg-error-container border border-error-container text-on-error-container" role="alert">
        <span class="material-symbols-outlined text-error text-[20px] mt-0.5">error</span>
        <div><p class="font-label-md text-label-md">Gagal Mengakses</p><p class="font-body-sm text-body-sm">Username atau password salah.</p></div>
      </div>
    <?php endif; ?>
    <?= \App\Core\View::partial('partials/flash', ['flash' => $flash]) ?>

    <form method="post" action="<?= e(url('admin/login')) ?>" class="flex flex-col gap-space-md" novalidate>
      <?= csrf_field() ?>
      <div>
        <label for="username" class="label">Username Admin <span class="text-error">*</span></label>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-secondary">person</span>
          <input id="username" name="username" type="text" maxlength="50" autocomplete="username" required autofocus value="<?= old($old, 'username') ?>" class="input pl-11 py-3">
        </div>
      </div>
      <div>
        <label for="password" class="label">Kata Sandi <span class="text-error">*</span></label>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-secondary">lock</span>
          <input id="password" name="password" type="password" autocomplete="current-password" required class="input pl-11 pr-12 py-3">
          <!-- BtnTampilPass -->
          <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-secondary hover:text-ink" data-tampil-password="#password" aria-label="Tampilkan password"><span class="material-symbols-outlined text-[20px]">visibility</span></button>
        </div>
      </div>
      <button type="submit" class="btn-primer w-full py-3.5 mt-2">Masuk ke Panel Admin <span class="material-symbols-outlined text-[18px]">arrow_forward</span></button>
    </form>
    <p class="text-center font-caption text-caption text-secondary normal-case tracking-normal flex items-center justify-center gap-1.5">
      <span class="material-symbols-outlined text-[14px]">shield</span>Sesi berakhir otomatis setelah <?= (int) config('toko.sesi_admin_menit', 30) ?> menit tanpa aktivitas.
    </p>
  </div>
</div>
</div>
