<?php
namespace App\Core;

/**
 * Hak akses pelanggan (tanpa login) ke halaman pesanan/pembayaran.
 * Akses diberikan setelah checkout berhasil ATAU setelah ID pesanan + nomor kontak
 * cocok pada halaman Cek Status (FR-08), dan disimpan pada sesi.
 */
final class AksesPesanan
{
    public static function beri(int $idPesanan, string $kontak): void
    {
        $_SESSION['akses_pesanan'][$idPesanan] = $kontak;
    }

    public static function kontak(int $idPesanan): ?string
    {
        return $_SESSION['akses_pesanan'][$idPesanan] ?? null;
    }

    public static function punya(int $idPesanan): bool
    {
        return self::kontak($idPesanan) !== null;
    }
}
