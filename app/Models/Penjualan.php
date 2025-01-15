<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $fillable = [
        'no_faktur', 'tanggal', 'penerima', 'alamat', 'jumlah_barang', 'subtotal', 'ppn', 'total_harga', 'keuntungan'
    ];
    

    // Relasi dengan ItemPenjualan
    public function items()
    {
        return $this->hasMany(ItemPenjualan::class, 'penjualan_id');
    }
    
    // Relasi dengan Piutang
    public function piutang()
    {
        return $this->hasOne(Piutang::class, 'no_faktur', 'no_faktur');
    }

    // Menambahkan kembali details() seperti yang sudah ada
    public function details()
    {
        return $this->hasMany(ItemPenjualan::class, 'penjualan_id');
    }

    /**
     * Hitung keuntungan untuk penjualan berdasarkan item
     */
    public function hitungKeuntungan()
    {
        $keuntungan = 0;

        // Menghitung keuntungan berdasarkan item penjualan
        foreach ($this->items as $item) {
            $keuntungan += ($item->harga_jual - $item->harga_beli) * $item->qty;
        }

        return $keuntungan;
    }

    /**
     * Menyimpan penjualan dan mencatat keuntungan di laporan
     */
    public static function simpanPenjualan($data)
    {
        // Buat penjualan
        $penjualan = self::create($data);

        // Hitung keuntungan penjualan
        $keuntungan = $penjualan->hitungKeuntungan();

        // Update keuntungan di tabel penjualan
        $penjualan->keuntungan = $keuntungan;
        $penjualan->save();

        // Update laporan keuntungan untuk periode ini
        self::updateLaporanKeuntungan($penjualan);

        return $penjualan;
    }

    /**
     * Update Laporan Keuntungan
     * Menambahkan atau memperbarui laporan keuntungan berdasarkan penjualan
     */
    public static function updateLaporanKeuntungan(Penjualan $penjualan)
    {
        // Cari laporan keuntungan untuk hari ini
        $laporan = LaporanKeuntungan::whereDate('tanggal', Carbon::today())->first();

        if (!$laporan) {
            // Jika laporan belum ada, buat laporan baru
            $laporan = LaporanKeuntungan::create([
                'tanggal' => Carbon::today(),
                'total_transaksi' => 1,
                'total_modal' => $penjualan->items->sum(fn($item) => $item->harga_beli * $item->qty),
                'total_penjualan' => $penjualan->items->sum(fn($item) => $item->harga_jual * $item->qty),
                'total_keuntungan' => $penjualan->keuntungan,
            ]);
        } else {
            // Jika laporan sudah ada, update data laporan
            $laporan->total_transaksi += 1;
            $laporan->total_modal += $penjualan->items->sum(fn($item) => $item->harga_beli * $item->qty);
            $laporan->total_penjualan += $penjualan->items->sum(fn($item) => $item->harga_jual * $item->qty);
            $laporan->total_keuntungan += $penjualan->keuntungan;
            $laporan->save();
        }

        // Simpan detail laporan keuntungan
        self::simpanDetailLaporanKeuntungan($laporan, $penjualan);
    }

    /**
     * Menyimpan detail laporan keuntungan per item
     */
    public static function simpanDetailLaporanKeuntungan(LaporanKeuntungan $laporan, Penjualan $penjualan)
    {
        foreach ($penjualan->items as $item) {
            LaporanKeuntunganDetail::create([
                'laporan_keuntungan_id' => $laporan->id,
                'nama_produk' => $item->nama_produk,
                'qty' => $item->qty,
                'harga_beli' => $item->harga_beli,
                'harga_jual' => $item->harga_jual,
                'keuntungan' => ($item->harga_jual - $item->harga_beli) * $item->qty,
            ]);
        }
    }
}
