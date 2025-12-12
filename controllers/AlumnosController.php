<?php
require_once "../config/conexion.php";
require_once "../models/AlumnosModel.php";

session_start();

$alumnosModel = new AlumnosModel($conn);

// Datos de sesión
$rol = $_SESSION['rol'] ?? null;
$id_carrera_user = $_SESSION['id_carrera'] ?? null;

// Verificación básica
if (!$rol) {
    header("Location: ../views/login.php");
    exit();
}

/* ============================================================
   MÉTODO: AGREGAR ALUMNO
   ============================================================ */
if (isset($_POST['accion']) && $_POST['accion'] === 'agregar') {

    $nombre = $_POST['nombre'] ?? '';
    $id_grupo = $_POST['id_grupo'] ?? '';
    $from_grupo = $_POST['id_grupo_origen'] ?? null; // NUEVO: desde qué grupo se abrió el modal

    $result = $alumnosModel->agregarAlumno($nombre, $id_grupo, $rol, $id_carrera_user);

    $redirect = $from_grupo ? "../views/alumnos_por_grupo.php?id_grupo=$from_grupo" : "../views/alumnos.php";

    if ($result['success']) {
        header("Location: $redirect?msg=success");
    } else {
        $reason = urlencode($result['message']);
        header("Location: $redirect?msg=error&reason=$reason");
    }
    exit();
}


/* ============================================================
   MÉTODO: EDITAR ALUMNO
   ============================================================ */
if (isset($_POST['accion']) && $_POST['accion'] === 'editar') {

    $id = $_POST['id_alumno'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $id_grupo = $_POST['id_grupo'] ?? '';
    $from_grupo = $_POST['id_grupo_origen'] ?? null;

    $result = $alumnosModel->editarAlumno($id, $nombre, $id_grupo, $rol, $id_carrera_user);

    $redirect = $from_grupo ? "../views/alumnos_por_grupo.php?id_grupo=$from_grupo" : "../views/alumnos.php";

    if ($result['success']) {
        header("Location: $redirect?msg=updated");
    } else {
        $reason = urlencode($result['message']);
        header("Location: $redirect?msg=error&reason=$reason");
    }
    exit();
}


/* ============================================================
   MÉTODO: ELIMINAR ALUMNO
   ============================================================ */
if (isset($_GET['eliminar'])) {

    $id = $_GET['eliminar'];
    $from_grupo = $_GET['from_grupo'] ?? null;

    $result = $alumnosModel->eliminarAlumno($id, $rol, $id_carrera_user);

    $redirect = $from_grupo ? "../views/alumnos_por_grupo.php?id_grupo=$from_grupo" : "../views/alumnos.php";

    if ($result['success']) {
        header("Location: $redirect?msg=deleted");
    } else {
        $reason = urlencode($result['message']);
        header("Location: $redirect?msg=error&reason=$reason");
    }
    exit();
}


if (isset($_POST['accion']) && $_POST['accion'] === 'baja') {
    $id = $_POST['id_alumno'];
    $motivo = $_POST['motivo'] ?? null;
    $from_grupo = $_POST['from_grupo'] ?? null;

    $result = $alumnosModel->darBajaAlumno($id, $motivo, $rol, $id_carrera_user);
    $redirect = $from_grupo ? "../views/alumnos_por_grupo.php?id_grupo=$from_grupo" : "../views/alumnos.php";

    $msg = $result['success'] ? 'success' : 'error';
    $reason = $result['success'] ? '' : '&reason=' . urlencode($result['message']);
    header("Location: $redirect?msg=$msg$reason");
    exit();
}


/* ============================================================
   SI LLEGAN AQUÍ SIN ACCIÓN → REDIRECCIÓN SEGURA
   ============================================================ */
header("Location: ../views/alumnos.php");
exit();

?>
