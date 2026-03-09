<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailPesanan extends Model
{
    use HasFactory;

    protected $table = 'detail_pesanan';
    protected $primaryKey = 'id_detail_pesanan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_detail_pesanan', 
        'id_pesanan', 
        'id_material', 
        'jumlah_pesan'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_detail_pesanan)) {
                $last = DB::table('detail_pesanan')->orderBy('id_detail_pesanan', 'desc')->first();
                if ($last) {
                    $number = intval(substr($last->id_detail_pesanan, 3)) + 1;
                    $model->id_detail_pesanan = 'DPS' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_detail_pesanan = 'DPS0001';
                }
            }
        });
    }

    // Relasi
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }
}