@extends('ui.layout')

@section('title', 'Detalle OT')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Detalle de OT</h1>
        <p class="text-muted mb-0">{{ $ot->codigo }} — {{ $ot->descripcion }}</p>
    </div>
    <a class="btn btn-outline-secondary" href="/ui/ots">Volver</a>
</div>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <div class="fw-semibold mb-1">Se han producido errores:</div>
        <ul class="mb-0">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    <!-- COLUMNA IZQUIERDA -->
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6">Datos</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Código</dt>
                    <dd class="col-sm-8">{{ $ot->codigo }}</dd>

                    <dt class="col-sm-4">Descripción</dt>
                    <dd class="col-sm-8">{{ $ot->descripcion }}</dd>

                    <dt class="col-sm-4">Fecha apertura</dt>
                    <dd class="col-sm-8">{{ $ot->fecha_apertura }}</dd>

                    <dt class="col-sm-4">Estado</dt>
                    <dd class="col-sm-8">
                        @php
                            $badge = match($ot->estado) {
                                'PENDIENTE' => 'secondary',
                                'EN_CURSO' => 'primary',
                                'FINALIZADA' => 'success',
                                'ARCHIVADA' => 'dark',
                                default => 'light'
                            };
                        @endphp
                        <span class="badge text-bg-{{ $badge }}">{{ $ot->estado }}</span>
                    </dd>
                </dl>
            </div>
        </div>

        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <h2 class="h6 mb-3">Líneas (artículos)</h2>

                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th class="text-end">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ot->articulos as $a)
                                <tr>
                                    <td>{{ $a->id }}</td>
                                    <td class="fw-semibold">{{ $a->codigo }}</td>
                                    <td>{{ $a->nombre }}</td>
                                    <td class="text-end">{{ $a->pivot->cantidad }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">
                                        Esta OT no tiene artículos asociados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- COLUMNA DERECHA -->
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6">Cambiar estado</h2>

                <form method="post" action="/ui/ots/{{ $ot->id }}/estado" class="vstack gap-2">
                    @csrf
                    @php $val = old('estado', $ot->estado); @endphp
                    <select class="form-select" name="estado" required>
                        <option value="PENDIENTE" {{ $val==='PENDIENTE' ? 'selected' : '' }}>PENDIENTE</option>
                        <option value="EN_CURSO" {{ $val==='EN_CURSO' ? 'selected' : '' }}>EN_CURSO</option>
                        <option value="FINALIZADA" {{ $val==='FINALIZADA' ? 'selected' : '' }}>FINALIZADA</option>
                        <option value="ARCHIVADA" {{ $val==='ARCHIVADA' ? 'selected' : '' }}>ARCHIVADA</option>
                    </select>
                    <button class="btn btn-primary" type="submit">Guardar estado</button>
                </form>

                <hr>

                <h2 class="h6">Consumir artículos (SALIDA)</h2>
                <form method="post" action="/ui/ots/{{ $ot->id }}/consumir"
                      class="vstack gap-2"
                      onsubmit="return confirm('Esto registrará SALIDAS y descontará stock. ¿Continuar?');">
                    @csrf

                    <label class="form-label mb-0">Usuario que registra</label>
                    <select class="form-select" name="id_usuario" required>
                        <option value="">Selecciona...</option>
                        @foreach(\App\Models\Usuario::where('activo',1)->orderBy('nombre')->get() as $u)
                            <option value="{{ $u->id }}">{{ $u->nombre }} ({{ $u->username }})</option>
                        @endforeach
                    </select>

                    <button class="btn btn-warning" type="submit">Consumir (crear SALIDAS)</button>
                </form>

                <div class="text-muted small mt-2">
                    Genera movimientos SALIDA por cada línea de la OT y descuenta stock.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MOVIMIENTOS ASOCIADOS -->
<div class="card shadow-sm mt-3">
    <div class="card-body">
        <h2 class="h6 mb-3">Movimientos asociados a esta OT</h2>

        @if(($movimientos ?? collect())->isEmpty())
            <p class="text-muted mb-0">Aún no hay movimientos asociados a esta OT.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Artículo</th>
                            <th class="text-end">Cantidad</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movimientos as $m)
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
                                <td>
                                    @if($m->articulo)
                                        <span class="fw-semibold">{{ $m->articulo->codigo }}</span>
                                        — {{ $m->articulo->nombre }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-end">{{ $m->cantidad }}</td>
                                <td>{{ optional($m->usuario)->nombre ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
