<?php

require_once "../config/conexion.php";
$id_docente = $_POST['id_docente'];
$id_materia = $_POST['id_materia'];
$id_grupo   = $_POST['id_grupo'];
$dia        = $_POST['dia_semana'];
$inicio     = $_POST['hora_inicio'];
$fin        = $_POST['hora_fin'];

$sql = "INSERT INTO horarios (id_docente, id_materia, id_grupo, dia_semana, hora_inicio, hora_fin)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiisss", $id_docente, $id_materia, $id_grupo, $dia, $inicio, $fin);

if ($stmt->execute()) {
    echo "Horario guardado correctamente. <a href='../views/capturar_horario.php'>Regresar</a>";
} else {
    echo "Error al guardar horario: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
