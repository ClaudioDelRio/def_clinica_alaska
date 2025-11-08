<?php
/**
 * GENERAR REPORTE DIARIO EN PDF
 * Genera un PDF con las citas de un médico en una fecha específica
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../../config/configuracion.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Verificar si el médico está logueado
if (!isset($_SESSION['medico_id'])) {
    die('Acceso no autorizado');
}

// Obtener parámetros
$medico_id = $_GET['medico_id'] ?? '';
$fecha = $_GET['fecha'] ?? date('Y-m-d');

if (empty($medico_id) || empty($fecha)) {
    die('Parámetros incompletos');
}

// Validar formato de fecha
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    die('Formato de fecha inválido');
}

try {
    // Obtener información del médico o "Todos"
    $nombreMedico = 'Todos los médicos';
    $especialidadMedico = '';
    
    if ($medico_id !== 'todos') {
        $stmtMedico = $pdo->prepare("
            SELECT nombre, especialidad 
            FROM ca_medicos 
            WHERE id = ? AND activo = 1
        ");
        $stmtMedico->execute([$medico_id]);
        $medico = $stmtMedico->fetch();
        
        if ($medico) {
            $nombreMedico = $medico['nombre'];
            $especialidadMedico = $medico['especialidad'] ?? '';
        }
    }

    // Construir consulta SQL según si es un médico específico o todos
    $selectColumns = "
        SELECT 
            c.id,
            c.fecha_cita,
            c.hora_cita,
            c.servicio,
            c.motivo,
            c.estado,
            c.duracion_minutos,
            u.nombre AS cliente_nombre,
            u.telefono AS cliente_telefono,
            u.email AS cliente_email,
            m.nombre AS mascota_nombre,
            m.especie AS mascota_especie,
            m.raza AS mascota_raza,
            med.nombre AS medico_nombre,
            med.especialidad AS medico_especialidad
    ";

    $fromJoins = "
        FROM ca_citas c
        INNER JOIN ca_usuarios u ON c.usuario_id = u.id
        INNER JOIN ca_mascotas m ON c.mascota_id = m.id
        LEFT JOIN ca_medicos med ON c.doctor_id = med.id
    ";

    if ($medico_id === 'todos') {
        $sql = $selectColumns . $fromJoins . "
            WHERE c.fecha_cita = ?
            ORDER BY c.hora_cita ASC, med.nombre ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$fecha]);
    } else {
        $sql = $selectColumns . $fromJoins . "
            WHERE c.doctor_id = ? AND c.fecha_cita = ?
            ORDER BY c.hora_cita ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$medico_id, $fecha]);
    }
    
    $citas = $stmt->fetchAll();

    // Formatear fecha para el reporte
    setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish_Spain', 'Spanish');
    $fechaFormateada = strftime('%A, %d de %B de %Y', strtotime($fecha));
    $fechaFormateada = ucfirst($fechaFormateada);

    // Calcular estadísticas
    $totalCitas = count($citas);
    $citasPendientes = count(array_filter($citas, fn($c) => $c['estado'] === 'pendiente'));
    $citasConfirmadas = count(array_filter($citas, fn($c) => $c['estado'] === 'confirmada'));
    $citasCompletadas = count(array_filter($citas, fn($c) => $c['estado'] === 'completada'));
    $citasCanceladas = count(array_filter($citas, fn($c) => $c['estado'] === 'cancelada'));

    // Generar HTML del reporte
    $html = generarHTMLReporte(
        $nombreMedico,
        $especialidadMedico,
        $fechaFormateada,
        $fecha,
        $citas,
        $totalCitas,
        $citasPendientes,
        $citasConfirmadas,
        $citasCompletadas,
        $citasCanceladas,
        $medico_id === 'todos'
    );

    // Configurar Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('chroot', realpath(__DIR__ . '/../../'));

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('letter', 'portrait');
    $dompdf->render();

    // Nombre del archivo
    $nombreArchivo = 'Reporte_Diario_' . str_replace(' ', '_', $nombreMedico) . '_' . $fecha . '.pdf';

    // Enviar PDF al navegador
    $dompdf->stream($nombreArchivo, ['Attachment' => false]);

} catch (Exception $e) {
    error_log('Error al generar reporte: ' . $e->getMessage());
    die('Error al generar el reporte: ' . $e->getMessage());
}

/**
 * Genera el HTML del reporte
 */
function generarHTMLReporte($nombreMedico, $especialidadMedico, $fechaFormateada, $fecha, $citas, $totalCitas, $pendientes, $confirmadas, $completadas, $canceladas, $todosMedicos = false) {
    $html = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Reporte Diario</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: "DejaVu Sans", sans-serif;
                font-size: 11px;
                color: #333;
                line-height: 1.4;
                margin: 20px 30px;
            }
            
            .header {
                background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
                color: white;
                padding: 20px;
                text-align: center;
                margin-bottom: 20px;
            }
            
            .header h1 {
                font-size: 24px;
                margin-bottom: 5px;
                font-weight: 600;
            }
            
            .header p {
                font-size: 12px;
                opacity: 0.9;
            }
            
            .info-section {
                background: #f8f9fa;
                padding: 15px;
                margin-bottom: 20px;
                border-left: 4px solid #D4A574;
                border-radius: 4px;
            }
            
            .info-row {
                display: table;
                width: 100%;
                margin-bottom: 8px;
            }
            
            .info-row:last-child {
                margin-bottom: 0;
            }
            
            .info-label {
                display: table-cell;
                font-weight: 600;
                color: #2c3e50;
                width: 150px;
            }
            
            .info-value {
                display: table-cell;
                color: #555;
            }
            
            .stats-container {
                display: table;
                width: 100%;
                margin-bottom: 20px;
                border-collapse: collapse;
            }
            
            .stat-box {
                display: table-cell;
                text-align: center;
                padding: 15px;
                background: #f8f9fa;
                border: 2px solid #e0e0e0;
            }
            
            .stat-box:first-child {
                border-radius: 8px 0 0 8px;
            }
            
            .stat-box:last-child {
                border-radius: 0 8px 8px 0;
            }
            
            .stat-number {
                font-size: 28px;
                font-weight: 700;
                color: #2c3e50;
                display: block;
                margin-bottom: 5px;
            }
            
            .stat-label {
                font-size: 10px;
                color: #7f8c8d;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .section-title {
                font-size: 16px;
                font-weight: 600;
                color: #2c3e50;
                margin: 25px 0 15px 0;
                padding-bottom: 8px;
                border-bottom: 2px solid #D4A574;
            }
            
            .citas-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            
            .citas-table thead {
                background: #2c3e50;
                color: white;
            }
            
            .citas-table th {
                padding: 10px 8px;
                text-align: left;
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .citas-table tbody tr {
                border-bottom: 1px solid #e0e0e0;
            }
            
            .citas-table tbody tr:nth-child(even) {
                background: #f8f9fa;
            }
            
            .citas-table tbody tr:hover {
                background: #f1f3f5;
            }
            
            .citas-table td {
                padding: 10px 8px;
                font-size: 10px;
                vertical-align: top;
            }
            
            .hora-badge {
                display: inline-block;
                padding: 4px 8px;
                background: #e3f2fd;
                color: #1565c0;
                border-radius: 4px;
                font-weight: 600;
                font-size: 10px;
            }

            .hora-cell {
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }
            
            .estado-badge {
                display: inline-block;
                padding: 4px 10px;
                border-radius: 12px;
                font-size: 9px;
                font-weight: 600;
                text-transform: uppercase;
            }
            
            .estado-pendiente {
                background: #fff3e0;
                color: #e65100;
            }
            
            .estado-confirmada {
                background: #e8f5e9;
                color: #2e7d32;
            }
            
            .estado-completada {
                background: #e3f2fd;
                color: #1565c0;
            }
            
            .estado-cancelada {
                background: #ffebee;
                color: #c62828;
            }
            
            .servicio-tag {
                display: inline-block;
                padding: 3px 8px;
                background: #D4A574;
                color: white;
                border-radius: 4px;
                font-size: 9px;
                font-weight: 500;
            }
            
            .especie-icon {
                font-size: 12px;
                margin-right: 3px;
            }
            
            .no-citas {
                text-align: center;
                padding: 40px;
                color: #95a5a6;
                font-size: 12px;
            }
            
            .footer {
                margin-top: 40px;
                padding-top: 15px;
                border-top: 2px solid #e0e0e0;
                text-align: center;
                font-size: 9px;
                color: #7f8c8d;
            }
            
            .footer p {
                margin: 3px 0;
            }
            
            .duracion-badge {
                display: inline-block;
                padding: 2px 6px;
                background: #f3e5f5;
                color: #7b1fa2;
                border-radius: 3px;
                font-size: 8px;
                font-weight: 600;
                margin-left: 5px;
            }

            .medico-column {
                font-weight: 600;
                color: #D4A574;
            }
        </style>
    </head>
    <body>';
    
    // Header
    $html .= '
        <div class="header">
            <h1 style="color: #333;">Informe Diario de Citas</h1>
            <p style="color: #333;">Clínica Veterinaria Alaska Pets Center</p>
        </div>';
    
    // Información del reporte
    $html .= '
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Médico:</span>
                <span class="info-value">' . htmlspecialchars($nombreMedico) . 
                    ($especialidadMedico ? ' - ' . htmlspecialchars($especialidadMedico) : '') . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha:</span>
                <span class="info-value">' . htmlspecialchars($fechaFormateada) . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">Generado:</span>
                <span class="info-value">' . date('d/m/Y H:i:s') . '</span>
            </div>
        </div>';
    
    // Estadísticas
    $html .= '
        <div class="stats-container">
            <div class="stat-box">
                <span class="stat-number">' . $totalCitas . '</span>
                <span class="stat-label">Total</span>
            </div>
            <div class="stat-box">
                <span class="stat-number">' . $pendientes . '</span>
                <span class="stat-label">Pendientes</span>
            </div>
            <div class="stat-box">
                <span class="stat-number">' . $confirmadas . '</span>
                <span class="stat-label">Confirmadas</span>
            </div>
            <div class="stat-box">
                <span class="stat-number">' . $completadas . '</span>
                <span class="stat-label">Completadas</span>
            </div>
            <div class="stat-box">
                <span class="stat-number">' . $canceladas . '</span>
                <span class="stat-label">Canceladas</span>
            </div>
        </div>';
    
    // Tabla de citas
    $html .= '<h2 class="section-title">Detalle de Citas</h2>';
    
    if (empty($citas)) {
        $html .= '<div class="no-citas">No hay citas registradas para este médico en la fecha seleccionada.</div>';
    } else {
        $html .= '<table class="citas-table">';
        $html .= '<thead><tr>';
        $html .= '<th style="width: 60px;">Hora</th>';
        if ($todosMedicos) {
            $html .= '<th style="width: 120px;">Médico</th>';
        }
        $html .= '<th style="width: 120px;">Cliente</th>';
        $html .= '<th style="width: 100px;">Mascota</th>';
        $html .= '<th style="width: 80px;">Servicio</th>';
        $html .= '<th>Motivo</th>';
        $html .= '<th style="width: 70px;">Estado</th>';
        $html .= '</tr></thead>';
        $html .= '<tbody>';
        
        foreach ($citas as $cita) {
            $hora = $cita['hora_cita'] ? date('H:i', strtotime($cita['hora_cita'])) : '';
            $duracion = $cita['duracion_minutos'] ?? 30;
            $horaContenido = '<div class="hora-cell"><span class="hora-badge">' . $hora . '</span>';
            if ($duracion > 30) {
                $horaContenido .= '<span class="duracion-badge">' . $duracion . ' min</span>';
            }
            $horaContenido .= '</div>';
            
            // Icono de especie
            // Traducir servicio
            $servicios = [
                'consulta' => 'Consulta',
                'vacunacion' => 'Vacunación',
                'cirugia' => 'Cirugía',
                'radiologia' => 'Radiología',
                'laboratorio' => 'Laboratorio',
                'peluqueria' => 'Peluquería',
                'emergencia' => 'Emergencia'
            ];
            $servicioKey = $cita['servicio'] ?? '';
            $servicioTexto = $servicios[$servicioKey] ?? ucfirst($servicioKey);
            
            $html .= '<tr>';
            $html .= '<td>' . $horaContenido . '</td>';
            
            if ($todosMedicos) {
                $medicoNombreCita = $cita['medico_nombre'] ?? 'Sin asignar';
                $html .= '<td class="medico-column">' . htmlspecialchars($medicoNombreCita) . '</td>';
            }
            
            $html .= '<td><strong>' . htmlspecialchars($cita['cliente_nombre']) . '</strong><br>';
            $html .= '<small>' . htmlspecialchars($cita['cliente_telefono']) . '</small></td>';
            
            $html .= '<td><strong>' . htmlspecialchars($cita['mascota_nombre']) . '</strong><br>';
            $html .= '<small>' . htmlspecialchars($cita['mascota_raza']) . '</small></td>';
            
            $html .= '<td><span class="servicio-tag">' . $servicioTexto . '</span></td>';
            
            $motivo = !empty($cita['motivo']) ? htmlspecialchars($cita['motivo']) : '<em>Sin motivo especificado</em>';
            $html .= '<td><small>' . $motivo . '</small></td>';
            
            $estadoClass = 'estado-' . $cita['estado'];
            $estadoTexto = ucfirst($cita['estado']);
            $html .= '<td><span class="estado-badge ' . $estadoClass . '">' . $estadoTexto . '</span></td>';
            
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table>';
    }
    
    // Footer
    $html .= '
        <div class="footer">
            <p><strong>Clínica Veterinaria Alaska Pets Center</strong></p>
            <p>Alcalde Saturnino Barril 1380, Osorno | Tel: (+64) 227 0539 | osorno@clinicaalaska.cl</p>
            <p>Este documento es un reporte generado automáticamente por el sistema de gestión de citas.</p>
        </div>';
    
    $html .= '</body></html>';
    
    return $html;
}

