<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UiController;

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect('/ui'));

/*
|--------------------------------------------------------------------------
| DASHBOARD (Breeze)
|--------------------------------------------------------------------------
| Breeze redirige a route('dashboard') tras login.
| Mantenemos la ruta y la redirigimos a /ui.
*/
Route::middleware('auth')->get('/dashboard', fn () => redirect('/ui'))->name('dashboard');

/*
|--------------------------------------------------------------------------
| UI (PROTEGIDA POR LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard real de tu app
    Route::get('/ui', [UiController::class, 'dashboard'])->name('ui.dashboard');

    /*
    |--------------------------------------------------------------------------
    | UI - Artículos
    |--------------------------------------------------------------------------
    */
    Route::get('/ui/articulos', [UiController::class, 'articulos'])->name('ui.articulos.index');
    Route::get('/ui/articulos/crear', [UiController::class, 'articuloCreate'])->name('ui.articulos.create');
    Route::post('/ui/articulos', [UiController::class, 'articuloStore'])->name('ui.articulos.store');

    Route::get('/ui/articulos/{id}', [UiController::class, 'articuloShow'])->name('ui.articulos.show');
    Route::get('/ui/articulos/{id}/editar', [UiController::class, 'articuloEdit'])->name('ui.articulos.edit');
    Route::post('/ui/articulos/{id}/editar', [UiController::class, 'articuloUpdate'])->name('ui.articulos.update');
    Route::post('/ui/articulos/{id}/eliminar', [UiController::class, 'articuloDestroy'])->name('ui.articulos.destroy');
    Route::post('/ui/articulos/{id}/reactivar', [UiController::class, 'articuloReactivar'])->name('ui.articulos.reactivar');

    /*
    |--------------------------------------------------------------------------
    | UI - Órdenes de trabajo
    |--------------------------------------------------------------------------
    */
    Route::get('/ui/ots', [UiController::class, 'ots'])->name('ui.ots.index');
    Route::get('/ui/ots/crear', [UiController::class, 'otCreateForm'])->name('ui.ots.create');
    Route::post('/ui/ots', [UiController::class, 'otStore'])->name('ui.ots.store');
    Route::get('/ui/ots/{id}', [UiController::class, 'otShow'])->name('ui.ots.show');

    Route::match(['POST','PATCH'], '/ui/ots/{id}/estado', [UiController::class, 'otUpdateEstado'])->name('ui.ots.estado');
    Route::post('/ui/ots/{id}/consumir', [UiController::class, 'otConsumir'])->name('ui.ots.consumir');

    /*
    |--------------------------------------------------------------------------
    | UI - Movimientos
    |--------------------------------------------------------------------------
    */
    Route::get('/ui/movimientos', [UiController::class, 'movimientos'])->name('ui.movimientos.index');
    Route::post('/ui/movimientos/entrada', [UiController::class, 'movEntrada'])->name('ui.movimientos.entrada');
    Route::post('/ui/movimientos/salida', [UiController::class, 'movSalida'])->name('ui.movimientos.salida');
    Route::post('/ui/movimientos/devolucion', [UiController::class, 'movDevolucion'])->name('ui.movimientos.devolucion');

    /*
    |--------------------------------------------------------------------------
    | UI - ZONA RESTRINGIDA POR ROL (ADMIN = id_rol 1)
    |--------------------------------------------------------------------------
    | Reportes + Alertas solo para rol 1.
    */
    Route::middleware('role:1')->group(function () {
        // Reportes (obligatorios)
        Route::get('/ui/reportes/inventario', [UiController::class, 'reporteInventario'])->name('ui.reportes.inventario');
        Route::get('/ui/reportes/movimientos', [UiController::class, 'reporteMovimientos'])->name('ui.reportes.movimientos');

        // Alertas
        Route::get('/ui/alertas', [UiController::class, 'alertasIndex'])->name('ui.alertas.index');
        Route::post('/ui/alertas/{id}/atender', [UiController::class, 'alertaAtender'])->name('ui.alertas.atender');
    });
});

/*
|--------------------------------------------------------------------------
| AP4 - pantallas mínimas (Actividad IV) - SIN LOGIN
|--------------------------------------------------------------------------
*/
Route::prefix('ap4')->group(function () {
    Route::get('/', fn () => view('ap4.home'));
    Route::get('/login', fn () => view('ap4.login'));

    Route::get('/articulos', function () {
        $articulos = [
            ['codigo' => 'A-001', 'nombre' => 'Taladro', 'categoria' => 'Herramientas', 'stock' => 12],
            ['codigo' => 'A-002', 'nombre' => 'Guantes', 'categoria' => 'EPI', 'stock' => 45],
            ['codigo' => 'A-003', 'nombre' => 'Linterna', 'categoria' => 'Material', 'stock' => 7],
        ];
        return view('ap4.articulos', compact('articulos'));
    });

    Route::get('/articulos/crear', fn () => view('ap4.crear'));
    Route::post('/articulos/crear', fn () => redirect('/ap4/articulos')->with('status', 'Artículo creado correctamente (simulado).'));
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
