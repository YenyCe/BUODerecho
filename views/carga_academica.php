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
    SELECT 
        CONCAT(d.nombre,' ',d.apellidos) AS nombre,
        c.nombre AS carrera
    FROM docentes d
    INNER JOIN carreras c ON d.id_carrera = c.id_carrera
    WHERE d.id_docente = $id_docente
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
// ================= FECHA SELECCIONABLE =================
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
  background: url('/img/<?= $membrete; ?>') no-repeat center;
  background-size: contain;">

<div class="contenido">

<div class="encabezado">
    <?= $fecha ?><br>
    BUO/CEL/LD/810<br>
    <strong>ASUNTO:</strong> CARGA ACADÉMICA
</div>


<div class="titulo">
     <strong><?= strtoupper($docente['nombre']) ?></strong><br>
    DOCENTE DE ASIGNATURA DE LA LICENCIATURA <br> EN <?= strtoupper($docente['carrera']) ?> DE LA BENEMÉRITA UNIVERSIDAD DE OAXACA<br>
    PRESENTE
</div>

<div class="texto">
    <p>
        La Benemérita Universidad de Oaxaca (BUO), a través del Programa Académico de la
        Licenciatura en Derecho, asume el compromiso de formar profesionales de excelencia,
        que impacten de manera positiva en nuestra sociedad, como reconocidos Licenciados
        en Derecho del país; por lo que resulta de gran importancia contar con docentes de
        alto nivel, que mediante su práctica impacten de manera positiva en nuestra
        comunidad universitaria.
    </p>

    <p>
        Conocedores de su amplia trayectoria como profesional del derecho y de su alto
        profesionalismo académico, comprometido por vocación en la labor educativa,
        agradecemos su invaluable participación en esta institución y le brindamos la más
        cordial
    </p>

    <p class="bienvenida">
        <span class="bienvenida-naranja">BIENVENIDA A NUESTRO</span><br>
        <span class="bienvenida-azul">CLAUSTRO DOCENTE BUO, CICLO ESCOLAR 2024–2025</span>
    </p>
    
    <p>
        En este sentido y con fundamento en los artículos 30, 32, 34, 35 y 36 del Reglamento
        Interno de la BUO, tengo a bien presentarle las horas clase asignadas en la
        Licenciatura en Derecho de esta institución educativa, durante el periodo
        comprendido del 19 de agosto al 21 de diciembre de 2024, que se detalla a
        continuación:
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

    <tbody >
    <?php while ($r = $carga->fetch_assoc()): ?>
   

        <!-- FILA DETALLE -->
        <tr class="fila-detalle-datos">
            <td align="center"><?= $r['semestre'] ?></td>
            <td><?= $r['materia'] ?></td>
            <td><?= $r['horario'] ?></td>
            <td align="center"><?= (int)$r['horas'] ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>


<div class="texto">
        <p>
        Seguros del amplio intercambio de experiencias educativas, me suscribo de usted
reiterándole mis consideraciones. Sin otro particular, le envió un respetuoso saludo.
    </p>
    <br>
</div>

<div class="firma">
    <div class="firma-imagen">
        <img src="/img/firma.png" alt="Firma digital">
    </div>

    <p class="firma-atentamente">ATENTAMENTE</p> <br>
<br>
    <p class="firma-nombre">MTRA. ADABELIA PELÁEZ GARCÍA</p>

    <p class="firma-cargo">
        Directora de la Facultad de Ciencias Jurídicas y Humanidades<br>
        de la Benemérita Universidad de Oaxaca
    </p>
</div>



</div>
</div>

</body>
</html>
