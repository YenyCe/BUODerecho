<?php
session_start();
require_once "../config/conexion.php";
require_once "../models/ParcialesModel.php";

$Parciales = new ParcialesModel($conn);
$rol = $_SESSION['rol'];
$id_carrera_sesion = $_SESSION['id_carrera'] ?? null;

/* --- AGREGAR --- */
if (isset($_POST['accion']) && $_POST['accion'] === "agregar") {

    $numero = $_POST['numero_parcial'];
    $inicio = $_POST['fecha_inicio'];
    $fin = $_POST['fecha_fin'];

    $id_carrera = ($rol === "coordinador") ? $id_carrera_sesion : $_POST['id_carrera'];

    if ($Parciales->agregarParcial($numero, $inicio, $fin, $id_carrera)) {
        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => 'Parcial agregado correctamente'
        ];
    } else {
        $_SESSION['alerta'] = [
            'tipo' => 'error',
            'mensaje' => 'No se pudo agregar el parcial'
        ];
    }

    header("Location: ../views/parciales.php");
    exit();
}

/* --- EDITAR --- */
if (isset($_POST['accion']) && $_POST['accion'] === "editar") {

    $id = $_POST['id_parcial'];
    $numero = $_POST['numero_parcial'];
    $inicio = $_POST['fecha_inicio'];
    $fin = $_POST['fecha_fin'];

    if ($Parciales->editarParcial($id, $numero, $inicio, $fin)) {
        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => 'Parcial actualizado correctamente'
        ];
    } else {
        $_SESSION['alerta'] = [
            'tipo' => 'error',
            'mensaje' => 'No se pudo actualizar el parcial'
        ];
    }

    header("Location: ../views/parciales.php");
    exit();
}

/* --- ELIMINAR --- */
if (isset($_GET['eliminar'])) {

    if ($Parciales->eliminarParcial($_GET['eliminar'])) {
        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => 'Parcial eliminado correctamente'
        ];
    } else {
        $_SESSION['alerta'] = [
            'tipo' => 'error',
            'mensaje' => 'No se pudo eliminar el parcial'
        ];
    }

    header("Location: ../views/parciales.php");
    exit();
}
