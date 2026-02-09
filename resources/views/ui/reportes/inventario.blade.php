<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Inventario - SIGE ALM</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; }
        h2 { margin: 0 0 6px 0; }
        .meta { color:#666; font-size: 12px; margin-bottom: 14px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; font-size: 12px; vertical-align: top; }
        th { background: #f2f2f2; text-align: left; }
        .danger { font-weight: bold; }
        .btn { padding: 8px 10px; border: 1px solid #333; background: #fff; cursor: pointer; }
        .topbar { display:flex; gap:10px; align-items:center; margin: 14px 0; }
    </style>
</head>
<body>

<h2>Reporte de Inventario</h2>
<div class="meta">SIGE-ALM | Generado: {{ now() }}</div>

<div class="topbar">
    <button class="btn" onclick="window.print()">Imprimir / Guardar PDF</button>
    <a class="btn" href="/ui">Volver</a>
</div>

<table>
    <thead>
    <tr>
        <th>Código</th>
        <th>Nombre</th>
        <th>Categoría</th>
        <th>Proveedor</th>
        <th>Stock</th>
        <th>Mínimo</th>
        <th>Activo</th>
    </tr>
    </thead>
    <tbody>
    @foreach($articulos as $a)
        @php
            $bajoMinimo = ($a->stock_actual < $a->stock_minimo);
        @endphp
        <tr>
            <td>{{ $a->codigo }}</td>
            <td>{{ $a->nombre }}</td>
            <td>{{ $a->categoria?->nombre ?? '-' }}</td>
            <td>{{ $a->proveedorPreferente?->nombre ?? '-' }}</td>
            <td class="{{ $bajoMinimo ? 'danger' : '' }}">{{ $a->stock_actual }}</td>
            <td>{{ $a->stock_minimo }}</td>
            <td>{{ $a->activo ? 'Sí' : 'No' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
