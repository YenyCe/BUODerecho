<?php
include("conexion.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generar sesiones</title>
</head>
<body>

<h2>Generar sesiones del parcial</h2>

<form action="procesar_sesiones.php" method="POST">

    <label>Docente:</label>
    <select name="docente" required>
        <?php
        $sql = $conexion->query("SELECT * FROM docentes");
        while ($d = $sql->fetch_assoc()) {
            echo "<option value='".$d['id_docente']."'>".$d['nombre']."</option>";
        }
        ?>
    </select>
    <br><br>

    <label>Grupo:</label>
    <select name="grupo" required>
        <?php
        $sql = $conexion->query("SELECT * FROM grupos");
        while ($g = $sql->fetch_assoc()) {
            echo "<option value='".$g['id_grupo']."'>".$g['nombre']."</option>";
        }
        ?>
    </select>
    <br><br>

    <label>Materia:</label>
    <select name="materia" required>
        <?php
        $sql = $conexion->query("SELECT * FROM materias");
        while ($m = $sql->fetch_assoc()) {
            echo "<option value='".$m['id_materia']."'>".$m['nombre']."</option>";
        }
        ?>
    </select>

    <br><br>

    <label>Fecha inicio parcial:</label>
    <input type="date" name="inicio" required>

    <label>Fecha fin parcial:</label>
    <input type="date" name="fin" required>

    <br><br>

    <button type="submit">Generar Sesiones</button>
</form>

</body>
</html>
