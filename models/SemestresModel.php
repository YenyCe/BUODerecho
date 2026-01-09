<?php
class SemestresModel {

    private $conn;

    public function __construct($conexion){
        $this->conn = $conexion;
    }

    /* ================= SEMESTRES ================= */

    public function getSemestres(){
        $sql = "SELECT * FROM semestres ORDER BY numero ASC";
        $result = $this->conn->query($sql);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function existeSemestre($numero, $id_excluir = null){
        if($id_excluir){
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) as total FROM semestres WHERE numero=? AND id_semestre!=?"
            );
            if (!$stmt) return false;
            $stmt->bind_param("ii", $numero, $id_excluir);
        } else {
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) as total FROM semestres WHERE numero=?"
            );
            if (!$stmt) return false;
            $stmt->bind_param("i", $numero);
        }

        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return ($res && $res['total'] > 0);
    }

    public function agregarSemestre($numero){
        if($this->existeSemestre($numero)) return false;

        $stmt = $this->conn->prepare(
            "INSERT INTO semestres (numero) VALUES (?)"
        );
        if (!$stmt) return false;

        $stmt->bind_param("i", $numero);
        return $stmt->execute();
    }

    public function editarSemestre($id, $numero){
        if($this->existeSemestre($numero, $id)) return false;

        $stmt = $this->conn->prepare(
            "UPDATE semestres SET numero=? WHERE id_semestre=?"
        );
        if (!$stmt) return false;

        $stmt->bind_param("ii", $numero, $id);
        return $stmt->execute();
    }

    public function semestreTieneGrupos($id_semestre){
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) as total FROM grupos WHERE id_semestre=?"
        );
        if (!$stmt) return false;

        $stmt->bind_param("i", $id_semestre);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return ($res && $res['total'] > 0);
    }

    public function eliminarSemestre($id){
        if($this->semestreTieneGrupos($id)) return false;

        $stmt = $this->conn->prepare(
            "DELETE FROM semestres WHERE id_semestre=?"
        );
        if (!$stmt) return false;

        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /* ================= GRUPOS ================= */

    public function getGrupos($id_carrera = null){
        $sql = "
            SELECT g.id_grupo, g.nombre, g.id_semestre, g.id_carrera,
                   s.numero AS semestre_num,
                   c.nombre AS nombre_carrera
            FROM grupos g
            JOIN semestres s ON g.id_semestre = s.id_semestre
            LEFT JOIN carreras c ON g.id_carrera = c.id_carrera
        ";

        if($id_carrera){
            $sql .= " WHERE g.id_carrera = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) return [];

            $stmt->bind_param("i", $id_carrera);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($sql);
        }

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
  
    // Obtener información de un grupo por su ID
    public function getGrupo($id_grupo)
    {
        $stmt = $this->conn->prepare("
        SELECT 
            g.id_grupo,
            g.nombre AS grupo,
            g.id_carrera,
            s.id_semestre,
            s.numero AS semestre
        FROM grupos g
        INNER JOIN semestres s ON g.id_semestre = s.id_semestre
        WHERE g.id_grupo = ?
    ");
        $stmt->bind_param("i", $id_grupo);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?: false;
    }

    public function getGruposPorCarrera($id_carrera)
    {
        $stmt = $this->conn->prepare(
            "SELECT g.id_grupo, g.nombre AS nombre_grupo, g.id_carrera, c.nombre AS nombre_carrera
         FROM grupos g
         JOIN carreras c ON g.id_carrera = c.id_carrera
         WHERE g.id_carrera = ?
         ORDER BY g.nombre ASC"
        );
        $stmt->bind_param("i", $id_carrera);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function existeGrupo($nombre, $id_semestre, $id_carrera, $id_excluir = null){
        if($id_excluir){
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) as total
                 FROM grupos
                 WHERE nombre=? AND id_semestre=? AND id_carrera=? AND id_grupo!=?"
            );
            if (!$stmt) return false;
            $stmt->bind_param("siii", $nombre, $id_semestre, $id_carrera, $id_excluir);
        } else {
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) as total
                 FROM grupos
                 WHERE nombre=? AND id_semestre=? AND id_carrera=?"
            );
            if (!$stmt) return false;
            $stmt->bind_param("sii", $nombre, $id_semestre, $id_carrera);
        }

        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return ($res && $res['total'] > 0);
    }

    public function agregarGrupo($nombre, $id_semestre, $id_carrera){
        if($this->existeGrupo($nombre, $id_semestre, $id_carrera)) return false;

        $stmt = $this->conn->prepare(
            "INSERT INTO grupos (nombre, id_semestre, id_carrera) VALUES (?, ?, ?)"
        );
        if (!$stmt) return false;

        $stmt->bind_param("sii", $nombre, $id_semestre, $id_carrera);
        return $stmt->execute();
    }

    public function editarGrupo($id, $nombre, $id_semestre, $id_carrera){
        if($this->existeGrupo($nombre, $id_semestre, $id_carrera, $id)) return false;

        $stmt = $this->conn->prepare(
            "UPDATE grupos SET nombre=?, id_semestre=?, id_carrera=? WHERE id_grupo=?"
        );
        if (!$stmt) return false;

        $stmt->bind_param("siii", $nombre, $id_semestre, $id_carrera, $id);
        return $stmt->execute();
    }

    public function grupoTieneAlumnos($id_grupo){
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) as total FROM alumnos WHERE id_grupo=?"
        );
        if (!$stmt) return false;

        $stmt->bind_param("i", $id_grupo);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return ($res && $res['total'] > 0);
    }

    public function eliminarGrupo($id_grupo){
        if($this->grupoTieneAlumnos($id_grupo)) return false;

        $stmt = $this->conn->prepare(
            "DELETE FROM grupos WHERE id_grupo=?"
        );
        if (!$stmt) return false;

        $stmt->bind_param("i", $id_grupo);
        return $stmt->execute();
    }
}
