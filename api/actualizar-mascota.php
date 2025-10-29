<?php
/* ============================================
   ENDPOINT: ACTUALIZAR MASCOTA
   Clínica Veterinaria Alaska Pets Center
   ============================================ */

require_once 'configuracion.php';

// Configurar headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Solo acepta peticiones POST o PUT
if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT'])) {
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

if ($mascotaId <= 0) {
    enviarError('ID de mascota inválido');
}

// Verificar que la mascota pertenezca al usuario
try {
    $sqlVerificar = "SELECT id FROM ca_mascotas WHERE id = :id AND usuario_id = :usuario_id";
    $stmtVerificar = $pdo->prepare($sqlVerificar);
    $stmtVerificar->execute([
        'id' => $mascotaId,
        'usuario_id' => $usuarioId
    ]);
    
    if (!$stmtVerificar->fetch()) {
        enviarError('Mascota no encontrada o no tienes permisos para editarla');
    }
} catch (PDOException $e) {
    error_log('Error en actualizar-mascota.php (verificar): ' . $e->getMessage());
    enviarError('Error al verificar la mascota');
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

if (empty($nombre)) {
    $errores[] = 'El nombre de la mascota es obligatorio';
} elseif (strlen($nombre) < 2) {
    $errores[] = 'El nombre debe tener al menos 2 caracteres';
}

if (empty($especie) || !in_array(strtolower($especie), ['perro', 'gato', 'otro'])) {
    $errores[] = 'La especie debe ser: perro, gato u otro';
}

if (empty($raza)) {
    $errores[] = 'La raza es obligatoria';
}

if ($edad < 0 || $edad > 30) {
    $errores[] = 'La edad debe estar entre 0 y 30 años';
}

if (empty($sexo) || !in_array(strtolower($sexo), ['macho', 'hembra'])) {
    $errores[] = 'El sexo debe ser: macho o hembra';
}

if ($peso <= 0 || $peso > 200) {
    $errores[] = 'El peso debe estar entre 0.1 y 200 kg';
}

if (!empty($errores)) {
    enviarError(implode('. ', $errores));
}

/* ============================================
   ACTUALIZAR MASCOTA
   ============================================ */

try {
    $sql = "UPDATE ca_mascotas SET 
            nombre = :nombre,
            especie = :especie,
            raza = :raza,
            edad = :edad,
            sexo = :sexo,
            peso = :peso,
            color = :color,
            vacunas_al_dia = :vacunas_al_dia
            WHERE id = :id AND usuario_id = :usuario_id";
    
    $stmt = $pdo->prepare($sql);
    $resultado = $stmt->execute([
        'nombre' => $nombre,
        'especie' => $especie,
        'raza' => $raza,
        'edad' => $edad,
        'sexo' => $sexo,
        'peso' => $peso,
        'color' => $color,
        'vacunas_al_dia' => $vacunas_al_dia,
        'id' => $mascotaId,
        'usuario_id' => $usuarioId
    ]);
    
    if ($resultado) {
        enviarExito('✅ Mascota actualizada exitosamente', [
            'mascota_id' => $mascotaId,
            'nombre' => $nombre
        ]);
    } else {
        enviarError('Error al actualizar la mascota');
    }
    
} catch (PDOException $e) {
    error_log('Error en actualizar-mascota.php: ' . $e->getMessage());
    enviarError('Error al actualizar la mascota');
}

