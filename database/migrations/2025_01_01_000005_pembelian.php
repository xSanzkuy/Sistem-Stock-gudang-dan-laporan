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
    Schema::create('pembelian', function (Blueprint $table) {
        $table->id();
        $table->string('no_faktur')->unique();
        $table->date('tanggal');
        $table->string('supplier')->nullable();
        $table->decimal('subtotal', 20, 2)->default(0);
        $table->decimal('ppn', 20, 2)->default(0);
        $table->decimal('total_harga', 20, 2)->default(0);
        $table->timestamps();
    });
    
}


    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::dropIfExists('pembelian');
}

};
