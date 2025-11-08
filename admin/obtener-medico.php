<?php
/**
 * OBTENER MÉDICO POR ID
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../config/configuracion.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar si el médico está logueado
if (!isset($_SESSION['medico_id'])) {
    die(json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]));
}

// Obtener ID del médico
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die(json_encode([
        'success' => false,
        'message' => 'ID de médico inválido'
    ]));
}

try {
    $stmt = $pdo->prepare('SELECT id, nombre, especialidad, telefono, email, es_admin, activo 
                           FROM ca_medicos WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $medico = $stmt->fetch();
    
    if ($medico) {
        echo json_encode([
            'success' => true,
            'data' => $medico
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Médico no encontrado'
        ]);
    }
} catch (Throwable $e) {
    error_log('Error al obtener médico: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener datos del médico'
    ]);
}

