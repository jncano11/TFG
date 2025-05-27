<?php
require_once '../config/bd.php';
require_once '../models/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'], $_POST['rol'])) {
        $id = $_POST['id'];
        $nuevoRol = $_POST['rol'];

        $db = conectarBD();
        $usuario = new Usuario($db);
        
        if ($usuario->cambiarRol($id, $nuevoRol)) {
            header("Location: ../views/administrar_usuarios.php");
            exit;
        } else {
            echo "Error al cambiar el rol.";
        }
    } else {
        echo "Faltan datos.";
    }
} else {
    echo "Método no permitido.";
}
?>
