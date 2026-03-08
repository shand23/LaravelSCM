<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';
    protected $primaryKey = 'id_supplier';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_supplier',
        'nama_supplier',
        'kontak_person',
        'no_telepon',
        'email',
        'alamat',
        'status_supplier',
    ];

    // ==========================================
    // LOGIC AUTO-GENERATE ID (SUP0001)
    // ==========================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_supplier)) {
                $last = DB::table('supplier')
                    ->orderBy('id_supplier', 'desc')
                    ->first();

                if ($last) {
                    // Ambil angka dari string (contoh: SUP0005 -> 5)
                    $number = intval(substr($last->id_supplier, 3)) + 1;
                    $model->id_supplier = 'SUP' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $model->id_supplier = 'SUP0001';
                }
            }
        });
    }
}