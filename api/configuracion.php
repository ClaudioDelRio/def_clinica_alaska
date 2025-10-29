<?php
/* ============================================
   ARCHIVO DE CONFIGURACIÓN PRINCIPAL
   Clínica Veterinaria Alaska Pets Center
   Desarrollado por: Claudio del Rio - Web.malgarini®
   ============================================ */

// Iniciar sesión
session_start();

// Configuración de errores (cambiar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php-errors.log');

/* ============================================
   CONFIGURACIÓN DE BASE DE DATOS
   ============================================ */

// Credenciales de conexión
define('DB_HOST', 'cldelrio.laboratoriodiseno.cl');
define('DB_NAME', 'cldelriolaborato_c_alaska');
define('DB_USER', 'cldelriolaborato_c_delriom');
define('DB_PASS', 'vO)8Yx7[I-4~BXVo');
define('DB_CHARSET', 'utf8mb4');

// Crear conexión PDO
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log('Error de conexión: ' . $e->getMessage());
    die(json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos'
    ]));
}

// Incluir clase DB para consultas alternativas si existe
if (file_exists(__DIR__ . '/db.php')) {
    require_once __DIR__ . '/db.php';
    $db = new DB(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_CHARSET);
}

/* ============================================
   CONSTANTES DE LA APLICACIÓN
   ============================================ */

define('TIMEZONE', 'America/Santiago');
date_default_timezone_set(TIMEZONE);

// Tipos de servicios disponibles
define('SERVICIOS', [
    'consulta' => 'Consulta General',
    'vacunacion' => 'Vacunación',
    'cirugia' => 'Cirugía',
    'radiologia' => 'Radiología',
    'laboratorio' => 'Exámenes de Laboratorio',
    'peluqueria' => 'Peluquería',
    'emergencia' => 'Emergencia'
]);

// Estados de citas
define('ESTADOS_CITA', [
    'pendiente' => 'Pendiente',
    'confirmada' => 'Confirmada',
    'completada' => 'Completada',
    'cancelada' => 'Cancelada'
]);

// Horarios disponibles (cada 30 minutos)
define('HORARIOS', [
    // Bloque mañana: 10:00 - 13:00
    '10:00', '10:30', '11:00', '11:30', '12:00', '12:30',
    // Bloque tarde: 15:00 - 19:00
    '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30'
]);

/* ============================================
   FUNCIONES DE SESIÓN
   ============================================ */

/**
 * Verifica si el usuario está logueado
 * @return bool
 */
function estaLogueado() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

/**
 * Requiere que el usuario esté logueado
 * Redirige al login si no lo está
 */
function requerirLogin() {
    if (!estaLogueado()) {
        enviarRespuesta(false, 'Sesión no válida. Por favor, inicia sesión.');
    }
}

/**
 * Obtiene el ID del usuario actual
 * @return int|null
 */
function obtenerUsuarioId() {
    return $_SESSION['usuario_id'] ?? null;
}

/**
 * Obtiene los datos del usuario actual
 * @return array|null
 */
function obtenerUsuarioActual() {
    if (!estaLogueado()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['usuario_id'],
        'nombre' => $_SESSION['usuario_nombre'] ?? '',
        'email' => $_SESSION['usuario_email'] ?? ''
    ];
}

/* ============================================
   FUNCIONES DE VALIDACIÓN
   ============================================ */

/**
 * Valida un email
 * @param string $email
 * @return bool
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida un teléfono chileno
 * @param string $telefono
 * @return bool
 */
function validarTelefono($telefono) {
    // Permitir formatos: +56912345678, 912345678, 642270539, etc.
    $patron = '/^(\+?56)?([2-9]\d{7,8})$/';
    return preg_match($patron, $telefono);
}

/**
 * Limpia una entrada de texto
 * @param string $input
 * @return string
 */
function limpiarInput($input) {
    $input = trim($input);
    $input = strip_tags($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/* ============================================
   FUNCIONES DE RESPUESTA JSON
   ============================================ */

/**
 * Envía una respuesta JSON y termina la ejecución
 * @param bool $success
 * @param string $message
 * @param array $data
 */
function enviarRespuesta($success, $message, $data = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Envía respuesta de error
 * @param string $message
 */
function enviarError($message) {
    enviarRespuesta(false, $message);
}

/**
 * Envía respuesta de éxito
 * @param string $message
 * @param array $data
 */
function enviarExito($message, $data = []) {
    enviarRespuesta(true, $message, $data);
}

/* ============================================
   CONSTANTES DEL SISTEMA
   ============================================ */

// Servicios disponibles
define('SERVICIOS', [
    'consulta' => 'Consulta General',
    'vacunacion' => 'Vacunación',
    'cirugia' => 'Cirugía',
    'radiologia' => 'Radiología',
    'laboratorio' => 'Exámenes de Laboratorio',
    'peluqueria' => 'Peluquería',
    'emergencia' => 'Emergencia'
]);

// Estados de citas
define('ESTADOS_CITA', [
    'pendiente' => 'Pendiente',
    'confirmada' => 'Confirmada',
    'completada' => 'Completada',
    'cancelada' => 'Cancelada'
]);

// Horarios de atención
define('HORA_INICIO_AM', '10:00');
define('HORA_FIN_AM', '13:00');
define('HORA_INICIO_PM', '15:00');
define('HORA_FIN_PM', '19:00');
define('INTERVALO_MINUTOS', 30);

// Límite de citas por horario
define('MAX_CITAS_POR_HORA', 3);

/* ============================================
   FUNCIONES AUXILIARES
   ============================================ */

/**
 * Obtiene las iniciales de un nombre
 * @param string $nombre
 * @return string
 */
function obtenerIniciales($nombre) {
    $palabras = explode(' ', $nombre);
    $iniciales = '';
    foreach ($palabras as $palabra) {
        if (!empty($palabra)) {
            $iniciales .= mb_strtoupper(mb_substr($palabra, 0, 1));
            if (strlen($iniciales) >= 2) break;
        }
    }
    return $iniciales ?: 'U';
}

/**
 * Obtiene el icono según la especie
 * @param string $especie
 * @return string
 */
function obtenerIconoEspecie($especie) {
    switch ($especie) {
        case 'perro':
            return 'fa-dog';
        case 'gato':
            return 'fa-cat';
        default:
            return 'fa-paw';
    }
}

/**
 * Obtiene el icono según el servicio
 * @param string $servicio
 * @return string
 */
function obtenerIconoServicio($servicio) {
    $iconos = [
        'consulta' => 'fa-stethoscope',
        'vacunacion' => 'fa-syringe',
        'cirugia' => 'fa-user-md',
        'radiologia' => 'fa-x-ray',
        'laboratorio' => 'fa-flask',
        'peluqueria' => 'fa-cut',
        'emergencia' => 'fa-ambulance'
    ];
    return $iconos[$servicio] ?? 'fa-calendar';
}

/**
 * Obtiene la clase CSS según el estado
 * @param string $estado
 * @return string
 */
function obtenerClaseEstado($estado) {
    $clases = [
        'pendiente' => 'dashboard-badge-pendiente',
        'confirmada' => 'dashboard-badge-pendiente',
        'completada' => 'dashboard-badge-completado',
        'cancelada' => 'dashboard-badge-cancelado'
    ];
    return $clases[$estado] ?? 'dashboard-badge-pendiente';
}

/**
 * Formatea una fecha en español
 * @param string $fecha
 * @return string
 */
function formatearFecha($fecha) {
    $meses = [
        1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
        5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
        9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
    ];
    
    $timestamp = strtotime($fecha);
    $dia = date('d', $timestamp);
    $mes = $meses[(int)date('m', $timestamp)];
    $anio = date('Y', $timestamp);
    
    return "$dia $mes, $anio";
}

// Configuración completada

