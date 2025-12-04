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
    $id_carrera_usuario = null;
} else {
    $id_carrera_usuario = $_SESSION['id_carrera'];
    $horarios = $model->getHorariosByCarrera($id_carrera_usuario);
    $carreras = [$model->getCarrera($id_carrera_usuario)];
}

// Docentes: para admin traer todos; para coordinador traer sólo los de su carrera
if ($_SESSION['rol'] === 'admin') {
    $docentes = $model->getDocentes(); // todos
} else {
    $docentes = $model->getDocentesByCarrera($id_carrera_usuario); // sólo de su carrera
}

// Para cargar grupos, materias y docentes por carrera desde PHP (JS)
$gruposPorCarrera = [];
$materiasPorCarrera = [];
$docentesPorCarrera = [];
$todas = $model->getCarreras();
foreach ($todas as $c) {
    $id = $c['id_carrera'];
    $gruposPorCarrera[$id] = $model->getGruposByCarrera($id);
    $materiasPorCarrera[$id] = $model->getMateriasByCarrera($id);
    // Si tu modelo tiene un método para traer docentes por carrera:
    if (method_exists($model, 'getDocentesByCarrera')) {
        $docentesPorCarrera[$id] = $model->getDocentesByCarrera($id);
    } else {
        // fallback: todos (mejor implementar getDocentesByCarrera en el modelo)
        $docentesPorCarrera[$id] = $model->getDocentes();
    }
}

// Mapa id->nombre de carreras (útil para crear option oculto)
$carrerasMap = [];
foreach ($todas as $c) $carrerasMap[$c['id_carrera']] = $c['nombre'];

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
                        <option value="<?= $c['id_carrera'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
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
                <!-- para coordinador: select oculto pero con el valor correcto y texto -->
                <select name="id_carrera" id="id_carrera" required style="display:none;">
                    <option value="<?= $id_carrera_usuario ?>" selected><?= htmlspecialchars($carrerasMap[$id_carrera_usuario] ?? $id_carrera_usuario) ?></option>
                </select>
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
                        <?= htmlspecialchars($d['nombre'] . ' ' . ($d['apellidos'] ?? '')) ?>
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
    // Datos PHP -> JS
    const gruposPorCarrera = <?= json_encode($gruposPorCarrera) ?>;
    const materiasPorCarrera = <?= json_encode($materiasPorCarrera) ?>;
    const docentesPorCarrera = <?= json_encode($docentesPorCarrera) ?>;
    const carrerasMap = <?= json_encode($carrerasMap) ?>;
    const DEFAULT_CARRERA = <?= json_encode($id_carrera_usuario ?? null) ?>;

    // función para abrir modal (crear/editar)
    function abrirModalHorario(id = null, data = null) {
        const modal = document.getElementById('modalHorario');
        modal.style.display = 'block';

        document.getElementById('accion').value = id ? 'editar' : 'guardar';
        document.getElementById('id_horario').value = id || '';

        // limpiar selects y campos
        const gSelect = document.getElementById('id_grupo');
        const mSelect = document.getElementById('id_materia');
        const dSelect = document.getElementById('id_docente');
        gSelect.innerHTML = '<option value="">Seleccione grupo...</option>';
        mSelect.innerHTML = '<option value="">Seleccione materia...</option>';
        // no limpiamos dSelect porque lo rellenaremos según carrera
        document.querySelectorAll("input[name='dia_semana[]']").forEach(cb => cb.checked = false);
        document.getElementById('horario_texto').value = '';

        // obtener select carrera (puede estar oculto para coordinador)
        const carreraSelect = document.getElementById('id_carrera');

        // determinar carreraId con prioridad:
        // 1) data.id_carrera (registro existente)
        // 2) DEFAULT_CARRERA (coordinador)
        // 3) valor actual del select (admin)
        let carreraId = null;
        if (data && data.id_carrera) {
            carreraId = data.id_carrera;
        } else if (DEFAULT_CARRERA) {
            carreraId = DEFAULT_CARRERA;
        } else if (carreraSelect) {
            carreraId = carreraSelect.value || null;
        }

        // si no hay carreraId, dejamos selects vacíos y salimos
        if (!carreraId) {
            // asegurar que docSelect tenga por lo menos la lista completa si admin
            if (docentesPorCarrera && Object.keys(docentesPorCarrera).length) {
                rellenarDocentes(null); // dejar vacio o con global
            }
            return;
        }

        // si existe el select de carrera, setear su valor y si hace falta crear option (coordinador oculto)
        if (carreraSelect) {
            if (!Array.from(carreraSelect.options).some(o => o.value == carreraId)) {
                const opt = document.createElement('option');
                opt.value = carreraId;
                opt.textContent = carrerasMap[carreraId] || '';
                carreraSelect.appendChild(opt);
            }
            carreraSelect.value = carreraId;
        }

        // cargar grupos/materias y docentes para la carrera
        cargarSelects(carreraId, data?.id_grupo, data?.id_materia);
        rellenarDocentes(carreraId, data?.id_docente);

        // si data (edición) completar dias y horario
        if (data) {
            // completar dias (p.ej. "L-M-X")
            if (data.dias && typeof data.dias === 'string') {
                data.dias.split('-').forEach(d => {
                    const cb = document.querySelector("input[name='dia_semana[]'][value='" + d + "']");
                    if (cb) cb.checked = true;
                });
            }
            // horario texto
            if (data.horario_texto) {
                document.getElementById('horario_texto').value = data.horario_texto;
            }
        }
    }

    // cuando admin cambie de carrera en el modal, recargar selects
    function cambiarCarrera(id_carrera) {
        cargarSelects(id_carrera);
        rellenarDocentes(id_carrera);
    }

    // carga grupos y materias de la carrera, y selecciona si se pasan valores
    function cargarSelects(id_carrera, selectedGrupo = null, selectedMateria = null) {
        const gSelect = document.getElementById('id_grupo');
        const mSelect = document.getElementById('id_materia');
        gSelect.innerHTML = '<option value="">Seleccione grupo...</option>';
        mSelect.innerHTML = '<option value="">Seleccione materia...</option>';

        if (!id_carrera) return;

        // Grupos
        if (gruposPorCarrera[id_carrera]) {
            gruposPorCarrera[id_carrera].forEach(g => {
                const opt = document.createElement('option');
                opt.value = g.id_grupo;
                opt.textContent = g.nombre;
                if (selectedGrupo && selectedGrupo == g.id_grupo) opt.selected = true;
                gSelect.appendChild(opt);
            });
        }

        // Materias
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

    // rellena (o filtra) el select de docentes según la carrera
    // si id_carrera es null -> deja la lista que ya trae el servidor (o vacía)
    // si id_docente_selected se pasa, lo selecciona al final (usado en edición)
    function rellenarDocentes(id_carrera = null, id_docente_selected = null) {
        const dSelect = document.getElementById('id_docente');
        dSelect.innerHTML = '<option value="">Seleccione...</option>';

        if (!id_carrera) {
            // si no hay carrera, y existe un mapping global, no rellenar
            return;
        }

        const lista = docentesPorCarrera[id_carrera] || [];

        lista.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.id_docente;
            // algunos modelos devuelven nombre + apellidos separados; ajusta si hace falta
            opt.textContent = (d.nombre ? d.nombre : '') + (d.apellidos ? ' ' + d.apellidos : '');
            dSelect.appendChild(opt);
        });

        if (id_docente_selected) {
            setTimeout(() => {
                dSelect.value = id_docente_selected;
            }, 10);
        }
    }

    // filtro en la tabla principal por carrera (solo UI)
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

    // cerrar modal al click fuera
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('modalHorario');
        if (event.target == modal) modal.style.display = 'none';
    });
</script>

<script src="/ASISTENCIAS/js/modales.js"></script>

<?php
$content = ob_get_clean();
$title = "Horarios";
include "dashboard.php";
?>
