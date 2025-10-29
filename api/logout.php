<?php
/* ============================================
   ENDPOINT DE CIERRE DE SESIÓN
   ============================================ */

require_once 'config.php';

// Permitir peticiones CORS (si es necesario)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

/* ============================================
   CERRAR SESIÓN
   ============================================ */

if (estaLogueado()) {
    // Guardar el nombre antes de destruir la sesión
    $nombre = $_SESSION['usuario_nombre'];
    
    // Limpiar todas las variables de sesión
    $_SESSION = array();
    
    // Destruir la cookie de sesión si existe
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destruir la sesión
    session_destroy();
    
    enviarRespuesta(true, "Hasta pronto, $nombre. Sesión cerrada correctamente.");
} else {
    enviarRespuesta(false, 'No hay ninguna sesión activa');
}
