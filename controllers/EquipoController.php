<?php
session_start();
require_once __DIR__ . '/../models/Equipo.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar sesión y rol entrenador
    if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Entrenador') {
        header('Location: ../views/login.php');
        exit();
    }

    $nombre = $_POST['nombre'] ?? '';
    $estadio = $_POST['estadio'] ?? '';
    $entrenador_id = $_SESSION['usuario']['id'];

    // Subida de escudo
    $escudo = null;
    if (isset($_FILES['escudo']) && $_FILES['escudo']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = uniqid() . '-' . basename($_FILES['escudo']['name']);
        $rutaDestino = __DIR__ . '/../public/uploads/' . $nombreArchivo;
        move_uploaded_file($_FILES['escudo']['tmp_name'], $rutaDestino);
        $escudo = 'uploads/' . $nombreArchivo;
    }

    // Subida de equipación
    $equipacion = null;
    if (isset($_FILES['equipacion']) && $_FILES['equipacion']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = uniqid() . '-' . basename($_FILES['equipacion']['name']);
        $rutaDestino = __DIR__ . '/../public/uploads/' . $nombreArchivo;
        move_uploaded_file($_FILES['equipacion']['tmp_name'], $rutaDestino);
        $equipacion = 'uploads/' . $nombreArchivo;
    }

    if ($nombre && $escudo && $equipacion && $estadio) {
        if (Equipo::crear($nombre, $escudo, $equipacion, $entrenador_id, $estadio)) {
            header('Location: ../views/index.php?success=1');
            exit();
        } else {
            $error = "Error al crear el equipo.";
        }
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}
?>
