<?php
// lista_impresion.php
require_once "../config/conexion.php";
require_once "../models/helpers_asistencia.php";

// Recibir datos
$id_docente = isset($_POST['id_docente']) ? (int)$_POST['id_docente'] : 0;
$id_materia  = isset($_POST['id_materia'])  ? (int)$_POST['id_materia'] : 0;
$id_grupo    = isset($_POST['id_grupo'])    ? (int)$_POST['id_grupo'] : 0;
$id_parcial  = isset($_POST['id_parcial'])  ? (int)$_POST['id_parcial'] : 0;
$fecha_inicio = $_POST['fecha_inicio'] ?? null;
$fecha_fin    = $_POST['fecha_fin'] ?? null;

// Validaciones básicas
if(!$id_docente || !$id_materia || !$id_grupo){
    die("Faltan parámetros obligatorios. Vuelve atrás y completa docente, materia y grupo.");
}

// Si seleccionó parcial, obtener fechas de parcial
if($id_parcial){
    $p = $conn->query("SELECT fecha_inicio, fecha_fin, numero_parcial FROM parciales WHERE id_parcial = $id_parcial")->fetch_assoc();
    if(!$p) die("Parcial no encontrado.");
    $fecha_inicio = $p['fecha_inicio'];
    $fecha_fin = $p['fecha_fin'];
} else {
    if(!$fecha_inicio || !$fecha_fin){
        die("Si no seleccionas parcial debes indicar fecha inicio y fecha fin.");
    }
}

// Traer datos para encabezado
$docente = $conn->query("SELECT CONCAT(nombre,' ',apellidos) AS nombre FROM docentes WHERE id_docente = $id_docente")->fetch_assoc();
$materia = $conn->query("SELECT * FROM materias WHERE id_materia = $id_materia")->fetch_assoc();
$grupo = $conn->query("SELECT g.nombre AS nombre_grupo, s.numero AS semestre_num
                       FROM grupos g
                       INNER JOIN semestres s ON g.id_semestre = s.id_semestre
                       WHERE g.id_grupo = $id_grupo")->fetch_assoc();

// Traer alumnos
$alumnos = $conn->query("SELECT * FROM alumnos WHERE id_grupo = $id_grupo ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);

// Traer horarios del docente para esa materia y grupo
$horarios = obtener_horarios_docente($conn, $id_docente, $id_materia, $id_grupo);

// Si no hay horarios mostrar mensaje
if(empty($horarios)){
    die("No se encontraron horarios para ese docente/materia/grupo. Revisa la tabla 'horarios'.");
}

// Generar fechas de clase
$fechas = generar_fechas_por_horarios($fecha_inicio, $fecha_fin, $horarios);

// Si no hay fechas dentro del rango
if(empty($fechas)){
    die("No hay fechas de clase dentro del rango seleccionado (según los días registrados en horarios).");
}

// Imprimir vista
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de asistencia - Imprimir</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;margin:20px}
        .header{display:flex;justify-content:space-between;align-items:flex-start; gap:10px}
        .encabezado{font-size:14px}
        .tabla{width:100%;border-collapse:collapse;margin-top:12px}
        .tabla th,.tabla td{border:1px solid #333;padding:6px;text-align:left;font-size:12px}
        .tabla th{background:#f2f2f2}
        .nombre-col{width:260px}
        .small{font-size:11px;color:#555}
        .print-btn{position:fixed;right:20px;top:20px;padding:8px 12px}
        @media print{
            .print-btn{display:none}
            body{margin:0}
        }
    </style>
</head>
<body>
<button onclick="window.print()" class="print-btn">Imprimir</button>

<div class="header">
    <div class="encabezado">
        <h3>Lista de Asistencia</h3>
        <div><strong>Materia:</strong> <?php echo htmlspecialchars($materia['nombre'] ?? '-'); ?> &nbsp; <strong>Clave:</strong> <?php echo htmlspecialchars($materia['clave'] ?? '-'); ?></div>
        <div><strong>Horas semestre:</strong> <?php echo htmlspecialchars($materia['horas_semestre'] ?? '-'); ?> &nbsp; <strong>Horas semana:</strong> <?php echo htmlspecialchars($materia['horas_semana'] ?? '-'); ?></div>
    </div>
    <div class="encabezado" style="text-align:right">
        <div><strong>Grupo / Semestre:</strong> <?php echo htmlspecialchars($grupo['nombre_grupo'] ?? '-') . " / " . htmlspecialchars($grupo['semestre_num'] ?? '-'); ?></div>
        <div><strong>Docente:</strong> <?php echo htmlspecialchars($docente['nombre'] ?? '-'); ?></div>
        <div><strong>Parcial / Rango:</strong> 
            <?php 
                if($id_parcial) echo "Parcial ".$p['numero_parcial']." (".$fecha_inicio." a ".$fecha_fin.")";
                else echo $fecha_inicio." a ".$fecha_fin;
            ?>
        </div>
    </div>
</div>

<table class="tabla">
    <thead>
        <tr>
            <th class="nombre-col">Nombre del alumno</th>
            <?php foreach($fechas as $f): ?>
                <th><?php echo nombre_dia_corto($f) . " " . date('d/m', strtotime($f)); ?></th>
            <?php endforeach; ?>
            <th>Total</th>
            <th>Faltas</th>
            <th>Calif. parciales</th>
            <th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($alumnos as $a): ?>
            <tr>
                <td><?php echo htmlspecialchars($a['nombre']); ?></td>
                <?php foreach($fechas as $f): ?>
                    <td>&nbsp;</td>
                <?php endforeach; ?>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p class="small" style="margin-top:12px">Generado: <?php echo date('Y-m-d H:i'); ?> — Imprimir y rellenar a mano.</p>

</body>
</html>
