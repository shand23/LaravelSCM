<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailPenerimaan extends Model
{
    use HasFactory;

    protected $table = 'detail_penerimaan';
    protected $primaryKey = 'id_detail_terima';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_detail_terima', 'id_penerimaan', 'id_detail_kontrak', 'id_material',
        'jumlah_bagus', 'jumlah_rusak', 'alasan_return', 'foto_bukti_rusak'
    ];

    // ==========================================
    // LOGIC AUTO-GENERATE ID (DTR0001)
    // ==========================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_detail_terima)) {
                $last = DB::table('detail_penerimaan')->orderBy('id_detail_terima', 'desc')->first();
                if ($last) {
                    $number = intval(substr($last->id_detail_terima, 3)) + 1;
                    $model->id_detail_terima = 'DTR' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_detail_terima = 'DTR0001';
                }
            }
        });
    }

    public function penerimaan() { return $this->belongsTo(PenerimaanMaterial::class, 'id_penerimaan', 'id_penerimaan'); }
    public function detailKontrak() { return $this->belongsTo(DetailKontrak::class, 'id_detail_kontrak', 'id_detail_kontrak'); }
    public function material() { return $this->belongsTo(Material::class, 'id_material', 'id_material'); }
}