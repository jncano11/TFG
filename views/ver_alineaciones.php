<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../models/Partido.php';
require_once __DIR__ . '/../models/Alineacion.php';
require_once __DIR__ . '/../models/Equipo.php'; // NECESARIO para obtener el escudo

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID de partido no proporcionado.";
    exit();
}

$partido = Partido::obtenerPorId($id);
if (!$partido) {
    echo "Partido no encontrado.";
    exit();
}

$equipoModel = new Equipo();
$equipoLocal = $equipoModel->obtenerPorId($partido['equipo_local_id']);
$equipoVisitante = $equipoModel->obtenerPorId($partido['equipo_visitante_id']);


$alineacionModel = new Alineacion();
$alineaciones = $alineacionModel->obtenerPorPartido($id);

$local = null;
$visitante = null;

foreach ($alineaciones as $alin) {
    if ($alin['tipo'] === 'local') {
        $local = $alin;
    } elseif ($alin['tipo'] === 'visitante') {
        $visitante = $alin;
    }
}

function jugadoresArray($cadena) {
    return array_filter(array_map('trim', explode(',', $cadena)));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alineaciones - <?= htmlspecialchars($partido['equipo_local']) ?> vs <?= htmlspecialchars($partido['equipo_visitante']) ?></title>
    <link rel="icon" href="/TFG/public/img/icons/logo.png" type="image/png" />
    <link rel="stylesheet" href="../public/css/ver_alineaciones.css">
</head>
<body>
<header>
    <div class="contenedor">
        <div class="logo">JC<span class="verde">SCORES</span></div>
        <nav>
            <a href="partido_detalle.php?id=<?= $id ?>">Volver al Partido</a>
        </nav>
    </div>
</header>

<main class="alineaciones-campo">
    <h1>Alineaciones del partido</h1>

    <!-- Eliminamos el div .info-equipos para quitar los escudos fuera del campo -->

    <div class="campo-futbol">
        <!-- Escudos y nombres dentro del campo, en esquinas y abajo de los escudos -->
        <img class="escudo-local-campo" src="/public/<?= htmlspecialchars($equipoLocal['escudo']) ?>" alt="Escudo Local">
        <img class="escudo-visitante-campo" src="/public/<?= htmlspecialchars($equipoVisitante['escudo']) ?>" alt="Escudo Visitante">

        <div class="nombre-local-campo"><?= htmlspecialchars($partido['equipo_local']) ?></div>
        <div class="nombre-visitante-campo"><?= htmlspecialchars($partido['equipo_visitante']) ?></div>

        <div class="lineas"></div>

        <!-- LOCAL -->
        <div class="local">
            <?php if ($local): ?>
                <?php
                    $jugadores = jugadoresArray($local['jugadores']);
                    array_unshift($jugadores, "Portero");
                    $formacion = explode('-', $local['formacion']);
                    $index = 1;
                ?>
                <!-- Portero -->
                <div class="fila portero">
                    <div class="jugador"><?= htmlspecialchars($jugadores[0]) ?></div>
                </div>
                <!-- Resto de líneas -->
                <?php foreach ($formacion as $linea): ?>
                    <div class="fila">
                        <?php for ($i = 0; $i < (int)$linea; $i++): ?>
                            <div class="jugador"><?= htmlspecialchars($jugadores[$index] ?? '?') ?></div>
                            <?php $index++; ?>
                        <?php endfor; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- VISITANTE -->
        <div class="visitante">
            <?php if ($visitante): ?>
                <?php
                    $jugadores = jugadoresArray($visitante['jugadores']);
                    array_unshift($jugadores, "Portero");
                    $formacion = explode('-', $visitante['formacion']);
                    $index = 1;
                ?>
                <!-- Portero -->
                <div class="fila portero">
                    <div class="jugador"><?= htmlspecialchars($jugadores[0]) ?></div>
                </div>
                <!-- Resto de líneas -->
                <?php foreach ($formacion as $linea): ?>
                    <div class="fila">
                        <?php for ($i = 0; $i < (int)$linea; $i++): ?>
                            <div class="jugador"><?= htmlspecialchars($jugadores[$index] ?? '?') ?></div>
                            <?php $index++; ?>
                        <?php endfor; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>
</body>
</html>
