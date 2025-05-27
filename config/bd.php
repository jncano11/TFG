<?php
$host = 'sql7.freesqldatabase.com';
$db   = 'sql7781605';
$user = 'sql7781605';
$pass = 'gjM8I3Ssxd';

if (!function_exists('conectarBD')) {
    function conectarBD() {
        global $host, $db, $user, $pass;
        try {
            $conexion = new PDO("mysql:host=$host;port=3306;dbname=$db;charset=utf8", $user, $pass);
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conexion;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
?>
