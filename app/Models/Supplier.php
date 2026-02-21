<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    // PENTING: Mendefinisikan nama tabel secara manual karena bentuknya tunggal
    protected $table = 'supplier'; 
    
    protected $primaryKey = 'id_supplier';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_supplier',
        'nama_supplier',
        'alamat',
        'kota',            // Sesuai migration
        'kontak_person',   // Sesuai migration
        'no_telepon',      // Sesuai migration
        'email',
        'status_supplier',
    ];
}