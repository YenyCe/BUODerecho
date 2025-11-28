<?php
require_once "../config/conexion.php";
require_once "../models/SemestresModel.php";

$model = new SemestresModel($conn);

/* --- SEMESTRES --- */
if(isset($_POST['accion_semestre'])){
    if($_POST['accion_semestre'] == 'agregar'){
        $model->agregarSemestre($_POST['numero']);
    }
    if($_POST['accion_semestre'] == 'editar'){
        $model->editarSemestre($_POST['id_semestre'], $_POST['numero']);
    }
    header("Location: ../views/semestres_grupos.php?msg=success");
    exit();
}

if(isset($_GET['eliminar_semestre'])){
    $model->eliminarSemestre($_GET['eliminar_semestre']);
    header("Location: ../views/semestres_grupos.php?msg=deleted");
    exit();
}

/* --- GRUPOS --- */
if(isset($_POST['accion_grupo'])){
    $nombre = $_POST['nombre'];
    $id_semestre = $_POST['id_semestre'];
    // Para administradores viene por el select, para coordinadores usamos su propia carrera
    $id_carrera = $_POST['id_carrera'] ?? $_SESSION['id_carrera'];

    if($_POST['accion_grupo'] == 'agregar'){
        $model->agregarGrupo($nombre, $id_semestre, $id_carrera);
    }
    if($_POST['accion_grupo'] == 'editar'){
        $id_grupo = $_POST['id_grupo'];
        $model->editarGrupo($id_grupo, $nombre, $id_semestre, $id_carrera);
    }
    header("Location: ../views/semestres_grupos.php?msg=success");
    exit();
}

if(isset($_GET['eliminar_grupo'])){
    $model->eliminarGrupo($_GET['eliminar_grupo']);
    header("Location: ../views/semestres_grupos.php?msg=deleted");
    exit();
}
