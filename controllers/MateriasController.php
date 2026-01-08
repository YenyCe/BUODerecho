<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/MateriasModel.php";

$rol = $_SESSION['rol'];
$id_carrera_sesion = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;
$materiaModel = new MateriasModel($conn);

// ================= AGREGAR =================
if (isset($_POST['accion']) && $_POST['accion'] == 'agregar') {

    $id_carrera_final = ($rol === 'coordinador')
        ? $id_carrera_sesion
        : $_POST['id_carrera'];

    $materiaModel->agregarMateria(
        $_POST['nombre'],
        $_POST['clave'],
        $_POST['id_semestre'],     // <-- el semestre
        $_POST['horas_semana'],
        $_POST['horas_semestre'],
        $id_carrera_final
    );

    $_SESSION['alerta'] = [
        'tipo' => 'success',
        'mensaje' => 'Materia agregada correctamente'
    ];

    header("Location: ../views/materias.php");
    exit();
}

// ================= EDITAR =================
if (isset($_POST['accion']) && $_POST['accion'] == 'editar') {

    $id_carrera_final = ($rol === 'coordinador')
        ? $id_carrera_sesion
        : $_POST['id_carrera'];

    $materiaModel->editarMateria(
        $_POST['id_materia'],
        $_POST['nombre'],
        $_POST['clave'],
        $_POST['id_semestre'],    // <-- el semestre
        $_POST['horas_semana'],
        $_POST['horas_semestre'],
        $id_carrera_final
    );

    $_SESSION['alerta'] = [
        'tipo' => 'success',
        'mensaje' => 'Materia editada correctamente'
    ];

    header("Location: ../views/materias.php");
    exit();
}

// ================= ELIMINAR =================
if (isset($_GET['eliminar'])) {
    $materiaModel->eliminarMateria($_GET['eliminar']);
    $_SESSION['alerta'] = [
        'tipo' => 'error',
        'mensaje' => 'Materia eliminada correctamente'
    ];

    header("Location: ../views/materias.php");
    exit();
}
?>
