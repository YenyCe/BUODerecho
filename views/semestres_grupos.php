<?php
require_once "../config/conexion.php";
require_once "../models/SemestresModel.php";

$model = new SemestresModel($conn);
$semestres = $model->getSemestres();
$grupos = $model->getGrupos();

$alerta = "";
if(isset($_GET['msg'])){
    $alerta = "<div class='alerta success'>Acción realizada correctamente</div>";
}
// INICIAR CAPTURA  
ob_start();
?>

<div class="container-form">

    <?php echo $alerta; ?>

    <div class="tablas-pequenas">
        <!-- SEMESTRES -->
               <h2>Semestres</h2>
        <button class="btn-agregar" onclick="abrirModalSemestre()">Agregar Semestre</button>
        <table class="tabla-docentes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Número</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($semestres as $s): ?>
                    <tr data-id="<?php echo $s['id_semestre']; ?>" data-numero="<?php echo $s['numero']; ?>">
                        <td><?php echo $s['id_semestre']; ?></td>
                        <td><?php echo $s['numero']; ?></td>
                        <td>
                            <button class="btn-editar" onclick="abrirModalSemestre(<?php echo $s['id_semestre']; ?>)">Editar</button>
                            <a href="../controllers/SemestresController.php?eliminar_semestre=<?php echo $s['id_semestre']; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este semestre?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- GRUPOS -->
         <h2>Grupos</h2>
        <button class="btn-agregar" onclick="abrirModalGrupo()">Agregar Grupo</button>
        <table class="tabla-docentes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Semestre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($grupos as $g): ?>
                    <tr 
                        data-id="<?php echo isset($g['id_grupo']) ? $g['id_grupo'] : ''; ?>" 
                        data-nombre="<?php echo isset($g['nombre']) ? $g['nombre'] : ''; ?>" 
                        data-id_semestre="<?php echo isset($g['id_semestre']) ? $g['id_semestre'] : ''; ?>"
                    >
                        <td><?php echo $g['id_grupo']; ?></td>
                        <td><?php echo $g['nombre']; ?></td>
                        <td><?php echo $g['semestre_num']; ?></td>
                        <td>
                            <button class="btn-editar" onclick="abrirModalGrupo(<?php echo $g['id_grupo']; ?>)">Editar</button>
                            <a href="../controllers/SemestresController.php?eliminar_grupo=<?php echo $g['id_grupo']; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este grupo?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- Modal Semestre -->
<div id="modalSemestre" class="modal">
    <div class="modal-content">
        <span class="cerrar" onclick="cerrarModalSemestre()">&times;</span>
        <h2 id="tituloModalSemestre">Agregar Semestre</h2>
        <form action="../controllers/SemestresController.php" method="POST">
            <input type="hidden" name="accion_semestre" value="agregar" id="accion_semestre">
            <input type="hidden" name="id_semestre" id="id_semestre">
            <label>Número</label>
            <input type="number" name="numero" id="numero_semestre" required>
            <button type="submit">Guardar</button>
        </form>
    </div>
</div>

<!-- Modal Grupo -->
<div id="modalGrupo" class="modal">
    <div class="modal-content">
        <span class="cerrar" onclick="cerrarModalGrupo()">&times;</span>
        <h2 id="tituloModalGrupo">Agregar Grupo</h2>
        <form action="../controllers/SemestresController.php" method="POST">
            <input type="hidden" name="accion_grupo" value="agregar" id="accion_grupo">
            <input type="hidden" name="id_grupo" id="id_grupo">
            <label>Nombre</label>
            <input type="text" name="nombre" id="nombre_grupo" required>
            <label>Semestre</label>
            <select name="id_semestre" id="id_semestre_grupo" required>
                <option value="">Seleccione un semestre</option>
                <?php foreach($semestres as $s): ?>
                    <option value="<?php echo $s['id_semestre']; ?>"><?php echo $s['numero']; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Guardar</button>
        </form>
    </div>
</div>

<script>
/* Modal Semestre */
function abrirModalSemestre(id=null){
    const modal = document.getElementById('modalSemestre');
    modal.style.display = 'block';
    if(id){
        const row = document.querySelector(`tr[data-id='${id}']`);
        document.getElementById('tituloModalSemestre').innerText = 'Editar Semestre';
        document.getElementById('accion_semestre').value = 'editar';
        document.getElementById('id_semestre').value = id;
        document.getElementById('numero_semestre').value = row.dataset.numero;
    } else {
        document.getElementById('tituloModalSemestre').innerText = 'Agregar Semestre';
        document.getElementById('accion_semestre').value = 'agregar';
        document.getElementById('id_semestre').value = '';
        document.getElementById('numero_semestre').value = '';
    }
}
function cerrarModalSemestre(){ document.getElementById('modalSemestre').style.display='none'; }

/* Modal Grupo */
function abrirModalGrupo(id=null){
    const modal = document.getElementById('modalGrupo');
    modal.style.display = 'block';
    if(id){
        const row = document.querySelector(`tr[data-id='${id}']`);
        document.getElementById('tituloModalGrupo').innerText = 'Editar Grupo';
        document.getElementById('accion_grupo').value = 'editar';
        document.getElementById('id_grupo').value = id;
        document.getElementById('nombre_grupo').value = row.dataset.nombre;
        document.getElementById('id_semestre_grupo').value = row.dataset.id_semestre;
    } else {
        document.getElementById('tituloModalGrupo').innerText = 'Agregar Grupo';
        document.getElementById('accion_grupo').value = 'agregar';
        document.getElementById('id_grupo').value = '';
        document.getElementById('nombre_grupo').value = '';
        document.getElementById('id_semestre_grupo').value = '';
    }
}
function cerrarModalGrupo(){ document.getElementById('modalGrupo').style.display='none'; }

window.onclick = function(event){
    const modalS = document.getElementById('modalSemestre');
    const modalG = document.getElementById('modalGrupo');
    if(event.target == modalS) cerrarModalSemestre();
    if(event.target == modalG) cerrarModalGrupo();
}
</script>


<?php
// FIN de la captura
$content = ob_get_clean();
$title = "Semestres y Grupos ";

// Cargar layout
include "dashboard.php";
?>
