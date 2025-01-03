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
        Schema::create('piutang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelanggan');
            $table->date('tanggal');
            $table->string('no_faktur');
            $table->decimal('jumlah', 10, 2);
            $table->decimal('pembayaran', 10, 2)->default(0);
            $table->enum('status', ['Belum Lunas', 'Lunas'])->default('Belum Lunas');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piutang');
    }
};
