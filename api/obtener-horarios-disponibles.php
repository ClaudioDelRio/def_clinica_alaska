<?php
/* ============================================
   ENDPOINT: OBTENER HORARIOS DISPONIBLES
   Devuelve los horarios disponibles para una fecha específica
   considerando que solo puede haber UNA cita por horario
   Clínica Veterinaria Alaska Pets Center
   ============================================ */

require_once __DIR__ . '/../config/configuracion.php';

// Configurar headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Verificar que el usuario esté logueado
if (!estaLogueado()) {
    enviarError('Debe iniciar sesión primero');
}

// Obtener la fecha de la consulta
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';

// Validar fecha
if (empty($fecha)) {
    enviarError('La fecha es obligatoria');
}

// Validar formato de fecha
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    enviarError('Formato de fecha inválido. Use YYYY-MM-DD');
}

// Validar que la fecha no sea anterior a hoy
$fechaActual = date('Y-m-d');
if ($fecha < $fechaActual) {
    enviarError('No se pueden reservar citas en fechas pasadas');
}

// Validar que no sea domingo
$diaSemana = date('N', strtotime($fecha));
if ($diaSemana == 7) {
    enviarError('No atendemos los domingos');
}

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
    
    enviarExito('Horarios obtenidos exitosamente', [
        'fecha' => $fecha,
        'dia_semana' => obtenerNombreDia($diaSemana),
        'horarios_disponibles' => $horariosDisponibles,
        'estadisticas' => [
            'total' => $totalHorarios,
            'ocupados' => $horariosOcupados,
            'disponibles' => $horariosLibres
        ]
    ]);
    
} catch (PDOException $e) {
    error_log('Error en obtener-horarios-disponibles.php: ' . $e->getMessage());
    enviarError('Error al obtener los horarios disponibles');
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

