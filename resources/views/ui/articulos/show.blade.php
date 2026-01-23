@extends('ui.layout')

@section('title', 'Detalle artículo')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Detalle de artículo</h1>
        <p class="text-muted mb-0">{{ $articulo->codigo }} — {{ $articulo->nombre }}</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="/ui/articulos">Volver</a>
        <a class="btn btn-outline-primary" href="/ui/articulos/{{ $articulo->id }}/editar">Editar</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6">Datos</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Código</dt>
                    <dd class="col-sm-8">{{ $articulo->codigo }}</dd>

                    <dt class="col-sm-4">Nombre</dt>
                    <dd class="col-sm-8">{{ $articulo->nombre }}</dd>

                    <dt class="col-sm-4">Descripción</dt>
                    <dd class="col-sm-8">{{ $articulo->descripcion ?: '-' }}</dd>

                    <dt class="col-sm-4">Categoría</dt>
                    <dd class="col-sm-8">{{ optional($articulo->categoria)->nombre ?? '-' }}</dd>

                    <dt class="col-sm-4">Proveedor</dt>
                    <dd class="col-sm-8">{{ optional($articulo->proveedorPreferente)->nombre ?? '-' }}</dd>

                    <dt class="col-sm-4">Stock actual</dt>
                    <dd class="col-sm-8">{{ $articulo->stock_actual }}</dd>

                    <dt class="col-sm-4">Stock mínimo</dt>
                    <dd class="col-sm-8">{{ $articulo->stock_minimo }}</dd>

                    <dt class="col-sm-4">Estado</dt>
                    <dd class="col-sm-8">
                        @if($articulo->activo)
                            <span class="badge text-bg-success">Activo</span>
                        @else
                            <span class="badge text-bg-secondary">Inactivo</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6">Acciones</h2>

                @if($articulo->activo)
                    <form method="post" action="/ui/articulos/{{ $articulo->id }}/eliminar"
                          onsubmit="return confirm('¿Desactivar este artículo?');">
                        @csrf
                        <button class="btn btn-danger w-100" type="submit">Desactivar artículo</button>
                    </form>
                @else
                    <p class="text-muted mb-0">Este artículo ya está inactivo.</p>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
