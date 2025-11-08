<?php
require_once __DIR__ . '/../config/configuracion.php';

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

$totalClientes = 0;
$totalMascotas = 0;
$totalCitasHoy = 0;
$totalCitasCompletadasMes = 0;
$estadisticasMensuales = [];
$maxValorGrafico = 0;
$anioActual = date('Y');
$mesesEtiquetas = [1 => 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

try {
    $totalClientes = (int)$pdo->query('SELECT COUNT(*) FROM ca_usuarios')->fetchColumn();
    $totalMascotas = (int)$pdo->query('SELECT COUNT(*) FROM ca_mascotas')->fetchColumn();

    $hoy = date('Y-m-d');
    $stmtCitasHoy = $pdo->prepare("SELECT COUNT(DISTINCT CONCAT(fecha_cita,'|', usuario_id,'|', mascota_id,'|', COALESCE(motivo,''))) FROM ca_citas WHERE fecha_cita = :fecha AND estado IN ('pendiente','confirmada','completada')");
    $stmtCitasHoy->execute(['fecha' => $hoy]);
    $totalCitasHoy = (int)$stmtCitasHoy->fetchColumn();

    $inicioMes = date('Y-m-01');
    $finMes = date('Y-m-t');
    $stmtCompletadas = $pdo->prepare("SELECT COUNT(DISTINCT CONCAT(fecha_cita,'|', usuario_id,'|', mascota_id,'|', COALESCE(motivo,''))) FROM ca_citas WHERE estado = 'completada' AND fecha_cita BETWEEN :inicio AND :fin");
    $stmtCompletadas->execute([
        'inicio' => $inicioMes,
        'fin' => $finMes
    ]);
    $totalCitasCompletadasMes = (int)$stmtCompletadas->fetchColumn();

    for ($mes = 1; $mes <= 12; $mes++) {
        $estadisticasMensuales[$mes] = [
            'label' => $mesesEtiquetas[$mes],
            'completadas' => 0,
            'canceladas' => 0
        ];
    }

    $stmtMensual = $pdo->prepare("SELECT MONTH(fecha_cita) AS mes,
        COUNT(DISTINCT CASE WHEN estado = 'completada' THEN CONCAT(fecha_cita,'|', usuario_id,'|', mascota_id,'|', COALESCE(motivo,'')) END) AS completadas,
        COUNT(DISTINCT CASE WHEN estado = 'cancelada' THEN CONCAT(fecha_cita,'|', usuario_id,'|', mascota_id,'|', COALESCE(motivo,'')) END) AS canceladas
        FROM ca_citas
        WHERE YEAR(fecha_cita) = :anio
        GROUP BY mes");
    $stmtMensual->execute(['anio' => $anioActual]);
    foreach ($stmtMensual->fetchAll(PDO::FETCH_ASSOC) as $fila) {
        $mes = (int)$fila['mes'];
        if (isset($estadisticasMensuales[$mes])) {
            $estadisticasMensuales[$mes]['completadas'] = (int)$fila['completadas'];
            $estadisticasMensuales[$mes]['canceladas'] = (int)$fila['canceladas'];
            $maxValorGrafico = max($maxValorGrafico, $estadisticasMensuales[$mes]['completadas'], $estadisticasMensuales[$mes]['canceladas']);
        }
    }

    if ($maxValorGrafico === 0) {
        $maxValorGrafico = 1;
    }
} catch (Throwable $e) {
    error_log('Error obteniendo estadísticas del dashboard: ' . $e->getMessage());
}

$chartPadding = 30;
$chartHeight = 220;
$chartMinWidth = 720;
$countMeses = count($estadisticasMensuales);
$chartWidth = $chartMinWidth;
if ($countMeses > 1) {
    $chartWidth = max($chartMinWidth, $chartPadding * 2 + ($countMeses - 1) * 55);
    $step = ($chartWidth - 2 * $chartPadding) / ($countMeses - 1);
} else {
    $step = 0;
}

$pathCompletadasParts = [];
$pathCanceladasParts = [];
$pointsCompletadas = [];
$pointsCanceladas = [];
$textsCompletadas = [];
$textsCanceladas = [];

foreach ($estadisticasMensuales as $mes => $datos) {
    $index = $mes - 1;
    $x = $chartPadding + $index * $step;
    $ratioCompletadas = $datos['completadas'] / $maxValorGrafico;
    $ratioCanceladas = $datos['canceladas'] / $maxValorGrafico;
    $yCompletadas = $chartHeight - ($ratioCompletadas * ($chartHeight - 2 * $chartPadding)) - $chartPadding;
    $yCanceladas = $chartHeight - ($ratioCanceladas * ($chartHeight - 2 * $chartPadding)) - $chartPadding;

    $pathCompletadasParts[] = (empty($pathCompletadasParts) ? 'M ' : 'L ') . round($x, 2) . ' ' . round($yCompletadas, 2);
    $pathCanceladasParts[] = (empty($pathCanceladasParts) ? 'M ' : 'L ') . round($x, 2) . ' ' . round($yCanceladas, 2);

    $pointsCompletadas[] = ['x' => round($x, 2), 'y' => round($yCompletadas, 2), 'value' => $datos['completadas']];
    $pointsCanceladas[] = ['x' => round($x, 2), 'y' => round($yCanceladas, 2), 'value' => $datos['canceladas']];
    $textsCompletadas[] = ['x' => round($x, 2), 'y' => round($yCompletadas - 8, 2), 'value' => $datos['completadas']];
    $textsCanceladas[] = ['x' => round($x, 2), 'y' => round($yCanceladas + 18, 2), 'value' => $datos['canceladas']];
}

$pathCompletadas = implode(' ', $pathCompletadasParts);
$pathCanceladas = implode(' ', $pathCanceladasParts);

$gridLines = [];
$gridSegments = 4;
for ($i = 0; $i <= $gridSegments; $i++) {
    $gridLines[] = $chartPadding + ($chartHeight - 2 * $chartPadding) * $i / $gridSegments;
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
                <h3>TOTAL CLIENTES</h3>
                <div class="number"><?php echo number_format($totalClientes); ?></div>
                <div class="trend neutral">Registro acumulado</div>
            </div>
            <div class="stat-card">
                <h3>TOTAL MASCOTAS</h3>
                <div class="number"><?php echo number_format($totalMascotas); ?></div>
                <div class="trend neutral">Mascotas activas</div>
            </div>
            <div class="stat-card">
                <h3>CITAS PARA HOY</h3>
                <div class="number"><?php echo number_format($totalCitasHoy); ?></div>
                <div class="trend neutral">Agenda del día</div>
            </div>
            <div class="stat-card">
                <h3>CITAS COMPLETADAS (MES)</h3>
                <div class="number"><?php echo number_format($totalCitasCompletadasMes); ?></div>
                <div class="trend neutral">Mes en curso</div>
            </div>
        </div>
        
        <div class="chart-container">
            <div class="chart-header">
                <h2>Citas mensuales <?php echo $anioActual; ?></h2>
                <div class="chart-legend">
                    <span class="legend-item completadas"><i class="fas fa-square"></i> Completadas</span>
                    <span class="legend-item canceladas"><i class="fas fa-square"></i> Canceladas</span>
                </div>
            </div>
            <div class="chart">
                <div class="chart-inner" style="width: <?php echo (int)ceil($chartWidth); ?>px;">
                    <svg class="chart-svg" viewBox="0 0 <?php echo (int)ceil($chartWidth); ?> <?php echo $chartHeight; ?>" preserveAspectRatio="none">
                        <?php foreach ($gridLines as $lineY): ?>
                            <line class="chart-grid-line" x1="0" y1="<?php echo round($lineY, 2); ?>" x2="<?php echo (int)ceil($chartWidth); ?>" y2="<?php echo round($lineY, 2); ?>" />
                        <?php endforeach; ?>
                        <line class="chart-axis" x1="0" y1="<?php echo $chartHeight - $chartPadding; ?>" x2="<?php echo (int)ceil($chartWidth); ?>" y2="<?php echo $chartHeight - $chartPadding; ?>" />
                        <?php if ($pathCompletadas): ?>
                            <path class="chart-path completadas" d="<?php echo $pathCompletadas; ?>" />
                        <?php endif; ?>
                        <?php if ($pathCanceladas): ?>
                            <path class="chart-path canceladas" d="<?php echo $pathCanceladas; ?>" />
                        <?php endif; ?>
                        <?php foreach ($pointsCompletadas as $p): ?>
                            <circle class="chart-point completadas" cx="<?php echo $p['x']; ?>" cy="<?php echo $p['y']; ?>" r="4" />
                        <?php endforeach; ?>
                        <?php foreach ($pointsCanceladas as $p): ?>
                            <circle class="chart-point canceladas" cx="<?php echo $p['x']; ?>" cy="<?php echo $p['y']; ?>" r="4" />
                        <?php endforeach; ?>
                        <?php foreach ($textsCompletadas as $t): ?>
                            <text class="chart-value-text completadas" x="<?php echo $t['x']; ?>" y="<?php echo $t['y']; ?>"><?php echo $t['value']; ?></text>
                        <?php endforeach; ?>
                        <?php foreach ($textsCanceladas as $t): ?>
                            <text class="chart-value-text canceladas" x="<?php echo $t['x']; ?>" y="<?php echo $t['y']; ?>"><?php echo $t['value']; ?></text>
                        <?php endforeach; ?>
                    </svg>
                    <div class="chart-labels" style="width: <?php echo (int)ceil($chartWidth); ?>px;">
                        <?php foreach ($estadisticasMensuales as $datos): ?>
                            <span><?php echo $datos['label']; ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
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


