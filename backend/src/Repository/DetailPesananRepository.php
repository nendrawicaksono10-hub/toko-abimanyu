<?php
namespace App\Repository;

use App\Entity\DetailPesanan;

/**
 * Akses data tabel detail_pesanan.
 */
class DetailPesananRepository extends Repository
{
    public function save(DetailPesanan $d): int
    {
        // Nama, kategori & harga disalin saat transaksi agar riwayat tetap utuh walau produk dihapus.
        $st = $this->pdo->prepare('INSERT INTO detail_pesanan
            (idPesanan, idProduk, jumlah, subtotal, namaProduk, namaKategori, hargaSatuan) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $st->execute([
            $d->getIdPesanan(), $d->getIdProduk(), $d->getJumlah(), $d->getSubtotal(),
            mb_substr((string) $d->getNamaProduk(), 0, 150), $d->getNamaKategori(), $d->getHargaSatuan(),
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    /** @return DetailPesanan[] */
    public function findByPesanan(int $idPesanan): array
    {
        // LEFT JOIN: baris tetap tampil walau produknya sudah dihapus (pakai salinan nama/kategori).
        $st = $this->pdo->prepare("SELECT d.idDetail, d.idPesanan, d.idProduk, d.jumlah, d.subtotal, d.hargaSatuan,
                COALESCE(NULLIF(d.namaProduk, ''), p.nama, 'Produk telah dihapus') AS namaProduk,
                COALESCE(d.namaKategori, k.namaKategori) AS namaKategori,
                p.fotoUrl, (p.idProduk IS NOT NULL) AS produkTersedia
            FROM detail_pesanan d
            LEFT JOIN produk p ON p.idProduk = d.idProduk
            LEFT JOIN kategori k ON k.idKategori = p.idKategori
            WHERE d.idPesanan = ? ORDER BY d.idDetail");
        $st->execute([$idPesanan]);
        return array_map([DetailPesanan::class, 'fromArray'], $st->fetchAll());
    }
}
