@extends('ui.layout')

@section('title', 'Inicio')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="p-4 bg-white rounded-3 shadow-sm border">
            <h1 class="h3 mb-1">Panel de control</h1>
            <p class="text-muted mb-0">Acceso rápido a las funciones principales del sistema.</p>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h2 class="h5">Artículos</h2>
                <p class="text-muted">Consultar, crear, editar y eliminar artículos del inventario.</p>
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
@endsection
