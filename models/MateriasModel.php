<?php
class MateriasModel {

    private $conn;

    public function __construct($conexion){
        $this->conn = $conexion;
    }

    // Obtener materias, opcionalmente filtrando por carrera
    public function getMaterias($id_carrera = null){
        $sql = "SELECT m.*, c.nombre AS nombre_carrera, s.numero AS semestre_num
                FROM materias m
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                LEFT JOIN semestres s ON m.id_semestre = s.id_semestre";

        $params = [];
        $types = "";

        if ($id_carrera) {
            $sql .= " WHERE m.id_carrera = ?";
            $types .= "i";
            $params[] = $id_carrera;
        }

        $sql .= " ORDER BY s.numero, m.nombre";

        if (!empty($params)) {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($sql);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function agregarMateria($nombre, $clave, $id_semestre, $horas_semana, $horas_semestre, $id_carrera){
        $stmt = $this->conn->prepare("INSERT INTO materias (nombre, clave, id_semestre, horas_semana, horas_semestre, id_carrera) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiiii", $nombre, $clave, $id_semestre, $horas_semana, $horas_semestre, $id_carrera);
        return $stmt->execute();
    }

    public function editarMateria($id, $nombre, $clave, $id_semestre, $horas_semana, $horas_semestre, $id_carrera){
        $stmt = $this->conn->prepare("UPDATE materias SET nombre=?, clave=?, id_semestre=?, horas_semana=?, horas_semestre=?, id_carrera=? WHERE id_materia=?");
        $stmt->bind_param("ssiiiii", $nombre, $clave, $id_semestre, $horas_semana, $horas_semestre, $id_carrera, $id);
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
