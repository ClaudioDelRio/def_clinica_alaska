<?php
/**
 * ACTUALIZAR CITA
 * Solo administradores pueden actualizar citas
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../api/configuracion.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar si el médico está logueado y es admin
if (!isset($_SESSION['medico_id']) || !isset($_SESSION['medico_es_admin']) || !$_SESSION['medico_es_admin']) {
    die(json_encode([
        'success' => false,
        'message' => 'No autorizado. Solo administradores pueden actualizar citas.'
    ]));
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]));
}

// Obtener datos
$data = json_decode(file_get_contents('php://input'), true);
$cita_id = isset($data['cita_id']) ? intval($data['cita_id']) : 0;
$fecha_cita = isset($data['fecha_cita']) ? trim($data['fecha_cita']) : '';
$hora_cita = isset($data['hora_cita']) ? trim($data['hora_cita']) : '';
$estado = isset($data['estado']) ? trim($data['estado']) : '';
$servicio = isset($data['servicio']) ? trim($data['servicio']) : '';
$doctor_id = isset($data['doctor_id']) ? intval($data['doctor_id']) : null;
$motivo = isset($data['motivo']) ? trim($data['motivo']) : '';

// Validaciones
if ($cita_id <= 0) {
    die(json_encode([
        'success' => false,
        'message' => 'ID de cita inválido'
    ]));
}

if (empty($fecha_cita) || empty($hora_cita) || empty($estado) || empty($servicio)) {
    die(json_encode([
        'success' => false,
        'message' => 'Todos los campos son obligatorios'
    ]));
}

// Validar estado
$estados_validos = ['pendiente', 'confirmada', 'completada', 'cancelada'];
if (!in_array($estado, $estados_validos)) {
    die(json_encode([
        'success' => false,
        'message' => 'Estado inválido'
    ]));
}

// Validar servicio
$servicios_validos = ['consulta', 'vacunacion', 'cirugia', 'radiologia', 'laboratorio', 'peluqueria', 'emergencia'];
if (!in_array($servicio, $servicios_validos)) {
    die(json_encode([
        'success' => false,
        'message' => 'Servicio inválido'
    ]));
}

try {
    // Verificar que la cita existe
    $stmt = $pdo->prepare("SELECT id FROM ca_citas WHERE id = :id");
    $stmt->execute(['id' => $cita_id]);
    
    if (!$stmt->fetch()) {
        throw new Exception('La cita no existe');
    }
    
    // Si se especificó un doctor, verificar que existe
    if ($doctor_id !== null && $doctor_id > 0) {
        $stmt = $pdo->prepare("SELECT id FROM ca_medicos WHERE id = :id AND activo = 1");
        $stmt->execute(['id' => $doctor_id]);
        
        if (!$stmt->fetch()) {
            throw new Exception('El doctor especificado no existe o no está activo');
        }
    } else {
        $doctor_id = null;
    }
    
    // Verificar disponibilidad del horario (excepto para la misma cita)
    $stmt = $pdo->prepare("
        SELECT id 
        FROM ca_citas 
        WHERE fecha_cita = :fecha 
        AND hora_cita = :hora 
        AND id != :cita_id
        AND estado NOT IN ('cancelada', 'completada')
    ");
    $stmt->execute([
        'fecha' => $fecha_cita,
        'hora' => $hora_cita,
        'cita_id' => $cita_id
    ]);
    
    if ($stmt->fetch()) {
        throw new Exception('El horario seleccionado ya está ocupado');
    }
    
    // Actualizar la cita
    $sql = "
        UPDATE ca_citas 
        SET fecha_cita = :fecha_cita,
            hora_cita = :hora_cita,
            estado = :estado,
            servicio = :servicio,
            doctor_id = :doctor_id,
            motivo = :motivo
        WHERE id = :id
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'fecha_cita' => $fecha_cita,
        'hora_cita' => $hora_cita,
        'estado' => $estado,
        'servicio' => $servicio,
        'doctor_id' => $doctor_id,
        'motivo' => $motivo,
        'id' => $cita_id
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Cita actualizada exitosamente'
    ]);
    
} catch (Throwable $e) {
    error_log('Error al actualizar cita: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar la cita: ' . $e->getMessage()
    ]);
}

