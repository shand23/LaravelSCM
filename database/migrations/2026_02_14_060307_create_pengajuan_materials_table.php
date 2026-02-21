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
        Schema::create('pengajuan_material', function (Blueprint $table) {
            $table->string('id_pengajuan', 20)->primary();
            $table->string('id_proyek', 20);
            $table->string('id_user_pengaju', 20);
            $table->date('tanggal_pengajuan');
            $table->enum('status_pengajuan', ['Menunggu','Disetujui','Ditolak'])->default('Menunggu');
            $table->text('catatan_pengajuan')->nullable();
            $table->timestamps();
    
            $table->foreign('id_proyek')->references('id_proyek')->on('proyek');
            $table->foreign('id_user_pengaju')->references('id_user')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_material');
    }
};
