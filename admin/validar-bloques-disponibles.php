<?php
/**
 * VALIDAR BLOQUES HORARIOS DISPONIBLES
 * Verifica si múltiples bloques horarios están disponibles
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../api/configuracion.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar si el médico está logueado y es admin
if (!isset($_SESSION['medico_id']) || !isset($_SESSION['medico_es_admin']) || !$_SESSION['medico_es_admin']) {
    die(json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]));
}

// Obtener parámetros
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$bloques_str = isset($_GET['bloques']) ? $_GET['bloques'] : '';

if (empty($fecha) || empty($bloques_str)) {
    die(json_encode([
        'success' => false,
        'message' => 'Faltan parámetros requeridos'
    ]));
}

// Convertir string de bloques a array
$bloques = explode(',', $bloques_str);

try {
    // Verificar cada bloque horario
    $bloques_ocupados = [];
    
    foreach ($bloques as $bloque) {
        $stmt = $pdo->prepare("
            SELECT id 
            FROM ca_citas 
            WHERE fecha_cita = :fecha 
            AND hora_cita = :hora 
            AND estado NOT IN ('cancelada', 'completada')
        ");
        $stmt->execute([
            'fecha' => $fecha,
            'hora' => $bloque
        ]);
        
        if ($stmt->fetch()) {
            $bloques_ocupados[] = $bloque;
        }
    }
    
    $todos_disponibles = count($bloques_ocupados) === 0;
    
    echo json_encode([
        'success' => true,
        'data' => [
            'todos_disponibles' => $todos_disponibles,
            'bloques_ocupados' => $bloques_ocupados,
            'bloques_disponibles' => array_diff($bloques, $bloques_ocupados),
            'total_bloques' => count($bloques)
        ]
    ]);
    
} catch (Throwable $e) {
    error_log('Error al validar bloques: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al validar los bloques horarios'
    ]);
}

