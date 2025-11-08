<?php
/**
 * OBTENER CITAS PARA CALENDARIO
 * Retorna citas por rango de fechas para visualización en calendario
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

// Obtener parámetros
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null; // Para vista diaria

try {
    if ($fecha) {
        // Vista diaria: obtener citas de un día específico
        $sql = "
            SELECT 
                c.id,
                c.fecha_cita,
                c.hora_cita,
                c.estado,
                c.servicio,
                c.motivo,
                u.nombre as cliente_nombre,
                u.email as cliente_email,
                u.telefono as cliente_telefono,
                m.nombre as mascota_nombre,
                m.especie as mascota_especie,
                d.nombre as doctor_nombre
            FROM ca_citas c
            INNER JOIN ca_usuarios u ON c.usuario_id = u.id
            INNER JOIN ca_mascotas m ON c.mascota_id = m.id
            LEFT JOIN ca_medicos d ON c.doctor_id = d.id
            WHERE c.fecha_cita = :fecha
            ORDER BY c.hora_cita ASC
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['fecha' => $fecha]);
    } else {
        // Vista mensual: obtener citas por rango de fechas
        $sql = "
            SELECT 
                c.id,
                c.fecha_cita,
                c.hora_cita,
                c.estado,
                c.servicio,
                u.nombre as cliente_nombre,
                m.nombre as mascota_nombre,
                d.nombre as doctor_nombre
            FROM ca_citas c
            INNER JOIN ca_usuarios u ON c.usuario_id = u.id
            INNER JOIN ca_mascotas m ON c.mascota_id = m.id
            LEFT JOIN ca_medicos d ON c.doctor_id = d.id
            WHERE c.fecha_cita BETWEEN :fecha_inicio AND :fecha_fin
            ORDER BY c.fecha_cita ASC, c.hora_cita ASC
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
        ]);
    }
    
    $citas = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $citas
    ]);
    
} catch (Throwable $e) {
    error_log('Error al obtener citas del calendario: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener citas'
    ]);
}

