<?php
require_once "../config/conexion.php";
require_once "../models/helpers_asistencia.php";

// Recibir datos
$id_docente  = isset($_POST['id_docente']) ? (int)$_POST['id_docente'] : 0;
$id_materia  = isset($_POST['id_materia']) ? (int)$_POST['id_materia'] : 0;
$id_grupo    = isset($_POST['id_grupo']) ? (int)$_POST['id_grupo'] : 0;
$id_parcial  = isset($_POST['id_parcial']) ? (int)$_POST['id_parcial'] : 0;
$fecha_inicio = $_POST['fecha_inicio'] ?? null;
$fecha_fin    = $_POST['fecha_fin'] ?? null;

if (!$id_docente || !$id_materia || !$id_grupo) {
    die("Faltan parámetros obligatorios: docente, materia o grupo.");
}

// Obtener fechas si viene un parcial
if ($id_parcial) {
    $p = $conn->query("SELECT fecha_inicio, fecha_fin, numero_parcial FROM parciales WHERE id_parcial = $id_parcial")->fetch_assoc();
    if (!$p) die("Parcial no encontrado.");
    $fecha_inicio = $p['fecha_inicio'];
    $fecha_fin = $p['fecha_fin'];
} else {
    if (!$fecha_inicio || !$fecha_fin) die("Debes indicar fecha inicio y fin si no seleccionas parcial.");
}

// Datos generales
$docente = $conn->query("SELECT CONCAT(nombre,' ',apellidos) AS nombre FROM docentes WHERE id_docente = $id_docente")->fetch_assoc();
$materia = $conn->query("SELECT * FROM materias WHERE id_materia = $id_materia")->fetch_assoc();

$grupo = $conn->query("
    SELECT g.nombre AS nombre_grupo, s.numero AS semestre_num
    FROM grupos g
    INNER JOIN semestres s ON g.id_semestre = s.id_semestre
    WHERE g.id_grupo = $id_grupo
")->fetch_assoc();

// Alumnos
$alumnos = $conn->query("
    SELECT * FROM alumnos 
    WHERE id_grupo = $id_grupo 
    ORDER BY nombre
")->fetch_all(MYSQLI_ASSOC) ?? [];

// Horarios del docente (usa tu helper existente)
$horarios = obtener_horarios_docente($conn, $id_docente, $id_materia, $id_grupo) ?? [];

// Generamos fechas con horas
$fechas = generar_fechas_y_horas($fecha_inicio, $fecha_fin, $horarios);

// Ajuste de nombre-clase según cantidad de fechas (para css)
$total_fechas = count($fechas);

if ($total_fechas <= 10) {
    $nombre_clase = "nombre-normal";   // tamaño normal
} elseif ($total_fechas <= 20) {
    $nombre_clase = "nombre-pequeno";  // un poco más pequeño
} else {
    $nombre_clase = "nombre-muy-pequeno"; // aún más pequeño
}

// Arreglo con los nombres de los meses en español
$meses_es = [
    1 => 'enero',
    2 => 'febrero',
    3 => 'marzo',
    4 => 'abril',
    5 => 'mayo',
    6 => 'junio',
    7 => 'julio',
    8 => 'agosto',
    9 => 'septiembre',
    10 => 'octubre',
    11 => 'noviembre',
    12 => 'diciembre'
];

// Agrupar fechas por mes (cada elemento mantiene 'fecha' y 'horas')
$fechas_por_mes = [];
foreach ($fechas as $f) {
    $fecha_real = $f['fecha'];
    $num_mes = (int)date('m', strtotime($fecha_real));
    $anio = date('Y', strtotime($fecha_real));
    $mes = $meses_es[$num_mes] . " " . $anio;
    if (!isset($fechas_por_mes[$mes])) $fechas_por_mes[$mes] = [];
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

function nombre_dia_corto_local($fecha)
{
    $map = [
        'Mon' => 'Lun',
        'Tue' => 'Mar',
        'Wed' => 'Mie',
        'Thu' => 'Jue',
        'Fri' => 'Vie',
        'Sat' => 'Sab',
        'Sun' => 'Dom'
    ];
    $d = date('D', strtotime($fecha));
    return $map[$d] ?? $d;
}
function fecha_larga_es($fecha)
{
    setlocale(LC_TIME, 'es_MX.UTF-8', 'es_ES.UTF-8', 'spanish');
    $timestamp = strtotime($fecha);
    return strftime('%d de %B', $timestamp);
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Lista de Asistencia</title>
    <link rel="stylesheet" href="../css/lista.css">
</head>

<body>

    <button onclick="window.print()" class="print-btn">Imprimir</button>

    <?php
    // PAGINAR 15 POR HOJA (alumnos)
    $total_alumnos = count($alumnos);
    $por_pagina = 15;
    $paginas = max(1, ceil($total_alumnos / $por_pagina));

    $index_global = 0;

    for ($pagina = 1; $pagina <= $paginas; $pagina++):
    ?>

        <div class="page">
            <div class="contenido" style="margin-top:40px;">
                <!-- ENCABEZADO INFORMACIÓN -->
                <table class="info-table">
                    <tr>
                        <td colspan="7"><strong>Materia:</strong> <?= htmlspecialchars($materia['nombre'] ?? '-') ?></td>
                        <td colspan="2"><strong>Clave:</strong> <?= htmlspecialchars($materia['clave'] ?? '-') ?></td>
                        <td colspan="2"><strong>Horas por semestre:</strong> <?= htmlspecialchars($materia['horas_semestre'] ?? '-') ?></td>
                        <td colspan="2"><strong>Horas por semana:</strong> <?= htmlspecialchars($materia['horas_semana'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td colspan="4"> <?= ($grupo['semestre_num'] ?? '-') . ' / ' . ($grupo['nombre_grupo'] ?? '-') ?></td>
                        <td colspan="2">Parcial </strong> <?= ($p['numero_parcial'] ?? '-')  ?></td>
                        <td colspan="2">
                            <?php
                            $f_i = fecha_larga_es($fecha_inicio);
                            $f_f = fecha_larga_es($fecha_fin);

                            if ($id_parcial) {
                                echo " $f_i a $f_f";
                            }
                            ?>
                        </td>

                        <td colspan="4"><strong>Docente:</strong> <?= htmlspecialchars($docente['nombre'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <td colspan="12"><strong>Horario:</strong> <?= htmlspecialchars(formato_horarios($horarios)) ?></td>
                    </tr>
                </table>

                <!-- TABLA PRINCIPAL -->
                <table class="<?= $nombre_clase ?>">
                    <thead>
                        <!-- fila 1: meses + Total y Calificación Parcial -->
                        <tr>
                            <th rowspan="2">N.</th>
                            <th rowspan="2" class="nombre-col">Nombre del alumno</th>

                            <?php foreach ($fechas_por_mes as $mes => $dias): ?>
                                <?php
                                $cols_mes = 0;
                                foreach ($dias as $d) $cols_mes += (int)$d['horas'];
                                ?>
                                <th colspan="<?= $cols_mes ?>"><?= ucfirst($mes) ?></th>
                            <?php endforeach; ?>

                            <th colspan="2">Total</th>
                            <th colspan="2">Calificación Parcial</th>
                            <th rowspan="2" class="observaciones">Observaciones</th>
                        </tr>

                        <!-- fila 2: fechas + subcolumnas Total y Calificación Parcial -->
                        <tr>
                            <?php foreach ($fechas_por_mes as $dias): ?>
                                <?php foreach ($dias as $d): ?>
                                    <th colspan="<?= (int)$d['horas'] ?>">
                                        <?= nombre_dia_corto_local($d['fecha']) . ' ' . date('d', strtotime($d['fecha'])) ?>
                                    </th>
                                <?php endforeach; ?>
                            <?php endforeach; ?>

                            <!-- subcolumnas de Total -->
                            <th>Asistencias</th>
                            <th>Faltas</th>

                            <!-- subcolumnas de Calificación Parcial -->
                            <th>Número</th>
                            <th>Letra</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        for ($i = 1; $i <= $por_pagina; $i++):
                            if ($index_global >= $total_alumnos) break;
                            $a = $alumnos[$index_global];
                        ?>
                            <tr>
                                <td><?= $index_global + 1 ?></td>
                                <td class="capitalizar <?= $nombre_clase ?>"><?= htmlspecialchars(strtolower($a['nombre']), ENT_QUOTES, 'UTF-8') ?></td>

                                <!-- celdas por cada fecha y por cada hora -->
                                <?php foreach ($fechas_por_mes as $dias): ?>
                                    <?php foreach ($dias as $d): ?>
                                        <?php for ($h = 1; $h <= (int)$d['horas']; $h++): ?>
                                            <td class="asistencia-cell">&nbsp;</td>
                                        <?php endfor; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>

                                <td>&nbsp;</td>
                                <td>&nbsp;</td>

                                <td>&nbsp;</td>
                                <td>&nbsp;</td>

                                <td>&nbsp;</td>
                            </tr>
                        <?php
                            $index_global++;
                        endfor;
                        ?>
                    </tbody>
                </table>

                <?php if ($pagina == $paginas): ?>

                    <div class="pie-final">
                        <div class="fila-flex">
                            <div class="pie-col">
                                <p style="margin:0;">
                                    <?= htmlspecialchars($docente['nombre'] ?? '-') ?>
                                </p>
                                <div class="linea-firma"></div>
                                <p style="margin:0;"><strong>NOMBRE Y FIRMA DEL DOCENTE</strong></p>
                            </div>
                            <div class="pie-col">
                                <p><strong>CALF. APROBATORIA</strong> (Usar tinta negra)</p>
                                <p><strong>CALF. REPROBATORIA</strong> (Usar tinta roja)</p>
                            </div>

                        </div>

                        <div class="fila-flex">
                            <div class="pie-col">
                                <div class="linea-firma"></div>
                                <p><strong>FECHA DE ENTREGA</strong></p>
                            </div>
                            <div class="pie-col">
                                <div class="linea-firma"></div>
                                <p><strong>NOMBRE DE QUIEN RECIBE</strong></p>
                            </div>
                        </div>
                    </div>


                <?php endif; ?>

            </div>
        </div>

        <?php if ($pagina < $paginas): ?>
            <div class="page-break"></div>
        <?php endif; ?>

    <?php endfor; ?>

</body>

</html>