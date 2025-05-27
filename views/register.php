<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - JCSCORES</title>
  <link rel="icon" href="/TFG/public/img/icons/logo.png" type="image/png" />
  <link rel="stylesheet" href="../public/css/register.css">
  <script>
    function mostrarPosicion() {
      const rol = document.getElementById('rol').value;
      const divPosicion = document.getElementById('posicion-container');
      divPosicion.style.display = (rol === 'Jugador') ? 'block' : 'none';
    }

    window.onload = mostrarPosicion;
  </script>
</head>
<body>
  <div class="register-container">
    <h2>Crear cuenta</h2>

    <form action="../controllers/procesar_registro.php  " method="POST" enctype="multipart/form-data">
      <label for="nombre">Nombre:</label>
      <input type="text" name="nombre" id="nombre" required>

      <label for="apellidos">Apellidos:</label>
      <input type="text" name="apellidos" id="apellidos" required>

      <label for="email">Correo electrónico:</label>
      <input type="email" name="email" id="email" required>

      <label for="password">Contraseña:</label>
      <input type="password" name="password" id="password" required>

      <label for="foto">Foto de perfil:</label>
      <input type="file" name="foto" id="foto" accept="image/*" required>

      <label for="fecha_nacimiento">Fecha de nacimiento:</label>
      <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required>

      <label for="rol">Rol:</label>
      <select name="rol" id="rol" onchange="mostrarPosicion()" required>
        <option value="" disabled selected>Selecciona un rol</option>
        <option value="Usuario">Usuario</option>
      </select>

      <div id="posicion-container" style="display: none;">
        <label for="posicion">Posición:</label>
        <select name="posicion" id="posicion">
          <option value="Portero">Portero</option>
          <option value="Defensa">Defensa</option>
          <option value="Mediocentro">Mediocentro</option>
          <option value="Delantero">Delantero</option>
        </select>
      </div>

      <button type="submit">Registrarse</button>
    </form>

    <div class="extras">
      <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>
  </div>
</body>
</html>
