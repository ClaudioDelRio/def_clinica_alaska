<?php
/**
 * ELIMINAR MÉDICO
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../api/configuracion.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

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
    // Verificar si el médico existe
    $stmt = $pdo->prepare('SELECT id FROM ca_medicos WHERE id = :id');
    $stmt->execute(['id' => $id]);
    
    if (!$stmt->fetch()) {
        die(json_encode([
            'success' => false,
            'message' => 'Médico no encontrado'
        ]));
    }
    
    // Eliminar el médico (soft delete poniendo activo = 0)
    $stmt = $pdo->prepare('UPDATE ca_medicos SET activo = 0 WHERE id = :id');
    $stmt->execute(['id' => $id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Médico eliminado correctamente'
    ]);
    
} catch (Throwable $e) {
    error_log('Error al eliminar médico: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar el médico'
    ]);
}

