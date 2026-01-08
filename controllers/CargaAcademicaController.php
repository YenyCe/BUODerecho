<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/CargaAcademicaModel.php";

$model = new CargaAcademicaModel($conn);

$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador')
    ? $_SESSION['id_carrera']
    : ($_POST['id_carrera'] ?? null);

/* =====================
   DATOS
===================== */
$data = [
    'id_carrera'        => $id_carrera,
    'clave_oficio'      => $_POST['clave_oficio'] ?? '',
    'texto_presentacion'=> $_POST['texto_presentacion'] ?? '',
    'ciclo_escolar'     => $_POST['ciclo_escolar'] ?? '',
    'claustro_texto'    => $_POST['claustro_texto'] ?? '',
    'texto_pie'         => $_POST['texto_pie'] ?? '',
    'nombre_director'   => $_POST['nombre_director'] ?? '',
    'cargo_director'    => $_POST['cargo_director'] ?? '',
    'archivo_firma'     => $_POST['archivo_firma'] ?? ''
];

/* =====================
   GUARDAR
===================== */
if (isset($_POST['accion']) && $_POST['accion'] === 'guardar') {

    $model->crearConfig($data);

    $_SESSION['alerta'] = [
        'tipo' => 'success',
        'mensaje' => 'Configuración de carga académica guardada correctamente'
    ];

    header("Location: ../views/config_carga_academica.php");
    exit();
}

/* =====================
   EDITAR
===================== */
if (isset($_POST['accion']) && $_POST['accion'] === 'editar') {

    $model->editarConfig($_POST['id_carga'], $data);

    $_SESSION['alerta'] = [
        'tipo' => 'success',
        'mensaje' => 'Configuración de carga académica actualizada correctamente'
    ];

    header("Location: ../views/config_carga_academica.php");
    exit();
}

/* =====================
   FALLBACK
===================== */
header("Location: ../views/config_carga_academica.php");
exit();
