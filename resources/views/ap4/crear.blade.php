<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AP4 - Crear artículo</title>
</head>
<body>
  <header>
    <h1>Crear artículo</h1>
    <nav>
      <a href="/ap4">Inicio</a> |
      <a href="/ap4/articulos">Volver al listado</a>
    </nav>
  </header>

  <main>
    <form method="post" action="/ap4/articulos/crear">
      @csrf
      <fieldset>
        <legend>Datos del artículo</legend>

        <div>
          <label for="codigo">Código</label><br>
          <input id="codigo" name="codigo" type="text" required>
        </div>

        <div>
          <label for="nombre">Nombre</label><br>
          <input id="nombre" name="nombre" type="text" required>
        </div>

        <div>
          <label for="categoria">Categoría</label><br>
          <select id="categoria" name="categoria" required>
            <option value="">Selecciona una categoría</option>
            <option>Herramientas</option>
            <option>EPI</option>
            <option>Material</option>
          </select>
        </div>

        <div>
          <label for="stock">Stock inicial</label><br>
          <input id="stock" name="stock" type="number" min="0" required>
        </div>

        <div style="margin-top: 12px;">
          <button type="submit">Guardar</button>
          <a href="/ap4/articulos">Cancelar</a>
        </div>
      </fieldset>
    </form>
  </main>
</body>
</html>
