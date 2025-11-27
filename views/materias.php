<?php
require_once "../config/conexion.php";
require_once "../models/MateriasModel.php";
$materiaModel = new MateriasModel($conn);
$materias = $materiaModel->getMaterias();

$alerta = "";
if(isset($_GET['msg'])){
    if($_GET['msg'] == "success") $alerta = "<div class='alerta success'>Materia agregada correctamente</div>";
    if($_GET['msg'] == "edited") $alerta = "<div class='alerta success'>Materia editada correctamente</div>";
    if($_GET['msg'] == "deleted") $alerta = "<div class='alerta error'>Materia eliminada correctamente</div>";
}
ob_start();
?>

<div class="container-form">
        <h2>Materias</h2>
    <?php echo $alerta; ?>

    <button class="btn-agregar" onclick="abrirModalMateria()">Agregar Materia</button>

    <table class="tabla-docentes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Clave</th>
                <th>Horas/Semana</th>
                <th>Horas/Semestre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($materias as $m): ?>
            <tr data-id="<?php echo $m['id_materia']; ?>"
                data-nombre="<?php echo $m['nombre']; ?>"
                data-clave="<?php echo $m['clave']; ?>"
                data-horas_semana="<?php echo $m['horas_semana']; ?>"
                data-horas_semestre="<?php echo $m['horas_semestre']; ?>">
                <td><?php echo $m['id_materia']; ?></td>
                <td><?php echo $m['nombre']; ?></td>
                <td><?php echo $m['clave']; ?></td>
                <td><?php echo $m['horas_semana']; ?></td>
                <td><?php echo $m['horas_semestre']; ?></td>
                <td>
                    <button class="btn-editar" onclick="abrirModalMateria(<?php echo $m['id_materia']; ?>)">Editar</button>
                    <a href="../controllers/MateriasController.php?eliminar=<?php echo $m['id_materia']; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar esta materia?')">Eliminar</a>
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
            <button type="submit">Guardar</button>
        </form>
    </div>
</div>

<script>
function abrirModalMateria(id=null){
    const modal = document.getElementById('modalMateria');
    modal.style.display = 'block';

    if(id){
        const row = document.querySelector(`tr[data-id='${id}']`);
        document.getElementById('tituloModalMateria').innerText = 'Editar Materia';
        document.getElementById('accionMateria').value = 'editar';
        document.getElementById('id_materia').value = id;
        document.getElementById('nombreMateria').value = row.dataset.nombre;
        document.getElementById('claveMateria').value = row.dataset.clave;
        document.getElementById('horasSemanaMateria').value = row.dataset.horas_semana;
        document.getElementById('horasSemestreMateria').value = row.dataset.horas_semestre;
    } else {
        document.getElementById('tituloModalMateria').innerText = 'Agregar Materia';
        document.getElementById('accionMateria').value = 'agregar';
        document.getElementById('id_materia').value = '';
        document.getElementById('nombreMateria').value = '';
        document.getElementById('claveMateria').value = '';
        document.getElementById('horasSemanaMateria').value = '';
        document.getElementById('horasSemestreMateria').value = '';
    }
}

function cerrarModalMateria(){
    document.getElementById('modalMateria').style.display = 'none';
}

window.onclick = function(event){
    const modal = document.getElementById('modalMateria');
    if(event.target == modal){
        cerrarModalMateria();
    }
}
</script>

<?php
// FIN de la captura
$content = ob_get_clean();
$title = "Materias";

// Cargar layout
include "dashboard.php";
?>
