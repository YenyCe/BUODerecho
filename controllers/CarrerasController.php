<?php
require_once "../config/conexion.php";
require_once "../models/CarrerasModel.php";

$carreraModel = new CarrerasModel($conn);

// Agregar
if (isset($_POST['accion']) && $_POST['accion'] == 'agregar') {
    $carreraModel->agregarCarrera($_POST['nombre'], $_POST['clave']);
    header("Location: ../views/carreras.php?msg=success");
    exit();
}

// Editar
if (isset($_POST['accion']) && $_POST['accion'] == 'editar') {
    $carreraModel->editarCarrera($_POST['id_carrera'], $_POST['nombre'], $_POST['clave']);
    header("Location: ../views/carreras.php?msg=edited");
    exit();
}

// Eliminar
if (isset($_GET['eliminar'])) {
    $carreraModel->eliminarCarrera($_GET['eliminar']);
    header("Location: ../views/carreras.php?msg=deleted");
    exit();
}
