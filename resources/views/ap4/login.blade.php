<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AP4 - Login</title>
</head>
<body>
  <header>
    <h1>Login</h1>
    <nav>
      <a href="/ap4">Inicio</a> |
      <a href="/ap4/articulos">Artículos</a>
    </nav>
  </header>

  <main>
    <form method="post" action="#">
      <fieldset>
        <legend>Acceso</legend>

        <div>
          <label for="email">Email</label><br>
          <input id="email" name="email" type="email" autocomplete="email" required>
        </div>

        <div>
          <label for="password">Contraseña</label><br>
          <input id="password" name="password" type="password" autocomplete="current-password" required>
        </div>

        <div style="margin-top: 12px;">
          <button type="submit">Entrar</button>
          <button type="reset">Limpiar</button>
        </div>
      </fieldset>
    </form>
  </main>
</body>
</html>
