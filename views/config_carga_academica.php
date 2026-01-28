<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";
require_once "../models/CargaAcademicaModel.php";

$model = new CargaAcademicaModel($conn);
$id_carrera = $_SESSION['id_carrera'];

$config = $model->getConfigByCarrera($id_carrera);
ob_start();
?>

<div class="container-form">
    <h2>Configuración de Carga Académica</h2>
    <form method="POST" action="../controllers/CargaAcademicaController.php">

        <input type="hidden" name="accion" value="<?= $config ? 'editar' : 'guardar' ?>">
        <input type="hidden" name="id_carga" value="<?= $config['id_carga'] ?? '' ?>">

        <div class="form-grid form-elegante">

            <div>
                <label>Clave de Oficio</label>
                <input type="text" name="clave_oficio"
                    value="<?= $config['clave_oficio'] ?? '' ?>"
                    placeholder="BUO/CEL/LD/810">
            </div>

            <div>
                <label>Ciclo Escolar</label>
                <input type="text" name="ciclo_escolar"
                    value="<?= $config['ciclo_escolar'] ?? '' ?>"
                    placeholder="2024–2025">
            </div>

            <div class="full-row">
                <label>Texto de Presentación</label>
                <textarea name="texto_presentacion" rows="4"
                    placeholder="Texto institucional inicial..."><?= $config['texto_presentacion'] ?? '' ?></textarea>
            </div>

            <div class="full-row">
                <label>Texto Claustro</label>
                <textarea name="claustro_texto" rows="4"
                    placeholder="Texto de bienvenida al claustro..."><?= $config['claustro_texto'] ?? '' ?></textarea>
            </div>

            <div class="full-row">
                <label>Texto de Cierre</label>
                <textarea name="texto_pie" rows="3"
                    placeholder="Texto final del documento..."><?= $config['texto_pie'] ?? '' ?></textarea>
            </div>

            <div>
                <label>Nombre del Director</label>
                <input type="text" name="nombre_director"
                    value="<?= $config['nombre_director'] ?? '' ?>"
                    placeholder="Nombre completo">
            </div>

            <div>
                <label>Cargo del Director</label>
                <textarea name="cargo_director" rows="2"
                    placeholder="Cargo institucional"><?= $config['cargo_director'] ?? '' ?></textarea>
            </div>

            <div class="full-row">
                <label>Archivo de Firma</label>
                <input type="text" name="archivo_firma"
                    value="<?= $config['archivo_firma'] ?? '' ?>"
                    placeholder="firma.png">
            </div>

            <div class="full-row">
                <button type="submit">
                    💾 Guardar Configuración
                </button>
            </div>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
$title = "Carga Académica";
$pagina = "carga";
include "dashboard.php";
?>