<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterLokasiRak extends Model
{
    use HasFactory;

    protected $table = 'master_lokasi_rak';
    protected $primaryKey = 'id_lokasi';
    public $incrementing = false; 
    protected $keyType = 'string'; 

    protected $fillable = [
        'id_lokasi',
        'nama_lokasi',
        'AREA',
        'keterangan',
    ];

    // LOGIC AUTO-GENERATE ID (LOC0001)
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id_lokasi) {
                $last = DB::table('master_lokasi_rak')
                    ->orderBy('id_lokasi', 'desc')
                    ->first();

                if ($last) {
                    // Ambil angka dari string (contoh: LOC0001 -> 0001)
                    $number = intval(substr($last->id_lokasi, 3)) + 1;
                    $model->id_lokasi = 'LOC' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_lokasi = 'LOC0001';
                }
            }
        });
    }

    public function stokBatchFifo()
    {
        return $this->hasMany(StokBatchFifo::class, 'id_lokasi', 'id_lokasi');
    }
}