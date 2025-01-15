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
        Schema::create('hutang_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hutang_id');
            $table->foreign('hutang_id')->references('id')->on('hutang')->onDelete('cascade');            
            $table->decimal('jumlah', 15, 2);
            $table->decimal('pembayaran', 15, 2)->nullable(); // Tambahkan nullable()
            $table->decimal('kekurangan', 15, 2)->nullable(); // Tambahkan nullable()
            $table->string('status');
            $table->timestamps();
        });
        
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hutang_histories');
    }
};
