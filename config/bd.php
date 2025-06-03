<?php
$host = 'sql7.freesqldatabase.com';
$db   = 'sql7782885';
$user = 'sql7782885';
$pass = 'pFhsWrxT6q';

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
