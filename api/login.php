<?php
/* ============================================
   ENDPOINT DE INICIO DE SESIÓN
   ============================================ */

require_once 'config.php';

// Permitir peticiones CORS (si es necesario)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Solo acepta peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    enviarRespuesta(false, 'Método no permitido');
}

// Rate limiting: máximo 5 intentos de login por minuto
if (!verificarRateLimit('login', 5, 60)) {
    enviarRespuesta(false, 'Demasiados intentos de inicio de sesión. Por favor, espera un minuto.');
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

$errores = [];

// Validar email
if (empty($email)) {
    $errores[] = 'El correo electrónico es obligatorio';
} elseif (!validarEmail($email)) {
    $errores[] = 'El formato del correo electrónico no es válido';
}

// Validar contraseña
if (empty($password)) {
    $errores[] = 'La contraseña es obligatoria';
}

// Si hay errores, devolverlos
if (!empty($errores)) {
    enviarRespuesta(false, implode('. ', $errores));
}

/* ============================================
   VERIFICAR CREDENCIALES
   ============================================ */

try {
    $sql = "SELECT id, nombre, email, rut, password FROM ca_usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        // No revelar si el email existe o no por seguridad
        enviarRespuesta(false, 'Correo electrónico o contraseña incorrectos');
    }
    
    // Verificar la contraseña
    if (!password_verify($password, $usuario['password'])) {
        enviarRespuesta(false, 'Correo electrónico o contraseña incorrectos');
    }
    
    // Credenciales correctas - Iniciar sesión
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_rut'] = $usuario['rut'];
    
    // Actualizar último acceso
    $sqlUpdate = "UPDATE ca_usuarios SET ultimo_acceso = NOW() WHERE id = :id";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->execute(['id' => $usuario['id']]);
    
    enviarRespuesta(true, '¡Inicio de sesión exitoso!', [
        'usuario' => [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'email' => $usuario['email'],
            'rut' => $usuario['rut']
        ]
    ]);
    
} catch (PDOException $e) {
    enviarRespuesta(false, 'Error en el servidor. Por favor, intente más tarde.');
}
