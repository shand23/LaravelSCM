<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenyesuaianStok extends Model
{
    use HasFactory;

    // 1. Tentukan nama tabel secara eksplisit
    protected $table = 'penyesuaian_stok';

    // 2. Pengaturan Primary Key karena tidak menggunakan 'id' dan bukan Auto Increment (Integer)
    protected $primaryKey = 'id_penyesuaian';
    public $incrementing = false;
    protected $keyType = 'string';

    // 3. Kolom yang diizinkan untuk diisi secara massal (Mass Assignment)
    protected $fillable = [
        'id_penyesuaian',
        'id_stok',
        'id_material',
        'id_user',
        'jenis_penyesuaian',
        'jumlah_penyesuaian',
        'keterangan',
        'bukti_foto'
    ];

    // ========================================================
    // DEFINISI RELASI ANTAR TABEL (FOREIGN KEYS)
    // ========================================================

    /**
     * Relasi ke tabel stok_batch_fifo
     * Setiap penyesuaian pasti merujuk pada 1 batch stok tertentu
     */
    public function stokBatch()
    {
        return $this->belongsTo(StokBatchFifo::class, 'id_stok', 'id_stok');
    }

    /**
     * Relasi ke tabel material
     * Setiap penyesuaian pasti terkait dengan 1 jenis material
     */
    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }

    /**
     * Relasi ke tabel users
     * Untuk mengetahui admin/user siapa yang melaporkan penyesuaian ini
     */
    public function user()
    {
        // Sesuaikan 'id_user' dengan nama primary key di tabel/model User Anda
        return $this->belongsTo(User::class, 'id_user', 'id_user'); 
    }
}