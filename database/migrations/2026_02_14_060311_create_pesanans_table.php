<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->string('id_pesanan', 20)->primary();
            $table->string('id_kontrak', 20);
            $table->date('tanggal_pesanan');
            $table->enum('status_pesanan', ['Menunggu','Pengiriman','Return','Selesai'])->default('Menunggu');
            $table->timestamps();
    
            $table->foreign('id_kontrak')->references('id_kontrak')->on('kontrak');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
