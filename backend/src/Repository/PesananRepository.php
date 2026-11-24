<?php
namespace App\Repository;

use App\Entity\Pesanan;

/**
 * Akses data tabel pesanan (Progres Sprint 3: simpan & baca pesanan).
 */
class PesananRepository extends Repository
{
    public function save(Pesanan $p): int
    {
        $st = $this->pdo->prepare('INSERT INTO pesanan (tanggal, namaPelanggan, kontak, alamat, status, totalHarga)
            VALUES (NOW(), ?, ?, ?, ?, ?)');
        $st->execute([$p->getNamaPelanggan(), $p->getKontak(), $p->getAlamat(), $p->getStatus(), $p->getTotalHarga()]);
        return (int) $this->pdo->lastInsertId();
    }

    public function findById(int $id): ?Pesanan
    {
        $st = $this->pdo->prepare('SELECT * FROM pesanan WHERE idPesanan = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ? Pesanan::fromArray($row) : null;
    }
}
