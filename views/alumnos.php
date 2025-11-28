<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/AlumnosModel.php";
$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

$alumnoModel = new AlumnosModel($conn);

// ✅ Aquí obtenemos los alumnos filtrando por carrera si es coordinador
$alumnos = $alumnoModel->getAlumnos($id_carrera);
$grupos = $alumnoModel->getGrupos();


$alerta = "";
if(isset($_GET['msg'])){
    if($_GET['msg'] == "success") $alerta = "<div class='alerta success'>Alumno agregado correctamente</div>";
    if($_GET['msg'] == "edited") $alerta = "<div class='alerta success'>Alumno editado correctamente</div>";
    if($_GET['msg'] == "deleted") $alerta = "<div class='alerta error'>Alumno eliminado correctamente</div>";
}
// INICIAR CAPTURA  
    ob_start();
    ?>
<div class="container-form">
           <h2>Alumnos</h2>
    <?php echo $alerta; ?>

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
        <?php foreach($alumnos as $a): ?>
            <tr data-id="<?php echo $a['id_alumno']; ?>"
                data-nombre="<?php echo $a['nombre']; ?>"
                data-id_grupo="<?php echo array_search($a['grupo'], array_column($grupos,'nombre'))+1; ?>"
                data-grupo="<?php echo $a['grupo']; ?>">
                <td><?php echo $a['id_alumno']; ?></td>
                <td><?php echo $a['nombre']; ?></td>
                <td><?php echo $a['grupo']; ?></td>
                <td>
                    <button class="btn-editar" onclick="abrirModalAlumno(<?php echo $a['id_alumno']; ?>)">Editar</button>
                    <a href="../controllers/AlumnosController.php?eliminar=<?php echo $a['id_alumno']; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este alumno?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Alumno -->
<div id="modalAlumno" class="modal">
    <div class="modal-content">
        <span class="cerrar" onclick="cerrarModalAlumno()">&times;</span>
        <h2 id="tituloModalAlumno">Agregar Alumno</h2>
        <form id="formAlumno" action="../controllers/AlumnosController.php" method="POST">
            <input type="hidden" name="accion" value="agregar" id="accionAlumno">
            <input type="hidden" name="id_alumno" id="id_alumno">
            <label>Nombre</label>
            <input type="text" name="nombre" id="nombreAlumno" required>
            <label>Grupo</label>
            <select name="id_grupo" id="id_grupoAlumno" required>
                <option value="">Seleccione un grupo</option>
                <?php foreach($grupos as $g): ?>
                    <option value="<?php echo $g['id_grupo']; ?>"><?php echo $g['nombre']; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Guardar</button>
        </form>
    </div>
</div>

<script>
function abrirModalAlumno(id=null){
    const modal = document.getElementById('modalAlumno');
    modal.style.display = 'block';

    if(id){
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

function cerrarModalAlumno(){
    document.getElementById('modalAlumno').style.display = 'none';
}

window.onclick = function(event){
    const modal = document.getElementById('modalAlumno');
    if(event.target == modal){
        cerrarModalAlumno();
    }
}
</script>

<?php
// FIN de la captura
$content = ob_get_clean();
$title = "Alumnos";
$pagina = "alumnos";
// Cargar layout
include "dashboard.php";
?>
