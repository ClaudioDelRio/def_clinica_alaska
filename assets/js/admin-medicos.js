/**
 * GESTIÓN DE MÉDICOS - ADMIN PANEL
 * Clínica Veterinaria Alaska Pets Center
 */

(function() {
    'use strict';

    /**
     * Abrir modal para crear nuevo médico
     */
    function abrirModalCrearMedico() {
        const modal = document.getElementById('modalMedico');
        document.getElementById('modalTitulo').textContent = 'Nuevo Médico';
        document.getElementById('formMedico').reset();
        document.getElementById('medico_id').value = '';
        document.getElementById('password').required = true;
        document.getElementById('password-label').textContent = '*';
        modal.classList.add('active');
        modal.style.display = 'flex';
    }

    /**
     * Cerrar modal
     */
    function cerrarModalMedico() {
        const modal = document.getElementById('modalMedico');
        modal.style.display = 'none';
        modal.classList.remove('active');
    }

    /**
     * Editar médico - Cargar datos y abrir modal
     */
    async function editarMedico(id) {
        try {
            const response = await fetch(`admin/obtener-medico.php?id=${id}`);
            const result = await response.json();
            
            if (result.success) {
                const medico = result.data;
                document.getElementById('modalTitulo').textContent = 'Editar Médico';
                document.getElementById('medico_id').value = medico.id;
                document.getElementById('nombre').value = medico.nombre;
                document.getElementById('especialidad').value = medico.especialidad || '';
                document.getElementById('telefono').value = medico.telefono || '';
                document.getElementById('email').value = medico.email || '';
                document.getElementById('es_admin').checked = medico.es_admin == 1;
                document.getElementById('activo').value = medico.activo;
                
                // Contraseña opcional en edición
                document.getElementById('password').required = false;
                document.getElementById('password-label').textContent = '';
                document.getElementById('password').value = '';
                
                const modal = document.getElementById('modalMedico');
                modal.classList.add('active');
                modal.style.display = 'flex';
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error al cargar datos del médico');
        }
    }

    /**
     * Eliminar médico - Usar modal de confirmación
     */
    let medicoToggleId = null;
    let medicoToggleActivo = false;
    
    async function eliminarMedico(id) {
        try {
            // Obtener datos del médico para saber si está activo o inactivo
            const response = await fetch(`admin/obtener-medico.php?id=${id}`);
            const result = await response.json();
            
            if (result.success) {
                const medico = result.data;
                medicoToggleId = id;
                medicoToggleActivo = medico.activo == 1;
                
                // Configurar el modal según el estado del médico
                configurarModalToggle(medico);
                abrirModalConfirm();
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error al cargar datos del médico');
        }
    }
    
    /**
     * Configurar el modal según si va a inactivar o activar
     */
    function configurarModalToggle(medico) {
        const icon = document.getElementById('confirmIcon');
        const title = document.getElementById('confirmTitle');
        const message = document.getElementById('confirmMessage');
        const medicoNombre = document.getElementById('confirmMedico');
        const btnConfirm = document.getElementById('confirmBtn');
        
        medicoNombre.textContent = medico.nombre;
        
        if (medico.activo == 1) {
            // Va a inactivar
            icon.className = 'modal-confirm-icon icon-inactivo';
            icon.innerHTML = '<i class="fas fa-ban"></i>';
            title.textContent = '¿Inactivar Médico?';
            message.textContent = 'Se va a inactivar este médico. No podrá iniciar sesión ni realizar operaciones.';
            btnConfirm.className = 'modal-confirm-btn modal-confirm-btn-confirm btn-inactivo';
            btnConfirm.innerHTML = '<i class="fas fa-ban"></i> Inactivar';
        } else {
            // Va a activar
            icon.className = 'modal-confirm-icon icon-activo';
            icon.innerHTML = '<i class="fas fa-check-circle"></i>';
            title.textContent = '¿Activar Médico?';
            message.textContent = 'Se va a activar este médico. Podrá iniciar sesión y realizar operaciones normalmente.';
            btnConfirm.className = 'modal-confirm-btn modal-confirm-btn-confirm btn-activo';
            btnConfirm.innerHTML = '<i class="fas fa-check-circle"></i> Activar';
        }
    }
    
    /**
     * Abrir modal de confirmación
     */
    function abrirModalConfirm() {
        const modal = document.getElementById('modalConfirmToggle');
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.classList.add('active');
        }, 10);
    }
    
    /**
     * Cerrar modal de confirmación
     */
    function cerrarModalConfirm() {
        const modal = document.getElementById('modalConfirmToggle');
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }
    
    /**
     * Confirmar toggle (activar/inactivar)
     */
    async function confirmarToggleMedico() {
        if (!medicoToggleId) return;
        
        try {
            const response = await fetch('admin/toggle-medico.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: medicoToggleId })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('success', 'Éxito', result.message);
                cerrarModalConfirm();
                // Esperar un poco para que se vea el toast antes de recargar
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error al cambiar el estado del médico');
        }
    }

    /**
     * Guardar médico (crear o actualizar)
     */
    async function guardarMedico() {
        const formData = {
            id: document.getElementById('medico_id').value || null,
            nombre: document.getElementById('nombre').value,
            especialidad: document.getElementById('especialidad').value,
            telefono: document.getElementById('telefono').value,
            email: document.getElementById('email').value,
            es_admin: document.getElementById('es_admin').checked,
            activo: document.getElementById('activo').value,
            password: document.getElementById('password').value
        };
        
        // Si es edición y no hay contraseña, no enviarla
        if (formData.id && !formData.password) {
            delete formData.password;
        }
        
        try {
            const response = await fetch('admin/guardar-medico.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('success', 'Éxito', result.message);
                cerrarModalMedico();
                // Esperar un poco para que se vea el toast antes de recargar
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error al guardar el médico');
        }
    }

    // Exportar funciones globalmente
    window.abrirModalCrearMedico = abrirModalCrearMedico;
    window.cerrarModalMedico = cerrarModalMedico;
    window.editarMedico = editarMedico;
    window.eliminarMedico = eliminarMedico;
    window.guardarMedico = guardarMedico;
    window.cerrarModalConfirm = cerrarModalConfirm;
    window.confirmarToggleMedico = confirmarToggleMedico;

    // Inicializar eventos cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar envío del formulario
        const formMedico = document.getElementById('formMedico');
        if (formMedico) {
            formMedico.addEventListener('submit', async function(e) {
                e.preventDefault();
                await guardarMedico();
            });
        }

        // Cerrar modal al hacer clic fuera
        const modalMedico = document.getElementById('modalMedico');
        if (modalMedico) {
            modalMedico.addEventListener('click', function(e) {
                if (e.target === this) {
                    cerrarModalMedico();
                }
            });
        }

        // Cerrar modal de confirmación al hacer clic fuera
        const modalConfirmToggle = document.getElementById('modalConfirmToggle');
        if (modalConfirmToggle) {
            modalConfirmToggle.addEventListener('click', function(e) {
                if (e.target === this) {
                    cerrarModalConfirm();
                }
            });
        }
    });
})();

