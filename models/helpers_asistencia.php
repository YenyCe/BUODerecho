<?php
// helpers_asistencia.php
// Funciones auxiliares para generar fechas según horarios.
// Requiere que el script que lo incluya tenga $conn (mysqli).

function map_dia_enum_a_num($enum) {
    $map = ['L'=>1,'M'=>2,'X'=>3,'J'=>4,'V'=>5,'S'=>6];
    return $map[$enum] ?? null;
}

function obtener_horarios_docente($conn, $id_docente, $id_materia, $id_grupo) {
    // Devuelve array de filas con id_horario, dias (como 'L-M-X'), horario_texto
    $sql = "SELECT 
                h.id_horario,
                GROUP_CONCAT(hd.dia ORDER BY FIELD(hd.dia,'L','M','X','J','V','S') SEPARATOR '-') AS dias,
                h.horario_texto
            FROM horarios h
            LEFT JOIN horario_dias hd ON h.id_horario = hd.id_horario
            WHERE h.id_docente = ? AND h.id_materia = ? AND h.id_grupo = ?
            GROUP BY h.id_horario, h.horario_texto
            ORDER BY FIELD(GROUP_CONCAT(hd.dia ORDER BY FIELD(hd.dia,'L','M','X','J','V','S')), 'L','M','X','J','V','S')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $id_docente, $id_materia, $id_grupo);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Transformamos 'dias' en array individual de letras si quieres mantener compatibilidad
    foreach ($rows as &$row) {
        $row['dias_array'] = explode('-', $row['dias']);
    }

    return $rows;
}


function generar_fechas_por_horarios($fecha_inicio, $fecha_fin, $horarios) {
    // $horarios: array con 'dias_array' => ['L','M',...]
    if (empty($horarios)) return [];
    
    $dias_allowed = [];
    foreach ($horarios as $h) {
        if (isset($h['dias_array']) && is_array($h['dias_array'])) {
            foreach ($h['dias_array'] as $dia) {
                $dias_allowed[] = map_dia_enum_a_num($dia);
            }
        }
    }

    // eliminar duplicados y mantener solo números válidos 1..6
    $dias_allowed = array_values(array_unique(array_filter($dias_allowed)));

    $inicio = new DateTime($fecha_inicio);
    $fin = new DateTime($fecha_fin);
    $fin->modify('+1 day'); // incluir fecha_fin
    $fechas = [];

    for ($d = clone $inicio; $d < $fin; $d->modify('+1 day')) {
        $w = (int)$d->format('N'); // 1 = lunes ... 7 = domingo
        if (in_array($w, $dias_allowed)) {
            $fechas[] = $d->format('Y-m-d');
        }
    }

    return $fechas;
}


function nombre_dia_corto($fecha) {
    // "Lun", "Mar", "Mie", "Jue", "Vie", "Sab", "Dom"
    $map = [
        'Mon' => 'Lun', 'Tue' => 'Mar', 'Wed'=>'Mie',
        'Thu'=>'Jue','Fri'=>'Vie','Sat'=>'Sab','Sun'=>'Dom'
    ];
    $d = date('D', strtotime($fecha));
    return $map[$d] ?? $d;
}
