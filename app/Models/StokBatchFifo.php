<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StokBatchFifo extends Model
{
    use HasFactory;

    protected $table = 'stok_batch_fifo';
    protected $primaryKey = 'id_stok';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_stok', 'id_material', 'id_penerimaan', 'tanggal_masuk',
        'jumlah_awal', 'sisa_stok', 'lokasi_rak', 'status_stok'
    ];

    // ==========================================
    // LOGIC AUTO-GENERATE ID (STK0001)
    // ==========================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_stok)) {
                $last = DB::table('stok_batch_fifo')->orderBy('id_stok', 'desc')->first();
                if ($last) {
                    $number = intval(substr($last->id_stok, 3)) + 1;
                    $model->id_stok = 'STK' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_stok = 'STK0001';
                }
            }
        });
    }

    public function material() { return $this->belongsTo(Material::class, 'id_material', 'id_material'); }
    public function penerimaan() { return $this->belongsTo(PenerimaanMaterial::class, 'id_penerimaan', 'id_penerimaan'); }
}