<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenTrabajoArticulo extends Model
{
    use HasFactory;

    protected $table = 'orden_trabajo_articulos';

    protected $fillable = [
        'id_orden_trabajo',
        'id_articulo',
        'cantidad',
    ];

    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'id_orden_trabajo');
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class, 'id_articulo');
    }
}
