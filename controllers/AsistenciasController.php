<?php
require_once "../config/conexion.php";

// Traemos los datos de la BD
$docentes = $conn->query("SELECT id_docente, CONCAT(nombre,' ',apellidos) AS nombre_completo FROM docentes ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
$materias = $conn->query("SELECT * FROM materias ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
$semestres = $conn->query("SELECT * FROM semestres ORDER BY numero ASC")->fetch_all(MYSQLI_ASSOC);
$grupos = $conn->query("SELECT g.id_grupo, g.nombre AS nombre_grupo, s.numero AS semestre_num
                        FROM grupos g
                        INNER JOIN semestres s ON g.id_semestre = s.id_semestre
                        ORDER BY s.numero, g.nombre")->fetch_all(MYSQLI_ASSOC);
$parciales = $conn->query("SELECT * FROM parciales ORDER BY numero_parcial ASC")->fetch_all(MYSQLI_ASSOC);
?>
