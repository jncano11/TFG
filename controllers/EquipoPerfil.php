<?php
require_once __DIR__ . '/../models/Equipo.php';

$equipoModel = new Equipo();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$equipo = $equipoModel->obtenerDetallesPorId($id);
$jugadores = $equipoModel->obtenerJugadores($id);

require_once __DIR__ . '/../views/equipo_perfil.php';
