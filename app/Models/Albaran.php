<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Albaran extends Model
{
    use HasFactory;

    protected $table = 'albaranes';

    protected $fillable = [
        'numero',
        'fecha',
        'id_proveedor',
    ];
}
