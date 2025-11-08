<?php
/**
 * GUARDAR/CREAR MÉDICO
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
$especialidad = isset($datos['especialidad']) ? trim($datos['especialidad']) : null;
$telefono = isset($datos['telefono']) ? trim($datos['telefono']) : null;
$email = isset($datos['email']) ? trim($datos['email']) : null;
$es_admin = isset($datos['es_admin']) && $datos['es_admin'] ? 1 : 0;
$activo = isset($datos['activo']) ? intval($datos['activo']) : 1;
$password = isset($datos['password']) ? $datos['password'] : '';

// Validaciones
if (empty($nombre)) {
    die(json_encode([
        'success' => false,
        'message' => 'El nombre es obligatorio'
    ]));
}

try {
    // Si hay un ID, es una edición
    if ($id && $id > 0) {
        // Verificar si el médico existe
        $stmt = $pdo->prepare('SELECT id FROM ca_medicos WHERE id = :id');
        $stmt->execute(['id' => $id]);
        
        if (!$stmt->fetch()) {
            die(json_encode([
                'success' => false,
                'message' => 'Médico no encontrado'
            ]));
        }
        
        // Si se proporciona una nueva contraseña, actualizarla
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $sql = "UPDATE ca_medicos 
                    SET nombre = :nombre, especialidad = :especialidad, telefono = :telefono, 
                        email = :email, es_admin = :es_admin, activo = :activo, password_hash = :password 
                    WHERE id = :id";
            $params = [
                'nombre' => $nombre,
                'especialidad' => $especialidad,
                'telefono' => $telefono,
                'email' => $email,
                'es_admin' => $es_admin,
                'activo' => $activo,
                'password' => $passwordHash,
                'id' => $id
            ];
        } else {
            // No actualizar la contraseña
            $sql = "UPDATE ca_medicos 
                    SET nombre = :nombre, especialidad = :especialidad, telefono = :telefono, 
                        email = :email, es_admin = :es_admin, activo = :activo 
                    WHERE id = :id";
            $params = [
                'nombre' => $nombre,
                'especialidad' => $especialidad,
                'telefono' => $telefono,
                'email' => $email,
                'es_admin' => $es_admin,
                'activo' => $activo,
                'id' => $id
            ];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        echo json_encode([
            'success' => true,
            'message' => 'Médico actualizado correctamente',
            'data' => ['id' => $id]
        ]);
        
    } else {
        // Es una creación, la contraseña es obligatoria
        if (empty($password)) {
            die(json_encode([
                'success' => false,
                'message' => 'La contraseña es obligatoria para nuevos médicos'
            ]));
        }
        
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO ca_medicos (nombre, especialidad, telefono, email, es_admin, activo, password_hash) 
                VALUES (:nombre, :especialidad, :telefono, :email, :es_admin, :activo, :password)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nombre' => $nombre,
            'especialidad' => $especialidad,
            'telefono' => $telefono,
            'email' => $email,
            'es_admin' => $es_admin,
            'activo' => $activo,
            'password' => $passwordHash
        ]);
        
        $nuevoId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Médico creado correctamente',
            'data' => ['id' => $nuevoId]
        ]);
    }
    
} catch (Throwable $e) {
    error_log('Error al guardar médico: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el médico: ' . $e->getMessage()
    ]);
}

