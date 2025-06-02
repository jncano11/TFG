<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Entrenador') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Equipo</title>
    <link rel="icon" href="/TFG/public/img/icons/logo.png" type="image/png" />
    <link rel="stylesheet" href="../public/css/crear_equipo.css">
</head>
<body>
    <h1>Crear nuevo equipo</h1>

    <?php if (!empty($error)): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="/controllers/EquipoController.php" method="POST" enctype="multipart/form-data">
    <label for="nombre">Nombre del equipo:</label>
    <input type="text" name="nombre" required>

    <label for="estadio">Estadio:</label>
    <input type="text" name="estadio" required>

    <label for="escudo">Escudo:</label>
    <input type="file" name="escudo" accept="image/*" required>

    <label for="equipacion">Equipación:</label>
    <input type="file" name="equipacion" accept="image/*" required>

    <button type="submit">Crear equipo</button>
</form>


    <a href="/index.php" class="volver-inicio">← Volver al inicio</a>
</body>
</html>
