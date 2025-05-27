<?php
require_once __DIR__ . '/../config/bd.php';

class Tarjeta {
    public static function guardar($partido_id, $arbitro_id, $jugador_id, $equipo_id, $tipo, $minuto, $motivo = null) {
        $db = conectarBD();
        $stmt = $db->prepare("INSERT INTO tarjetas (partido_id, arbitro_id, jugador_id, equipo_id, tipo, minuto, motivo) 
                            VALUES (:partido_id, :arbitro_id, :jugador_id, :equipo_id, :tipo, :minuto, :motivo)");
        return $stmt->execute([
            'partido_id' => $partido_id,
            'arbitro_id' => $arbitro_id,
            'jugador_id' => $jugador_id,
            'equipo_id'  => $equipo_id,
            'tipo'       => $tipo,
            'minuto'     => $minuto,
            'motivo'     => $motivo
        ]);
    }

    public static function obtenerPorPartido($partido_id) {
        $db = conectarBD();
        $stmt = $db->prepare("SELECT t.*, 
                                    j.nombre AS jugador, 
                                    a.nombre AS arbitro,
                                    e.nombre AS equipo
                            FROM tarjetas t
                            JOIN usuarios j ON t.jugador_id = j.id
                            JOIN usuarios a ON t.arbitro_id = a.id
                            LEFT JOIN equipos e ON t.equipo_id = e.id
                            WHERE t.partido_id = :partido_id
                            ORDER BY t.minuto");
        $stmt->execute(['partido_id' => $partido_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function eliminarPorPartido($partido_id) {
        $db = conectarBD();
        $stmt = $db->prepare("DELETE FROM tarjetas WHERE partido_id = :partido_id");
        return $stmt->execute(['partido_id' => $partido_id]);
    }
}
