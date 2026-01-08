<?php
require_once "../config/conexion.php";

$id_materia = intval($_GET['id_materia']);

$res = $conn->query("
    SELECT DISTINCT g.id_grupo, g.nombre
    FROM horarios h
    INNER JOIN grupos g ON g.id_grupo = h.id_grupo
    WHERE h.id_materia = $id_materia
    ORDER BY g.nombre
");

echo json_encode($res->fetch_all(MYSQLI_ASSOC));
