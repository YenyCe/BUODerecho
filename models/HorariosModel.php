<?php
class HorariosModel
{

    private $conn;

    public function __construct($conexion)
    {
        $this->conn = $conexion;
    }

    // =======================
    // Obtener todos los horarios (para admin)
    // =======================
    public function getHorarios()
    {
        $sql = "
        SELECT 
            h.id_horario,
            h.id_carrera,
            h.id_grupo,
            h.id_materia,
            h.id_docente,
            c.nombre AS carrera,
            g.nombre AS grupo,
            m.nombre AS materia,
            CONCAT(d.nombre, ' ', d.apellidos) AS docente,
            h.horario_texto,
            GROUP_CONCAT(hd.dia ORDER BY FIELD(hd.dia,'L','M','X','J','V') SEPARATOR '-') AS dias
        FROM horarios h
        INNER JOIN grupos g ON h.id_grupo = g.id_grupo
        INNER JOIN carreras c ON h.id_carrera = c.id_carrera
        INNER JOIN materias m ON h.id_materia = m.id_materia
        INNER JOIN docentes d ON h.id_docente = d.id_docente
        LEFT JOIN horario_dias hd ON hd.id_horario = h.id_horario
        GROUP BY h.id_horario
        ORDER BY c.nombre, g.nombre, d.nombre
    ";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }


    // =======================
    // Obtener horarios por carrera (coordinador)
    // =======================
public function getHorariosByCarrera($id_carrera)
{
    $stmt = $this->conn->prepare("
        SELECT 
            h.id_horario,
            h.id_carrera,
            h.id_grupo,
            h.id_materia,
            h.id_docente,
            c.nombre AS carrera,
            g.nombre AS grupo,
            m.nombre AS materia,
            CONCAT(d.nombre, ' ', d.apellidos) AS docente,
            h.horario_texto,
            GROUP_CONCAT(hd.dia ORDER BY FIELD(hd.dia,'L','M','X','J','V') SEPARATOR '-') AS dias
        FROM horarios h
        INNER JOIN grupos g ON h.id_grupo = g.id_grupo
        INNER JOIN carreras c ON h.id_carrera = c.id_carrera
        INNER JOIN materias m ON h.id_materia = m.id_materia
        INNER JOIN docentes d ON h.id_docente = d.id_docente
        LEFT JOIN horario_dias hd ON hd.id_horario = h.id_horario
        WHERE h.id_carrera = ?
        GROUP BY h.id_horario
        ORDER BY g.nombre, m.nombre
    ");
    $stmt->bind_param("i", $id_carrera);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


    // =======================
    // Obtener carreras
    // =======================
    public function getCarreras()
    {
        return $this->conn->query("SELECT * FROM carreras ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
    }

    public function getCarrera($id_carrera)
    {
        $stmt = $this->conn->prepare("SELECT * FROM carreras WHERE id_carrera = ?");
        $stmt->bind_param("i", $id_carrera);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // =======================
    // Obtener docentes
    // =======================
    public function getDocentes()
    {
        return $this->conn->query("SELECT * FROM docentes ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
    }
    // =======================
// Obtener docentes por carrera
// =======================
public function getDocentesByCarrera($id_carrera)
{
    $stmt = $this->conn->prepare("
        SELECT *
        FROM docentes
        WHERE id_carrera = ?
        ORDER BY nombre
    ");
    $stmt->bind_param("i", $id_carrera);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


    // =======================
    // Obtener grupos por carrera (para select dinámico)
    // =======================
    public function getGruposByCarrera($id_carrera)
    {
        $stmt = $this->conn->prepare("SELECT * FROM grupos WHERE id_carrera = ? ORDER BY nombre");
        $stmt->bind_param("i", $id_carrera);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // =======================
    // Obtener materias por carrera (para select dinámico)
    // =======================
    public function getMateriasByCarrera($id_carrera)
    {
        $stmt = $this->conn->prepare("SELECT * FROM materias WHERE id_carrera = ? ORDER BY nombre");
        $stmt->bind_param("i", $id_carrera);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // =======================
    // Guardar nuevo horario
    // =======================
    public function guardar($id_carrera, $id_grupo, $id_materia, $id_docente, $horario_texto, $dias = [])
    {

        // Insertar horario
        $stmt = $this->conn->prepare("
        INSERT INTO horarios (id_carrera, id_grupo, id_materia, id_docente, horario_texto)
        VALUES (?, ?, ?, ?, ?)
    ");
        $stmt->bind_param("iiiis", $id_carrera, $id_grupo, $id_materia, $id_docente, $horario_texto);
        $stmt->execute();

        $id_horario = $stmt->insert_id;

        // Insertar días seleccionados
        $stmtDias = $this->conn->prepare("INSERT INTO horario_dias (id_horario, dia) VALUES (?, ?)");
        foreach ($dias as $d) {
            $stmtDias->bind_param("is", $id_horario, $d);
            $stmtDias->execute();
        }

        return true;
    }


    // =======================
    // Editar horario
    // =======================
    public function editarHorario($id_horario, $id_docente, $id_materia, $id_grupo, $id_carrera, $horario_texto, $dias = [])
    {

        // Actualizar horario
        $stmt = $this->conn->prepare("
        UPDATE horarios
        SET id_docente = ?, id_materia = ?, id_grupo = ?, id_carrera = ?, horario_texto = ?
        WHERE id_horario = ?
    ");
        $stmt->bind_param("iiiisi", $id_docente, $id_materia, $id_grupo, $id_carrera, $horario_texto, $id_horario);
        $stmt->execute();

        // Borrar días antiguos
        $this->conn->query("DELETE FROM horario_dias WHERE id_horario = $id_horario");

        // Insertar días nuevos
        $stmtDias = $this->conn->prepare("INSERT INTO horario_dias (id_horario, dia) VALUES (?, ?)");
        foreach ($dias as $d) {
            $stmtDias->bind_param("is", $id_horario, $d);
            $stmtDias->execute();
        }

        return true;
    }



    // =======================
    // Eliminar horario
    // =======================
    public function eliminar($id_horario)
    {
        $this->conn->query("DELETE FROM horario_dias WHERE id_horario = $id_horario");
        $this->conn->query("DELETE FROM horarios WHERE id_horario = $id_horario");
        return true;
    }

    // =======================
    // Obtener un horario por ID
    // =======================
    public function getHorario($id_horario)
    {
        $stmt = $this->conn->prepare("SELECT * FROM horarios WHERE id_horario = ?");
        $stmt->bind_param("i", $id_horario);
        $stmt->execute();
        $horario = $stmt->get_result()->fetch_assoc();

        // Traer días
        $res = $this->conn->query("SELECT dia FROM horario_dias WHERE id_horario = $id_horario");
        $dias = [];
        while ($row = $res->fetch_assoc()) {
            $dias[] = $row['dia'];
        }
        $horario['dias'] = implode('-', $dias);

        return $horario;
    }
}
