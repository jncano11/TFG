<?php
try {
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=futbol_tfg;charset=utf8", "root", "admin");
    echo "✅ Conexión exitosa";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
