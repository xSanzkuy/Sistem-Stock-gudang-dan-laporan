<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Piutang extends Model
{
    protected $table = 'piutang';
    protected $fillable = ['nama_pelanggan', 'tanggal', 'no_faktur', 'jumlah', 'pembayaran', 'status', 'kekurangan'];

    // Relasi dengan Penjualan
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'no_faktur', 'no_faktur');
    }

    // Mutator untuk menghitung kekurangan dan memperbarui status
    public function setPembayaranAttribute($value)
    {
        // Set pembayaran
        $this->attributes['pembayaran'] = $value;

        // Hitung kekurangan berdasarkan jumlah dan pembayaran
        $this->kekurangan = $this->jumlah - $this->pembayaran;

        // Tentukan status berdasarkan kekurangan
        $this->status = $this->kekurangan > 0 ? 'Belum Lunas' : 'Lunas';

        $this->save(); // Simpan perubahan
    }

    public function piutangHistories()
{
    return $this->hasMany(PiutangHistory::class);
}

}
