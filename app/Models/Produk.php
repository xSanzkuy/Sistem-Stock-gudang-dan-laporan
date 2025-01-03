<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $fillable = ['kode', 'jenis', 'nama_barang', 'stok', 'harga_beli', 'harga_jual'];

    public function itemPenjualan()
    {
        return $this->hasMany(ItemPenjualan::class, 'produk_id');
    }

    public function itemPembelian()
    {
        return $this->hasMany(ItemPembelian::class, 'produk_id');
    }

    public function reduceStock($qty)
{
    if ($this->stok < $qty) {
        throw new \Exception("Stok tidak mencukupi untuk produk {$this->nama_barang}.");
    }
    $this->stok -= $qty;
    $this->save();
}

public function pembelian()
{
    return $this->hasManyThrough(
        \App\Models\Pembelian::class,
        \App\Models\ItemPembelian::class,
        'produk_id', // Foreign key di ItemPembelian
        'id',        // Foreign key di Pembelian
        'id',        // Local key di Produk
        'pembelian_id' // Local key di ItemPembelian
    );
}


}
