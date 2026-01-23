@extends('ui.layout')

@section('title', 'Órdenes de trabajo')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Órdenes de trabajo</h1>
        <p class="text-muted mb-0">Listado y detalle de OTs.</p>
    </div>
    <a class="btn btn-primary" href="/ui/ots/crear">+ Crear OT</a>
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

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Fecha apertura</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ots as $ot)
                    <tr>
                        <td>{{ $ot->id }}</td>
                        <td class="fw-semibold">{{ $ot->codigo }}</td>
                        <td>{{ $ot->descripcion }}</td>
                        <td>{{ $ot->fecha_apertura }}</td>
                        <td>
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
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="/ui/ots/{{ $ot->id }}">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No hay órdenes de trabajo registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-body">
        {{ $ots->links() }}
    </div>
</div>
@endsection
