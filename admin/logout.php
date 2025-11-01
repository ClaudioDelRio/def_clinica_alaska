<?php
/**
 * LOGOUT DE INTRANET
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../api/configuracion.php';

// Destruir la sesión
session_destroy();

// Redirigir al login
header('Location: login.php');
exit;

