<?php
/**
 * LISTAR MASCOTAS DE UN CLIENTE
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
    // Obtener mascotas del cliente
    $stmt = $pdo->prepare('SELECT id, nombre, especie, raza, edad, peso, sexo, color, observaciones, activo, vacunas_al_dia, fecha_registro
                           FROM ca_mascotas 
                           WHERE usuario_id = :cliente_id 
                           ORDER BY fecha_registro DESC');
    $stmt->execute(['cliente_id' => $cliente_id]);
    $mascotas = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $mascotas
    ]);
    
} catch (Throwable $e) {
    error_log('Error al obtener mascotas del cliente: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener las mascotas del cliente'
    ]);
}

