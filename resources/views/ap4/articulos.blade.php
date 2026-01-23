<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AP4 - Artículos</title>
</head>
<body>
  <header>
    <h1>Listado de artículos</h1>
    <nav>
      <a href="/ap4">Inicio</a> |
      <a href="/ap4/articulos/crear">Crear artículo</a>
    </nav>
  </header>

  <main>
    @if(session('status'))
      <p role="status">{{ session('status') }}</p>
    @endif

    <table border="1" cellpadding="8">
      <caption>Artículos registrados</caption>
      <thead>
        <tr>
          <th scope="col">Código</th>
          <th scope="col">Nombre</th>
          <th scope="col">Categoría</th>
          <th scope="col">Stock</th>
        </tr>
      </thead>
      <tbody>
        @foreach($articulos as $a)
          <tr>
            <td>{{ $a['codigo'] }}</td>
            <td>{{ $a['nombre'] }}</td>
            <td>{{ $a['categoria'] }}</td>
            <td>{{ $a['stock'] }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </main>
</body>
</html>
