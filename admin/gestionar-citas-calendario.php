<?php
require_once __DIR__ . '/../api/configuracion.php';

// Verificar si el médico está logueado
if (!isset($_SESSION['medico_id'])) {
    header('Location: login.php');
    exit;
}

// Verificar si el médico es admin
if (!isset($_SESSION['medico_es_admin']) || !$_SESSION['medico_es_admin']) {
    header('Location: panel-admin.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Citas - Clínica Alaska</title>
    <base href="../">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php 
    $pageActive = 'citas';
    include __DIR__ . '/nav-panel.php'; 
    ?>
    
    <div class="main-content">
        <div class="panel-header">
            <h1><i class="fas fa-calendar-alt"></i> Calendario de Citas</h1>
            <div class="calendar-controls">
                <button class="btn-icon" id="btnPrev" onclick="navegarAnterior()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <select id="selectDia" onchange="cambiarDia()" style="display: none;">
                    <!-- Se llenará dinámicamente -->
                </select>
                <select id="selectMes" onchange="cambiarMesAno()">
                    <option value="1">Enero</option>
                    <option value="2">Febrero</option>
                    <option value="3">Marzo</option>
                    <option value="4">Abril</option>
                    <option value="5">Mayo</option>
                    <option value="6">Junio</option>
                    <option value="7">Julio</option>
                    <option value="8">Agosto</option>
                    <option value="9">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                </select>
                <input type="number" id="selectAno" value="<?php echo date('Y'); ?>" min="2020" max="2100" onchange="cambiarMesAno()" style="width: 80px; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                <button class="btn-icon" id="btnNext" onclick="navegarSiguiente()">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <button class="btn-icon" id="btnHoy" onclick="irHoy()">
                    <i class="fas fa-home"></i> Hoy
                </button>
            </div>
        </div>

        <!-- Vista mensual -->
        <div id="vistaMensual" class="calendar-container">
            <div class="calendar-grid" id="calendarGrid">
                <!-- Se llenará dinámicamente -->
            </div>
        </div>

        <!-- Vista diaria -->
        <div id="vistaDiaria" class="calendar-day-view" style="display: none;">
            <div class="day-hours-grid" id="dayHoursGrid">
                <!-- Se llenará dinámicamente -->
            </div>
        </div>
    </div>

    <!-- Contenedor de toasts -->
    <div id="toastContainer" class="toast-container"></div>

    <script src="assets/js/admin-panel.js"></script>
    <script src="assets/js/admin-calendario.js"></script>
</body>
</html>

