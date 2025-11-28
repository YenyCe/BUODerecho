<?php
class DocentesModel {

    private $conn;

    public function __construct($conexion){
        $this->conn = $conexion;
    }

    // Obtener docentes, filtrando por carrera si se pasa $id_carrera
    public function getDocentes($id_carrera = null){
        $sql = "SELECT d.id_docente, d.nombre, d.apellidos, d.correo, d.telefono, d.id_carrera
                FROM docentes d";

        if($id_carrera){
            $sql .= " WHERE d.id_carrera = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id_carrera);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($sql);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Agregar docente con carrera
    public function agregarDocente($nombre, $apellidos, $correo, $telefono, $id_carrera){
        $stmt = $this->conn->prepare("INSERT INTO docentes (nombre, apellidos, correo, telefono, id_carrera) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $nombre, $apellidos, $correo, $telefono, $id_carrera);
        return $stmt->execute();
    }

    // Obtener un docente por id
    public function getDocente($id){
        $stmt = $this->conn->prepare("SELECT * FROM docentes WHERE id_docente=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Editar docente con carrera
    public function editarDocente($id, $nombre, $apellidos, $correo, $telefono, $id_carrera){
        $stmt = $this->conn->prepare("UPDATE docentes SET nombre=?, apellidos=?, correo=?, telefono=?, id_carrera=? WHERE id_docente=?");
        $stmt->bind_param("ssssii", $nombre, $apellidos, $correo, $telefono, $id_carrera, $id);
        return $stmt->execute();
    }

    // Eliminar docente
    public function eliminarDocente($id){
        $stmt = $this->conn->prepare("DELETE FROM docentes WHERE id_docente=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
