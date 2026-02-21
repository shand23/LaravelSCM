<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'id_user',
        'nama_lengkap',
        'email',
        'password',
        'role',
        'jabatan',
        'status_user',
    ];

    protected $hidden = [
        'password',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!$user->id_user) {
                $last = DB::table('users')
                    ->orderBy('id_user', 'desc')
                    ->first();

                if ($last) {
                    $number = intval(substr($last->id_user, 3)) + 1;
                    $user->id_user = 'USR' . str_pad($number, 4, '0', STR_PAD_LEFT);
                } else {
                    $user->id_user = 'USR0001';
                }
            }
        });
    }
}
