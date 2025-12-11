<?php
/**
 * LOGOUT DE INTRANET
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../config/configuracion.php';

// Destruir la sesión
session_destroy();

// Redirigir al inicio
header('Location: ../index.html');
exit;

