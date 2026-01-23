@extends('ui.layout')

@section('title', 'Crear OT')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Crear Orden de Trabajo</h1>
        <p class="text-muted mb-0">Define la OT y añade líneas de artículos con cantidad.</p>
    </div>
    <a class="btn btn-outline-secondary" href="/ui/ots">Volver</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="post" action="/ui/ots" class="row g-3">
            @csrf

            <div class="col-md-4">
                <label class="form-label" for="codigo">Código</label>
                <input class="form-control" id="codigo" name="codigo"
                       value="{{ old('codigo') }}" placeholder="OT-2026-001" required>
            </div>

            <div class="col-md-8">
                <label class="form-label" for="descripcion">Descripción</label>
                <input class="form-control" id="descripcion" name="descripcion"
                       value="{{ old('descripcion') }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label" for="fecha_apertura">Fecha apertura</label>
                <input class="form-control" id="fecha_apertura" name="fecha_apertura" type="date"
                       value="{{ old('fecha_apertura', date('Y-m-d')) }}" required>
            </div>

            <div class="col-md-4">
    <label class="form-label" for="estado">Estado</label>
   <select class="form-select" id="estado" name="estado" required>
    <option value="PENDIENTE">PENDIENTE</option>
    <option value="EN_CURSO">EN_CURSO</option>
    <option value="FINALIZADA">FINALIZADA</option>
    <option value="ARCHIVADA">ARCHIVADA</option>
</select>
</div>


            <div class="col-12">
                <hr>
                <h2 class="h6 mb-2">Líneas de artículos</h2>
                <p class="text-muted small mb-3">Añade uno o varios artículos y su cantidad.</p>

                <div id="lineas" class="vstack gap-2"></div>

                <button type="button" class="btn btn-outline-primary mt-2" onclick="addLinea()">
                    + Añadir artículo
                </button>
            </div>

            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary" type="submit">Crear OT</button>
                <a class="btn btn-outline-secondary" href="/ui/ots">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
const ARTICULOS = @json($articulos->map(fn($a) => ['id'=>$a->id,'nombre'=>$a->nombre,'codigo'=>$a->codigo])->values());

function lineaTemplate(idx) {
    const options = ARTICULOS.map(a => `<option value="${a.id}">${a.codigo} — ${a.nombre}</option>`).join('');
    return `
    <div class="border rounded p-2 bg-light">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-8">
                <label class="form-label mb-1">Artículo</label>
                <select class="form-select" name="articulos[${idx}][id_articulo]" required>
                    <option value="">Selecciona...</option>
                    ${options}
                </select>
            </div>
            <div class="col-8 col-md-3">
                <label class="form-label mb-1">Cantidad</label>
                <input class="form-control" type="number" min="1" name="articulos[${idx}][cantidad]" value="1" required>
            </div>
            <div class="col-4 col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger" onclick="removeLinea(this)">X</button>
            </div>
        </div>
    </div>`;
}

let lineaIndex = 0;

function addLinea() {
    const container = document.getElementById('lineas');
    container.insertAdjacentHTML('beforeend', lineaTemplate(lineaIndex));
    lineaIndex++;
}

function removeLinea(btn) {
    btn.closest('.border').remove();
}

// Añade 1 línea por defecto
addLinea();
</script>
@endsection
