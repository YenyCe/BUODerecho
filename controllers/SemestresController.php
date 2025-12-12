<?php
require_once "../config/conexion.php";
require_once "../models/SemestresModel.php";

$model = new SemestresModel($conn);

/* --- SEMESTRES --- */
if(isset($_POST['accion_semestre'])){
    $numero = intval($_POST['numero']);
    $id_semestre = isset($_POST['id_semestre']) ? intval($_POST['id_semestre']) : null;

    if($numero < 1){
        header("Location: ../views/semestres_grupos.php?msg=error_numero");
        exit();
    }

    if($_POST['accion_semestre'] == 'agregar'){
        if($model->agregarSemestre($numero)){
            header("Location: ../views/semestres_grupos.php?msg=success");
        } else {
            header("Location: ../views/semestres_grupos.php?msg=error_duplicate");
        }
    }
    if($_POST['accion_semestre'] == 'editar'){
        if($model->editarSemestre($id_semestre, $numero)){
            header("Location: ../views/semestres_grupos.php?msg=success");
        } else {
            header("Location: ../views/semestres_grupos.php?msg=error_duplicate");
        }
    }
    exit();
}

if(isset($_GET['eliminar_semestre'])){
    if($model->eliminarSemestre($_GET['eliminar_semestre'])){
        header("Location: ../views/semestres_grupos.php?msg=deleted");
    } else {
        header("Location: ../views/semestres_grupos.php?msg=error_dependencia");
    }
    exit();
}

/* --- GRUPOS --- */
if(isset($_POST['accion_grupo'])){
    $nombre = trim($_POST['nombre']);
    $id_semestre = intval($_POST['id_semestre']);
    $id_carrera = !empty($_POST['id_carrera']) ? intval($_POST['id_carrera']) : $_SESSION['id_carrera'];
    $id_grupo = isset($_POST['id_grupo']) ? intval($_POST['id_grupo']) : null;

    if(empty($nombre)){
        header("Location: ../views/semestres_grupos.php?msg=error_nombre");
        exit();
    }

    if($_POST['accion_grupo'] == 'agregar'){
        if($model->agregarGrupo($nombre, $id_semestre, $id_carrera)){
            header("Location: ../views/semestres_grupos.php?msg=success");
        } else {
            header("Location: ../views/semestres_grupos.php?msg=error_duplicate");
        }
    }

    if($_POST['accion_grupo'] == 'editar'){
        if($model->editarGrupo($id_grupo, $nombre, $id_semestre, $id_carrera)){
            header("Location: ../views/semestres_grupos.php?msg=success");
        } else {
            header("Location: ../views/semestres_grupos.php?msg=error_duplicate");
        }
    }
    exit();
}

if(isset($_GET['eliminar_grupo'])){
    if($model->eliminarGrupo($_GET['eliminar_grupo'])){
        header("Location: ../views/semestres_grupos.php?msg=deleted");
    } else {
        header("Location: ../views/semestres_grupos.php?msg=error_dependencia");
    }
    exit();
}
?>
