<?php
class AlumnosModel {

    private $conn;

    public function __construct($conexion){
        $this->conn = $conexion;
    }

public function getAlumnos($id_carrera = null){
    $sql = "SELECT a.id_alumno, a.nombre, g.nombre AS grupo
            FROM alumnos a
            JOIN grupos g ON a.id_grupo = g.id_grupo";

    if($id_carrera){
        $sql .= " WHERE g.id_carrera = ?";
        $stmt = $this->conn->prepare($sql);
        if(!$stmt) return []; // Retorna array vacío si falla la preparación
        $stmt->bind_param("i", $id_carrera);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $this->conn->query($sql);
        if(!$result) return []; // Retorna array vacío si falla la consulta
    }

    return $result->fetch_all(MYSQLI_ASSOC) ?? [];
}


    public function agregarAlumno($nombre, $id_grupo){
        $stmt = $this->conn->prepare("INSERT INTO alumnos (nombre, id_grupo) VALUES (?, ?)");
        $stmt->bind_param("si", $nombre, $id_grupo);
        return $stmt->execute();
    }

    public function getAlumno($id){
        $stmt = $this->conn->prepare("SELECT * FROM alumnos WHERE id_alumno=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function editarAlumno($id, $nombre, $id_grupo){
        $stmt = $this->conn->prepare("UPDATE alumnos SET nombre=?, id_grupo=? WHERE id_alumno=?");
        $stmt->bind_param("sii", $nombre, $id_grupo, $id);
        return $stmt->execute();
    }

    public function eliminarAlumno($id){
        $stmt = $this->conn->prepare("DELETE FROM alumnos WHERE id_alumno=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getGrupos(){
        $sql = "SELECT id_grupo, nombre FROM grupos ORDER BY nombre ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
