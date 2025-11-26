<?php
require_once "../config/conexion.php";

$id_docente = intval($_GET['id_docente']);

$sql = "
    SELECT DISTINCT h.id_materia, m.nombre
    FROM horarios h
    INNER JOIN materias m ON h.id_materia = m.id_materia
    WHERE h.id_docente = $id_docente
    ORDER BY m.nombre
";

$result = $conn->query($sql);

echo "<option value=''>Seleccione materia...</option>";

while($row = $result->fetch_assoc()){
    echo "<option value='{$row['id_materia']}'>{$row['nombre']}</option>";
}
