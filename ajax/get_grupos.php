<?php
require_once "../config/conexion.php";

$id_docente = intval($_GET['id_docente']);
$id_materia = intval($_GET['id_materia']);

$sql = "
    SELECT DISTINCT h.id_grupo, g.nombre
    FROM horarios h
    INNER JOIN grupos g ON h.id_grupo = g.id_grupo
    WHERE h.id_docente = $id_docente
      AND h.id_materia = $id_materia
    ORDER BY g.nombre
";

$result = $conn->query($sql);

echo "<option value=''>Seleccione grupo...</option>";

while($row = $result->fetch_assoc()){
    echo "<option value='{$row['id_grupo']}'>{$row['nombre']}</option>";
}
