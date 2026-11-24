<?php
namespace App\Repository;

use App\Entity\Produk;

/**
 * Akses data tabel produk.
 */
class ProdukRepository extends Repository
{
    /** FR-44: daftar putih urutan (tidak diikat sebagai parameter). */
    public const URUTAN = [
        'terbaru'  => 'p.idProduk DESC',
        'termurah' => 'p.harga ASC, p.idProduk DESC',
        'termahal' => 'p.harga DESC, p.idProduk DESC',
    ];

    /** Kolom yang ditelusuri pencarian (nama, kategori, atribut). */
    private const KOLOM_CARI = ['p.nama', 'k.namaKategori', 'p.material', 'p.ruangan', 'p.gaya'];

    private const SELECT = 'SELECT p.*, k.namaKategori FROM produk p JOIN kategori k ON k.idKategori = p.idKategori';

    // ------------------------------------------------------------------
    // Katalog pelanggan (Query #1): hanya produk aktif, 12 per halaman
    // ------------------------------------------------------------------

    private function filterAktif(string $kataKunci, ?int $idKategori): array
    {
        $where = ['p.aktif = 1'];
        $params = [];
        [$sqlKata, $paramKata] = self::filterKataKunci($kataKunci, self::KOLOM_CARI);
        if ($sqlKata !== '') {
            $where[] = $sqlKata;
            array_push($params, ...$paramKata);
        }
        if ($idKategori !== null) {
            $where[] = 'p.idKategori = ?';
            $params[] = $idKategori;
        }
        return [implode(' AND ', $where), $params];
    }

    public function hitungAktif(string $kataKunci = '', ?int $idKategori = null): int
    {
        [$where, $params] = $this->filterAktif($kataKunci, $idKategori);
        $st = $this->pdo->prepare("SELECT COUNT(*) FROM produk p JOIN kategori k ON k.idKategori = p.idKategori WHERE {$where}");
        $st->execute($params);
        return (int) $st->fetchColumn();
    }

    /**
     * Katalog / pencarian / filter kategori dengan urutan & pagination.
     * @return Produk[]
     */
    public function search(string $kataKunci, ?int $idKategori, string $urutan, int $limit, int $offset): array
    {
        [$where, $params] = $this->filterAktif($kataKunci, $idKategori);
        $order = self::URUTAN[$urutan] ?? self::URUTAN['terbaru'];
        $st = $this->pdo->prepare(self::SELECT . " WHERE {$where} ORDER BY {$order} LIMIT {$limit} OFFSET {$offset}");
        $st->execute($params);
        return array_map([Produk::class, 'fromArray'], $st->fetchAll());
    }

    /** @return Produk[] */
    public function findByKategori(int $idKategori, string $urutan, int $limit, int $offset): array
    {
        return $this->search('', $idKategori, $urutan, $limit, $offset);
    }

    /** @return Produk[] */
    public function getAktif(string $urutan, int $limit, int $offset): array
    {
        return $this->search('', null, $urutan, $limit, $offset);
    }

    /**
     * Query #10(a): produk unggulan = produk aktif terlaris; bila kurang, diisi produk aktif terbaru.
     * @return Produk[]
     */
    public function getProdukUnggulan(int $limit = 8): array
    {
        $sql = "SELECT p.*, k.namaKategori, SUM(d.jumlah) AS terjual
                FROM produk p
                JOIN kategori k ON k.idKategori = p.idKategori
                JOIN detail_pesanan d ON d.idProduk = p.idProduk
                JOIN pesanan s ON s.idPesanan = d.idPesanan
                WHERE p.aktif = 1 AND s.status <> 'Dibatalkan'
                GROUP BY p.idProduk
                ORDER BY terjual DESC, p.nama
                LIMIT {$limit}";
        $hasil = array_map([Produk::class, 'fromArray'], $this->pdo->query($sql)->fetchAll());

        if (count($hasil) < $limit) {
            $ids = array_map(fn(Produk $p) => $p->getIdProduk(), $hasil);
            $sisa = $limit - count($hasil);
            $not = $ids ? 'AND p.idProduk NOT IN (' . implode(',', array_map('intval', $ids)) . ')' : '';
            $st = $this->pdo->query(self::SELECT . " WHERE p.aktif = 1 {$not} ORDER BY p.idProduk DESC LIMIT {$sisa}");
            $hasil = array_merge($hasil, array_map([Produk::class, 'fromArray'], $st->fetchAll()));
        }
        return $hasil;
    }

    public function findById(int $id, bool $hanyaAktif = false): ?Produk
    {
        $sql = self::SELECT . ' WHERE p.idProduk = ?' . ($hanyaAktif ? ' AND p.aktif = 1' : '');
        $st = $this->pdo->prepare($sql);
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ? Produk::fromArray($row) : null;
    }

    /**
     * Harga & stok terkini beberapa produk sekaligus (keranjang/checkout).
     * @return array<int, Produk>
     */
    public function getHargaByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (!$ids) {
            return [];
        }
        $in = implode(',', array_fill(0, count($ids), '?'));
        $st = $this->pdo->prepare(self::SELECT . " WHERE p.idProduk IN ({$in})");
        $st->execute($ids);
        $out = [];
        foreach ($st->fetchAll() as $row) {
            $out[(int) $row['idProduk']] = Produk::fromArray($row);
        }
        return $out;
    }

    public function getHargaStok(int $id): ?Produk
    {
        return $this->findById($id);
    }

    /** Nilai unik atribut (ruangan/gaya/material/ukuran) produk aktif. */
    public function nilaiAtribut(string $kolom): array
    {
        if (!in_array($kolom, ['ruangan', 'gaya', 'material', 'ukuran'], true)) {
            return [];
        }
        return $this->pdo->query("SELECT DISTINCT {$kolom} FROM produk WHERE aktif = 1 ORDER BY {$kolom}")->fetchAll(\PDO::FETCH_COLUMN);
    }

    // ------------------------------------------------------------------
    // Admin: daftar produk (20 baris per halaman)
    // ------------------------------------------------------------------

    public function hitungSemua(string $kataKunci = ''): int
    {
        [$sqlKata, $params] = self::filterKataKunci($kataKunci, self::KOLOM_CARI);
        if ($sqlKata === '') {
            return (int) $this->pdo->query('SELECT COUNT(*) FROM produk')->fetchColumn();
        }
        $st = $this->pdo->prepare("SELECT COUNT(*) FROM produk p JOIN kategori k ON k.idKategori = p.idKategori WHERE {$sqlKata}");
        $st->execute($params);
        return (int) $st->fetchColumn();
    }

    /** @return Produk[] */
    public function getAll(string $kataKunci, int $limit, int $offset, string $order = 'p.idProduk DESC'): array
    {
        [$sqlKata, $params] = self::filterKataKunci($kataKunci, self::KOLOM_CARI);
        $where = $sqlKata !== '' ? "WHERE {$sqlKata}" : '';
        $st = $this->pdo->prepare(self::SELECT . " {$where} ORDER BY {$order} LIMIT {$limit} OFFSET {$offset}");
        $st->execute($params);
        return array_map([Produk::class, 'fromArray'], $st->fetchAll());
    }

    /** Daftar ringkas seluruh produk (dropdown atribut rekomendasi). */
    public function getDaftarRingkas(): array
    {
        return $this->pdo->query('SELECT idProduk, nama, aktif FROM produk ORDER BY nama')->fetchAll();
    }

    /** @return array{total:int, aktif:int, nonaktif:int} */
    public function hitungProduk(): array
    {
        $r = $this->pdo->query('SELECT COUNT(*) AS total, COALESCE(SUM(aktif = 1),0) AS aktif FROM produk')->fetch();
        return ['total' => (int) $r['total'], 'aktif' => (int) $r['aktif'], 'nonaktif' => (int) $r['total'] - (int) $r['aktif']];
    }

    public function save(Produk $p): int
    {
        $st = $this->pdo->prepare('INSERT INTO produk
            (idKategori, nama, ruangan, gaya, material, ukuran, panjang, lebar, tinggi, harga, stok, deskripsi, fotoUrl, aktif)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $st->execute([
            $p->getIdKategori(), $p->getNama(), $p->getRuangan(), $p->getGaya(), $p->getMaterial(), $p->getUkuran(),
            $p->getPanjang(), $p->getLebar(), $p->getTinggi(), $p->getHarga(), $p->getStok(),
            $p->getDeskripsi(), $p->getFotoUrl(), $p->getAktif() ? 1 : 0,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(Produk $p): void
    {
        $st = $this->pdo->prepare('UPDATE produk SET idKategori=?, nama=?, ruangan=?, gaya=?, material=?, ukuran=?,
            panjang=?, lebar=?, tinggi=?, harga=?, stok=?, deskripsi=?, fotoUrl=? WHERE idProduk=?');
        $st->execute([
            $p->getIdKategori(), $p->getNama(), $p->getRuangan(), $p->getGaya(), $p->getMaterial(), $p->getUkuran(),
            $p->getPanjang(), $p->getLebar(), $p->getTinggi(), $p->getHarga(), $p->getStok(),
            $p->getDeskripsi(), $p->getFotoUrl(), $p->getIdProduk(),
        ]);
    }

    /** FR-19: perbarui atribut rekomendasi saja. */
    public function updateAtribut(int $id, int $idKategori, string $ruangan, string $gaya, string $material, string $ukuran, float $harga): void
    {
        $st = $this->pdo->prepare('UPDATE produk SET idKategori=?, ruangan=?, gaya=?, material=?, ukuran=?, harga=? WHERE idProduk=?');
        $st->execute([$idKategori, $ruangan, $gaya, $material, $ukuran, $harga, $id]);
    }

    /** Produk yang sudah tercatat di riwayat pesanan tidak boleh dihapus (FR-13). */
    public function punyaRiwayatPesanan(int $id): bool
    {
        $st = $this->pdo->prepare('SELECT 1 FROM detail_pesanan WHERE idProduk = ? LIMIT 1');
        $st->execute([$id]);
        return (bool) $st->fetchColumn();
    }

    public function delete(int $id): void
    {
        $st = $this->pdo->prepare('DELETE FROM produk WHERE idProduk = ?');
        $st->execute([$id]);
    }

    /** FR-42: nonaktifkan / aktifkan kembali produk. */
    public function updateAktif(int $id, bool $aktif): void
    {
        $st = $this->pdo->prepare('UPDATE produk SET aktif = ? WHERE idProduk = ?');
        $st->execute([$aktif ? 1 : 0, $id]);
    }

    // ------------------------------------------------------------------
    // Stok (FR-15, FR-23, FR-24, FR-30)
    // ------------------------------------------------------------------

    public function updateStok(int $id, int $stok): void
    {
        $st = $this->pdo->prepare('UPDATE produk SET stok = ? WHERE idProduk = ?');
        $st->execute([$stok, $id]);
    }

    /**
     * Query #6: pengurangan stok atomik. Mengembalikan false bila stok tidak mencukupi
     * (baris terdampak = 0) sehingga transaksi pemesanan harus di-ROLLBACK.
     */
    public function kurangiStokAtomik(int $idProduk, int $jumlah): bool
    {
        $st = $this->pdo->prepare('UPDATE produk SET stok = stok - ? WHERE idProduk = ? AND aktif = 1 AND stok >= ?');
        $st->execute([$jumlah, $idProduk, $jumlah]);
        return $st->rowCount() === 1;
    }

    /**
     * Query #4: daftar stok menipis (stok < ambang), dihitung dari data terkini.
     * @return Produk[]
     */
    public function getStokMenipis(int $ambang): array
    {
        $st = $this->pdo->prepare(self::SELECT . ' WHERE p.stok < ? ORDER BY p.stok ASC, p.nama');
        $st->execute([$ambang]);
        return array_map([Produk::class, 'fromArray'], $st->fetchAll());
    }
}
