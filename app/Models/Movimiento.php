<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $table = 'movimientos';

    protected $fillable = [
        'tipo',
        'cantidad',
        'fecha_hora',
        'id_articulo',
        'id_usuario',
        'id_orden_trabajo',
        'id_albaran'
    ];

    public $timestamps = false; 
}
