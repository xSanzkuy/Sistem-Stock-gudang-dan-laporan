<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporanKeuntunganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_keuntungan', function (Blueprint $table) {
            $table->id(); // Kolom primary key dengan auto_increment
            $table->date('tanggal'); // Kolom tanggal
            $table->unsignedInteger('total_transaksi'); // Kolom jumlah total transaksi
            $table->decimal('total_modal', 15, 2)->nullable(); // Kolom total modal
            $table->decimal('total_penjualan', 15, 2)->nullable(); // Kolom total penjualan
            $table->decimal('total_keuntungan', 15, 2)->nullable(); // Kolom total keuntungan
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laporan_keuntungan');
    }
}
