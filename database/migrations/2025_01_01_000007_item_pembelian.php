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
        Schema::create('item_pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelian')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');
            $table->integer('qty');
            $table->decimal('harga', 20, 2);
            $table->decimal('diskon', 5, 2)->default(0);
            $table->unsignedBigInteger('jumlah');
            $table->timestamps();
        });     
           
    }
    
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::dropIfExists('item_pembelian'); // Pastikan nama tabel yang tepat
}
};
