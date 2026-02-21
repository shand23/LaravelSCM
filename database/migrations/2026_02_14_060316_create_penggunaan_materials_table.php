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
        Schema::create('penggunaan_material', function (Blueprint $table) {
            $table->string('id_penggunaan', 20)->primary();
            $table->string('id_stok', 20);
            $table->string('id_proyek', 20);
            $table->date('tanggal_penggunaan');
            $table->integer('jumlah_digunakan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
    
            $table->foreign('id_stok')->references('id_stok')->on('stok_material_proyek');
            $table->foreign('id_proyek')->references('id_proyek')->on('proyek');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggunaan_material');
    }
};
