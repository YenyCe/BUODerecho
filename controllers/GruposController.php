<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/AlumnosModel.php";

$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

$alumnoModel = new AlumnosModel($conn);

// Obtener grupos según rol
if ($rol === 'admin') {
    $grupos = $alumnoModel->getGrupos();
} else {
    $grupos = $alumnoModel->getGruposPorCarrera($id_carrera);
}

ob_start();
?>

<div class="container">
    <h2>Grupos</h2>

    <ul>
        <?php foreach ($grupos as $g): ?>
            <li>
                <a href="alumnos_por_grupo.php?id_grupo=<?= $g['id_grupo']; ?>">
                    <?= htmlspecialchars($g['nombre']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php
$content = ob_get_clean();
$title = "Grupos";
$pagina = "grupos";
include "dashboard.php";
?>
