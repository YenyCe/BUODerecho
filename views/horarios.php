<?php
require_once "../config/conexion.php";

// Obtener datos para selects
$docentes = $conn->query("SELECT id_docente, nombre FROM docentes ORDER BY nombre");
$materias = $conn->query("SELECT id_materia, nombre FROM materias ORDER BY nombre");
$grupos   = $conn->query("SELECT id_grupo, nombre FROM grupos ORDER BY nombre");

// Obtener horarios con días desde la tabla nueva
$horarios = $conn->query("
SELECT 
    h.id_horario,
    d.nombre AS docente,
    m.nombre AS materia,
    g.nombre AS grupo,
    GROUP_CONCAT(hd.dia ORDER BY FIELD(hd.dia,'L','M','X','J','V') SEPARATOR '-') AS dias,
    h.hora_inicio,
    h.hora_fin
FROM horarios h
INNER JOIN docentes d ON h.id_docente = d.id_docente
INNER JOIN materias m ON h.id_materia = m.id_materia
INNER JOIN grupos g   ON h.id_grupo   = g.id_grupo
LEFT JOIN horario_dias hd ON h.id_horario = hd.id_horario
GROUP BY h.id_horario
ORDER BY d.nombre;
");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Horarios</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="container-form">

        <h2>Horario</h2>
        <button class="btn-agregar" onclick="abrirModalHorario()">Agregar Horario</button>

        <h2>Horarios Registrados</h2>

        <table class="tabla-docentes">
            <tr>
                <th>Docente</th>
                <th>Materia</th>
                <th>Grupo</th>
                <th>Días</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Acciones</th>
            </tr>

            <?php while ($h = $horarios->fetch_assoc()): ?>
                <tr>
                    <td><?= $h['docente']; ?></td>
                    <td><?= $h['materia']; ?></td>
                    <td><?= $h['grupo']; ?></td>
                    <td><?= $h['dias']; ?></td>
                    <td><?= $h['hora_inicio']; ?></td>
                    <td><?= $h['hora_fin']; ?></td>
                    <td>

                        <button class="btn-editar" onclick='editarHorario(<?= json_encode($h); ?>)'>
                            Editar
                        </button>

                        <a href="../controllers/horariosController.php?accion=eliminar&id=<?= $h['id_horario']; ?>"
                            onclick="return confirm('¿Eliminar este horario COMPLETO y sus días?');"
                            class="btn-eliminar">Eliminar</a>

                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

    </div>

    <!-- Modal Horarios -->
    <div id="modalHorario" class="modal">
        <div class="modal-content">
            <span class="cerrar" onclick="cerrarModalHorario()">&times;</span>
            <h2 id="tituloModalHorario">Agregar Horario</h2>
            <form action="../controllers/HorariosController.php" method="POST">
                <input type="hidden" name="accion" id="accionHorario" value="guardar">
                <input type="hidden" name="id_horario" id="id_horario">

                <div class="form-grid">

                    <div class="full-row">
                        <label>Docente:</label>
                        <select name="id_docente" id="id_docente" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($docentes as $d): ?>
                                <option value="<?= $d['id_docente']; ?>"><?= $d['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label>Materia:</label>
                        <select name="id_materia" id="id_materia" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($materias as $m): ?>
                                <option value="<?= $m['id_materia']; ?>"><?= $m['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label>Grupo:</label>
                        <select name="id_grupo" id="id_grupo" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($grupos as $g): ?>
                                <option value="<?= $g['id_grupo']; ?>"><?= $g['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="full-row">
                        <label>Días:</label>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="dia_semana[]" value="L"> L</label>
                            <label><input type="checkbox" name="dia_semana[]" value="M"> M</label>
                            <label><input type="checkbox" name="dia_semana[]" value="X"> X</label>
                            <label><input type="checkbox" name="dia_semana[]" value="J"> J</label>
                            <label><input type="checkbox" name="dia_semana[]" value="V"> V</label>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div>
                            <label>Hora inicio:</label>
                            <input type="time" name="hora_inicio">
                        </div>
                        <div>
                            <label>Hora fin:</label>
                            <input type="time" name="hora_fin">
                        </div>
                    </div>
                </div>

                <button type="submit">Guardar</button>
            </form>

        </div>
    </div>
    <script>
        function abrirModalHorario() {
            document.getElementById("modalHorario").style.display = "block";
        }

        function cerrarModalHorario() {
            document.getElementById("modalHorario").style.display = "none";
        }

        // Cerrar si hace clic afuera
        window.onclick = function(e) {
            let modal = document.getElementById("modalHorario");
            if (e.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>

</html>