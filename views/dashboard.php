<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? "Sistema Académico"; ?></title>
    <link rel="stylesheet" href="/ASISTENCIAS/css/styles.css">

</head>
<body>
<div class="sidebar">
    <img src="../img/logo1.png" alt="Logo" />
    <h2>Sistema Académico</h2>
<?php $pagina = basename($_SERVER['PHP_SELF']); ?>
<ul>
    <li><a href="dashboard.php" class="<?= $pagina=='dashboard.php' ? 'active' : '' ?>">Inicio</a></li>
    <li><a href="docentes.php" class="<?= $pagina=='docentes.php' ? 'active' : '' ?>">Docentes</a></li>
    <li><a href="materias.php" class="<?= $pagina=='materias.php' ? 'active' : '' ?>">Materias</a></li>
    <li><a href="alumnos.php" class="<?= $pagina=='alumnos.php' ? 'active' : '' ?>">Alumnos</a></li>
    <li><a href="semestres_grupos.php" class="<?= $pagina=='semestres_grupos.php' ? 'active' : '' ?>">Semestre y Grupos</a></li>
    <li><a href="parciales.php" class="<?= $pagina=='parciales.php' ? 'active' : '' ?>">Parciales</a></li>
    <li><a href="generar_listas.php" class="<?= $pagina=='generar_listas.php' ? 'active' : '' ?>">Lista</a></li>
    <li><a href="horarios.php" class="<?= $pagina=='horarios.php' ? 'active' : '' ?>">Horario</a></li>
        <li><a href="carreras.php" class="<?= $pagina=='carreras.php' ? 'active' : '' ?>">Carreras</a></li>
    <li><a href="salir.php">Cerrar Sesión</a></li>
</ul>

</div>



<div class="main">
    <?php echo $content; ?>
</div>
<script src="../js/modals.js"></script>

</body>
</html>
