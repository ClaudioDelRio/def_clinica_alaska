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
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar datos del médico');
        }
    }

    /**
     * Eliminar médico
     */
    async function eliminarMedico(id) {
        if (confirm('¿Está seguro de eliminar este médico?')) {
            try {
                const response = await fetch('admin/eliminar-medico.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Médico eliminado correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al eliminar el médico');
            }
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
                alert(result.message);
                cerrarModalMedico();
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar el médico');
        }
    }

    // Exportar funciones globalmente
    window.abrirModalCrearMedico = abrirModalCrearMedico;
    window.cerrarModalMedico = cerrarModalMedico;
    window.editarMedico = editarMedico;
    window.eliminarMedico = eliminarMedico;
    window.guardarMedico = guardarMedico;

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
    });
})();

