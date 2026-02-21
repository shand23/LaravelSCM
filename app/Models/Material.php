<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Tambahkan ini jika perlu factory
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Material extends Model
{
    use HasFactory;

    protected $table = 'material';
    protected $primaryKey = 'id_material';
    public $incrementing = false; // Penting: Matikan auto-increment integer
    protected $keyType = 'string'; // Penting: Tipe data string

    protected $fillable = [
        'id_material',
        'id_kategori_material',
        'nama_material',
        'satuan',
        'spesifikasi',
        'standar_kualitas',
        'status_material',
    ];

    // Relasi ke Kategori
    public function kategori()
    {
        return $this->belongsTo(KategoriMaterial::class, 'id_kategori_material', 'id_kategori_material');
    }

    // ==========================================
    // LOGIC AUTO-GENERATE ID (MAT0001)
    // ==========================================
    protected static function boot()
    {
        parent::boot();

        // Event 'creating' berjalan otomatis SEBELUM data disimpan ke DB
        static::creating(function ($model) {
            // Cek apakah ID sudah ada? Jika belum, buatkan otomatis.
            if (empty($model->id_material)) {
                
                // Ambil data terakhir dari tabel 'material'
                $last = DB::table('material')
                    ->orderBy('id_material', 'desc')
                    ->first();

                if ($last) {
                    // Ambil angka dari string (contoh: MAT0005 -> 5)
                    // substr($str, 3) membuang 3 karakter awal ('MAT')
                    $number = intval(substr($last->id_material, 3)) + 1;
                    
                    // Format ulang menjadi MAT + 4 digit angka (MAT0006)
                    $model->id_material = 'MAT' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    // Jika tabel kosong, mulai dari 1
                    $model->id_material = 'MAT0001';
                }
            }
        });
    }
}