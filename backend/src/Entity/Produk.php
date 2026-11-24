<?php
namespace App\Entity;

/**
 * Entity produk (tabel produk, Tabel 4.12).
 */
class Produk
{
    private ?int $idProduk = null;
    private int $idKategori = 0;
    private string $nama = '';
    private string $ruangan = '';
    private string $gaya = '';
    private string $material = '';
    private string $ukuran = '';
    private float $panjang = 0.0;
    private float $lebar = 0.0;
    private float $tinggi = 0.0;
    private float $harga = 0.0;
    private int $stok = 0;
    private ?string $deskripsi = null;
    private ?string $fotoUrl = null;
    private bool $aktif = true;
    private ?string $namaKategori = null;

    /** Ringkasan detail produk (dimensi P x L x T cm). */
    public function getDetail(): array
    {
        return $this->toArray() + ['dimensi' => $this->getDimensi()];
    }

    public function getDimensi(): string
    {
        $f = fn(float $n) => rtrim(rtrim(number_format($n, 2, ',', ''), '0'), ',');
        return $f($this->panjang) . ' x ' . $f($this->lebar) . ' x ' . $f($this->tinggi) . ' cm';
    }

    public function isTersedia(): bool
    {
        return $this->aktif && $this->stok > 0;
    }

    /** Bentuk entity dari baris hasil query. */
    public static function fromArray(array $row): self
    {
        $e = new self();
        if (array_key_exists('idProduk', $row)) { $e->idProduk = $row['idProduk'] === null ? null : (int) $row['idProduk']; }
        if (isset($row['idKategori'])) { $e->idKategori = (int) $row['idKategori']; }
        if (isset($row['nama'])) { $e->nama = (string) $row['nama']; }
        if (isset($row['ruangan'])) { $e->ruangan = (string) $row['ruangan']; }
        if (isset($row['gaya'])) { $e->gaya = (string) $row['gaya']; }
        if (isset($row['material'])) { $e->material = (string) $row['material']; }
        if (isset($row['ukuran'])) { $e->ukuran = (string) $row['ukuran']; }
        if (isset($row['panjang'])) { $e->panjang = (float) $row['panjang']; }
        if (isset($row['lebar'])) { $e->lebar = (float) $row['lebar']; }
        if (isset($row['tinggi'])) { $e->tinggi = (float) $row['tinggi']; }
        if (isset($row['harga'])) { $e->harga = (float) $row['harga']; }
        if (isset($row['stok'])) { $e->stok = (int) $row['stok']; }
        if (array_key_exists('deskripsi', $row)) { $e->deskripsi = $row['deskripsi'] === null ? null : (string) $row['deskripsi']; }
        if (array_key_exists('fotoUrl', $row)) { $e->fotoUrl = $row['fotoUrl'] === null ? null : (string) $row['fotoUrl']; }
        if (isset($row['aktif'])) { $e->aktif = (bool) $row['aktif']; }
        if (array_key_exists('namaKategori', $row)) { $e->namaKategori = $row['namaKategori'] === null ? null : (string) $row['namaKategori']; }
        return $e;
    }

    public function toArray(): array
    {
        return [
            'idProduk' => $this->idProduk,
            'idKategori' => $this->idKategori,
            'nama' => $this->nama,
            'ruangan' => $this->ruangan,
            'gaya' => $this->gaya,
            'material' => $this->material,
            'ukuran' => $this->ukuran,
            'panjang' => $this->panjang,
            'lebar' => $this->lebar,
            'tinggi' => $this->tinggi,
            'harga' => $this->harga,
            'stok' => $this->stok,
            'deskripsi' => $this->deskripsi,
            'fotoUrl' => $this->fotoUrl,
            'aktif' => $this->aktif,
            'namaKategori' => $this->namaKategori,
        ];
    }

    public function getIdProduk(): ?int
    {
        return $this->idProduk;
    }

    public function setIdProduk(?int $idProduk): void
    {
        $this->idProduk = $idProduk;
    }

    public function getIdKategori(): int
    {
        return $this->idKategori;
    }

    public function setIdKategori(int $idKategori): void
    {
        $this->idKategori = $idKategori;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function setNama(string $nama): void
    {
        $this->nama = $nama;
    }

    public function getRuangan(): string
    {
        return $this->ruangan;
    }

    public function setRuangan(string $ruangan): void
    {
        $this->ruangan = $ruangan;
    }

    public function getGaya(): string
    {
        return $this->gaya;
    }

    public function setGaya(string $gaya): void
    {
        $this->gaya = $gaya;
    }

    public function getMaterial(): string
    {
        return $this->material;
    }

    public function setMaterial(string $material): void
    {
        $this->material = $material;
    }

    public function getUkuran(): string
    {
        return $this->ukuran;
    }

    public function setUkuran(string $ukuran): void
    {
        $this->ukuran = $ukuran;
    }

    public function getPanjang(): float
    {
        return $this->panjang;
    }

    public function setPanjang(float $panjang): void
    {
        $this->panjang = $panjang;
    }

    public function getLebar(): float
    {
        return $this->lebar;
    }

    public function setLebar(float $lebar): void
    {
        $this->lebar = $lebar;
    }

    public function getTinggi(): float
    {
        return $this->tinggi;
    }

    public function setTinggi(float $tinggi): void
    {
        $this->tinggi = $tinggi;
    }

    public function getHarga(): float
    {
        return $this->harga;
    }

    public function setHarga(float $harga): void
    {
        $this->harga = $harga;
    }

    public function getStok(): int
    {
        return $this->stok;
    }

    public function setStok(int $stok): void
    {
        $this->stok = $stok;
    }

    public function getDeskripsi(): ?string
    {
        return $this->deskripsi;
    }

    public function setDeskripsi(?string $deskripsi): void
    {
        $this->deskripsi = $deskripsi;
    }

    public function getFotoUrl(): ?string
    {
        return $this->fotoUrl;
    }

    public function setFotoUrl(?string $fotoUrl): void
    {
        $this->fotoUrl = $fotoUrl;
    }

    public function getAktif(): bool
    {
        return $this->aktif;
    }

    public function setAktif(bool $aktif): void
    {
        $this->aktif = $aktif;
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
