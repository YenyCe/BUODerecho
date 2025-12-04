<?php
require_once "../config/conexion.php";

$id_materia = intval($_GET['id_materia']);

$data = [];

// obtener todos los docentes de esa materia
$docentes = $conn->query("
    SELECT DISTINCT d.id_docente, CONCAT(d.nombre,' ',d.apellidos) AS nombre
    FROM docentes d
    INNER JOIN horarios h ON h.id_docente = d.id_docente
    WHERE h.id_materia = $id_materia
    ORDER BY d.nombre
")->fetch_all(MYSQLI_ASSOC);

$data["docentes"] = $docentes;

// obtener todos los grupos de esa materia
$grupos = $conn->query("
    SELECT DISTINCT g.id_grupo, g.nombre
    FROM grupos g
    INNER JOIN horarios h ON h.id_grupo = g.id_grupo
    WHERE h.id_materia = $id_materia
    ORDER BY g.nombre
")->fetch_all(MYSQLI_ASSOC);

$data["grupos"] = $grupos;

echo json_encode($data);
