<?php
require_once "../config/conexion.php";
require_once "../models/UsuariosModel.php";

$usuarioModel = new UsuariosModel($conn);

// Agregar
if(isset($_POST['accion']) && $_POST['accion'] == 'agregar'){
    $usuarioModel->agregarUsuario(
        $_POST['nombre'],
        $_POST['correo'],
        $_POST['usuario'],
        $_POST['password'],
        $_POST['rol'],
        $_POST['id_carrera'] ?? null
    );
    header("Location: ../views/usuarios.php?msg=success");
    exit();
}

// Editar
if(isset($_POST['accion']) && $_POST['accion'] == 'editar'){
    $usuarioModel->editarUsuario(
        $_POST['id_usuario'],
        $_POST['nombre'],
        $_POST['correo'],
        $_POST['usuario'],
        $_POST['rol'],
        $_POST['estado'],
        $_POST['id_carrera'] ?? null
    );
    header("Location: ../views/usuarios.php?msg=edited");
    exit();
}

// Eliminar
if(isset($_GET['eliminar'])){
    $usuarioModel->eliminarUsuario($_GET['eliminar']);
    header("Location: ../views/usuarios.php?msg=deleted");
    exit();
}
