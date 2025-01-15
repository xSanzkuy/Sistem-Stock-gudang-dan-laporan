<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HutangHistory extends Model
{
    protected $table = 'hutang_histories';

    protected $fillable = [
        'hutang_id',
        'jumlah',
        'pembayaran',
        'kekurangan',
        'status',
        'created_at',
    ];

    public function hutang()
    {
        return $this->belongsTo(Hutang::class);
    }
}
