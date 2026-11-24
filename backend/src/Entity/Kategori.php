<?php
namespace App\Entity;

/**
 * Entity kategori (tabel kategori, Tabel 4.11).
 */
class Kategori
{
    private ?int $idKategori = null;
    private string $namaKategori = '';
    private int $jumlahProduk = 0;

    public function getDetail(): array
    {
        return $this->toArray();
    }

    /** Bentuk entity dari baris hasil query. */
    public static function fromArray(array $row): self
    {
        $e = new self();
        if (array_key_exists('idKategori', $row)) { $e->idKategori = $row['idKategori'] === null ? null : (int) $row['idKategori']; }
        if (isset($row['namaKategori'])) { $e->namaKategori = (string) $row['namaKategori']; }
        if (isset($row['jumlahProduk'])) { $e->jumlahProduk = (int) $row['jumlahProduk']; }
        return $e;
    }

    public function toArray(): array
    {
        return [
            'idKategori' => $this->idKategori,
            'namaKategori' => $this->namaKategori,
            'jumlahProduk' => $this->jumlahProduk,
        ];
    }

    public function getIdKategori(): ?int
    {
        return $this->idKategori;
    }

    public function setIdKategori(?int $idKategori): void
    {
        $this->idKategori = $idKategori;
    }

    public function getNamaKategori(): string
    {
        return $this->namaKategori;
    }

    public function setNamaKategori(string $namaKategori): void
    {
        $this->namaKategori = $namaKategori;
    }

    public function getJumlahProduk(): int
    {
        return $this->jumlahProduk;
    }

    public function setJumlahProduk(int $jumlahProduk): void
    {
        $this->jumlahProduk = $jumlahProduk;
    }
}
