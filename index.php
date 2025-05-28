<?php
session_start();
date_default_timezone_set('Europe/Madrid');
if (!isset($_SESSION['usuario'])) {
    header("Location: /views/login.php");
    exit();
}
$nombreUsuario = $_SESSION['usuario']['nombre'];
$rol = $_SESSION['usuario']['rol'];

require_once __DIR__ . '/models/Partido.php';

$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == 1;

$fechaSeleccionada = $_GET['fecha'] ?? date('Y-m-d');

$partidos = Partido::obtenerPorFecha($fechaSeleccionada);

if ($isAjax) {
  ?>
  <style>
    .partido.categoria-regional-preferente {
  background-color:rgb(18, 17, 51);
  border-left: 5px solid #38ff94; /* verde neón */
}

.partido.categoria-1ª-regional {
  background-color:red;
  border-left: 5px solid #3ee27f;
}

.partido.categoria-2ª-regional {
  background-color:rgb(70, 12, 12);
  border-left: 5px solid #2ecc71;
}

.partido.categoria-3ª-regional {
  background-color: #141c24;
  border-left: 5px solid #27f16b;
}

.partido.categoria-sin-categoria {
  background-color: #1a1a1a;
  border-left: 5px solid #555;
}

  </style>

  <h2>Partidos para <?= date('d/m/Y', strtotime($fechaSeleccionada)) ?></h2>

  <?php if (empty($partidos)): ?>
    <p>No hay partidos programados para esta fecha.</p>
  <?php else: ?>
    <?php foreach ($partidos as $partido): ?>
      <?php
        $fechaHoraPartido = strtotime($partido['fecha'] . ' ' . $partido['hora']);
        $ahora = time();
        $partidoEsFuturo = $fechaHoraPartido > $ahora;

        $categoria = strtolower(str_replace(' ', '-', $partido['categoria'] ?? 'sin-categoria'));
      ?>
      <div class="partido-link" data-id="<?= $partido['id'] ?>">
        <div class="partido categoria-<?= $categoria ?>">
          <div class="equipos">
            <div class="equipo local">
              <a href="	/views/equipo_perfil.php?id=<?= urlencode($partido['equipo_local_id']) ?>">
                <img src="/uploads<?= htmlspecialchars($partido['equipo_local_escudo'] ?? 'uploads/default.png') ?>" alt="Escudo <?= htmlspecialchars($partido['equipo_local']) ?>" />
                <span><?= htmlspecialchars($partido['equipo_local']) ?></span>
              </a>
            </div>
            <div class="vs">vs</div>
            <div class="equipo visitante">
              <a href="/views/equipo_perfil.php?id=<?= urlencode($partido['equipo_visitante_id']) ?>">
                <img src="/uploads<?= htmlspecialchars($partido['equipo_visitante_escudo'] ?? 'uploads/default.png') ?>" alt="Escudo <?= htmlspecialchars($partido['equipo_visitante']) ?>" />
                <span><?= htmlspecialchars($partido['equipo_visitante']) ?></span>
              </a>
            </div>
          </div>
          <div class="info-partido">
            <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($partido['fecha'])) ?> a las <?= date('H:i', strtotime($partido['hora'])) ?></p>
            <p><strong>Estadio:</strong> <?= htmlspecialchars($partido['estadio']) ?></p>
            <p><strong>Árbitro:</strong> <?= htmlspecialchars($partido['arbitro']) ?></p>
            <p class="resultado"><strong>Resultado:</strong> <?= $partido['resultado_local'] ?? '-' ?> - <?= $partido['resultado_visitante'] ?? '-' ?></p>
          </div>

          <?php if ($rol === 'Entrenador'): ?>
            <div class="boton-editar-partido">
              <a href="/views/editar_partido.php?id=<?= urlencode($partido['id']) ?>" class="btn-editar">Editar Partido</a>
            </div>
            <div class="botones-entrenador">
              <?php if ($partidoEsFuturo): ?>
                <a href="/views/añadir_alineaciones.php?partido_id=<?= urlencode($partido['id']) ?>&equipo_local_id=<?= urlencode($partido['equipo_local_id']) ?>&equipo_visitante_id=<?= urlencode($partido['equipo_visitante_id']) ?>" class="btn-alineacion">
                  Añadir Alineación
                </a>
              <?php endif; ?>
              <a href="/views/asignar_eventos.php?id=<?= urlencode($partido['id']) ?>" class="btn-detalles">Añadir Detalles</a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif;
  exit;
}

// Código normal (carga toda la página)

// Construir lista de días: 3 antes y 50 después
$dias = [];
for ($i = -3; $i <= 50; $i++) {
    $fecha = strtotime("$i days");
    $dias[] = [
        'fecha' => date('Y-m-d', $fecha),
        'label' => strtoupper(date('D d.m', $fecha))
    ];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>JCSCORES - Resultados en vivo</title>
  <link rel="icon" href="/TFG/public/img/icons/logo.png" type="image/png" />
  <link rel="stylesheet" href="/public/css/styles.css" />
  <style>
    .dias-navegacion {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin: 20px 0;
    }
    .flecha {
      background: #eee;
      border: none;
      font-size: 1.5rem;
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.2s;
    }
    .flecha:disabled {
      opacity: 0.4;
      cursor: default;
    }
    .flecha:hover:not(:disabled) {
      background: #ddd;
    }
    .dias-deslizables {
      display: flex;
      gap: 8px;
      overflow: hidden;
    }
    .dia-boton {
      flex: 0 0 auto;
      padding: 8px 12px;
      background: #eee;
      border: none;
      border-radius: 20px;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s;
    }
    .dia-boton.activo {
      background: #4caf50;
      color: #fff;
    }
    .dia-boton:hover:not(.activo) {
      background: #c8e6c9;
    }

    .buscador-equipos {
  position: relative;
  display: inline-block;
  margin-left: 20px;
}

.buscador-equipos input {
  padding: 6px;
  border-radius: 4px;
  border: 1px solid #ccc;
  width: 200px;
}

.resultados-buscador {
  position: absolute;
  top: 36px;
  background: white;
  border: 1px solid #ccc;
  max-height: 200px;
  overflow-y: auto;
  width: 100%;
  z-index: 999;
}

.resultados-buscador div {
  padding: 6px;
  cursor: pointer;
}

.resultados-buscador div:hover {
  background-color: #f0f0f0;
}
  </style>
</head>
<body>
  <header>
    <div class="logo">JC<span class="verde">SCORES</span></div>
    <nav>
      <a href="/views/perfil.php">Ver Perfil</a>
      <a id="btnGenerarPDF" href="#" target="_blank" class="btn-pdf">Generar PDF</a>
      <a href="/views/logout.php">Cerrar sesión (<?= htmlspecialchars($nombreUsuario) ?>)</a>
    </nav>
  </header>

  <?php if ($rol === 'Admin'): ?>
  <div class="admin-panel">
    <h2>Panel de administrador</h2>
    <div class="admin-options">
      <a href="/views/crear_partido.php">Gestionar partidos</a>
      <a href="/views/crear_equipo.php">Crear equipo</a>
      <a href="/views/administrar_usuarios.php">Usuarios registrados</a>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($rol === 'Entrenador'): ?>
  <div class="entrenador-panel">
    <h2>Panel de entrenador</h2>
    <div class="entrenador-options">
      <a href="/views/crear_equipo.php">Crear equipo</a>
      <a href="/views/ver_plantilla.php">Ver plantilla</a>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($rol !== 'Admin' && $rol !== 'Entrenador'): ?>
  <div class="bienvenida-container">
    <h1 class="bienvenida-text">Bienvenido, <?= htmlspecialchars($nombreUsuario) ?> 👋</h1>
  </div>
  <?php endif; ?>

  <div class="menu-secundario">
    <a href="/views/clasificacionRP.php">REGIONAL PREFERENTE</a>
    <a href="/views/clasificacion.php">1ª REGIONAL</a>
    <a href="/views/clasificacion2R.php">2ª REGIONAL</a>
    <a href="/views/clasificacionM.php">MUNDIAL CLUBES</a>
  </div>

  <!-- Navegación de días con flechas -->
  <div class="dias-navegacion">
    <button id="prevDias" class="flecha">‹</button>
    <div class="dias-deslizables" id="diasDeslizables"></div>
    <button id="nextDias" class="flecha">›</button>
  </div>

  <section class="partidos-base" id="contenedorPartidos">
    <!-- Aquí se cargan dinámicamente los partidos por JS -->
  </section>

  <script>
    const dias = <?= json_encode($dias, JSON_UNESCAPED_UNICODE) ?>;
    let windowStart = 0;
    const windowSize = 7;
    const contenedor = document.getElementById('diasDeslizables');
    const btnPrev = document.getElementById('prevDias');
    const btnNext = document.getElementById('nextDias');
    const hoy = '<?= $fechaSeleccionada ?>';
    const idxHoy = dias.findIndex(d => d.fecha === hoy);
    windowStart = Math.min(Math.max(idxHoy - 3, 0), dias.length - windowSize);

    function renderDias() {
      contenedor.innerHTML = '';
      for (let i = windowStart; i < windowStart + windowSize; i++) {
        const d = dias[i];
        const btn = document.createElement('button');
        btn.className = 'dia-boton' + (d.fecha === hoy ? ' activo' : '');
        btn.textContent = d.label;
        btn.dataset.fecha = d.fecha;
        btn.addEventListener('click', async () => {
          document.querySelectorAll('.dia-boton').forEach(b => b.classList.remove('activo'));
          btn.classList.add('activo');
          const html = await fetch(`index.php?fecha=${d.fecha}&ajax=1`).then(r => r.text());
          document.getElementById('contenedorPartidos').innerHTML = html;
        });
        contenedor.appendChild(btn);
      }
      btnPrev.disabled = windowStart === 0;
      btnNext.disabled = windowStart + windowSize >= dias.length;
    }

    btnPrev.addEventListener('click', () => {
      windowStart = Math.max(windowStart - windowSize, 0);
      renderDias();
    });
    btnNext.addEventListener('click', () => {
      windowStart = Math.min(windowStart + windowSize, dias.length - windowSize);
      renderDias();
    });

    renderDias();
    (async () => {
      const activo = contenedor.querySelector('.dia-boton.activo');
      if (activo) {
        const html = await fetch(`index.php?fecha=${activo.dataset.fecha}&ajax=1`).then(r => r.text());
        document.getElementById('contenedorPartidos').innerHTML = html;
      }
    })();

    // Delegar clic en partido-link
    document.addEventListener('click', function(e) {
      const partidoDiv = e.target.closest('.partido-link');
      if (partidoDiv && !e.target.closest('a')) {
        const partidoId = partidoDiv.dataset.id;
        window.location.href = `/views/partido_detalle.php?id=${partidoId}`;
      }
    });


    document.addEventListener("DOMContentLoaded", function () {
    const btnPDF = document.getElementById("btnGenerarPDF");

    // Función para obtener los parámetros de la URL
    function getFechaSeleccionada() {
        const params = new URLSearchParams(window.location.search);
        return params.get("fecha") || new Date().toISOString().slice(0, 10); // Usa hoy si no hay ?fecha=
    }

    const fecha = getFechaSeleccionada();
    btnPDF.href = `/TFG/controllers/generar_pdf.php?fecha=${encodeURIComponent(fecha)}`;
    });
  </script>

  <footer class="footer">
    <p>© 2025 JCSCORES. Todos los derechos reservados.</p>
    <p>Contacto: <a href="mailto:contacto@jcscores.com">contacto@jcscores.com</a></p>
    <div class="footer-links">
      <a href="#">Política de privacidad</a> |
      <a href="#">Términos de uso</a>
    </div>
  </footer>
</body>
</html>