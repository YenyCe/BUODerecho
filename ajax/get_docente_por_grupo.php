<?php
require_once "../config/conexion.php";

$id_materia = intval($_GET['id_materia']);
$id_grupo   = intval($_GET['id_grupo']);

$res = $conn->query("
    SELECT d.id_docente, d.nombre
    FROM horarios h
    INNER JOIN docentes d ON d.id_docente = h.id_docente
    WHERE h.id_materia = $id_materia
      AND h.id_grupo = $id_grupo
    LIMIT 1
");

echo json_encode($res->fetch_assoc());
