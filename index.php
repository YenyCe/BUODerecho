<?php
require_once "middlewares/auth.php";

ob_start();
?>
<h1>Bienvenido, <?php echo $_SESSION['nombre']; ?></h1>
<p>Rol: <?php echo $_SESSION['rol']; ?></p>
<?php
$content = ob_get_clean();
$title = "Dashboard";
include "dashboard.php";
?>
