<?php
/**
 * TOGGLE MÉDICO - ACTIVAR/INACTIVAR
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../api/configuracion.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Verificar si el médico está logueado
if (!isset($_SESSION['medico_id'])) {
    die(json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]));
}

// Solo acepta peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]));
}

// Obtener ID del médico
$datos = json_decode(file_get_contents('php://input'), true);
$id = isset($datos['id']) ? intval($datos['id']) : 0;

if ($id <= 0) {
    die(json_encode([
        'success' => false,
        'message' => 'ID de médico inválido'
    ]));
}

try {
    // Verificar si el médico existe y obtener su estado actual
    $stmt = $pdo->prepare('SELECT id, activo FROM ca_medicos WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $medico = $stmt->fetch();
    
    if (!$medico) {
        die(json_encode([
            'success' => false,
            'message' => 'Médico no encontrado'
        ]));
    }
    
    // Invertir el estado (toggle)
    $nuevoEstado = $medico['activo'] == 1 ? 0 : 1;
    
    // Actualizar el estado
    $stmt = $pdo->prepare('UPDATE ca_medicos SET activo = :activo WHERE id = :id');
    $stmt->execute([
        'activo' => $nuevoEstado,
        'id' => $id
    ]);
    
    $mensaje = $nuevoEstado == 1 ? 'Médico activado correctamente' : 'Médico inactivado correctamente';
    
    echo json_encode([
        'success' => true,
        'message' => $mensaje
    ]);
    
} catch (Throwable $e) {
    error_log('Error al toggle médico: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al cambiar el estado del médico'
    ]);
}

