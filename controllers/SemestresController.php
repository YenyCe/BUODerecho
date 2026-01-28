<?php
session_start();
require_once "../config/conexion.php";
require_once "../models/SemestresModel.php";

$model = new SemestresModel($conn);

/* --- SEMESTRES --- */
/* --- SEMESTRES --- */
if (isset($_POST['accion_semestre'])) {

    $numero = intval($_POST['numero']);
    $id_semestre = isset($_POST['id_semestre']) ? intval($_POST['id_semestre']) : null;

    if ($numero < 1) {
        $_SESSION['alerta'] = [
            'tipo' => 'error',
            'mensaje' => 'Número de semestre inválido'
        ];
        header("Location: ../views/semestres_grupos.php");
        exit();
    }

    if ($_POST['accion_semestre'] == 'agregar') {
        if ($model->agregarSemestre($numero)) {
            $_SESSION['alerta'] = [
                'tipo' => 'success',
                'mensaje' => 'Semestre agregado correctamente'
            ];
        } else {
            $_SESSION['alerta'] = [
                'tipo' => 'error',
                'mensaje' => 'El semestre ya existe'
            ];
        }
        header("Location: ../views/semestres_grupos.php");
        exit();
    }

    if ($_POST['accion_semestre'] == 'editar') {
        if ($model->editarSemestre($id_semestre, $numero)) {
            $_SESSION['alerta'] = [
                'tipo' => 'success',
                'mensaje' => 'Semestre actualizado correctamente'
            ];
        } else {
            $_SESSION['alerta'] = [
                'tipo' => 'error',
                'mensaje' => 'El semestre ya existe'
            ];
        }
        header("Location: ../views/semestres_grupos.php");
        exit();
    }
}


if (isset($_GET['eliminar_semestre'])) {
    if ($model->eliminarSemestre($_GET['eliminar_semestre'])) {
        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => 'Semestre eliminado correctamente'
        ];
    } else {
        $_SESSION['alerta'] = [
            'tipo' => 'error',
            'mensaje' => 'No se puede eliminar, tiene grupos asociados'
        ];
    }
    header("Location: ../views/semestres_grupos.php");
    exit();
}


/* --- GRUPOS --- */
/* --- GRUPOS --- */
if (isset($_POST['accion_grupo'])) {

    $nombre = trim($_POST['nombre']);
    $id_semestre = intval($_POST['id_semestre']);
    $id_carrera = !empty($_POST['id_carrera']) ? intval($_POST['id_carrera']) : $_SESSION['id_carrera'];
    $id_grupo = isset($_POST['id_grupo']) ? intval($_POST['id_grupo']) : null;

    if (empty($nombre)) {
        $_SESSION['alerta'] = [
            'tipo' => 'error',
            'mensaje' => 'El nombre del grupo es obligatorio'
        ];
        header("Location: ../views/semestres_grupos.php");
        exit();
    }

    if ($_POST['accion_grupo'] == 'agregar') {
        if ($model->agregarGrupo($nombre, $id_semestre, $id_carrera)) {
            $_SESSION['alerta'] = [
                'tipo' => 'success',
                'mensaje' => 'Grupo agregado correctamente'
            ];
        } else {
            $_SESSION['alerta'] = [
                'tipo' => 'error',
                'mensaje' => 'El grupo ya existe'
            ];
        }
        header("Location: ../views/semestres_grupos.php");
        exit();
    }

    if ($_POST['accion_grupo'] == 'editar') {
        if ($model->editarGrupo($id_grupo, $nombre, $id_semestre, $id_carrera)) {
            $_SESSION['alerta'] = [
                'tipo' => 'success',
                'mensaje' => 'Grupo actualizado correctamente'
            ];
        } else {
            $_SESSION['alerta'] = [
                'tipo' => 'error',
                'mensaje' => 'El grupo ya existe'
            ];
        }
        header("Location: ../views/semestres_grupos.php");
        exit();
    }
}


if (isset($_GET['eliminar_grupo'])) {
    if ($model->eliminarGrupo($_GET['eliminar_grupo'])) {
        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => 'Grupo eliminado correctamente'
        ];
    } else {
        $_SESSION['alerta'] = [
            'tipo' => 'error',
            'mensaje' => 'No se puede eliminar, tiene alumnos asociados'
        ];
    }
    header("Location: ../views/semestres_grupos.php");
    exit();
}

?>
