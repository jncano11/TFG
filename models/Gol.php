<?php
require_once __DIR__ . '/../config/bd.php';

class Gol {
    public static function guardar($partido_id, $jugador_id, $equipo_id, $minuto, $tipo = 'Normal') {
        $db = conectarBD();
        $stmt = $db->prepare("INSERT INTO goles (partido_id, jugador_id, equipo_id, minuto, tipo) 
                            VALUES (:partido_id, :jugador_id, :equipo_id, :minuto, :tipo)");
        return $stmt->execute([
            'partido_id' => $partido_id,
            'jugador_id' => $jugador_id,
            'equipo_id' => $equipo_id,
            'minuto' => $minuto,
            'tipo' => $tipo
        ]);
    }

    public static function obtenerPorPartido($partido_id) {
        $db = conectarBD();
        $stmt = $db->prepare("SELECT g.*, u.nombre AS jugador, e.nombre AS equipo
                            FROM goles g
                            JOIN usuarios u ON g.jugador_id = u.id
                            JOIN equipos e ON g.equipo_id = e.id
                            WHERE g.partido_id = :partido_id
                            ORDER BY g.minuto");
        $stmt->execute(['partido_id' => $partido_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function eliminarPorPartido($partido_id) {
        $db = conectarBD();
        $stmt = $db->prepare("DELETE FROM goles WHERE partido_id = :partido_id");
        return $stmt->execute(['partido_id' => $partido_id]);
    }
}
