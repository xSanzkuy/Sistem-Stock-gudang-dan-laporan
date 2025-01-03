<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $table = 'pembelian';
    protected $fillable = ['no_faktur', 'tanggal', 'supplier', 'subtotal', 'ppn', 'total_harga'];

    public function details()
    {
        return $this->hasMany(ItemPembelian::class, 'pembelian_id');
    }
}
