<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/AlumnosModel.php";

$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

$alumnoModel = new AlumnosModel($conn);

/* -----------------------------------------------------------
   LISTA DE ALUMNOS
----------------------------------------------------------- */
$alumnos = $alumnoModel->getAlumnos($id_carrera);

/* -----------------------------------------------------------
   GRUPOS
----------------------------------------------------------- */
if ($rol === 'admin') {
    $grupos = $alumnoModel->getGrupos();
} else {
    $grupos = $alumnoModel->getGruposPorCarrera($id_carrera);
}

/* -----------------------------------------------------------
   CARRERAS (solo admin)
----------------------------------------------------------- */
if ($rol === 'admin') {
    require_once "../models/CarrerasModel.php";
    $cm = new CarrerasModel($conn);
    $carreras = $cm->obtenerCarreras();
}

/* -----------------------------------------------------------
   ALERTAS
----------------------------------------------------------- */
$alerta = '';
if (isset($_SESSION['alerta'])) {
    $alerta = "<div class='alerta {$_SESSION['alerta']['tipo']}'>
                {$_SESSION['alerta']['mensaje']}
               </div>";
    unset($_SESSION['alerta']);
}


ob_start();
?>

<div class="container-form">
    <h2>Alumnos</h2>
    <?= $alerta ?>

    <!-- FILTROS -->
    <div class="filtros-container">

        <?php if ($rol === 'admin'): ?>
            <div style="margin-bottom: 15px;">
                <label>Carrera:</label>
                <select id="filtroCarrera" class="form-control">
                    <option value="">Todas</option>
                    <?php foreach ($carreras as $c): ?>
                        <option value="<?= $c['id_carrera'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 15px;">
            <label>Grupo:</label>
            <select id="filtroGrupo" class="form-control">
                <option value="">Todos</option>
                <?php foreach ($grupos as $g): ?>
                    <option value="<?= $g['id_grupo'] ?>"><?= htmlspecialchars($g['nombre_grupo']) ?></option>

                <?php endforeach; ?>
            </select>
        </div>

    </div>

    <button class="btn-agregar" onclick="abrirModalAlumno()">Agregar Alumno</button>

    <!-- TABLA -->
    <table class="tabla-docentes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Grupo</th>
                <th>Carrera</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
    <?php if (count($alumnos) === 0): ?>
        <tr>
            <td colspan="5" style="text-align:center; color:#555;">
                No hay alumnos registrados
            </td>
        </tr>
    <?php else: ?>
        <?php foreach ($alumnos as $a): ?>
            <tr
                data-id="<?= $a['id_alumno']; ?>"
                data-nombre="<?= htmlspecialchars($a['nombre']); ?>"
                data-id_grupo="<?= $a['id_grupo']; ?>"
                data-carrera="<?= $a['id_carrera']; ?>"
            >
                <td><?= $a['id_alumno']; ?></td>
                <td><?= htmlspecialchars($a['nombre']); ?></td>
                <td><?= htmlspecialchars($a['grupo']); ?></td>
                <td><?= htmlspecialchars($a['carrera']); ?></td>

                <td>
                    <button class="btn-editar" onclick="abrirModalAlumno(<?= $a['id_alumno']; ?>)">Editar</button>
                    <a href="../controllers/AlumnosController.php?eliminar=<?= $a['id_alumno']; ?>"
                       class="btn-eliminar"
                       onclick="return confirm('¿Eliminar este alumno?')">
                        Eliminar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>

    </table>

</div>

<!-- MODAL -->
<div id="modalAlumno" class="modal">
    <div class="modal-content">
        <span class="cerrar" onclick="cerrarModal('modalAlumno')">&times;</span>
        <h2 id="tituloModalAlumno">Agregar Alumno</h2>

        <form id="formAlumno" action="../controllers/AlumnosController.php" method="POST">
            <input type="hidden" name="accion" value="agregar" id="accionAlumno">
            <input type="hidden" name="id_alumno" id="id_alumno">

            <label>Nombre</label>
            <input type="text" name="nombre" id="nombreAlumno" required>

            <label>Grupo</label>
            <select name="id_grupo" id="id_grupoAlumno" required>
                <option value="">Seleccione un grupo</option>

                <?php foreach ($grupos as $g): ?>
                    <option value="<?= $g['id_grupo']; ?>">
                        <?= htmlspecialchars($g['nombre_grupo']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Guardar</button>
        </form>
    </div>
</div>

<script>
function abrirModalAlumno(id = null) {
    const modal = document.getElementById('modalAlumno');
    modal.style.display = 'block';

    if (id) {
        const row = document.querySelector(`tr[data-id='${id}']`);
        document.getElementById('tituloModalAlumno').innerText = 'Editar Alumno';
        document.getElementById('accionAlumno').value = 'editar';
        document.getElementById('id_alumno').value = id;
        document.getElementById('nombreAlumno').value = row.dataset.nombre;
        document.getElementById('id_grupoAlumno').value = row.dataset.id_grupo;

    } else {
        document.getElementById('tituloModalAlumno').innerText = 'Agregar Alumno';
        document.getElementById('accionAlumno').value = 'agregar';
        document.getElementById('id_alumno').value = '';
        document.getElementById('nombreAlumno').value = '';
        document.getElementById('id_grupoAlumno').value = '';
    }
}

const filtroCarrera = document.getElementById("filtroCarrera");
const filtroGrupo = document.getElementById("filtroGrupo");
const filas = document.querySelectorAll("tbody tr");

function aplicarFiltros() {
    const c = filtroCarrera?.value || "";
    const g = filtroGrupo.value || "";

    filas.forEach(fila => {
        const cFila = fila.dataset.carrera;
        const gFila = fila.dataset.id_grupo;

        const okCarrera = (c === "" || c === cFila);
        const okGrupo   = (g === "" || g === gFila);

        fila.style.display = (okCarrera && okGrupo) ? "" : "none";
    });
}

filtroCarrera?.addEventListener("change", aplicarFiltros);
filtroGrupo.addEventListener("change", aplicarFiltros);
</script>

<script src="/ASISTENCIAS/js/modales.js"></script>

<?php
$content = ob_get_clean();
$title = "Alumnos";
$pagina = "alumnos";
include "dashboard.php";
?>


