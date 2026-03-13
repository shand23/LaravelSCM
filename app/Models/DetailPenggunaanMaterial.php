<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenggunaanMaterial extends Model
{
    use HasFactory;

    // Nama tabel sesuai di database (tanpa akhiran 's')
    protected $table = 'detail_penggunaan_material';

    // Pengaturan Primary Key karena menggunakan VARCHAR(20)
    protected $primaryKey = 'id_detail_penggunaan';
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'id_detail_penggunaan',
        'id_penggunaan',
        'id_material',
        'jumlah_terpasang_riil',
        'jumlah_rusak_lapangan',
        'jumlah_sisa_material',
        'catatan_khusus',
        'created_at',
        'updated_at'
    ];

    /**
     * Relasi balik ke Header Penggunaan Material
     */
    public function penggunaan()
    {
        return $this->belongsTo(PenggunaanMaterial::class, 'id_penggunaan', 'id_penggunaan');
    }

    /**
     * Relasi ke Master Material
     */
    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }
}