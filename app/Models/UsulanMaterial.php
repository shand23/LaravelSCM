<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // WAJIB ditambahkan untuk query DB

class UsulanMaterial extends Model
{
    use HasFactory;

    protected $table = 'usulan_material';

    // Konfigurasi Primary Key
    protected $primaryKey = 'id_usulan_material';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_usulan_material',
        'id_user_pengusul',
        'id_kategori_material',
        'nama_material',
        'satuan',
        'spesifikasi',
        'status_usulan',
        'catatan_admin',
    ];

    // --- LOGIC AUTO-GENERATE ID ---
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Cek apakah ID sudah ada? Jika belum, buatkan otomatis.
            if (empty($model->id_usulan_material)) {
                
                // Ambil data terakhir berdasarkan id_usulan_material
                $last = DB::table('usulan_material')
                    ->orderBy('id_usulan_material', 'desc')
                    ->first();

                if ($last) {
                    // Ambil angka dari string (contoh: REQ0005 -> 5)
                    // substr membuang 3 karakter awal ('REQ')
                    $number = intval(substr($last->id_usulan_material, 3)) + 1;
                    
                    // Format ulang menjadi REQ + 4 digit angka (REQ0006)
                    $model->id_usulan_material = 'REQ' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    // Jika tabel kosong, mulai dari 1
                    $model->id_usulan_material = 'REQ0001';
                }
            }
        });
    }

    // --- RELASI ---
    public function pengusul()
    {
        return $this->belongsTo(User::class, 'id_user_pengusul', 'id_user');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriMaterial::class, 'id_kategori_material', 'id_kategori_material');
    }
}