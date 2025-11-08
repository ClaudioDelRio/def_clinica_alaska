<?php
/* ============================================
   ENDPOINT: AGREGAR NUEVA MASCOTA
   Clínica Veterinaria Alaska Pets Center
   ============================================ */

require_once __DIR__ . '/../config/configuracion.php';

// Configurar headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Solo acepta peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    enviarError('Método no permitido');
}

// Verificar que el usuario esté logueado
if (!estaLogueado()) {
    enviarError('Debe iniciar sesión primero');
}

$usuarioId = obtenerUsuarioId();

// Obtener los datos del formulario
$datos = json_decode(file_get_contents('php://input'), true);

if (!$datos) {
    enviarError('No se recibieron datos');
}

// Extraer y limpiar los datos
$nombre = isset($datos['nombre']) ? limpiarInput($datos['nombre']) : '';
$especie = isset($datos['especie']) ? limpiarInput($datos['especie']) : '';
$raza = isset($datos['raza']) ? limpiarInput($datos['raza']) : '';
$edad = isset($datos['edad']) ? (int)$datos['edad'] : 0;
$sexo = isset($datos['sexo']) ? limpiarInput($datos['sexo']) : '';
$peso = isset($datos['peso']) ? (float)$datos['peso'] : 0;
$color = isset($datos['color']) ? limpiarInput($datos['color']) : '';
$vacunas_al_dia = isset($datos['vacunas_al_dia']) ? (int)$datos['vacunas_al_dia'] : 0;

/* ============================================
   VALIDACIONES
   ============================================ */

$errores = [];

// Validar nombre
if (empty($nombre)) {
    $errores[] = 'El nombre de la mascota es obligatorio';
} elseif (strlen($nombre) < 2) {
    $errores[] = 'El nombre debe tener al menos 2 caracteres';
} elseif (strlen($nombre) > 50) {
    $errores[] = 'El nombre no puede exceder los 50 caracteres';
}

// Validar especie
if (empty($especie)) {
    $errores[] = 'La especie es obligatoria';
} elseif (!in_array(strtolower($especie), ['perro', 'gato', 'otro'])) {
    $errores[] = 'La especie debe ser: perro, gato u otro';
}

// Validar raza
if (empty($raza)) {
    $errores[] = 'La raza es obligatoria';
} elseif (strlen($raza) > 50) {
    $errores[] = 'La raza no puede exceder los 50 caracteres';
}

// Validar edad
if ($edad < 0 || $edad > 30) {
    $errores[] = 'La edad debe estar entre 0 y 30 años';
}

// Validar sexo
if (empty($sexo)) {
    $errores[] = 'El sexo es obligatorio';
} elseif (!in_array(strtolower($sexo), ['macho', 'hembra'])) {
    $errores[] = 'El sexo debe ser: macho o hembra';
}

// Validar peso
if ($peso <= 0 || $peso > 200) {
    $errores[] = 'El peso debe estar entre 0.1 y 200 kg';
}

// Si hay errores, devolverlos
if (!empty($errores)) {
    enviarError(implode('. ', $errores));
}

/* ============================================
   INSERTAR MASCOTA
   ============================================ */

try {
    $sql = "INSERT INTO ca_mascotas 
            (usuario_id, nombre, especie, raza, edad, sexo, peso, color, vacunas_al_dia, fecha_registro) 
            VALUES 
            (:usuario_id, :nombre, :especie, :raza, :edad, :sexo, :peso, :color, :vacunas_al_dia, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $resultado = $stmt->execute([
        'usuario_id' => $usuarioId,
        'nombre' => $nombre,
        'especie' => $especie,
        'raza' => $raza,
        'edad' => $edad,
        'sexo' => $sexo,
        'peso' => $peso,
        'color' => $color,
        'vacunas_al_dia' => $vacunas_al_dia
    ]);
    
    if ($resultado) {
        $mascotaId = $pdo->lastInsertId();
        
        enviarExito('✅ Mascota agregada exitosamente', [
            'mascota_id' => $mascotaId,
            'nombre' => $nombre
        ]);
    } else {
        enviarError('Error al agregar la mascota');
    }
    
} catch (PDOException $e) {
    error_log('Error en agregar-mascota.php: ' . $e->getMessage());
    enviarError('Error al agregar la mascota');
}

