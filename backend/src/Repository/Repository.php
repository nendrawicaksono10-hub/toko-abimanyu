<?php
namespace App\Repository;

use App\Core\Database;
use PDO;

/**
 * Kelas dasar Repository (lapisan akses data, PDO + prepared statement).
 */
abstract class Repository
{
    protected PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::get();
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    /** Escape karakter khusus LIKE (pakai "!" sebagai karakter escape, aman di semua sql_mode) (FR-03). */
    protected static function like(string $kata): string
    {
        return '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], mb_strtolower($kata, 'UTF-8')) . '%';
    }

    /**
     * Pencarian toleran (FR-03): kata kunci dipecah per kata, huruf besar/kecil diabaikan,
     * dan setiap kata cukup muncul di salah satu kolom. Contoh "meja jati" cocok dengan
     * produk bernama "Meja Makan Jati" maupun kategori "Meja" + material "Kayu Jati".
     * Seluruh nilai tetap dikirim sebagai parameter terikat (anti SQL injection).
     *
     * @param string[] $kolom ekspresi kolom tepercaya (bukan input pengguna)
     * @return array{0:string,1:array} potongan WHERE (tanpa "WHERE") dan parameternya
     */
    protected static function filterKataKunci(string $kataKunci, array $kolom): array
    {
        $token = tokenPencarian($kataKunci);
        if (!$token) {
            return ['', []];
        }
        $gabung = "LOWER(CONCAT_WS(' ', " . implode(', ', $kolom) . '))';
        $sql = [];
        $params = [];
        foreach ($token as $t) {
            $sql[] = "{$gabung} LIKE ? ESCAPE '!'";
            $params[] = self::like($t);
        }
        return ['(' . implode(' AND ', $sql) . ')', $params];
    }
}
