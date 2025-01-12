<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporanKeuntunganDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_keuntungan_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_keuntungan_id')
                ->constrained('laporan_keuntungan')
                ->onDelete('cascade'); // Relasi ke laporan_keuntungan
            $table->foreignId('produk_id')
                ->nullable()
                ->constrained('produk')
                ->onDelete('set null'); // Relasi ke produk, nullable jika produk dihapus
            $table->string('nama_produk')->nullable(); // Nama produk (nullable karena ada produk_id)
            $table->integer('qty'); // Jumlah yang terjual
            $table->decimal('harga_beli', 15, 2); // Harga beli (modal per item)
            $table->decimal('harga_jual', 15, 2); // Harga jual per item
            $table->decimal('keuntungan', 15, 2); // Keuntungan per item (harga_jual - harga_beli) * qty

            // Tambahkan kolom detail tambahan
            $table->string('kategori')->nullable(); // Kategori produk
            $table->text('deskripsi')->nullable(); // Deskripsi tambahan
            $table->string('kode_produk')->nullable(); // Tambahkan kode produk untuk identifikasi unik
            
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laporan_keuntungan_detail');
    }
}
