<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKeuntunganDetail extends Model
{
    use HasFactory;

    protected $table = 'laporan_keuntungan_detail';

    protected $fillable = [
        'laporan_keuntungan_id',
        'produk_id',
        'nama_produk',
        'qty',
        'harga_beli',
        'harga_jual',
        'keuntungan',
        'kategori',
        'deskripsi',
        'kode_produk',
    ];

    public function laporan()
    {
        return $this->belongsTo(LaporanKeuntungan::class, 'laporan_keuntungan_id');
    }
    

    public function details()
    {
        return $this->hasMany(LaporanKeuntunganDetail::class, 'laporan_keuntungan_id');
    }
    
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
    
public function laporanKeuntungan()
{
    return $this->belongsTo(LaporanKeuntungan::class, 'laporan_keuntungan_id');
}


}
