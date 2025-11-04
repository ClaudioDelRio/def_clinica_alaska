<?php
/**
 * CREAR MASCOTA RÁPIDA DESDE ADMIN
 * Para el flujo de creación de citas
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../api/configuracion.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar si el médico está logueado y es admin
if (!isset($_SESSION['medico_id']) || !isset($_SESSION['medico_es_admin']) || !$_SESSION['medico_es_admin']) {
    die(json_encode([
        'success' => false,
        'message' => 'No autorizado. Solo administradores pueden crear mascotas.'
    ]));
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]));
}

// Obtener datos
$data = json_decode(file_get_contents('php://input'), true);
$usuario_id = isset($data['usuario_id']) ? intval($data['usuario_id']) : 0;
$nombre = isset($data['nombre']) ? trim($data['nombre']) : '';
$especie = isset($data['especie']) ? trim($data['especie']) : '';
$raza = isset($data['raza']) ? trim($data['raza']) : '';
$edad = isset($data['edad']) ? intval($data['edad']) : null;
$peso = isset($data['peso']) ? floatval($data['peso']) : null;
$sexo = isset($data['sexo']) ? trim($data['sexo']) : '';

// Validaciones
if ($usuario_id <= 0) {
    die(json_encode([
        'success' => false,
        'message' => 'El ID del usuario es inválido'
    ]));
}

if (empty($nombre)) {
    die(json_encode([
        'success' => false,
        'message' => 'El nombre de la mascota es obligatorio'
    ]));
}

if (empty($especie)) {
    die(json_encode([
        'success' => false,
        'message' => 'La especie es obligatoria'
    ]));
}

// Validar especie
$especies_validas = ['perro', 'gato', 'ave', 'roedor', 'reptil', 'otro'];
if (!in_array($especie, $especies_validas)) {
    die(json_encode([
        'success' => false,
        'message' => 'Especie inválida'
    ]));
}

// Validar sexo si se proporcionó
if (!empty($sexo)) {
    $sexos_validos = ['macho', 'hembra'];
    if (!in_array($sexo, $sexos_validos)) {
        die(json_encode([
            'success' => false,
            'message' => 'Sexo inválido'
        ]));
    }
}

try {
    // Verificar que el usuario existe
    $stmt = $pdo->prepare("SELECT id FROM ca_usuarios WHERE id = :id");
    $stmt->execute(['id' => $usuario_id]);
    if (!$stmt->fetch()) {
        throw new Exception('El usuario no existe');
    }
    
    // Insertar mascota
    $sql = "INSERT INTO ca_mascotas (usuario_id, nombre, especie, raza, edad, peso, sexo, activo, fecha_registro)
            VALUES (:usuario_id, :nombre, :especie, :raza, :edad, :peso, :sexo, 1, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'usuario_id' => $usuario_id,
        'nombre' => $nombre,
        'especie' => $especie,
        'raza' => $raza,
        'edad' => $edad,
        'peso' => $peso,
        'sexo' => $sexo
    ]);
    
    $mascota_id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Mascota creada exitosamente',
        'data' => [
            'id' => $mascota_id,
            'nombre' => $nombre,
            'especie' => $especie,
            'raza' => $raza,
            'edad' => $edad,
            'peso' => $peso,
            'sexo' => $sexo
        ]
    ]);
    
} catch (Throwable $e) {
    error_log('Error al crear mascota: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al crear la mascota: ' . $e->getMessage()
    ]);
}

