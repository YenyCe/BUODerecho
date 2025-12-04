<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/AlumnosModel.php";
$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

$alumnoModel = new AlumnosModel($conn);

$alumnos = $alumnoModel->getAlumnos($id_carrera);
$grupos = $alumnoModel->getGrupos();


// Si es ADMIN: mostrar carreras y grupos dinámicos
if ($rol === 'admin') {
    require_once "../models/CarrerasModel.php";
    $carrerasModel = new CarrerasModel($conn);
    $carreras = $carrerasModel->obtenerCarreras();
}

// Grupos del coordinador o grupos totales (admin)
$grupos = ($rol === 'admin')
    ? $alumnoModel->getGrupos()
    : $alumnoModel->getGruposPorCarrera($id_carrera);

$alerta = "";
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == "success") $alerta = "<div class='alerta success'>Alumno agregado correctamente</div>";
    if ($_GET['msg'] == "edited") $alerta = "<div class='alerta success'>Alumno editado correctamente</div>";
    if ($_GET['msg'] == "deleted") $alerta = "<div class='alerta error'>Alumno eliminado correctamente</div>";
}
// INICIAR CAPTURA  
ob_start();
?>
<div class="container-form">
    <h2>Alumnos</h2>
    <?php if ($alerta): ?>
        <div id="alertaMsg" class="alerta <?php echo strpos($alerta, 'success') !== false ? 'success' : 'error'; ?>">
            <span><?php echo strip_tags($alerta); ?></span>
            <span class="cerrar-alerta" onclick="cerrarAlerta()">&times;</span>
        </div>
    <?php endif; ?>

    <div class="filtros-container">

        <?php if ($rol === 'admin'): ?>
            <!-- FILTRO PARA ADMIN -->
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

        <!-- FILTRO DE GRUPO PARA ADMIN Y COORDINADOR -->
        <div style="margin-bottom: 15px;">
            <label>Grupo:</label>
            <select id="filtroGrupo" class="form-control">
                <option value="">Todos</option>
                <?php foreach ($grupos as $g): ?>
                    <option value="<?= $g['nombre'] ?>"><?= htmlspecialchars($g['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

    </div>

    <button class="btn-agregar" onclick="abrirModalAlumno()">Agregar Alumno</button>

    <table class="tabla-docentes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Grupo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($alumnos as $a): ?>
                <tr
                    data-id="<?= $a['id_alumno']; ?>"
                    data-nombre="<?= $a['nombre']; ?>"
                    data-id_grupo="<?= $a['id_grupo']; ?>"
                    data-grupo="<?= $a['grupo']; ?>"
                    data-carrera="<?= $a['id_carrera']; ?>">
                    <td><?= $a['id_alumno']; ?></td>
                    <td><?= $a['nombre']; ?></td>
                    <td><?= $a['grupo']; ?></td>
                    <td>
                        <button class="btn-editar" onclick="abrirModalAlumno(<?= $a['id_alumno']; ?>)">Editar</button>
                        <a href="../controllers/AlumnosController.php?eliminar=<?= $a['id_alumno']; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este alumno?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
</div>

<!-- Modal Alumno -->
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
                    <option value="<?php echo $g['id_grupo']; ?>"><?php echo $g['nombre']; ?></option>
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
        const carrera = filtroCarrera?.value || "";
        const grupo = filtroGrupo?.value || "";

        filas.forEach(fila => {
            const carreraFila = fila.dataset.carrera;
            const grupoFila = fila.dataset.grupo;

            const coincideCarrera = (carrera === "" || carrera === carreraFila);
            const coincideGrupo = (grupo === "" || grupo === grupoFila);

            fila.style.display = (coincideCarrera && coincideGrupo) ? "" : "none";
        });
    }

    filtroCarrera?.addEventListener("change", aplicarFiltros);
    filtroGrupo?.addEventListener("change", aplicarFiltros);
</script>

<script src="/ASISTENCIAS/js/modales.js"></script>

<?php
// FIN de la captura
$content = ob_get_clean();
$title = "Alumnos";
$pagina = "alumnos";
// Cargar layout
include "dashboard.php";
?>
