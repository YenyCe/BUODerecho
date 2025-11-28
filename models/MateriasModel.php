<?php
class MateriasModel {

    private $conn;

    public function __construct($conexion){
        $this->conn = $conexion;
    }

    // Obtener materias, opcionalmente filtrando por carrera
// Obtener materias, opcionalmente filtrando por carrera
public function getMaterias($id_carrera = null){
    $sql = "SELECT m.*, c.nombre AS nombre_carrera
            FROM materias m
            LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
            ORDER BY m.id_materia DESC";

    if($id_carrera){
        $sql = "SELECT m.*, c.nombre AS nombre_carrera
                FROM materias m
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                WHERE m.id_carrera = ?
                ORDER BY m.id_materia DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_carrera);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $this->conn->query($sql);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

public function agregarMateria($nombre, $clave, $horas_semana, $horas_semestre, $id_carrera){
    $stmt = $this->conn->prepare("INSERT INTO materias (nombre, clave, horas_semana, horas_semestre, id_carrera) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiii", $nombre, $clave, $horas_semana, $horas_semestre, $id_carrera);
    return $stmt->execute();
}

public function editarMateria($id, $nombre, $clave, $horas_semana, $horas_semestre, $id_carrera){
    $stmt = $this->conn->prepare("UPDATE materias SET nombre=?, clave=?, horas_semana=?, horas_semestre=?, id_carrera=? WHERE id_materia=?");
    $stmt->bind_param("ssiiii", $nombre, $clave, $horas_semana, $horas_semestre, $id_carrera, $id);
    return $stmt->execute();
}


    public function getMateria($id){
        $stmt = $this->conn->prepare("SELECT * FROM materias WHERE id_materia=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }


    public function eliminarMateria($id){
        $stmt = $this->conn->prepare("DELETE FROM materias WHERE id_materia=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
