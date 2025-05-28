<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . '/../models/Partido.php';
require_once __DIR__ . '/../models/Gol.php';
require_once __DIR__ . '/../models/Tarjeta.php';

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

$goles = Gol::obtenerPorPartido($id);
$tarjetas = Tarjeta::obtenerPorPartido($id);

// Añadir tipo de evento para poder mezclar
foreach ($goles as &$gol) {
    $gol['tipo_evento'] = 'gol';
}
foreach ($tarjetas as &$tarjeta) {
    $tarjeta['tipo_evento'] = 'tarjeta';
}

// Unir y ordenar por minuto
$eventos = array_merge($goles, $tarjetas);
usort($eventos, fn($a, $b) => $a['minuto'] <=> $b['minuto']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalle del Partido</title>
  <link rel="icon" href="/TFG/public/img/icons/logo.png" type="image/png" />
  <link rel="stylesheet" href="../public/css/partido_detalle.css">
  <style>
    #resumen-detalle {
      display: none;
      margin-top: 20px;
      padding: 20px;
      background-color: #ffffff10;
      border-radius: 12px;
      backdrop-filter: blur(6px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
      color: white;
    }

    #resumen-detalle h2 {
      color: #00ff88;
      border-bottom: 1px solid #00ff88;
      padding-bottom: 5px;
      margin-top: 20px;
    }

    .eventos-container {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }

    .columna-eventos {
      width: 48%;
    }

    .evento {
      margin: 8px 0;
      font-size: 1rem;
      color: #fff;
    }

    .evento strong {
      color: #00ff88;
    }

    .icono-evento {
      width: 20px;
      height: 20px;
      vertical-align: middle;
      margin-right: 8px;
    }
  </style>
  <script>
    function toggleResumen() {
      const resumen = document.getElementById('resumen-detalle');
      resumen.style.display = (resumen.style.display === 'none') ? 'block' : 'none';
    }
  </script>
</head>
<body>
<header>
  <div class="contenedor">
    <div class="logo">JC<span class="verde">SCORES</span></div>
    <nav>
      <a href="/index.php">Volver</a>
    </nav>
  </div>
</header>

<main class="detalle-partido">    
  <h1><?= htmlspecialchars($partido['equipo_local']) ?> vs <?= htmlspecialchars($partido['equipo_visitante']) ?></h1>
  <div class="escudos">
    <div class="equipo">
      <img src="/public/<?= htmlspecialchars($partido['equipo_local_escudo']) ?>" alt="Escudo local">
      <span class="nombre"><?= htmlspecialchars($partido['equipo_local']) ?></span>
      <span class="resultado"><?= $partido['resultado_local'] ?? '-' ?></span>
    </div>
    <div class="vs">VS</div>
    <div class="equipo">
      <img src="/public/<?= htmlspecialchars($partido['equipo_visitante_escudo']) ?>" alt="Escudo visitante">
      <span class="nombre"><?= htmlspecialchars($partido['equipo_visitante']) ?></span>
      <span class="resultado"><?= $partido['resultado_visitante'] ?? '-' ?></span>
    </div>
  </div>

  <div class="info-partido">
    <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($partido['fecha'])) ?></p>
    <p><strong>Hora:</strong> <?= date('H:i', strtotime($partido['hora'])) ?></p>
    <p><strong>Estadio:</strong> <?= htmlspecialchars($partido['estadio']) ?></p>
    <p><strong>Categoría:</strong> <?= htmlspecialchars($partido['categoria']) ?></p>
    <p><strong>Árbitro:</strong> <?= htmlspecialchars($partido['arbitro']) ?></p>
  </div>

  <div class="botones-partido">
    <button type="button" class="btn" onclick="toggleResumen()">Resumen</button>
    <a href="ver_alineaciones.php?id=<?= $partido['id'] ?>" class="btn">Ver Alineaciones</a>
  </div>

  <div id="resumen-detalle">
    <h2>Detalles del Partido</h2>
    <div class="eventos-container">
      <div class="columna-eventos">
        <h3><?= htmlspecialchars($partido['equipo_local']) ?></h3>
        <?php foreach ($eventos as $evento): ?>
          <?php if ($evento['equipo'] === $partido['equipo_local']): ?>
            <?php
              if ($evento['tipo_evento'] === 'gol') {
                  $tipo = strtolower($evento['tipo'] ?? 'normal');
                  $icono = match ($tipo) {
                      'penalti' => 'penalti.png',
                      'propia puerta' => 'agol.png',
                      default => 'gol.png'
                  };
              } elseif ($evento['tipo_evento'] === 'tarjeta') {
                  $tipoTarjeta = strtolower($evento['tipo']);
                  $icono = match ($tipoTarjeta) {
                      'amarilla' => 'tarjeta_amarilla.png',
                      'roja' => 'tarjeta_roja.png',
                      default => 'tarjeta.png'
                  };
              }
            ?>
            <div class="evento" <?= $evento['tipo_evento'] === 'tarjeta' ? 'title="' . htmlspecialchars($evento['motivo']) . '"' : '' ?>>
            <img src="/TFG/public/img/icons/<?= $icono ?>" class="icono-evento">
            <?php
              $esPropia = ($evento['tipo_evento'] === 'gol') && in_array($tipo, ['propia puerta']);
              $claseJugador = $esPropia ? 'propia' : '';
            ?>
            <strong class="<?= $claseJugador ?>"><?= htmlspecialchars($evento['jugador']) ?></strong> -  
            Minuto <?= htmlspecialchars($evento['minuto']) ?>
            </div>

          <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <div class="columna-eventos">
        <h3><?= htmlspecialchars($partido['equipo_visitante']) ?></h3>
        <?php foreach ($eventos as $evento): ?>
          <?php if ($evento['equipo'] === $partido['equipo_visitante']): ?>
            <?php
              if ($evento['tipo_evento'] === 'gol') {
                  $tipo = strtolower($evento['tipo'] ?? 'normal');
                  $icono = match ($tipo) {
                      'penalti' => 'penalti.png',
                      'propia puerta' => 'agol.png',
                      default => 'gol.png'
                  };
              } elseif ($evento['tipo_evento'] === 'tarjeta') {
                  $tipoTarjeta = strtolower($evento['tipo']);
                  $icono = match ($tipoTarjeta) {
                      'amarilla' => 'tarjeta_amarilla.png',
                      'roja' => 'tarjeta_roja.png',
                      default => 'tarjeta.png'
                  };
              }
            ?>
            <div class="evento" <?= $evento['tipo_evento'] === 'tarjeta' ? 'title="' . htmlspecialchars($evento['motivo']) . '"' : '' ?>>
              <img src="/TFG/public/img/icons/<?= $icono ?>" class="icono-evento">
            <?php
              $esPropia = ($evento['tipo_evento'] === 'gol') && in_array($tipo, ['propia puerta']);
              $claseJugador = $esPropia ? 'propia' : '';
            ?>
              <strong class="<?= $claseJugador ?>"><?= htmlspecialchars($evento['jugador']) ?></strong> -  
              Minuto <?= htmlspecialchars($evento['minuto']) ?>
            </div>

          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</main>
</body>
</html>