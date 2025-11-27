<?php
require_once "../config/conexion.php";
require_once "../models/ParcialesModel.php";

$model = new ParcialesModel($conn);
$parciales = $model->getParciales();

$alerta = "";
if(isset($_GET['msg'])){
    $alerta = "<div class='alerta success'>Acción realizada correctamente</div>";
}
// INICIAR CAPTURA  
ob_start();
?>


<div class="container-form">
    <?php echo $alerta; ?>

    <h2>Parciales</h2>
    <button class="btn-agregar" onclick="abrirModal()">Agregar Parcial</button>

    <table class="tabla-docentes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Número</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($parciales as $p): ?>
                <tr data-id="<?php echo $p['id_parcial']; ?>" data-numero="<?php echo $p['numero_parcial']; ?>" data-inicio="<?php echo $p['fecha_inicio']; ?>" data-fin="<?php echo $p['fecha_fin']; ?>">
                    <td><?php echo $p['id_parcial']; ?></td>
                    <td><?php echo $p['numero_parcial']; ?></td>
                    <td><?php echo $p['fecha_inicio']; ?></td>
                    <td><?php echo $p['fecha_fin']; ?></td>
                    <td>
                        <button class="btn-editar" onclick="abrirModal(<?php echo $p['id_parcial']; ?>)">Editar</button>
                        <a href="../controllers/ParcialesController.php?eliminar=<?php echo $p['id_parcial']; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este parcial?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="cerrar" onclick="cerrarModal()">&times;</span>
        <h2 id="tituloModal">Agregar Parcial</h2>
        <form action="../controllers/ParcialesController.php" method="POST">
            <input type="hidden" name="accion" id="accion" value="agregar">
            <input type="hidden" name="id_parcial" id="id_parcial">
            
            <label>Número de Parcial</label>
            <input type="number" name="numero_parcial" id="numero_parcial" required>
            
            <label>Fecha Inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" required>
            
            <label>Fecha Fin</label>
            <input type="date" name="fecha_fin" id="fecha_fin" required>
            
            <button type="submit">Guardar</button>
        </form>
    </div>
</div>

<script>
function abrirModal(id=null){
    const modal = document.getElementById('modal');
    modal.style.display = 'block';

    if(id){
        const row = document.querySelector(`tr[data-id='${id}']`);
        document.getElementById('tituloModal').innerText = 'Editar Parcial';
        document.getElementById('accion').value = 'editar';
        document.getElementById('id_parcial').value = id;
        document.getElementById('numero_parcial').value = row.dataset.numero;
        document.getElementById('fecha_inicio').value = row.dataset.inicio;
        document.getElementById('fecha_fin').value = row.dataset.fin;
    } else {
        document.getElementById('tituloModal').innerText = 'Agregar Parcial';
        document.getElementById('accion').value = 'agregar';
        document.getElementById('id_parcial').value = '';
        document.getElementById('numero_parcial').value = '';
        document.getElementById('fecha_inicio').value = '';
        document.getElementById('fecha_fin').value = '';
    }
}

function cerrarModal(){
    document.getElementById('modal').style.display = 'none';
}

window.onclick = function(event){
    const modal = document.getElementById('modal');
    if(event.target == modal) cerrarModal();
}
</script>

<?php
// FIN de la captura
$content = ob_get_clean();
$title = "Parciales ";

// Cargar layout
include "dashboard.php";
?>
