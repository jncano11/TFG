<?php
require_once 'models/Equipo.php';

class EquiposController {

    public function index() {
        $equipoModel = new Equipo();
        $equipos = $equipoModel->obtenerTodos();
        require_once 'views/administrar_equipos.php';
    }

    public function eliminar() {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $equipoModel = new Equipo();
            $equipoModel->eliminarPorId((int)$_GET['id']);
        }
        header("Location: index.php?controller=equipos&action=index");
    }
}
