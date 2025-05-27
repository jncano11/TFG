<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Entrenador') {
    header('Location: login.php');
    exit();
}

$partido_id = $_GET['partido_id'] ?? null;
$equipo_local_id = $_GET['equipo_local_id'] ?? null;
$equipo_visitante_id = $_GET['equipo_visitante_id'] ?? null;

if (!$partido_id || !$equipo_local_id || !$equipo_visitante_id) {
    echo "Faltan parámetros para crear alineaciones.";
    exit();
}

// Formaciones disponibles
$formaciones = ['4-4-2', '4-3-3', '3-5-2', '5-3-2', '3-4-3', '4-2-3-1'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Alineación</title>
    <link rel="icon" href="/TFG/public/img/icons/logo.png" type="image/png" />
    <link rel="stylesheet" href="../public/css/añadir_alineacion.css">
</head>
<body>
<div class="container-alineacion">
    <h1>Crear Alineación para el Partido</h1>
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <p style="color: green; font-weight: bold;">Alineación añadida correctamente.</p>
    <?php endif; ?>


    <!-- Alineación Local -->
    <h2>Alineación Local</h2>
    <form action="../controllers/AlineacionController.php" method="POST">
    <input type="hidden" name="partido_id" value="<?= htmlspecialchars($partido_id) ?>">
    <input type="hidden" name="equipo_id" value="<?= htmlspecialchars($equipo_local_id) ?>">
    <input type="hidden" name="tipo" value="local">
    <!-- NUEVOS CAMPOS -->
    <input type="hidden" name="equipo_local_id" value="<?= htmlspecialchars($equipo_local_id) ?>">
    <input type="hidden" name="equipo_visitante_id" value="<?= htmlspecialchars($equipo_visitante_id) ?>">

    <label for="formacion_local">Formación:</label>
    <select name="formacion" id="formacion_local" required>
        <option value="">Selecciona una formación</option>
        <?php foreach ($formaciones as $formacion): ?>
            <option value="<?= $formacion ?>"><?= $formacion ?></option>
        <?php endforeach; ?>
    </select>

    <label for="alineacion_local">Jugadores (separados por coma):</label>
    <textarea name="alineacion" id="alineacion_local" rows="4" required></textarea>

    <button type="submit">Guardar Alineación Local</button>
</form>


    <hr>

    <!-- Alineación Visitante -->
    <h2>Alineación Visitante</h2>
    <form action="../controllers/AlineacionController.php" method="POST">
    <input type="hidden" name="partido_id" value="<?= htmlspecialchars($partido_id) ?>">
    <input type="hidden" name="equipo_id" value="<?= htmlspecialchars($equipo_visitante_id) ?>">
    <input type="hidden" name="tipo" value="visitante">
    <!-- NUEVOS CAMPOS -->
    <input type="hidden" name="equipo_local_id" value="<?= htmlspecialchars($equipo_local_id) ?>">
    <input type="hidden" name="equipo_visitante_id" value="<?= htmlspecialchars($equipo_visitante_id) ?>">

    <label for="formacion_visitante">Formación:</label>
    <select name="formacion" id="formacion_visitante" required>
        <option value="">Selecciona una formación</option>
        <?php foreach ($formaciones as $formacion): ?>
            <option value="<?= $formacion ?>"><?= $formacion ?></option>
        <?php endforeach; ?>
    </select>

    <label for="alineacion_visitante">Jugadores (separados por coma):</label>
    <textarea name="alineacion" id="alineacion_visitante" rows="4" required></textarea>

    <button type="submit">Guardar Alineación Visitante</button>
</form>


    <a href="index.php" class="volver-btn">Volver al inicio</a>
</div>
</body>
</html>
