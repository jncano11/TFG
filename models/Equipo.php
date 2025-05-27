<?php
require_once __DIR__ . '/../config/bd.php';

class Equipo {
    private $db;

    public function __construct()
    {
        $this->db = conectarBD();
    }

    // Crear un nuevo equipo con equipación y estadio
    public static function crear($nombre, $escudo, $equipacion, $entrenador_id, $estadio) {
        $db = conectarBD();
        $sql = "INSERT INTO equipos (nombre, escudo, equipacion, entrenador_id, estadio) 
                VALUES (:nombre, :escudo, :equipacion, :entrenador_id, :estadio)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':escudo', $escudo);
        $stmt->bindParam(':equipacion', $equipacion);
        $stmt->bindParam(':entrenador_id', $entrenador_id);
        $stmt->bindParam(':estadio', $estadio);
        return $stmt->execute();
    }


    // Obtener un equipo por ID (incluye estadio, escudo y equipación)
    public function obtenerPorId($id) {
        $sql = "SELECT id, nombre, estadio, escudo, equipacion FROM equipos WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Buscar equipos por nombre (incluye estadio, escudo y equipación)
    public function buscarPorNombre($term) {
        $sql = "SELECT id, nombre, estadio, escudo, equipacion FROM equipos 
                WHERE nombre LIKE ? ORDER BY nombre LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["%$term%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todos los datos de un equipo, incluyendo el nombre del entrenador
    public function obtenerDetallesPorId($id) {
        $sql = "SELECT e.*, u.nombre AS nombre_entrenador 
                FROM equipos e
                LEFT JOIN usuarios u ON e.entrenador_id = u.id
                WHERE e.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener jugadores del equipo
    public function obtenerJugadores($equipo_id) {
        $sql = "SELECT * FROM usuarios 
                WHERE rol = 'Jugador' AND equipo_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$equipo_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar solo el escudo
    public function actualizarEscudo(int $equipoId, string $rutaEscudo): bool {
        $sql = "UPDATE equipos 
                SET escudo = :escudo 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':escudo', $rutaEscudo);
        $stmt->bindParam(':id', $equipoId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Actualizar solo la equipación
    public function actualizarEquipacion(int $equipoId, string $rutaEquipacion): bool {
        $sql = "UPDATE equipos 
                SET equipacion = :equipacion 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':equipacion', $rutaEquipacion);
        $stmt->bindParam(':id', $equipoId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function obtenerTodos() {
        $sql = "SELECT * FROM equipos";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Eliminar un equipo por ID
    public function eliminarPorId(int $id): bool {
        $sql = "DELETE FROM equipos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
