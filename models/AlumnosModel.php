<?php
class AlumnosModel {

    private $conn;
    // Ajustes
    private $NOMBRE_MAX = 150;

    public function __construct($conexion){
        $this->conn = $conexion;
    }

    /* ===========================
       UTIL: Obtener fila del grupo (si existe)
       Devuelve array asociativo del grupo o false
       =========================== */
    private function obtenerGrupo($id_grupo){
        $stmt = $this->conn->prepare("SELECT id_grupo, nombre, id_carrera FROM grupos WHERE id_grupo = ?");
        $stmt->bind_param("i", $id_grupo);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?: false;
    }

    /* ===========================
       UTIL: Sanitizar y validar nombre
       =========================== */
    private function validarNombre($nombre){
        $nombre = trim($nombre);
        if ($nombre === '') return [false, 'El nombre no puede estar vacío.'];
        if (mb_strlen($nombre) > $this->NOMBRE_MAX) return [false, "El nombre no puede exceder {$this->NOMBRE_MAX} caracteres."];
        return [true, $nombre];
    }

    /* ===========================
       LISTAR ALUMNOS
       $id_carrera (opcional) -> si se pasa, filtra por carrera (coordinador)
       Devuelve array de alumnos
       =========================== */
    public function getAlumnos($id_carrera = null){
$sql = "SELECT 
            a.id_alumno, 
            a.nombre, 
            a.id_grupo,
            a.id_carrera,
            g.nombre AS grupo,
            c.nombre AS carrera
        FROM alumnos a
        JOIN grupos g ON a.id_grupo = g.id_grupo
        JOIN carreras c ON a.id_carrera = c.id_carrera";




        if ($id_carrera){
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

    /* ===========================
       OBTENER UN ALUMNO
       =========================== */
    public function getAlumno($id){
        $stmt = $this->conn->prepare("SELECT * FROM alumnos WHERE id_alumno=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /* ===========================
       AGREGAR ALUMNO (validado)
       $user_role: 'admin' | 'coordinador' | null
       $user_carrera: int|null (si rol == 'coordinador', pasar su id_carrera)
       Retorna ['success'=>bool, 'message'=>string, 'id' => inserted_id (si success)]
       =========================== */
    public function agregarAlumno($nombre, $id_grupo, $user_role = null, $user_carrera = null){
        // validar nombre
        list($ok, $nombre_clean_or_msg) = $this->validarNombre($nombre);
        if (!$ok) return ['success' => false, 'message' => $nombre_clean_or_msg];

        // validar id_grupo
        if (!is_numeric($id_grupo) || intval($id_grupo) <= 0) {
            return ['success' => false, 'message' => 'Grupo inválido.'];
        }
        $id_grupo = intval($id_grupo);

        // comprobar que el grupo exista y obtener su carrera
        $grupo = $this->obtenerGrupo($id_grupo);
        if (!$grupo) return ['success' => false, 'message' => 'El grupo seleccionado no existe.'];

        $grupo_carrera = intval($grupo['id_carrera']);

        // Si el usuario es coordinador, validar que el grupo pertenezca a su carrera
        if ($user_role === 'coordinador') {
            if ($user_carrera === null) return ['success' => false, 'message' => 'No se proporcionó la carrera del coordinador.'];
            if (intval($user_carrera) !== $grupo_carrera) {
                return ['success' => false, 'message' => 'No tienes permiso para agregar alumnos a ese grupo (carrera distinta).'];
            }
        }

        // Insertar (establecemos id_carrera basado en el grupo para mantener coherencia)
        $stmt = $this->conn->prepare("INSERT INTO alumnos (nombre, id_grupo, id_carrera) VALUES (?, ?, ?)");
        $id_carrera_para_insert = $grupo_carrera;
        $stmt->bind_param("sii", $nombre_clean_or_msg, $id_grupo, $id_carrera_para_insert);
        $exec = $stmt->execute();

        if ($exec) {
            return ['success' => true, 'message' => 'Alumno agregado correctamente.', 'id' => $stmt->insert_id];
        } else {
            return ['success' => false, 'message' => 'Error al insertar alumno: ' . $this->conn->error];
        }
    }

    /* ===========================
       EDITAR ALUMNO (validado)
       Parametros: $user_role, $user_carrera (para validar permisos)
       =========================== */
    public function editarAlumno($id, $nombre, $id_grupo, $user_role = null, $user_carrera = null){
        // validar id
        if (!is_numeric($id) || intval($id) <= 0) return ['success' => false, 'message' => 'ID de alumno inválido.'];
        $id = intval($id);

        // validar nombre
        list($ok, $nombre_clean_or_msg) = $this->validarNombre($nombre);
        if (!$ok) return ['success' => false, 'message' => $nombre_clean_or_msg];

        // validar id_grupo
        if (!is_numeric($id_grupo) || intval($id_grupo) <= 0) {
            return ['success' => false, 'message' => 'Grupo inválido.'];
        }
        $id_grupo = intval($id_grupo);

        // comprobar que el alumno exista
        $alumno = $this->getAlumno($id);
        if (!$alumno) return ['success' => false, 'message' => 'Alumno no encontrado.'];

        // comprobar que el grupo exista
        $grupo = $this->obtenerGrupo($id_grupo);
        if (!$grupo) return ['success' => false, 'message' => 'El grupo seleccionado no existe.'];

        $grupo_carrera = intval($grupo['id_carrera']);

        // permisos: si coordinador, no puede mover alumno a grupo de otra carrera
        if ($user_role === 'coordinador') {
            if ($user_carrera === null) return ['success' => false, 'message' => 'No se proporcionó la carrera del coordinador.'];
            if (intval($user_carrera) !== $grupo_carrera) {
                return ['success' => false, 'message' => 'No tienes permiso para asignar este grupo al alumno (carrera distinta).'];
            }
            // Además, verificar que el alumno actual pertenezca a la misma carrera del coordinador
            $alumno_carrera = intval($alumno['id_carrera']);
            if ($alumno_carrera !== intval($user_carrera)) {
                return ['success' => false, 'message' => 'No tienes permiso para editar este alumno (pertenece a otra carrera).'];
            }
        }

        // Actualizar: también actualizamos id_carrera para mantener coherencia
        $stmt = $this->conn->prepare("UPDATE alumnos SET nombre = ?, id_grupo = ?, id_carrera = ? WHERE id_alumno = ?");
        $stmt->bind_param("siii", $nombre_clean_or_msg, $id_grupo, $grupo_carrera, $id);
        $exec = $stmt->execute();

        if ($exec) {
            return ['success' => true, 'message' => 'Alumno actualizado correctamente.'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar alumno: ' . $this->conn->error];
        }
    }

    /* ===========================
       ELIMINAR ALUMNO (validado)
       - Coordinador solo puede eliminar alumnos de su carrera
       =========================== */
    public function eliminarAlumno($id, $user_role = null, $user_carrera = null){
        if (!is_numeric($id) || intval($id) <= 0) return ['success' => false, 'message' => 'ID inválido.'];
        $id = intval($id);

        $alumno = $this->getAlumno($id);
        if (!$alumno) return ['success' => false, 'message' => 'Alumno no encontrado.'];

        if ($user_role === 'coordinador') {
            if ($user_carrera === null) return ['success' => false, 'message' => 'No se proporcionó la carrera del coordinador.'];
            if (intval($alumno['id_carrera']) !== intval($user_carrera)) {
                return ['success' => false, 'message' => 'No tienes permiso para eliminar este alumno (pertenece a otra carrera).'];
            }
        }

        $stmt = $this->conn->prepare("DELETE FROM alumnos WHERE id_alumno = ?");
        $stmt->bind_param("i", $id);
        $exec = $stmt->execute();

        if ($exec) return ['success' => true, 'message' => 'Alumno eliminado correctamente.'];
        return ['success' => false, 'message' => 'Error al eliminar alumno: ' . $this->conn->error];
    }

    /* ===========================
       LISTAR GRUPOS (sin cambios funcionales, seguro)
       =========================== */
    public function getGrupos(){
    $sql = "SELECT g.id_grupo, g.nombre AS nombre_grupo, g.id_carrera, c.nombre AS nombre_carrera
            FROM grupos g
            JOIN carreras c ON g.id_carrera = c.id_carrera
            ORDER BY g.nombre ASC";
    $result = $this->conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}


    // Obtener información de un grupo por su ID
public function getGrupo($id_grupo){
    $stmt = $this->conn->prepare("SELECT id_grupo, nombre, id_carrera FROM grupos WHERE id_grupo = ?");
    $stmt->bind_param("i", $id_grupo);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_assoc() ?: false;
}

public function getGruposPorCarrera($id_carrera){
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



    public function getAlumnosPorGrupo($id_grupo){
    $stmt = $this->conn->prepare(
        "SELECT id_alumno, nombre 
         FROM alumnos 
         WHERE id_grupo = ? 
         ORDER BY nombre ASC"
    );
    $stmt->bind_param("i", $id_grupo);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}




public function getAlumnosBaja($id_carrera = null){
    $sql = "SELECT 
                a.id_alumno,
                a.nombre,
                a.motivo_baja,
                c.nombre AS carrera
            FROM alumnos a
            JOIN carreras c ON a.id_carrera = c.id_carrera
            WHERE a.status = 'baja'";

    if ($id_carrera) {
        $sql .= " AND a.id_carrera = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_carrera);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $this->conn->query($sql);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

public function darBajaAlumno($id, $motivo = null, $user_role = null, $user_carrera = null){
    if (!is_numeric($id) || intval($id) <= 0) 
        return ['success'=>false, 'message'=>'ID inválido.'];

    $id = intval($id);
    $alumno = $this->getAlumno($id);
    if (!$alumno) return ['success'=>false, 'message'=>'Alumno no encontrado.'];

    if ($user_role === 'coordinador' && intval($alumno['id_carrera']) !== intval($user_carrera)) {
        return ['success'=>false, 'message'=>'No tienes permiso para dar de baja a este alumno.'];
    }

    // Solo actualizamos id_grupo, status y motivo_baja
    $stmt = $this->conn->prepare(
        "UPDATE alumnos 
         SET id_grupo = NULL, status = 'baja', motivo_baja = ? 
         WHERE id_alumno = ?"
    );
    $stmt->bind_param("si", $motivo, $id);
    $exec = $stmt->execute();

    if ($exec) return ['success'=>true, 'message'=>'Alumno dado de baja correctamente.'];
    return ['success'=>false, 'message'=>'Error al actualizar alumno: '.$this->conn->error];
}



}
?>
