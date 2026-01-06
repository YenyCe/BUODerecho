<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "../config/conexion.php";

/* ================= SEGURIDAD ================= */
if (!isset($_SESSION['id_usuario'])) {
    die("403");
}

$id_carrera = !empty($_SESSION['id_carrera']) ? $_SESSION['id_carrera'] : null;

if(!$id_carrera){
    die("403");
}

$id_docente = (int)($_GET['id_docente'] ?? 0);

if (!$id_docente) {
    die("Docente inválido");
}

/* ================= MEMBRETE ================= */
$membrete = '';
switch ($id_carrera) {
    case 1: $membrete = 'logo2.jpg'; break;
    case 3: $membrete = 'me.png'; break;
}

/* ================= DOCENTE ================= */
$docente = $conn->query("
    SELECT CONCAT(nombre,' ',apellidos) AS nombre
    FROM docentes
    WHERE id_docente = $id_docente
")->fetch_assoc();

if (!$docente) {
    die("Docente no encontrado");
}

/* ================= CARGA ACADÉMICA ================= */
$carga = $conn->query("
    SELECT
        m.id_semestre AS semestre,
        m.nombre AS materia,
        h.horario_texto AS horario,
        m.horas_semana AS horas
    FROM horarios h
    INNER JOIN materias m ON h.id_materia = m.id_materia
    WHERE h.id_docente = $id_docente
    ORDER BY m.id_semestre, m.nombre
");


/* ================= FECHA ================= */
$fecha = "Oaxaca de Juárez, Oaxaca a ".date('d')." de ".date('F')." del ".date('Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Carga Académica</title>
<link rel="stylesheet" href="../css/control_docente.css">

</head>

<body>

<button onclick="window.print()" class="print-btn">Imprimir</button>

<div class="page" style="
  background: url('/img/<?= $nombre_membreteV; ?>') no-repeat center;
  background-size: contain;">

<div class="contenido">

<div class="encabezado">
<?= $fecha ?><br>
<strong>ASUNTO:</strong> CARGA ACADÉMICA
</div>

<div class="titulo">
<?= strtoupper($docente['nombre']) ?><br>
DOCENTE DE ASIGNATURA<br>
PRESENTE
</div>

<div class="texto">
La Benemérita Universidad de Oaxaca (BUO), a través de sus Programas Académicos,
asume el compromiso de formar profesionales de excelencia; por lo que resulta
fundamental contar con docentes de alto nivel académico.

En este sentido, se detallan a continuación las horas clase asignadas durante
el ciclo escolar vigente:
</div>

<table class="tabla">
<thead>
<tr>
    <th>SEMESTRE</th>
    <th>MATERIA</th>
    <th>DÍAS Y HORAS</th>
    <th>HORAS</th>
</tr>
</thead>
<tbody>
<?php while ($r = $carga->fetch_assoc()): ?>
<tr>
    <td align="center"><?= $r['semestre'] ?></td>
    <td><?= $r['materia'] ?></td>
    <td><?= $r['horario'] ?></td>
    <td align="center"><?= (int)$r['horas'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<div class="texto">
Seguros del amplio intercambio de experiencias educativas, me suscribo reiterándole
mis consideraciones.
</div>

<div class="firma">
<strong>MTRA. ADABELIA PELÁEZ GARCÍA</strong><br>
Directora de la Facultad de Ciencias Jurídicas y Humanidades<br>
Benemérita Universidad de Oaxaca
</div>

</div>
</div>

</body>
</html>
