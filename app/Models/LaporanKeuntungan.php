<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKeuntungan extends Model
{
    use HasFactory;

    protected $table = 'laporan_keuntungan';

    protected $fillable = [
        'tanggal',
        'total_transaksi',
        'total_modal',
        'total_penjualan',
        'total_keuntungan',
    ];

    public function detail()
    {
        return $this->hasMany(LaporanKeuntunganDetail::class, 'laporan_keuntungan_id');
    }

    public function details()
    {
        return $this->hasMany(LaporanKeuntunganDetail::class, 'laporan_keuntungan_id');
    }    


}


