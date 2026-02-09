<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'username', 'password', 'nombre', 'email', 'activo', 'id_rol'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $timestamps = false;

   
    protected $casts = [
        'activo' => 'boolean',
    ];
}
