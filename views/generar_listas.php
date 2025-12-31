<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";

// ===========================================================
// 1. OBTENER CARRERA SEGÚN ROL
// ===========================================================
$id_carrera = null;

// Coordinador → carrera fija
if ($_SESSION['rol'] === "coordinador") {
    $id_carrera = $_SESSION['id_carrera'];
}

// Admin → puede seleccionar carrera
if ($_SESSION['rol'] === "admin") {

    if (!empty($_POST['id_carrera'])) {
        $id_carrera = intval($_POST['id_carrera']);
    }

    $carreras = $conn->query("
        SELECT id_carrera, nombre 
        FROM carreras 
        ORDER BY nombre
    ")->fetch_all(MYSQLI_ASSOC);
}

// ===========================================================
// 2. OBTENER MATERIAS FILTRADAS POR CARRERA
// ===========================================================
$materias = [];

if ($id_carrera) {
    $materias = $conn->query("
        SELECT DISTINCT m.id_materia, m.nombre 
        FROM materias m
        INNER JOIN horarios h ON h.id_materia = m.id_materia
        INNER JOIN grupos g ON g.id_grupo = h.id_grupo
        WHERE g.id_carrera = $id_carrera
        ORDER BY m.nombre
    ")->fetch_all(MYSQLI_ASSOC);
}

// ===========================================================
// 3. OBTENER PARCIALES DE ESA CARRERA
// ===========================================================
$parciales = [];

if ($id_carrera) {
    $parciales = $conn->query("
        SELECT * FROM parciales
        WHERE id_carrera = $id_carrera
        ORDER BY numero_parcial
    ")->fetch_all(MYSQLI_ASSOC);
}

// INICIAR CAPTURA
ob_start();
?>

<script>
function cargarGrupos() {
    let materia = document.getElementById("materia").value;

    if (materia === "") {
        document.getElementById("grupo").innerHTML = "<option value=''>Seleccione materia...</option>";
        document.getElementById("docente").innerHTML = "<option value=''>Seleccione grupo...</option>";
        return;
    }

    fetch("../ajax/get_grupos_por_materia.php?id_materia=" + materia)
        .then(r => r.json())
        .then(data => {

            let htmlGrupo = "<option value=''>Seleccione grupo...</option>";
            data.forEach(g => {
                htmlGrupo += `<option value="${g.id_grupo}">${g.nombre}</option>`;
            });

            document.getElementById("grupo").innerHTML = htmlGrupo;
            document.getElementById("docente").innerHTML = "<option value=''>Seleccione grupo...</option>";
        });
}

function cargarDocente() {
    let materia = document.getElementById("materia").value;
    let grupo = document.getElementById("grupo").value;

    if (grupo === "") return;

    fetch(`../ajax/get_docente_por_grupo.php?id_materia=${materia}&id_grupo=${grupo}`)
        .then(r => r.json())
        .then(d => {
            let html = `<option value="${d.id_docente}" selected>${d.nombre}</option>`;
            document.getElementById("docente").innerHTML = html;
        });
}
</script>



<div class="container-form">
    <h2>Generar lista de asistencia</h2>

    <!-- Selección de carrera SOLO admin -->
    <?php if ($_SESSION['rol'] === 'admin'): ?>
    <form action="" method="POST" class="form-grid">
        <div class="full-row">
            <label>Carrera</label>
            <select name="id_carrera" required onchange="this.form.submit()">
                <option value="">Seleccione carrera...</option>
                <?php foreach ($carreras as $c): ?>
                    <option value="<?= $c['id_carrera'] ?>"
                        <?= ($id_carrera == $c['id_carrera']) ? 'selected' : '' ?>>
                        <?= $c['nombre'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
    <?php endif; ?>

    <!-- Coordinador: manda carrera oculta -->
    <?php if ($_SESSION['rol'] === 'coordinador'): ?>
        <input type="hidden" name="id_carrera" value="<?= $id_carrera ?>">
    <?php endif; ?>


    <?php if ($id_carrera): ?>
    <form action="lista_impresion.php" method="POST" target="_blank" class="form-grid">

        <input type="hidden" name="id_carrera" value="<?= $id_carrera ?>">

        <div class="full-row">
            <label>Materia</label>
                <select name="id_materia" id="materia" required onchange="cargarGrupos()">

                <option value="">Seleccione materia...</option>
                <?php foreach ($materias as $m): ?>
                    <option value="<?= $m['id_materia'] ?>"><?= $m['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Docente</label>
            <select name="id_docente" id="docente" required readonly>
    <option value="">Seleccione grupo...</option>
</select>

        </div>

        <div>
            <label>Grupo</label>
            <select name="id_grupo" id="grupo" required onchange="cargarDocente()">
    <option value="">Seleccione materia...</option>
</select>

        </div>

        <div>
            <label>Parcial (opcional)</label>
            <select name="id_parcial">
                <option value="">-- Ninguno --</option>
                <?php foreach ($parciales as $p): ?>
                    <option value="<?= $p['id_parcial'] ?>">
                        Parcial <?= $p['numero_parcial'] ?> (<?= $p['fecha_inicio'] ?> a <?= $p['fecha_fin'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="full-row" style="display:flex; gap:10px; justify-content:center;">

<div class="full-generar">
    <!-- LISTA DE ASISTENCIA -->
    <button type="submit"
            formaction="lista_impresion.php"
            class="btn-agregar"
            style="background:#007bff;">
        Generar Lista de Asistencia
    </button>

    <!-- REPORTE DE CALIFICACIONES -->
    <button type="submit"
            formaction="reporte_calificaciones.php"
            class="btn-agregar"
            style="background:#28a745;">
        Generar Reporte de Calificaciones
    </button>

    <!-- CONTROL DE ASISTENCIA DEL DOCENTE -->
    <button type="submit"
            formaction="control_asistencia_docente.php"
            class="btn-agregar"
            style="background:#6f42c1;">
        Generar Control de Asistencia Docente
    </button>
</div>



</div>

    </form>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$title = "Generación de Listas";
$pagina = "generar_listas";
include "dashboard.php";
?>


