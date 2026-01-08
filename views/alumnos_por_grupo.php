<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/AlumnosModel.php";

$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

if (!isset($_GET['id_grupo'])) {
    header("Location: grupos.php");
    exit();
}

$id_grupo = intval($_GET['id_grupo']);
$alumnoModel = new AlumnosModel($conn);

// Info del grupo
$grupoInfo = $alumnoModel->getGrupo($id_grupo);
if (!$grupoInfo) {
    echo "Grupo no encontrado";
    exit();
}

// Alumnos del grupo
$alumnos = $alumnoModel->getAlumnos($id_carrera);
$alumnosGrupo = array_filter($alumnos, fn($a) => intval($a['id_grupo']) === $id_grupo);

// Lista de grupos para el select (editar/agregar)
$grupos = ($rol === 'admin') ? $alumnoModel->getGrupos() : $alumnoModel->getGruposPorCarrera($id_carrera);

ob_start();
?>

<div class="container-form">
<h2>
   Alumnos del Semestre:   <?= htmlspecialchars($grupoInfo['semestre']); ?>  -
 Grupo: <?= htmlspecialchars($grupoInfo['grupo']); ?>
  
</h2>
    <a href=" grupos.php" class="btn-agregar">← Volver a Grupos</a>
    <button class="btn-agregar" onclick="abrirModalAlumno()">Agregar Alumno</button>

    <table class="tabla-docentes">
        <thead>
            <tr>
                <th style="width:60px;">N°</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php $num = 1; ?>
            <?php foreach ($alumnosGrupo as $a): ?>
                <tr data-id="<?= $a['id_alumno']; ?>" data-nombre="<?= htmlspecialchars($a['nombre']); ?>" data-id_grupo="<?= $a['id_grupo']; ?>">
                    <td style="text-align:center;"><?= $num++; ?></td>
                    <td><?= htmlspecialchars($a['nombre']); ?></td>
                    <td>
                        <button class="btn-editar" onclick="abrirModalAlumno(<?= $a['id_alumno']; ?>)">Editar</button>
                        <form action="../controllers/AlumnosController.php" method="POST" style="display:inline;">
                            <button type="submit" class="btn-eliminar" onclick="return confirm('¿Dar de baja a este alumno?')">
                                Dar de baja
                            </button>
                            <input type="hidden" name="accion" value="baja">
                            <input type="hidden" name="id_alumno" value="<?= $a['id_alumno'] ?>">
                            <input type="hidden" name="id_grupo_origen" value="<?= $id_grupo ?>">

                            <input type="text" name="motivo" placeholder="Motivo de baja (opcional)">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="modalAlumno" class="modal">
    <div class="modal-content">
        <span class="cerrar" onclick="cerrarModal('modalAlumno')">&times;</span>
        <h2 id="tituloModalAlumno">Agregar Alumno</h2>

        <form id="formAlumno" action="../controllers/AlumnosController.php" method="POST">
            <input type="hidden" name="accion" value="agregar" id="accionAlumno">
            <input type="hidden" name="id_alumno" id="id_alumno">
            <input type="hidden" name="id_grupo_origen" value="<?= $id_grupo; ?>">

            <label>Nombre</label>
            <input type="text" name="nombre" id="nombreAlumno" required>

            <label>Grupo</label>
            <select name="id_grupo" id="id_grupoAlumno" required>
                <option value="">Seleccione un grupo</option>
                <?php foreach ($grupos as $g): ?>
                    <option value="<?= $g['id_grupo']; ?>" <?= ($g['id_grupo'] == $id_grupo) ? "selected" : "" ?>>
                        <?= htmlspecialchars($g['nombre_grupo']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Guardar</button>
        </form>
    </div>
</div>

<script src="/js/modales.js"></script>
<script>
    function abrirModalAlumno(id = null) {
        const modal = document.getElementById('modalAlumno');
        modal.style.display = 'block';

        if (id) {
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
            document.getElementById('id_grupoAlumno').value = '<?= $id_grupo; ?>';
        }   
    }
</script>

<?php
$content = ob_get_clean();
$title = "Alumnos del Grupo";
$pagina = "alumnos";
include "dashboard.php";
?>