<?php
require_once '../config/bd.php';
$db = conectarBD();

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Admin') {
    header('Location: login.php');
    exit();
}

// Traer equipos y árbitros
$equipos = $db->query("SELECT id, nombre FROM equipos")->fetchAll(PDO::FETCH_ASSOC);
$arbitros = $db->query("SELECT id, nombre FROM usuarios WHERE rol = 'Arbitro'")->fetchAll(PDO::FETCH_ASSOC);

$categorias = ['Regional Preferente', '1ªRegional', '2ªRegional', '3ªRegional'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Crear partido</title>
<link rel="icon" href="/TFG/public/img/icons/logo.png" type="image/png" />
<link rel="stylesheet" href="../public/css/crear_partido.css">
</head>
<body>

<a href="/index.php" class="btn-volver" title="Volver al inicio">&#8592;</a>

<div class="crear-partido-container">
    <h1>Crear nuevo partido</h1>
    <form action="../controllers/PartidoController.php" method="POST" id="formPartido">
        <input type="hidden" name="accion" value="crear_partido">

        <!-- Equipo Local -->
        <label for="equipoLocalInput">Equipo Local:</label>
        <input list="equiposLocal" id="equipoLocalInput" placeholder="Escribe o elige..." autocomplete="off" required>
        <datalist id="equiposLocal">
            <?php foreach ($equipos as $e): ?>
                <option data-id="<?= $e['id'] ?>" value="<?= htmlspecialchars($e['nombre']) ?>"></option>
            <?php endforeach; ?>
        </datalist>
        <input type="hidden" name="equipo_local_id" id="equipoLocalId">

        <!-- Equipo Visitante -->
        <label for="equipoVisitanteInput">Equipo Visitante:</label>
        <input list="equiposVisitante" id="equipoVisitanteInput" placeholder="Escribe o elige..." autocomplete="off" required>
        <datalist id="equiposVisitante">
            <?php foreach ($equipos as $e): ?>
                <option data-id="<?= $e['id'] ?>" value="<?= htmlspecialchars($e['nombre']) ?>"></option>
            <?php endforeach; ?>
        </datalist>
        <input type="hidden" name="equipo_visitante_id" id="equipoVisitanteId">

        <!-- Resto de campos -->
        <label>Estadio:</label>
        <input type="text" name="estadio" required>

        <label>Fecha:</label>
        <input type="date" name="fecha" required>

        <label>Hora:</label>
        <input type="time" name="hora" required>

        <label>Categoría:</label>
        <select name="categoria" required>
            <option value="" disabled selected>Seleccione categoría</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Árbitro:</label>
        <select name="arbitro_id" required>
            <?php foreach ($arbitros as $arb): ?>
                <option value="<?= $arb['id'] ?>"><?= htmlspecialchars($arb['nombre']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Crear Partido</button>
    </form>
</div>

<script>
// Construimos un map de nombre->id para lookup rápido
const mapLocal = {};
document.querySelectorAll('#equiposLocal option').forEach(opt => {
    mapLocal[opt.value] = opt.dataset.id;
});
const mapVisit = {};
document.querySelectorAll('#equiposVisitante option').forEach(opt => {
    mapVisit[opt.value] = opt.dataset.id;
});

// Referencias DOM
const inputLocal  = document.getElementById('equipoLocalInput');
const inputVisit  = document.getElementById('equipoVisitanteInput');
const hiddenLocal = document.getElementById('equipoLocalId');
const hiddenVisit = document.getElementById('equipoVisitanteId');
const form        = document.getElementById('formPartido');

// Función para asignar id al hidden
function syncId(inputEl, hiddenEl, map) {
  const val = inputEl.value;
  if (map[val]) {
    hiddenEl.value = map[val];
    inputEl.classList.remove('error');
  } else {
    hiddenEl.value = '';
    // opcional: marcar en rojo si no coincide
    inputEl.classList.add('error');
  }
}

// Listeners
inputLocal.addEventListener('input', () => syncId(inputLocal, hiddenLocal, mapLocal));
inputVisit.addEventListener('input', () => syncId(inputVisit, hiddenVisit, mapVisit));

// Validar antes de enviar
form.addEventListener('submit', e => {
  if (!hiddenLocal.value || !hiddenVisit.value) {
    e.preventDefault();
    alert('Por favor, selecciona un equipo válido de la lista para ambos campos.');
  }
});
</script>

<style>
/* Marcado de error en input si no coincide */
input.error {
  border: 2px solid #e74c3c;
}
</style>

</body>
</html>
