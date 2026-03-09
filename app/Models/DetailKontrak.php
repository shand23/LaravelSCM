<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailKontrak extends Model
{
    use HasFactory;

    protected $table = 'detail_kontrak';
    protected $primaryKey = 'id_detail_kontrak';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_detail_kontrak', 'id_kontrak', 'id_material',
        'jumlah_final', 'harga_negosiasi_satuan',
        'jumlah_diterima', 'catatan_penerimaan'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_detail_kontrak)) {
                $last = DB::table('detail_kontrak')->orderBy('id_detail_kontrak', 'desc')->first();
                if ($last) {
                    $number = intval(substr($last->id_detail_kontrak, 3)) + 1;
                    $model->id_detail_kontrak = 'DKO' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_detail_kontrak = 'DKO0001';
                }
            }
        });
    }

    public function material() { return $this->belongsTo(Material::class, 'id_material', 'id_material'); }
}