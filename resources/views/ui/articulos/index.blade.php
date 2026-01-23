@extends('ui.layout')

@section('title', 'Artículos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Artículos</h1>
        <p class="text-muted mb-0">CRUD directo a base de datos (UI).</p>
    </div>
    <a class="btn btn-primary" href="/ui/articulos/crear">+ Crear artículo</a>
</div>
<form class="row g-2 mb-3" method="get" action="/ui/articulos">
    <div class="col-auto">
        <input class="form-control" name="q" placeholder="Buscar por código o nombre" value="{{ $q ?? '' }}">
    </div>

    <div class="col-auto">
        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
    </div>

    <div class="col-auto form-check mt-2 ms-2">
        <input class="form-check-input" type="checkbox" id="show_inactive" name="show_inactive" value="1"
               {{ !empty($showInactive) ? 'checked' : '' }}>
        <label class="form-check-label" for="show_inactive">
            Mostrar inactivos
        </label>
    </div>
</form>


<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
            <thead class="table-light">
<tr>
    <th>ID</th>
    <th>Código</th>
    <th>Nombre</th>
    <th class="text-end">Stock</th>
    <th>Estado</th>
    <th class="text-end">Acciones</th>
</tr>
</thead>

            <tbody>
           @foreach($articulos as $a)
<tr>
    <td>{{ $a->id }}</td>
    <td class="fw-semibold">{{ $a->codigo }}</td>
    <td>{{ $a->nombre }}</td>
    <td class="text-end">{{ $a->stock_actual }}</td>
    <td>
        @if($a->activo)
            <span class="badge text-bg-success">Activo</span>
        @else
            <span class="badge text-bg-secondary">Inactivo</span>
        @endif
    </td>
    <td class="text-end">
        <a class="btn btn-sm btn-outline-primary" href="/ui/articulos/{{ $a->id }}">Ver</a>
        <a class="btn btn-sm btn-outline-secondary" href="/ui/articulos/{{ $a->id }}/editar">Editar</a>

       @if($a->activo)
    <form class="d-inline" method="post" action="/ui/articulos/{{ $a->id }}/eliminar"
          onsubmit="return confirm('¿Desactivar este artículo?');">
        @csrf
        <button class="btn btn-sm btn-outline-danger" type="submit">Desactivar</button>
    </form>
@else
    <form class="d-inline" method="post" action="/ui/articulos/{{ $a->id }}/reactivar"
          onsubmit="return confirm('¿Reactivar este artículo?');">
        @csrf
        <button class="btn btn-sm btn-outline-success" type="submit">Reactivar</button>
    </form>
@endif

    </td>
</tr>
@endforeach

            </tbody>
        </table>
    </div>

    <div class="card-body">
        {{ $articulos->links() }}
    </div>
</div>
@endsection
