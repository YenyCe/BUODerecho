<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/SemestresModel.php";
require_once "../models/CarrerasModel.php";

$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

$semModel = new SemestresModel($conn);
$carrerasModel = new CarrerasModel($conn);

// Obtener semestres y grupos
$semestres = $semModel->getSemestres();
$grupos = $semModel->getGrupos($id_carrera); // Filtra por carrera si es coordinador

// Solo administrador necesita todas las carreras para el select
$carreras = ($rol === 'admin') ? $carrerasModel->obtenerCarreras() : [];
// INICIAR CAPTURA  
ob_start();
?>

<div class="container-form">
    <div class="tablas-pequenas">
        <!-- SEMESTRES -->
        <h2>Semestres</h2>
        <button class="btn-agregar" onclick="abrirModalSemestre()">Agregar Semestre</button>
        <table id="tablaSemestres" class="tabla-docentes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Número</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($semestres)): ?>
                    <tr>
                        <td colspan="3" style="text-align:center;">No hay semestres registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($semestres as $s): ?>
                        <tr data-id="<?= $s['id_semestre'] ?>" data-numero="<?= $s['numero'] ?>">
                            <td><?= $s['id_semestre'] ?></td>
                            <td><?= htmlspecialchars($s['numero'], ENT_QUOTES, 'UTF-8') ?></td>

                            <td>
                                <button class="btn-editar" onclick="abrirModalSemestre(<?= $s['id_semestre'] ?>)">Editar</button>
                                <a href="../controllers/SemestresController.php?eliminar_semestre=<?= $s['id_semestre'] ?>" class="btn-eliminar">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <br>

        <!-- GRUPOS -->
        <h2>Grupos</h2>
        <?php if ($rol === 'admin'): ?>
            <div class="filtros-container">
                <label>Carrera:</label>
                <select id="filtroCarrera" class="form-control">
                    <option value="">Todas</option>
                    <?php foreach ($carreras as $c): ?>
                        <option value="<?= (int)$c['id_carrera'] ?>">
                            <?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        </option>

                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <button class="btn-agregar" onclick="abrirModalGrupo()">Agregar Grupo</button>

        <table id="tablaGrupos" class="tabla-docentes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Semestre</th>
                    <?php if ($rol === 'admin'): ?><th>Carrera</th><?php endif; ?>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($grupos)): ?>
                    <tr>
                        <td colspan="<?= ($rol === 'admin') ? 5 : 4 ?>" style="text-align:center;">
                            No hay grupos registrados
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($grupos as $g): ?>
                        <tr
                            data-id="<?= $g['id_grupo'] ?>"
                            data-nombre="<?= htmlspecialchars($g['nombre']) ?>"
                            data-id_semestre="<?= $g['id_semestre'] ?>"
                            data-id_carrera="<?= $g['id_carrera'] ?>">
                            <td><?= $g['id_grupo'] ?></td>
                            <td><?= htmlspecialchars($g['nombre']) ?></td>
                            <td><?= $g['semestre_num'] ?></td>
                            <?php if ($rol === 'admin'): ?>
                                <td><?= htmlspecialchars($g['nombre_carrera']) ?></td>
                            <?php endif; ?>
                            <td>
                                <button class="btn-editar" onclick="abrirModalGrupo(<?= $g['id_grupo'] ?>)">Editar</button>
                                <a href="../controllers/SemestresController.php?eliminar_grupo=<?= $g['id_grupo'] ?>" class="btn-eliminar">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

        </table>
    </div>
</div>

<!-- Modal Semestre -->
<div id="modalSemestre" class="modal">
    <div class="modal-content">
        <span class="cerrar" onclick="cerrarModal('modalSemestre')">&times;</span>
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
        <span class="cerrar" onclick="cerrarModal('modalGrupo')">&times;</span>
        <h2 id="tituloModalGrupo">Agregar Grupo</h2>
        <form action="../controllers/SemestresController.php" method="POST">
            <input type="hidden" name="accion_grupo" value="agregar" id="accion_grupo">
            <input type="hidden" name="id_grupo" id="id_grupo">
            <label>Nombre</label>
            <input type="text" name="nombre" id="nombre_grupo" required>
            <label>Semestre</label>
            <select name="id_semestre" id="id_semestre_grupo" required>
                <option value="">Seleccione un semestre</option>
                <?php foreach ($semestres as $s): ?>
                    <option value="<?= $s['id_semestre'] ?>"><?= $s['numero'] ?></option>
                <?php endforeach; ?>
            </select>

            <?php if ($rol === 'admin'): ?>
                <label>Carrera</label>
                <select name="id_carrera" id="id_carrera_grupo" required>
                    <option value="">Seleccione una carrera</option>
                    <?php foreach ($carreras as $c): ?>
                        <option value="<?= $c['id_carrera'] ?>"><?= $c['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            <?php elseif ($rol === 'coordinador'): ?>
                <input type="hidden" name="id_carrera" value="<?= $_SESSION['id_carrera'] ?>">
            <?php endif; ?>

            <button type="submit">Guardar</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {

        /* --- MODAL SEMESTRE --- */
        window.abrirModalSemestre = function(id = null) {
            const modal = document.getElementById('modalSemestre');
            modal.style.display = 'block';
            const titulo = document.getElementById('tituloModalSemestre');
            const accion = document.getElementById('accion_semestre');
            const id_input = document.getElementById('id_semestre');
            const numero_input = document.getElementById('numero_semestre');

            if (id) {
                const row = document.querySelector(`#tablaSemestres tbody tr[data-id='${id}']`);
                if (!row) return;
                titulo.innerText = 'Editar Semestre';
                accion.value = 'editar';
                id_input.value = id;
                numero_input.value = row.dataset.numero;
            } else {
                titulo.innerText = 'Agregar Semestre';
                accion.value = 'agregar';
                id_input.value = '';
                numero_input.value = '';
            }
        }

        /* --- MODAL GRUPO --- */
        window.abrirModalGrupo = function(id = null) {
            const modal = document.getElementById('modalGrupo');
            modal.style.display = 'block';
            const titulo = document.getElementById('tituloModalGrupo');
            const accion = document.getElementById('accion_grupo');
            const id_input = document.getElementById('id_grupo');
            const nombre_input = document.getElementById('nombre_grupo');
            const semestre_input = document.getElementById('id_semestre_grupo');
            const carrera_input = document.getElementById('id_carrera_grupo');

            if (id) {
                const rows = document.querySelectorAll("#tablaGrupos tbody tr");
                let row = null;
                rows.forEach(r => {
                    if (r.dataset.id == id) row = r;
                });
                if (!row) return;
                titulo.innerText = 'Editar Grupo';
                accion.value = 'editar';
                id_input.value = id;
                nombre_input.value = row.dataset.nombre;
                semestre_input.value = row.dataset.id_semestre;
                if (carrera_input) carrera_input.value = row.dataset.id_carrera || '';
            } else {
                titulo.innerText = 'Agregar Grupo';
                accion.value = 'agregar';
                id_input.value = '';
                nombre_input.value = '';
                semestre_input.value = '';
                if (carrera_input) carrera_input.value = '';
            }
        }

        /* --- FILTRO DE CARRERA --- */
        const filtroCarrera = document.getElementById("filtroCarrera");
        if (filtroCarrera) {
            const filas = document.querySelectorAll("#tablaGrupos tbody tr");
            filtroCarrera.addEventListener("change", () => {
                let carrera = filtroCarrera.value;
                filas.forEach(fila => {
                    fila.style.display = (carrera === "" || fila.dataset.id_carrera === carrera) ? "" : "none";
                });
            });
        }

        /* --- VALIDACIÓN FORMULARIOS --- */
        const formSemestre = document.querySelector("#modalSemestre form");
        if (formSemestre) {
            formSemestre.addEventListener("submit", e => {
                const num = parseInt(document.getElementById("numero_semestre").value);
                if (isNaN(num) || num < 1) {
                    alert("Número de semestre inválido");
                    e.preventDefault();
                }
            });
        }

        const formGrupo = document.querySelector("#modalGrupo form");
        if (formGrupo) {
            formGrupo.addEventListener("submit", e => {
                const nombre = document.getElementById("nombre_grupo").value.trim();
                const semestre = document.getElementById("id_semestre_grupo").value;
                if (nombre === "" || semestre === "") {
                    alert("Debe completar todos los campos del grupo");
                    e.preventDefault();
                }
            });
        }
    });
</script>
<?php
$content = ob_get_clean();
$title = "Semestres y Grupos";
$pagina = "semestres_grupos";
include "dashboard.php";
?>