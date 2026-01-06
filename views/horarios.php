<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$alerta = '';
if (isset($_SESSION['alerta'])) {
    $alerta = "<div class='alerta {$_SESSION['alerta']['tipo']}'>
                {$_SESSION['alerta']['mensaje']}
               </div>";
    unset($_SESSION['alerta']);
}
var_dump($_SESSION['id_carrera']);

// ===========================================================
// 3. OBTENER PARCIALES DE ESA CARRERA
// ===========================================================
$parciales = [];

if ($id_carrera_usuario) {
    $parciales = $conn->query("
        SELECT * FROM parciales
        WHERE id_carrera = $id_carrera_usuario
        ORDER BY numero_parcial
    ")->fetch_all(MYSQLI_ASSOC);
}
ob_start();
?>

<div class="container-form">
    <?= $alerta ?>

    <h2>Horarios</h2>
<div class="filtros-container" style="margin-bottom:15px; display:flex; gap:15px;">

    <!-- FILTRO CARRERA (SOLO ADMIN) -->
    <?php if ($_SESSION['rol'] === 'admin'): ?>
        <div>
            <label>Carrera:</label>
            <select id="filtroCarrera" class="form-control">
                <option value="">Todas</option>
                <?php foreach ($carreras as $c): ?>
                    <option value="<?= $c['id_carrera'] ?>">
                        <?= htmlspecialchars($c['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>

    <!-- FILTRO GRUPO (ADMIN Y COORDINADOR) -->
    <div>
        <label>Grupo:</label>
        <select id="filtroGrupo" class="form-control">
            <option value="">Todos</option>
            <?php
            $gruposUnicos = [];
            foreach ($horarios as $h) {
                if (!in_array($h['grupo'], $gruposUnicos)) {
                    $gruposUnicos[] = $h['grupo'];
                    echo "<option value='{$h['grupo']}'>{$h['grupo']}</option>";
                }
            }
            ?>
        </select>
    </div>
</div>

            
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
                        <a class="btn-eliminar" href="/controllers/HorariosController.php?accion=eliminar&id=<?= $h['id_horario'] ?>" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                        <button class="btn-generar"
                            onclick='abrirModalGenerar(
                                <?= $h["id_carrera"] ?>,
                                <?= $h["id_grupo"] ?>,
                                <?= $h["id_materia"] ?>,
                                <?= $h["id_docente"] ?>
                            )'>
                            Generar
                        </button>

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
        <form action="../controllers/HorariosController.php" method="POST" id="formHorario">

            <input type="hidden" name="accion" id="accion" value="guardar">
            <input type="hidden" name="id_horario" id="id_horario">

            <div class="form-grid">

                <!-- Carrera -->
                <?php if ($_SESSION['rol'] === 'admin'): ?>
                    <div>
                        <label>Carrera</label>
                        <select name="id_carrera" id="id_carrera" required onchange="cambiarCarrera(this.value)">
                            <option value="">Seleccione...</option>
                            <?php foreach ($carreras as $c): ?>
                                <option value="<?= $c['id_carrera'] ?>">
                                    <?= htmlspecialchars($c['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <!-- Coordinador -->
                    <input type="hidden" name="id_carrera" id="id_carrera"
                        value="<?= $id_carrera_usuario ?>">
                <?php endif; ?>

                <!-- Grupo -->
                <div>
                    <label>Grupo</label>
                    <select name="id_grupo" id="id_grupo" required>
                        <option value="">Seleccione grupo...</option>
                    </select>
                </div>

                <!-- Materia -->
                <div>
                    <label>Materia</label>
                    <select name="id_materia" id="id_materia" required>
                        <option value="">Seleccione materia...</option>
                    </select>
                </div>

                <!-- Docente -->
                <div>
                    <label>Docente</label>
                    <select name="id_docente" id="id_docente" required>
                        <option value="">Seleccione...</option>
                        <?php foreach ($docentes as $d): ?>
                            <option value="<?= $d['id_docente'] ?>">
                                <?= htmlspecialchars($d['nombre'] . ' ' . ($d['apellidos'] ?? '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Días -->
                <div class="full-row">
                    <label>Días</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="dia_semana[]" value="L"> Lun</label>
                        <label><input type="checkbox" name="dia_semana[]" value="M"> Mar</label>
                        <label><input type="checkbox" name="dia_semana[]" value="X"> Mié</label>
                        <label><input type="checkbox" name="dia_semana[]" value="J"> Jue</label>
                        <label><input type="checkbox" name="dia_semana[]" value="V"> Vie</label>
                        <label><input type="checkbox" name="dia_semana[]" value="S"> Sáb</label>

                    </div>
                </div>

                <!-- Horario -->
                <div class="full-row">
                    <label>Horario</label>
                    <input type="text" name="horario_texto" id="horario_texto"
                        placeholder="Ej: LUNES DE 07:00 A 08:50, MARTES DE 07:00 A 07:50" required>
                </div>

                <!-- Botón -->
                <div class="full-row">
                    <button type="submit">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="modalGenerar" class="modal">
    <div class="modal-content">
        <span class="cerrar" onclick="cerrarModal('modalGenerar')">&times;</span>
        <h2>Generar listas / reportes</h2>

        <form method="POST" target="_blank" id="formGenerar">

            <input type="hidden" name="id_carrera" id="g_id_carrera">
            <input type="hidden" name="id_grupo" id="g_id_grupo">
            <input type="hidden" name="id_materia" id="g_id_materia">
            <input type="hidden" name="id_docente" id="g_id_docente">

                   <label>Parcial</label>
                <select name="id_parcial">
                    <option value="">-- Ninguno --</option>
                    <?php foreach ($parciales as $p): ?>
                        <option value="<?= $p['id_parcial'] ?>">
                            Parcial <?= $p['numero_parcial'] ?> (<?= $p['fecha_inicio'] ?> a <?= $p['fecha_fin'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            <div style="display:flex; gap:10px; margin-top:15px; justify-content:center;">

                <button type="submit"
                    formaction="lista_impresion.php"
                    class="btn-agregar">
                    Lista de Asistencia
                </button>

                <button type="submit"
                    formaction="reporte_calificaciones.php"
                    class="btn-agregar"
                    style="background:#28a745;">
                    Reporte Calificaciones
                </button>

                <button type="submit"
                    formaction="control_asistencia_docente.php"
                    class="btn-agregar"
                    style="background:#6f42c1;">
                    Control Docente
                </button>

            </div>

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

        if (carreraSelect && carreraSelect.options) {
            if (!Array.from(carreraSelect.options).some(o => o.value == carreraId)) {
                const opt = document.createElement('option');
                opt.value = carreraId;
                opt.textContent = carrerasMap[carreraId] || '';
                carreraSelect.appendChild(opt);
            }
            carreraSelect.value = carreraId;
        } else {
            console.error("El select de carrera no está disponible en el DOM.");
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

    function abrirModalGenerar(id_carrera, id_grupo, id_materia, id_docente) {

    document.getElementById('g_id_carrera').value = id_carrera;
    document.getElementById('g_id_grupo').value   = id_grupo;
    document.getElementById('g_id_materia').value = id_materia;
    document.getElementById('g_id_docente').value = id_docente;

    document.getElementById('modalGenerar').style.display = 'block';
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
<script>
function aplicarFiltros() {
    const carreraSelect = document.getElementById("filtroCarrera");
    const grupoSelect = document.getElementById("filtroGrupo");

    const carrera = carreraSelect ? carreraSelect.value : "";
    const grupo = grupoSelect ? grupoSelect.value : "";

    document.querySelectorAll(".tabla-docentes tbody tr").forEach(fila => {
        const tdCarrera = fila.children[0].innerText.trim();
        const tdGrupo   = fila.children[1].innerText.trim();

        let mostrar = true;

        if (carrera !== "" && tdCarrera !== carrera) {
            mostrar = false;
        }

        if (grupo !== "" && tdGrupo !== grupo) {
            mostrar = false;
        }

        fila.style.display = mostrar ? "" : "none";
    });
}

document.getElementById("filtroCarrera")?.addEventListener("change", aplicarFiltros);
document.getElementById("filtroGrupo")?.addEventListener("change", aplicarFiltros);
</script>

<script src="/js/modales.js"></script>

<?php
$content = ob_get_clean();
$title = "Horarios";
$pagina = "horarios";
include "dashboard.php";
?>

