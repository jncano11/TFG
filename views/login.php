<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión - JCSCORES</title>
  <link rel="icon" href="/TFG/public/img/icons/logo.png" type="image/png" />
  <link rel="stylesheet" href="../public/css/login.css">
</head>
<body>
  <div class="login-container">
    <h2>Iniciar Sesión</h2>

    <?php if (isset($_GET['error'])): ?>
      <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <form action="../controllers/procesar_login.php" method="POST">
      <label for="email">Email:</label>
      <input type="text" name="email" id="email" required>

      <label for="password">Contraseña:</label>
      <input type="password" name="password" id="password" required>

      <button type="submit">Entrar</button>
    </form>

    <div class="extras">
      <p><a href="register.php" class="registro-btn">¿No tienes cuenta? Regístrate</a></p>
      <p><a href="#" class="olvide">¿Has olvidado tu contraseña?</a></p>
    </div>
  </div>
</body>
</html>
