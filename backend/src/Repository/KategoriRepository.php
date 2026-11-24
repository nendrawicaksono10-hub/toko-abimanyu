<?php
namespace App\Repository;

use App\Entity\Kategori;

/**
 * Akses data tabel kategori.
 */
class KategoriRepository extends Repository
{
    /**
     * Seluruh kategori beserta jumlah produk.
     * @param bool $hanyaAktif true = hitung produk aktif saja (filter katalog)
     * @return Kategori[]
     */
    public function getAll(bool $hanyaAktif = false): array
    {
        $cond = $hanyaAktif ? 'AND p.aktif = 1' : '';
        $sql = "SELECT k.idKategori, k.namaKategori, COUNT(p.idProduk) AS jumlahProduk
                FROM kategori k LEFT JOIN produk p ON p.idKategori = k.idKategori {$cond}
                GROUP BY k.idKategori, k.namaKategori
                ORDER BY k.namaKategori";
        return array_map([Kategori::class, 'fromArray'], $this->pdo->query($sql)->fetchAll());
    }

    public function findById(int $id): ?Kategori
    {
        $st = $this->pdo->prepare('SELECT k.idKategori, k.namaKategori,
                (SELECT COUNT(*) FROM produk p WHERE p.idKategori = k.idKategori) AS jumlahProduk
            FROM kategori k WHERE k.idKategori = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ? Kategori::fromArray($row) : null;
    }

    /** Cek nama kategori kembar (tanpa membedakan huruf besar/kecil). */
    public function namaDipakai(string $nama, ?int $kecualiId = null): bool
    {
        $st = $this->pdo->prepare('SELECT 1 FROM kategori WHERE LOWER(namaKategori) = LOWER(?) AND idKategori <> ? LIMIT 1');
        $st->execute([$nama, $kecualiId ?? 0]);
        return (bool) $st->fetchColumn();
    }

    public function save(Kategori $k): int
    {
        $st = $this->pdo->prepare('INSERT INTO kategori (namaKategori) VALUES (?)');
        $st->execute([$k->getNamaKategori()]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(Kategori $k): void
    {
        $st = $this->pdo->prepare('UPDATE kategori SET namaKategori = ? WHERE idKategori = ?');
        $st->execute([$k->getNamaKategori(), $k->getIdKategori()]);
    }

    /**
     * Hapus kategori. Bila $pindahKe diisi, produk di kategori ini dipindahkan dulu
     * (dalam satu transaksi) sehingga tidak ada produk yang ikut hilang.
     */
    public function delete(int $id, ?int $pindahKe = null): void
    {
        $this->pdo->beginTransaction();
        try {
            if ($pindahKe !== null) {
                $st = $this->pdo->prepare('UPDATE produk SET idKategori = ? WHERE idKategori = ?');
                $st->execute([$pindahKe, $id]);
            }
            $st = $this->pdo->prepare('DELETE FROM kategori WHERE idKategori = ?');
            $st->execute([$id]);
            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Query #10(b): kategori populer berdasarkan jumlah item terjual (pesanan selain Dibatalkan).
     * @return Kategori[]
     */
    public function getKategoriPopuler(int $limit = 6): array
    {
        $sql = "SELECT k.idKategori, k.namaKategori, SUM(d.jumlah) AS terjual,
                       (SELECT COUNT(*) FROM produk x WHERE x.idKategori = k.idKategori AND x.aktif = 1) AS jumlahProduk
                FROM kategori k
                JOIN produk p ON p.idKategori = k.idKategori
                JOIN detail_pesanan d ON d.idProduk = p.idProduk
                JOIN pesanan s ON s.idPesanan = d.idPesanan
                WHERE s.status <> 'Dibatalkan'
                GROUP BY k.idKategori, k.namaKategori
                ORDER BY terjual DESC
                LIMIT {$limit}";
        return array_map([Kategori::class, 'fromArray'], $this->pdo->query($sql)->fetchAll());
    }
}
