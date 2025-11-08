<?php
/**
 * GUARDAR/CREAR CLIENTE
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../config/configuracion.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Verificar si el médico está logueado
if (!isset($_SESSION['medico_id'])) {
    die(json_encode([
        'success' => false,
        'message' => 'No autorizado'
    ]));
}

// Solo acepta peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]));
}

// Obtener los datos
$datos = json_decode(file_get_contents('php://input'), true);

if (!$datos) {
    die(json_encode([
        'success' => false,
        'message' => 'No se recibieron datos'
    ]));
}

// Extraer y limpiar los datos
$id = isset($datos['id']) ? intval($datos['id']) : null;
$nombre = isset($datos['nombre']) ? trim($datos['nombre']) : '';
$email = isset($datos['email']) ? trim($datos['email']) : '';
$rut = isset($datos['rut']) ? trim($datos['rut']) : '';
$telefono = isset($datos['telefono']) ? trim($datos['telefono']) : '';
$direccion = isset($datos['direccion']) ? trim($datos['direccion']) : '';
$activo = isset($datos['activo']) ? intval($datos['activo']) : 1;
$password = isset($datos['password']) ? $datos['password'] : '';

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

if (empty($rut)) {
    die(json_encode([
        'success' => false,
        'message' => 'El RUT es obligatorio'
    ]));
}

if (empty($telefono)) {
    die(json_encode([
        'success' => false,
        'message' => 'El teléfono es obligatorio'
    ]));
}

if (empty($direccion)) {
    die(json_encode([
        'success' => false,
        'message' => 'La dirección es obligatoria'
    ]));
}

try {
    // Si hay un ID, es una edición
    if ($id && $id > 0) {
        // Verificar si el cliente existe
        $stmt = $pdo->prepare('SELECT id FROM ca_usuarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
        
        if (!$stmt->fetch()) {
            die(json_encode([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ]));
        }
        
        // Si se proporciona una nueva contraseña, actualizarla
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $sql = "UPDATE ca_usuarios 
                    SET nombre = :nombre, email = :email, rut = :rut, telefono = :telefono, 
                        direccion = :direccion, activo = :activo, password = :password 
                    WHERE id = :id";
            $params = [
                'nombre' => $nombre,
                'email' => $email,
                'rut' => $rut,
                'telefono' => $telefono,
                'direccion' => $direccion,
                'activo' => $activo,
                'password' => $passwordHash,
                'id' => $id
            ];
        } else {
            // No actualizar la contraseña
            $sql = "UPDATE ca_usuarios 
                    SET nombre = :nombre, email = :email, rut = :rut, telefono = :telefono, 
                        direccion = :direccion, activo = :activo 
                    WHERE id = :id";
            $params = [
                'nombre' => $nombre,
                'email' => $email,
                'rut' => $rut,
                'telefono' => $telefono,
                'direccion' => $direccion,
                'activo' => $activo,
                'id' => $id
            ];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        echo json_encode([
            'success' => true,
            'message' => 'Cliente actualizado correctamente',
            'data' => ['id' => $id]
        ]);
        
    } else {
        // Es una creación, la contraseña es obligatoria
        if (empty($password)) {
            die(json_encode([
                'success' => false,
                'message' => 'La contraseña es obligatoria para nuevos clientes'
            ]));
        }
        
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO ca_usuarios (nombre, email, rut, telefono, direccion, activo, password, fecha_registro) 
                VALUES (:nombre, :email, :rut, :telefono, :direccion, :activo, :password, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nombre' => $nombre,
            'email' => $email,
            'rut' => $rut,
            'telefono' => $telefono,
            'direccion' => $direccion,
            'activo' => $activo,
            'password' => $passwordHash
        ]);
        
        $nuevoId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Cliente creado correctamente',
            'data' => ['id' => $nuevoId]
        ]);
    }
    
} catch (Throwable $e) {
    error_log('Error al guardar cliente: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el cliente: ' . $e->getMessage()
    ]);
}

