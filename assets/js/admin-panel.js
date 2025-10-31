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
        initToastContainer();
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

    /**
     * Inicializar contenedor de toasts
     */
    function initToastContainer() {
        // Verificar si ya existe el contenedor
        let toastContainer = document.getElementById('toastContainer');
        
        if (!toastContainer) {
            // Crear el contenedor si no existe
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'toast-container';
            document.body.appendChild(toastContainer);
        }
    }

    /**
     * Mostrar un toast
     * @param {string} type - Tipo de toast: 'success', 'error', 'info'
     * @param {string} title - Título del toast
     * @param {string} message - Mensaje del toast
     * @param {number} duration - Duración en ms (default: 5000)
     */
    function showToast(type, title, message, duration = 5000) {
        const toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) return;

        // Crear el toast
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        // Iconos según el tipo
        const icons = {
            success: '<i class="fas fa-check"></i>',
            error: '<i class="fas fa-times"></i>',
            info: '<i class="fas fa-info"></i>'
        };

        toast.innerHTML = `
            <div class="toast-icon">
                ${icons[type] || icons.info}
            </div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;

        // Agregar al contenedor
        toastContainer.appendChild(toast);

        // Auto-remover después de la duración especificada
        setTimeout(() => {
            toast.style.animation = 'toastSlideOut 0.4s ease-out';
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 400);
        }, duration);
    }

    // Exportar función globalmente
    window.showToast = showToast;
})();

