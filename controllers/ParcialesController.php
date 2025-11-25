<?php
require_once "../config/conexion.php";
require_once "../models/ParcialesModel.php";

$model = new ParcialesModel($conn);

if(isset($_POST['accion'])){
    $accion = $_POST['accion'];

    if($accion == "agregar"){
        $numero = $_POST['numero_parcial'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $model->agregarParcial($numero, $fecha_inicio, $fecha_fin);
    }

    if($accion == "editar"){
        $id = $_POST['id_parcial'];
        $numero = $_POST['numero_parcial'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $model->editarParcial($id, $numero, $fecha_inicio, $fecha_fin);
    }

    header("Location: ../views/parciales.php?msg=ok");
    exit;
}

if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $model->eliminarParcial($id);
    header("Location: ../views/parciales.php?msg=ok");
    exit;
}
?>
