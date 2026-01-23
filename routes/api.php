<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\OrdenTrabajoController;

Route::get('/ping', function () {
    return response()->json(['pong' => true]);
});

/*
|--------------------------------------------------------------------------
| ARTÍCULOS (Sprint 2)
|--------------------------------------------------------------------------
| HU-01 Crear artículo
| HU-02 Consultar artículos
| HU-03 Editar artículo
| HU-04 Eliminar artículo
| HU-05 Ver detalle de artículo
*/
Route::post('/articulos', [ArticuloController::class, 'store']);
Route::get('/articulos', [ArticuloController::class, 'index']);
Route::get('/articulos/{id}', [ArticuloController::class, 'show']);
Route::put('/articulos/{id}', [ArticuloController::class, 'update']);
Route::delete('/articulos/{id}', [ArticuloController::class, 'destroy']);

// Consulta de stock (Sprint 1)
Route::get('/articulos/{id}/stock', [ArticuloController::class, 'consultaStock']);


/*
|--------------------------------------------------------------------------
| MOVIMIENTOS (Sprint 1 + Sprint 2)
|--------------------------------------------------------------------------
| Sprint 1:
| - Entrada
| - Salida
|
| Sprint 2:
| HU-12 Registrar devolución de material
| HU-13 Consultar historial de movimientos
*/
Route::post('/movimientos/entrada', [MovimientoController::class, 'registrarEntrada']);
Route::post('/movimientos/salida',  [MovimientoController::class, 'registrarSalida']);
Route::post('/movimientos/devolucion', [MovimientoController::class, 'registrarDevolucion']);

// Historial por artículo 
Route::get('/movimientos/historial/{id_articulo}', [MovimientoController::class, 'historialPorArticulo']);

/*
|--------------------------------------------------------------------------
| ÓRDENES DE TRABAJO (Sprint 2)
|--------------------------------------------------------------------------
| HU-20 Crear OT (descuenta stock al crear)
| HU-21 Consultar OT
| HU-22 Actualizar estado OT
*/
Route::post('/ordenes-trabajo', [OrdenTrabajoController::class, 'store']);
Route::get('/ordenes-trabajo', [OrdenTrabajoController::class, 'index']);
Route::get('/ordenes-trabajo/{id}', [OrdenTrabajoController::class, 'show']);
Route::patch('/ordenes-trabajo/{id}/estado', [OrdenTrabajoController::class, 'updateEstado']);
