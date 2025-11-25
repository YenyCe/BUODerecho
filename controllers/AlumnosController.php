<?php
require_once "../config/conexion.php";
require_once "../models/AlumnosModel.php";

$alumnoModel = new AlumnosModel($conn);

// Agregar
if(isset($_POST['accion']) && $_POST['accion'] == 'agregar'){
    $alumnoModel->agregarAlumno($_POST['nombre'], $_POST['id_grupo']);
    header("Location: ../views/alumnos.php?msg=success");
    exit();
}

// Editar
if(isset($_POST['accion']) && $_POST['accion'] == 'editar'){
    $alumnoModel->editarAlumno($_POST['id_alumno'], $_POST['nombre'], $_POST['id_grupo']);
    header("Location: ../views/alumnos.php?msg=edited");
    exit();
}

// Eliminar
if(isset($_GET['eliminar'])){
    $alumnoModel->eliminarAlumno($_GET['eliminar']);
    header("Location: ../views/alumnos.php?msg=deleted");
    exit();
}
