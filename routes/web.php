<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/ui'); // evita romper "/" si alguna vista antigua usa @vite
});

Route::get('/ui', [UiController::class, 'dashboard']);

/*
|--------------------------------------------------------------------------
| UI - Artículos (CRUD vía API)
|--------------------------------------------------------------------------
*/
Route::get('/ui/articulos', [UiController::class, 'articulos']);
Route::get('/ui/articulos/crear', [UiController::class, 'articuloCreate']);
Route::post('/ui/articulos', [UiController::class, 'articuloStore']);

Route::get('/ui/articulos/{id}', [UiController::class, 'articuloShow']);
Route::get('/ui/articulos/{id}/editar', [UiController::class, 'articuloEdit']);
Route::post('/ui/articulos/{id}/editar', [UiController::class, 'articuloUpdate']); // POST simple
Route::post('/ui/articulos/{id}/eliminar', [UiController::class, 'articuloDestroy']); // POST simple
Route::post('/ui/articulos/{id}/reactivar', [UiController::class, 'articuloReactivar']);


/*
|--------------------------------------------------------------------------
| UI - Órdenes de trabajo (ya lo tienes)
|--------------------------------------------------------------------------
*/
Route::get('/ui/ots', [UiController::class, 'ots']);
Route::get('/ui/ots/crear', [UiController::class, 'otCreateForm']);
Route::post('/ui/ots', [UiController::class, 'otStore']);
Route::get('/ui/ots/{id}', [UiController::class, 'otShow']);
Route::patch('/ui/ots/{id}/estado', [UiController::class, 'otUpdateEstado']);
Route::post('/ui/ots/{id}/estado', [UiController::class, 'otUpdateEstado']);

/*
|--------------------------------------------------------------------------
| UI - Movimientos (ya lo tienes)
|--------------------------------------------------------------------------
*/
Route::get('/ui/movimientos', [UiController::class, 'movimientos']);
Route::post('/ui/movimientos/entrada', [UiController::class, 'movEntrada']);
Route::post('/ui/movimientos/salida', [UiController::class, 'movSalida']);
Route::post('/ui/movimientos/devolucion', [UiController::class, 'movDevolucion']);
Route::post('/ui/ots/{id}/consumir', [UiController::class, 'otConsumir']);


/*
|--------------------------------------------------------------------------
| AP4 - pantallas mínimas (mantener para la Actividad IV)
|--------------------------------------------------------------------------
*/
Route::prefix('ap4')->group(function () {
    Route::get('/', fn() => view('ap4.home'));
    Route::get('/login', fn() => view('ap4.login'));

    Route::get('/articulos', function () {
        $articulos = [
            ['codigo' => 'A-001', 'nombre' => 'Taladro', 'categoria' => 'Herramientas', 'stock' => 12],
            ['codigo' => 'A-002', 'nombre' => 'Guantes', 'categoria' => 'EPI', 'stock' => 45],
            ['codigo' => 'A-003', 'nombre' => 'Linterna', 'categoria' => 'Material', 'stock' => 7],
        ];
        return view('ap4.articulos', compact('articulos'));
    });

    Route::get('/articulos/crear', fn() => view('ap4.crear'));
    Route::post('/articulos/crear', fn() => redirect('/ap4/articulos')->with('status', 'Artículo creado correctamente (simulado).'));
});
