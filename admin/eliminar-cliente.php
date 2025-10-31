<?php
/**
 * ELIMINAR CLIENTE CON CASCADA (mascotas y citas)
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

// Obtener ID del cliente
$datos = json_decode(file_get_contents('php://input'), true);
$id = isset($datos['id']) ? intval($datos['id']) : 0;

if ($id <= 0) {
    die(json_encode([
        'success' => false,
        'message' => 'ID de cliente inválido'
    ]));
}

try {
    // Verificar si el cliente existe y obtener su estado actual
    $stmt = $pdo->prepare('SELECT id, activo FROM ca_usuarios WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $cliente = $stmt->fetch();
    
    if (!$cliente) {
        die(json_encode([
            'success' => false,
            'message' => 'Cliente no encontrado'
        ]));
    }
    
    // Invertir el estado (toggle)
    $nuevoEstado = $cliente['activo'] == 1 ? 0 : 1;
    
    // Actualizar el estado
    $stmt = $pdo->prepare('UPDATE ca_usuarios SET activo = :activo WHERE id = :id');
    $stmt->execute([
        'activo' => $nuevoEstado,
        'id' => $id
    ]);
    
    $mensaje = $nuevoEstado == 1 ? 'Cliente activado correctamente' : 'Cliente inactivado correctamente';
    
    echo json_encode([
        'success' => true,
        'message' => $mensaje
    ]);
    
} catch (Throwable $e) {
    error_log('Error al toggle cliente: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al cambiar el estado del cliente'
    ]);
}

