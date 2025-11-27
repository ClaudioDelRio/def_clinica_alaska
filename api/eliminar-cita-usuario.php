<?php
/**
 * ELIMINAR/CANCELAR CITA - USUARIO
 * Permite a los usuarios cancelar sus propias citas
 * Clínica Veterinaria Alaska Pets Center
 * Desarrollado por: Claudio del Rio - Web.malgarini®
 */

require_once __DIR__ . '/../config/configuracion.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar si el usuario está logueado
if (!estaLogueado()) {
    http_response_code(401);
    die(json_encode([
        'success' => false,
        'message' => 'No autorizado. Debes iniciar sesión.'
    ]));
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]));
}

// Obtener datos
$data = json_decode(file_get_contents('php://input'), true);
$cita_id = isset($data['cita_id']) ? intval($data['cita_id']) : 0;
$usuario_id = $_SESSION['usuario_id'];

// Validar ID de cita
if ($cita_id <= 0) {
    http_response_code(400);
    die(json_encode([
        'success' => false,
        'message' => 'ID de cita inválido'
    ]));
}

try {
    // Verificar que la cita existe y pertenece al usuario
    $stmt = $pdo->prepare("
        SELECT c.id, c.estado, c.fecha_cita, c.hora_cita, 
               m.nombre as mascota_nombre, m.usuario_id
        FROM ca_citas c
        INNER JOIN ca_mascotas m ON c.mascota_id = m.id
        WHERE c.id = :cita_id
    ");
    $stmt->execute(['cita_id' => $cita_id]);
    $cita = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verificar que la cita existe
    if (!$cita) {
        http_response_code(404);
        throw new Exception('La cita no existe');
    }
    
    // Verificar que la cita pertenece al usuario logueado
    if ($cita['usuario_id'] != $usuario_id) {
        http_response_code(403);
        throw new Exception('No tienes permisos para cancelar esta cita');
    }
    
    // Verificar que la cita puede ser cancelada (solo pendiente o confirmada)
    if ($cita['estado'] !== 'pendiente' && $cita['estado'] !== 'confirmada') {
        http_response_code(400);
        throw new Exception('Esta cita no puede ser cancelada (estado actual: ' . $cita['estado'] . ')');
    }
    
    // Verificar que la cita no sea en el pasado
    $fecha_hora_cita = strtotime($cita['fecha_cita'] . ' ' . $cita['hora_cita']);
    $ahora = time();
    
    if ($fecha_hora_cita < $ahora) {
        http_response_code(400);
        throw new Exception('No se pueden cancelar citas pasadas');
    }
    
    // Cambiar estado de la cita a "cancelada" (en lugar de eliminarla)
    $stmt = $pdo->prepare("
        UPDATE ca_citas 
        SET estado = 'cancelada'
        WHERE id = :cita_id
    ");
    $stmt->execute(['cita_id' => $cita_id]);
    
    // Log de la acción
    error_log("Usuario ID {$usuario_id} canceló la cita ID {$cita_id}");
    
    echo json_encode([
        'success' => true,
        'message' => 'Cita cancelada exitosamente'
    ]);
    
} catch (Throwable $e) {
    error_log('Error al cancelar cita: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

