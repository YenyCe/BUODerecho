<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/DocentesModel.php";

$rol = $_SESSION['rol'];
$id_carrera = ($rol === 'coordinador') ? $_SESSION['id_carrera'] : null;

$docenteModel = new DocentesModel($conn);
$docentes = $docenteModel->getDocentes($id_carrera);


// INICIAR CAPTURA  
ob_start();
?>

<div class="container-form">
    <h2>Docentes</h2>   

    <?php if ($rol === 'admin'): ?>
        <div class="filtros-container">
            <div>
                <label>Filtrar por carrera:</label>

                <select id="filtroCarrera" onchange="filtrarCarrera()">
                    <option value="">Todas</option>
                    <?php
                    $carreras = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
                    foreach ($carreras as $c): ?>
                        <option value="<?= $c['id_carrera'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
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
                <th>Genero</th>
                <?php if ($rol === 'admin'): ?>
                    <th>Carrera</th>
                <?php endif; ?>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($docentes)): ?>
                <tr>
                    <td colspan="<?= ($rol === 'admin') ? 6 : 5 ?>" style="text-align:center; padding:20px;">
                        No hay docentes registrados.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($docentes as $d): ?>
                    <tr data-id="<?= $d['id_docente'] ?>"
                        data-nombre="<?= htmlspecialchars($d['nombre']) ?>"
                        data-apellidos="<?= htmlspecialchars($d['apellidos']) ?>"
                        data-correo="<?= htmlspecialchars($d['correo']) ?>"
                        data-telefono="<?= htmlspecialchars($d['telefono']) ?>"
                        data-genero="<?= $d['genero'] ?>"
                        data-id_carrera="<?= $d['id_carrera'] ?? '' ?>">
                        <td><?= $d['id_docente'] ?></td>
                        <td><?= htmlspecialchars($d['nombre'] . ' ' . $d['apellidos']) ?></td>
                        <td><?= htmlspecialchars($d['correo']) ?></td>
                        <td><?= htmlspecialchars($d['telefono']) ?></td>
                        <td>
                            <?= ($d['genero'] === 'M') ? 'Mujer' : 'Hombre' ?>
                        </td>

                        <?php if ($rol === 'admin'): ?>
                            <td>
                                <?php
                                if ($d['id_carrera']) {
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
                            <!-- GENERAR CARGA ACADÉMICA -->
                            <a class="btn-carga"
                                href="../views/impresion_carga_academica.php?id_docente=<?= $d['id_docente'] ?>" target="_blank">
                                Carga Académica
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Docente -->
<div id="modalDocente" class="modal">
    <div class="modal-content">
        <span class="cerrar" onclick="cerrarModal('modalDocente')">&times;</span>
        <h2 id="tituloModal">Agregar Docente</h2>

        <form id="formDocente" action="../controllers/DocentesController.php" method="POST">

            <input type="hidden" name="accion" value="agregar" id="accion">
            <input type="hidden" name="id_docente" id="id_docente">

            <div class="form-grid">

                <!-- Nombre -->
                <div>
                    <label>Nombre</label>
                    <input type="text" name="nombre" id="nombre" required>
                </div>

                <!-- Apellidos -->
                <div>
                    <label>Apellidos</label>
                    <input type="text" name="apellidos" id="apellidos" required>
                </div>

                <!-- Correo -->
                <div>
                    <label>Correo</label>
                    <input type="email" name="correo" id="correo" required>
                </div>

                <!-- Teléfono -->
                <div>
                    <label>Teléfono</label>
                    <input type="text" name="telefono" id="telefono">
                </div>
                <div>
                    <label>Género</label>
                    <select name="genero" id="genero" required>
                        <option value="">Seleccione</option>
                        <option value="H">Hombre</option>
                        <option value="M">Mujer</option>
                    </select>
                </div>


                <!-- Carrera (solo admin) -->
                <?php if ($rol === 'admin'): ?>
                    <div class="full-row">
                        <label>Carrera</label>
                        <select name="id_carrera" id="id_carrera" required>
                            <option value="">Seleccione una carrera</option>
                            <?php
                            $carreras = $conn->query("SELECT id_carrera, nombre FROM carreras ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
                            foreach ($carreras as $c): ?>
                                <option value="<?= $c['id_carrera'] ?>">
                                    <?= htmlspecialchars($c['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- Botón -->
                <div class="full-row">
                    <button type="submit">Guardar</button>
                </div>

            </div>
        </form>
    </div>
</div>


<script>
    function abrirModal(id = null) {
        const modal = document.getElementById('modalDocente');
        modal.style.display = 'block';

        if (id) {
            const row = document.querySelector(`tr[data-id='${id}']`);
            document.getElementById('tituloModal').innerText = 'Editar Docente';
            document.getElementById('accion').value = 'editar';
            document.getElementById('id_docente').value = id;
            document.getElementById('nombre').value = row.dataset.nombre;
            document.getElementById('apellidos').value = row.dataset.apellidos;
            document.getElementById('correo').value = row.dataset.correo;
            document.getElementById('telefono').value = row.dataset.telefono;
            document.getElementById('genero').value = row.dataset.genero;
            <?php if ($rol === 'admin'): ?>
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
            document.getElementById('genero').value = '';
            <?php if ($rol === 'admin'): ?>
                document.getElementById('id_carrera').value = '';
            <?php endif; ?>
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
<?php
$content = ob_get_clean();
$title = "Docentes";
$pagina = "docentes";
include "dashboard.php";
?>