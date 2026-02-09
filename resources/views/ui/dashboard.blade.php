@extends('ui.layout')

@section('title', 'Inicio')

@section('content')

{{-- CABECERA --}}
<div class="row g-4 mb-3">
    <div class="col-12">
        <div class="p-4 bg-white rounded-3 shadow-sm border d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-1">Panel de control</h1>
                <p class="text-muted mb-0">Acceso rápido a las funciones principales del sistema.</p>
            </div>

            {{-- BOTONES SUPERIORES: ALERTAS + REPORTES --}}
            <div class="d-flex gap-2 flex-wrap">

                {{-- ALERTAS --}}
                <a href="/ui/alertas"
                   class="btn {{ ($alertasPendientes ?? 0) > 0 ? 'btn-danger' : 'btn-outline-secondary' }}">
                    Alertas
                    <span class="badge bg-light text-dark ms-2">
                        {{ $alertasPendientes ?? 0 }}
                    </span>
                </a>

                {{-- REPORTES (desplegable) --}}
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Reportes
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="/ui/reportes/inventario">
                                Reporte de Inventario
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/ui/reportes/movimientos">
                                Reporte de Movimientos
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>


{{-- KPIs --}}
<div class="row g-4 mb-4">

    <div class="col-12 col-md-3">
        <div class="card shadow-sm h-100 border">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="h6 mb-0 text-muted">Artículos bajo mínimo</h2>
                    <span class="badge bg-danger">KPI</span>
                </div>
                <div class="display-6 fw-bold mt-2">
                    {{ $articulosBajoMinimo ?? 0 }}
                </div>
                <div class="text-muted small mt-1">Activos con stock &lt; mínimo</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3">
        <div class="card shadow-sm h-100 border">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="h6 mb-0 text-muted">Movimientos hoy</h2>
                    <span class="badge bg-info">KPI</span>
                </div>
                <div class="display-6 fw-bold mt-2">
                    {{ $movimientosHoy ?? 0 }}
                </div>
                <div class="text-muted small mt-1">Entradas / salidas / devoluciones</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3">
        <div class="card shadow-sm h-100 border">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="h6 mb-0 text-muted">OTs abiertas</h2>
                    <span class="badge bg-success">KPI</span>
                </div>
                <div class="display-6 fw-bold mt-2">
                    {{ $otsAbiertas ?? 0 }}
                </div>
                <div class="text-muted small mt-1">PENDIENTE / EN_CURSO</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3">
        <div class="card shadow-sm h-100 border {{ ($alertasPendientes ?? 0) > 0 ? 'border-danger' : '' }}">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="h6 mb-0 text-muted">Alertas pendientes</h2>
                    <span class="badge {{ ($alertasPendientes ?? 0) > 0 ? 'bg-danger' : 'bg-secondary' }}">AUTO</span>
                </div>
                <div class="display-6 fw-bold mt-2">
                    {{ $alertasPendientes ?? 0 }}
                </div>
                <div class="text-muted small mt-1">
                    Generadas cuando stock baja del mínimo
                </div>
            </div>
        </div>
    </div>

</div>


{{-- TARJETAS PRINCIPALES (accesos rápidos) --}}
<div class="row g-4">

    <div class="col-12 col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h2 class="h5">Artículos</h2>
                <p class="text-muted">Consultar, crear, editar y desactivar artículos del inventario.</p>
                <a class="btn btn-primary" href="/ui/articulos">Ir a Artículos</a>
                <a class="btn btn-outline-primary ms-2" href="/ui/articulos/crear">Crear</a>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h2 class="h5">Órdenes de trabajo</h2>
                <p class="text-muted">Ver OTs, crear nuevas y cambiar su estado.</p>
                <a class="btn btn-primary" href="/ui/ots">Ir a OTs</a>
                <a class="btn btn-outline-primary ms-2" href="/ui/ots/crear">Crear</a>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h2 class="h5">Movimientos</h2>
                <p class="text-muted">Entradas, salidas y devoluciones de stock.</p>
                <a class="btn btn-primary" href="/ui/movimientos">Ir a Movimientos</a>
            </div>
        </div>
    </div>

</div>


{{-- BLOQUE: ÚLTIMAS ALERTAS --}}
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="card shadow-sm border {{ ($alertasPendientes ?? 0) > 0 ? 'border-danger' : '' }}">
            <div class="card-body">

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h3 class="h5 mb-1">Últimas alertas pendientes</h3>
                        <p class="text-muted mb-0 small">
                            Si pulsas “Alertas”, puedes marcarlas como atendidas cuando las resuelvas.
                        </p>
                    </div>

                    <a href="/ui/alertas"
                       class="btn {{ ($alertasPendientes ?? 0) > 0 ? 'btn-danger' : 'btn-outline-secondary' }}">
                        Ver alertas
                    </a>
                </div>

                <hr>

                @php
                    $lista = $ultimasAlertas ?? collect();
                @endphp

                @if($lista->count() === 0)
                    <div class="alert alert-success mb-0">
                        ✅ No hay alertas pendientes ahora mismo.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Artículo</th>
                                    <th>Stock actual</th>
                                    <th>Mínimo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lista as $a)
                                    <tr>
                                        <td class="text-muted small">{{ $a->fecha_hora }}</td>
                                        <td>
                                            <span class="fw-semibold">{{ $a->articulo_codigo }}</span>
                                            <div class="text-muted small">{{ $a->articulo_nombre }}</div>
                                        </td>
                                        <td class="fw-bold text-danger">{{ $a->stock_actual }}</td>
                                        <td class="fw-bold">{{ $a->stock_minimo }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-muted small">
                        Mostrando {{ $lista->count() }} últimas alertas (pendientes).
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

@endsection
