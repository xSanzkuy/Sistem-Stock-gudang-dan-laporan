<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPenjualan extends Model
{
    protected $table = 'item_penjualan';
    protected $fillable = ['penjualan_id', 'produk_id', 'qty', 'harga', 'diskon', 'jumlah'];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function pembelian()
    {
        // Asumsi bahwa setiap produk bisa memiliki banyak pembelian (history pembelian)
        return $this->belongsToMany(Pembelian::class, 'item_pembelian', 'item_penjualan_id', 'pembelian_id');
    }
}

