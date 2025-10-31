<?php
require_once __DIR__ . '/../api/configuracion.php';

// Verificar si el médico está logueado
if (!isset($_SESSION['medico_id'])) {
    header('Location: login.php');
    exit;
}

// Verificar si el médico está activo
if (!isset($_SESSION['medico_es_admin'])) {
    header('Location: login.php');
    exit;
}
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
    <?php 
    $pageActive = 'dashboard';
    include __DIR__ . '/nav-panel.php'; 
    ?>
    
    <div class="main-content">
        <div class="panel-header">
            <h1>Panel de Control</h1>
            <div class="panel-header-user">
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo htmlspecialchars($_SESSION['medico_nombre'] ?? 'Usuario'); ?></span>
                </div>
                <a href="./admin/logout.php">
                    <button class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </button>
                </a>
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


