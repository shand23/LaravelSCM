<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penugasan_proyek', function (Blueprint $table) {
            $table->string('id_penugasan', 20)->primary();
            $table->string('id_user', 20);
            $table->string('id_proyek', 20);
            $table->string('peran_proyek', 100)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status_penugasan', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('id_proyek')->references('id_proyek')->on('proyek')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan_proyek');
    }
};