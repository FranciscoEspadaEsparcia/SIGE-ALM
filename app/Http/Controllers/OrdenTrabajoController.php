<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\OrdenTrabajo;
use App\Models\OrdenTrabajoArticulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrdenTrabajoController extends Controller
{
    /**
     * HU-21: Consultar Órdenes de Trabajo (listado)
     */
    public function index()
    {
        $ots = OrdenTrabajo::query()
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json($ots);
    }

    /**
     * HU-21: Consultar OT (detalle con líneas)
     */
    public function show($id)
    {
        $ot = OrdenTrabajo::query()
            ->with(['lineas.articulo'])
            ->findOrFail($id);

        return response()->json($ot);
    }

    /**
     * HU-20: Crear Orden de Trabajo (DESCUENTA STOCK AL CREAR)
     *
     * Body esperado:
     * {
     *   "codigo": "OT-0001", (opcional, si no lo envías lo autogeneramos)
     *   "descripcion": "Cambio de luminarias",
     *   "fecha_apertura": "2026-01-03",
     *   "articulos": [
     *      {"id_articulo": 1, "cantidad": 3},
     *      {"id_articulo": 2, "cantidad": 1}
     *   ]
     * }
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => ['nullable', 'string', 'max:50', 'unique:ordenes_trabajo,codigo'],
            'descripcion' => ['required', 'string', 'max:255'],
            'fecha_apertura' => ['required', 'date'],
            'articulos' => ['required', 'array', 'min:1'],
            'articulos.*.id_articulo' => ['required', 'integer', 'exists:articulos,id'],
            'articulos.*.cantidad' => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($data) {

            // Autogenerar código si no viene
            $codigo = $data['codigo'] ?? $this->generarCodigoOT();

            // 1) Crear OT
            $ot = OrdenTrabajo::create([
                'codigo' => $codigo,
                'descripcion' => $data['descripcion'],
                'estado' => 'PENDIENTE',
                'fecha_apertura' => $data['fecha_apertura'],
            ]);

            // 2) Procesar líneas: verificar stock y descontar
            foreach ($data['articulos'] as $linea) {
                $articulo = Articulo::query()
                    ->lockForUpdate()
                    ->findOrFail($linea['id_articulo']);

                $cantidad = (int) $linea['cantidad'];

                if ($articulo->stock_actual < $cantidad) {
                    abort(response()->json([
                        'message' => 'Stock insuficiente para crear la OT.',
                        'detalle' => [
                            'id_articulo' => $articulo->id,
                            'stock_actual' => $articulo->stock_actual,
                            'cantidad_solicitada' => $cantidad,
                        ]
                    ], 400));
                }

                // Crear línea OT
                OrdenTrabajoArticulo::create([
                    'id_orden_trabajo' => $ot->id,
                    'id_articulo' => $articulo->id,
                    'cantidad' => $cantidad,
                ]);

                // Descontar stock
                $articulo->stock_actual = $articulo->stock_actual - $cantidad;
                $articulo->save();
            }

            // Devolver OT con líneas
            $ot->load(['lineas.articulo']);

            return response()->json($ot, 201);
        });
    }

    /**
     * HU-22: Actualizar estado OT
     * Permitimos transiciones:
     * PENDIENTE -> EN_CURSO -> FINALIZADA
     * y opcionalmente FINALIZADA -> ARCHIVADA (si lo quieres usar)
     */
    public function updateEstado(Request $request, $id)
    {
        $data = $request->validate([
            'estado' => ['required', Rule::in(['PENDIENTE', 'EN_CURSO', 'FINALIZADA', 'ARCHIVADA'])],
        ]);

        $ot = OrdenTrabajo::findOrFail($id);

        $actual = $ot->estado;
        $nuevo = $data['estado'];

        $transicionesValidas = [
            'PENDIENTE' => ['EN_CURSO'],
            'EN_CURSO' => ['FINALIZADA'],
            'FINALIZADA' => ['ARCHIVADA'],
            'ARCHIVADA' => [],
        ];

        if (!isset($transicionesValidas[$actual]) || !in_array($nuevo, $transicionesValidas[$actual], true)) {
            return response()->json([
                'message' => 'Transición de estado no permitida.',
                'estado_actual' => $actual,
                'estado_solicitado' => $nuevo,
            ], 400);
        }

        $ot->estado = $nuevo;
        $ot->save();

        return response()->json($ot);
    }

    private function generarCodigoOT(): string
    {
        $ultimoId = (int) (OrdenTrabajo::query()->max('id') ?? 0) + 1;
        return 'OT-' . str_pad((string)$ultimoId, 5, '0', STR_PAD_LEFT);
    }
}
