<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/ParcialesModel.php";

$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

$model = new ParcialesModel($conn);
$parciales = $model->getParciales($id_carrera);



ob_start();
?>

<div class="container-form">
    <h2>Parciales</h2>



    <?php if ($rol === 'admin'): ?>
        <div class="filtros-container" style="margin-bottom:15px;">
            <div>
                <label>Filtrar por carrera:</label>
                <select id="filtroCarrera">
                    <option value="">Todas</option>
                    <?php
                    $carreras = $conn->query("SELECT id_carrera, nombre FROM carreras")->fetch_all(MYSQLI_ASSOC);
                    foreach ($carreras as $c): ?>
                        <option value="<?= $c['id_carrera'] ?>"><?= $c['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>

    <button class="btn-agregar" onclick="abrirModal()">Agregar Parcial</button>

    <table class="tabla-docentes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Parcial</th>
                <th>Fecha inicio</th>
                <th>Fecha fin</th>
                <?php if ($rol === 'admin'): ?><th>Carrera</th><?php endif; ?>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>

            <?php foreach ($parciales as $p): ?>
                <tr data-id="<?= $p['id_parcial'] ?>"
                    data-numero="<?= $p['numero_parcial'] ?>"
                    data-inicio="<?= $p['fecha_inicio'] ?>"
                    data-fin="<?= $p['fecha_fin'] ?>"
                    data-id_carrera="<?= $p['id_carrera'] ?>">

                    <td><?= $p['id_parcial'] ?></td>
                    <td><?= $p['numero_parcial'] ?></td>
                    <td><?= $p['fecha_inicio'] ?></td>
                    <td><?= $p['fecha_fin'] ?></td>

                    <?php if ($rol === 'admin'): ?>
                        <td><?= $p['carrera'] ?></td>
                    <?php endif; ?>

                    <td>
                        <button class="btn-editar" onclick="abrirModal(<?= $p['id_parcial'] ?>)">Editar</button>
                        <a href="../controllers/ParcialesController.php?eliminar=<?= $p['id_parcial'] ?>"
                            class="btn-eliminar"
                            onclick="return confirm('¿Eliminar parcial?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
</div>

<!-- MODAL -->
<div id="modalParcial" class="modal">
    <div class="modal-content">
        <span class="cerrar" onclick="cerrarModal('modalParcial')">&times;</span>
        <h2 id="tituloModal">Agregar Parcial</h2>

        <form method="POST" action="../controllers/ParcialesController.php">

            <input type="hidden" name="accion" id="accion" value="agregar">
            <input type="hidden" name="id_parcial" id="id_parcial">

            <label>Parcial</label>
            <input type="number" name="numero_parcial" id="numero_parcial" required>

            <label>Fecha inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" required>

            <label>Fecha fin</label>
            <input type="date" name="fecha_fin" id="fecha_fin" required>

            <?php if ($rol === 'admin'): ?>
                <label>Carrera</label>
                <select name="id_carrera" id="id_carrera">
                    <?php foreach ($carreras as $c): ?>
                        <option value="<?= $c['id_carrera'] ?>"><?= $c['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <button type="submit">Guardar</button>
        </form>

    </div>
</div>

<script>
    function abrirModal(id = null) {
        const modal = document.getElementById("modalParcial");
        modal.style.display = "block";

        if (id) {
            const row = document.querySelector(`tr[data-id='${id}']`);
            document.getElementById("tituloModal").innerText = "Editar Parcial";
            document.getElementById("accion").value = "editar";
            document.getElementById("id_parcial").value = id;
            document.getElementById("numero_parcial").value = row.dataset.numero;
            document.getElementById("fecha_inicio").value = row.dataset.inicio;
            document.getElementById("fecha_fin").value = row.dataset.fin;

            <?php if ($rol === 'admin'): ?>
                document.getElementById("id_carrera").value = row.dataset.id_carrera;
            <?php endif; ?>

        } else {
            document.getElementById("tituloModal").innerText = "Agregar Parcial";
            document.getElementById("accion").value = "agregar";
            document.getElementById("id_parcial").value = "";
            document.getElementById("numero_parcial").value = "";
            document.getElementById("fecha_inicio").value = "";
            document.getElementById("fecha_fin").value = "";
        }
    }

    // FILTRO POR CARRERA (solo admin)
    document.getElementById('filtroCarrera')?.addEventListener('change', function() {
        let filtro = this.value;
        let filas = document.querySelectorAll(".tabla-docentes tbody tr");

        filas.forEach(row => {
            if (filtro === "" || row.dataset.id_carrera === filtro) {
                row.style.display = ""; // mostrar
            } else {
                row.style.display = "none"; // ocultar
            }
        });
    });
</script>
<script src="/ASISTENCIAS/js/modales.js"></script>
<?php
$content = ob_get_clean();
$title = "Parciales";
$pagina = "parciales";
include "dashboard.php";
?>