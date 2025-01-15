<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hutang extends Model
{
    protected $table = 'hutang';

    protected $fillable = ['nama_supplier', 'tanggal', 'no_faktur', 'jumlah', 'pembayaran', 'kekurangan', 'jatuh_tempo', 'status'];


    protected $casts = [
        'jumlah' => 'float',
        'pembayaran' => 'float',
        'kekurangan' => 'float',
        'tanggal' => 'date',
        'jatuh_tempo' => 'date',
    ];

    public $timestamps = false;

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'no_faktur', 'no_faktur');
    }

    public function hutangHistories()
    {
        return $this->hasMany(HutangHistory::class);
    }
}
