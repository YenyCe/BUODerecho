<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/AlumnosModel.php";

$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

$alumnosModel = new AlumnosModel($conn);
$alumnosBaja = $alumnosModel->getAlumnosBaja($id_carrera);

// Alertas
$alerta = "";
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === "success") {
        $alerta = "<div class='alerta success'>Acción realizada correctamente</div>";
    }
    if ($_GET['msg'] === "error") {
        $razon = htmlspecialchars($_GET['reason'] ?? "Error desconocido");
        $alerta = "<div class='alerta error'>Error: $razon</div>";
    }
}

ob_start();
?>

<div class="container-form">
    <h2>Alumnos de Baja</h2>

    <?php if ($alerta): ?>
        <div id="alertaMsg" class="alerta <?php echo (strpos($alerta,'success') ? 'success':'error'); ?>">
            <span><?php echo strip_tags($alerta); ?></span>
            <span class="cerrar-alerta" onclick="cerrarAlerta()">&times;</span>
        </div>
    <?php endif; ?>

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
function cerrarAlerta() {
    const alerta = document.getElementById("alertaMsg");
    alerta.classList.add("ocultar");
    setTimeout(() => alerta.remove(), 600);
}
</script>

<?php
$content = ob_get_clean();
$title = "Alumnos de Baja";
$pagina = "alumnos_baja";
include "dashboard.php";
?>
