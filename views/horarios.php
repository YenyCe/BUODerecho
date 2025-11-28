<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";

// Obtener datos para selects
$docentes = $conn->query("SELECT id_docente, nombre FROM docentes ORDER BY nombre");
$materias = $conn->query("SELECT id_materia, nombre FROM materias ORDER BY nombre");
$grupos   = $conn->query("SELECT id_grupo, nombre FROM grupos ORDER BY nombre");

// Obtener horarios con días desde la tabla nueva
$horarios = $conn->query("
SELECT 
    h.id_horario,
    h.id_docente,
    h.id_materia,
    h.id_grupo,
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

// INICIAR CAPTURA  
ob_start();
?>

<div class="container-form">

    <h2>Horarios</h2>
    <button class="btn-agregar" onclick="abrirModalHorario()">Agregar Horario</button>

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
                    <button class="btn-editar" onclick='abrirModalHorario(<?= $h["id_horario"] ?>, <?= json_encode($h) ?>)'>
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
    function abrirModalHorario(id = null, data = null) {
        const modal = document.getElementById("modalHorario");
        modal.style.display = "block";
        const titulo = document.getElementById("tituloModalHorario");
        const accion = document.getElementById("accionHorario");
        const idHorario = document.getElementById("id_horario");

        const selectDocente = document.getElementById("id_docente");
        const selectMateria = document.getElementById("id_materia");
        const selectGrupo = document.getElementById("id_grupo");

        const horaInicio = document.querySelector("input[name='hora_inicio']");
        const horaFin = document.querySelector("input[name='hora_fin']");

        const checkboxes = document.querySelectorAll("input[name='dia_semana[]']");

        if (id && data) {
            titulo.textContent = "Editar Horario";
            accion.value = "editar";
            idHorario.value = id;

            selectDocente.value = data.id_docente;
            selectMateria.value = data.id_materia;
            selectGrupo.value = data.id_grupo;

            // Limpiar todos antes de marcar
            checkboxes.forEach(cb => cb.checked = false);

            // Marcar días recibidos en array
            if (data.dias) {
                data.dias.split("-").forEach(d => {
                    let cb = document.querySelector(`input[name='dia_semana[]'][value='${d}']`);
                    if (cb) cb.checked = true;
                });
            }

            horaInicio.value = data.hora_inicio;
            horaFin.value = data.hora_fin;

        } else {
            titulo.textContent = "Agregar Horario";
            accion.value = "guardar";
            idHorario.value = "";

            selectDocente.value = "";
            selectMateria.value = "";
            selectGrupo.value = "";

            checkboxes.forEach(cb => cb.checked = false);

            horaInicio.value = "";
            horaFin.value = "";
        }
    }

    function cerrarModalHorario() {
        document.getElementById("modalHorario").style.display = "none";
    }

    window.onclick = function(event) {
        const modal = document.getElementById("modalHorario");
        if (event.target === modal) {
            cerrarModalHorario();
        }
    }
</script>

<?php
// FIN de la captura
$content = ob_get_clean();
$title = "Horario";

// Cargar layout
include "dashboard.php";
?>