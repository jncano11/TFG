<?php
session_start();
require_once __DIR__ . '/../config/bd.php';
require_once __DIR__ . '/../models/Equipo.php';

// Solo Admin o Entrenador pueden cambiar la equipación
if (
    !isset($_SESSION['usuario']) ||
    (
        $_SESSION['usuario']['rol'] !== 'Admin'
        && $_SESSION['usuario']['rol'] !== 'Entrenador'
    )
) {
    header('Location: ../views/login.php');
    exit();
}

// Recogemos POST
$equipoId = intval($_POST['equipo_id'] ?? 0);
if ($equipoId <= 0) {
    die('Equipo inválido.');
}

// Si es Entrenador, verificar que sea su equipo
if ($_SESSION['usuario']['rol'] === 'Entrenador'
    && $_SESSION['usuario']['equipo_id'] != $equipoId
) {
    die('No tienes permisos para editar este equipo.');
}

// Procesar subida
if (empty($_FILES['equipacion']) || $_FILES['equipacion']['error'] !== UPLOAD_ERR_OK) {
    die('Error al subir el archivo.');
}

$archivo = $_FILES['equipacion'];
// Validar tipo MIME
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($archivo['tmp_name']);
$ext   = '';
switch ($mime) {
    case 'image/jpeg': $ext = '.jpg'; break;
    case 'image/png':  $ext = '.png'; break;
    case 'image/gif':  $ext = '.gif'; break;
    default:
        die('Formato de imagen no permitido.');
}

// Guardar con nombre único
$nombreFoto   = 'equipacion_' . $equipoId . '_' . uniqid() . $ext;
$rutaServidor = __DIR__ . '/../public/uploads/' . $nombreFoto;
if (!move_uploaded_file($archivo['tmp_name'], $rutaServidor)) {
    die('Error al guardar la imagen.');
}

// Guardar en BD
$rutaBD = 'uploads/' . $nombreFoto;
$equipoModel = new Equipo();
if ($equipoModel->actualizarEquipacion($equipoId, $rutaBD)) {
    header("Location: ../views/equipo_perfil.php?id={$equipoId}&equipacion=ok");
    exit();
} else {
    die('Error al actualizar en la base de datos.');
}
