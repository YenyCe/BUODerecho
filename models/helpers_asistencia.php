<?php
// helpers_asistencia.php
// Funciones auxiliares para generar fechas según horarios.
// Requiere que el script que lo incluya tenga $conn (mysqli).

// Map de letra a número de semana
function map_dia_enum_a_num($enum)
{
    $map = ['L' => 1, 'M' => 2, 'X' => 3, 'J' => 4, 'V' => 5, 'S' => 6, 'D' => 7];
    return $map[$enum] ?? null;
}

// Obtener horarios del docente (mejorado: usa SUBSTRING_INDEX para ordenar)
function obtener_horarios_docente($conn, $id_docente, $id_materia, $id_grupo)
{
    $sql = "SELECT 
                h.id_horario,
                GROUP_CONCAT(hd.dia ORDER BY FIELD(hd.dia,'L','M','X','J','V','S') SEPARATOR '-') AS dias,
                h.horario_texto
            FROM horarios h
            LEFT JOIN horario_dias hd ON h.id_horario = hd.id_horario
            WHERE h.id_docente = ? AND h.id_materia = ? AND h.id_grupo = ?
            GROUP BY h.id_horario, h.horario_texto
            ORDER BY FIELD(
                SUBSTRING_INDEX(GROUP_CONCAT(hd.dia ORDER BY FIELD(hd.dia,'L','M','X','J','V','S') SEPARATOR '-'), '-', 1),
                'L','M','X','J','V','S'
            )";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iii', $id_docente, $id_materia, $id_grupo);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Normalizar: dias_array siempre como array de letras
    foreach ($rows as &$row) {
        if (!empty($row['dias'])) {
            // limpiar y explotar por '-' o ',' o espacios
            $clean = preg_replace('/\s+/', '', $row['dias']);
            $parts = preg_split('/[,\-]+/', $clean);
            $parts = array_values(array_filter($parts));
            $row['dias_array'] = $parts;
        } else {
            $row['dias_array'] = [];
        }
    }

    return $rows;
}

// Nombre de día corto
function nombre_dia_corto($fecha)
{
    $map = [
        'Mon' => 'Lun',
        'Tue' => 'Mar',
        'Wed' => 'Mie',
        'Thu' => 'Jue',
        'Fri' => 'Vie',
        'Sat' => 'Sab',
        'Sun' => 'Dom'
    ];
    $d = date('D', strtotime($fecha));
    return $map[$d] ?? $d;
}

// Contar ocurrencias de nombres de día dentro de horario_texto
function contar_ocurrencias_texto_por_letra($texto)
{
    $texto = mb_strtolower($texto, 'UTF-8');
    $result = ['L' => 0, 'M' => 0, 'X' => 0, 'J' => 0, 'V' => 0, 'S' => 0, 'D' => 0];
    $patrones = [
        'L' => '/\blunes\b/i',
        'M' => '/\bmartes\b/i',
        'X' => '/\bmi[eé]rcoles\b/i',
        'J' => '/\bjueves\b/i',
        'V' => '/\bviernes\b/i',
        'S' => '/\bs[aá]bado\b/i',
        'D' => '/\bdomingo\b/i'
    ];
    foreach ($patrones as $letra => $pat) {
        if (preg_match_all($pat, $texto, $m)) {
            $result[$letra] = count($m[0]);
        }
    }
    return $result;
}

// -------------------------------------------------
// Función: generar_fechas_y_horas($fecha_inicio,$fecha_fin,$horarios)
// Devuelve array de elementos: ['fecha'=>'YYYY-MM-DD','horas'=>N]
// -------------------------------------------------
function generar_fechas_y_horas($fecha_inicio, $fecha_fin, $horarios)
{
    if (empty($horarios)) return [];

    // Mapas
    $map = ['l' => 1, 'm' => 2, 'x' => 3, 'j' => 4, 'v' => 5, 's' => 6, 'd' => 7];
    $dias_full = [
        'lunes' => 1,
        'martes' => 2,
        'miercoles' => 3,
        'miércoles' => 3,
        'jueves' => 4,
        'viernes' => 5,
        'sabado' => 6,
        'sábado' => 6,
        'sábado' => 6,
        'domingo' => 7
    ];

    $horas_por_dia = [];

    foreach ($horarios as $h) {
        if (empty($h['horario_texto'])) continue;

        $texto_raw = $h['horario_texto'];
        $texto = mb_strtolower($texto_raw, 'UTF-8');

        // dividir segmentos por comas o punto y coma (cada segmento suele contener información de un día)
        $segmentos = preg_split('/[,;]+/', $texto);

        foreach ($segmentos as $seg_raw) {
            $seg = trim($seg_raw);
            if ($seg === '') continue;

            $dia_num = null;

            // 1) Intentar detectar por nombre completo (más fiable)
            foreach ($dias_full as $nombre => $num) {
                if (mb_stripos($seg, $nombre, 0, 'UTF-8') !== false) {
                    $dia_num = $num;
                    break;
                }
            }

            // 2) Si no se detectó por nombre, buscar letra aislada (L M X J V S D)
            if (!$dia_num) {
                if (preg_match('/\b([lmxjvsd])\b/iu', $seg, $m)) {
                    $letra = mb_strtolower($m[1], 'UTF-8');
                    if (isset($map[$letra])) $dia_num = $map[$letra];
                }
            }

            if (!$dia_num) continue;

            // 3) Extraer rango horario (HH:MM ... HH:MM). Acepta varias variantes de separador (a, A, to, -, etc.)
            $horasCalc = null;
            if (preg_match('/(\d{1,2}):(\d{2})\s*(?:a|to|-|–)\s*(\d{1,2}):(\d{2})/iu', $seg, $m)) {
                $h1 = intval($m[1]);
                $min1 = intval($m[2]);
                $h2 = intval($m[3]);
                $min2 = intval($m[4]);

                $startMin = $h1 * 60 + $min1;
                $endMin = $h2 * 60 + $min2;
                if ($endMin <= $startMin) $endMin += 24 * 60;

                $diffMin = $endMin - $startMin;
                $horasCalc = (int) ceil($diffMin / 60);
                if ($horasCalc < 1) $horasCalc = 1;
            } else {
                // fallback: si no hay rango, asignamos 1 hora por defecto
                $horasCalc = 1;
            }

            if (!isset($horas_por_dia[$dia_num]) || $horasCalc > $horas_por_dia[$dia_num]) {
                $horas_por_dia[$dia_num] = $horasCalc;
            }
        }
    }

    // Generar fechas entre el rango
    try {
        $inicio = new DateTime($fecha_inicio);
        $fin = new DateTime($fecha_fin);
    } catch (Exception $e) {
        return [];
    }
    $fin->modify('+1 day');

    $fechas = [];
    for ($d = clone $inicio; $d < $fin; $d->modify('+1 day')) {
        $w = intval($d->format('N')); // 1..7
        if (isset($horas_por_dia[$w])) {
            $fechas[] = [
                'fecha' => $d->format('Y-m-d'),
                'horas' => (int)$horas_por_dia[$w]
            ];
        }
    }

    return $fechas;
}


// Agrupar fechas por mes y año (para reportes)
function agrupar_fechas_por_mes(array $fechas): array
{
    $meses = [
        1 => 'ENERO',
        2 => 'FEBRERO',
        3 => 'MARZO',
        4 => 'ABRIL',
        5 => 'MAYO',
        6 => 'JUNIO',
        7 => 'JULIO',
        8 => 'AGOSTO',
        9 => 'SEPTIEMBRE',
        10 => 'OCTUBRE',
        11 => 'NOVIEMBRE',
        12 => 'DICIEMBRE'
    ];

    $resultado = [];

    foreach ($fechas as $f) {
        $m = (int)date('m', strtotime($f['fecha']));
        $a = date('Y', strtotime($f['fecha']));
        $resultado[$meses[$m] . " " . $a][] = $f;
    }

    return $resultado;
}

/* ================= HELPERS ================= */
function formato_horarios($horarios)
{
    if (!$horarios) return '-';
    return implode(', ', array_column($horarios, 'horario_texto'));
}

function dia_fecha($fecha)
{
    $dias = ['Sun' => 'DOMINGO', 'Mon' => 'LUNES', 'Tue' => 'MARTES', 'Wed' => 'MIERCOLES', 'Thu' => 'JUEVES', 'Fri' => 'VIERNES', 'Sat' => 'SABADO'];
    return $dias[date('D', strtotime($fecha))] . " " . date('d', strtotime($fecha));
}