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

$id_docente = (int)($_GET['id_docente'] ?? 0);
$id_carrera = $_SESSION['id_carrera'] ?? 1;

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
        s.numero AS semestre,
        m.nombre AS materia,
        GROUP_CONCAT(
            CONCAT(
                h.dias, ' ',
                TIME_FORMAT(h.hora_inicio,'%H:%i'),
                ' a ',
                TIME_FORMAT(h.hora_fin,'%H:%i')
            )
            SEPARATOR ', '
        ) AS horario,
        SUM(TIMESTAMPDIFF(MINUTE, h.hora_inicio, h.hora_fin))/60 AS horas
    FROM horarios h
    INNER JOIN materias m ON h.id_materia = m.id_materia
    INNER JOIN grupos g ON h.id_grupo = g.id_grupo
    INNER JOIN semestres s ON g.id_semestre = s.id_semestre
    WHERE h.id_docente = $id_docente
    GROUP BY s.numero, m.nombre
    ORDER BY s.numero
");

/* ================= FECHA ================= */
$fecha = "Oaxaca de Juárez, Oaxaca a ".date('d')." de ".date('F')." del ".date('Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Carga Académica</title>

<style>
@page {
  size: letter portrait;
  margin: 20mm;
}

body {
  font-family: Arial, Helvetica, sans-serif;
  margin: 0;
}

.page {
  background: url('/img/<?= $membrete ?>') no-repeat center;
  background-size: contain;
  min-height: 279mm;
}

.contenido {
  padding: 70px 40px;
}

.encabezado {
  text-align: right;
  font-size: 12px;
}

.titulo {
  margin-top: 30px;
  font-weight: bold;
}

.texto {
  margin-top: 20px;
  text-align: justify;
  font-size: 13px;
}

.tabla {
  width: 100%;
  border-collapse: collapse;
  margin-top: 25px;
  font-size: 13px;
}

.tabla th, .tabla td {
  border: 1px solid #000;
  padding: 6px;
}

.tabla th {
  background: #f2f2f2;
}

.firma {
  margin-top: 70px;
  text-align: center;
}
</style>
</head>

<body>

<button onclick="window.print()" class="print-btn">Imprimir</button>

<div class="page">
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
