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
        Schema::create('kontrak', function (Blueprint $table) {
            $table->string('id_kontrak', 20)->primary();
            $table->string('id_pengajuan', 20);
            $table->string('id_supplier', 20);
            $table->string('id_user_pengadaan', 20);
            $table->string('nomor_kontrak', 100)->nullable();
            $table->string('file_kontrak_path', 255)->nullable();
            $table->date('tanggal_kontrak')->nullable();
            $table->decimal('total_harga_awal', 15, 2)->nullable();
            $table->decimal('total_harga_negosiasi', 15, 2)->nullable();
            $table->decimal('total_diskon', 15, 2)->nullable();
            $table->decimal('total_ongkir', 15, 2)->nullable();
            $table->decimal('total_ppn', 15, 2)->nullable();
            $table->decimal('total_nilai_kontrak', 15, 2)->nullable();
            $table->enum('status_kontrak', ['Draft','Disepakati','Batal'])->default('Draft');
            $table->timestamps();
    
            $table->foreign('id_pengajuan')->references('id_pengajuan')->on('pengajuan_material');
            $table->foreign('id_supplier')->references('id_supplier')->on('supplier');
            $table->foreign('id_user_pengadaan')->references('id_user')->on('users');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontrak');
    }
};
