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
        Schema::create('stok_material_proyek', function (Blueprint $table) {
            $table->string('id_stok', 20)->primary();
            $table->string('id_proyek', 20);
            $table->string('id_material', 20);
            $table->string('id_penerimaan', 20)->nullable();
            $table->date('tanggal_terima');
            $table->integer('jumlah_masuk');
            $table->integer('sisa_stok');
            $table->decimal('harga_satuan_batch', 15, 2)->nullable();
            $table->timestamps();
    
            $table->foreign('id_proyek')->references('id_proyek')->on('proyek');
            $table->foreign('id_material')->references('id_material')->on('material');
            $table->foreign('id_penerimaan')->references('id_penerimaan')->on('penerimaan_material');
        });
    
        Schema::table('stok_material_proyek', function (Blueprint $table) {
            $table->index(['id_material','id_proyek','tanggal_terima','sisa_stok'], 'idx_fifo_stok');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_material_proyek');
    }
};
