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

// Validaciones
if (!$id_docente || !$id_materia || !$id_grupo) {
    die("Faltan parámetros obligatorios: docente, materia o grupo.");
}

// Obtener fechas de parcial
if ($id_parcial) {
    $p = $conn->query("SELECT fecha_inicio, fecha_fin, numero_parcial FROM parciales WHERE id_parcial = $id_parcial")->fetch_assoc();
    if (!$p) die("Parcial no encontrado.");
    $fecha_inicio = $p['fecha_inicio'];
    $fecha_fin = $p['fecha_fin'];
} else {
    if (!$fecha_inicio || !$fecha_fin) die("Debes indicar fecha inicio y fin si no seleccionas parcial.");
}

// Traer datos de docente, materia y grupo
$docente = $conn->query("SELECT CONCAT(nombre,' ',apellidos) AS nombre FROM docentes WHERE id_docente = $id_docente")->fetch_assoc();
$materia = $conn->query("SELECT * FROM materias WHERE id_materia = $id_materia")->fetch_assoc();
$grupo   = $conn->query("SELECT g.nombre AS nombre_grupo, s.numero AS semestre_num
                         FROM grupos g
                         INNER JOIN semestres s ON g.id_semestre = s.id_semestre
                         WHERE g.id_grupo = $id_grupo")->fetch_assoc();

// Traer alumnos
$alumnos = $conn->query("SELECT * FROM alumnos WHERE id_grupo = $id_grupo ORDER BY nombre")->fetch_all(MYSQLI_ASSOC) ?? [];

// Traer horarios del docente
$horarios = obtener_horarios_docente($conn, $id_docente, $id_materia, $id_grupo) ?? [];

// Generar fechas de clase
$fechas = generar_fechas_por_horarios($fecha_inicio, $fecha_fin, $horarios) ?? [];

// Función para mostrar horarios
// Función para mostrar horarios
function formato_horarios($horarios)
{
    if (empty($horarios) || !is_array($horarios)) return '-';
    $arr = [];
    foreach ($horarios as $h) {
        // Cambiado de hora_inicio/hora_fin a horario_texto
        $arr[] = $h['horario_texto'] ?? '-';
    }
    return implode(', ', $arr);
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
    <div class="page" style="position:relative;">
        
        <div class="contenido" style="position:relative; z-index:1; margin-top:40px;">
            <!-- Aquí tu tabla de info y asistencia -->


            <div class="contenido">
                <table class="info-table">
                    <tr>
                        <th>Materia</th>
                        <td><?php echo htmlspecialchars($materia['nombre'] ?? '-'); ?></td>
                        <th>Clave</th>
                        <td><?php echo htmlspecialchars($materia['clave'] ?? '-'); ?></td>
                        <th>Horas semestre</th>
                        <td><?php echo htmlspecialchars($materia['horas_semestre'] ?? '-'); ?></td>
                        <th>Horas semana</th>
                        <td><?php echo htmlspecialchars($materia['horas_semana'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <th>Semestre / Grupo</th>
                        <td><?php echo htmlspecialchars($grupo['semestre_num'] ?? '-') . ' / ' . htmlspecialchars($grupo['nombre_grupo'] ?? '-'); ?></td>
                        <th>Parcial</th>
                        <td colspan="3"><?php echo $id_parcial ? "Parcial " . $p['numero_parcial'] . " (" . $fecha_inicio . " a " . $fecha_fin . ")" : $fecha_inicio . " a " . $fecha_fin; ?></td>
                        <th>Docente</th>
                        <td><?php echo htmlspecialchars($docente['nombre'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <th>Horario</th>
                        <td colspan="7"><?php echo formato_horarios($horarios); ?></td>
                    </tr>
                </table>


                <table>
                    <thead>
                        <tr>
                            <th rowspan="2">N.</th>
                            <th rowspan="2" class="nombre-col">Nombre del alumno</th>

                            <?php
                            // Agrupar fechas por mes
                            $fechas_por_mes = [];
                            foreach ($fechas as $f) {
                                $mes = date('F Y', strtotime($f)); // Ej: "November 2025"
                                $fechas_por_mes[$mes][] = $f;
                            }

                            // Primera fila: nombre del mes con colspan
                            foreach ($fechas_por_mes as $mes => $dias) {
                                echo '<th colspan="' . count($dias) . '">' . strftime('%B %Y', strtotime($dias[0])) . '</th>';
                            }

                            // Asistencias y Calificación Parcial
                            echo '<th colspan="2">Asistencias</th>';
                            echo '<th colspan="2">Calificación Parcial</th>';

                            // Observaciones al final, abarca las dos filas
                            echo '<th rowspan="2" class="observaciones">Observaciones</th>';
                            ?>
                        </tr>
                        <tr>
                            <?php
                            // Segunda fila: los días individuales
                            foreach ($fechas_por_mes as $dias) {
                                foreach ($dias as $f) {
                                    echo '<th>' . nombre_dia_corto($f) . ' ' . date('d/m', strtotime($f)) . '</th>';
                                }
                            }

                            // Subcolumnas Asistencias
                            echo '<th>Total</th>';
                            echo '<th>Faltas</th>';

                            // Subcolumnas Calificación Parcial
                            echo '<th>Número</th>';
                            echo '<th>Letra</th>';
                            ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($alumnos as $index => $a): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($a['nombre']); ?></td>

                                <?php foreach ($fechas as $f): ?>
                                    <td>&nbsp;</td>
                                <?php endforeach; ?>

                                <!-- Subcolumnas Asistencias -->
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>

                                <!-- Subcolumnas Calificación Parcial -->
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>

                                <!-- Observaciones -->
                                <td>&nbsp;</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>

            </div>
        </div>

</body>

</html>