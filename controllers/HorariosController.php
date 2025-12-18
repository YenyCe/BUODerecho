<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/HorariosModel.php";

$model = new HorariosModel($conn);

// =======================
// LISTAR HORARIOS
// =======================
if (!isset($_GET['accion']) && !isset($_POST['accion'])) {
    // Para incluir la vista
    if ($_SESSION['rol'] === 'admin') {
        $horarios = $model->getHorarios();
        $carreras = $model->getCarreras();
    } else {
        $id_carrera = $_SESSION['id_carrera'];
        $horarios = $model->getHorariosByCarrera($id_carrera);
        $carreras = [$model->getCarrera($id_carrera)];
    }

    $docentes = $model->getDocentes();
    include "../views/horarios.php";
    exit();
}

// =======================
// AJAX: Traer Materias por carrera
// =======================
// GET AJAX: Materias
if (isset($_GET['accion']) && $_GET['accion'] === 'getMaterias') {
    $id_carrera = intval($_GET['id_carrera']);
    $materias = $model->getMateriasByCarrera($id_carrera);
    echo json_encode($materias); // Array de {id_materia, nombre}
    exit();
}

// =======================
// AJAX: Traer Grupos por carrera
// =======================
if (isset($_GET['accion']) && $_GET['accion'] === 'getGrupos') {
    $id_carrera = intval($_GET['id_carrera']);
    $grupos = $model->getGruposByCarrera($id_carrera);
    echo json_encode($grupos); // Array de {id_grupo, nombre}
    exit();
}

// =======================
// GUARDAR HORARIO
// =======================
if (isset($_POST['accion']) && $_POST['accion'] === 'guardar') {

    $id_carrera = $_POST['id_carrera'];
    $id_grupo = $_POST['id_grupo'];
    $id_materia = $_POST['id_materia'];
    $id_docente = $_POST['id_docente'];
    $horario_texto = $_POST['horario_texto'];
    $dias = $_POST['dia_semana'] ?? [];

    if ($model->guardar($id_carrera, $id_grupo, $id_materia, $id_docente, $horario_texto, $dias)) {
        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => 'Horario agregado correctamente'
        ];
    } else {
        $_SESSION['alerta'] = [
            'tipo' => 'error',
            'mensaje' => 'No se pudo agregar el horario'
        ];
    }

    header("Location: ../views/horarios.php");
    exit();
}


// =======================
// EDITAR HORARIO
// =======================
if (isset($_POST['accion']) && $_POST['accion'] === 'editar') {

    $id_horario = $_POST['id_horario'];
    $id_carrera = $_POST['id_carrera'];
    $id_grupo = $_POST['id_grupo'];
    $id_materia = $_POST['id_materia'];
    $id_docente = $_POST['id_docente'];
    $horario_texto = $_POST['horario_texto'];
    $dias = $_POST['dia_semana'] ?? [];

    if ($model->editarHorario($id_horario, $id_docente, $id_materia, $id_grupo, $id_carrera, $horario_texto, $dias)) {
        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => 'Horario actualizado correctamente'
        ];
    } else {
        $_SESSION['alerta'] = [
            'tipo' => 'error',
            'mensaje' => 'No se pudo actualizar el horario'
        ];
    }

    header("Location: ../views/horarios.php");
    exit();
}

// =======================
// ELIMINAR HORARIO
// =======================
if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar') {

    if ($model->eliminar(intval($_GET['id']))) {
        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => 'Horario eliminado correctamente'
        ];
    } else {
        $_SESSION['alerta'] = [
            'tipo' => 'error',
            'mensaje' => 'No se pudo eliminar el horario'
        ];
    }

    header("Location: ../views/horarios.php");
    exit();
}
