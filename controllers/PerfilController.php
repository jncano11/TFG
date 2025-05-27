<?php
require_once 'config/bd.php';
require_once 'models/Usuario.php';

class PerfilController {
    public function verPerfil() {
        session_start();

        // Para depuración inicial (puedes descomentar esto si lo necesitas)
        // echo "<pre>"; var_dump($_SESSION); echo "</pre>"; exit;

        // Verificamos que el usuario está logueado y tiene email en sesión
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['email'])) {
            header("Location: login.php");
            exit;
        }

        $email = $_SESSION['usuario']['email'];

        // Conectamos con la base de datos
        $db = conectarBD();
        $usuarioModel = new Usuario($db);

        // Obtenemos los datos del usuario
        $datosUsuario = $usuarioModel->obtenerPorEmail($email);

        // Validamos que se haya encontrado el usuario en la base de datos
        if (!$datosUsuario) {
            echo "<p style='color:red;'>Error: No se encontraron datos para el usuario con email: $email</p>";
            exit;
        }

        // Mostramos la vista de perfil con los datos
        require 'views/perfil.php';
    }
}
