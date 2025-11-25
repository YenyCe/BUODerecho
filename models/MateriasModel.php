<?php
class MateriasModel {

    private $conn;

    public function __construct($conexion){
        $this->conn = $conexion;
    }

    public function getMaterias(){
        $sql = "SELECT * FROM materias ORDER BY id_materia DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function agregarMateria($nombre, $clave, $horas_semana, $horas_semestre){
        $stmt = $this->conn->prepare("INSERT INTO materias (nombre, clave, horas_semana, horas_semestre) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $nombre, $clave, $horas_semana, $horas_semestre);
        return $stmt->execute();
    }

    public function getMateria($id){
        $stmt = $this->conn->prepare("SELECT * FROM materias WHERE id_materia=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function editarMateria($id, $nombre, $clave, $horas_semana, $horas_semestre){
        $stmt = $this->conn->prepare("UPDATE materias SET nombre=?, clave=?, horas_semana=?, horas_semestre=? WHERE id_materia=?");
        $stmt->bind_param("ssiii", $nombre, $clave, $horas_semana, $horas_semestre, $id);
        return $stmt->execute();
    }

    public function eliminarMateria($id){
        $stmt = $this->conn->prepare("DELETE FROM materias WHERE id_materia=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
