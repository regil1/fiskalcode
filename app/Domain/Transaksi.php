<?php

namespace App\Domain;

abstract class Transaksi
{
    protected float $jumlah;
    protected string $tanggal;
    protected string $deskripsi;

    public function __construct(float $jumlah, string $tanggal, string $deskripsi)
    {
        $this->setJumlah($jumlah);
        $this->tanggal = $tanggal;
        $this->deskripsi = $deskripsi;
    }

    public function setJumlah(float $jumlah): void
    {
        if ($jumlah <= 0) {
            throw new \InvalidArgumentException("Nominal transaksi harus lebih dari 0!");
        }
        $this->jumlah = $jumlah;
    }

    public function getJumlah(): float
    {
        return $this->jumlah;
    }

    abstract public function prosesTransaksi(float $saldoSaatIni): float;
}
