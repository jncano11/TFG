<?php
require_once '../models/Partido.php';
require_once '../models/Usuario.php';
require_once '../models/Equipo.php';

$partido_id = $_GET['id'] ?? null;
if (!$partido_id) {
    echo "Error: No se ha proporcionado un ID de partido.";
    exit;
}
$jugadores = Usuario::obtenerJugadoresPorPartido($partido_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Evento</title>
    <link rel="stylesheet" href="../public/css/eventos.css">
</head>
<body>
    <div class="evento-container">
        <h2>Asignar Evento</h2>

        <?php if (isset($_GET['exito'])): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                Evento guardado correctamente.
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                Error al guardar el evento. Inténtalo de nuevo.
            </div>
        <?php endif; ?>

        <form method="POST" action="../controllers/guardar_evento.php" onsubmit="return validarFormulario();">
            <input type="hidden" name="partido_id" value="<?= $partido_id ?>">

            <label for="jugador_id">Jugador:</label>
            <select name="jugador_id" required>
                <?php foreach ($jugadores as $jugador): ?>
                    <option value="<?= $jugador['id'] ?>"><?= $jugador['nombre'] ?> (<?= $jugador['equipo'] ?>)</option>
                <?php endforeach; ?>
            </select>

            <label for="tipo_evento">Tipo de evento:</label>
            <select name="tipo_evento" id="tipo_evento" required>
                <option value="gol">Gol</option>
                <option value="tarjeta">Tarjeta</option>
            </select>

            <div id="tarjeta-fields" style="display: none;">
                <label for="tipo_tarjeta">Tipo de tarjeta:</label>
                <select name="tipo_tarjeta">
                    <option value="amarilla">Amarilla</option>
                    <option value="roja">Roja</option>
                </select>

                <label for="motivo">Motivo:</label>
                <input type="text" name="motivo">

                <label for="arbitro_id">Árbitro:</label>
                <input type="number" name="arbitro_id" placeholder="Opcional">
            </div>

            <div id="gol-fields" style="display: none;">
                <label for="tipo_gol">Tipo de gol:</label>
                <select name="tipo_gol">
                    <option value="Normal">Normal</option>
                    <option value="Penalti">Penalti</option>
                    <option value="Propia puerta">Propia puerta</option>
                </select>
            </div>

            <!-- Campo común: aparece tanto en gol como en tarjeta -->
            <div id="equipo-field" style="display: none;">
                <label for="equipo_id">ID del equipo:</label>
                <input type="number" name="equipo_id">
            </div>

            <label for="minuto">Minuto:</label>
            <input type="number" name="minuto" required>

            <button type="submit">Guardar Evento</button>
        </form>
        <div class="volver">
            <a href="/index.php">← Volver al inicio</a>
        </div>
    </div>
    <script>
        const tipoEvento = document.getElementById('tipo_evento');
        const tarjetaFields = document.getElementById('tarjeta-fields');
        const golFields = document.getElementById('gol-fields');
        const equipoField = document.getElementById('equipo-field');

        tipoEvento.addEventListener('change', function () {
            if (this.value === 'tarjeta') {
                tarjetaFields.style.display = 'block';
                golFields.style.display = 'none';
                equipoField.style.display = 'block';
            } else if (this.value === 'gol') {
                tarjetaFields.style.display = 'none';
                golFields.style.display = 'block';
                equipoField.style.display = 'block';
            } else {
                tarjetaFields.style.display = 'none';
                golFields.style.display = 'none';
                equipoField.style.display = 'none';
            }
        });

        // Mostrar los campos correctos al cargar
        window.addEventListener('DOMContentLoaded', () => {
            tipoEvento.dispatchEvent(new Event('change'));
        });

        // Validación simple antes de enviar
        function validarFormulario() {
            alert("Formulario enviado correctamente");
            return true;
        }
    </script>
</body>
</html>
