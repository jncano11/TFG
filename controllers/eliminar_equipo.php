<?php
require_once '../models/Equipo.php';

if (isset($_GET['id'])) {
    $equipo = new Equipo();
    $equipo->eliminarPorId($_GET['id']);
}

header("Location: ../views/administrar_equipos.php");
exit;
