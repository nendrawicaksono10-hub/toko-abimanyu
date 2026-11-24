<?php
namespace App\Entity;

/**
 * Entity detail pesanan (tabel detail_pesanan, Tabel 4.14).
 */
class DetailPesanan
{
    private ?int $idDetail = null;
    private int $idPesanan = 0;
    private ?int $idProduk = null;
    private float $hargaSatuan = 0.0;
    private bool $produkTersedia = true;
    private int $jumlah = 0;
    private float $subtotal = 0.0;
    private ?string $namaProduk = null;
    private ?string $fotoUrl = null;
    private ?string $namaKategori = null;

    /** Subtotal = harga saat transaksi x jumlah (FR-22). */
    public function hitungSubtotal(float $harga): float
    {
        $this->hargaSatuan = round($harga, 2);
        $this->subtotal = round($harga * $this->jumlah, 2);
        return $this->subtotal;
    }

    public function getHargaSatuan(): float
    {
        if ($this->hargaSatuan > 0) {
            return $this->hargaSatuan;
        }
        return $this->jumlah > 0 ? $this->subtotal / $this->jumlah : 0.0;
    }

    /** Bentuk entity dari baris hasil query. */
    public static function fromArray(array $row): self
    {
        $e = new self();
        if (array_key_exists('idDetail', $row)) { $e->idDetail = $row['idDetail'] === null ? null : (int) $row['idDetail']; }
        if (isset($row['idPesanan'])) { $e->idPesanan = (int) $row['idPesanan']; }
        if (array_key_exists('idProduk', $row)) { $e->idProduk = $row['idProduk'] === null ? null : (int) $row['idProduk']; }
        if (isset($row['hargaSatuan'])) { $e->hargaSatuan = (float) $row['hargaSatuan']; }
        if (array_key_exists('produkTersedia', $row)) { $e->produkTersedia = (bool) $row['produkTersedia']; }
        if (isset($row['jumlah'])) { $e->jumlah = (int) $row['jumlah']; }
        if (isset($row['subtotal'])) { $e->subtotal = (float) $row['subtotal']; }
        if (array_key_exists('namaProduk', $row)) { $e->namaProduk = $row['namaProduk'] === null ? null : (string) $row['namaProduk']; }
        if (array_key_exists('fotoUrl', $row)) { $e->fotoUrl = $row['fotoUrl'] === null ? null : (string) $row['fotoUrl']; }
        if (array_key_exists('namaKategori', $row)) { $e->namaKategori = $row['namaKategori'] === null ? null : (string) $row['namaKategori']; }
        return $e;
    }

    public function toArray(): array
    {
        return [
            'idDetail' => $this->idDetail,
            'idPesanan' => $this->idPesanan,
            'idProduk' => $this->idProduk,
            'jumlah' => $this->jumlah,
            'subtotal' => $this->subtotal,
            'hargaSatuan' => $this->getHargaSatuan(),
            'namaProduk' => $this->namaProduk,
            'fotoUrl' => $this->fotoUrl,
            'namaKategori' => $this->namaKategori,
        ];
    }

    public function getIdDetail(): ?int
    {
        return $this->idDetail;
    }

    public function setIdDetail(?int $idDetail): void
    {
        $this->idDetail = $idDetail;
    }

    public function getIdPesanan(): int
    {
        return $this->idPesanan;
    }

    public function setIdPesanan(int $idPesanan): void
    {
        $this->idPesanan = $idPesanan;
    }

    /** Null bila produk sudah dihapus dari katalog (riwayat tetap tersimpan). */
    public function getIdProduk(): ?int
    {
        return $this->idProduk;
    }

    /** False bila produk asal sudah dihapus admin. */
    public function isProdukTersedia(): bool
    {
        return $this->produkTersedia && $this->idProduk !== null;
    }

    public function setIdProduk(?int $idProduk): void
    {
        $this->idProduk = $idProduk;
    }

    public function getJumlah(): int
    {
        return $this->jumlah;
    }

    public function setJumlah(int $jumlah): void
    {
        $this->jumlah = $jumlah;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function setSubtotal(float $subtotal): void
    {
        $this->subtotal = $subtotal;
    }

    public function getNamaProduk(): ?string
    {
        return $this->namaProduk;
    }

    public function setNamaProduk(?string $namaProduk): void
    {
        $this->namaProduk = $namaProduk;
    }

    public function getFotoUrl(): ?string
    {
        return $this->fotoUrl;
    }

    public function setFotoUrl(?string $fotoUrl): void
    {
        $this->fotoUrl = $fotoUrl;
    }

    public function getNamaKategori(): ?string
    {
        return $this->namaKategori;
    }

    public function setNamaKategori(?string $namaKategori): void
    {
        $this->namaKategori = $namaKategori;
    }
}
