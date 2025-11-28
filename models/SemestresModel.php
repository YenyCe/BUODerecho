<?php
class SemestresModel {

    private $conn;

    public function __construct($conexion){
        $this->conn = $conexion;
    }

    /* --- SEMESTRES --- */
    public function getSemestres(){
        $sql = "SELECT * FROM semestres ORDER BY numero ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function agregarSemestre($numero){
        $stmt = $this->conn->prepare("INSERT INTO semestres (numero) VALUES (?)");
        $stmt->bind_param("i", $numero);
        return $stmt->execute();
    }

    public function editarSemestre($id, $numero){
        $stmt = $this->conn->prepare("UPDATE semestres SET numero=? WHERE id_semestre=?");
        $stmt->bind_param("ii", $numero, $id);
        return $stmt->execute();
    }

    public function eliminarSemestre($id){
        $stmt = $this->conn->prepare("DELETE FROM semestres WHERE id_semestre=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }


/* --- GRUPOS --- */
public function getGrupos($id_carrera = null){
    $sql = "SELECT g.id_grupo, g.nombre, s.numero AS semestre_num, g.id_semestre, g.id_carrera, c.nombre AS nombre_carrera
            FROM grupos g
            JOIN semestres s ON g.id_semestre = s.id_semestre
            LEFT JOIN carreras c ON g.id_carrera = c.id_carrera";

    if($id_carrera){
        $sql .= " WHERE g.id_carrera = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_carrera);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $this->conn->query($sql);
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}

public function agregarGrupo($nombre, $id_semestre, $id_carrera){
    $stmt = $this->conn->prepare("INSERT INTO grupos (nombre, id_semestre, id_carrera) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $nombre, $id_semestre, $id_carrera);
    return $stmt->execute();
}

public function editarGrupo($id, $nombre, $id_semestre, $id_carrera){
    $stmt = $this->conn->prepare("UPDATE grupos SET nombre=?, id_semestre=?, id_carrera=? WHERE id_grupo=?");
    $stmt->bind_param("siii", $nombre, $id_semestre, $id_carrera, $id);
    return $stmt->execute();
}
public function eliminarGrupo($id_grupo){
    $stmt = $this->conn->prepare("DELETE FROM grupos WHERE id_grupo=?");
    $stmt->bind_param("i", $id_grupo);
    return $stmt->execute();
}


}
