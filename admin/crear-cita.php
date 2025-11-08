<?php
/**
 * CREAR CITA DESDE ADMIN
 * Solo administradores pueden crear citas
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../config/configuracion.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar si el médico está logueado y es admin
if (!isset($_SESSION['medico_id']) || !isset($_SESSION['medico_es_admin']) || !$_SESSION['medico_es_admin']) {
    die(json_encode([
        'success' => false,
        'message' => 'No autorizado. Solo administradores pueden crear citas.'
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
$usuario_id = isset($data['usuario_id']) ? intval($data['usuario_id']) : 0;
$mascota_id = isset($data['mascota_id']) ? intval($data['mascota_id']) : 0;
$fecha_cita = isset($data['fecha_cita']) ? trim($data['fecha_cita']) : '';
$hora_cita = isset($data['hora_cita']) ? trim($data['hora_cita']) : '';
$duracion_minutos = isset($data['duracion_minutos']) ? intval($data['duracion_minutos']) : 30;
$servicio = isset($data['servicio']) ? trim($data['servicio']) : '';
$doctor_id = isset($data['doctor_id']) ? intval($data['doctor_id']) : null;
$motivo = isset($data['motivo']) ? trim($data['motivo']) : '';

// Validaciones
if ($usuario_id <= 0) {
    die(json_encode([
        'success' => false,
        'message' => 'Debe seleccionar un cliente'
    ]));
}

if ($mascota_id <= 0) {
    die(json_encode([
        'success' => false,
        'message' => 'Debe seleccionar una mascota'
    ]));
}

if (empty($fecha_cita) || empty($hora_cita) || empty($servicio)) {
    die(json_encode([
        'success' => false,
        'message' => 'Todos los campos son obligatorios'
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
    // Verificar que el cliente existe
    $stmt = $pdo->prepare("SELECT id FROM ca_usuarios WHERE id = :id");
    $stmt->execute(['id' => $usuario_id]);
    if (!$stmt->fetch()) {
        throw new Exception('El cliente seleccionado no existe');
    }
    
    // Verificar que la mascota existe y pertenece al cliente
    $stmt = $pdo->prepare("SELECT id FROM ca_mascotas WHERE id = :id AND usuario_id = :usuario_id");
    $stmt->execute(['id' => $mascota_id, 'usuario_id' => $usuario_id]);
    if (!$stmt->fetch()) {
        throw new Exception('La mascota seleccionada no existe o no pertenece al cliente');
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
    
    // Calcular bloques horarios necesarios
    $bloques_horarios = [];
    list($hora, $minuto) = explode(':', $hora_cita);
    $minuto_actual = ($hora * 60) + $minuto;
    $bloques_necesarios = ceil($duracion_minutos / 30);
    
    for ($i = 0; $i < $bloques_necesarios; $i++) {
        $h = floor($minuto_actual / 60);
        $m = $minuto_actual % 60;
        $bloques_horarios[] = sprintf('%02d:%02d', $h, $m);
        $minuto_actual += 30;
    }
    
    // Verificar disponibilidad de todos los bloques
    foreach ($bloques_horarios as $bloque) {
        $stmt = $pdo->prepare("
            SELECT id 
            FROM ca_citas 
            WHERE fecha_cita = :fecha 
            AND hora_cita = :hora 
            AND estado NOT IN ('cancelada', 'completada')
        ");
        $stmt->execute([
            'fecha' => $fecha_cita,
            'hora' => $bloque
        ]);
        
        if ($stmt->fetch()) {
            throw new Exception("El horario $bloque ya está ocupado");
        }
    }
    
    // Generar ID único para agrupar todos los bloques
    $grupo_cita_id = uniqid('cita_', true);
    
    // Iniciar transacción para crear todos los bloques
    $pdo->beginTransaction();
    
    try {
        $cita_id = null;
        
        // Crear una cita por cada bloque horario
        foreach ($bloques_horarios as $index => $bloque) {
            $sql = "
                INSERT INTO ca_citas (usuario_id, mascota_id, fecha_cita, hora_cita, duracion_minutos, servicio, doctor_id, motivo, estado, grupo_cita_id)
                VALUES (:usuario_id, :mascota_id, :fecha_cita, :hora_cita, :duracion_minutos, :servicio, :doctor_id, :motivo, 'confirmada', :grupo_cita_id)
            ";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'usuario_id' => $usuario_id,
                'mascota_id' => $mascota_id,
                'fecha_cita' => $fecha_cita,
                'hora_cita' => $bloque,
                'duracion_minutos' => $duracion_minutos,
                'servicio' => $servicio,
                'doctor_id' => $doctor_id,
                'motivo' => $motivo,
                'grupo_cita_id' => $grupo_cita_id
            ]);
            
            // Guardar el ID de la primera cita como referencia
            if ($index === 0) {
                $cita_id = $pdo->lastInsertId();
            }
        }
        
        // Confirmar transacción
        $pdo->commit();
    } catch (Exception $e) {
        // Revertir en caso de error
        $pdo->rollBack();
        throw $e;
    }
    
    $mensaje_duracion = '';
    if ($duracion_minutos > 30) {
        $horas = floor($duracion_minutos / 60);
        $minutos = $duracion_minutos % 60;
        if ($horas > 0 && $minutos > 0) {
            $mensaje_duracion = " ({$horas}h {$minutos}min - {$bloques_necesarios} bloques)";
        } else if ($horas > 0) {
            $mensaje_duracion = " ({$horas}h - {$bloques_necesarios} bloques)";
        } else {
            $mensaje_duracion = " ({$minutos}min - {$bloques_necesarios} bloques)";
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Cita creada exitosamente' . $mensaje_duracion,
        'data' => [
            'cita_id' => $cita_id,
            'grupo_cita_id' => $grupo_cita_id,
            'bloques_creados' => count($bloques_horarios),
            'duracion_minutos' => $duracion_minutos
        ]
    ]);
    
} catch (Throwable $e) {
    error_log('Error al crear cita: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al crear la cita: ' . $e->getMessage()
    ]);
}

