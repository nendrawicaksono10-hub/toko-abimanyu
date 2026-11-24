<?php
namespace App\Entity;

/**
 * Entity admin (tabel admin, Tabel 4.15). Password disimpan sebagai hash bcrypt.
 */
class Admin
{
    private string $username = '';
    private string $password = '';

    public function getDetail(): array
    {
        return ['username' => $this->username];
    }

    /** Bentuk entity dari baris hasil query. */
    public static function fromArray(array $row): self
    {
        $e = new self();
        if (isset($row['username'])) { $e->username = (string) $row['username']; }
        if (isset($row['password'])) { $e->password = (string) $row['password']; }
        return $e;
    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
        ];
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
