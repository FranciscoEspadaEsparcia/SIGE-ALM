@extends('ui.layout')

@section('title', 'Movimientos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Movimientos</h1>
        <p class="text-muted mb-0">Entradas, salidas y devoluciones con actualización automática de stock.</p>
    </div>
    <a class="btn btn-outline-secondary" href="/ui">Inicio</a>
</div>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <div class="fw-semibold mb-1">Errores:</div>
        <ul class="mb-0">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">Registrar movimiento</h2>

                <form method="get" action="/ui/movimientos" class="vstack gap-2 mb-3">
                    <label class="form-label mb-0">Artículo (para ver historial)</label>
                    <select class="form-select" name="id_articulo" onchange="this.form.submit()">
                        <option value="">Selecciona...</option>
                        @foreach($articulos as $a)
                            <option value="{{ $a->id }}" {{ (string)$id_articulo === (string)$a->id ? 'selected' : '' }}>
                                {{ $a->codigo }} — {{ $a->nombre }} (Stock: {{ $a->stock_actual }})
                            </option>
                        @endforeach
                    </select>
                </form>

                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#t1" type="button">ENTRADA</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t2" type="button">SALIDA</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#t3" type="button">DEVOLUCIÓN</button></li>
                </ul>

                <div class="tab-content border border-top-0 rounded-bottom p-3">
                    <div class="tab-pane fade show active" id="t1">
                        <form method="post" action="/ui/movimientos/entrada" class="vstack gap-2">
                            @csrf
                            @include('ui.movimientos._form')
                            <button class="btn btn-success" type="submit">Registrar entrada</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="t2">
                        <form method="post" action="/ui/movimientos/salida" class="vstack gap-2">
                            @csrf
                            @include('ui.movimientos._form')
                            <button class="btn btn-danger" type="submit">Registrar salida</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="t3">
                        <form method="post" action="/ui/movimientos/devolucion" class="vstack gap-2">
                            @csrf
                            @include('ui.movimientos._form')
                            <button class="btn btn-primary" type="submit">Registrar devolución</button>
                        </form>
                    </div>
                </div>

                <div class="text-muted small mt-3">
                    ENTRADA/DEVOLUCIÓN suman stock. SALIDA resta (no permite stock negativo).
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">Historial (últimos 50)</h2>

                @if(!$id_articulo)
                    <p class="text-muted mb-0">Selecciona un artículo para ver el historial.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th class="text-end">Cantidad</th>
                                    <th>Usuario</th>
                                    <th>OT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historial as $m)
                                    <tr>
                                        <td>{{ $m->fecha_hora }}</td>
                                        <td>
                                            @php
                                                $b = match($m->tipo) {
                                                    'ENTRADA' => 'success',
                                                    'SALIDA' => 'danger',
                                                    'DEVOLUCION' => 'primary',
                                                    'AJUSTE' => 'warning',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge text-bg-{{ $b }}">{{ $m->tipo }}</span>
                                        </td>
                                        <td class="text-end">{{ $m->cantidad }}</td>
                                        <td>{{ optional($m->usuario)->nombre ?? '-' }}</td>
                                        <td>{{ optional($m->ordenTrabajo)->codigo ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-3">Sin movimientos.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
