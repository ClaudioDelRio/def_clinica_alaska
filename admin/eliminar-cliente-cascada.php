<?php
/**
 * ELIMINAR CLIENTE CON CASCADA (mascotas y citas)
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../config/configuracion.php';

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
    // Verificar si el cliente existe
    $stmt = $pdo->prepare('SELECT id, nombre FROM ca_usuarios WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $cliente = $stmt->fetch();
    
    if (!$cliente) {
        die(json_encode([
            'success' => false,
            'message' => 'Cliente no encontrado'
        ]));
    }
    
    // Iniciar transacción para eliminación en cascada
    $pdo->beginTransaction();
    
    try {
        // 1. Eliminar citas del cliente
        $stmt = $pdo->prepare('DELETE FROM ca_citas WHERE usuario_id = :id');
        $stmt->execute(['id' => $id]);
        $citasEliminadas = $stmt->rowCount();
        
        // 2. Eliminar mascotas del cliente
        $stmt = $pdo->prepare('DELETE FROM ca_mascotas WHERE usuario_id = :id');
        $stmt->execute(['id' => $id]);
        $mascotasEliminadas = $stmt->rowCount();
        
        // 3. Eliminar el cliente
        $stmt = $pdo->prepare('DELETE FROM ca_usuarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
        
        // Confirmar transacción
        $pdo->commit();
        
        $mensaje = "Cliente eliminado correctamente";
        if ($citasEliminadas > 0) {
            $mensaje .= ". Se eliminaron $citasEliminadas cita(s)";
        }
        if ($mascotasEliminadas > 0) {
            $mensaje .= " y $mascotasEliminadas mascota(s)";
        }
        
        echo json_encode([
            'success' => true,
            'message' => $mensaje
        ]);
        
    } catch (Throwable $e) {
        // Revertir transacción en caso de error
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Throwable $e) {
    error_log('Error al eliminar cliente con cascada: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar el cliente: ' . $e->getMessage()
    ]);
}

