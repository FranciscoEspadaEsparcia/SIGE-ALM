<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Articulo;
use App\Models\OrdenTrabajo;
use App\Models\Movimiento;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;





class UiController extends Controller
{
    private function api()
    {
        return Http::acceptJson();
    }

    private function apiFailToErrors($res)
    {
        $json = $res->json();
        return $json['errors'] ?? ['api' => $json['message'] ?? 'Error API'];
    }

    public function dashboard()
    {
        return view('ui.dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | ARTÍCULOS (UI -> API)
    |--------------------------------------------------------------------------
    */

public function articulos(Request $request)
{
    $q = $request->query('q');
    $showInactive = $request->query('show_inactive') === '1';

    $query = \App\Models\Articulo::query()->orderBy('id', 'desc');

    // Por defecto, ocultamos inactivos
    if (!$showInactive) {
        $query->where('activo', 1);
    }

    if ($q) {
        $query->where(function($qq) use ($q) {
            $qq->where('codigo', 'like', "%{$q}%")
               ->orWhere('nombre', 'like', "%{$q}%");
        });
    }

    $articulos = $query->paginate(10)->withQueryString();

    return view('ui.articulos.index', [
        'articulos' => $articulos,
        'q' => $q,
        'showInactive' => $showInactive,
    ]);
}



public function articuloShow($id)
{
    $articulo = Articulo::with(['categoria','proveedorPreferente'])->findOrFail($id);
    return view('ui.articulos.show', compact('articulo'));
}



   public function articuloCreate()
{
    // NO llamar a la API por HTTP (timeout en artisan serve)
    $categorias = Categoria::orderBy('nombre')->get();
    $proveedores = Proveedor::orderBy('nombre')->get();

    return view('ui.articulos.create', compact('categorias', 'proveedores'));
}

public function articuloStore(Request $request)
{
    $data = $request->validate([
        'codigo' => ['required','string','max:50'],
        'nombre' => ['required','string','max:255'],
        'descripcion' => ['nullable','string','max:2000'],
        'stock_actual' => ['required','integer','min:0'],
        'stock_minimo' => ['required','integer','min:0'],
        'activo' => ['required','in:0,1'],
        'id_categoria' => ['required','integer'],
        'id_proveedor_preferente' => ['nullable','integer'],
    ]);

    $data['activo'] = (int)$data['activo'];

    Articulo::create($data);

    return redirect('/ui/articulos')->with('status', 'Artículo creado correctamente.');
}


   public function articuloEdit($id)
{
    $articulo = Articulo::findOrFail($id);
    $categorias = Categoria::orderBy('nombre')->get();
    $proveedores = Proveedor::orderBy('nombre')->get();

    return view('ui.articulos.edit', compact('articulo', 'categorias', 'proveedores'));
}


public function articuloUpdate(Request $request, $id)
{
    $articulo = Articulo::findOrFail($id);

    $data = $request->validate([
        'codigo' => ['required','string','max:50'],
        'nombre' => ['required','string','max:255'],
        'descripcion' => ['nullable','string','max:2000'],
        'stock_actual' => ['required','integer','min:0'],
        'stock_minimo' => ['required','integer','min:0'],
        'activo' => ['required','in:0,1'],
        'id_categoria' => ['required','integer'],
        'id_proveedor_preferente' => ['nullable','integer'],
    ]);

    $data['activo'] = (int)$data['activo'];

    $articulo->update($data);

    return redirect('/ui/articulos')->with('status', 'Artículo actualizado correctamente.');
}


public function articuloDestroy($id)
{
    $articulo = \App\Models\Articulo::findOrFail($id);

    // Borrado lógico: no eliminar físicamente, solo desactivar
    $articulo->activo = 0;
    $articulo->save();

    return redirect('/ui/articulos')->with('status', 'Artículo desactivado correctamente.');
}


public function articuloReactivar($id)
{
    $articulo = \App\Models\Articulo::findOrFail($id);
    $articulo->activo = 1;
    $articulo->save();

    return redirect('/ui/articulos')->with('status', 'Artículo reactivado correctamente.');
}


    /*
    |--------------------------------------------------------------------------
    | OTS (tu código)
    |--------------------------------------------------------------------------
    */




public function ots()
{
    $ots = OrdenTrabajo::orderByDesc('id')->paginate(10);
    return view('ui.ots.index', compact('ots'));
}


public function otShow($id)
{
    $ot = OrdenTrabajo::with(['articulos'])->findOrFail($id);

    $movimientos = Movimiento::with(['articulo','usuario'])
        ->where('id_orden_trabajo', $ot->id)
        ->orderByDesc('fecha_hora')
        ->limit(200)
        ->get();

    return view('ui.ots.show', compact('ot','movimientos'));
}






   public function otCreateForm()
{
    $articulos = \App\Models\Articulo::where('activo', 1)
        ->orderBy('nombre')
        ->get();

    return view('ui.ots.create', compact('articulos'));
}


public function otStore(Request $request)
{
    $data = $request->validate([
        'codigo' => ['required'],
        'descripcion' => ['required'],
        'fecha_apertura' => ['required','date'],
        'estado' => ['required'],
        'articulos' => ['required','array','min:1'],
        'articulos.*.id_articulo' => ['required','exists:articulos,id'],
        'articulos.*.cantidad' => ['required','integer','min:1'],
    ]);

    $ot = OrdenTrabajo::create([
        'codigo' => $data['codigo'],
        'descripcion' => $data['descripcion'],
        'fecha_apertura' => $data['fecha_apertura'],
        'estado' => $data['estado'],
    ]);

    foreach ($data['articulos'] as $a) {
        $ot->articulos()->attach($a['id_articulo'], [
            'cantidad' => $a['cantidad']
        ]);
    }

    return redirect("/ui/ots/{$ot->id}")
           ->with('ok','Orden de trabajo creada');
}



  public function otUpdateEstado(Request $request, $id)
{
    $data = $request->validate([
        'estado' => ['required', 'in:PENDIENTE,EN_CURSO,FINALIZADA,ARCHIVADA'],
    ]);

    $ot = \App\Models\OrdenTrabajo::findOrFail($id);
    $ot->estado = $data['estado'];
    $ot->save();

    return back()->with('status', 'Estado actualizado correctamente.');
}
public function otConsumir(Request $request, $id)
{
    $data = $request->validate([
        'id_usuario' => ['required','exists:usuarios,id'],
    ]);

    try {
        DB::transaction(function () use ($id, $data) {
            $ot = \App\Models\OrdenTrabajo::with('articulos')->lockForUpdate()->findOrFail($id);

            // Solo permitir consumo si está EN_CURSO o FINALIZADA (ajústalo si quieres)
            if (!in_array($ot->estado, ['EN_CURSO','FINALIZADA'])) {
                throw new \RuntimeException("La OT debe estar EN_CURSO o FINALIZADA para consumir stock.");
            }

            foreach ($ot->articulos as $art) {
                $cantidad = (int) $art->pivot->cantidad;

                // Bloqueamos el artículo para stock consistente
                $a = \App\Models\Articulo::lockForUpdate()->findOrFail($art->id);

                if ($a->stock_actual < $cantidad) {
                    throw new \RuntimeException("Stock insuficiente para {$a->codigo} ({$a->nombre}). Stock: {$a->stock_actual}, requerido: {$cantidad}.");
                }

                // Crear movimiento SALIDA asociado a la OT
                \App\Models\Movimiento::create([
                    'tipo' => 'SALIDA',
                    'cantidad' => $cantidad,
                    'fecha_hora' => now(),
                    'id_articulo' => $a->id,
                    'id_usuario' => $data['id_usuario'],
                    'id_orden_trabajo' => $ot->id,
                    'id_albaran' => null,
                ]);

                // Restar stock
                $a->stock_actual = $a->stock_actual - $cantidad;
                $a->save();
            }
        });
    } catch (\RuntimeException $e) {
        return back()->withErrors(['stock' => $e->getMessage()]);
    }

    return back()->with('status', 'Consumo registrado: salidas generadas y stock actualizado.');
}



    /*
    |--------------------------------------------------------------------------
    | MOVIMIENTOS (tu código)
    |--------------------------------------------------------------------------
    */

public function movimientos(Request $request)
{
    $articulos = Articulo::where('activo', 1)->orderBy('nombre')->get();
    $usuarios = Usuario::where('activo', 1)->orderBy('nombre')->get();
    $ots = OrdenTrabajo::orderByDesc('id')->limit(200)->get();

    $idArticulo = $request->query('id_articulo');

    $historial = collect();
    if ($idArticulo) {
        $historial = Movimiento::with(['usuario','ordenTrabajo'])
            ->where('id_articulo', $idArticulo)
            ->orderByDesc('fecha_hora')
            ->limit(50)
            ->get();
    }

    return view('ui.movimientos.index', [
        'articulos' => $articulos,
        'usuarios' => $usuarios,
        'ots' => $ots,
        'id_articulo' => $idArticulo,
        'historial' => $historial,
    ]);
}

public function movEntrada(Request $request)
{
    return $this->registrarMovimientoConStock('ENTRADA', $request);
}

public function movSalida(Request $request)
{
    return $this->registrarMovimientoConStock('SALIDA', $request);
}

public function movDevolucion(Request $request)
{
    return $this->registrarMovimientoConStock('DEVOLUCION', $request);
}

private function registrarMovimientoConStock(string $tipo, Request $request)
{
    $data = $request->validate([
        'id_articulo' => ['required','exists:articulos,id'],
        'id_usuario' => ['required','exists:usuarios,id'],
        'cantidad' => ['required','integer','min:1'],
        'id_orden_trabajo' => ['nullable','exists:ordenes_trabajo,id'],
        'id_albaran' => ['nullable','integer'],
    ]);

    try {
        DB::transaction(function () use ($tipo, $data) {
            $art = Articulo::lockForUpdate()->findOrFail($data['id_articulo']);

            $delta = match($tipo) {
                'ENTRADA' => +$data['cantidad'],
                'DEVOLUCION' => +$data['cantidad'],
                'SALIDA' => -$data['cantidad'],
                default => 0
            };

            $nuevo = $art->stock_actual + $delta;

            if ($nuevo < 0) {
                throw new \RuntimeException("Stock insuficiente. Stock actual: {$art->stock_actual}, salida: {$data['cantidad']}.");
            }

            Movimiento::create([
                'tipo' => $tipo,
                'cantidad' => $data['cantidad'],
                'fecha_hora' => now(),
                'id_articulo' => $data['id_articulo'],
                'id_usuario' => $data['id_usuario'],
                'id_orden_trabajo' => $data['id_orden_trabajo'] ?? null,
                'id_albaran' => $data['id_albaran'] ?? null,
            ]);

            $art->stock_actual = $nuevo;
            $art->save();
        });
    } catch (\RuntimeException $e) {
        return back()->withErrors(['stock' => $e->getMessage()])->withInput();
    }

    return redirect('/ui/movimientos?id_articulo=' . $data['id_articulo'])
        ->with('status', "Movimiento {$tipo} registrado y stock actualizado.");
}


}
