<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenTrabajo extends Model
{
    protected $table = 'ordenes_trabajo';

    protected $fillable = [
        'codigo',
        'descripcion',
        'estado',
        'fecha_apertura',
    ];

    public function lineas()
    {
        return $this->hasMany(OrdenTrabajoArticulo::class, 'id_orden_trabajo');
    }

    public function articulos()
    {
        return $this->belongsToMany(
            Articulo::class,
            'orden_trabajo_articulos',
            'id_orden_trabajo',
            'id_articulo'
        )->withPivot('cantidad')
         ->withTimestamps();
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'id_orden_trabajo');
    }
}
