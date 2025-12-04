<?php
require_once "../config/conexion.php";
require_once "../models/ParcialesModel.php";
session_start();

$Parciales = new ParcialesModel($conn);
$rol = $_SESSION['rol'];
$id_carrera_sesion = $_SESSION['id_carrera'] ?? null;

if ($_POST['accion'] === "agregar") {

    $numero = $_POST['numero_parcial'];
    $inicio = $_POST['fecha_inicio'];
    $fin = $_POST['fecha_fin'];

    // Coordinador: usa su carrera automáticamente
    if ($rol === "coordinador") {
        $id_carrera = $id_carrera_sesion;
    } else {
        $id_carrera = $_POST['id_carrera'];
    }

    $Parciales->agregarParcial($numero, $inicio, $fin, $id_carrera);
    header("Location: ../views/parciales.php?msg=success");
    exit();
}

if ($_POST['accion'] === "editar") {
    $id = $_POST['id_parcial'];
    $numero = $_POST['numero_parcial'];
    $inicio = $_POST['fecha_inicio'];
    $fin = $_POST['fecha_fin'];

    $Parciales->editarParcial($id, $numero, $inicio, $fin);
    header("Location: ../views/parciales.php?msg=edited");
    exit();
}

if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $Parciales->eliminarParcial($id);
    header("Location: ../views/parciales.php?msg=deleted");
    exit();
}
?>
