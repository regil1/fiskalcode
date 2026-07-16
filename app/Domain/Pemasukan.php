<?php

namespace App\Domain;

class Pemasukan extends Transaksi
{
    private string $sumberDana;

    public function __construct(float $jumlah, string $tanggal, string $deskripsi, string $sumberDana)
    {
        parent::__construct($jumlah, $tanggal, $deskripsi);
        $this->sumberDana = $sumberDana;
    }

    public function prosesTransaksi(float $saldoSaatIni): float
    {
        return $saldoSaatIni + $this->getJumlah();
    }
    
    public function getSumberDana(): string
    {
        return $this->sumberDana;
    }
}
