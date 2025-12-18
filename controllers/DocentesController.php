<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/DocentesModel.php";

$docenteModel = new DocentesModel($conn);
$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

// AGREGAR / EDITAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';

    $id_carrera_docente = ($rol === 'coordinador')
        ? $id_carrera
        : ($_POST['id_carrera'] ?? null);

    if ($accion === 'agregar') {
        $docenteModel->agregarDocente($nombre, $apellidos, $correo, $telefono, $id_carrera_docente);

        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => 'Docente agregado correctamente'
        ];

        header("Location: ../views/docentes.php");
        exit();
    }

    if ($accion === 'editar') {
        $id_docente = $_POST['id_docente'];

        $docenteModel->editarDocente($id_docente, $nombre, $apellidos, $correo, $telefono, $id_carrera_docente);

        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => 'Docente editado correctamente'
        ];

        header("Location: ../views/docentes.php");
        exit();
    }
}

// ELIMINAR
if (isset($_GET['eliminar'])) {
    $docenteModel->eliminarDocente($_GET['eliminar']);

    $_SESSION['alerta'] = [
        'tipo' => 'error',
        'mensaje' => 'Docente eliminado correctamente'
    ];

    header("Location: ../views/docentes.php");
    exit();
}
