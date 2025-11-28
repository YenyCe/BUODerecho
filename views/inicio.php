<?php
require_once "../middlewares/auth.php"; // Ya inicia sesión y valida

// Tomar variables de sesión
$rol = $_SESSION['rol'] ?? '';
$nombre = $_SESSION['nombre'] ?? 'Usuario';

// Captura del contenido
ob_start();
?>
<div class="container-form">
    <h2>Bienvenido, <?= htmlspecialchars($nombre) ?>!</h2>
    <p>Este es el panel de inicio de tu sistema académico.</p>

    <?php if($rol === 'admin'): ?>
        <p>Como administrador, puedes gestionar usuarios, carreras, materias, alumnos y horarios.</p>
    <?php else: ?>
        <p>Como coordinador, puedes ver y administrar solo la información de tu carrera.</p>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
$title = "Inicio";
$pagina = "inicio";
// Carga el layout
include "dashboard.php";
