<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $fillable = [
        'no_faktur', 'tanggal', 'penerima', 'alamat', 'jumlah_barang', 'subtotal', 'ppn', 'total_harga'
    ];
    

    public function items()
    {
        return $this->hasMany(ItemPenjualan::class, 'penjualan_id');
    }

    public function details()
    {
        return $this->hasMany(ItemPenjualan::class, 'penjualan_id');
    }

    public function piutang()
{
    return $this->hasOne(Piutang::class, 'no_faktur', 'no_faktur');
}

}
