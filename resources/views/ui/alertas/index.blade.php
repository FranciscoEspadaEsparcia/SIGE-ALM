@extends('ui.layout')

@section('title', 'Alertas de stock')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h1 class="h4 mb-1">Alertas de stock</h1>
        <div class="text-muted">Lista de alertas generadas automáticamente cuando el stock baja del mínimo.</div>
    </div>

    <a href="/ui" class="btn btn-outline-secondary">Volver</a>
</div>

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="card shadow-sm mb-3">
    <div class="card-body d-flex flex-wrap gap-2 align-items-center">
        <span class="text-muted me-2">Filtro:</span>

        <a class="btn btn-sm {{ $estado==='PENDIENTE' ? 'btn-danger' : 'btn-outline-danger' }}"
           href="/ui/alertas?estado=PENDIENTE">Pendientes</a>

        <a class="btn btn-sm {{ $estado==='ATENDIDA' ? 'btn-success' : 'btn-outline-success' }}"
           href="/ui/alertas?estado=ATENDIDA">Atendidas</a>

        <a class="btn btn-sm {{ $estado==='TODAS' ? 'btn-primary' : 'btn-outline-primary' }}"
           href="/ui/alertas?estado=TODAS">Todas</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Artículo</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Mínimo</th>
                        <th>Estado</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($alertas as $a)
                    @php
                        $pendiente = (trim(strtoupper($a->estado)) === 'PENDIENTE');
                    @endphp
                    <tr>
                        <td style="white-space:nowrap">{{ $a->fecha_hora }}</td>
                        <td>
                            <div class="fw-semibold">{{ $a->articulo_codigo }}</div>
                            <div class="text-muted small">{{ $a->articulo_nombre }}</div>
                        </td>
                        <td class="text-center fw-bold {{ $pendiente ? 'text-danger' : '' }}">
                            {{ $a->stock_actual }}
                        </td>
                        <td class="text-center">{{ $a->stock_minimo }}</td>
                        <td>
                            @if($pendiente)
                                <span class="badge bg-danger">PENDIENTE</span>
                            @else
                                <span class="badge bg-success">ATENDIDA</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($pendiente)
                                <form method="post" action="/ui/alertas/{{ $a->id }}/atender" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success">
                                        Marcar atendida
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-muted">
                            No hay alertas para este filtro.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $alertas->links() }}
</div>
@endsection
