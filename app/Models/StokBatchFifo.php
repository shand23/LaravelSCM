<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokBatchFifo extends Model
{
    use HasFactory;

    // 1. Definisikan Nama Tabel
    protected $table = 'stok_batch_fifo';

    // 2. Konfigurasi Primary Key Custom (String/VARCHAR)
    protected $primaryKey = 'id_stok';
    public $incrementing = false; // Wajib false karena PK bukan Auto Increment (Integer)
    protected $keyType = 'string'; // Wajib string karena PK adalah VARCHAR

    // 3. Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'id_stok',
        'id_material',
        'id_lokasi',
        'id_penerimaan',
        'tanggal_masuk',
        'jumlah_awal',
        'sisa_stok',
        'status_stok',
    ];

    // 4. Casting tipe data otomatis
    protected $casts = [
        'tanggal_masuk' => 'date',
        'jumlah_awal'   => 'integer',
        'sisa_stok'     => 'integer',
    ];

    /* ====================================================
     * RELASI DATABASE (Sesuai Foreign Key)
     * ==================================================== */

    /**
     * Relasi ke tabel material
     * Menghubungkan id_material di tabel ini dengan id_material di master material
     */
    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }

    /**
     * Relasi ke tabel master_lokasi_rak
     * Menghubungkan id_lokasi di tabel ini dengan id_lokasi di master_lokasi_rak
     */
    public function lokasiRak()
    {
        return $this->belongsTo(MasterLokasiRak::class, 'id_lokasi', 'id_lokasi');
    }

    /**
     * Relasi ke tabel penerimaan_material
     * Menghubungkan id_penerimaan di tabel ini dengan riwayat penerimaan
     */
    public function penerimaan()
    {
        return $this->belongsTo(PenerimaanMaterial::class, 'id_penerimaan', 'id_penerimaan');
    }
}