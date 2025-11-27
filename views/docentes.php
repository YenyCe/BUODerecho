<?php
require_once "../config/conexion.php";
require_once "../models/DocentesModel.php";

$docenteModel = new DocentesModel($conn);
$docentes = $docenteModel->getDocentes();

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
                <th>Apellidos</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($docentes as $d): ?>
                <tr>
                    <td><?= $d['id_docente'] ?></td>
                    <td><?= $d['nombre'] ?></td>
                    <td><?= $d['apellidos'] ?></td>
                    <td><?= $d['correo'] ?></td>
                    <td><?= $d['telefono'] ?></td>
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

            <button type="submit">Guardar</button>
        </form>
    </div>
</div>

<script src="../js/script.js"></script>

<?php
// FIN de la captura
$content = ob_get_clean();
$title = "Docentes";

// Cargar layout
include "dashboard.php";
?>