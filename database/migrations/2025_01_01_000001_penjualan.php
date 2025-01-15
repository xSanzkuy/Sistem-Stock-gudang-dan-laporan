<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('penjualan', function (Blueprint $table) {
        $table->id();
        $table->string('no_faktur')->unique();
        $table->date('tanggal');
        $table->string('penerima')->nullable();
        $table->string('alamat')->nullable();
        $table->integer('jumlah_barang')->default(0);
        $table->decimal('subtotal', 15, 2)->default(0);
        $table->decimal('ppn', 20, 2)->nullable();
        $table->decimal('total_harga', 20, 2)->default(0);
        $table->decimal('keuntungan', 20, 2)->default(0); 
        $table->timestamps();
    });    
}

    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
