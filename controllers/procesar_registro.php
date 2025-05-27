<?php
require_once '../config/bd.php'; // Tu conexión PDO
$conexion = conectarBD();

// Validación de campos
$nombre = $_POST['nombre'] ?? '';
$apellidos = $_POST['apellidos'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
$rol = $_POST['rol'] ?? '';
$posicion = $_POST['posicion'] ?? null;

// Si no es jugador, posición debe ser null
if ($rol !== 'Jugador') {
    $posicion = null;
}

// Subida de imagen de perfil
$fotoRuta = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $directorioDestino = 'uploads/';
    if (!file_exists($directorioDestino)) {
        mkdir($directorioDestino, 0777, true);
    }

    $nombreArchivo = uniqid() . '_' . basename($_FILES['foto']['name']);
    $rutaCompleta = $directorioDestino . $nombreArchivo;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaCompleta)) {
        $fotoRuta = $rutaCompleta;
    }
}

// Encriptar contraseña
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

// Insertar en base de datos
try {
    $stmt = $conexion->prepare("
        INSERT INTO usuarios (nombre, apellidos, email, password, foto_perfil, fecha_nacimiento, posicion, rol)
        VALUES (:nombre, :apellidos, :email, :password, :foto_perfil, :fecha_nacimiento, :posicion, :rol)
    ");

    $stmt->execute([
        ':nombre' => $nombre,
        ':apellidos' => $apellidos,
        ':email' => $email,
        ':password' => $passwordHash,
        ':foto_perfil' => $fotoRuta,
        ':fecha_nacimiento' => $fecha_nacimiento,
        ':posicion' => $posicion,
        ':rol' => $rol,
    ]);

    header("Location: ../views/login.php?registro=exitoso");
    exit;

} catch (PDOException $e) {
    echo "Error al registrar usuario: " . $e->getMessage();
}
