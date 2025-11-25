<?php
// Conexión a la BD
require_once "../config/conexion.php";

// Obtener docentes, materias y grupos para desplegar en selects
$docentes = $conn->query("SELECT id_docente, nombre FROM docentes");
$materias = $conn->query("SELECT id_materia, nombre FROM materias");
$grupos   = $conn->query("SELECT id_grupo, nombre FROM grupos");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Capturar Horario</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

    <h2>Registrar Horario</h2>

    <form action="../controllers/horario.php" method="POST">

        <!-- DOCENTE -->
        <label>Docente:</label><br>
        <select name="id_docente" required>
            <option value="">Seleccione...</option>
            <?php while($d = $docentes->fetch_assoc()): ?>
                <option value="<?php echo $d['id_docente']; ?>">
                    <?php echo $d['nombre']; ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <!-- MATERIA -->
        <label>Materia:</label><br>
        <select name="id_materia" required>
            <option value="">Seleccione...</option>
            <?php while($m = $materias->fetch_assoc()): ?>
                <option value="<?php echo $m['id_materia']; ?>">
                    <?php echo $m['nombre']; ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <!-- GRUPO -->
        <label>Grupo:</label><br>
        <select name="id_grupo" required>
            <option value="">Seleccione...</option>
            <?php while($g = $grupos->fetch_assoc()): ?>
                <option value="<?php echo $g['id_grupo']; ?>">
                    <?php echo $g['nombre']; ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <!-- DIA -->
        <label>Día de la semana:</label><br>
        <select name="dia_semana" required>
            <option value="L">Lunes</option>
            <option value="M">Martes</option>
            <option value="X">Miércoles</option>
            <option value="J">Jueves</option>
            <option value="V">Viernes</option>
        </select><br><br>

        <!-- HORAS -->
        <label>Hora inicio:</label><br>
        <input type="time" name="hora_inicio" required><br><br>

        <label>Hora fin:</label><br>
        <input type="time" name="hora_fin" required><br><br>

        <button type="submit">Guardar</button>

    </form>

</body>
</html>
