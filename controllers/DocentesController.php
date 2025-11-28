<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/DocentesModel.php";

$docenteModel = new DocentesModel($conn);
$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

// AGREGAR O EDITAR DOCENTE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    
    // Para coordinadores, siempre asignar su carrera
    if($rol === 'coordinador') {
        $id_carrera_docente = $id_carrera;
    } else {
        $id_carrera_docente = $_POST['id_carrera'] ?? null;
    }

    if($accion === 'agregar'){
        $docenteModel->agregarDocente($nombre, $apellidos, $correo, $telefono, $id_carrera_docente);
        header("Location: ../views/docentes.php?msg=success");
        exit();
    }

    if($accion === 'editar'){
        $id_docente = $_POST['id_docente'];
        $docenteModel->editarDocente($id_docente, $nombre, $apellidos, $correo, $telefono, $id_carrera_docente);
        header("Location: ../views/docentes.php?msg=edited");
        exit();
    }
}

// ELIMINAR DOCENTE
if(isset($_GET['eliminar'])){
    $id_docente = $_GET['eliminar'];
    $docenteModel->eliminarDocente($id_docente);
    header("Location: ../views/docentes.php?msg=deleted");
    exit();
}
?>
