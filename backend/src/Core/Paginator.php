<?php
namespace App\Core;

/**
 * Perhitungan pagination (FR-02: 12/halaman, NFR-07: 20 baris di admin).
 */
final class Paginator
{
    public readonly int $halaman;
    public readonly int $totalHalaman;
    public readonly int $offset;

    public function __construct(public readonly int $total, public readonly int $perHalaman, ?int $halaman)
    {
        $this->totalHalaman = max(1, (int) ceil($total / max(1, $perHalaman)));
        // Halaman 0, negatif, huruf, atau melebihi jumlah halaman -> halaman valid terdekat.
        $h = $halaman ?? 1;
        $this->halaman = min(max(1, $h), $this->totalHalaman);
        $this->offset = ($this->halaman - 1) * $perHalaman;
    }

    public function dari(): int
    {
        return $this->total === 0 ? 0 : $this->offset + 1;
    }

    public function sampai(): int
    {
        return min($this->total, $this->offset + $this->perHalaman);
    }

    /** Daftar nomor halaman untuk ditampilkan, dengan '…' sebagai pemisah. */
    public function nomor(): array
    {
        $n = $this->totalHalaman;
        $c = $this->halaman;
        if ($n <= 7) {
            return range(1, $n);
        }
        $pages = array_unique(array_filter([1, 2, $c - 1, $c, $c + 1, $n - 1, $n], fn($p) => $p >= 1 && $p <= $n));
        sort($pages);
        $out = [];
        $prev = 0;
        foreach ($pages as $p) {
            if ($p - $prev > 1) {
                $out[] = '…';
            }
            $out[] = $p;
            $prev = $p;
        }
        return $out;
    }
}
