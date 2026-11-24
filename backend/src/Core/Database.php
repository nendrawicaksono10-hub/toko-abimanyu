<?php
namespace App\Core;

use PDO;

/**
 * Koneksi PDO tunggal ke MySQL/MariaDB.
 * - ERRMODE_EXCEPTION agar setiap kegagalan dapat di-ROLLBACK (NFR-11)
 * - Prepared statement native (EMULATE_PREPARES = false) untuk mencegah SQL injection (NFR-05)
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function get(): PDO
    {
        if (self::$pdo === null) {
            $c = Config::get('db');
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $c['host'], $c['port'], $c['database'], $c['charset'] ?? 'utf8mb4'
            );
            self::$pdo = new PDO($dsn, $c['username'], $c['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ]);
            // Samakan zona waktu basis data dengan aplikasi (WIB) agar NOW() konsisten.
            $offset = (new \DateTime('now'))->format('P');
            self::$pdo->exec("SET time_zone = '{$offset}'");
        }
        return self::$pdo;
    }
}
