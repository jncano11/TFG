<?php
session_start();
require_once __DIR__ . '/../config/bd.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Equipo.php';
require_once __DIR__ . '/../models/Partido.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$conexion     = conectarBD(); // retorna PDO
$usuarioModel = new Usuario($conexion);
$equipoModel  = new Equipo();

// Usuario logueado
$usuarioLog = $_SESSION['usuario'] ?? null;
$rolActual  = $usuarioLog['rol']  ?? '';
$miEquipoId = $usuarioLog['equipo_id'] ?? null;

// ID de equipo a mostrar
$equipoId = $_GET['id'] ?? null;
if (!$equipoId) {
    echo "Equipo no especificado.";
    exit;
}

// Datos del equipo y plantilla
$equipo     = $equipoModel->obtenerDetallesPorId($equipoId);
$jugadores  = Usuario::obtenerJugadoresPorEquipo($equipoId);
$entrenador = $usuarioModel->obtenerPorId($equipo['entrenador_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil del Equipo - <?= htmlspecialchars($equipo['nombre']) ?></title>
    <link rel="icon" href="/TFG/public/img/icons/logo.png" type="image/png" />
    <link rel="stylesheet" href="/public/css/equipo_perfil.css">
    <style>
      .ultimos-resultados { text-align:center; margin: 20px 0; }
      .resultados-icons { display:flex; justify-content:center; gap:8px; }
      .icon { width:32px; height:32px; line-height:32px; border-radius:4px; color:#fff; font-weight:bold; }
      .win  { background:green; }
      .draw { background:orange; }
      .loss { background:red; }
    </style>
</head>
<body>

<a href="../views/index.php" class="volver-home" title="Volver al inicio">&#8592;</a>

<div class="contenedor">
    <h2><?= htmlspecialchars($equipo['nombre']) ?></h2>

    <div class="contenedor-imagenes">
        <?php if ($rolActual === 'Admin' || ($rolActual === 'Entrenador' && $miEquipoId == $equipoId)): ?>
            <form action="../controllers/CambiarEscudo.php" method="post" enctype="multipart/form-data" class="form-cambiar-escudo">
                <input type="hidden" name="equipo_id" value="<?= $equipo['id'] ?>">
                <label class="btn-cambiar-escudo">
                    <img class="escudo" src="/TFG/public/<?= htmlspecialchars($equipo['escudo']) ?>" alt="Escudo del equipo">
                    <input type="file" name="escudo" accept="image/*" onchange="this.form.submit()" hidden>
                    <span class="overlay">Cambiar escudo</span>
                </label>
            </form>
        <?php else: ?>
            <img class="escudo" src="/TFG/public/<?= htmlspecialchars($equipo['escudo']) ?>" alt="Escudo del equipo">
        <?php endif; ?>

        <?php if ($rolActual === 'Admin' || ($rolActual === 'Entrenador' && $miEquipoId == $equipoId)): ?>
            <form action="../controllers/CambiarEquipacion.php" method="post" enctype="multipart/form-data" class="form-cambiar-equipacion">
                <input type="hidden" name="equipo_id" value="<?= $equipo['id'] ?>">
                <label class="btn-cambiar-equipacion">
                    <img class="equipacion" src="/TFG/public/<?= htmlspecialchars($equipo['equipacion'] ?? 'img/default_kit.png') ?>" alt="Equipación del equipo">
                    <input type="file" name="equipacion" accept="image/*" onchange="this.form.submit()" hidden>
                    <span class="overlay">Cambiar equipación</span>
                </label>
            </form>
        <?php else: ?>
            <img class="equipacion" src="/TFG/public/<?= htmlspecialchars($equipo['equipacion'] ?? 'img/default_kit.png') ?>" alt="Equipación del equipo">
        <?php endif; ?>
    </div>

    <div class="botones">
        <button id="btn-datos" class="activo" onclick="mostrarSeccion('datos')">DATOS</button>
        <button id="btn-plantilla" onclick="mostrarSeccion('plantilla')">PLANTILLA</button>
    </div>

    <!-- ULTIMOS RESULTADOS -->
    <div class="ultimos-resultados">
      <h3>ÚLTIMOS RESULTADOS</h3>
      <div class="resultados-icons">
        <?php
          $ultimos = Partido::obtenerUltimosPorEquipo($equipoId, 6);
          foreach ($ultimos as $u) {
            $esLocal = $u['equipo_local_id']==$equipoId;
            $gf = $esLocal ? $u['resultado_local'] : $u['resultado_visitante'];
            $gc = $esLocal ? $u['resultado_visitante'] : $u['resultado_local'];
            if ($gf > $gc) { $res='V'; $c='win'; }
            elseif ($gf < $gc) { $res='D'; $c='loss'; }
            else { $res='E'; $c='draw'; }
            echo "<div class='icon $c'>$res</div>";
          }
        ?>
      </div>
    </div>

    <div id="seccion-datos" class="seccion activa">
        <h3>Datos del equipo</h3>
        <p><strong>ID:</strong> <?= $equipo['id'] ?></p>
        <p><strong>Nombre:</strong> <?= htmlspecialchars($equipo['nombre']) ?></p>
        <p><strong>Estadio:</strong> <?= htmlspecialchars($equipo['estadio'] ?? 'No especificado') ?></p>
    </div>

    <div id="seccion-plantilla" class="seccion">
        <h3>Entrenador</h3>
        <p><?= htmlspecialchars($entrenador['nombre'] ?? 'Sin asignar') ?></p>

        <h3>Jugadores</h3>
        <?php if ($jugadores): ?>
            <?php foreach ($jugadores as $jugador): ?>
            <div class="jugador">
                <img src="/TFG/public/img/icons/perfil.jpg" alt="Perfil" class="perfil-foto">
                <div>
                    <strong><?= htmlspecialchars($jugador['nombre']) ?> <?= htmlspecialchars($jugador['apellidos']) ?></strong><br>
                    Posición: <?= htmlspecialchars($jugador['posicion']) ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay jugadores registrados en este equipo.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function mostrarSeccion(s) {
    document.querySelectorAll('.seccion').forEach(x=>x.classList.remove('activa'));
    document.querySelectorAll('.botones button').forEach(b=>b.classList.remove('activo'));
    document.getElementById('seccion-'+s).classList.add('activa');
    document.getElementById('btn-'+s).classList.add('activo');
}
</script>

</body>
</html>
