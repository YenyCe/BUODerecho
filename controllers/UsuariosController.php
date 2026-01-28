<?php
session_start();
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/UsuariosModel.php";

$usuarioModel = new UsuariosModel($conn);

/* ================= AGREGAR ================= */
if (isset($_POST['accion']) && $_POST['accion'] === 'agregar') {

    $usuarioModel->agregarUsuario(
        $_POST['nombre'],
        $_POST['correo'],
        $_POST['usuario'],
        $_POST['password'],
        $_POST['rol'],
        $_POST['id_carrera'] ?? null
    );

    $_SESSION['alerta'] = [
        'tipo' => 'success',
        'mensaje' => 'Usuario agregado correctamente'
    ];

    header("Location: ../views/usuarios.php");
    exit();
}

// Editar
if (isset($_POST['accion']) && $_POST['accion'] == 'editar') {
    $usuarioModel->editarUsuario(
        $_POST['id_usuario'],
        $_POST['nombre'],
        $_POST['correo'],
        $_POST['usuario'],
        $_POST['password'] ?? '',
        $_POST['rol'],
        $_POST['estado'],
        $_POST['id_carrera'] ?? null
    );

    $_SESSION['alerta'] = [
        'tipo' => 'success',
        'mensaje' => 'Usuario editado correctamente'
    ];
    header("Location: ../views/usuarios.php");
    exit();
}

/* ================= ELIMINAR ================= */
if (isset($_GET['eliminar'])) {

    $usuarioModel->eliminarUsuario($_GET['eliminar']);

    $_SESSION['alerta'] = [
        'tipo' => 'error',
        'mensaje' => 'Usuario eliminado correctamente'
    ];

    header("Location: ../views/usuarios.php");
    exit();
}


