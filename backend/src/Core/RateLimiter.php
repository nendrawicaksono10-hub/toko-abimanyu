<?php
namespace App\Core;

/**
 * Pembatasan frekuensi per sesi DAN per alamat IP: pesanan (FR-31), percobaan login admin,
 * dan pencarian Cek Status Pesanan (mencegah tebak-tebakan nomor pesanan/brute force).
 * Jejak per-IP disimpan pada berkas di backend/storage/ratelimit/ (bukan basis data).
 */
final class RateLimiter
{
    public function __construct(private string $kunci, private int $maks, private int $detik) {}

    public function terlampaui(): bool
    {
        return count($this->jejakSesi()) >= $this->maks || count($this->jejakIp()) >= $this->maks;
    }

    public function catat(): void
    {
        $now = time();
        $s = $this->jejakSesi();
        $s[] = $now;
        $_SESSION['_rl'][$this->kunci] = $s;

        $ip = $this->jejakIp();
        $ip[] = $now;
        @file_put_contents($this->fileIp(), json_encode($ip), LOCK_EX);
    }

    /** Hapus jejak (mis. setelah login berhasil). */
    public function reset(): void
    {
        unset($_SESSION['_rl'][$this->kunci]);
        @unlink($this->fileIp());
    }

    /** Perkiraan menit sampai boleh mencoba lagi. */
    public function sisaMenit(): int
    {
        $semua = array_merge($this->jejakSesi(), $this->jejakIp());
        if (!$semua) {
            return 0;
        }
        return max(1, (int) ceil((min($semua) + $this->detik - time()) / 60));
    }

    private function jejakSesi(): array
    {
        $batas = time() - $this->detik;
        return array_values(array_filter($_SESSION['_rl'][$this->kunci] ?? [], fn($t) => $t > $batas));
    }

    private function jejakIp(): array
    {
        $f = $this->fileIp();
        $data = is_file($f) ? json_decode((string) file_get_contents($f), true) : [];
        $batas = time() - $this->detik;
        return array_values(array_filter(is_array($data) ? $data : [], fn($t) => is_int($t) && $t > $batas));
    }

    private function fileIp(): string
    {
        $dir = (string) Config::get('path.ratelimit');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        return $dir . '/' . $this->kunci . '_' . hash('sha256', Request::ip()) . '.json';
    }
}
