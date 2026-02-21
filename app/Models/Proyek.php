<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Proyek extends Model
{
    use HasFactory;

    protected $table = 'proyek';
    protected $primaryKey = 'id_proyek';
    public $incrementing = false; // Karena ID kita String (PRY0001)
    protected $keyType = 'string';

    protected $fillable = [
        'id_proyek',
        'nama_proyek',
        'lokasi_proyek',
        'deskripsi_proyek',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_proyek',
    ];

    // Casting tanggal agar mudah diformat di view (opsional tapi disarankan)
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // ==========================================
    // LOGIC AUTO-GENERATE ID (PRY0001)
    // ==========================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_proyek)) {
                $last = DB::table('proyek')
                    ->orderBy('id_proyek', 'desc')
                    ->first();

                if ($last) {
                    // Ambil angka dari string (PRY0005 -> 5)
                    $number = intval(substr($last->id_proyek, 3)) + 1;
                    $model->id_proyek = 'PRY' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_proyek = 'PRY0001';
                }
            }
        });
    }
}