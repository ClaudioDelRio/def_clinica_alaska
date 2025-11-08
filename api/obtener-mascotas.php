<?php
/* ============================================
   ENDPOINT: OBTENER MASCOTAS DEL USUARIO
   Clínica Veterinaria Alaska Pets Center
   ============================================ */

require_once __DIR__ . '/../config/configuracion.php';

// Configurar headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Verificar que el usuario esté logueado
if (!estaLogueado()) {
    enviarError('Debe iniciar sesión primero');
}

$usuarioId = obtenerUsuarioId();

try {
    $sql = "SELECT 
                id,
                nombre,
                especie,
                raza,
                edad,
                sexo,
                peso,
                color,
                vacunas_al_dia,
                fecha_registro
            FROM ca_mascotas 
            WHERE usuario_id = :usuario_id
            ORDER BY fecha_registro DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['usuario_id' => $usuarioId]);
    $mascotas = $stmt->fetchAll();
    
    // Formatear datos
    $mascotasFormateadas = [];
    foreach ($mascotas as $mascota) {
        $mascotasFormateadas[] = [
            'id' => $mascota['id'],
            'nombre' => $mascota['nombre'],
            'especie' => $mascota['especie'],
            'raza' => $mascota['raza'],
            'edad' => $mascota['edad'],
            'sexo' => $mascota['sexo'],
            'peso' => $mascota['peso'],
            'color' => $mascota['color'],
            'vacunas_al_dia' => (bool)$mascota['vacunas_al_dia'],
            'fecha_registro' => $mascota['fecha_registro'],
            'icono' => obtenerIconoEspecie($mascota['especie'])
        ];
    }
    
    enviarExito('Mascotas obtenidas exitosamente', [
        'mascotas' => $mascotasFormateadas,
        'total' => count($mascotasFormateadas)
    ]);
    
} catch (PDOException $e) {
    error_log('Error en obtener-mascotas.php: ' . $e->getMessage());
    enviarError('Error al obtener mascotas');
}

