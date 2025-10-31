<?php
require_once __DIR__ . '/../api/configuracion.php';

// TEMPORALMENTE: Sin restricciones de login mientras desarrollamos
// TODO: Habilitar estas validaciones cuando creemos el login de admin
/*
if (!estaLogueado()) {
    header('Location: ../index.html');
    exit;
}

$usuario = obtenerUsuarioActual(); // Asumiendo helper en configuracion.php

// Verificar si el usuario corresponde a un médico con rol admin
try {
    $stmt = $pdo->prepare('SELECT id, nombre, es_admin FROM ca_medicos WHERE usuario_id = :uid AND activo = 1');
    $stmt->execute(['uid' => $usuario['id']]);
    $medico = $stmt->fetch();
    if (!$medico || (int)$medico['es_admin'] !== 1) {
        header('Location: ../index.html');
        exit;
    }
} catch (Throwable $e) {
    header('Location: ../index.html');
    exit;
}
*/
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Clínica Alaska</title>
    <base href="../">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-paw"></i> Admin Panel
        </div>
        <a href="./admin/panel-admin.php" style="text-decoration: none; color: inherit; display: block;">
            <div class="menu-item active">
                <i class="fas fa-chart-line"></i> Principal
            </div>
        </a>
        <a href="./admin/gestionar-medicos.php" style="text-decoration: none; color: inherit; display: block;">
            <div class="menu-item">
                <i class="fas fa-users"></i> Usuarios
            </div>
        </a>
        <div class="menu-item">
            <i class="fas fa-paw"></i> Mascotas
        </div>
        <div class="menu-item">
            <i class="fas fa-calendar-check"></i> Citas
        </div>
        <div class="menu-item">
            <i class="fas fa-chart-bar"></i> Reportes
        </div>
        <div class="menu-item">
            <i class="fas fa-cog"></i> Configuración
        </div>
        <a href="../index.html" style="text-decoration: none; color: inherit; display: block;">
            <div class="menu-item" style="margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
                <i class="fas fa-home"></i> Volver al inicio
            </div>
        </a>
    </div>
    
    <div class="main-content">
        <div class="panel-header">
            <h1>Panel de Control</h1>
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-user-circle" style="font-size: 2rem; color: #3498db;"></i>
                <span>Admin User</span>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>TOTAL PACIENTES</h3>
                <div class="number">248</div>
                <div class="trend up">↑ 12% vs mes anterior</div>
            </div>
            <div class="stat-card">
                <h3>CITAS DEL MES</h3>
                <div class="number">1,542</div>
                <div class="trend up">↑ 8% vs mes anterior</div>
            </div>
            <div class="stat-card">
                <h3>INGRESOS</h3>
                <div class="number">$2,450,231</div>
                <div class="trend up">↑ 15% vs mes anterior</div>
            </div>
            <div class="stat-card">
                <h3>TASA ASISTENCIA</h3>
                <div class="number">94.5%</div>
                <div class="trend up">↑ 2.3% vs mes anterior</div>
            </div>
        </div>
        
        <div class="chart-container">
            <h2 style="margin-bottom: 20px;">Citas Mensuales</h2>
            <div class="chart">
                <div class="bar" style="height: 120px;"><span class="bar-label">Ene</span></div>
                <div class="bar" style="height: 150px;"><span class="bar-label">Feb</span></div>
                <div class="bar" style="height: 100px;"><span class="bar-label">Mar</span></div>
                <div class="bar" style="height: 180px;"><span class="bar-label">Abr</span></div>
                <div class="bar" style="height: 140px;"><span class="bar-label">May</span></div>
                <div class="bar" style="height: 160px;"><span class="bar-label">Jun</span></div>
            </div>
        </div>
        
        <div class="recent-activity">
            <h2 style="margin-bottom: 20px;">Actividad Reciente</h2>
            <div class="activity-item">
                <div class="activity-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="activity-details">
                    <strong>Nueva cita agendada</strong>
                    <div class="activity-time">Hace 5 minutos</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"><i class="fas fa-user-plus"></i></div>
                <div class="activity-details">
                    <strong>Nuevo paciente registrado</strong>
                    <div class="activity-time">Hace 23 minutos</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"><i class="fas fa-paw"></i></div>
                <div class="activity-details">
                    <strong>Consulta completada</strong>
                    <div class="activity-time">Hace 1 hora</div>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon"><i class="fas fa-file-medical"></i></div>
                <div class="activity-details">
                    <strong>Reporte médico generado</strong>
                    <div class="activity-time">Hace 2 horas</div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/admin-panel.js"></script>
</body>
</html>


