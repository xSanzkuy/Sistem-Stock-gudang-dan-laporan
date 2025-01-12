<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class LaporanKeuntunganExport implements FromArray
{
    protected $laporan;

    public function __construct($laporan)
    {
        $this->laporan = $laporan;
    }

    public function array(): array
    {
        // Konversi data laporan ke array
        return $this->laporan->toArray();
    }
}

