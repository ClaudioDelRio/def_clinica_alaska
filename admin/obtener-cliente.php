<?php
/**
 * OBTENER CLIENTE POR ID
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
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die(json_encode([
        'success' => false,
        'message' => 'ID de cliente inválido'
    ]));
}

try {
    $stmt = $pdo->prepare('SELECT id, nombre, email, rut, telefono, direccion, activo 
                           FROM ca_usuarios WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $cliente = $stmt->fetch();
    
    if ($cliente) {
        echo json_encode([
            'success' => true,
            'data' => $cliente
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Cliente no encontrado'
        ]);
    }
} catch (Throwable $e) {
    error_log('Error al obtener cliente: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener datos del cliente'
    ]);
}

