<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPembelian extends Model
{
    protected $table = 'item_pembelian';
    protected $fillable = ['pembelian_id', 'produk_id', 'qty', 'harga', 'diskon', 'jumlah'];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
