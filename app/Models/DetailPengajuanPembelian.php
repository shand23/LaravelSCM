<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailPengajuanPembelian extends Model
{
    protected $table = 'detail_pengajuan_pembelian';
    protected $primaryKey = 'id_detail_pengajuan';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    // Auto-Generate ID (DPR0001)
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id_detail_pengajuan) {
                $last = DB::table('detail_pengajuan_pembelian')
                    ->orderBy('id_detail_pengajuan', 'desc')
                    ->first();

                if ($last) {
                    // Ambil angka dari string (DPR0005 -> 5)
                    // 'DPR' memiliki panjang 3 karakter, jadi kita potong mulai index ke-3
                    $number = intval(substr($last->id_detail_pengajuan, 3)) + 1;
                    $model->id_detail_pengajuan = 'DPR' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_detail_pengajuan = 'DPR0001';
                }
            }
        });
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }
}