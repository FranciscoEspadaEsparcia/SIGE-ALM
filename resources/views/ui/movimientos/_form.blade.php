@php $sel = old('id_articulo', $id_articulo); @endphp

<label class="form-label mb-0">Artículo</label>
<select class="form-select" name="id_articulo" required>
    <option value="">Selecciona...</option>
    @foreach($articulos as $a)
        <option value="{{ $a->id }}" {{ (string)$sel === (string)$a->id ? 'selected' : '' }}>
            {{ $a->codigo }} — {{ $a->nombre }}
        </option>
    @endforeach
</select>

<label class="form-label mb-0 mt-2">Cantidad</label>
<input class="form-control" type="number" min="1" name="cantidad" value="{{ old('cantidad', 1) }}" required>

<label class="form-label mb-0 mt-2">Usuario</label>
<select class="form-select" name="id_usuario" required>
    <option value="">Selecciona...</option>
    @foreach($usuarios as $u)
        <option value="{{ $u->id }}" {{ (string)old('id_usuario') === (string)$u->id ? 'selected' : '' }}>
            {{ $u->nombre }} ({{ $u->username }})
        </option>
    @endforeach
</select>

<label class="form-label mb-0 mt-2">OT (opcional)</label>
<select class="form-select" name="id_orden_trabajo">
    <option value="">(Ninguna)</option>
    @foreach($ots as $ot)
        <option value="{{ $ot->id }}" {{ (string)old('id_orden_trabajo') === (string)$ot->id ? 'selected' : '' }}>
            {{ $ot->codigo }} — {{ $ot->estado }}
        </option>
    @endforeach
</select>

<label class="form-label mb-0 mt-2">Albarán (opcional)</label>
<input class="form-control" type="number" name="id_albaran" value="{{ old('id_albaran') }}">
