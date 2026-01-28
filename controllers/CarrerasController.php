<?php
session_start();

require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/CarrerasModel.php";

$carreraModel = new CarrerasModel($conn);

/* ================= AGREGAR ================= */
if (isset($_POST['accion']) && $_POST['accion'] === 'agregar') {

    $ok = $carreraModel->agregarCarrera($_POST['nombre'], $_POST['clave']);

    $_SESSION['alerta'] = [
        'tipo' => $ok ? 'success' : 'error',
        'mensaje' => $ok
            ? 'Carrera agregada correctamente'
            : 'No se pudo agregar la carrera'
    ];

    header("Location: ../views/carreras.php");
    exit;
}

/* ================= EDITAR ================= */
elseif (isset($_POST['accion']) && $_POST['accion'] === 'editar') {

    $ok = $carreraModel->editarCarrera(
        $_POST['id_carrera'],
        $_POST['nombre'],
        $_POST['clave']
    );

    $_SESSION['alerta'] = [
        'tipo' => $ok ? 'success' : 'error',
        'mensaje' => $ok
            ? 'Carrera editada correctamente'
            : 'No se pudo editar la carrera'
    ];

    header("Location: ../views/carreras.php");
    exit;
}

/* ================= ELIMINAR ================= */
elseif (isset($_GET['eliminar'])) {

    $ok = $carreraModel->eliminarCarrera($_GET['eliminar']);

    $_SESSION['alerta'] = [
        'tipo' => $ok ? 'success' : 'error',
        'mensaje' => $ok
            ? 'Carrera eliminada correctamente'
            : 'No se pudo eliminar la carrera'
    ];

    header("Location: ../views/carreras.php");
    exit;
}
