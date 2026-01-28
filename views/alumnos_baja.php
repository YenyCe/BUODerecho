<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/AlumnosModel.php";

$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

$alumnosModel = new AlumnosModel($conn);
$alumnosBaja = $alumnosModel->getAlumnosBaja($id_carrera);
ob_start();
?>

<div class="container-form">
    <h2>Alumnos de Baja</h2>
    <table class="tabla-docentes">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Carrera</th>
                <th>Motivo de baja</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($alumnosBaja)): ?>
                <?php foreach ($alumnosBaja as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['nombre']) ?></td>
                        <td><?= htmlspecialchars($a['carrera']) ?></td>
                        <td><?= htmlspecialchars($a['motivo_baja'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No hay alumnos de baja.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
    
<?php
$content = ob_get_clean();
$title = "Alumnos de Baja";
$pagina = "alumnos_baja";
include "dashboard.php";
?>