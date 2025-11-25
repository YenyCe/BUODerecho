<?php
require_once "../config/conexion.php";
require_once "../models/DocentesModel.php";

$docenteModel = new DocentesModel($conn);

// Agregar
if(isset($_POST['accion']) && $_POST['accion'] == 'agregar'){
    $docenteModel->agregarDocente($_POST['nombre'], $_POST['apellidos'], $_POST['correo'], $_POST['telefono']);
    header("Location: ../views/docentes.php?msg=success");
    exit();
}

// Editar
if(isset($_POST['accion']) && $_POST['accion'] == 'editar'){
    $docenteModel->editarDocente($_POST['id_docente'], $_POST['nombre'], $_POST['apellidos'], $_POST['correo'], $_POST['telefono']);
    header("Location: ../views/docentes.php?msg=edited");
    exit();
}

// Eliminar
if(isset($_GET['eliminar'])){
    $docenteModel->eliminarDocente($_GET['eliminar']);
    header("Location: ../views/docentes.php?msg=deleted");
    exit();
}
