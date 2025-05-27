<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Entrenador') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../models/Partido.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar_partido') {
    $id = $_POST['id'];
    $resultado_local = $_POST['resultado_local'];
    $resultado_visitante = $_POST['resultado_visitante'];
    $estadio = $_POST['estadio'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];

    $exito = Partido::actualizarDatosBasicos($id, $resultado_local, $resultado_visitante, $estadio, $fecha, $hora);

    header("Location: ../views/editar_partido.php?id=$id&msg=guardado");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID de partido no proporcionado.";
    exit();
}

$partido = Partido::obtenerPorId($id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Partido</title>
    <link rel="icon" href="/TFG/public/img/icons/logo.png" type="image/png" />
    <link rel="stylesheet" href="../public/css/editar_partido.css">
</head>
<body>

<div class="editar-partido-container">
<a href="index.php" class="btn-volver" title="Volver"></a>
    <h1>Editar Partido</h1>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'guardado'): ?>
        <p class="mensaje-exito">Cambios guardados correctamente.</p>
    <?php endif; ?>
    <form action="../controllers/PartidoController.php" method="POST">
        <input type="hidden" name="accion" value="editar_partido">
        <input type="hidden" name="id" value="<?= htmlspecialchars($partido['id']) ?>">

        <label>Lugar (Estadio):</label>
        <input type="text" name="estadio" value="<?= htmlspecialchars($partido['estadio']) ?>" required>

        <label>Fecha:</label>
        <input type="date" name="fecha" value="<?= htmlspecialchars($partido['fecha']) ?>" required>

        <label>Hora:</label>
        <input type="time" name="hora" value="<?= htmlspecialchars($partido['hora']) ?>" required>

        <label>Categoría:</label>
        <select name="categoria" required>
        <?php 
        $categorias = ['Regional Preferente', '1ªRegional', '2ªRegional', '3ªRegional'];
        foreach ($categorias as $cat):
            $selected = ($partido['categoria'] === $cat) ? 'selected' : '';
        ?>
        <option value="<?= $cat ?>" <?= $selected ?>><?= $cat ?></option>
        <?php endforeach; ?>
        </select>


        <label>Resultado Local:</label>
        <input type="number" name="resultado_local" value="<?= htmlspecialchars($partido['resultado_local']) ?>" required min="0">

        <label>Resultado Visitante:</label>
        <input type="number" name="resultado_visitante" value="<?= htmlspecialchars($partido['resultado_visitante']) ?>" required min="0">

        <br><br>
        <button type="submit">Guardar Cambios</button>
    </form>
</div>
</body>
</html>
