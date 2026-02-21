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
        Schema::create('detail_kontrak', function (Blueprint $table) {
            $table->string('id_detail_kontrak', 20)->primary();
            $table->string('id_kontrak', 20);
            $table->string('id_material', 20);
            $table->decimal('harga_awal_satuan', 15, 2)->nullable();
            $table->decimal('harga_negosiasi_satuan', 15, 2)->nullable();
            $table->timestamps();
    
            $table->foreign('id_kontrak')
                  ->references('id_kontrak')->on('kontrak')
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
        Schema::dropIfExists('detail_kontrak');
    }
};
