<?php
require_once "../config/conexion.php";

$accion = $_GET['accion'] ?? $_POST['accion'] ?? null;

switch ($accion) {

    /* ======================================
       GUARDAR HORARIO (y sus días)
    ======================================= */
    case "guardar":

        $id_docente = $_POST['id_docente'];
        $id_materia = $_POST['id_materia'];
        $id_grupo   = $_POST['id_grupo'];
        $dias       = $_POST['dia_semana']; // array
        $inicio     = $_POST['hora_inicio'];
        $fin        = $_POST['hora_fin'];

        // 1. Insertar horario SIN días
        $sql = "INSERT INTO horarios (id_docente, id_materia, id_grupo, hora_inicio, hora_fin)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiss", $id_docente, $id_materia, $id_grupo, $inicio, $fin);
        $stmt->execute();

        // obtener id del horario recién insertado
        $id_horario = $stmt->insert_id;

        // 2. Insertar los días en horario_dias
        $sqlDias = $conn->prepare("INSERT INTO horario_dias (id_horario, dia) VALUES (?, ?)");

        foreach ($dias as $dia) {
            $sqlDias->bind_param("is", $id_horario, $dia);
            $sqlDias->execute();
        }

        header("Location: ../views/horarios.php?msg=guardado");
        exit;
        break;



    /* ======================================
       ELIMINAR HORARIO COMPLETO
    ======================================= */
    case "eliminar":

        $id_horario = $_GET['id'];

        // Gracias al ON DELETE CASCADE, se eliminan los días automáticamente
        $stmt = $conn->prepare("DELETE FROM horarios WHERE id_horario = ?");
        $stmt->bind_param("i", $id_horario);
        $stmt->execute();

        header("Location: ../views/horarios.php?msg=eliminado");
        exit;
        break;



    /* ======================================
       ACTUALIZAR HORARIO + DÍAS
    ======================================= */
    case "actualizar":

        $id_horario = $_POST['id_horario'];
        $id_docente = $_POST['id_docente'];
        $id_materia = $_POST['id_materia'];
        $id_grupo   = $_POST['id_grupo'];
        $dias       = $_POST['dia_semana'];
        $inicio     = $_POST['hora_inicio'];
        $fin        = $_POST['hora_fin'];

        // 1. actualizar datos principales
        $sql = "UPDATE horarios
                SET id_docente=?, id_materia=?, id_grupo=?, hora_inicio=?, hora_fin=?
                WHERE id_horario=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiissi", $id_docente, $id_materia, $id_grupo, $inicio, $fin, $id_horario);
        $stmt->execute();

        // 2. borrar días anteriores
        $conn->query("DELETE FROM horario_dias WHERE id_horario = $id_horario");

        // 3. insertar nuevos días
        $sqlDias = $conn->prepare("INSERT INTO horario_dias (id_horario, dia) VALUES (?, ?)");

        foreach ($dias as $dia) {
            $sqlDias->bind_param("is", $id_horario, $dia);
            $sqlDias->execute();
        }

        header("Location: ../views/horarios.php?msg=actualizado");
        exit;
        break;


    default:
        echo "Acción no válida.";
        break;
}
