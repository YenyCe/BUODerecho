<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/AlumnosModel.php";
require_once "../models/SemestresModel.php";

$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

$alumnoModel = new AlumnosModel($conn);
$semestreModel  = new SemestresModel($conn);

// Obtener grupos según rol
if ($rol === 'admin') {
    $grupos = $semestreModel->getGrupos();
} else {
    $grupos = $semestreModel->getGruposPorCarrera($id_carrera);
}

ob_start();
?>

<div class="container">
    <h2>Grupos</h2>

    <?php if (empty($grupos)): ?>
        <p>No hay grupos disponibles.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($grupos as $g): ?>
                <li>
                    <a href="alumnos_por_grupo.php?id_grupo=<?= $g['id_grupo']; ?>">
                        <?= htmlspecialchars($g['nombre_grupo']); ?>
                    </a>
                    <?php if ($rol === 'admin'): ?>
                        <div style="font-size: 0.85rem; color:#555;">
                            Carrera: <?= htmlspecialchars($g['nombre_carrera']); ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$title = "Grupos";
$pagina = "grupos";
include "dashboard.php";
?>