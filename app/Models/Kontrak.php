<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Kontrak extends Model
{
    use HasFactory;

    protected $table = 'kontrak';
    protected $primaryKey = 'id_kontrak';
    public $incrementing = false; 
    protected $keyType = 'string';

    protected $fillable = [
        'id_kontrak', 'id_pesanan', 'id_supplier', 'id_user_pengadaan',
        'nomor_kontrak', 'file_kontrak_path', 'tanggal_kontrak',
        'total_harga_awal', 'total_harga_negosiasi', 'total_diskon',
        'total_ongkir', 'total_ppn', 'total_nilai_kontrak',
        'status_kontrak', 'status_pengiriman'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_kontrak)) {
                $last = DB::table('kontrak')->orderBy('id_kontrak', 'desc')->first();
                if ($last) {
                    $number = intval(substr($last->id_kontrak, 3)) + 1;
                    $model->id_kontrak = 'KON' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_kontrak = 'KON0001';
                }
            }
        });
    }

    public function pesanan() { return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan'); }
    public function supplier() { return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier'); }
    public function detailKontrak() { return $this->hasMany(DetailKontrak::class, 'id_kontrak', 'id_kontrak'); }
}