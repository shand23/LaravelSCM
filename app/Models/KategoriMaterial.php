<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KategoriMaterial extends Model
{
    protected $table = 'kategori_material';
    protected $primaryKey = 'id_kategori_material';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_kategori_material',
        'nama_kategori',
        'deskripsi',
        'status_kategori',
    ];

    // Auto-Generate ID (CAT0001)
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id_kategori_material) {
                $last = DB::table('kategori_material')
                    ->orderBy('id_kategori_material', 'desc')
                    ->first();

                if ($last) {
                    // Ambil angka dari string (contoh: CAT0005 -> 5)
                    $number = intval(substr($last->id_kategori_material, 3)) + 1;
                    $model->id_kategori_material = 'CAT' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_kategori_material = 'CAT0001';
                }
            }
        });
    }
}