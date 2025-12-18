<?php
class UsuariosModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getUsuarios()
    {
        $sql = "SELECT u.id_usuario, u.nombre, u.correo, u.usuario, u.rol, u.estado, u.id_carrera, c.nombre AS carrera
                FROM usuarios u
                LEFT JOIN carreras c ON u.id_carrera = c.id_carrera
                ORDER BY u.id_usuario ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function agregarUsuario($nombre, $correo, $usuario, $password, $rol, $id_carrera = null)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, correo, usuario, password, rol, id_carrera) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssi", $nombre, $correo, $usuario, $hash, $rol, $id_carrera);
        return $stmt->execute();
    }

   public function editarUsuario($id_usuario,$nombre,$correo,$usuario,$password,$rol,$estado,$id_carrera = null) {

        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nombre = ?,correo = ?,usuario = ?,password = ?,rol = ?, estado = ?, id_carrera = ?
                    WHERE id_usuario = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param( "sssssiii",$nombre,$correo,$usuario, $hash, $rol, $estado, $id_carrera, $id_usuario);

        } else {
            $sql = "UPDATE usuarios SET nombre = ?, correo = ?, usuario = ?, rol = ?, estado = ?, id_carrera = ?  WHERE id_usuario = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssssiii", $nombre, $correo,  $usuario, $rol, $estado, $id_carrera, $id_usuario );
        }
        return $stmt->execute();
    }

    public function eliminarUsuario($id_usuario)
    {
        $sql = "DELETE FROM usuarios WHERE id_usuario=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        return $stmt->execute();
    }

    public function getUsuario($id_usuario)
    {
        $sql = "SELECT * FROM usuarios WHERE id_usuario=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getCarreras()
    {
        $sql = "SELECT * FROM carreras";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
