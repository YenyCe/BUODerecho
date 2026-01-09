<?php
require_once "../middlewares/auth.php"; // Solo usuarios logueados
require_once "../config/conexion.php";
require_once "../models/UsuariosModel.php";
require_once "../models/CarrerasModel.php";

$usuarioModel = new UsuariosModel($conn);
$usuarios = $usuarioModel->getUsuarios();

$carreraModel = new CarrerasModel($conn);
$carreras = $carreraModel->obtenerCarreras();
ob_start();
?>

<div class="container-form">
    <h2>Usuarios</h2>
    <button class="btn-agregar" onclick="abrirModalUsuario()">Agregar Usuario</button>

    <table class="tabla-docentes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Carrera</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr data-id="<?= $u['id_usuario'] ?>"
                    data-nombre="<?= $u['nombre'] ?>"
                    data-correo="<?= $u['correo'] ?>"
                    data-usuario="<?= $u['usuario'] ?>"
                    data-rol="<?= $u['rol'] ?>"
                    data-id_carrera="<?= $u['id_carrera'] ?>"
                    data-estado="<?= $u['estado'] ?>">
                    <td><?= $u['id_usuario'] ?></td>
                    <td><?= $u['nombre'] ?></td>
                    <td><?= $u['correo'] ?></td>
                    <td><?= $u['usuario'] ?></td>
                    <td><?= $u['rol'] ?></td>
                    <td><?= $u['carrera'] ?></td>
                    <td>
                        <button class="btn-editar" onclick="abrirModalUsuario(<?= $u['id_usuario'] ?>)">Editar</button>
                        <a href="../controllers/UsuariosController.php?eliminar=<?= $u['id_usuario'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="modalUsuario" class="modal">
    <div class="modal-content">
        <span class="cerrar" onclick="cerrarModal('modalUsuario')">&times;</span>
        <h2 id="tituloModalUsuario">Agregar Usuario</h2>

        <form id="formUsuario" action="../controllers/UsuariosController.php" method="POST">

            <input type="hidden" name="accion" value="agregar" id="accionUsuario">
            <input type="hidden" name="id_usuario" id="id_usuario">

            <div class="form-grid">

                <div>
                    <label>Nombre</label>
                    <input type="text" name="nombre" id="nombreUsuario" required>
                </div>

                <div>
                    <label>Correo</label>
                    <input type="email" name="correo" id="correoUsuario" required>
                </div>

                <div>
                    <label>Usuario</label>
                    <input type="text" name="usuario" id="usuarioUsuario" required>
                </div>

                <div>
                    <label>Contraseña</label>
                    <input type="password" name="password" id="passwordUsuario">
                </div>

                <div>
                    <label>Rol</label>
                    <select name="rol" id="rolUsuario" required>
                        <option value="">Seleccione un rol</option>
                        <option value="admin">Administrador</option>
                        <option value="coordinador">Coordinador</option>
                    </select>
                </div>

                <div>
                    <label>Carrera</label>
                    <select name="id_carrera" id="id_carreraUsuario">
                        <option value="">Seleccione una carrera</option>
                        <?php foreach ($carreras as $c): ?>
                            <option value="<?= $c['id_carrera'] ?>">
                                <?= $c['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="full-row">
                    <label>Estado</label>
                    <select name="estado" id="estadoUsuario">
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>

                <div class="full-row">
                    <button type="submit">Guardar</button>
                </div>

            </div>
        </form>
    </div>
</div>


<script>
    function abrirModalUsuario(id = null) {
        const modal = document.getElementById('modalUsuario');
        modal.style.display = 'block';

        if (id) {
            const row = document.querySelector(`tr[data-id='${id}']`);
            document.getElementById('tituloModalUsuario').innerText = 'Editar Usuario';
            document.getElementById('accionUsuario').value = 'editar';
            document.getElementById('id_usuario').value = id;
            document.getElementById('nombreUsuario').value = row.dataset.nombre;
            document.getElementById('correoUsuario').value = row.dataset.correo;
            document.getElementById('usuarioUsuario').value = row.dataset.usuario;
            document.getElementById('rolUsuario').value = row.dataset.rol;
            document.getElementById('id_carreraUsuario').value = row.dataset.id_carrera;
            document.getElementById('estadoUsuario').value = row.dataset.estado;
            document.getElementById('passwordUsuario').required = false;
        } else {
            document.getElementById('tituloModalUsuario').innerText = 'Agregar Usuario';
            document.getElementById('accionUsuario').value = 'agregar';
            document.getElementById('id_usuario').value = '';
            document.getElementById('nombreUsuario').value = '';
            document.getElementById('correoUsuario').value = '';
            document.getElementById('usuarioUsuario').value = '';
            document.getElementById('rolUsuario').value = '';
            document.getElementById('id_carreraUsuario').value = '';
            document.getElementById('estadoUsuario').value = '1';
            document.getElementById('passwordUsuario').value = '';
            document.getElementById('passwordUsuario').required = true;
        }
    }
</script>
<?php
$content = ob_get_clean();
$title = "Usuarios";
$pagina = "usuarios";
include "dashboard.php";
?>