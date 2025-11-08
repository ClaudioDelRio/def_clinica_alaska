<?php
/**
 * CREAR CLIENTE RÁPIDO DESDE ADMIN
 * Para el flujo de creación de citas
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../config/configuracion.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar si el médico está logueado y es admin
if (!isset($_SESSION['medico_id']) || !isset($_SESSION['medico_es_admin']) || !$_SESSION['medico_es_admin']) {
    die(json_encode([
        'success' => false,
        'message' => 'No autorizado. Solo administradores pueden crear clientes.'
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
$nombre = isset($data['nombre']) ? trim($data['nombre']) : '';
$email = isset($data['email']) ? trim($data['email']) : '';
$rut = isset($data['rut']) ? trim($data['rut']) : '';
$telefono = isset($data['telefono']) ? trim($data['telefono']) : '';
$direccion = isset($data['direccion']) ? trim($data['direccion']) : '';

// Validaciones
if (empty($nombre)) {
    die(json_encode([
        'success' => false,
        'message' => 'El nombre es obligatorio'
    ]));
}

if (empty($email)) {
    die(json_encode([
        'success' => false,
        'message' => 'El email es obligatorio'
    ]));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode([
        'success' => false,
        'message' => 'El email no es válido'
    ]));
}

if (empty($telefono)) {
    die(json_encode([
        'success' => false,
        'message' => 'El teléfono es obligatorio'
    ]));
}

try {
    // Verificar si el email ya existe
    $stmt = $pdo->prepare("SELECT id FROM ca_usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        throw new Exception('El email ya está registrado');
    }
    
    // Verificar si el RUT ya existe (si se proporcionó)
    if (!empty($rut)) {
        $stmt = $pdo->prepare("SELECT id FROM ca_usuarios WHERE rut = :rut");
        $stmt->execute(['rut' => $rut]);
        if ($stmt->fetch()) {
            throw new Exception('El RUT ya está registrado');
        }
    }
    
    // Crear contraseña temporal
    $password_temporal = bin2hex(random_bytes(4)); // 8 caracteres aleatorios
    $password_hash = password_hash($password_temporal, PASSWORD_DEFAULT);
    
    // Insertar cliente
    $sql = "INSERT INTO ca_usuarios (nombre, email, rut, telefono, direccion, password, activo, fecha_registro)
            VALUES (:nombre, :email, :rut, :telefono, :direccion, :password, 1, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nombre' => $nombre,
        'email' => $email,
        'rut' => $rut,
        'telefono' => $telefono,
        'direccion' => $direccion,
        'password' => $password_hash
    ]);
    
    $cliente_id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Cliente creado exitosamente',
        'data' => [
            'id' => $cliente_id,
            'nombre' => $nombre,
            'email' => $email,
            'rut' => $rut,
            'telefono' => $telefono,
            'direccion' => $direccion,
            'password_temporal' => $password_temporal
        ]
    ]);
    
} catch (Throwable $e) {
    error_log('Error al crear cliente: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al crear el cliente: ' . $e->getMessage()
    ]);
}

