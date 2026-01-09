<?php
session_start();
require_once "../config/conexion.php";
require_once "../models/helpers_asistencia.php";
require_once "../models/ParcialesModel.php";
require_once "../models/SemestresModel.php";
require_once "../models/DocentesModel.php";
require_once "../models/MateriasModel.php";

$id_carrera = !empty($_SESSION['id_carrera']) ? $_SESSION['id_carrera'] : null;

if (!$id_carrera) {
    die("403");
}
$id_docente  = (int)($_POST['id_docente'] ?? 0);
$id_materia  = (int)($_POST['id_materia'] ?? 0);
$id_grupo    = (int)($_POST['id_grupo'] ?? 0);
$id_parcial  = (int)($_POST['id_parcial'] ?? 0);

$parcialModel  = new ParcialesModel($conn);
$semestreModel = new SemestresModel($conn);
$docenteModel  = new DocentesModel($conn);
$materiaModel  = new MateriasModel($conn);

if (!$id_docente || !$id_materia || !$id_grupo || !$id_parcial) {
    die("Faltan parámetros obligatorios.");
}

/* ================= PARCIAL ================= */
$p       = $parcialModel->getParcialPorId($id_parcial);

$fecha_inicio = $p['fecha_inicio'];
$fecha_fin    = $p['fecha_fin'];

/* ================= DATOS ================= */
$docente = $docenteModel->getDocente($id_docente);
$materia = $materiaModel->getMateria($id_materia);

$grupo   = $semestreModel->getGrupo($id_grupo);

/* ================= HORARIOS ================= */
$horarios = obtener_horarios_docente($conn, $id_docente, $id_materia, $id_grupo);
$fechas = generar_fechas_y_horas($fecha_inicio, $fecha_fin, $horarios);

/* ================= AGRUPAR POR MES ================= */
$fechas_por_mes = agrupar_fechas_por_mes($fechas);

$nombre_membreteV = '';

switch ($id_carrera) {
    case 1:
        $nombre_membreteV = 'logo2.jpg';
        break;
    case 3:
        $nombre_membreteV = 'me.png';
        break;
}
/* ================= CONFIG ================= */
$filas_por_hoja = 15;
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

        <?php
        $filas = [];
        foreach ($dias as $d) {
            for ($i = 1; $i <= ($d['horas'] ?? 1); $i++) {
                $filas[] = [
                    'fecha' => $d['fecha'],
                    'hora_inicio' => $d['hora_inicio'] ?? '',
                    'hora_fin' => $d['hora_fin'] ?? '',
                    'rowspan' => ($i === 1 ? ($d['horas'] ?? 1) : 0)
                ];
            }
        }

        $paginas = array_chunk($filas, $filas_por_hoja);
        ?>

        <?php foreach ($paginas as $pagina): ?>
            <div class="page" style="background: url('/img/<?= $nombre_membreteV; ?>') no-repeat center; background-size: contain;">
                <div class="contenido" style="margin-top:60px;">
                    <table class="info-table">
                        <tr>
                            <td colspan="8"><strong>Materia:</strong> <?= $materia['nombre'] ?></td>
                            <td colspan="4"><strong>Mes:</strong> <?= $mes ?></td>
                        </tr>
                        <tr>
                            <td colspan="4"><?= $grupo['semestre'] ?> / <?= $grupo['grupo'] ?></td>
                            <td colspan="8"><strong>Docente:</strong> <?= $docente['nombre'] ?></td>
                        </tr>
                        <tr>
                            <td colspan="12"><strong>Horario:</strong> <?= formato_horarios($horarios) ?></td>
                        </tr>
                    </table>

                    <table class="table-reporte" style="margin-top:15px;">
                        <thead>
                            <tr>
                                <th colspan="7">CONTROL DE ASISTENCIAS</th>
                            </tr>
                            <tr>
                                <th>DÍA</th>
                                <th>ASISTENCIA</th>
                                <th>HORA ENTRADA</th>
                                <th>HORA SALIDA</th>
                                <th>INASISTENCIA</th>
                                <th>REPOSICIÓN</th>
                                <th>OBSERVACIONES</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($pagina as $f): ?>
                                <tr>
                                    <?php if ($f['rowspan']): ?>
                                        <td rowspan="<?= $f['rowspan'] ?>"><?= dia_fecha($f['fecha']) ?></td>
                                    <?php endif; ?>
                                    <td></td>
                                    <td><?= $f['hora_inicio'] ?></td>
                                    <td><?= $f['hora_fin'] ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>
            <div class="page-break"></div>
        <?php endforeach; ?>

    <?php endforeach; ?>

</body>

</html>