<?php
/**
 * OBTENER HORARIOS DISPONIBLES PARA ADMINISTRADORES
 * Devuelve los horarios disponibles para una fecha específica
 * Solo para uso del panel de administración
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../config/configuracion.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar si el médico está logueado y es admin
if (!isset($_SESSION['medico_id']) || !isset($_SESSION['medico_es_admin']) || !$_SESSION['medico_es_admin']) {
    die(json_encode([
        'success' => false,
        'message' => 'No autorizado. Solo administradores pueden acceder.'
    ]));
}

// Obtener la fecha de la consulta
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';

// Validar fecha
if (empty($fecha)) {
    die(json_encode([
        'success' => false,
        'message' => 'La fecha es obligatoria'
    ]));
}

// Validar formato de fecha
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    die(json_encode([
        'success' => false,
        'message' => 'Formato de fecha inválido. Use YYYY-MM-DD'
    ]));
}

// Validar que no sea domingo (opcional, el admin puede querer ver todos los días)
$diaSemana = date('N', strtotime($fecha));

try {
    // Obtener las horas ya ocupadas para esa fecha
    $sql = "SELECT hora_cita 
            FROM ca_citas 
            WHERE fecha_cita = :fecha 
            AND estado IN ('pendiente', 'confirmada')";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['fecha' => $fecha]);
    $horasOcupadas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Convertir a formato simple (HH:MM)
    $horasOcupadasFormateadas = array_map(function($hora) {
        return substr($hora, 0, 5); // Tomar solo HH:MM
    }, $horasOcupadas);
    
    // Generar lista de horarios disponibles
    $horariosDisponibles = [];
    foreach (HORARIOS as $horario) {
        if (!in_array($horario, $horasOcupadasFormateadas)) {
            $horariosDisponibles[] = [
                'hora' => $horario,
                'disponible' => true,
                'texto' => $horario . ' hrs'
            ];
        }
    }
    
    // Información adicional
    $totalHorarios = count(HORARIOS);
    $horariosOcupados = count($horasOcupadasFormateadas);
    $horariosLibres = count($horariosDisponibles);
    
    echo json_encode([
        'success' => true,
        'message' => 'Horarios obtenidos exitosamente',
        'data' => [
            'fecha' => $fecha,
            'dia_semana' => obtenerNombreDia($diaSemana),
            'horarios_disponibles' => $horariosDisponibles,
            'estadisticas' => [
                'total' => $totalHorarios,
                'ocupados' => $horariosOcupados,
                'disponibles' => $horariosLibres
            ]
        ]
    ]);
    
} catch (Throwable $e) {
    error_log('Error en obtener-horarios-disponibles.php (admin): ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener los horarios disponibles'
    ]);
}

/**
 * Obtiene el nombre del día de la semana en español
 * @param int $numeroDia (1=Lunes, 7=Domingo)
 * @return string
 */
function obtenerNombreDia($numeroDia) {
    $dias = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo'
    ];
    return $dias[$numeroDia] ?? '';
}

