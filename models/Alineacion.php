<?php
require_once __DIR__ . '/../config/bd.php';

class Alineacion
{
    private $db;
    public function __construct()
    {
        $this->db = conectarBD();
    }
    public function guardar($partido_id, $equipo_id, $tipo, $formacion, $alineacion_texto)
    {
        if ($tipo === 'local') {
            $sql = "UPDATE alineaciones 
                    SET equipo_local_id = :equipo_id, formacion_local = :formacion, alineacion_local = :alineacion 
                    WHERE partido_id = :partido_id";
        } else {
            $sql = "UPDATE alineaciones 
                    SET equipo_visitante_id = :equipo_id, formacion_visitante = :formacion, alineacion_visitante = :alineacion 
                    WHERE partido_id = :partido_id";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':equipo_id', $equipo_id, PDO::PARAM_INT);
        $stmt->bindParam(':formacion', $formacion, PDO::PARAM_STR);
        $stmt->bindParam(':alineacion', $alineacion_texto, PDO::PARAM_STR);
        $stmt->bindParam(':partido_id', $partido_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function crearRegistroSiNoExiste($partido_id, $equipo_local_id, $equipo_visitante_id)
{
    $sql = "SELECT id FROM alineaciones WHERE partido_id = :partido_id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':partido_id', $partido_id);
    $stmt->execute();
    if ($stmt->rowCount() === 0) {
        $sqlInsert = "INSERT INTO alineaciones (partido_id, equipo_local_id, equipo_visitante_id)
                    VALUES (:partido_id, :equipo_local_id, :equipo_visitante_id)";
        $stmtInsert = $this->db->prepare($sqlInsert);
        $stmtInsert->bindParam(':partido_id', $partido_id);
        $stmtInsert->bindParam(':equipo_local_id', $equipo_local_id);
        $stmtInsert->bindParam(':equipo_visitante_id', $equipo_visitante_id);
        $stmtInsert->execute();
    }
}
public function obtenerPorPartido($partido_id)
{
    $sql = "SELECT 
                a.formacion_local AS formacion,
                a.alineacion_local AS jugadores,
                e_local.nombre AS equipo_nombre,
                'local' AS tipo
            FROM alineaciones a
            JOIN equipos e_local ON a.equipo_local_id = e_local.id
            WHERE a.partido_id = :partido_id
            UNION
            SELECT 
                a.formacion_visitante AS formacion,
                a.alineacion_visitante AS jugadores,
                e_visitante.nombre AS equipo_nombre,
                'visitante' AS tipo
            FROM alineaciones a
            JOIN equipos e_visitante ON a.equipo_visitante_id = e_visitante.id
            WHERE a.partido_id = :partido_id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':partido_id', $partido_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
