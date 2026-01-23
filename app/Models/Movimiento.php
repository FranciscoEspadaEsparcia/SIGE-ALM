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

    public function articulo()
{
    return $this->belongsTo(Articulo::class, 'id_articulo');
}

public function usuario()
{
    return $this->belongsTo(Usuario::class, 'id_usuario');
}

public function ordenTrabajo()
{
    return $this->belongsTo(OrdenTrabajo::class, 'id_orden_trabajo');
}

}
