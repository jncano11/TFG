<?php
require_once '../config/bd.php';
require_once '../models/Usuario.php';

if (isset($_GET['id'])) {
    $db = conectarBD();
    $usuario = new Usuario($db);
    $usuario->eliminarPorId($_GET['id']);
}

// Redirigir de vuelta a la vista
header("Location: ../views/administrar_usuarios.php");
exit;
