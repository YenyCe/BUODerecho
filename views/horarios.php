<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/HorariosModel.php";

$model = new HorariosModel($conn);

// =======================
// Traer horarios y datos necesarios
// =======================
if ($_SESSION['rol'] === 'admin') {
    $horarios = $model->getHorarios();
    $carreras = $model->getCarreras();
} else {
    $id_carrera = $_SESSION['id_carrera'];
    $horarios = $model->getHorariosByCarrera($id_carrera);
    $carreras = [$model->getCarrera($id_carrera)];
}

$docentes = $model->getDocentes();

// Para cargar grupos y materias directamente desde PHP
$gruposPorCarrera = [];
$materiasPorCarrera = [];

$carrerasTodos = $model->getCarreras();
foreach ($carrerasTodos as $c) {
    $gruposPorCarrera[$c['id_carrera']] = $model->getGruposByCarrera($c['id_carrera']);
    $materiasPorCarrera[$c['id_carrera']] = $model->getMateriasByCarrera($c['id_carrera']);
}

ob_start();
?>

<div class="container-form">
    <h2>Horarios</h2>
    <?php if ($_SESSION['rol'] === 'admin'): ?>
        <div class="filtros-container" style="margin-bottom:15px;">
            <div>
                <label>Carrera:</label>
                <select id="filtroCarrera" class="form-control">
                    <option value="">Todas</option>
                    <?php foreach ($carreras as $c): ?>
                        <option value="<?= $c['id_carrera'] ?>"><?= $c['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>
    <button class="btn-agregar" onclick="abrirModalHorario()">Agregar Horario</button>

    <table class="tabla-docentes">
        <thead>
            <tr>
                <th>Carrera</th>
                <th>Grupo</th>
                <th>Materia</th>
                <th>Docente</th>
                <th>Días</th>
                <th>Horario</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($horarios as $h): ?>
                <tr>
                    <td><?= htmlspecialchars($h['carrera'] ?? '') ?></td>
                    <td><?= htmlspecialchars($h['grupo'] ?? '') ?></td>
                    <td><?= htmlspecialchars($h['materia'] ?? '') ?></td>
                    <td><?= htmlspecialchars($h['docente'] ?? '') ?></td>
                    <td><?= htmlspecialchars($h['dias'] ?? '') ?></td>
                    <td><?= htmlspecialchars($h['horario_texto'] ?? '') ?></td>
                    <td>
                        <button class="btn-editar"
                            onclick='abrirModalHorario(<?= $h["id_horario"] ?>, <?= json_encode($h, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                            Editar
                        </button>
                        <a class="btn-eliminar" href="../controllers/horariosController.php?accion=eliminar&id=<?= $h['id_horario'] ?>" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Horario -->
<div id="modalHorario" class="modal">
    <div class="modal-content">
        <span class="cerrar" onclick="cerrarModal('modalHorario')">&times;</span>
        <h2 id="tituloModalHorario">Agregar Horario</h2>
        <form action="../controllers/horariosController.php" method="POST" id="formHorario">
            <input type="hidden" name="accion" id="accion" value="guardar">
            <input type="hidden" name="id_horario" id="id_horario">

            <!-- Carrera -->
            <?php if ($_SESSION['rol'] === 'admin'): ?>
                <label>Carrera:</label>
                <select name="id_carrera" id="id_carrera" required onchange="cambiarCarrera(this.value)">
                    <option value="">Seleccione...</option>
                    <?php foreach ($carreras as $c): ?>
                        <option value="<?= $c['id_carrera'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input type="hidden" name="id_carrera" id="id_carrera" value="<?= $_SESSION['id_carrera'] ?>">
            <?php endif; ?>

            <!-- Grupo -->
            <label>Grupo:</label>
            <select name="id_grupo" id="id_grupo" required>
                <option value="">Seleccione grupo...</option>
            </select>

            <!-- Materia -->
            <label>Materia:</label>
            <select name="id_materia" id="id_materia" required>
                <option value="">Seleccione materia...</option>
            </select>

            <!-- Docente -->
            <label>Docente:</label>
            <select name="id_docente" id="id_docente" required>
                <option value="">Seleccione...</option>
                <?php foreach ($docentes as $d): ?>
                    <option value="<?= $d['id_docente'] ?>">
                        <?= htmlspecialchars($d['nombre'] . ' ' . $d['apellidos']) ?>
                    </option>

                <?php endforeach; ?>
            </select>

            <!-- Días -->
            <label>Días:</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="dia_semana[]" value="L"> L</label>
                <label><input type="checkbox" name="dia_semana[]" value="M"> M</label>
                <label><input type="checkbox" name="dia_semana[]" value="X"> X</label>
                <label><input type="checkbox" name="dia_semana[]" value="J"> J</label>
                <label><input type="checkbox" name="dia_semana[]" value="V"> V</label>
            </div>

            <!-- Horario en texto -->
            <label>Horario:</label>
            <input type="text" name="horario_texto" id="horario_texto" placeholder="Ej: 8:00-10:00" required>

            <button type="submit">Guardar</button>
        </form>
    </div>
</div>

<script>
    // Arrays de PHP a JS
    const gruposPorCarrera = <?= json_encode($gruposPorCarrera) ?>;
    const materiasPorCarrera = <?= json_encode($materiasPorCarrera) ?>;

    function abrirModalHorario(id = null, data = null) {
        const modal = document.getElementById('modalHorario');
        modal.style.display = 'block';

        document.getElementById('accion').value = id ? 'editar' : 'guardar';
        document.getElementById('id_horario').value = id || '';

        // Limpiar selects y checkboxes
        document.getElementById('id_grupo').innerHTML = '<option value="">Seleccione grupo...</option>';
        document.getElementById('id_materia').innerHTML = '<option value="">Seleccione materia...</option>';
        document.querySelectorAll("input[name='dia_semana[]']").forEach(cb => cb.checked = false);
        document.getElementById('horario_texto').value = '';

        let carreraId = document.getElementById('id_carrera').value;

        if (data) {
            carreraId = data.id_carrera ?? carreraId;
            document.getElementById('id_carrera').value = carreraId;

            cargarSelects(carreraId, data.id_grupo, data.id_materia);

            if (data.id_docente) document.getElementById('id_docente').value = data.id_docente.toString();

            if (data.dias && typeof data.dias === 'string') {
                data.dias.split('-').forEach(d => {
                    const cb = document.querySelector("input[name='dia_semana[]'][value='" + d + "']");
                    if (cb) cb.checked = true;
                });
            }

            if (data.horario_texto) document.getElementById('horario_texto').value = data.horario_texto;
        } else if (carreraId) {
            cargarSelects(carreraId);
        }
    }

    function cambiarCarrera(id_carrera) {
        cargarSelects(id_carrera);
    }

    function cargarSelects(id_carrera, selectedGrupo = null, selectedMateria = null) {
        const gSelect = document.getElementById('id_grupo');
        const mSelect = document.getElementById('id_materia');

        gSelect.innerHTML = '<option value="">Seleccione grupo...</option>';
        mSelect.innerHTML = '<option value="">Seleccione materia...</option>';

        if (gruposPorCarrera[id_carrera]) {
            gruposPorCarrera[id_carrera].forEach(g => {
                const opt = document.createElement('option');
                opt.value = g.id_grupo;
                opt.textContent = g.nombre;
                if (selectedGrupo && selectedGrupo == g.id_grupo) opt.selected = true;
                gSelect.appendChild(opt);
            });
        }

        if (materiasPorCarrera[id_carrera]) {
            materiasPorCarrera[id_carrera].forEach(m => {
                const opt = document.createElement('option');
                opt.value = m.id_materia;
                opt.textContent = m.nombre;
                if (selectedMateria && selectedMateria == m.id_materia) opt.selected = true;
                mSelect.appendChild(opt);
            });
        }
    }

    // FILTRO POR CARRERA EN TABLA
    document.getElementById("filtroCarrera")?.addEventListener("change", function() {
        const carrera = this.value;
        const filas = document.querySelectorAll(".tabla-docentes tbody tr");

        filas.forEach(fila => {
            const colCarrera = fila.children[0].innerText.trim();
            if (carrera === "" || colCarrera === this.options[this.selectedIndex].text) {
                fila.style.display = "";
            } else {
                fila.style.display = "none";
            }
        });
    });
</script>
<script src="/ASISTENCIAS/js/modales.js"></script>
<?php
$content = ob_get_clean();
$title = "Horarios";
include "dashboard.php";
?>