<?php
/**
 * NAVEGACIÓN LATERAL DEL PANEL DE ADMINISTRACIÓN
 * Clínica Veterinaria Alaska Pets Center
 * 
 * Este archivo contiene el menú lateral del panel admin
 
 * 
 * @param string $pageActive - Página activa actual (opcional)
 */

// Determinar página activa si no se pasó como parámetro
$pageActive = $pageActive ?? 'dashboard';
?>

<div class="sidebar">
    <div class="logo">
        <i class="fas fa-paw"></i> Admin Panel
    </div>
    <a href="panel-admin.php" style="text-decoration: none; color: inherit; display: block;">
        <div class="menu-item <?php echo $pageActive === 'dashboard' ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i> Dashboard
        </div>
    </a>
    <a href="gestionar-medicos.php" style="text-decoration: none; color: inherit; display: block;">
        <div class="menu-item <?php echo $pageActive === 'usuarios' ? 'active' : ''; ?>">
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
    <a href="logout.php" style="text-decoration: none; color: inherit; display: block;">
        <div class="menu-item" style="margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </div>
    </a>
</div>

