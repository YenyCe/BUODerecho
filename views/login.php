<?php
session_start();
if (isset($_SESSION['id_usuario'])) {
    header("Location: ../views/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Universidad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="login-body">

<div class="login-card">

    <div class="login-header">
        <img src="../img/logo1.png" alt="Logo Universidad">
        <h4>Iniciar Sesión</h4>
    </div>

    <!-- ALERTAS -->
    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Usuario o contraseña incorrectos
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif(isset($_GET['inactivo'])): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            Usuario inactivo, contacte al administrador
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- FORMULARIO -->
    <form action="../controllers/LoginController.php" method="POST">
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuario</label>
            <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Ingrese su usuario" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Ingrese su contraseña" required>
               
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
    </form>

    <div class="text-center mt-3">
        <small class="text-muted">© <?= date('Y') ?> BUO. Todos los derechos reservados.</small>
    </div>
</div>

<script>
    function togglePassword() {
        const pass = document.getElementById('password');
        pass.type = pass.type === 'password' ? 'text' : 'password';
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
