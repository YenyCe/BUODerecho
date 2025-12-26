<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/MateriasModel.php";
require_once "../models/CarrerasModel.php";
require_once "../models/SemestresModel.php";

$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

$materiaModel = new MateriasModel($conn);
$carrerasModel = new CarrerasModel($conn);
$semModel = new SemestresModel($conn);

// Obtener materias filtradas por carrera si es coordinador
$materias = $materiaModel->getMaterias($id_carrera);

// Obtener todos los semestres para el select y filtro
$semestres = $semModel->getSemestres();

// Solo admin necesita todas las carreras
$carreras = ($rol === 'admin') ? $carrerasModel->obtenerCarreras() : [];

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
    <h2>Materias</h2>
    <?= $alerta ?>

    <?php if ($rol === 'admin'): ?>
<div class="filtros-container" style="margin-bottom:15px;">
    <?php if ($rol === 'admin'): ?>
        <div>
            <label>Filtrar por carrera:</label>
            <select id="filtroCarrera">
                <option value="">Todas</option>
                <?php foreach ($carreras as $c): ?>
                    <option value="<?= $c['id_carrera'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>

    <div>
        <label>Filtrar por semestre:</label>
        <select id="filtroSemestre">
            <option value="">Todos</option>
            <?php foreach ($semestres as $s): ?>
                <option value="<?= $s['id_semestre'] ?>"><?= $s['numero'] ?>°</option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

    <?php endif; ?>

    <button class="btn-agregar" onclick="abrirModalMateria()">Agregar Materia</button>

    <table class="tabla-docentes">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Clave</th>
                <th>Semestre</th>
                <th>H/Semana</th>
                <th>H/Semestre</th>
                <?php if ($rol === 'admin'): ?><th>Carrera</th><?php endif; ?>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($materias)): ?>
                <tr>
                    <td colspan="<?= $rol === 'admin' ? 7 : 6 ?>" style="text-align:center; color:#555;">
                        No hay materias registradas
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($materias as $m): ?>
                    <tr
                        data-id="<?= $m['id_materia'] ?>"
                        data-nombre="<?= htmlspecialchars($m['nombre']) ?>"
                        data-clave="<?= htmlspecialchars($m['clave']) ?>"
                        data-horas_semana="<?= $m['horas_semana'] ?>"
                        data-horas_semestre="<?= $m['horas_semestre'] ?>"
                        data-id_carrera="<?= $m['id_carrera'] ?>"
                        data-id_semestre="<?= $m['id_semestre'] ?>">
                        <td><?= htmlspecialchars($m['nombre']) ?></td>
                        <td><?= htmlspecialchars($m['clave']) ?></td>
                        <td><?= $m['semestre_num'] ?></td>
                        <td><?= $m['horas_semana'] ?></td>
                        <td><?= $m['horas_semestre'] ?></td>
                        <?php if ($rol === 'admin'): ?>
                            <td><?= htmlspecialchars($m['nombre_carrera'] ?? '') ?></td>
                        <?php endif; ?>
                        <td>
                            <button class="btn-editar" onclick="abrirModalMateria(<?= $m['id_materia'] ?>)">Editar</button>
                            <a href="../controllers/MateriasController.php?eliminar=<?= $m['id_materia'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar esta materia?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Materia -->
<div id="modalMateria" class="modal">
    <div class="modal-content">
        <span class="cerrar" onclick="cerrarModalMateria()">&times;</span>
        <h2 id="tituloModalMateria">Agregar Materia</h2>
        <form id="formMateria" action="../controllers/MateriasController.php" method="POST">
            <input type="hidden" name="accion" value="agregar" id="accionMateria">
            <input type="hidden" name="id_materia" id="id_materia">

            <label>Nombre</label>
            <input type="text" name="nombre" id="nombreMateria" required>

            <label>Clave</label>
            <input type="text" name="clave" id="claveMateria" required>

            <label>Semestre</label>
            <select name="id_semestre" id="id_semestre_materia" required>
                <option value="">Seleccione un semestre</option>
                <?php foreach ($semestres as $s): ?>
                    <option value="<?= $s['id_semestre'] ?>"><?= $s['numero'] ?>°</option>
                <?php endforeach; ?>
            </select>

            <label>Horas/Semana</label>
            <input type="number" name="horas_semana" id="horasSemanaMateria" required>

            <label>Horas/Semestre</label>
            <input type="number" name="horas_semestre" id="horasSemestreMateria" required>

            <?php if ($rol === 'admin'): ?>
                <label>Carrera</label>
                <select name="id_carrera" id="id_carrera_materia" required>
                    <option value="">Seleccione una carrera</option>
                    <?php foreach ($carreras as $c): ?>
                        <option value="<?= $c['id_carrera'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input type="hidden" name="id_carrera" value="<?= $id_carrera ?>">
            <?php endif; ?>

            <button type="submit">Guardar</button>
        </form>
    </div>
</div>

<script>
function abrirModalMateria(id = null) {
    const modal = document.getElementById('modalMateria');
    modal.style.display = 'block';

    if (id) {
        const row = document.querySelector(`tr[data-id='${id}']`);
        document.getElementById('tituloModalMateria').innerText = 'Editar Materia';
        document.getElementById('accionMateria').value = 'editar';
        document.getElementById('id_materia').value = id;
        document.getElementById('nombreMateria').value = row.dataset.nombre;
        document.getElementById('claveMateria').value = row.dataset.clave;
        document.getElementById('horasSemanaMateria').value = row.dataset.horas_semana;
        document.getElementById('horasSemestreMateria').value = row.dataset.horas_semestre;
        document.getElementById('id_semestre_materia').value = row.dataset.id_semestre || '';
        const carrera_input = document.getElementById('id_carrera_materia');
        if(carrera_input) carrera_input.value = row.dataset.id_carrera || '';
    } else {
        document.getElementById('tituloModalMateria').innerText = 'Agregar Materia';
        document.getElementById('accionMateria').value = 'agregar';
        document.getElementById('id_materia').value = '';
        document.getElementById('nombreMateria').value = '';
        document.getElementById('claveMateria').value = '';
        document.getElementById('horasSemanaMateria').value = '';
        document.getElementById('horasSemestreMateria').value = '';
        document.getElementById('id_semestre_materia').value = '';
        const carrera_input = document.getElementById('id_carrera_materia');
        if(carrera_input) carrera_input.value = '';
    }
}

function cerrarModalMateria() {
    document.getElementById('modalMateria').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('modalMateria');
    if (event.target == modal) cerrarModalMateria();
}

// Filtro por carrera
document.getElementById('filtroCarrera')?.addEventListener('change', function() {
    const carrera = this.value;
    const filas = document.querySelectorAll('table tbody tr');
    filas.forEach(fila => {
        const idCarreraFila = fila.dataset.id_carrera;
        fila.style.display = (carrera === "" || carrera === idCarreraFila) ? "" : "none";
    });
});

// Filtro por semestre
document.getElementById('filtroSemestre')?.addEventListener('change', function() {
    const semestre = this.value;
    const filas = document.querySelectorAll('table tbody tr');
    filas.forEach(fila => {
        const idSemestreFila = fila.dataset.id_semestre;
        fila.style.display = (semestre === "" || idSemestreFila === semestre) ? "" : "none";
    });
});
</script>
<script src="/ASISTENCIAS/js/modales.js"></script>

<?php
$content = ob_get_clean();
$title = "Materias";
$pagina = "materias";
include "dashboard.php";
?>
