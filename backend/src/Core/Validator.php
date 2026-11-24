<?php
namespace App\Core;

/**
 * Validasi input sisi server (FR-20, NFR-11).
 * Setiap aturan menambahkan pesan kesalahan berbahasa Indonesia.
 */
final class Validator
{
    private array $errors = [];

    public function __construct(private array $data) {}

    public function value(string $key): string
    {
        $v = $this->data[$key] ?? '';
        return is_string($v) ? trim($v) : '';
    }

    public function required(string $key, string $label): self
    {
        if ($this->value($key) === '') {
            $this->add($key, "{$label} wajib diisi.");
        }
        return $this;
    }

    public function maxLength(string $key, int $max, string $label): self
    {
        if (mb_strlen($this->value($key)) > $max) {
            $this->add($key, "{$label} maksimal {$max} karakter.");
        }
        return $this;
    }

    /** Bilangan bulat >= $min (tanpa desimal, tanpa huruf). */
    public function integer(string $key, string $label, int $min = 0, int $max = 2147483647): self
    {
        $v = $this->value($key);
        if ($v === '') {
            return $this;
        }
        if (!preg_match('/^-?\d+$/', $v)) {
            $this->add($key, "{$label} harus berupa bilangan bulat.");
        } elseif ((int) $v < $min) {
            $this->add($key, $min === 0 ? "{$label} tidak boleh negatif." : "{$label} minimal {$min}.");
        } elseif ((int) $v > $max) {
            $this->add($key, "{$label} terlalu besar.");
        }
        return $this;
    }

    /** Angka desimal (titik atau koma sebagai pemisah desimal). */
    public function number(string $key, string $label, float $min = 0, bool $exclusiveMin = false, float $max = PHP_FLOAT_MAX): self
    {
        $v = $this->value($key);
        if ($v === '') {
            return $this;
        }
        $n = self::toNumber($v);
        if ($n === null) {
            $this->add($key, "{$label} harus berupa angka.");
        } elseif ($exclusiveMin ? $n <= $min : $n < $min) {
            $this->add($key, $exclusiveMin ? "{$label} harus lebih dari {$min}." : "{$label} tidak boleh negatif.");
        } elseif ($n > $max) {
            $this->add($key, "{$label} terlalu besar.");
        }
        return $this;
    }

    public function in(string $key, array $allowed, string $label): self
    {
        $v = $this->value($key);
        if ($v !== '' && !in_array($v, $allowed, true)) {
            $this->add($key, "{$label} tidak valid.");
        }
        return $this;
    }

    public function date(string $key, string $label, bool $notFuture = false): self
    {
        $v = $this->value($key);
        if ($v === '') {
            return $this;
        }
        $d = \DateTime::createFromFormat('!Y-m-d', $v);
        if (!$d || $d->format('Y-m-d') !== $v) {
            $this->add($key, "{$label} tidak valid.");
        } elseif ($notFuture && $v > date('Y-m-d')) {
            $this->add($key, "{$label} tidak boleh melewati hari ini.");
        }
        return $this;
    }

    public function add(string $key, string $message): self
    {
        $this->errors[$key] ??= $message;
        return $this;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): string
    {
        return (string) (array_values($this->errors)[0] ?? '');
    }

    /** "1.250.000" / "1250000" / "12,5" -> float; null jika bukan angka. */
    public static function toNumber(string $v): ?float
    {
        $v = trim(str_replace(' ', '', $v));
        if (preg_match('/^-?\d{1,3}(\.\d{3})+$/', $v)) {      // format ribuan Indonesia
            $v = str_replace('.', '', $v);
        }
        $v = str_replace(',', '.', $v);
        return is_numeric($v) ? (float) $v : null;
    }
}
