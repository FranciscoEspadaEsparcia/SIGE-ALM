<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Movimientos - SIGE ALM</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        h2 { margin: 0 0 6px 0; }
        .meta { color:#666; font-size: 12px; margin-bottom: 14px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; font-size: 12px; vertical-align: top; }
        th { background: #f2f2f2; text-align: left; }
        .btn { padding: 8px 10px; border: 1px solid #333; background: #fff; cursor: pointer; text-decoration:none; color:#000; }
        .topbar { display:flex; gap:10px; align-items:center; margin: 14px 0; flex-wrap: wrap; }
        .filters { font-size: 12px; color:#333; }
        input, select { padding: 6px; }
        .small { font-size: 12px; color:#444; }
    </style>
</head>
<body>

@php
    // Blindaje total por si el controller no envía algo
    $tipo  = $tipo  ?? null;
    $desde = $desde ?? null;
    $hasta = $hasta ?? null;
@endphp

<h2>Reporte de Movimientos</h2>

<div class="meta">
    SIGE-ALM | Generado: {{ now() }}
    @if(!empty($tipo))  | Tipo: {{ $tipo }} @endif
    @if(!empty($desde)) | Desde: {{ $desde }} @endif
    @if(!empty($hasta)) | Hasta: {{ $hasta }} @endif
</div>

<div class="topbar">
    <button class="btn" onclick="window.print()">Imprimir / Guardar PDF</button>
    <a class="btn" href="/ui">Volver</a>

    <form class="filters" method="get" action="/ui/reportes/movimientos">
        <label>Tipo:
            <select name="tipo">
                <option value="" {{ empty($tipo) ? 'selected' : '' }}>Todos</option>
                <option value="ENTRADA" {{ $tipo === 'ENTRADA' ? 'selected' : '' }}>ENTRADA</option>
                <option value="SALIDA" {{ $tipo === 'SALIDA' ? 'selected' : '' }}>SALIDA</option>
                <option value="DEVOLUCION" {{ $tipo === 'DEVOLUCION' ? 'selected' : '' }}>DEVOLUCION</option>
            </select>
        </label>

        <label>Desde:
            <input type="date" name="desde" value="{{ $desde }}">
        </label>

        <label>Hasta:
            <input type="date" name="hasta" value="{{ $hasta }}">
        </label>

        <button class="btn" type="submit">Filtrar</button>
        <a class="btn" href="/ui/reportes/movimientos">Limpiar</a>
    </form>
</div>

<table>
    <thead>
    <tr>
        <th>Fecha</th>
        <th>Tipo</th>
        <th>Artículo</th>
        <th>Cantidad</th>
        <th>Usuario</th>
        <th>OT</th>
    </tr>
    </thead>
    <tbody>
    @forelse($movimientos as $m)
        <tr>
            <td>{{ $m->fecha_hora }}</td>
            <td>{{ $m->tipo }}</td>
            <td>{{ $m->articulo?->codigo }} - {{ $m->articulo?->nombre }}</td>
            <td>{{ $m->cantidad }}</td>
            <td>{{ $m->usuario?->nombre ?? $m->id_usuario }}</td>
            <td>{{ $m->ordenTrabajo?->codigo ?? '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="small">No hay movimientos para los filtros seleccionados.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<p class="small">Mostrando {{ is_countable($movimientos) ? count($movimientos) : 0 }} registros (máx. 500).</p>

</body>
</html>
