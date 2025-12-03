<?php
require_once "../middlewares/auth.php";
require_once "../config/conexion.php";

// Obtener docentes
$docentes = $conn->query("
    SELECT id_docente, CONCAT(nombre,' ',apellidos) AS nombre 
    FROM docentes
    ORDER BY nombre, apellidos
")->fetch_all(MYSQLI_ASSOC);

// INICIAR CAPTURA  
ob_start();
?>

<script>
    function cargarMaterias() {
        let docente = document.getElementById("docente").value;
        if (docente === "") {
            document.getElementById("materia").innerHTML = "<option value=''>Seleccione...</option>";
            document.getElementById("grupo").innerHTML = "<option value=''>Seleccione...</option>";
            return;
        }
        fetch("../ajax/get_materias.php?id_docente=" + docente)
            .then(r => r.text())
            .then(html => {
                document.getElementById("materia").innerHTML = html;
                document.getElementById("grupo").innerHTML = "<option value=''>Seleccione materia primero...</option>";
            });
    }

    function cargarGrupos() {
        let docente = document.getElementById("docente").value;
        let materia = document.getElementById("materia").value;
        if (materia === "") {
            document.getElementById("grupo").innerHTML = "<option value=''>Seleccione...</option>";
            return;
        }
        fetch("../ajax/get_grupos.php?id_docente=" + docente + "&id_materia=" + materia)
            .then(r => r.text())
            .then(html => {
                document.getElementById("grupo").innerHTML = html;
            });
    }
</script>


<div class="container-form">
    <h2>Generar lista de asistencia</h2>

    <form action="lista_impresion.php" method="POST" target="_blank" class="form-grid">

        <div>
            <label>Docente</label>
            <select name="id_docente" id="docente" required onchange="cargarMaterias()">
                <option value="">Seleccione docente...</option>
                <?php foreach ($docentes as $d): ?>
                    <option value="<?= $d['id_docente'] ?>"><?= $d['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Parcial (opcional)</label>
            <select name="id_parcial">
                <option value="">-- Ninguno --</option>
                <?php
                $parciales = $conn->query("SELECT * FROM parciales ORDER BY numero_parcial")->fetch_all(MYSQLI_ASSOC);
                foreach ($parciales as $p):
                ?>
                    <option value="<?= $p['id_parcial'] ?>">Parcial <?= $p['numero_parcial'] ?> (<?= $p['fecha_inicio'] ?> a <?= $p['fecha_fin'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="full-row">
            <label>Materia (solo las que imparte ese docente)</label>
            <select name="id_materia" id="materia" required onchange="cargarGrupos()">
                <option value="">Seleccione docente primero...</option>
            </select>
        </div>

        <div class="full-row">
            <label>Grupo (solo donde ese docente imparte esa materia)</label>
            <select name="id_grupo" id="grupo" required>
                <option value="">Seleccione materia primero...</option>
            </select>
        </div>

        <div class="full-row">
            <button type="submit" class="btn-agregar">Generar Lista</button>
        </div>


    </form>
</div>


<?php
// FIN de la captura
$content = ob_get_clean();
$title = " Generacion de Listas ";

// Cargar layout
include "dashboard.php";
?>