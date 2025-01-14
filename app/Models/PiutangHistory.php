<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PiutangHistory extends Model
{
    protected $table = 'piutang_histories';
    protected $fillable = ['piutang_id', 'jumlah', 'pembayaran', 'kekurangan', 'status'];

    public function piutang()
    {
        return $this->belongsTo(Piutang::class);
    }
}
