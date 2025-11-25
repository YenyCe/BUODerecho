<?php
// generar_listas.php
require_once "../config/conexion.php"; // ajustar ruta si es necesario
// Traer datos para selects
$docentes = $conn->query("SELECT id_docente, CONCAT(nombre,' ',apellidos) AS nombre_completo FROM docentes ORDER BY nombre, apellidos")->fetch_all(MYSQLI_ASSOC);
$materias = $conn->query("SELECT * FROM materias ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$grupos = $conn->query("SELECT g.id_grupo, g.nombre AS nombre_grupo, s.numero AS semestre_num
                        FROM grupos g
                        INNER JOIN semestres s ON g.id_semestre = s.id_semestre
                        ORDER BY s.numero, g.nombre")->fetch_all(MYSQLI_ASSOC);
$parciales = $conn->query("SELECT * FROM parciales ORDER BY numero_parcial")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generar lista imprimible</title>
    <link rel="stylesheet" href="../public/css/styles.css"> <!-- opcional -->
    <style>
        /* Estilos mínimos para el formulario */
        body{font-family:Arial,Helvetica,sans-serif;padding:20px}
        .container{max-width:900px;margin:0 auto}
        label{display:block;margin-top:10px}
        select,input[type=date]{width:100%;padding:6px;margin-top:4px}
        .row{display:flex;gap:10px}
        .col{flex:1}
        button{margin-top:12px;padding:10px 16px}
    </style>
</head>
<body>
<div class="container">
    <h2>Generar lista de asistencia (vista para imprimir)</h2>

    <form action="lista_impresion.php" method="POST" target="_blank">
        <div class="row">
            <div class="col">
                <label>Docente</label>
                <select name="id_docente" required>
                    <option value="">-- Seleccione docente --</option>
                    <?php foreach($docentes as $d): ?>
                        <option value="<?php echo $d['id_docente']; ?>"><?php echo htmlspecialchars($d['nombre_completo']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col">
                <label>Grupo</label>
                <select name="id_grupo" required>
                    <option value="">-- Seleccione grupo --</option>
                    <?php foreach($grupos as $g): ?>
                        <option value="<?php echo $g['id_grupo']; ?>"><?php echo htmlspecialchars($g['nombre_grupo']." - Sem ".$g['semestre_num']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <label>Materia</label>
                <select name="id_materia" required>
                    <option value="">-- Seleccione materia --</option>
                    <?php foreach($materias as $m): ?>
                        <option value="<?php echo $m['id_materia']; ?>"><?php echo htmlspecialchars($m['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col">
                <label>Parcial (opcional - si seleccionas reemplaza rango de fechas)</label>
                <select name="id_parcial">
                    <option value="">-- Seleccione parcial --</option>
                    <?php foreach($parciales as $p): ?>
                        <option value="<?php echo $p['id_parcial']; ?>"><?php echo "Parcial ".$p['numero_parcial']." (".$p['fecha_inicio']." a ".$p['fecha_fin'].")"; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <label>Fecha inicio (si no usas parcial)</label>
                <input type="date" name="fecha_inicio" >
            </div>
            <div class="col">
                <label>Fecha fin (si no usas parcial)</label>
                <input type="date" name="fecha_fin" >
            </div>
        </div>

        <p style="margin-top:10px;color:#555">Nota: Las fechas de las columnas se generan automáticamente según los días que estén registrados en <code>horarios</code> para el docente, materia y grupo seleccionados.</p>

        <button type="submit">Generar vista imprimible</button>
    </form>
</div>
</body>
</html>
