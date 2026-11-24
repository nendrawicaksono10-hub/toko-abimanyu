<?php
namespace App\Repository;

use App\Entity\Admin;

/**
 * Akses data tabel admin.
 */
class AdminRepository extends Repository
{
    public function findByUsername(string $username): ?Admin
    {
        $st = $this->pdo->prepare('SELECT username, password FROM admin WHERE username = ?');
        $st->execute([$username]);
        $row = $st->fetch();
        return $row ? Admin::fromArray($row) : null;
    }

    public function updatePassword(string $username, string $hash): void
    {
        $st = $this->pdo->prepare('UPDATE admin SET password = ? WHERE username = ?');
        $st->execute([$hash, $username]);
    }
}
