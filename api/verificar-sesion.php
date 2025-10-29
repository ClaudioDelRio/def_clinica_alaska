<?php
/* ============================================
   ENDPOINT DE VERIFICACIÓN DE SESIÓN
   ============================================ */

require_once 'config.php';

// Permitir peticiones CORS (si es necesario)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

/* ============================================
   VERIFICAR SI EL USUARIO ESTÁ LOGUEADO
   ============================================ */

if (estaLogueado()) {
    $usuario = obtenerUsuarioActual();
    
    enviarRespuesta(true, 'Usuario autenticado', [
        'logueado' => true,
        'usuario' => $usuario
    ]);
} else {
    enviarRespuesta(false, 'Usuario no autenticado', [
        'logueado' => false
    ]);
}
