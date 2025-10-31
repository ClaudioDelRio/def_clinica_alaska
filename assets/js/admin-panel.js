/**
 * SCRIPT PARA PANEL DE ADMINISTRACIÓN
 * Clínica Veterinaria Alaska Pets Center
 */

(function() {
    'use strict';

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        initMenuNavigation();
        initResponsiveSidebar();
    });

    /**
     * Navegación del menú lateral
     */
    function initMenuNavigation() {
        const menuItems = document.querySelectorAll('.sidebar .menu-item');
        
        menuItems.forEach(item => {
            item.addEventListener('click', function() {
                // Remover clase active de todos los items
                menuItems.forEach(mi => mi.classList.remove('active'));
                
                // Agregar clase active al item clickeado
                this.classList.add('active');
                
                // Aquí se puede agregar lógica para cambiar el contenido
                // según el item seleccionado
            });
        });
    }

    /**
     * Sidebar responsive para móviles
     */
    function initResponsiveSidebar() {
        const sidebar = document.querySelector('.sidebar');
        if (!sidebar) return;
        
        // Si hay un botón de menú hamburguesa, agregarlo aquí
        // Por ahora, la funcionalidad está en CSS
    }
})();

