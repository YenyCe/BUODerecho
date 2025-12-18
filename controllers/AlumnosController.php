<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/AlumnosModel.php";

$alumnoModel = new AlumnosModel($conn);

/* =====================
   AGREGAR
===================== */
if (isset($_POST['accion']) && $_POST['accion'] === 'agregar') {

    $alumnoModel->agregarAlumno(
        $_POST['nombre'],
        $_POST['id_grupo']
    );

    $_SESSION['alerta'] = [
        'tipo' => 'success',
        'mensaje' => 'Alumno agregado correctamente'
    ];

    if (!empty($_POST['id_grupo_origen'])) {
        header("Location: ../views/alumnos_por_grupo.php?id_grupo=".$_POST['id_grupo_origen']);
    } else {
        header("Location: ../views/alumnos.php");
    }
    exit();
}

/* =====================
   EDITAR
===================== */
if (isset($_POST['accion']) && $_POST['accion'] === 'editar') {

    $alumnoModel->editarAlumno(
        $_POST['id_alumno'],
        $_POST['nombre'],
        $_POST['id_grupo']
    );

    $_SESSION['alerta'] = [
        'tipo' => 'success',
        'mensaje' => 'Alumno editado correctamente'
    ];

    if (!empty($_POST['id_grupo_origen'])) {
        header("Location: ../views/alumnos_por_grupo.php?id_grupo=".$_POST['id_grupo_origen']);
    } else {
        header("Location: ../views/alumnos.php");
    }
    exit();
}

/* =====================
   ELIMINAR / BAJA
===================== */
if (isset($_POST['accion']) && $_POST['accion'] === 'baja') {

    $alumnoModel->darBajaAlumno(
        $_POST['id_alumno'],
        $_POST['motivo'] ?? null
    );

    $_SESSION['alerta'] = [
        'tipo' => 'success',
        'mensaje' => 'Alumno dado de baja correctamente'
    ];

    header("Location: ../views/alumnos_por_grupo.php?id_grupo=".$_POST['id_grupo_origen']);
    exit();
}


if (isset($_GET['eliminar'])) {

    $alumnoModel->eliminarAlumno($_GET['eliminar']);

    $_SESSION['alerta'] = [
        'tipo' => 'error',
        'mensaje' => 'Alumno eliminado'
    ];

    header("Location: ../views/alumnos.php");
    exit();
}

header("Location: ../views/alumnos.php");
exit();
