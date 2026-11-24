<?php
/** HalamanUbahPasswordView (FR-27). */
$err = fn($k) => isset($errors[$k]) ? '<p class="pesan-galat">' . e($errors[$k]) . '</p>' : '';
$pw = function (string $name, string $label, string $auto) use ($errors, $err) {
    return '<div><label for="' . $name . '" class="label">' . $label . ' <span class="text-error">*</span></label>'
        . '<div class="relative"><input id="' . $name . '" name="' . $name . '" type="password" autocomplete="' . $auto . '" required class="input pr-12' . (isset($errors[$name]) ? ' input-galat' : '') . '">'
        . '<button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-secondary hover:text-ink" data-tampil-password="#' . $name . '" aria-label="Tampilkan password"><span class="material-symbols-outlined text-[20px]">visibility</span></button></div>'
        . $err($name) . '</div>';
};
?>
<?= \App\Core\View::partial('partials/admin-judul', [
    'eyebrow' => 'Keamanan & Akun', 'judul' => 'Ubah Password',
    'deskripsi' => 'Ganti password admin secara berkala. Password disimpan dalam bentuk hash (bcrypt).',
]) ?>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-start">
  <form method="post" action="<?= e(url('admin/password')) ?>" class="lg:col-span-7 kartu p-space-lg md:p-space-xl flex flex-col gap-space-md" novalidate>
    <?= csrf_field() ?>
    <?= $pw('password_lama', 'Password Lama', 'current-password') ?>
    <?= $pw('password_baru', 'Password Baru', 'new-password') ?>
    <?= $pw('password_konfirmasi', 'Ulangi Password Baru', 'new-password') ?>
    <div class="flex gap-3 pt-2">
      <button type="submit" class="btn-primer"><span class="material-symbols-outlined text-[18px]">key</span>Simpan Password Baru</button>
    </div>
  </form>
  <aside class="lg:col-span-5 kartu-muted p-space-lg flex flex-col gap-3">
    <h2 class="font-title-md text-title-md">Ketentuan Password</h2>
    <ul class="font-body-sm text-body-sm text-secondary space-y-2 list-disc pl-5">
      <li>Minimal 8 karakter; gunakan kombinasi huruf, angka, dan simbol.</li>
      <li>Password lama wajib diisi dengan benar.</li>
      <li>Sistem tidak menyediakan fitur lupa password. Bila lupa, minta pengembang mereset password melalui phpMyAdmin (lihat panduan di README).</li>
    </ul>
  </aside>
</div>
