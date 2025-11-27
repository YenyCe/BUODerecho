<?php
require_once "../config/conexion.php";
require_once "../models/CarrerasModel.php";

$model = new CarrerasModel($conn);
$carreras = $model->obtenerCarreras();

$alerta = "";
if(isset($_GET['msg'])){
    if($_GET['msg'] == "success") $alerta = "<div class='alerta success'>Carrera agregada correctamente</div>";
    if($_GET['msg'] == "edited")  $alerta = "<div class='alerta success'>Carrera editada correctamente</div>";
    if($_GET['msg'] == "deleted") $alerta = "<div class='alerta error'>Carrera eliminada correctamente</div>";
}

/* =============================
   INICIAR CONTENIDO
============================= */
ob_start();
?>
<div class="container-form">
    <h2>Carreras</h2>
    <?php echo $alerta; ?>

    <button class="btn-agregar" onclick="abrirModalCarrera()">Agregar Carrera</button>

    <table class="tabla-docentes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Clave</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($carreras as $c): ?>
            <tr data-id="<?php echo $c['id_carrera']; ?>"
                data-nombre="<?php echo $c['nombre']; ?>"
                data-clave="<?php echo $c['clave']; ?>">
                
                <td><?php echo $c['id_carrera']; ?></td>
                <td><?php echo $c['nombre']; ?></td>
                <td><?php echo $c['clave']; ?></td>
                <td>
                    <button class="btn-editar" onclick="abrirModalCarrera(<?php echo $c['id_carrera']; ?>)">Editar</button>
                    <a href="../controllers/CarrerasController.php?eliminar=<?php echo $c['id_carrera']; ?>" 
                       class="btn-eliminar"
                       onclick="return confirm('¿Eliminar esta carrera?')">
                       Eliminar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Carrera -->
<div id="modalCarrera" class="modal">
    <div class="modal-content">
        <span class="cerrar" onclick="cerrarModalCarrera()">&times;</span>
        <h2 id="tituloModalCarrera">Agregar Carrera</h2>

        <form id="formCarrera" action="../controllers/CarrerasController.php" method="POST">
            <input type="hidden" name="accion" value="agregar" id="accionCarrera">
            <input type="hidden" name="id_carrera" id="id_carrera">

            <label>Nombre</label>
            <input type="text" name="nombre" id="nombreCarrera" required>

            <label>Clave</label>
            <input type="text" name="clave" id="claveCarrera" required>

            <button type="submit">Guardar</button>
        </form>
    </div>
</div>

<script>
function abrirModalCarrera(id=null){
    const modal = document.getElementById('modalCarrera');
    modal.style.display = 'block';

    if(id){
        const row = document.querySelector(`tr[data-id='${id}']`);
        document.getElementById('tituloModalCarrera').innerText = 'Editar Carrera';
        document.getElementById('accionCarrera').value = 'editar';
        document.getElementById('id_carrera').value = id;
        document.getElementById('nombreCarrera').value = row.dataset.nombre;
        document.getElementById('claveCarrera').value = row.dataset.clave;
    } else {
        document.getElementById('tituloModalCarrera').innerText = 'Agregar Carrera';
        document.getElementById('accionCarrera').value = 'agregar';
        document.getElementById('id_carrera').value = '';
        document.getElementById('nombreCarrera').value = '';
        document.getElementById('claveCarrera').value = '';
    }
}

function cerrarModalCarrera(){
    document.getElementById('modalCarrera').style.display = 'none';
}

window.onclick = function(event){
    const modal = document.getElementById('modalCarrera');
    if(event.target == modal){
        cerrarModalCarrera();
    }
}
</script>

<?php
// FIN CAPTURA
$content = ob_get_clean();
$title = "Carreras";

include "dashboard.php";
?>
