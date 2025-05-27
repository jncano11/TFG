<?php
require_once '../config/bd.php';
require_once '../models/Usuario.php';

$db = conectarBD();
$usuario = new Usuario($db);
$usuarios = $usuario->obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Usuarios</title>
    <link rel="icon" href="/TFG/public/img/icons/logo.png" type="image/png" />
    <link rel="stylesheet" href="../public/css/administrar_usuarios.css">
</head>
<body>
<main class="admin-usuarios-container">
    <div class="top-buttons">
        <a href="../views/index.php" class="btn-volver" title="Volver al inicio">⟵</a>
        <a href="administrar_equipos.php" class="btn-admin-equipos" title="Administrar equipos">Administrar equipos</a>
    </div>
    <h1 class="titulo">Administrar Usuarios</h1>

    <table class="tabla-usuarios">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                    <td><?= htmlspecialchars($usuario['apellidos']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td>
                        <form method="POST" action="../controllers/cambiar_rol.php" style="display: inline-flex; align-items: center;">
                            <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                            <select name="rol">
                                <option value="Admin" <?= $usuario['rol'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="Jugador" <?= $usuario['rol'] === 'Jugador' ? 'selected' : '' ?>>Jugador</option>
                                <option value="Entrenador" <?= $usuario['rol'] === 'Entrenador' ? 'selected' : '' ?>>Entrenador</option>
                                <option value="Arbitro" <?= $usuario['rol'] === 'Arbitro' ? 'selected' : '' ?>>Arbitro</option>
                            </select>
                            <button type="submit" class="btn btn-cambiar-rol">Cambiar</button>
                        </form>
                    </td>
                    <td>
                        <a class="btn btn-eliminar" href="../controllers/eliminar_usuario.php?id=<?= $usuario['id'] ?>" onclick="return confirm('¿Estás seguro que deseas eliminar este usuario?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
</body>
</html>
