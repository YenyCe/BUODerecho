<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/CargaAcademicaModel.php";

/* ================= SEGURIDAD ================= */
if (!isset($_SESSION['id_usuario'])) {
    die("403");
}

$id_docente = (int)($_GET['id_docente'] ?? 0);
if (!$id_docente) {
    die("Docente inválido");
}

/* ================= DOCENTE ================= */
$docente = $conn->query("
    SELECT 
        d.id_docente,
        CONCAT(d.nombre,' ',d.apellidos) AS nombre,
        d.genero,
        d.id_carrera,
        c.nombre AS carrera
    FROM docentes d
    INNER JOIN carreras c ON d.id_carrera = c.id_carrera
    WHERE d.id_docente = $id_docente
")->fetch_assoc();

if (!$docente) {
    die("Docente no encontrado");
}

$id_carrera = $docente['id_carrera'];
$saludo = ($docente['genero'] === 'M') ? 'BIENVENIDA' : 'BIENVENIDO';

/* ================= CONFIGURACIÓN ================= */
$model = new CargaAcademicaModel($conn);
$config = $model->getConfigByCarrera($id_carrera);

if (!$config) {
    die("No existe configuración de carga académica para esta carrera");
}

/* ================= MEMBRETE (OPCIONAL) ================= */
$membrete = '';
switch ($id_carrera) {
    case 1: $membrete = 'logo2.jpg'; break;
    case 3: $membrete = 'me.png'; break;
    case 6: $membrete = 'D.jpg'; break;
}

/* ================= HORARIOS ================= */
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
$fecha_input = $_GET['fecha'] ?? date('Y-m-d');

$meses = [
    1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',
    5=>'mayo',6=>'junio',7=>'julio',8=>'agosto',
    9=>'septiembre',10=>'octubre',11=>'noviembre',12=>'diciembre'
];

$timestamp = strtotime($fecha_input);
$fecha = "Oaxaca de Juárez, Oaxaca a "
        . date('d', $timestamp)
        . " de "
        . $meses[(int)date('m', $timestamp)]
        . " del "
        . date('Y', $timestamp);

ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Carga Académica</title>
<link rel="stylesheet" href="../css/carga_academica.css">
</head>

<body>
<button onclick="window.print()" class="print-btn">Imprimir</button>

<form method="GET" style="margin:20px;">
    <input type="hidden" name="id_docente" value="<?= $id_docente ?>">
    <label><strong>Seleccionar fecha del documento:</strong></label><br>
    <input type="date" name="fecha" value="<?= htmlspecialchars($fecha_input) ?>">
    <button type="submit">Aplicar fecha</button>
</form>

<div class="page" style="
  background: url('../img/<?= $membrete ?>') no-repeat center;
  background-size: contain;">

<div class="contenido">

<div class="encabezado">
      <?php
        $clave_oficio_final = $_GET['clave_oficio'] ?? $config['clave_oficio'];
    ?>
    <?= $fecha ?><br>
            <!-- Clave de oficio -->
        <input type="text" name="clave_oficio" value="<?= htmlspecialchars($clave_oficio_final) ?>" style="border:none; background:transparent; font-size:inherit; text-align: right;"><br>
 
    <strong>ASUNTO:</strong> CARGA ACADÉMICA
</div>

<div class="titulo">
    <strong><?= strtoupper($docente['nombre']) ?></strong><br>
    DOCENTE DE ASIGNATURA DE LA LICENCIATURA <br>
    EN <?= strtoupper($docente['carrera']) ?> DE LA BENEMÉRITA UNIVERSIDAD DE OAXACA<br>
    PRESENTE
</div>

<div class="texto">
       <p> 

    <?= nl2br(htmlspecialchars($config['texto_presentacion'])) ?>
      </p>
</div>

<p class="bienvenida">
    <span class="bienvenida-naranja"><?= $saludo ?> A NUESTRO</span><br>
    <span class="bienvenida-azul"> CLAUSTRO DOCENTE BUO, CICLO ESCOLAR <?= strtoupper($config['ciclo_escolar']) ?></span>
</p>



<div class="texto">
    <p>
    <?= nl2br(htmlspecialchars($config['claustro_texto'])) ?>
    </p>
</div>

<table class="tabla">
    <thead>
        <tr class="fila-docente">
            <td class="docente-label">DOCENTE</td>
            <td class="docente-nombre" colspan="3">
                <?= strtoupper($docente['nombre']) ?>
            </td>
        </tr>
        <tr class="fila-detalle">
            <th>SEMESTRE</th>
            <th>MATERIA</th>
            <th>DÍAS</th>
            <th>HORAS</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($r = $carga->fetch_assoc()): ?>
        <tr class="fila-detalle-datos">
            <td align="center"><?= $r['semestre'] ?></td>
            <td><?= htmlspecialchars($r['materia']) ?></td>
            <td><?= htmlspecialchars($r['horario']) ?></td>
            <td align="center"><?= (int)$r['horas'] ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<div class="texto">
    <p>
        <?= nl2br(htmlspecialchars($config['texto_pie'])) ?>
    </p>
       <br>
</div>

<div class="firma">
    <div class="firma-imagen">
        <img src="../img/<?= htmlspecialchars($config['archivo_firma']) ?>" alt="Firma">
    </div>

    <p class="firma-atentamente">ATENTAMENTE</p><br><br>

    <p class="firma-nombre">
        <?= strtoupper($config['nombre_director']) ?>
    </p>

    <p class="firma-cargo">
        <?= nl2br(htmlspecialchars($config['cargo_director'])) ?>
    </p>
</div>

</div>
</div>

</body>
</html>
