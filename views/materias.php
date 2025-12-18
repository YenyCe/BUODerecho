<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/MateriasModel.php";
require_once "../models/CarrerasModel.php";

$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

$materiaModel = new MateriasModel($conn);
$carrerasModel = new CarrerasModel($conn);

$materias = $materiaModel->getMaterias($id_carrera); // Filtra por carrera si es coordinador

// Solo administrador necesita todas las carreras para el select
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
            <div>
                <label>Filtrar por carrera:</label>

                <select id="filtroCarrera" onchange="filtrarCarrera()">
                    <option value="">Todas</option>
                    <?php
                    $carreras = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
                    foreach ($carreras as $c): ?>
                        <option value="<?= $c['id_carrera'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
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
                <th>H/Semana</th>
                <th>H/Semestre</th>
                <?php if ($rol === 'admin'): ?><th>Carrera</th><?php endif; ?>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($materias as $m): ?>
                <tr
                    data-id="<?= $m['id_materia'] ?>"
                    data-nombre="<?= htmlspecialchars($m['nombre']) ?>"
                    data-clave="<?= htmlspecialchars($m['clave']) ?>"
                    data-horas_semana="<?= $m['horas_semana'] ?>"
                    data-horas_semestre="<?= $m['horas_semestre'] ?>"
                    data-id_carrera="<?= $m['id_carrera'] ?? '' ?>">
                    <td><?= htmlspecialchars($m['nombre']) ?></td>
                    <td><?= htmlspecialchars($m['clave']) ?></td>
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

            if (document.getElementById('id_carrera_materia')) {
                document.getElementById('id_carrera_materia').value = row.dataset.id_carrera || '';
            }
        } else {
            document.getElementById('tituloModalMateria').innerText = 'Agregar Materia';
            document.getElementById('accionMateria').value = 'agregar';
            document.getElementById('id_materia').value = '';
            document.getElementById('nombreMateria').value = '';
            document.getElementById('claveMateria').value = '';
            document.getElementById('horasSemanaMateria').value = '';
            document.getElementById('horasSemestreMateria').value = '';
            if (document.getElementById('id_carrera_materia')) document.getElementById('id_carrera_materia').value = '';
        }
    }

    function cerrarModalMateria() {
        document.getElementById('modalMateria').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('modalMateria');
        if (event.target == modal) {
            cerrarModalMateria();
        }
    }

    document.getElementById('filtroCarrera')?.addEventListener('change', function() {
        const carrera = this.value;
        const filas = document.querySelectorAll('table tbody tr');

        filas.forEach(fila => {
            const idCarreraFila = fila.getAttribute('data-id_carrera');
            fila.style.display = (carrera === "" || carrera === idCarreraFila) ? "" : "none";
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