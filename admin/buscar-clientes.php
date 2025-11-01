<?php
/**
 * BUSCAR CLIENTES
 * Búsqueda dinámica por nombre, RUT o nombre de mascota
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

// Obtener término de búsqueda
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($searchTerm)) {
    die(json_encode([
        'success' => true,
        'data' => [],
        'count' => 0
    ]));
}

try {
    // Preparar término de búsqueda para LIKE
    $searchPattern = '%' . $searchTerm . '%';
    
    // Búsqueda simple en la tabla de usuarios
    $sql = "
        SELECT id, nombre, email, rut, telefono, direccion, activo, fecha_registro, ultimo_acceso
        FROM ca_usuarios
        WHERE (
            nombre LIKE :search1 
            OR rut LIKE :search2 
            OR email LIKE :search3
            OR telefono LIKE :search4
        )
        ORDER BY nombre ASC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'search1' => $searchPattern,
        'search2' => $searchPattern,
        'search3' => $searchPattern,
        'search4' => $searchPattern
    ]);
    
    $clientes = $stmt->fetchAll();
    
    // Obtener IDs de clientes encontrados hasta ahora
    $idsClientes = array_column($clientes, 'id');
    
    // Buscar mascotas que coincidan
    $sqlMascotas = "
        SELECT DISTINCT usuario_id 
        FROM ca_mascotas 
        WHERE nombre LIKE :search
    ";
    
    $stmtMascotas = $pdo->prepare($sqlMascotas);
    $stmtMascotas->execute(['search' => $searchPattern]);
    $mascotasEncontradas = $stmtMascotas->fetchAll();
    
    // Si hay mascotas que coinciden, agregar sus dueños
    if (count($mascotasEncontradas) > 0) {
        $idsMascotas = array_column($mascotasEncontradas, 'usuario_id');
        
        // Obtener clientes de las mascotas que no están ya en los resultados
        $nuevosIds = array_diff($idsMascotas, $idsClientes);
        
        if (count($nuevosIds) > 0) {
            $placeholders = implode(',', array_fill(0, count($nuevosIds), '?'));
            $sqlNuevosClientes = "
                SELECT id, nombre, email, rut, telefono, direccion, activo, fecha_registro, ultimo_acceso
                FROM ca_usuarios
                WHERE id IN ($placeholders)
                ORDER BY nombre ASC
            ";
            
            $stmtNuevos = $pdo->prepare($sqlNuevosClientes);
            $stmtNuevos->execute(array_values($nuevosIds));
            $clientesAdicionales = $stmtNuevos->fetchAll();
            
            // Combinar resultados
            $clientes = array_merge($clientes, $clientesAdicionales);
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $clientes,
        'count' => count($clientes)
    ]);
    
} catch (Throwable $e) {
    error_log('Error al buscar clientes: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al realizar la búsqueda: ' . $e->getMessage()
    ]);
}
