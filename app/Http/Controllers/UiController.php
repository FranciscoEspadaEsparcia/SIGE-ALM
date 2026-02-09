<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Categoria;
use App\Models\Proveedor;
use App\Models\Articulo;
use App\Models\OrdenTrabajo;
use App\Models\Movimiento;
use App\Models\Usuario;

class UiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function dashboard()
    {
        $articulosBajoMinimo = Articulo::where('activo', 1)
            ->whereColumn('stock_actual', '<', 'stock_minimo')
            ->count();

        $movimientosHoy = Movimiento::whereDate('fecha_hora', now()->toDateString())
            ->count();

        $otsAbiertas = OrdenTrabajo::whereIn('estado', ['PENDIENTE', 'EN_CURSO'])
            ->count();

        $alertasPendientes = DB::table('alertas_stock')
            ->whereRaw("TRIM(UPPER(estado)) = 'PENDIENTE'")
            ->count();

        $ultimasAlertas = DB::table('alertas_stock as a')
            ->join('articulos as ar', 'ar.id', '=', 'a.id_articulo')
            ->select(
                'a.id',
                'a.fecha_hora',
                'a.stock_actual',
                'a.stock_minimo',
                'ar.codigo as articulo_codigo',
                'ar.nombre as articulo_nombre'
            )
            ->whereRaw("TRIM(UPPER(a.estado)) = 'PENDIENTE'")
            ->orderByDesc('a.fecha_hora')
            ->limit(5)
            ->get();

        return view('ui.dashboard', compact(
            'articulosBajoMinimo',
            'movimientosHoy',
            'otsAbiertas',
            'alertasPendientes',
            'ultimasAlertas'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | ALERTAS
    |--------------------------------------------------------------------------
    */

    public function alertas()
    {
        $alertas = DB::table('alertas_stock as a')
            ->join('articulos as ar', 'ar.id', '=', 'a.id_articulo')
            ->select(
                'a.id',
                'a.fecha_hora',
                'a.stock_actual',
                'a.stock_minimo',
                'a.estado',
                'ar.codigo as articulo_codigo',
                'ar.nombre as articulo_nombre'
            )
            ->whereRaw("TRIM(UPPER(a.estado)) = 'PENDIENTE'")
            ->orderByDesc('a.fecha_hora')
            ->paginate(20);

        return view('ui.alertas.index', compact('alertas'));
    }

    public function alertasMarcarAtendidas(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('status', 'No se seleccionaron alertas.');
        }

        DB::table('alertas_stock')
            ->whereIn('id', $ids)
            ->update([
                'estado' => 'ATENDIDA',
                'updated_at' => now(),
            ]);

        return redirect('/ui/alertas')
            ->with('status', 'Alertas marcadas como atendidas.');
    }

    /*
    |--------------------------------------------------------------------------
    | ARTÍCULOS
    |--------------------------------------------------------------------------
    */

    public function articulos(Request $request)
    {
        $q = $request->query('q');
        $showInactive = $request->query('show_inactive') === '1';

        $query = Articulo::orderByDesc('id');

        if (!$showInactive) {
            $query->where('activo', 1);
        }

        if ($q) {
            $query->where(function ($qq) use ($q) {
                $qq->where('codigo', 'like', "%{$q}%")
                   ->orWhere('nombre', 'like', "%{$q}%");
            });
        }

        $articulos = $query->paginate(10)->withQueryString();

        return view('ui.articulos.index', compact('articulos', 'q', 'showInactive'));
    }

    public function articuloCreate()
    {
        return view('ui.articulos.create', [
            'categorias' => Categoria::orderBy('nombre')->get(),
            'proveedores' => Proveedor::orderBy('nombre')->get(),
        ]);
    }

    public function articuloStore(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required',
            'nombre' => 'required',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'activo' => 'required|in:0,1',
            'id_categoria' => 'required',
            'id_proveedor_preferente' => 'nullable',
        ]);

        Articulo::create($data);

        return redirect('/ui/articulos')->with('status', 'Artículo creado.');
    }

    /*
    |--------------------------------------------------------------------------
    | ÓRDENES DE TRABAJO
    |--------------------------------------------------------------------------
    */

    public function ots()
    {
        return view('ui.ots.index', [
            'ots' => OrdenTrabajo::orderByDesc('id')->paginate(10)
        ]);
    }

    public function otCreateForm()
    {
        return view('ui.ots.create', [
            'articulos' => Articulo::where('activo', 1)->orderBy('nombre')->get()
        ]);
    }

    public function otStore(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required',
            'descripcion' => 'required',
            'fecha_apertura' => 'required|date',
            'estado' => 'required',
            'articulos' => 'required|array|min:1',
            'articulos.*.id_articulo' => 'required|exists:articulos,id',
            'articulos.*.cantidad' => 'required|integer|min:1',
        ]);

        $ot = OrdenTrabajo::create($data);

        foreach ($data['articulos'] as $a) {
            $ot->articulos()->attach($a['id_articulo'], ['cantidad' => $a['cantidad']]);
        }

        return redirect("/ui/ots/{$ot->id}");
    }

    /*
    |--------------------------------------------------------------------------
    | MOVIMIENTOS
    |--------------------------------------------------------------------------
    */

    public function movimientos(Request $request)
    {
        return view('ui.movimientos.index', [
            'articulos' => Articulo::where('activo', 1)->get(),
            'usuarios' => Usuario::where('activo', 1)->get(),
            'ots' => OrdenTrabajo::orderByDesc('id')->limit(200)->get(),
            'historial' => collect(),
        ]);
    }

    public function movEntrada(Request $request)
    {
        return $this->registrarMovimiento('ENTRADA', $request);
    }

    public function movSalida(Request $request)
    {
        return $this->registrarMovimiento('SALIDA', $request);
    }

    public function movDevolucion(Request $request)
    {
        return $this->registrarMovimiento('DEVOLUCION', $request);
    }

    private function registrarMovimiento(string $tipo, Request $request)
    {
        $data = $request->validate([
            'id_articulo' => 'required|exists:articulos,id',
            'id_usuario' => 'required|exists:usuarios,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($tipo, $data) {
            $art = Articulo::lockForUpdate()->findOrFail($data['id_articulo']);

            $delta = $tipo === 'SALIDA' ? -$data['cantidad'] : $data['cantidad'];
            $art->stock_actual += $delta;

            if ($art->stock_actual < 0) {
                throw new \RuntimeException('Stock insuficiente');
            }

            Movimiento::create([
                'tipo' => $tipo,
                'cantidad' => $data['cantidad'],
                'fecha_hora' => now(),
                'id_articulo' => $art->id,
                'id_usuario' => $data['id_usuario'],
            ]);

            $art->save();

            if ($tipo === 'SALIDA') {
                $this->generarAlertaStockSiProcede($art);
            }
        });

        return back()->with('status', "Movimiento {$tipo} registrado.");
    }

    private function generarAlertaStockSiProcede(Articulo $art)
    {
        if ($art->stock_actual >= $art->stock_minimo) return;

        $existe = DB::table('alertas_stock')
            ->where('id_articulo', $art->id)
            ->whereRaw("TRIM(UPPER(estado)) = 'PENDIENTE'")
            ->exists();

        if (!$existe) {
            DB::table('alertas_stock')->insert([
                'id_articulo' => $art->id,
                'stock_actual' => $art->stock_actual,
                'stock_minimo' => $art->stock_minimo,
                'estado' => 'PENDIENTE',
                'fecha_hora' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REPORTES
    |--------------------------------------------------------------------------
    */

    public function reporteInventario()
    {
        return view('ui.reportes.inventario', [
            'articulos' => Articulo::with(['categoria', 'proveedorPreferente'])->get()
        ]);
    }

public function reporteMovimientos(Request $request)
{
    $desde = $request->query('desde');
    $hasta = $request->query('hasta');
    $tipo  = $request->query('tipo'); // ENTRADA|SALIDA|DEVOLUCION (opcional)

    $q = Movimiento::with(['articulo', 'usuario', 'ordenTrabajo'])
        ->orderByDesc('fecha_hora');

    if (!empty($tipo)) {
        $q->where('tipo', $tipo);
    }

    // Filtro fecha robusto para DATETIME
    if (!empty($desde)) {
        $q->where('fecha_hora', '>=', $desde . ' 00:00:00');
    }
    if (!empty($hasta)) {
        $q->where('fecha_hora', '<=', $hasta . ' 23:59:59');
    }

    $movimientos = $q->limit(500)->get();

    return view('ui.reportes.movimientos', compact('movimientos', 'desde', 'hasta', 'tipo'));
}




        /*
    |--------------------------------------------------------------------------
    | ALERTAS (UI)
    |--------------------------------------------------------------------------
    */

    public function alertasIndex(Request $request)
    {
        // filtros opcionales
        $estado = $request->query('estado', 'PENDIENTE'); // PENDIENTE | ATENDIDA | TODAS

        $q = DB::table('alertas_stock as a')
            ->join('articulos as ar', 'ar.id', '=', 'a.id_articulo')
            ->select(
                'a.id',
                'a.fecha_hora',
                'a.stock_actual',
                'a.stock_minimo',
                'a.estado',
                'ar.codigo as articulo_codigo',
                'ar.nombre as articulo_nombre'
            )
            ->orderByDesc('a.fecha_hora');

        if ($estado === 'PENDIENTE') {
            $q->whereRaw("TRIM(UPPER(a.estado)) = 'PENDIENTE'");
        } elseif ($estado === 'ATENDIDA') {
            $q->whereRaw("TRIM(UPPER(a.estado)) = 'ATENDIDA'");
        } // TODAS => sin filtro

        $alertas = $q->paginate(20)->withQueryString();

        return view('ui.alertas.index', compact('alertas', 'estado'));
    }

    public function alertaAtender($id)
    {
        // marcamos como ATENDIDA
        DB::table('alertas_stock')
            ->where('id', $id)
            ->update([
                'estado' => 'ATENDIDA',
                'updated_at' => now(),
            ]);

        return back()->with('status', 'Alerta marcada como atendida.');
    }

}
