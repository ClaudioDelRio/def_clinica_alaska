<?php
/* ============================================
   ENDPOINT DE REGISTRO DE USUARIOS
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

// Rate limiting: máximo 3 intentos de registro por minuto
if (!verificarRateLimit('registro', 3, 60)) {
    enviarRespuesta(false, 'Demasiados intentos. Por favor, espera un minuto.');
}

// Obtener los datos del formulario
$datos = json_decode(file_get_contents('php://input'), true);

// Validar que se recibieron los datos
if (!$datos) {
    enviarRespuesta(false, 'No se recibieron datos');
}

// Extraer y limpiar los datos
$nombre = isset($datos['nombre']) ? limpiarInput($datos['nombre']) : '';
$email = isset($datos['email']) ? limpiarInput($datos['email']) : '';
$rut = isset($datos['rut']) ? limpiarInput($datos['rut']) : '';
$telefono = isset($datos['telefono']) ? limpiarInput($datos['telefono']) : '';
$direccion = isset($datos['direccion']) ? limpiarInput($datos['direccion']) : '';
$password = isset($datos['password']) ? $datos['password'] : '';

/* ============================================
   VALIDACIONES
   ============================================ */

$errores = [];

// Validar nombre
if (empty($nombre)) {
    $errores[] = 'El nombre es obligatorio';
} elseif (strlen($nombre) < 3) {
    $errores[] = 'El nombre debe tener al menos 3 caracteres';
} elseif (strlen($nombre) > 100) {
    $errores[] = 'El nombre no puede exceder los 100 caracteres';
}

// Validar email
if (empty($email)) {
    $errores[] = 'El correo electrónico es obligatorio';
} elseif (!validarEmail($email)) {
    $errores[] = 'El formato del correo electrónico no es válido';
}

// Validar RUT (OBLIGATORIO)
if (empty($rut)) {
    $errores[] = 'El RUT es obligatorio';
} elseif (!validarRUT($rut)) {
    $errores[] = 'El RUT ingresado no es válido. Formato: 12.345.678-9';
}

// Validar teléfono
if (empty($telefono)) {
    $errores[] = 'El teléfono es obligatorio';
} elseif (!validarTelefono($telefono)) {
    $errores[] = 'El formato del teléfono no es válido. Use formato chileno: +56912345678 o 912345678';
}

// Validar dirección
if (empty($direccion)) {
    $errores[] = 'La dirección es obligatoria';
} elseif (strlen($direccion) < 5) {
    $errores[] = 'La dirección debe tener al menos 5 caracteres';
} elseif (strlen($direccion) > 200) {
    $errores[] = 'La dirección no puede exceder los 200 caracteres';
}

// Validar contraseña
if (empty($password)) {
    $errores[] = 'La contraseña es obligatoria';
} elseif (strlen($password) < 6) {
    $errores[] = 'La contraseña debe tener al menos 6 caracteres';
} elseif (strlen($password) > 255) {
    $errores[] = 'La contraseña no puede exceder los 255 caracteres';
}

// Si hay errores, devolverlos
if (!empty($errores)) {
    enviarRespuesta(false, implode('. ', $errores));
}

/* ============================================
   VERIFICAR SI EL EMAIL O RUT YA EXISTEN
   ============================================ */

try {
    // Verificar email
    $sql = "SELECT id FROM ca_usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    
    if ($stmt->fetch()) {
        enviarRespuesta(false, 'Este correo electrónico ya está registrado');
    }
    
    // Verificar RUT duplicado
    $rutFormateado = formatearRUT($rut);
    $sql = "SELECT id FROM ca_usuarios WHERE rut = :rut";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['rut' => $rutFormateado]);
    
    if ($stmt->fetch()) {
        enviarRespuesta(false, 'Este RUT ya está registrado');
    }
    
} catch (PDOException $e) {
    // En desarrollo, mostrar el error real
    error_log('Error en verificación: ' . $e->getMessage());
    enviarRespuesta(false, 'Error al verificar los datos: ' . $e->getMessage());
}

/* ============================================
   REGISTRAR USUARIO
   ============================================ */

try {
    // Encriptar la contraseña
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    
    // Formatear el RUT
    $rutFormateado = formatearRUT($rut);
    
    // Insertar en la base de datos
    $sql = "INSERT INTO ca_usuarios (nombre, email, rut, telefono, direccion, password, fecha_registro) 
            VALUES (:nombre, :email, :rut, :telefono, :direccion, :password, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $resultado = $stmt->execute([
        'nombre' => $nombre,
        'email' => $email,
        'rut' => $rutFormateado,
        'telefono' => $telefono,
        'direccion' => $direccion,
        'password' => $passwordHash
    ]);
    
    if ($resultado) {
        // Obtener el ID del usuario recién creado
        $usuario_id = $pdo->lastInsertId();
        
        // Iniciar sesión automáticamente
        $_SESSION['usuario_id'] = $usuario_id;
        $_SESSION['usuario_nombre'] = $nombre;
        $_SESSION['usuario_email'] = $email;
        $_SESSION['usuario_rut'] = $rutFormateado;
        
        enviarRespuesta(true, 'Registro exitoso. ¡Bienvenido/a!', [
            'usuario' => [
                'id' => $usuario_id,
                'nombre' => $nombre,
                'email' => $email,
                'rut' => $rutFormateado
            ]
        ]);
    } else {
        enviarRespuesta(false, 'Error al registrar el usuario');
    }
    
} catch (PDOException $e) {
    // En desarrollo, mostrar el error real
    error_log('Error en registro: ' . $e->getMessage());
    enviarRespuesta(false, 'Error en el servidor: ' . $e->getMessage());
}
