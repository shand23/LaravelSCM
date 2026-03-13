<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenggunaanMaterial extends Model
{
    use HasFactory;

    // Nama tabel sesuai di database (tanpa akhiran 's')
    protected $table = 'penggunaan_material';

    // Pengaturan Primary Key karena menggunakan VARCHAR(20)
    protected $primaryKey = 'id_penggunaan';
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'id_penggunaan',
        'id_permintaan',
        'id_proyek',
        'id_user_pelaksana',
        'tanggal_laporan',
        'area_pekerjaan',
        'keterangan_umum',
        'created_at',
        'updated_at'
    ];

    /**
     * Relasi ke Header Permintaan Proyek
     */
    public function permintaan()
    {
        return $this->belongsTo(PermintaanProyek::class, 'id_permintaan', 'id_permintaan');
    }

    /**
     * Relasi ke Master Proyek
     */
    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'id_proyek', 'id_proyek');
    }

    /**
     * Relasi ke User (Pelaksana)
     */
    public function pelaksana()
    {
        return $this->belongsTo(User::class, 'id_user_pelaksana', 'id_user');
    }

    /**
     * Relasi ke Detail Penggunaan (Satu laporan punya banyak item material)
     */
    public function detailPenggunaan()
    {
        return $this->hasMany(DetailPenggunaanMaterial::class, 'id_penggunaan', 'id_penggunaan');
    }
}