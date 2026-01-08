<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/DocentesModel.php";

$docenteModel = new DocentesModel($conn);
$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

// =====================
// AGREGAR / EDITAR
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $accion = $_POST['accion'] ?? '';
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $correo = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $genero = $_POST['genero'] ?? '';

    $id_carrera_docente = ($rol === 'coordinador')
        ? $id_carrera
        : ($_POST['id_carrera'] ?? null);

    /* =====================
       AGREGAR
    ===================== */
    if ($accion === 'agregar') {

        if ($docenteModel->existeDocentePorNombre($nombre, $apellidos)) {
            $_SESSION['alerta'] = [
                'tipo' => 'error',
                'mensaje' => 'Ya existe un docente con ese nombre y apellidos'
            ];

            header("Location: ../views/docentes.php");
            exit();
        }

        $docenteModel->agregarDocente(
            $nombre,
            $apellidos,
            $correo,
            $telefono,
            $genero,
            $id_carrera_docente
        );

        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => 'Docente agregado correctamente'
        ];

        header("Location: ../views/docentes.php");
        exit();
    }

    /* =====================
       EDITAR
    ===================== */
    if ($accion === 'editar') {

        $id_docente = $_POST['id_docente'];
        if ($docenteModel->existeDocentePorNombre($nombre, $apellidos, $id_docente)) {
            $_SESSION['alerta'] = [
                'tipo' => 'error',
                'mensaje' => 'Ya existe otro docente con ese nombre y apellidos'
            ];

            header("Location: ../views/docentes.php");
            exit();
        }

        $docenteModel->editarDocente(
            $id_docente,
            $nombre,
            $apellidos,
            $correo,
            $telefono,
            $genero,
            $id_carrera_docente
        );

        $_SESSION['alerta'] = [
            'tipo' => 'success',
            'mensaje' => 'Docente editado correctamente'
        ];

        header("Location: ../views/docentes.php");
        exit();
    }
}

// =====================
// ELIMINAR
// =====================
if (isset($_GET['eliminar'])) {

    $docenteModel->eliminarDocente($_GET['eliminar']);

    $_SESSION['alerta'] = [
        'tipo' => 'error',
        'mensaje' => 'Docente eliminado correctamente'
    ];

    header("Location: ../views/docentes.php");
    exit();
}

