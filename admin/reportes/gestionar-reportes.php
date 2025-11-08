<?php
require_once __DIR__ . '/../../config/configuracion.php';

// Verificar si el médico está logueado
if (!isset($_SESSION['medico_id'])) {
    header('Location: ../login.php');
    exit;
}

$medicoNombre = $_SESSION['medico_nombre'] ?? 'Médico';
$esAdmin = $_SESSION['medico_es_admin'] ?? false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Clínica Veterinaria Alaska</title>
    <base href="../../">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <?php include __DIR__ . '/../nav-panel.php'; ?>

        <div class="main-content">
            <!-- Header del Panel -->
            <div class="panel-header">
                <div class="header-left">
                    <h1><i class="fas fa-file-pdf"></i> Gestión de Reportes</h1>
                    <p class="subtitle">Genera reportes en PDF de las citas de la clínica</p>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($medicoNombre); ?></span>
                        <span class="user-role"><?php echo $esAdmin ? 'Administrador' : 'Médico'; ?></span>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="panel-body">
                <!-- Grid de Tipos de Reportes -->
                <div class="reportes-grid">
                    <!-- Reporte Diario por Médico -->
                    <div class="reporte-card">
                        <div class="reporte-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="reporte-content">
                            <h3>Reporte Diario</h3>
                            <p>Citas de un médico en un día específico</p>
                        </div>
                        <button class="btn-generar-reporte" onclick="mostrarModalReporteDiario()">
                            <i class="fas fa-file-pdf"></i> Generar
                        </button>
                    </div>

                    <!-- Futuros reportes (deshabilitados por ahora) -->
                    <div class="reporte-card disabled">
                        <div class="reporte-icon">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                        <div class="reporte-content">
                            <h3>Reporte Semanal</h3>
                            <p>Resumen semanal de citas (Próximamente)</p>
                        </div>
                        <button class="btn-generar-reporte" disabled>
                            <i class="fas fa-lock"></i> Próximamente
                        </button>
                    </div>

                    <div class="reporte-card disabled">
                        <div class="reporte-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="reporte-content">
                            <h3>Reporte Mensual</h3>
                            <p>Estadísticas mensuales (Próximamente)</p>
                        </div>
                        <button class="btn-generar-reporte" disabled>
                            <i class="fas fa-lock"></i> Próximamente
                        </button>
                    </div>

                    <div class="reporte-card disabled">
                        <div class="reporte-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="reporte-content">
                            <h3>Reporte de Ingresos</h3>
                            <p>Análisis financiero (Próximamente)</p>
                        </div>
                        <button class="btn-generar-reporte" disabled>
                            <i class="fas fa-lock"></i> Próximamente
                        </button>
                    </div>
                </div>

                <!-- Instrucciones -->
                <div class="instrucciones-card">
                    <div class="instrucciones-header">
                        <i class="fas fa-info-circle"></i>
                        <h3>Instrucciones</h3>
                    </div>
                    <ul class="instrucciones-lista">
                        <li><i class="fas fa-check"></i> Selecciona el tipo de reporte que deseas generar</li>
                        <li><i class="fas fa-check"></i> Completa los filtros requeridos (médico, fecha, etc.)</li>
                        <li><i class="fas fa-check"></i> Haz clic en "Generar PDF" para descargar el reporte</li>
                        <li><i class="fas fa-check"></i> El PDF se abrirá automáticamente en una nueva pestaña</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reporte Diario -->
    <div class="modal-overlay" id="modalReporteDiario">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-calendar-day"></i> Generar Reporte Diario</h2>
                <button class="modal-close" onclick="cerrarModalReporteDiario()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="formReporteDiario">
                    <div class="form-group">
                        <label for="medico_id">
                            <i class="fas fa-user-md"></i> Seleccionar Médico *
                        </label>
                        <select id="medico_id" name="medico_id" required>
                            <option value="">-- Seleccione un médico --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fecha_reporte">
                            <i class="fas fa-calendar"></i> Fecha del Reporte *
                        </label>
                        <input type="date" id="fecha_reporte" name="fecha_reporte" required>
                    </div>

                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <div class="info-text">
                            <strong>Información del reporte:</strong>
                            <p>Se generará un PDF con todas las citas del médico seleccionado para la fecha especificada, incluyendo datos del cliente, mascota, servicio y estado de cada cita.</p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModalReporteDiario()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn-primary" onclick="generarReporteDiario()">
                    <i class="fas fa-file-pdf"></i> Generar PDF
                </button>
            </div>
        </div>
    </div>

    <script src="assets/js/admin-panel.js"></script>
    <script src="assets/js/admin-reportes.js"></script>
</body>
</html>

