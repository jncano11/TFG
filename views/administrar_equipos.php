<?php
require_once '../models/Equipo.php';

$equipoModel = new Equipo();
$equipos = $equipoModel->obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Equipos</title>
    <link rel="icon" href="/TFG/public/img/icons/logo.png" type="image/png" />
    <link rel="stylesheet" href="../public/css/administrar_usuarios.css">
</head>
<body>
    <main class="admin-usuarios-container">
        <a href="../index.php" class="btn-volver" title="Volver al inicio">⟵</a>
        <h1 class="titulo">Administrar Equipos</h1>

        <table class="tabla-usuarios">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Escudo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($equipos as $equipo): ?>
                    <tr>
                        <td><?= htmlspecialchars($equipo['nombre']) ?></td>
                        <td><img src="/public/<?= htmlspecialchars($equipo['escudo']) ?>" alt="Escudo" width="40"></td>
                        <td>
                            <a class="btn btn-eliminar" href="../controllers/eliminar_equipo.php?id=<?= $equipo['id'] ?>" onclick="return confirm('¿Eliminar equipo?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
