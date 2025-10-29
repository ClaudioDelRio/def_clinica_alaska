<?php
/* ============================================
   ENDPOINT: OBTENER DATOS DEL USUARIO
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
    // Obtener información del usuario
    $sql = "SELECT id, nombre, email, telefono, direccion, fecha_registro, ultimo_acceso 
            FROM ca_usuarios 
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $usuarioId]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        enviarError('Usuario no encontrado');
    }
    
    // Contar mascotas del usuario
    $sqlMascotas = "SELECT COUNT(*) as total FROM ca_mascotas WHERE usuario_id = :usuario_id";
    $stmtMascotas = $pdo->prepare($sqlMascotas);
    $stmtMascotas->execute(['usuario_id' => $usuarioId]);
    $totalMascotas = $stmtMascotas->fetch()['total'];
    
    // Contar citas totales
    $sqlCitas = "SELECT COUNT(*) as total FROM ca_citas WHERE usuario_id = :usuario_id";
    $stmtCitas = $pdo->prepare($sqlCitas);
    $stmtCitas->execute(['usuario_id' => $usuarioId]);
    $totalCitas = $stmtCitas->fetch()['total'];
    
    // Obtener próxima cita
    $sqlProximaCita = "SELECT fecha_cita, hora_cita 
                       FROM ca_citas 
                       WHERE usuario_id = :usuario_id 
                       AND fecha_cita >= CURDATE() 
                       AND estado IN ('pendiente', 'confirmada')
                       ORDER BY fecha_cita ASC, hora_cita ASC 
                       LIMIT 1";
    $stmtProxima = $pdo->prepare($sqlProximaCita);
    $stmtProxima->execute(['usuario_id' => $usuarioId]);
    $proximaCita = $stmtProxima->fetch();
    
    $proximaCitaTexto = 'Sin citas programadas';
    if ($proximaCita) {
        $proximaCitaTexto = formatearFecha($proximaCita['fecha_cita']);
    }
    
    // Preparar respuesta
    $response = [
        'usuario' => [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'email' => $usuario['email'],
            'telefono' => $usuario['telefono'],
            'direccion' => $usuario['direccion'],
            'iniciales' => obtenerIniciales($usuario['nombre']),
            'fecha_registro' => formatearFecha($usuario['fecha_registro']),
            'ultimo_acceso' => $usuario['ultimo_acceso']
        ],
        'estadisticas' => [
            'total_mascotas' => $totalMascotas,
            'total_citas' => $totalCitas,
            'proxima_cita' => $proximaCitaTexto
        ]
    ];
    
    enviarExito('Datos obtenidos exitosamente', $response);
    
} catch (PDOException $e) {
    error_log('Error en obtener-datos-usuario.php: ' . $e->getMessage());
    enviarError('Error al obtener datos del usuario');
}

