<?php
include("conexion.php");

$docente = $_POST['docente'];
$grupo = $_POST['grupo'];
$materia = $_POST['materia'];
$inicio = $_POST['inicio'];
$fin = $_POST['fin'];

// OBTENER LOS HORARIOS DEL MAESTRO PARA ESA MATERIA Y GRUPO
$sql = $conexion->prepare("
    SELECT id_horario, dia_semana
    FROM horarios
    WHERE id_docente = ? AND id_grupo = ? AND id_materia = ?
");
$sql->bind_param("iii", $docente, $grupo, $materia);
$sql->execute();
$result = $sql->get_result();

$horarios = [];
while ($row = $result->fetch_assoc()) {
    $horarios[] = $row;
}

// CONVERTIR DÍAS DE SEMANA A NÚMEROS
$mapaDias = [
    'L' => 1,
    'M' => 2,
    'X' => 3,
    'J' => 4,
    'V' => 5,
    'S' => 6
];

$fechaActual = new DateTime($inicio);
$fechaFin = new DateTime($fin);

// RECORRER DÍA POR DÍA
while ($fechaActual <= $fechaFin) {

    $numDia = $fechaActual->format('N'); // 1=lunes 7=domingo

    foreach ($horarios as $h) {
        if ($mapaDias[$h['dia_semana']] == $numDia) {

            // INSERTAR SESIÓN
            $insert = $conexion->prepare("
                INSERT INTO sesiones_clase (id_horario, fecha)
                VALUES (?, ?)
            ");
            $fechaStr = $fechaActual->format('Y-m-d');
            $insert->bind_param("is", $h['id_horario'], $fechaStr);
            $insert->execute();
        }
    }

    // AVANZAR AL SIGUIENTE DÍA
    $fechaActual->modify("+1 day");
}

echo "<h2>Sesiones generadas correctamente</h2>";
echo "<a href='generar_sesiones.php'>Volver</a>";
?>
