<?php
$rol = $_SESSION['rol'] ?? '';
$pagina = $pagina ?? 'dashboard'; // Si la página no define $pagina, usar 'dashboard'
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? "Sistema Académico"; ?></title>
    <link rel="stylesheet" href="/ASISTENCIAS/css/styles.css">
</head>

<body>
    <div class="sidebar">
        <img src="../img/logo1.png" alt="Logo" />
        <h2>Sistema Académico</h2>

        <ul>
            <li><a href="inicio.php" class="<?= $pagina == 'inicio' ? 'active' : '' ?>">Inicio</a></li>

            <?php if ($rol === 'admin'): ?>
                <li><a href="usuarios.php" class="<?= $pagina == 'usuarios' ? 'active' : '' ?>">Usuarios</a></li>
                <li><a href="carreras.php" class="<?= $pagina == 'carreras' ? 'active' : '' ?>">Carreras</a></li>
                <li><a href="docentes.php" class="<?= $pagina == 'docentes' ? 'active' : '' ?>">Docentes</a></li>
                <li><a href="materias.php" class="<?= $pagina == 'materias' ? 'active' : '' ?>">Materias</a></li>
                <li><a href="alumnos.php" class="<?= $pagina == 'alumnos' ? 'active' : '' ?>">Alumnos</a></li>
                <li><a href="grupos.php" class="<?= $pagina == 'grupos' ? 'active' : '' ?>">Grupos</a></li>
                <li><a href="semestres_grupos.php" class="<?= $pagina == 'semestres_grupos' ? 'active' : '' ?>">Semestres y Grupos</a></li>
                <li><a href="parciales.php" class="<?= $pagina == 'parciales' ? 'active' : '' ?>">Parciales</a></li>
                <li><a href="generar_listas.php" class="<?= $pagina == 'generar_listas' ? 'active' : '' ?>">Listas</a></li>
                <li><a href="horarios.php" class="<?= $pagina == 'horarios' ? 'active' : '' ?>">Horarios</a></li>
                 <li><a href="alumnos_baja.php" class="<?= $pagina == 'alumnos_baja' ? 'active' : '' ?>">alumnos_baja</a></li>
            <?php elseif ($rol === 'coordinador'): ?>
                <li><a href="docentes.php" class="<?= $pagina == 'docentes' ? 'active' : '' ?>">Docentes</a></li>
                <li><a href="materias.php" class="<?= $pagina == 'materias' ? 'active' : '' ?>">Materias</a></li>
                <li><a href="alumnos.php" class="<?= $pagina == 'alumnos' ? 'active' : '' ?>">Alumnos</a></li>
                <li><a href="grupos.php" class="<?= $pagina == 'grupos' ? 'active' : '' ?>">Grupos</a></li>
                <li><a href="semestres_grupos.php" class="<?= $pagina == 'semestres_grupos' ? 'active' : '' ?>">Semestres y Grupos</a></li>
                <li><a href="parciales.php" class="<?= $pagina == 'parciales' ? 'active' : '' ?>">Parciales</a></li>
                <li><a href="generar_listas.php" class="<?= $pagina == 'generar_listas' ? 'active' : '' ?>">Listas</a></li>
                <li><a href="horarios.php" class="<?= $pagina == 'horarios' ? 'active' : '' ?>">Horarios</a></li>
                <li><a href="alumnos_baja.php" class="<?= $pagina == 'alumnos_baja' ? 'active' : '' ?>">alumnos_baja</a></li>
            <?php endif; ?>

            <li><a href="salir.php">Cerrar Sesión</a></li>
        </ul>
    </div>

    <div class="main">
        <?php
        // Contenido de la página
        echo $content ?? '';
        ?>
    </div>

</body>

</html>
