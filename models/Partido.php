<?php
require_once __DIR__ . '/../config/bd.php';

class Partido {
    public static function obtenerTodos() {
        $db = conectarBD();
        $stmt = $db->query("SELECT p.*, 
                            el.nombre AS equipo_local, 
                            el.escudo AS equipo_local_escudo,
                            ev.nombre AS equipo_visitante,
                            ev.escudo AS equipo_visitante_escudo,
                            u.nombre AS arbitro
                            FROM partidos p
                            JOIN equipos el ON p.equipo_local_id = el.id
                            JOIN equipos ev ON p.equipo_visitante_id = ev.id
                            JOIN usuarios u ON p.arbitro_id = u.id
                            ORDER BY fecha, hora");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crear($datos) {
        $db = conectarBD();
        $stmt = $db->prepare("INSERT INTO partidos (equipo_local_id, equipo_visitante_id, estadio, fecha, hora, categoria, arbitro_id) 
                            VALUES (:equipo_local_id, :equipo_visitante_id, :estadio, :fecha, :hora, :categoria, :arbitro_id)");
        return $stmt->execute($datos);
    }

    public static function obtenerPorFecha($fecha) {
        $db = conectarBD();
        $stmt = $db->prepare("SELECT p.*, 
                            el.nombre AS equipo_local, 
                            el.escudo AS equipo_local_escudo,
                            ev.nombre AS equipo_visitante,
                            ev.escudo AS equipo_visitante_escudo,
                            u.nombre AS arbitro
                            FROM partidos p
                            JOIN equipos el ON p.equipo_local_id = el.id
                            JOIN equipos ev ON p.equipo_visitante_id = ev.id
                            JOIN usuarios u ON p.arbitro_id = u.id
                            WHERE p.fecha = :fecha
                            ORDER BY hora");
        $stmt->execute(['fecha' => $fecha]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId($id) {
        $db = conectarBD();
        $stmt = $db->prepare("SELECT p.*, 
                            el.nombre AS equipo_local, 
                            el.escudo AS equipo_local_escudo,
                            ev.nombre AS equipo_visitante,
                            ev.escudo AS equipo_visitante_escudo,
                            u.nombre AS arbitro
                            FROM partidos p
                            JOIN equipos el ON p.equipo_local_id = el.id
                            JOIN equipos ev ON p.equipo_visitante_id = ev.id
                            JOIN usuarios u ON p.arbitro_id = u.id
                            WHERE p.id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function actualizarDatosBasicos($id, $resultado_local, $resultado_visitante, $estadio, $fecha, $hora) {
        $db = conectarBD();  // Cambié esta línea para usar tu función conectarBD()
        $sql = "UPDATE partidos SET resultado_local = ?, resultado_visitante = ?, estadio = ?, fecha = ?, hora = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$resultado_local, $resultado_visitante, $estadio, $fecha, $hora, $id]);
    }

    public static function obtenerPorCategoria(string $categoria): array {
        $db = conectarBD();
        $sql = "SELECT p.*, el.nombre AS equipo_local, ev.nombre AS equipo_visitante
                FROM partidos p
            LEFT JOIN equipos el ON p.equipo_local_id = el.id
            LEFT JOIN equipos ev ON p.equipo_visitante_id = ev.id
                WHERE p.categoria = :cat";
        $stmt = $db->prepare($sql);
        $stmt->execute([':cat' => $categoria]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerUltimosPorEquipo($equipoId, $limite = 6) {
        $db = conectarBD();
        $sql = "SELECT *
                FROM partidos
                WHERE equipo_local_id = :equipoId OR equipo_visitante_id = :equipoId
                ORDER BY fecha DESC
                LIMIT :limite";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':equipoId', $equipoId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}