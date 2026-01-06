<?php
session_start();
require_once "../config/conexion.php";

$id_carrera = $_SESSION['id_carrera'] ?? null;
$id_docente = (int)($_GET['id_docente'] ?? 0);

if (!$id_carrera || !$id_docente) {
    die("403");
}

/* ================= CONFIG GENERAL ================= */
$fecha_oficio = "Oaxaca de Juárez, Oaxaca a " . date('d') . " de " . date('F') . " del " . date('Y');
$asunto = "CARGA ACADÉMICA";
$periodo = "19 de agosto al 21 de diciembre de 2024";

/* ================= MEMBRETE ================= */
$membrete = '';
switch ($id_carrera) {
    case 1: $membrete = 'logo2.jpg'; break;
    case 3: $membrete = 'me.png'; break;
}

/* ================= DOCENTE ================= */
$docente = $conn->query("
    SELECT 
        CONCAT(nombre,' ',apellidos) AS nombre,
        nivel_estudios
    FROM docentes
    WHERE id_docente = $id_docente
")->fetch_assoc();

if (!$docente) die("Docente no encontrado");

/* ================= CARGA ACADÉMICA ================= */
$carga = $conn->query("
    SELECT
        s.numero AS semestre,
        m.nombre AS materia,
        GROUP_CONCAT(
            CONCAT(
                h.dias,' de ',
                TIME_FORMAT(h.hora_inicio,'%H:%i'),
                ' a ',
                TIME_FORMAT(h.hora_fin,'%H:%i'),
                ' hrs'
            ) SEPARATOR ', '
        ) AS horarios,
        SUM(TIMESTAMPDIFF(MINUTE, h.hora_inicio, h.hora_fin))/60 AS horas
    FROM horarios h
    INNER JOIN materias m ON h.id_materia = m.id_materia
    INNER JOIN grupos g ON h.id_grupo = g.id_grupo
    INNER JOIN semestres s ON g.id_semestre = s.id_semestre
    WHERE h.id_docente = $id_docente
    GROUP BY s.numero, m.nombre
    ORDER BY s.numero
");

/* ================= TEXTO OFICIO ================= */
$texto = "
La Benemérita Universidad de Oaxaca (BUO) a través del Programa Académico correspondiente,
asume el compromiso de formar profesionales de excelencia, que impacten de manera positiva
en nuestra sociedad; por lo que resulta de gran importancia contar con docentes de alto nivel.

Conocedores de su amplia trayectoria profesional y su alto profesionalismo académico,
agradecemos su invaluable participación en esta institución y le brindamos la más cordial
bienvenida a nuestro Claustro Docente BUO, ciclo escolar 2024–2025.

En este sentido y con fundamento en el Reglamento Interno de la BUO, tengo a bien presentarle
las horas clase asignadas durante el periodo comprendido del $periodo, mismas que se detallan
a continuación:
";
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
  padding: 60px 40px;
}

.encabezado {
  text-align: right;
  font-size: 12px;
}

.asunto {
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
  margin-top: 20px;
  font-size: 13px;
}

.tabla th, .tabla td {
  border: 1px solid #000;
  padding: 6px;
}

.tabla th {
  background: #f0f0f0;
  text-align: center;
}

.firma {
  margin-top: 60px;
  text-align: center;
}

.firma img {
  height: 60px;
}
</style>
</head>

<body>

<button onclick="window.print()" style="position:fixed; top:20px; right:20px;">Imprimir</button>

<div class="page">
<div class="contenido">

<div class="encabezado">
<?= $fecha_oficio ?><br>
<strong>ASUNTO:</strong> <?= $asunto ?>
</div>

<div class="asunto">
<?= strtoupper($docente['nombre']) ?><br>
DOCENTE DE ASIGNATURA<br>
PRESENTE
</div>

<div class="texto">
<?= nl2br($texto) ?>
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
<?php while($r = $carga->fetch_assoc()): ?>
<tr>
    <td align="center"><?= $r['semestre'] ?></td>
    <td><?= $r['materia'] ?></td>
    <td><?= $r['horarios'] ?></td>
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
<img src="/img/firma_directora.png"><br>
<strong>MTRA. ADABELIA PELÁEZ GARCÍA</strong><br>
Directora de la Facultad de Ciencias Jurídicas y Humanidades<br>
Benemérita Universidad de Oaxaca
</div>

</div>
</div>

</body>
</html>
