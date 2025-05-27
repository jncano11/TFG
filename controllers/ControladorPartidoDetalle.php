<?php
require_once __DIR__ . '/../models/Partido.php';

if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'];
$partido = Partido::obtenerPorId($id);

if (!$partido) {
    echo "Partido no encontrado.";
    exit;
}

require_once __DIR__ . '/../views/partido_detalle.php';
