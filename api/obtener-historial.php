<?php
/* ============================================
   ENDPOINT: OBTENER HISTORIAL DE CITAS
   Clínica Veterinaria Alaska Pets Center
   ============================================ */

require_once 'configuracion.php';

// Configurar headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Verificar que el usuario esté logueado
if (!estaLogueado()) {
    enviarError('Debe iniciar sesión primero');
}

$usuarioId = obtenerUsuarioId();

try {
    $sql = "SELECT 
                c.id,
                c.servicio,
                c.fecha_cita,
                c.hora_cita,
                c.motivo,
                c.estado,
                c.observaciones,
                c.fecha_creacion,
                m.nombre as mascota_nombre,
                m.especie as mascota_especie,
                d.nombre as doctor_nombre,
                d.especialidad as doctor_especialidad
            FROM ca_citas c
            INNER JOIN ca_mascotas m ON c.mascota_id = m.id
            LEFT JOIN ca_medicos d ON c.doctor_id = d.id
            WHERE c.usuario_id = :usuario_id
            ORDER BY c.fecha_cita DESC, c.hora_cita DESC
            LIMIT 50";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['usuario_id' => $usuarioId]);
    $citas = $stmt->fetchAll();
    
    // Formatear datos
    $citasFormateadas = [];
    foreach ($citas as $cita) {
        // Determinar el icono según el servicio
        $icono = obtenerIconoServicio($cita['servicio']);
        
        // Determinar clase de badge según estado
        $badgeClasses = [
            'pendiente' => 'badge-pendiente',
            'confirmada' => 'badge-pendiente',
            'completada' => 'badge-completado',
            'cancelada' => 'badge-cancelado'
        ];
        
        $badgeClass = $badgeClasses[$cita['estado']] ?? 'badge-pendiente';
        
        $citasFormateadas[] = [
            'id' => $cita['id'],
            'servicio' => SERVICIOS[$cita['servicio']] ?? $cita['servicio'],
            'servicio_key' => $cita['servicio'],
            'fecha_cita' => $cita['fecha_cita'],
            'fecha_formateada' => formatearFecha($cita['fecha_cita']),
            'hora_cita' => $cita['hora_cita'],
            'motivo' => $cita['motivo'],
            'estado' => $cita['estado'],
            'estado_texto' => ESTADOS_CITA[$cita['estado']] ?? $cita['estado'],
            'observaciones' => $cita['observaciones'],
            'mascota_nombre' => $cita['mascota_nombre'],
            'mascota_especie' => $cita['mascota_especie'],
            'doctor_nombre' => $cita['doctor_nombre'],
            'doctor_especialidad' => $cita['doctor_especialidad'],
            'icono' => $icono,
            'badge_class' => $badgeClass,
            'fecha_creacion' => $cita['fecha_creacion']
        ];
    }
    
    enviarExito('Historial obtenido exitosamente', [
        'citas' => $citasFormateadas,
        'total' => count($citasFormateadas)
    ]);
    
} catch (PDOException $e) {
    error_log('Error en obtener-historial.php: ' . $e->getMessage());
    enviarError('Error al obtener el historial');
}

