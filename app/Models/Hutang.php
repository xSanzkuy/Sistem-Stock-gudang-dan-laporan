<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hutang extends Model
{
    protected $table = 'hutang';
    protected $fillable = ['nama_supplier', 'tanggal', 'no_faktur', 'jumlah', 'jatuh_tempo', 'status'];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'no_faktur', 'no_faktur');
    }
}
