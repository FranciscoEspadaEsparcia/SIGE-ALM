@extends('ui.layout')

@section('title', 'Editar artículo')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Editar artículo</h1>
        <p class="text-muted mb-0">Modificación del artículo seleccionado.</p>
    </div>
    <a class="btn btn-outline-secondary" href="/ui/articulos">Volver</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="post" action="/ui/articulos/{{ $articulo->id }}/editar" class="row g-3">
            @csrf

            <div class="col-md-4">
                <label class="form-label" for="codigo">Código</label>
                <input class="form-control" id="codigo" name="codigo"
                       value="{{ old('codigo', $articulo->codigo) }}" required>
            </div>

            <div class="col-md-8">
                <label class="form-label" for="nombre">Nombre</label>
                <input class="form-control" id="nombre" name="nombre"
                       value="{{ old('nombre', $articulo->nombre) }}" required>
            </div>

            <div class="col-12">
                <label class="form-label" for="descripcion">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $articulo->descripcion) }}</textarea>
            </div>

            <div class="col-md-3">
                <label class="form-label" for="stock_actual">Stock actual</label>
                <input class="form-control" id="stock_actual" name="stock_actual" type="number" min="0"
                       value="{{ old('stock_actual', $articulo->stock_actual) }}" required>
            </div>

            <div class="col-md-3">
                <label class="form-label" for="stock_minimo">Stock mínimo</label>
                <input class="form-control" id="stock_minimo" name="stock_minimo" type="number" min="0"
                       value="{{ old('stock_minimo', $articulo->stock_minimo) }}" required>
            </div>

            <div class="col-md-3">
                <label class="form-label" for="activo">Activo</label>
                <select class="form-select" id="activo" name="activo" required>
                    <option value="1" {{ old('activo', (string)$articulo->activo) === '1' ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ old('activo', (string)$articulo->activo) === '0' ? 'selected' : '' }}>No</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="id_categoria">Categoría</label>
                <select class="form-select" id="id_categoria" name="id_categoria" required>
                    <option value="">Selecciona...</option>
                    @foreach($categorias as $c)
                        <option value="{{ $c->id }}"
                            {{ (string)old('id_categoria', $articulo->id_categoria) === (string)$c->id ? 'selected' : '' }}>
                            {{ $c->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label" for="id_proveedor_preferente">Proveedor preferente (opcional)</label>
                <select class="form-select" id="id_proveedor_preferente" name="id_proveedor_preferente">
                    <option value="">(Ninguno)</option>
                    @foreach($proveedores as $p)
                        <option value="{{ $p->id }}"
                            {{ (string)old('id_proveedor_preferente', $articulo->id_proveedor_preferente) === (string)$p->id ? 'selected' : '' }}>
                            {{ $p->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary" type="submit">Guardar cambios</button>
                <a class="btn btn-outline-secondary" href="/ui/articulos">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
