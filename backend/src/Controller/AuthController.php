<?php
namespace App\Controller;

use App\Core\Config;
use App\Core\Csrf;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Repository\AdminRepository;

/**
 * Use case #9 Login Admin: login (FR-11), logout (FR-26), ubah password (FR-27), cek sesi.
 */
class AuthController
{
    private AdminRepository $adminRepo;

    public function __construct()
    {
        $this->adminRepo = new AdminRepository();
    }

    /** GET /admin/login - UI-18. */
    public function formLogin(): void
    {
        if (adminLogin()) {
            Response::redirect('/admin/produk');
        }
        Response::view('admin/auth/login', [
            'title' => 'Login Admin',
            'old'   => Session::pullOld(),
            'gagal' => Session::get('_login_gagal'),
        ], 'polos');
    }

    /** POST /admin/login */
    public function login(): void
    {
        $username = mb_substr(Request::post('username'), 0, 50);
        $password = (string) ($_POST['password'] ?? '');
        Session::forget('_login_gagal');

        // Anti brute force: maks. 5 kali gagal per 15 menit per sesi & per alamat IP.
        $limiter = new RateLimiter('login', 5, 15 * 60);
        if ($limiter->terlampaui()) {
            Session::flashInput(['username' => $username]);
            Session::flash('gagal', 'Terlalu banyak percobaan login gagal. Coba lagi dalam ' . $limiter->sisaMenit() . ' menit.');
            Response::redirect('/admin/login');
        }

        if ($username === '' || $password === '') {
            Session::flashInput(['username' => $username]);
            Session::flash('gagal', 'Username dan password wajib diisi.');
            Response::redirect('/admin/login');
        }
        $admin = $this->adminRepo->findByUsername($username);
        // Verifikasi tetap dijalankan walau username tidak ada agar waktu respons sama.
        $hash = $admin?->getPassword() ?? '$2y$10$LX5hm9RkupCqz4wo9gm0XeXdehrnZxQq.asNmHnpNLIEXvnNc0lfO';
        $cocok = password_verify($password, $hash) && $admin !== null;

        if (!$cocok) {
            $limiter->catat();
            Session::flashInput(['username' => $username]);
            Session::set('_login_gagal', true);
            Response::redirect('/admin/login');
        }
        $limiter->reset();
        // Perbarui hash bila algoritma/biaya bawaan PHP berubah.
        if (password_needs_rehash($admin->getPassword(), PASSWORD_DEFAULT)) {
            $this->adminRepo->updatePassword($admin->getUsername(), password_hash($password, PASSWORD_DEFAULT));
        }
        Session::regenerate(); // ID sesi diperbarui setelah login (NFR-05)
        Csrf::rotate();
        Session::forget('_login_gagal');
        Session::set('admin', $admin->getUsername());
        // Tandai bila masih memakai password bawaan instalasi -> panel menampilkan peringatan.
        Session::set('pw_bawaan', hash_equals('Abimanyu#2026', $password));
        Session::set('admin_aktivitas', time());
        $tujuan = Session::get('admin_tujuan', '/admin/produk');
        Session::forget('admin_tujuan');
        Response::redirect(is_string($tujuan) && str_starts_with($tujuan, '/admin') ? $tujuan : '/admin/produk');
    }

    /** POST /admin/logout - hapus seluruh data sesi. */
    public function logout(): void
    {
        Session::destroy();
        Session::start();
        Session::flash('info', 'Anda telah keluar dari panel admin.');
        Response::redirect('/admin/login');
    }

    /**
     * Middleware halaman admin: wajib login & sesi belum kedaluwarsa (30 menit tanpa aktivitas).
     */
    public function cekSesi(): void
    {
        // Halaman admin tidak boleh disimpan cache browser (tombol Back setelah logout).
        header('Cache-Control: no-store, no-cache, must-revalidate, private');
        header('Pragma: no-cache');

        $batas = 60 * (int) Config::get('toko.sesi_admin_menit', 30);
        $terakhir = (int) Session::get('admin_aktivitas', 0);
        if (adminLogin() && $terakhir > 0 && time() - $terakhir > $batas) {
            Session::destroy();
            Session::start();
            Session::flash('info', 'Sesi berakhir karena tidak ada aktivitas selama ' . ($batas / 60) . ' menit. Silakan login kembali.');
        }
        if (!adminLogin()) {
            if (Request::wantsJson()) {
                Response::json('error', null, 'Sesi admin tidak valid. Silakan login.', 401);
            }
            if (Request::method() === 'GET') {
                Session::set('admin_tujuan', currentPath() . (($_SERVER['QUERY_STRING'] ?? '') !== '' ? '?' . $_SERVER['QUERY_STRING'] : ''));
            }
            Response::redirect('/admin/login');
        }
        Session::set('admin_aktivitas', time());
    }

    /** GET /admin/password - Ubah Password (HalamanUbahPasswordView). */
    public function formUbahPassword(): void
    {
        Response::view('admin/auth/password', [
            'title'  => 'Ubah Password',
            'errors' => Session::pullErrors(),
        ], 'admin');
    }

    /** POST /admin/password */
    public function ubahPassword(): void
    {
        $v = new Validator($_POST);
        $lama = (string) ($_POST['password_lama'] ?? '');
        $baru = (string) ($_POST['password_baru'] ?? '');
        $ulang = (string) ($_POST['password_konfirmasi'] ?? '');
        if ($lama === '') {
            $v->add('password_lama', 'Password lama wajib diisi.');
        }
        if ($baru === '') {
            $v->add('password_baru', 'Password baru wajib diisi.');
        } elseif (strlen($baru) < 8) {
            $v->add('password_baru', 'Password baru minimal 8 karakter.');
        } elseif (strlen($baru) > 72) {
            $v->add('password_baru', 'Password baru maksimal 72 karakter.');
        }
        if ($baru !== '' && $baru !== $ulang) {
            $v->add('password_konfirmasi', 'Konfirmasi password baru tidak sama.');
        }
        $admin = $this->adminRepo->findByUsername((string) adminLogin());
        if (!$v->fails() && (!$admin || !password_verify($lama, $admin->getPassword()))) {
            $v->add('password_lama', 'Password lama salah.');
        }
        if ($v->fails()) {
            Session::flashErrors($v->errors());
            Session::flash('gagal', 'Password tidak diubah: ' . $v->firstError());
            Response::redirect('/admin/password');
        }
        $this->adminRepo->updatePassword($admin->getUsername(), password_hash($baru, PASSWORD_DEFAULT));
        Session::regenerate();
        Session::forget('pw_bawaan');
        Session::flash('sukses', 'Password berhasil diubah. Gunakan password baru pada login berikutnya.');
        Response::redirect('/admin/password');
    }
}
