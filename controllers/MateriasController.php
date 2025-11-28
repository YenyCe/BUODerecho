<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/MateriasModel.php";

$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

$materiaModel = new MateriasModel($conn);

// Agregar
if(isset($_POST['accion']) && $_POST['accion'] == 'agregar'){
    $materiaModel->agregarMateria(
        $_POST['nombre'],
        $_POST['clave'],
        $_POST['horas_semana'],
        $_POST['horas_semestre'],
        $_POST['id_carrera'] // ahora admin puede elegir carrera
    );
    header("Location: ../views/materias.php?msg=success");
    exit();
}

if(isset($_POST['accion']) && $_POST['accion'] == 'editar'){
    $materiaModel->editarMateria(
        $_POST['id_materia'],
        $_POST['nombre'],
        $_POST['clave'],
        $_POST['horas_semana'],
        $_POST['horas_semestre'],
        $_POST['id_carrera']
    );
    header("Location: ../views/materias.php?msg=edited");
    exit();
}


// Eliminar
if(isset($_GET['eliminar'])){
    $materiaModel->eliminarMateria($_GET['eliminar']);
    header("Location: ../views/materias.php?msg=deleted");
    exit();
}
?>
