<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Movimiento;
use App\Models\OrdenTrabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimientoController extends Controller
{
    /**
     * POST /api/movimientos/entrada
     * (Si ya lo tienes funcionando, puedes mantener tu lógica.
     *  Aquí lo dejo por compatibilidad mínima.)
     */
    public function registrarEntrada(Request $request)
    {
        $data = $request->validate([
            'id_articulo' => ['required', 'integer', 'exists:articulos,id'],
            'id_usuario' => ['required', 'integer', 'exists:usuarios,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'id_albaran' => ['nullable', 'integer', 'exists:albaranes,id'],
        ]);

        return DB::transaction(function () use ($data) {
            $articulo = Articulo::query()->lockForUpdate()->findOrFail($data['id_articulo']);

            $articulo->stock_actual += (int)$data['cantidad'];
            $articulo->save();

            $mov = Movimiento::create([
                'tipo' => 'ENTRADA',
                'cantidad' => (int)$data['cantidad'],
                'fecha_hora' => now(),
                'id_articulo' => $articulo->id,
                'id_usuario' => (int)$data['id_usuario'],
                'id_orden_trabajo' => null,
                'id_albaran' => $data['id_albaran'] ?? null,
            ]);

            return response()->json([
                'message' => 'Entrada registrada correctamente.',
                'movimiento' => $mov,
                'stock_actual' => $articulo->stock_actual,
            ], 201);
        });
    }

    /**
     * POST /api/movimientos/salida
     * (Si ya lo tienes funcionando, puedes mantener tu lógica.
     *  Aquí lo dejo por compatibilidad mínima.)
     */
    public function registrarSalida(Request $request)
    {
        $data = $request->validate([
            'id_articulo' => ['required', 'integer', 'exists:articulos,id'],
            'id_usuario' => ['required', 'integer', 'exists:usuarios,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'id_orden_trabajo' => ['nullable', 'integer', 'exists:ordenes_trabajo,id'],
        ]);

        return DB::transaction(function () use ($data) {
            $articulo = Articulo::query()->lockForUpdate()->findOrFail($data['id_articulo']);

            $cantidad = (int)$data['cantidad'];
            if ($articulo->stock_actual < $cantidad) {
                return response()->json([
                    'message' => 'Stock insuficiente para registrar salida.',
                    'stock_actual' => $articulo->stock_actual,
                    'cantidad_solicitada' => $cantidad,
                ], 400);
            }

            $articulo->stock_actual -= $cantidad;
            $articulo->save();

            $mov = Movimiento::create([
                'tipo' => 'SALIDA',
                'cantidad' => $cantidad,
                'fecha_hora' => now(),
                'id_articulo' => $articulo->id,
                'id_usuario' => (int)$data['id_usuario'],
                'id_orden_trabajo' => $data['id_orden_trabajo'] ?? null,
                'id_albaran' => null,
            ]);

            return response()->json([
                'message' => 'Salida registrada correctamente.',
                'movimiento' => $mov,
                'stock_actual' => $articulo->stock_actual,
            ], 201);
        });
    }

    /**
     * HU-12: Registrar devolución de material (PARCIAL permitida)
     * POST /api/movimientos/devolucion
     *
     * Body:
     * {
     *   "id_articulo": 1,
     *   "id_usuario": 1,
     *   "id_orden_trabajo": 10,
     *   "cantidad": 2
     * }
     */
    public function registrarDevolucion(Request $request)
    {
        $data = $request->validate([
            'id_articulo' => ['required', 'integer', 'exists:articulos,id'],
            'id_usuario' => ['required', 'integer', 'exists:usuarios,id'],
            'id_orden_trabajo' => ['required', 'integer', 'exists:ordenes_trabajo,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($data) {

            // Bloqueos para consistencia
            $articulo = Articulo::query()->lockForUpdate()->findOrFail($data['id_articulo']);
            $ot = OrdenTrabajo::query()->lockForUpdate()->findOrFail($data['id_orden_trabajo']);

            $cantidad = (int)$data['cantidad'];

            // Regla: devolución asociada a OT (confirmado)
            // Permite parcial: no imponemos que sea igual a la consumida.
            // (Si quieres validar contra líneas OT, se puede añadir luego.)

            // 1) Sumar stock
            $articulo->stock_actual += $cantidad;
            $articulo->save();

            // 2) Crear movimiento DEVOLUCION (fecha_hora obligatorio)
            $mov = Movimiento::create([
                'tipo' => 'DEVOLUCION',
                'cantidad' => $cantidad,
                'fecha_hora' => now(),
                'id_articulo' => $articulo->id,
                'id_usuario' => (int)$data['id_usuario'],
                'id_orden_trabajo' => $ot->id,
                'id_albaran' => null,
            ]);

            return response()->json([
                'message' => 'Devolución registrada correctamente.',
                'movimiento' => $mov,
                'stock_actual' => $articulo->stock_actual,
            ], 201);
        });
    }

    /**
     * HU-13: Consultar historial de movimientos por artículo
     * GET /api/movimientos/historial/{id_articulo}
     *
     * Query params opcionales:
     * - tipo=ENTRADA|SALIDA|AJUSTE|DEVOLUCION
     * - desde=YYYY-MM-DD
     * - hasta=YYYY-MM-DD
     */
    public function historialPorArticulo(Request $request, $id_articulo)
    {
        // Validar que el artículo existe
        Articulo::query()->findOrFail($id_articulo);

        $data = $request->validate([
            'tipo' => ['nullable', 'in:ENTRADA,SALIDA,AJUSTE,DEVOLUCION'],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date'],
        ]);

        $q = Movimiento::query()
            ->with(['usuario', 'ordenTrabajo', 'articulo'])
            ->where('id_articulo', $id_articulo)
            ->orderByDesc('fecha_hora');

        if (!empty($data['tipo'])) {
            $q->where('tipo', $data['tipo']);
        }

        if (!empty($data['desde'])) {
            $q->whereDate('fecha_hora', '>=', $data['desde']);
        }

        if (!empty($data['hasta'])) {
            $q->whereDate('fecha_hora', '<=', $data['hasta']);
        }

        return response()->json($q->paginate(20));
    }
}
