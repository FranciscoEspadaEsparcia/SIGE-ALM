<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;

class ArticuloController extends Controller
{
    public function consultaStock(Request $request)
    {
        $request->validate([
            'id_articulo' => 'required|exists:articulos,id'
        ]);

        $articulo = Articulo::findOrFail($request->id_articulo);

        return response()->json([
            'id_articulo' => $articulo->id,
            'stock_actual' => $articulo->stock_actual
        ]);
    }
}
