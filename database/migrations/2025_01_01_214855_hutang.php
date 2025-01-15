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
        Schema::create('hutang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_supplier');
            $table->date('tanggal');
            $table->string('no_faktur');
            $table->bigInteger('jumlah')->unsigned();
            $table->decimal('pembayaran', 15, 2)->nullable(); // Kolom pembayaran bisa kosong
            $table->decimal('kekurangan', 15, 2)->default(0);
            $table->date('jatuh_tempo')->nullable();
            $table->enum('status', ['Belum Lunas', 'Lunas'])->default('Belum Lunas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hutang');
    }
};
