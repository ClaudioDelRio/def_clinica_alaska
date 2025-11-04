<?php
/**
 * OBTENER DATOS DE UNA CITA
 * Para cargar la información en el formulario de edición
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

// Obtener ID de la cita
$cita_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($cita_id <= 0) {
    die(json_encode([
        'success' => false,
        'message' => 'ID de cita inválido'
    ]));
}

try {
    $sql = "
        SELECT 
            c.id,
            c.fecha_cita,
            c.hora_cita,
            c.estado,
            c.servicio,
            c.motivo,
            c.usuario_id,
            c.mascota_id,
            c.doctor_id,
            u.nombre as cliente_nombre,
            u.email as cliente_email,
            u.telefono as cliente_telefono,
            m.nombre as mascota_nombre,
            m.especie as mascota_especie,
            m.raza as mascota_raza,
            d.nombre as doctor_nombre
        FROM ca_citas c
        INNER JOIN ca_usuarios u ON c.usuario_id = u.id
        INNER JOIN ca_mascotas m ON c.mascota_id = m.id
        LEFT JOIN ca_medicos d ON c.doctor_id = d.id
        WHERE c.id = :id
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $cita_id]);
    $cita = $stmt->fetch();
    
    if (!$cita) {
        throw new Exception('La cita no existe');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $cita
    ]);
    
} catch (Throwable $e) {
    error_log('Error al obtener cita: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener la cita: ' . $e->getMessage()
    ]);
}

