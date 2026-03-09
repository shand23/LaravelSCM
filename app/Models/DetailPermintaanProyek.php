<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailPermintaanProyek extends Model
{
    protected $table = 'detail_permintaan_proyek';
    protected $primaryKey = 'id_detail_permintaan';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_detail_permintaan)) {
                $last = DB::table('detail_permintaan_proyek')
                    ->orderBy('id_detail_permintaan', 'desc')
                    ->first();

                if ($last) {
                    $number = intval(substr($last->id_detail_permintaan, 3)) + 1;
                    $model->id_detail_permintaan = 'DRQ' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_detail_permintaan = 'DRQ0001';
                }
            }
        });
    }

    public function permintaan() {
        return $this->belongsTo(PermintaanProyek::class, 'id_permintaan', 'id_permintaan');
    }

    public function material() {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }
}