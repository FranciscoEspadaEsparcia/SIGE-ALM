<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
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
}
