<?php
/* ============================================
   ENDPOINT: OBTENER CONTADOR DE CITAS POR MASCOTA
   Devuelve cantidad de citas pendientes/confirmadas futuras y total relacionadas
   ============================================ */

require_once 'configuracion.php';

// CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Verificar sesión
if (!estaLogueado()) {
    enviarError('Debe iniciar sesión primero');
}

$usuarioId = obtenerUsuarioId();

// Validar parámetros
$mascotaId = isset($_GET['mascota_id']) ? (int)$_GET['mascota_id'] : 0;
if ($mascotaId <= 0) {
    enviarError('Parámetro mascota_id inválido');
}

try {
    // Verificar pertenencia de la mascota
    $stmt = $pdo->prepare("SELECT id, nombre FROM ca_mascotas WHERE id = :id AND usuario_id = :usuario_id");
    $stmt->execute(['id' => $mascotaId, 'usuario_id' => $usuarioId]);
    $mascota = $stmt->fetch();
    if (!$mascota) {
        enviarError('Mascota no encontrada o no te pertenece');
    }

    // Contar citas pendientes/confirmadas futuras
    $stmtPend = $pdo->prepare("SELECT COUNT(*) as total FROM ca_citas WHERE mascota_id = :id AND estado IN ('pendiente','confirmada') AND fecha_cita >= CURDATE()");
    $stmtPend->execute(['id' => $mascotaId]);
    $pendientes = (int)$stmtPend->fetch()['total'];

    // Contar todas las citas relacionadas (históricas incluidas)
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) as total FROM ca_citas WHERE mascota_id = :id");
    $stmtTotal->execute(['id' => $mascotaId]);
    $totalRelacionadas = (int)$stmtTotal->fetch()['total'];

    enviarExito('Conteo obtenido', [
        'mascota_id' => $mascotaId,
        'mascota_nombre' => $mascota['nombre'],
        'pendientes' => $pendientes,
        'total_relacionadas' => $totalRelacionadas,
    ]);
} catch (PDOException $e) {
    error_log('Error en obtener-citas-pendientes-por-mascota.php: ' . $e->getMessage());
    enviarError('Error al obtener el conteo de citas');
}


