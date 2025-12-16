<?php
require_once "../config/conexion.php";

// Recibir datos
$id_docente  = $_POST['id_docente'] ?? 0;
$id_materia  = $_POST['id_materia'] ?? 0;
$id_grupo    = $_POST['id_grupo'] ?? 0;
$id_parcial  = $_POST['id_parcial'] ?? 0;

if (!$id_docente || !$id_materia || !$id_grupo || !$id_parcial) {
    die("Faltan parámetros obligatorios.");
}

// Datos generales
$docente = $conn->query("SELECT CONCAT(nombre,' ',apellidos) AS nombre FROM docentes WHERE id_docente = $id_docente")->fetch_assoc();
$materia = $conn->query("SELECT * FROM materias WHERE id_materia = $id_materia")->fetch_assoc();
$grupo = $conn->query("
    SELECT g.nombre AS nombre_grupo, s.numero AS semestre_num
    FROM grupos g
    INNER JOIN semestres s ON g.id_semestre = s.id_semestre
    WHERE g.id_grupo = $id_grupo
")->fetch_assoc();

// Parcial
$parcial = $conn->query("SELECT * FROM parciales WHERE id_parcial = $id_parcial")->fetch_assoc();

// Alumnos
$alumnos = $conn->query("
    SELECT *
    FROM alumnos
    WHERE id_grupo = $id_grupo
    ORDER BY nombre
")->fetch_all(MYSQLI_ASSOC);

// Paginación
$total_alumnos = count($alumnos);
$por_pagina = 15;
$paginas = ceil($total_alumnos / $por_pagina);
$index_global = 0;

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Calificaciones</title>
    <link rel="stylesheet" href="../css/lista.css">
</head>

<body>
    <button onclick="window.print()" class="print-btn">Imprimir</button>

    <?php for ($pagina = 1; $pagina <= $paginas; $pagina++): ?>
        <div class="page">
            <div class="contenido" style="margin-top:40px;">

                <!-- ================== ENCABEZADO ================== -->
                <table class="info-reporte">
                    <tr>
                        <td colspan="12" style="text-align:center;font-size:18px;">
                            <strong>REPORTE DE CALIFICACIONES PARCIALES CICLO ESCOLAR 2025 - 2026</strong>
                            <br>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6">
                            <strong>ASIGNATURA:</strong> <?= $materia['nombre'] ?>
                        </td>
                        <td colspan="6">
                            <strong>EVALUACIÓN PARCIAL NUMERO:</strong> <?= $parcial['numero_parcial'] ?>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6">
                            <strong>DOCENTE:</strong> <?= $docente['nombre'] ?>
                        </td>

                        <td colspan="6">
                            <strong>FECHA DE EVALUACIÓN:</strong>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="6">
                            <strong>SEMESTRE:</strong> <?= $grupo['nombre_grupo'] ?>
                        </td>

                        <td colspan="6">
                            <strong>FECHA DE REPORTE DE CALIFICACIONES:</strong>
                        </td>
                    </tr>

                </table>

                <!-- ================= TABLA PRINCIPAL ================= -->
                <table >
                    <thead class="table-reporte">
                        <tr>
                            <th rowspan="2">N.</th>
                            <th rowspan="2">Nombre del alumno</th>
                            <!-- Firma del alumno queda fuera -->
                            <th rowspan="2">Asistencia</th>
                            <!-- Fila que engloba los criterios (solo 6 columnas) -->
                            <th colspan="6">
                                CRITERIOS A EVALUAR
                            </th>
                        </tr>

                        <tr>

                            <th>Participación <br> %</th>
                            <th>TRABAJOS <br> %</th>
                            <th>Examen<br> %</th>
                            <th>Total de <br>Inasistencias</th>
                            <th>Total <br> 100%</th>
                            <th>Firma del <br> alumno</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        for ($i = 1; $i <= $por_pagina; $i++):
                            if ($index_global >= $total_alumnos) break;

                            $a = $alumnos[$index_global];
                        ?>
                            <tr>
                                <td><?= $index_global + 1 ?></td>
                                <td class="capitalizar"><?= htmlspecialchars($a['nombre']) ?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td> <!-- Firma del alumno -->
                            </tr>
                        <?php
                            $index_global++;
                        endfor;
                        ?>
                    </tbody>
                </table>


                <?php if ($pagina == $paginas): ?>
                    <div style="margin-top:20px; width:90%; font-size:0.75em; text-align:justify;">
                        NOTA: Los criterios aquí mencionados son enunciativos más no limitativos, por lo que el docente podrá variar los criterios de evaluación acorde a las actividades que considere optimicen el aprendizaje del alumno, pudiendo agregar o disminuir las columnas necesarias. **Borrar esta leyenda antes de entregar**
                    </div>

                    <div style="margin-top:20px; display:flex; justify-content:space-between; align-items:flex-start;">
                        <!-- Firma del docente -->
                        <div style="text-align:center; width:45%;">
                            <p><?= htmlspecialchars($docente['nombre']) ?> <br>TITULAR DE ASIGNATURA</p>
                        </div>
                        <!-- Firma del control escolar -->
                        <div style="text-align:center; width:45%;">
                            <br>
                            <div>
                                CONTROL ESCOLAR DE <br> LICENCIATURA EN DERECHO
                            </div>
                        </div>
                    </div>
                <?php endif; ?>


            </div>
        </div>

        <?php if ($pagina < $paginas): ?>
            <div class="page-break"></div>
        <?php endif; ?>

    <?php endfor; ?>

</body>

</html>