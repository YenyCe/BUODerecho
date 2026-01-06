<?php
session_start();

require_once "../middlewares/auth.php";
require_once "../config/conexion.php";

/* ================= VALIDACIONES ================= */
$id_docente = isset($_GET['id_docente']) ? (int)$_GET['id_docente'] : 0;

if (!$id_docente) {
    die("Docente no válido");
}

/* ================= DATOS DEL DOCENTE ================= */
$docente = $conn->query("
    SELECT 
        d.id_docente,
        CONCAT(d.nombre,' ',d.apellidos) AS nombre,
        d.correo
    FROM docentes d
    WHERE d.id_docente = $id_docente
")->fetch_assoc();

if (!$docente) {
    die("Docente no encontrado");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Carga Académica</title>
<link rel="stylesheet" href="../css/sistema.css">
</head>

<body>

<h2>Carga Académica del Docente</h2>

<div class="card">
    <p><strong>Nombre:</strong> <?= $docente['nombre'] ?></p>
    <p><strong>Correo:</strong> <?= $docente['correo'] ?></p>
</div>

<hr>

<p>⚠️ Aquí se mostrará la carga académica (materias, grupos y horarios).</p>

</body>
</html>
