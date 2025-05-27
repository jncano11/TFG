<?php
// models/Usuario.php
class Usuario {
    private $id;
    private $nombre;
    private $apellidos;
    private $email;
    private $password;
    private $foto_perfil;
    private $fecha_nacimiento;
    private $posicion;
    private $equipo_id;
    private $rol;
    private $creado_en;
    private $actualizado_en;
    private $db;

    public function __construct(PDO $conexion) {
        $this->db = $conexion;
    }
    public function setId($id)                    { $this->id = $id; }
    public function setNombre($nombre)            { $this->nombre = $nombre; }
    public function setApellidos($apellidos)      { $this->apellidos = $apellidos; }
    public function setEmail($email)              { $this->email = $email; }
    public function setPassword($password) { 
        $this->password = password_hash($password, PASSWORD_BCRYPT); 
    }
    public function setFotoPerfil($foto_perfil)   { $this->foto_perfil = $foto_perfil; }
    public function setFechaNacimiento($fecha)    { $this->fecha_nacimiento = $fecha; }
    public function setPosicion($posicion)        { $this->posicion = $posicion; }
    public function setEquipoId($equipo_id)       { $this->equipo_id = $equipo_id; }
    public function setRol($rol)                  { $this->rol = $rol; }
    public function setCreadoEn($ts)              { $this->creado_en = $ts; }
    public function setActualizadoEn($ts)         { $this->actualizado_en = $ts; }

    public function getId()               { return $this->id; }
    public function getNombre()           { return $this->nombre; }
    public function getApellidos()        { return $this->apellidos; }
    public function getEmail()            { return $this->email; }
    public function getFotoPerfil()       { return $this->foto_perfil; }
    public function getFechaNacimiento()  { return $this->fecha_nacimiento; }
    public function getPosicion()         { return $this->posicion; }
    public function getEquipoId()         { return $this->equipo_id; }
    public function getRol()              { return $this->rol; }
    public function getCreadoEn()         { return $this->creado_en; }
    public function getActualizadoEn()    { return $this->actualizado_en; }
    public function guardar() {
        $sql = "INSERT INTO usuarios
                (nombre, apellidos, email, password, foto_perfil, fecha_nacimiento, posicion, equipo_id, rol)
                VALUES
                (:nombre, :apellidos, :email, :password, :foto_perfil, :fecha_nacimiento, :posicion, :equipo_id, :rol)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre'           => $this->nombre,
            ':apellidos'        => $this->apellidos,
            ':email'            => $this->email,
            ':password'         => $this->password,
            ':foto_perfil'      => $this->foto_perfil,
            ':fecha_nacimiento' => $this->fecha_nacimiento,
            ':posicion'         => $this->posicion,
            ':equipo_id'        => $this->equipo_id,
            ':rol'              => $this->rol
        ]);
    }
    public function autenticar($email, $password) {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        if ($u = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $u['password'])) {
                return $u;
            }
        }
        return false;
    }
    public function obtenerTodos() {
        $sql = "SELECT * FROM usuarios";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        if ($u = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->id               = $u['id'];
            $this->nombre           = $u['nombre'];
            $this->apellidos        = $u['apellidos'];
            $this->email            = $u['email'];
            $this->foto_perfil      = $u['foto_perfil'];
            $this->fecha_nacimiento = $u['fecha_nacimiento'];
            $this->posicion         = $u['posicion'];
            $this->equipo_id        = $u['equipo_id'];
            $this->rol              = $u['rol'];
            $this->creado_en        = $u['creado_en'];
            $this->actualizado_en   = $u['actualizado_en'];
            return true;
        }

        return false;
    }

    public static function obtenerPorRol($rol)
    {
    $db = conectarBD();
    $stmt = $db->prepare("SELECT id, nombre, equipo_id FROM usuarios WHERE rol = ?");
    $stmt->execute([$rol]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerJugadoresPorEquipo($equipo_id) {
        require_once __DIR__ . '/../config/bd.php';
    
        $pdo = conectarBD(); // << aquí creas la conexión PDO
    
        $query = $pdo->prepare("SELECT * FROM usuarios WHERE equipo_id = :equipo_id AND rol = 'Jugador'");
        $query->execute(['equipo_id' => $equipo_id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function eliminarPorId($id) {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    public function hacerAdmin($id) {
        $sql = "UPDATE usuarios SET rol = 'Admin' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Usuario.php
    public static function obtenerJugadoresPorPartido($partido_id) {
        $db = conectarBD();
    
        // Obtener los equipos del partido
        $stmt = $db->prepare("SELECT equipo_local_id, equipo_visitante_id FROM partidos WHERE id = :id");
        $stmt->execute(['id' => $partido_id]);
        $partido = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$partido) return [];
    
        $equipo_local = $partido['equipo_local_id'];
        $equipo_visitante = $partido['equipo_visitante_id'];
    
        // Obtener los jugadores de ambos equipos con el nombre del equipo
        $stmt = $db->prepare("
            SELECT u.id, u.nombre, u.apellidos, u.posicion, u.equipo_id, e.nombre AS equipo
            FROM usuarios u
            JOIN equipos e ON u.equipo_id = e.id
            WHERE u.equipo_id IN (:local, :visitante) AND u.rol = 'Jugador'
        ");
    
        $stmt->execute([
            'local' => $equipo_local,
            'visitante' => $equipo_visitante
        ]);
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cambiarRol($id, $nuevoRol) {
        $sql = "UPDATE usuarios SET rol = :rol WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':rol', $nuevoRol);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
