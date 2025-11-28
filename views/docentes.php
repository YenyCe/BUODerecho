<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/DocentesModel.php";

$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

$docenteModel = new DocentesModel($conn);
$docentes = $docenteModel->getDocentes($id_carrera);

$alerta = "";
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == "success") $alerta = "<div class='alerta success'>Docente agregado correctamente</div>";
    if ($_GET['msg'] == "edited")  $alerta = "<div class='alerta success'>Docente editado correctamente</div>";
    if ($_GET['msg'] == "deleted") $alerta = "<div class='alerta error'>Docente eliminado correctamente</div>";
}

// INICIAR CAPTURA  
ob_start();
?>

<div class="container-form">
    <h2>Docentes</h2>
    <?php if ($alerta): ?>
        <div id="alertaMsg" class="alerta <?php echo strpos($alerta, 'success') !== false ? 'success' : 'error'; ?>">
            <span><?php echo strip_tags($alerta); ?></span>
            <span class="cerrar-alerta" onclick="cerrarAlerta()">&times;</span>
        </div>
    <?php endif; ?>

    <button class="btn-agregar" onclick="abrirModal()">Agregar Docente</button>

    <table class="tabla-docentes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <?php if($rol === 'admin'): ?>
                    <th>Carrera</th>
                <?php endif; ?>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($docentes as $d): ?>
                <tr data-id="<?= $d['id_docente'] ?>"
                    data-nombre="<?= htmlspecialchars($d['nombre']) ?>"
                    data-apellidos="<?= htmlspecialchars($d['apellidos']) ?>"
                    data-correo="<?= htmlspecialchars($d['correo']) ?>"
                    data-telefono="<?= htmlspecialchars($d['telefono']) ?>"
                    data-id_carrera="<?= $d['id_carrera'] ?? '' ?>">
                    <td><?= $d['id_docente'] ?></td>
                    <td><?= htmlspecialchars($d['nombre'].' '.$d['apellidos']) ?></td>
                    <td><?= htmlspecialchars($d['correo']) ?></td>
                    <td><?= htmlspecialchars($d['telefono']) ?></td>
                    <?php if($rol === 'admin'): ?>
                        <td>
                            <?php
                            if($d['id_carrera']){
                                $carrera = $conn->query("SELECT nombre FROM carreras WHERE id_carrera={$d['id_carrera']}")->fetch_assoc();
                                echo htmlspecialchars($carrera['nombre']);
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>
                    <?php endif; ?>
                    <td>
                        <button class="btn-editar" onclick="abrirModal(<?= $d['id_docente'] ?>)">Editar</button>
                        <a href="../controllers/DocentesController.php?eliminar=<?= $d['id_docente'] ?>"
                            class="btn-eliminar"
                            onclick="return confirm('¿Eliminar este docente?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="modalForm" class="modal">
    <div class="modal-content">
        <span class="cerrar" onclick="cerrarModal()">&times;</span>
        <h2 id="tituloModal">Agregar Docente</h2>
        <form id="formDocente" action="../controllers/DocentesController.php" method="POST">
            <input type="hidden" name="accion" value="agregar" id="accion">
            <input type="hidden" name="id_docente" id="id_docente">

            <label>Nombre</label>
            <input type="text" name="nombre" id="nombre" required>

            <label>Apellidos</label>
            <input type="text" name="apellidos" id="apellidos" required>

            <label>Correo</label>
            <input type="email" name="correo" id="correo" required>

            <label>Teléfono</label>
            <input type="text" name="telefono" id="telefono">

            <?php if($rol === 'admin'): ?>
                <label>Carrera</label>
                <select name="id_carrera" id="id_carrera" required>
                    <option value="">Seleccione una carrera</option>
                    <?php
                    $carreras = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
                    foreach($carreras as $c): ?>
                        <option value="<?= $c['id_carrera'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <button type="submit">Guardar</button>
        </form>
    </div>
</div>

<script>
function abrirModal(id=null){
    const modal = document.getElementById('modalForm');
    modal.style.display = 'block';

    if(id){
        const row = document.querySelector(`tr[data-id='${id}']`);
        document.getElementById('tituloModal').innerText = 'Editar Docente';
        document.getElementById('accion').value = 'editar';
        document.getElementById('id_docente').value = id;
        document.getElementById('nombre').value = row.dataset.nombre;
        document.getElementById('apellidos').value = row.dataset.apellidos;
        document.getElementById('correo').value = row.dataset.correo;
        document.getElementById('telefono').value = row.dataset.telefono;
        <?php if($rol === 'admin'): ?>
            document.getElementById('id_carrera').value = row.dataset.id_carrera;
        <?php endif; ?>
    } else {
        document.getElementById('tituloModal').innerText = 'Agregar Docente';
        document.getElementById('accion').value = 'agregar';
        document.getElementById('id_docente').value = '';
        document.getElementById('nombre').value = '';
        document.getElementById('apellidos').value = '';
        document.getElementById('correo').value = '';
        document.getElementById('telefono').value = '';
        <?php if($rol === 'admin'): ?>
            document.getElementById('id_carrera').value = '';
        <?php endif; ?>
    }
}

function cerrarModal(){
    document.getElementById('modalForm').style.display = 'none';
}

function cerrarAlerta(){
    document.getElementById('alertaMsg').style.display = 'none';
}

window.onclick = function(event){
    const modal = document.getElementById('modalForm');
    if(event.target == modal){
        cerrarModal();
    }
}
</script>

<?php
$content = ob_get_clean();
$title = "Docentes";
$pagina = "docentes";
include "dashboard.php";
?>
