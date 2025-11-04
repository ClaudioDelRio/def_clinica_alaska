<?php
/**
 * ELIMINAR CITA
 * Solo administradores pueden eliminar citas
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../api/configuracion.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar si el médico está logueado y es admin
if (!isset($_SESSION['medico_id']) || !isset($_SESSION['medico_es_admin']) || !$_SESSION['medico_es_admin']) {
    die(json_encode([
        'success' => false,
        'message' => 'No autorizado. Solo administradores pueden eliminar citas.'
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

if ($cita_id <= 0) {
    die(json_encode([
        'success' => false,
        'message' => 'ID de cita inválido'
    ]));
}

try {
    // Verificar que la cita existe
    $stmt = $pdo->prepare("SELECT id FROM ca_citas WHERE id = :id");
    $stmt->execute(['id' => $cita_id]);
    
    if (!$stmt->fetch()) {
        throw new Exception('La cita no existe');
    }
    
    // Eliminar la cita
    $stmt = $pdo->prepare("DELETE FROM ca_citas WHERE id = :id");
    $stmt->execute(['id' => $cita_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Cita eliminada exitosamente'
    ]);
    
} catch (Throwable $e) {
    error_log('Error al eliminar cita: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar la cita: ' . $e->getMessage()
    ]);
}

