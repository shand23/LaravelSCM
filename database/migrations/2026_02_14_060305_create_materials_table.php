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
        Schema::create('material', function (Blueprint $table) {
            $table->string('id_material', 20)->primary();
            $table->string('id_kategori_material', 20);
            $table->string('nama_material', 150);
            $table->string('satuan', 50);
            $table->text('spesifikasi')->nullable();
            $table->string('standar_kualitas', 100)->nullable();
            $table->enum('status_material', ['Aktif','Nonaktif'])->default('Aktif');
            $table->timestamps();
    
            $table->foreign('id_kategori_material')
                  ->references('id_kategori_material')
                  ->on('kategori_material');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material');
    }
};
