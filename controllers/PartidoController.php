<?php
require_once __DIR__ . '/../models/Partido.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['accion'] === 'crear_partido') {
        // Validación para creación
        $categorias_permitidas = ['Regional Preferente', '1ªRegional', '2ªRegional', '3ªRegional'];
        $categoria = $_POST['categoria'] ?? '';

        if (!in_array($categoria, $categorias_permitidas)) {
            echo "Categoría no válida.";
            exit();
        }

        // Crear
        $datos = [
            'equipo_local_id' => $_POST['equipo_local_id'],
            'equipo_visitante_id' => $_POST['equipo_visitante_id'],
            'estadio' => $_POST['estadio'],
            'fecha' => $_POST['fecha'],
            'hora' => $_POST['hora'],
            'categoria' => $categoria,
            'arbitro_id' => $_POST['arbitro_id']
        ];

        if (Partido::crear($datos)) {
            header("Location: /index.php");
            exit();
        } else {
            echo "Error al crear partido.";
        }

    } elseif ($_POST['accion'] === 'editar_partido') {
        // Editar
        $id = $_POST['id'];
        $resultado_local = $_POST['resultado_local'];
        $resultado_visitante = $_POST['resultado_visitante'];
        $estadio = $_POST['estadio'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];

        $exito = Partido::actualizarDatosBasicos($id, $resultado_local, $resultado_visitante, $estadio, $fecha, $hora);

        header("Location: ../views/editar_partido.php?id=$id&msg=guardado");
        exit();
    }
}
