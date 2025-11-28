<?php
session_start();
if(isset($_SESSION['id_usuario'])){
    header("Location: ../views/dashboard.php");

    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">

            <div class="card shadow">
                <div class="card-header text-center">
                    <h4>Iniciar Sesión</h4>
                </div>

                <div class="card-body">
                    <?php if(isset($_GET['error'])): ?>
                        <div class="alert alert-danger">Usuario o contraseña incorrectos</div>
                    <?php endif; ?>

                    <?php if(isset($_GET['inactivo'])): ?>
                        <div class="alert alert-warning">Usuario inactivo, contacte al administrador</div>
                    <?php endif; ?>

                    <form action="../controllers/LoginController.php" method="POST">
                        <div class="mb-3">
                            <label>Usuario</label>
                            <input type="text" name="usuario" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
