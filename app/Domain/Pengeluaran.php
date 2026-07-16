<?php

namespace App\Domain;

class Pengeluaran extends Transaksi
{
    private string $tingkatUrgensi;

    public function __construct(float $jumlah, string $tanggal, string $deskripsi, string $tingkatUrgensi)
    {
        parent::__construct($jumlah, $tanggal, $deskripsi);
        $this->tingkatUrgensi = $tingkatUrgensi;
    }

    public function prosesTransaksi(float $saldoSaatIni): float
    {
        $saldoBaru = $saldoSaatIni - $this->getJumlah();
        
        if ($saldoBaru < 0) {
            throw new \Exception("Saldo tidak mencukupi untuk pengeluaran ini!");
        }
        
        return $saldoBaru;
    }
}
