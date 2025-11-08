<?php
/**
 * GENERAR REPORTE POR RANGO EN PDF
 * Genera un PDF con las citas dentro de un rango 
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../../config/configuracion.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_SESSION['medico_id'])) {
    die('Acceso no autorizado');
}

$medico_id = $_GET['medico_id'] ?? '';
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

if (empty($medico_id) || empty($fecha_inicio) || empty($fecha_fin)) {
    die('Parámetros incompletos');
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
    die('Formato de fecha inválido');
}

if ($fecha_fin < $fecha_inicio) {
    die('La fecha de término no puede ser menor a la fecha de inicio');
}

try {
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
            WHERE c.fecha_cita BETWEEN ? AND ?
            ORDER BY c.fecha_cita ASC, c.hora_cita ASC, med.nombre ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$fecha_inicio, $fecha_fin]);
    } else {
        $sql = $selectColumns . $fromJoins . "
            WHERE c.doctor_id = ? AND c.fecha_cita BETWEEN ? AND ?
            ORDER BY c.fecha_cita ASC, c.hora_cita ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$medico_id, $fecha_inicio, $fecha_fin]);
    }

    $citas = $stmt->fetchAll();

    setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish_Spain', 'Spanish');
    $fechaInicioObj = new DateTime($fecha_inicio);
    $fechaFinObj = new DateTime($fecha_fin);
    $rangoTexto = $fechaInicioObj->format('d/m/Y') . ' - ' . $fechaFinObj->format('d/m/Y');

    $totalCitas = count($citas);
    $citasPendientes = count(array_filter($citas, fn($c) => $c['estado'] === 'pendiente'));
    $citasConfirmadas = count(array_filter($citas, fn($c) => $c['estado'] === 'confirmada'));
    $citasCompletadas = count(array_filter($citas, fn($c) => $c['estado'] === 'completada'));
    $citasCanceladas = count(array_filter($citas, fn($c) => $c['estado'] === 'cancelada'));

    $html = generarHTMLReporteSemanal(
        $nombreMedico,
        $especialidadMedico,
        $rangoTexto,
        $fecha_inicio,
        $fecha_fin,
        $citas,
        $totalCitas,
        $citasPendientes,
        $citasConfirmadas,
        $citasCompletadas,
        $citasCanceladas,
        $medico_id === 'todos'
    );

    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('chroot', realpath(__DIR__ . '/../../'));

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('letter', 'portrait');
    $dompdf->render();

    $nombreArchivo = 'Reporte_Semanal_' . str_replace(' ', '_', $nombreMedico) . '_' . $fecha_inicio . '_a_' . $fecha_fin . '.pdf';
    $dompdf->stream($nombreArchivo, ['Attachment' => false]);
} catch (Exception $e) {
    error_log('Error al generar reporte por rango: ' . $e->getMessage());
    die('Error al generar el reporte: ' . $e->getMessage());
}

function generarHTMLReporteSemanal($nombreMedico, $especialidadMedico, $rangoTexto, $fechaInicio, $fechaFin, $citas, $totalCitas, $pendientes, $confirmadas, $completadas, $canceladas, $todosMedicos = false)
{
    $html = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Reporte Por Rango</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: "DejaVu Sans", sans-serif; font-size: 11px; color: #333; line-height: 1.4; margin: 20px 30px; }
            .header { background: #f3f3f3; color: #333; padding: 20px; text-align: center; margin-bottom: 20px; border-radius: 10px; }
            .header h1, .header p { color: #333; }
            .header h1 { font-size: 24px; margin-bottom: 5px; font-weight: 600; }
            .header p { font-size: 12px; opacity: 0.9; }
            .info-section { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-left: 4px solid #D4A574; border-radius: 4px; }
            .info-row { display: table; width: 100%; margin-bottom: 8px; }
            .info-row:last-child { margin-bottom: 0; }
            .info-label { display: table-cell; font-weight: 600; color: #2c3e50; width: 150px; }
            .info-value { display: table-cell; color: #555; }
            .stats-container { display: table; width: 100%; margin-bottom: 20px; border-collapse: collapse; }
            .stat-box { display: table-cell; text-align: center; padding: 15px; background: #f8f9fa; border: 2px solid #e0e0e0; }
            .stat-box:first-child { border-radius: 8px 0 0 8px; }
            .stat-box:last-child { border-radius: 0 8px 8px 0; }
            .stat-number { font-size: 28px; font-weight: 700; color: #2c3e50; display: block; margin-bottom: 5px; }
            .stat-label { font-size: 10px; color: #7f8c8d; text-transform: uppercase; letter-spacing: 0.5px; }
            .section-title { font-size: 16px; font-weight: 600; color: #2c3e50; margin: 25px 0 15px 0; padding-bottom: 8px; border-bottom: 2px solid #D4A574; }
            .citas-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; }
            .citas-table th, .citas-table td { word-wrap: break-word; }
            .citas-table thead { background: #2c3e50; color: white; }
            .citas-table th { padding: 10px 8px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
            .citas-table tbody tr { border-bottom: 1px solid #e0e0e0; }
            .citas-table tbody tr:nth-child(even) { background: #f8f9fa; }
            .citas-table tbody tr:hover { background: #f1f3f5; }
            .citas-table td { padding: 10px 8px; font-size: 10px; vertical-align: top; }
            .hora-badge { display: inline-block; padding: 4px 8px; background: #e3f2fd; color: #1565c0; border-radius: 4px; font-weight: 600; font-size: 10px; }
            .hora-cell { display: flex; flex-direction: column; align-items: flex-start; gap: 4px; }
            .fecha-badge { display: inline-block; padding: 4px 8px; background: #fff8e1; color: #bf360c; border-radius: 4px; font-weight: 600; font-size: 10px; }
            .estado-badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 9px; font-weight: 600; text-transform: uppercase; }
            .estado-pendiente { background: #fff3e0; color: #e65100; }
            .estado-confirmada { background: #e8f5e9; color: #2e7d32; }
            .estado-completada { background: #e3f2fd; color: #1565c0; }
            .estado-cancelada { background: #ffebee; color: #c62828; }
            .servicio-tag { display: inline-block; padding: 3px 8px; background: #D4A574; color: white; border-radius: 4px; font-size: 9px; font-weight: 500; }
            .duracion-badge { display: inline-block; padding: 2px 6px; background: #f3e5f5; color: #7b1fa2; border-radius: 3px; font-size: 8px; font-weight: 600; }
            .fecha-col { font-weight: 600; color: #2c3e50; }
            .medico-column { font-weight: 600; color: #D4A574; }
            .no-citas { text-align: center; padding: 40px; color: #95a5a6; font-size: 12px; }
            .footer { margin-top: 40px; padding-top: 15px; border-top: 2px solid #e0e0e0; text-align: center; font-size: 9px; color: #7f8c8d; }
            .footer p { margin: 3px 0; }
        </style>
    </head>
    <body>';

    $html .= '
        <div class="header">
            <h1 style="color: #333;">Informe por rango de Citas</h1>
            <p style="color: #333;">Clínica Veterinaria Alaska Pets Center</p>
        </div>';

    $html .= '
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Médico:</span>
                <span class="info-value">' . htmlspecialchars($nombreMedico) .
                    ($especialidadMedico ? ' - ' . htmlspecialchars($especialidadMedico) : '') . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">Rango de fechas:</span>
                <span class="info-value">' . htmlspecialchars($rangoTexto) . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">Generado:</span>
                <span class="info-value">' . date('d/m/Y H:i:s') . '</span>
            </div>
        </div>';

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

    $html .= '<h2 class="section-title">Detalle de Citas</h2>';

    if (empty($citas)) {
        $html .= '<div class="no-citas">No hay citas registradas en el rango seleccionado.</div>';
    } else {
        $html .= '<table class="citas-table">';
        $html .= '<thead><tr>';
        $html .= '<th style="width: 80px;">Fecha/Hora</th>';
        if ($todosMedicos) {
            $html .= '<th style="width: 120px;">Médico</th>';
        }
        $html .= '<th style="width: 130px;">Cliente</th>';
        $html .= '<th style="width: 90px;">Mascota</th>';
        $html .= '<th style="width: 80px;">Servicio</th>';
        $html .= '<th>Motivo</th>';
        $html .= '<th style="width: 70px;">Estado</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($citas as $cita) {
            $fecha = new DateTime($cita['fecha_cita']);
            $fechaTexto = $fecha->format('d/m/Y');
            $hora = $cita['hora_cita'] ? date('H:i', strtotime($cita['hora_cita'])) : '';
            $duracion = $cita['duracion_minutos'] ?? 30;
            $horaContenido = '<div class="hora-cell"><span class="fecha-badge">' . $fechaTexto . '</span>';
            if ($hora) {
                $horaContenido .= '<span class="hora-badge">' . $hora . '</span>';
            }
            if ($duracion > 30) {
                $horaContenido .= '<span class="duracion-badge">' . $duracion . ' min</span>';
            }
            $horaContenido .= '</div>';

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
            $html .= '<td class="fecha-col">' . $horaContenido . '</td>';
            if ($todosMedicos) {
                $medicoNombreCita = $cita['medico_nombre'] ?? 'Sin asignar';
                $html .= '<td class="medico-column">' . htmlspecialchars($medicoNombreCita) . '</td>';
            }
            $html .= '<td style="max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><strong>' . htmlspecialchars($cita['cliente_nombre']) . '</strong><br>';
            $html .= '<small>' . htmlspecialchars($cita['cliente_telefono']) . '</small></td>';
            $html .= '<td style="max-width: 110px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><strong>' . htmlspecialchars($cita['mascota_nombre']) . '</strong><br>';
            $html .= '<small>' . htmlspecialchars($cita['mascota_raza']) . '</small></td>';
            $html .= '<td><span class="servicio-tag">' . htmlspecialchars($servicioTexto) . '</span></td>';
            $motivo = !empty($cita['motivo']) ? htmlspecialchars($cita['motivo']) : '<em>Sin motivo especificado</em>';
            $html .= '<td><small>' . $motivo . '</small></td>';
            $estadoClass = 'estado-' . $cita['estado'];
            $estadoTexto = ucfirst($cita['estado']);
            $html .= '<td><span class="estado-badge ' . $estadoClass . '">' . $estadoTexto . '</span></td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
    }

    $html .= '
        <div class="footer">
            <p><strong>Clínica Veterinaria Alaska Pets Center</strong></p>
            <p>Alcalde Saturnino Barril 1380, Osorno | Tel: (+64) 227 0539 | osorno@clinicaalaska.cl</p>
            <p>Este documento es un reporte generado automáticamente por el sistema de gestión de citas.</p>
        </div>
    </body>
    </html>';

    return $html;
}
