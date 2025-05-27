<?php
session_start();
require_once '../config/bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificamos que existan los campos antes de usarlos
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        header("Location: /views/login.php?error=campos_vacios");
        exit;
    }

    try {
        $db = conectarBD();

        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            // Guardamos todo agrupado bajo 'usuario'
            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre'],
                'rol' => $usuario['rol'],
                'email' => $usuario['email']
            ];

            header("Location: /index.php");
            exit;
        } else {
            header("Location: /views/login.php?error=credenciales");
            exit;
        }

    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
} else {
    header("Location:/views/login.php");
    exit;
}
