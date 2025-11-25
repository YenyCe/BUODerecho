<?php
require_once "../config/conexion.php";
require_once "../models/MateriasModel.php";

$materiaModel = new MateriasModel($conn);

// Agregar
if(isset($_POST['accion']) && $_POST['accion'] == 'agregar'){
    $materiaModel->agregarMateria($_POST['nombre'], $_POST['clave'], $_POST['horas_semana'], $_POST['horas_semestre']);
    header("Location: ../views/materias.php?msg=success");
    exit();
}

// Editar
if(isset($_POST['accion']) && $_POST['accion'] == 'editar'){
    $materiaModel->editarMateria($_POST['id_materia'], $_POST['nombre'], $_POST['clave'], $_POST['horas_semana'], $_POST['horas_semestre']);
    header("Location: ../views/materias.php?msg=edited");
    exit();
}

// Eliminar
if(isset($_GET['eliminar'])){
    $materiaModel->eliminarMateria($_GET['eliminar']);
    header("Location: ../views/materias.php?msg=deleted");
    exit();
}
