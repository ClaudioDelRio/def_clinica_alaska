<?php
/**
 * CONTAR MASCOTAS Y CITAS DE UN CLIENTE
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../api/configuracion.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar si el médico está logueado
if (!isset($_SESSION['medico_id'])) {
    die(json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]));
}

// Obtener ID del cliente
$cliente_id = isset($_GET['cliente_id']) ? intval($_GET['cliente_id']) : 0;

if ($cliente_id <= 0) {
    die(json_encode([
        'success' => false,
        'message' => 'ID de cliente inválido'
    ]));
}

try {
    // Contar mascotas
    $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM ca_mascotas WHERE usuario_id = :cliente_id');
    $stmt->execute(['cliente_id' => $cliente_id]);
    $mascotasCount = $stmt->fetchColumn();
    
    // Contar citas
    $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM ca_citas WHERE usuario_id = :cliente_id');
    $stmt->execute(['cliente_id' => $cliente_id]);
    $citasCount = $stmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'mascotas' => (int)$mascotasCount,
            'citas' => (int)$citasCount
        ]
    ]);
    
} catch (Throwable $e) {
    error_log('Error al contar mascotas y citas: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al contar mascotas y citas'
    ]);
}

