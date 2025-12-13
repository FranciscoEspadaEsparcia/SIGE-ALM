<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Articulo;
use Illuminate\Http\Request;

class MovimientoController extends Controller
{
    // --- ENTRADA ---
    public function registrarEntrada(Request $request)
    {
        $request->validate([
            'id_articulo' => 'required|exists:articulos,id',
            'cantidad'    => 'required|integer|min:1',
            // si no tienes tabla usuarios todavía, deja solo integer:
            // 'id_usuario'  => 'required|integer',
            'id_usuario'  => 'required|integer',
        ]);

        // Crear movimiento
        $movimiento = Movimiento::create([
            'tipo'        => 'ENTRADA',
            'cantidad'    => $request->cantidad,
            'fecha_hora'  => now(),
            'id_articulo' => $request->id_articulo,
            'id_usuario'  => $request->id_usuario,
        ]);

        // Actualizar stock
        $articulo = Articulo::findOrFail($request->id_articulo);
        $articulo->stock_actual += $request->cantidad;
        $articulo->save();

        return response()->json([
            'mensaje'      => 'Entrada registrada correctamente',
            'movimiento'   => $movimiento,
            'stock_actual' => $articulo->stock_actual,
        ], 201);
    }

    // --- SALIDA ---
    public function registrarSalida(Request $request)
    {
        $request->validate([
            'id_articulo' => 'required|exists:articulos,id',
            'cantidad'    => 'required|integer|min:1',
            // igual que arriba, si no tienes tabla usuarios todavía:
            'id_usuario'  => 'required|integer',
            // 'id_usuario'  => 'required|exists:usuarios,id',
        ]);

        $articulo = Articulo::findOrFail($request->id_articulo);

        if ($articulo->stock_actual < $request->cantidad) {
            return response()->json([
                'error' => 'Stock insuficiente para realizar la salida',
            ], 400);
        }

        $movimiento = Movimiento::create([
            'tipo'            => 'SALIDA',
            'cantidad'        => $request->cantidad,
            'fecha_hora'      => now(),
            'id_articulo'     => $request->id_articulo,
            'id_usuario'      => $request->id_usuario,
            'id_orden_trabajo'=> $request->id_orden_trabajo ?? null,
            'id_albaran'      => $request->id_albaran ?? null,
        ]);

        // Actualizar stock
        $articulo->stock_actual -= $request->cantidad;
        $articulo->save();

        return response()->json([
            'mensaje'      => 'Salida registrada correctamente',
            'movimiento'   => $movimiento,
            'stock_actual' => $articulo->stock_actual,
        ]);
    }
}
