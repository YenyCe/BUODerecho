<?php
// helpers_asistencia.php
// Funciones auxiliares para generar fechas según horarios.
// Requiere que el script que lo incluya tenga $conn (mysqli).

function map_dia_enum_a_num($enum) {
    $map = ['L'=>1,'M'=>2,'X'=>3,'J'=>4,'V'=>5,'S'=>6];
    return $map[$enum] ?? null;
}

function obtener_horarios_docente($conn, $id_docente, $id_materia, $id_grupo) {
    // devuelve array de filas de horarios (id_horario, dia_semana, hora_inicio, hora_fin)
    $sql = "SELECT id_horario, dia_semana, hora_inicio, hora_fin
            FROM horarios
            WHERE id_docente = ? AND id_materia = ? AND id_grupo = ?
            ORDER BY FIELD(dia_semana,'L','M','X','J','V','S'), hora_inicio";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $id_docente, $id_materia, $id_grupo);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function generar_fechas_por_horarios($fecha_inicio, $fecha_fin, $horarios) {
    // $horarios: array con 'dia_semana' (L M X J V S)
    if (empty($horarios)) return [];
    $dias_allowed = [];
    foreach ($horarios as $h) {
        $dias_allowed[] = map_dia_enum_a_num($h['dia_semana']);
    }
    $dias_allowed = array_values(array_unique($dias_allowed)); // números 1..6

    $inicio = new DateTime($fecha_inicio);
    $fin = new DateTime($fecha_fin);
    $fin->modify('+1 day'); // para incluir fecha_fin
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
