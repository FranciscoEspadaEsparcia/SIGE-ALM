@extends('ui.layout')

@section('title', 'Artículos')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-semibold">Artículos</h2>
    <div class="text-sm" style="color: var(--color-muted);">
      Total: {{ $page['total'] ?? '?' }}
    </div>
  </div>

  <div class="overflow-auto rounded-lg border" style="border-color: var(--color-border);">
    <table class="w-full text-sm">
      <thead style="background: rgba(0,0,0,.25);">
        <tr class="text-left">
          <th class="p-3">ID</th>
          <th class="p-3">Código</th>
          <th class="p-3">Nombre</th>
          <th class="p-3">Stock</th>
          <th class="p-3"></th>
        </tr>
      </thead>
      <tbody>
      @foreach(($page['data'] ?? []) as $a)
        <tr class="border-t" style="border-color: var(--color-border);">
          <td class="p-3">{{ $a['id'] }}</td>
          <td class="p-3 font-mono">{{ $a['codigo'] }}</td>
          <td class="p-3">{{ $a['nombre'] }}</td>
          <td class="p-3">{{ $a['stock_actual'] }}</td>
          <td class="p-3">
            <a class="px-3 py-1 rounded-md border text-xs"
               style="border-color: var(--color-border); color: var(--color-text);"
               href="/ui/articulos/{{ $a['id'] }}">Ver</a>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
@endsection
