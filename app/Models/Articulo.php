<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Articulo extends Model
{
    use HasFactory;

    protected $table = 'articulos';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'stock_actual',
        'stock_minimo',
        'activo',
        'id_categoria',
        'id_proveedor_preferente'
    ];

    public function movimientos()
{
    return $this->hasMany(Movimiento::class, 'id_articulo');
}

public function categoria()
{
    return $this->belongsTo(Categoria::class, 'id_categoria');
}

public function proveedorPreferente()
{
    return $this->belongsTo(Proveedor::class, 'id_proveedor_preferente');
}

public function ordenesTrabajo()
{
    return $this->belongsToMany(OrdenTrabajo::class, 'orden_trabajo_articulos', 'id_articulo', 'id_orden_trabajo')
        ->withPivot('cantidad')
        ->withTimestamps();
}

}
