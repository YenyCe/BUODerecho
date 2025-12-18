<?php
session_start();
require_once "../config/conexion.php";
require_once "../models/CarrerasModel.php";

$carreraModel = new CarrerasModel($conn);

// =======================
// AGREGAR
// =======================
if (isset($_POST['accion']) && $_POST['accion'] === 'agregar') {

    if ($carreraModel->agregarCarrera($_POST['nombre'], $_POST['clave'])) {
        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => 'Carrera agregada correctamente'
        ];
    } else {
        $_SESSION['alerta'] = [
            'tipo' => 'error',
            'mensaje' => 'No se pudo agregar la carrera'
        ];
    }

    header("Location: ../views/carreras.php");
    exit();
}

// =======================
// EDITAR
// =======================
if (isset($_POST['accion']) && $_POST['accion'] === 'editar') {

    if ($carreraModel->editarCarrera($_POST['id_carrera'], $_POST['nombre'], $_POST['clave'])) {
        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => 'Carrera editada correctamente'
        ];
    } else {
        $_SESSION['alerta'] = [
            'tipo' => 'error',
            'mensaje' => 'No se pudo editar la carrera'
        ];
    }

    header("Location: ../views/carreras.php");
    exit();
}

// =======================
// ELIMINAR
// =======================
if (isset($_GET['eliminar'])) {

    if ($carreraModel->eliminarCarrera($_GET['eliminar'])) {
        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => 'Carrera eliminada correctamente'
        ];
    } else {
        $_SESSION['alerta'] = [
            'tipo' => 'error',
            'mensaje' => 'No se pudo eliminar la carrera'
        ];
    }

    header("Location: ../views/carreras.php");
    exit();
}
