<?php
session_start();
require_once __DIR__ . '/../models/Alineacion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar sesión y rol entrenador
    if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'Entrenador') {
        header('Location: ../views/login.php');
        exit();
    }

    $entrenador_id = $_SESSION['usuario']['id'];
    $equipo_id = $_POST['equipo_id'] ?? null;
    $partido_id = $_POST['partido_id'] ?? null;
    $formacion = $_POST['formacion'] ?? '';
    $alineacion_texto = $_POST['alineacion'] ?? '';
    $tipo = $_POST['tipo'] ?? ''; // 'local' o 'visitante'
    $equipo_local_id = $_POST['equipo_local_id'] ?? null;
    $equipo_visitante_id = $_POST['equipo_visitante_id'] ?? null;

    if ($equipo_id && $partido_id && $formacion && $alineacion_texto && in_array($tipo, ['local', 'visitante'])) {
        // Crear modelo
        $alineacionModel = new Alineacion();

        // Asegurar que el registro del partido existe en la tabla de alineaciones
        $alineacionModel->crearRegistroSiNoExiste($partido_id, $equipo_local_id, $equipo_visitante_id);


        // Guardar alineación
        $resultado = $alineacionModel->guardar($partido_id, $equipo_id, $tipo, $formacion, $alineacion_texto);

        if ($resultado) {
            header("Location: ../views/añadir_alineaciones.php?partido_id=$partido_id&equipo_local_id=$equipo_local_id&equipo_visitante_id=$equipo_visitante_id&success=1");
            exit();
        } else {
            $error = "Error al guardar la alineación.";
        }
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}
?>
