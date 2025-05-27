<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../models/Gol.php';
require_once '../models/Tarjeta.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $partido_id = $_POST['partido_id'] ?? null;
    $jugador_id = $_POST['jugador_id'] ?? null;
    $tipo_evento = $_POST['tipo_evento'] ?? null;
    $minuto = $_POST['minuto'] ?? null;

    $exito = false;

    if ($tipo_evento === 'gol') {
        $tipo_gol = $_POST['tipo_gol'] ?? 'Normal';
        $equipo_id = $_POST['equipo_id'] ?? null;

        if ($partido_id && $jugador_id && $equipo_id && $minuto !== null) {
            $exito = Gol::guardar($partido_id, $jugador_id, $equipo_id, $minuto, $tipo_gol);
        }

    } elseif ($tipo_evento === 'tarjeta') {
        $tipo_tarjeta = $_POST['tipo_tarjeta'] ?? null;
        $motivo = $_POST['motivo'] ?? null;
        $equipo_id = $_POST['equipo_id'] ?? null;

        // Capturamos el árbitro si viene
        $arbitro_id = (!empty($_POST['arbitro_id'])) ? $_POST['arbitro_id'] : null;

        if ($partido_id && $jugador_id && $equipo_id && $tipo_tarjeta && $minuto !== null) {
            $exito = Tarjeta::guardar($partido_id, $arbitro_id, $jugador_id, $equipo_id, $tipo_tarjeta, $minuto, $motivo);
        }
    }

    // Redirigir con mensaje
    if ($exito) {
        header("Location: ../views/asignar_eventos.php?id=$partido_id&exito=1");
    } else {
        header("Location: ../views/asignar_eventos.php?id=$partido_id&error=1");
    }
    exit;
} else {
    echo "Acceso no permitido";
}
