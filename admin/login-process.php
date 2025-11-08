<?php
/**
 * PROCESAMIENTO DE LOGIN DE INTRANET
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../config/configuracion.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Solo acepta peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    enviarRespuesta(false, 'Método no permitido');
}

// Obtener los datos del formulario
$datos = json_decode(file_get_contents('php://input'), true);

// Validar que se recibieron los datos
if (!$datos) {
    enviarRespuesta(false, 'No se recibieron datos');
}

// Extraer y limpiar los datos
$email = isset($datos['email']) ? limpiarInput($datos['email']) : '';
$password = isset($datos['password']) ? $datos['password'] : '';

/* ============================================
   VALIDACIONES
   ============================================ */

// Validar email
if (empty($email)) {
    enviarRespuesta(false, 'El correo electrónico es obligatorio');
} elseif (!validarEmail($email)) {
    enviarRespuesta(false, 'El formato del correo electrónico no es válido');
}

// Validar contraseña
if (empty($password)) {
    enviarRespuesta(false, 'La contraseña es obligatoria');
}

/* ============================================
   VERIFICAR CREDENCIALES DE MÉDICO
   ============================================ */

try {
    // Buscar médico por email
    $sql = "SELECT id, nombre, email, password_hash, es_admin, activo FROM ca_medicos WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    
    $medico = $stmt->fetch();
    
    if (!$medico) {
        // No revelar si el email existe o no por seguridad
        enviarRespuesta(false, 'Correo electrónico o contraseña incorrectos');
    }
    
    // Verificar si el médico está activo
    if (!$medico['activo']) {
        enviarRespuesta(false, 'Su cuenta ha sido desactivada. Contacte al administrador.');
    }
    
    // Verificar la contraseña
    if (!password_verify($password, $medico['password_hash'])) {
        enviarRespuesta(false, 'Correo electrónico o contraseña incorrectos');
    }
    
    // Credenciales correctas - Iniciar sesión
    $_SESSION['medico_id'] = $medico['id'];
    $_SESSION['medico_nombre'] = $medico['nombre'];
    $_SESSION['medico_email'] = $medico['email'];
    $_SESSION['medico_es_admin'] = $medico['es_admin'];
    
    enviarRespuesta(true, '¡Inicio de sesión exitoso!', [
        'medico' => [
            'id' => $medico['id'],
            'nombre' => $medico['nombre'],
            'email' => $medico['email'],
            'es_admin' => $medico['es_admin']
        ]
    ]);
    
} catch (PDOException $e) {
    error_log('Error en login de intranet: ' . $e->getMessage());
    enviarRespuesta(false, 'Error en el servidor. Por favor, intente más tarde.');
}

