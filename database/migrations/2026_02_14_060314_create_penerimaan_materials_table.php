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
        Schema::create('penerimaan_material', function (Blueprint $table) {
            $table->string('id_penerimaan', 20)->primary();
            $table->string('id_pengiriman', 20);
            $table->string('id_user_penerima', 20);
            $table->date('tanggal_terima');
            $table->enum('status_penerimaan', ['Diterima','Return']);
            $table->integer('jumlah_terima')->default(0);
            $table->integer('jumlah_return')->default(0);
            $table->text('alasan_return')->nullable();
            $table->string('foto_bukti_terima', 255)->nullable();
            $table->timestamps();
    
            $table->foreign('id_pengiriman')->references('id_pengiriman')->on('pengiriman');
            $table->foreign('id_user_penerima')->references('id_user')->on('users');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaan_material');
    }
};
