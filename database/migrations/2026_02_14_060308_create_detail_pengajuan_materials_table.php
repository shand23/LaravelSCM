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
        Schema::create('detail_pengajuan_material', function (Blueprint $table) {
            $table->string('id_detail_pengajuan', 20)->primary();
            $table->string('id_pengajuan', 20);
            $table->string('id_material', 20);
            $table->integer('jumlah_diajukan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
    
            $table->foreign('id_pengajuan')
                  ->references('id_pengajuan')->on('pengajuan_material')
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
        Schema::dropIfExists('detail_pengajuan_material');
    }
};
