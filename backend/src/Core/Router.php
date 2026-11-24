<?php
namespace App\Core;

/**
 * Router sederhana: memetakan METHOD + path ke method Controller.
 * Pola: '/produk/{id}' -> {id} hanya menerima angka.
 */
final class Router
{
    private array $routes = [];

    public function get(string $pattern, array $handler, bool $admin = false): void
    {
        $this->add('GET', $pattern, $handler, $admin);
    }

    public function post(string $pattern, array $handler, bool $admin = false): void
    {
        $this->add('POST', $pattern, $handler, $admin);
    }

    private function add(string $method, string $pattern, array $handler, bool $admin): void
    {
        $regex = preg_replace_callback('/\{(\w+)\}/', function ($m) {
            return $m[1] === 'file' ? '([A-Za-z0-9._-]+)' : '(\d{1,9})';
        }, rtrim($pattern, '/') ?: '/');
        $this->routes[] = [$method, '#^' . $regex . '$#', $handler, $admin];
    }

    public function dispatch(string $method, string $path): void
    {
        $pathMatched = false;
        foreach ($this->routes as [$m, $regex, $handler, $admin]) {
            if (!preg_match($regex, $path, $params)) {
                continue;
            }
            $pathMatched = true;
            if ($m !== $method) {
                continue;
            }
            array_shift($params);

            if ($method === 'POST') {
                $this->verifikasiPost();
            }
            if ($admin) {
                (new \App\Controller\AuthController())->cekSesi();
            }
            [$class, $action] = $handler;
            $args = array_map(fn($p) => ctype_digit($p) ? (int) $p : $p, $params);
            (new $class())->$action(...$args);
            return;
        }
        if ($pathMatched) {
            http_response_code(405);
        }
        Response::notFound();
    }

    /** Setiap POST wajib membawa token CSRF yang valid (NFR-05). */
    private function verifikasiPost(): void
    {
        $len = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
        $max = self::iniBytes((string) ini_get('post_max_size'));
        if ($max > 0 && $len > $max) {
            $msg = 'Ukuran data yang dikirim terlalu besar. Periksa ukuran berkas (maksimal ' . Config::get('toko.maks_ukuran_unggah_mb', 2) . ' MB per berkas).';
            if (Request::wantsJson()) {
                Response::json('error', null, $msg, 413);
            }
            Session::flash('gagal', $msg);
            Response::back();
        }
        if (!Csrf::valid()) {
            $msg = 'Permintaan tidak valid atau sesi formulir telah kedaluwarsa. Silakan muat ulang halaman dan coba beberapa saat lagi.';
            if (Request::wantsJson()) {
                Response::json('error', null, $msg, 419);
            }
            Session::flash('gagal', $msg);
            Response::back();
        }
    }

    private static function iniBytes(string $v): int
    {
        $v = trim($v);
        $n = (int) $v;
        return match (strtolower(substr($v, -1))) {
            'g' => $n * 1024 ** 3,
            'm' => $n * 1024 ** 2,
            'k' => $n * 1024,
            default => $n,
        };
    }
}
