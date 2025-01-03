<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Piutang extends Model
{
    protected $table = 'piutang';
    protected $fillable = ['nama_pelanggan', 'tanggal', 'no_faktur', 'jumlah', 'pembayaran', 'status'];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'no_faktur', 'no_faktur');
    }
}

