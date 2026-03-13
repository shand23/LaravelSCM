<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterLokasiRak extends Model
{
    use HasFactory;

    // 1. Definisikan Nama Tabel
    protected $table = 'master_lokasi_rak';

    // 2. Konfigurasi Primary Key Custom (String/VARCHAR)
    protected $primaryKey = 'id_lokasi';
    public $incrementing = false; // Wajib false karena PK bukan Auto Increment (Integer)
    protected $keyType = 'string'; // Wajib string karena PK adalah VARCHAR

    // 3. Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'id_lokasi',
        'nama_lokasi',
        'AREA',
        'keterangan',
    ];

    /* ====================================================
     * RELASI DATABASE
     * ==================================================== */

    /**
     * Relasi ke tabel stok_batch_fifo (One to Many)
     * Satu lokasi rak bisa menyimpan banyak batch stok
     */
    public function stokBatchFifo()
    {
        return $this->hasMany(StokBatchFifo::class, 'id_lokasi', 'id_lokasi');
    }
}