<?php
session_start();
require_once '../config/bd.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['usuario']['email'];

try {
    $db = conectarBD();

    $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $datosUsuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$datosUsuario) {
        die("Usuario no encontrado.");
    }

} catch (PDOException $e) {
    die("Error al obtener datos del perfil: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?= htmlspecialchars($datosUsuario['nombre']) ?></title>
    <link rel="icon" href="/TFG/public/img/icons/logo.png" type="image/png" />
    <link rel="stylesheet" href="../public/css/perfil.css">
</head>
<body>
    <div class="perfil-container">
        <div class="perfil-header">
            <img src="/public/img/icons/perfil.jpg" alt="Foto de perfil por defecto" class="perfil-foto">
            <h1><?= htmlspecialchars($datosUsuario['nombre'] . ' ' . $datosUsuario['apellidos']) ?></h1>
        </div>

        <div class="perfil-datos">
            <div class="perfil-dato"><span class="label">Email:</span> <?= htmlspecialchars($datosUsuario['email']) ?></div>
            <div class="perfil-dato"><span class="label">Fecha de nacimiento:</span> <?= date('d/m/Y', strtotime($datosUsuario['fecha_nacimiento'])) ?></div>

            <?php if ($datosUsuario['rol'] === 'Jugador'): ?>
                <div class="perfil-dato"><span class="label">Posición:</span> <?= htmlspecialchars($datosUsuario['posicion']) ?></div>
            <?php endif; ?>

            <div class="perfil-dato"><span class="label">Equipo ID:</span> <?= (int)$datosUsuario['equipo_id'] ?></div>
            <div class="perfil-dato"><span class="label">Rol:</span> <?= htmlspecialchars($datosUsuario['rol']) ?></div>
            <div class="perfil-dato"><span class="label">Última actualización:</span> <?= date('d/m/Y H:i', strtotime($datosUsuario['actualizado_en'])) ?></div>
        </div>

        <p><a href="/index.php">← Volver al menú</a></p>
    </div>
</body>
</html>
