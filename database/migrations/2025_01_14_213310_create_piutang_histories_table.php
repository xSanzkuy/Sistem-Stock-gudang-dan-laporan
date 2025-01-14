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
        Schema::create('piutang_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('piutang_id');
            $table->decimal('jumlah', 15, 2);
            $table->decimal('pembayaran', 15, 2)->nullable();
            $table->decimal('kekurangan', 15, 2);
            $table->string('status');
            $table->timestamps();
    
            $table->foreign('piutang_id')->references('id')->on('piutang')->onDelete('cascade');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piutang_histories');
    }
};
