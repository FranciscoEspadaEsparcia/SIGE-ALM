<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\ArticuloController;

Route::get('/ping', function () {
    return response()->json(['pong' => true]);
});

// Movimientos
Route::post('/movimientos/entrada', [MovimientoController::class, 'registrarEntrada']);
Route::post('/movimientos/salida',  [MovimientoController::class, 'registrarSalida']);

// Consulta de stock
Route::get('/articulos/stock', [ArticuloController::class, 'consultaStock']);
