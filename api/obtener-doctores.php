<?php
/* ============================================
   ENDPOINT: OBTENER LISTA DE DOCTORES
   Devuelve la lista de médicos veterinarios activos
   Clínica Veterinaria Alaska Pets Center
   ============================================ */

require_once 'configuracion.php';

// Configurar headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT 
                id,
                nombre,
                especialidad,
                telefono,
                email
            FROM ca_medicos 
            WHERE activo = 1
            ORDER BY nombre ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $doctores = $stmt->fetchAll();
    
    // Formatear datos
    $doctoresFormateados = [];
    foreach ($doctores as $doctor) {
        $doctoresFormateados[] = [
            'id' => $doctor['id'],
            'nombre' => $doctor['nombre'],
            'especialidad' => $doctor['especialidad'],
            'telefono' => $doctor['telefono'],
            'email' => $doctor['email'],
            'nombre_completo' => $doctor['nombre'] . ($doctor['especialidad'] ? ' - ' . $doctor['especialidad'] : '')
        ];
    }
    
    enviarExito('Doctores obtenidos exitosamente', [
        'doctores' => $doctoresFormateados,
        'total' => count($doctoresFormateados)
    ]);
    
} catch (PDOException $e) {
    error_log('Error en obtener-doctores.php: ' . $e->getMessage());
    enviarError('Error al obtener la lista de doctores');
}

