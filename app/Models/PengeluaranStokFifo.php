<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranStokFifo extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit
    protected $table = 'pengeluaran_stok_fifo';

    // Menentukan primary key (bawaan Laravel sudah 'id', jadi opsional, tapi bagus untuk kejelasan)
    protected $primaryKey = 'id';

    // Kolom-kolom yang diizinkan untuk diisi secara massal (mass assignment)
    protected $fillable = [
        'id_permintaan',
        'id_material',
        'id_stok',
        'jumlah_diambil',
    ];

    /**
     * Relasi ke tabel permintaan_proyek
     */
    public function permintaanProyek()
    {
        return $this->belongsTo(PermintaanProyek::class, 'id_permintaan', 'id_permintaan');
    }

    /**
     * Relasi ke tabel material
     */
    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }

    /**
     * Relasi ke tabel stok_batch_fifo
     */
    public function stokBatch()
    {
        return $this->belongsTo(StokBatchFifo::class, 'id_stok', 'id_stok');
    }
}