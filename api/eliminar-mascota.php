<?php
/* ============================================
   ENDPOINT: ELIMINAR MASCOTA
   Clínica Veterinaria Alaska Pets Center
   ============================================ */

require_once __DIR__ . '/../config/configuracion.php';

// Configurar headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Solo acepta peticiones POST o DELETE
if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'DELETE'])) {
    enviarError('Método no permitido');
}

// Verificar que el usuario esté logueado
if (!estaLogueado()) {
    enviarError('Debe iniciar sesión primero');
}

$usuarioId = obtenerUsuarioId();

// Obtener los datos
$datos = json_decode(file_get_contents('php://input'), true);

if (!$datos) {
    enviarError('No se recibieron datos');
}

// Extraer ID de la mascota
$mascotaId = isset($datos['id']) ? (int)$datos['id'] : 0;
$forzar = isset($datos['force']) ? (bool)$datos['force'] : false;

if ($mascotaId <= 0) {
    enviarError('ID de mascota inválido');
}

try {
    // Verificar que la mascota pertenezca al usuario
    $sqlVerificar = "SELECT nombre FROM ca_mascotas WHERE id = :id AND usuario_id = :usuario_id";
    $stmtVerificar = $pdo->prepare($sqlVerificar);
    $stmtVerificar->execute([
        'id' => $mascotaId,
        'usuario_id' => $usuarioId
    ]);
    
    $mascota = $stmtVerificar->fetch();
    
    if (!$mascota) {
        enviarError('Mascota no encontrada o no tienes permisos para eliminarla');
    }
    
    // Verificar si hay citas pendientes o confirmadas para esta mascota
    $sqlCitas = "SELECT COUNT(*) as total 
                 FROM ca_citas 
                 WHERE mascota_id = :mascota_id 
                 AND estado IN ('pendiente', 'confirmada')
                 AND fecha_cita >= CURDATE()";
    $stmtCitas = $pdo->prepare($sqlCitas);
    $stmtCitas->execute(['mascota_id' => $mascotaId]);
    $citasPendientes = $stmtCitas->fetch()['total'];
    
    if ($citasPendientes > 0 && !$forzar) {
        enviarError("La mascota tiene $citasPendientes cita(s) pendiente(s). Se requiere confirmación.");
    }

    // Iniciar transacción para eliminar en cascada de forma segura
    $pdo->beginTransaction();

    // Eliminar todas las citas relacionadas (pendientes, confirmadas e históricas)
    $sqlDelCitas = "DELETE FROM ca_citas WHERE mascota_id = :id";
    $stmtDelCitas = $pdo->prepare($sqlDelCitas);
    $stmtDelCitas->execute(['id' => $mascotaId]);

    // Eliminar la mascota
    $sql = "DELETE FROM ca_mascotas WHERE id = :id AND usuario_id = :usuario_id";
    $stmt = $pdo->prepare($sql);
    $resultado = $stmt->execute([
        'id' => $mascotaId,
        'usuario_id' => $usuarioId
    ]);

    if ($resultado) {
        $pdo->commit();
    } else {
        $pdo->rollBack();
    }
    
    if ($resultado) {
        enviarExito('✅ Mascota eliminada exitosamente', [
            'mascota_id' => $mascotaId,
            'nombre' => $mascota['nombre']
        ]);
    } else {
        enviarError('Error al eliminar la mascota');
    }
    
} catch (PDOException $e) {
    error_log('Error en eliminar-mascota.php: ' . $e->getMessage());
    enviarError('Error al eliminar la mascota');
}

