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
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->string('id_detail_pesanan', 20)->primary();
            $table->string('id_pesanan', 20);
            $table->string('id_material', 20);
            $table->integer('jumlah_pesan');
            $table->timestamps();
    
            $table->foreign('id_pesanan')
                  ->references('id_pesanan')->on('pesanan')
                  ->onDelete('cascade');
    
            $table->foreign('id_material')
                  ->references('id_material')->on('material');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};
