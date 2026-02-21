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
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->string('id_pengiriman', 20)->primary();
            $table->string('id_pesanan', 20);
            $table->string('id_user_pengirim', 20);
            $table->date('tanggal_kirim');
            $table->enum('status_pengiriman', ['Dikirim','Pending','Selesai'])->default('Dikirim');
            $table->enum('kategori_pengiriman', ['Baru','Tukar Guling/Ganti'])->default('Baru');
            $table->timestamps();
    
            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan');
            $table->foreign('id_user_pengirim')->references('id_user')->on('users');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengiriman');
    }
};
