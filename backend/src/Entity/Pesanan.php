<?php
namespace App\Entity;

/**
 * Entity pesanan (tabel pesanan, Tabel 4.13).
 */
class Pesanan
{
    private ?int $idPesanan = null;
    private string $tanggal = '';
    private string $namaPelanggan = '';
    private string $kontak = '';
    private string $alamat = '';
    private string $status = 'Menunggu Konfirmasi';
    private float $totalHarga = 0.0;

    public const STATUS = ['Menunggu Konfirmasi', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'];

    /** Tabel 4.10 Alur Perubahan Status Pesanan. */
    public const TRANSISI = [
        'Menunggu Konfirmasi' => ['Diproses', 'Dibatalkan'],
        'Diproses'            => ['Dikirim', 'Dibatalkan'],
        'Dikirim'             => ['Selesai'],
        'Selesai'             => [],
        'Dibatalkan'          => [],
    ];

    /** FR-21: kode tampilan ORD-nnnnnn (tidak disimpan di basis data). */
    public function getKodePesanan(): string
    {
        return kodePesanan((int) $this->idPesanan);
    }

    public function getDetail(): array
    {
        return $this->toArray() + ['kodePesanan' => $this->getKodePesanan()];
    }

    /** Bentuk entity dari baris hasil query. */
    public static function fromArray(array $row): self
    {
        $e = new self();
        if (array_key_exists('idPesanan', $row)) { $e->idPesanan = $row['idPesanan'] === null ? null : (int) $row['idPesanan']; }
        if (isset($row['tanggal'])) { $e->tanggal = (string) $row['tanggal']; }
        if (isset($row['namaPelanggan'])) { $e->namaPelanggan = (string) $row['namaPelanggan']; }
        if (isset($row['kontak'])) { $e->kontak = (string) $row['kontak']; }
        if (isset($row['alamat'])) { $e->alamat = (string) $row['alamat']; }
        if (isset($row['status'])) { $e->status = (string) $row['status']; }
        if (isset($row['totalHarga'])) { $e->totalHarga = (float) $row['totalHarga']; }
        return $e;
    }

    public function toArray(): array
    {
        return [
            'idPesanan' => $this->idPesanan,
            'tanggal' => $this->tanggal,
            'namaPelanggan' => $this->namaPelanggan,
            'kontak' => $this->kontak,
            'alamat' => $this->alamat,
            'status' => $this->status,
            'totalHarga' => $this->totalHarga,
        ];
    }

    public function getIdPesanan(): ?int
    {
        return $this->idPesanan;
    }

    public function setIdPesanan(?int $idPesanan): void
    {
        $this->idPesanan = $idPesanan;
    }

    public function getTanggal(): string
    {
        return $this->tanggal;
    }

    public function setTanggal(string $tanggal): void
    {
        $this->tanggal = $tanggal;
    }

    public function getNamaPelanggan(): string
    {
        return $this->namaPelanggan;
    }

    public function setNamaPelanggan(string $namaPelanggan): void
    {
        $this->namaPelanggan = $namaPelanggan;
    }

    public function getKontak(): string
    {
        return $this->kontak;
    }

    public function setKontak(string $kontak): void
    {
        $this->kontak = $kontak;
    }

    public function getAlamat(): string
    {
        return $this->alamat;
    }

    public function setAlamat(string $alamat): void
    {
        $this->alamat = $alamat;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getTotalHarga(): float
    {
        return $this->totalHarga;
    }

    public function setTotalHarga(float $totalHarga): void
    {
        $this->totalHarga = $totalHarga;
    }
}
