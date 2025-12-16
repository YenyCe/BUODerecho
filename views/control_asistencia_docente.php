<?php
require_once "../config/conexion.php";
require_once "../models/helpers_asistencia.php";

$id_docente  = (int)($_POST['id_docente'] ?? 0);
$id_materia  = (int)($_POST['id_materia'] ?? 0);
$id_grupo    = (int)($_POST['id_grupo'] ?? 0);
$id_parcial  = (int)($_POST['id_parcial'] ?? 0);

if (!$id_docente || !$id_materia || !$id_grupo) {
    die("Faltan parámetros obligatorios.");
}

// Fechas según parcial
if ($id_parcial) {
    $p = $conn->query("
        SELECT fecha_inicio, fecha_fin, numero_parcial 
        FROM parciales 
        WHERE id_parcial = $id_parcial
    ")->fetch_assoc();

    $fecha_inicio = $p['fecha_inicio'];
    $fecha_fin    = $p['fecha_fin'];
} else {
    die("El control de asistencia docente requiere un parcial.");
}

// Datos generales
$docente = $conn->query("
    SELECT CONCAT(nombre,' ',apellidos) AS nombre 
    FROM docentes 
    WHERE id_docente = $id_docente
")->fetch_assoc();

$materia = $conn->query("
    SELECT nombre, clave 
    FROM materias 
    WHERE id_materia = $id_materia
")->fetch_assoc();

$grupo = $conn->query("
    SELECT g.nombre AS grupo, s.numero AS semestre
    FROM grupos g
    INNER JOIN semestres s ON g.id_semestre = s.id_semestre
    WHERE g.id_grupo = $id_grupo
")->fetch_assoc();

// Horarios del docente (MISMO helper)
$horarios = obtener_horarios_docente($conn, $id_docente, $id_materia, $id_grupo);

// Fechas reales según horario
$fechas = generar_fechas_y_horas($fecha_inicio, $fecha_fin, $horarios);
$meses_es = [
    1 => 'ENERO',
    2 => 'FEBRERO',
    3 => 'MARZO',
    4 => 'ABRIL',
    5 => 'MAYO',
    6 => 'JUNIO',
    7 => 'JULIO',
    8 => 'AGOSTO',
    9 => 'SEPTIEMBRE',
    10 => 'OCTUBRE',
    11 => 'NOVIEMBRE',
    12 => 'DICIEMBRE'
];

$fechas_por_mes = [];

foreach ($fechas as $f) {
    $num_mes = (int)date('m', strtotime($f['fecha']));
    $anio = date('Y', strtotime($f['fecha']));
    $mes = $meses_es[$num_mes] . " " . $anio;

    $fechas_por_mes[$mes][] = $f;
}


// Formato horarios (texto para mostrar en encabezado)
function formato_horarios($horarios)
{
    if (empty($horarios)) return '-';
    $arr = [];
    foreach ($horarios as $h) {
        $arr[] = $h['horario_texto'] ?? '-';
    }
    return implode(', ', $arr);
}

function dia_fecha_mayusculas($fecha)
{
    $map = [
        'Mon' => 'LUNES',
        'Tue' => 'MARTES',
        'Wed' => 'MIERCOLES',
        'Thu' => 'JUEVES',
        'Fri' => 'VIERNES',
        'Sat' => 'SABADO',
        'Sun' => 'DOMINGO'
    ];

    $dia = date('D', strtotime($fecha));
    $dia_es = $map[$dia] ?? strtoupper($dia);

    return $dia_es . ' ' . date('d', strtotime($fecha));
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Control de Asistencia Docente</title>
    <link rel="stylesheet" href="../css/control_docente.css">

</head>

<body>

    <button onclick="window.print()" class="print-btn">Imprimir</button>

    <?php foreach ($fechas_por_mes as $mes => $dias): ?>

        <div class="page">
            <div class="contenido" style="margin-top:60px;">

                <!-- DATOS GENERALES -->
                <table class="info-table">
                    <tr>
                        <td colspan="8"><strong>Materia:</strong> <?= $materia['nombre'] ?></td>
                        <td colspan="4"><strong>Mes:</strong> <?= $mes ?></td>
                    </tr>
                    <tr>
                        <td colspan="4"> <?= $grupo['semestre'] ?> / <?= $grupo['grupo'] ?></td>
                        <td colspan="8"><strong>Docente:</strong> <?= $docente['nombre'] ?></td>
                    </tr>
                    <tr>
                        <td colspan="12">
                            <strong>Horario:</strong> <?= htmlspecialchars(formato_horarios($horarios)) ?>
                        </td>
                    </tr>
                </table>

                <!-- TABLA DE CONTROL -->
                <table class="table-reporte" style="margin-top:15px;">
                    <thead>
                        <tr>
                            <th colspan="7" style="text-align:center; font-size:14px;">
                                CONTROL DE ASISTENCIAS
                            </th>
                        </tr>
                        <tr>
                            <th class="col-dia">Día</th>
                            <th class="col-asistencia">Asistencia</th>
                            <th>Hora Entrada</th>
                            <th>Hora Salida</th>
                            <th>Inasistencia</th>
                            <th>Fecha Reposición</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>

                  <tbody>
<?php foreach ($dias as $f): ?>

    <?php
    $horas = $f['horas'] ?? 1;
    ?>

    <?php for ($i = 1; $i <= $horas; $i++): ?>
        <tr>
            <?php if ($i === 1): ?>
                <td class="col-dia" rowspan="<?= $horas ?>">
                    <?= dia_fecha_mayusculas($f['fecha']) ?>
                </td>
            <?php endif; ?>

            <td class="col-asistencia"></td>

            <td><?= $f['hora_inicio'] ?? '' ?></td>
            <td><?= $f['hora_fin'] ?? '' ?></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    <?php endfor; ?>

<?php endforeach; ?>
</tbody>

                </table>


            </div>
        </div>

        <div class="page-break"></div>

    <?php endforeach; ?>


</body>

</html>