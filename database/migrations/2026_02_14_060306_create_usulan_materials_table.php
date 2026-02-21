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
        Schema::create('usulan_material', function (Blueprint $table) {
            $table->string('id_usulan_material', 20)->primary();
            $table->string('id_user_pengusul', 20);
            $table->string('nama_material', 150);
            $table->string('satuan', 50);
            $table->text('spesifikasi')->nullable();
            $table->enum('status_usulan', ['Menunggu','Disetujui','Ditolak'])->default('Menunggu');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
    
            $table->foreign('id_user_pengusul')
                  ->references('id_user')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usulan_material');
    }
};
