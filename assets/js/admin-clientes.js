/**
 * GESTIÓN DE CLIENTES - ADMIN PANEL
 * Clínica Veterinaria Alaska Pets Center
 */

(function() {
    'use strict';

    /**
     * Abrir modal para crear nuevo cliente
     */
    function abrirModalCrearCliente() {
        const modal = document.getElementById('modalCliente');
        document.getElementById('modalTitulo').textContent = 'Nuevo Cliente';
        document.getElementById('formCliente').reset();
        document.getElementById('cliente_id').value = '';
        document.getElementById('password').required = true;
        document.getElementById('password-label').textContent = '*';
        modal.classList.add('active');
        modal.style.display = 'flex';
    }

    /**
     * Cerrar modal
     */
    function cerrarModalCliente() {
        const modal = document.getElementById('modalCliente');
        modal.style.display = 'none';
        modal.classList.remove('active');
    }

    /**
     * Editar cliente - Cargar datos y abrir modal
     */
    async function editarCliente(id) {
        try {
            const response = await fetch(`admin/obtener-cliente.php?id=${id}`);
            const result = await response.json();
            
            if (result.success) {
                const cliente = result.data;
                document.getElementById('modalTitulo').textContent = 'Editar Cliente';
                document.getElementById('cliente_id').value = cliente.id;
                document.getElementById('nombre').value = cliente.nombre;
                document.getElementById('email').value = cliente.email || '';
                document.getElementById('rut').value = cliente.rut || '';
                document.getElementById('telefono').value = cliente.telefono || '';
                document.getElementById('direccion').value = cliente.direccion || '';
                document.getElementById('activo').value = cliente.activo;
                
                // Contraseña opcional en edición
                document.getElementById('password').required = false;
                document.getElementById('password-label').textContent = '';
                document.getElementById('password').value = '';
                
                const modal = document.getElementById('modalCliente');
                modal.classList.add('active');
                modal.style.display = 'flex';
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error al cargar datos del cliente');
        }
    }

    /**
     * Eliminar cliente - Usar modal de confirmación
     */
    let clientToggleId = null;
    let clientToggleActivo = false;
    
    async function eliminarCliente(id) {
        try {
            // Obtener datos del cliente para saber si está activo o inactivo
            const response = await fetch(`admin/obtener-cliente.php?id=${id}`);
            const result = await response.json();
            
            if (result.success) {
                const cliente = result.data;
                clientToggleId = id;
                clientToggleActivo = cliente.activo == 1;
                
                // Configurar el modal según el estado del cliente
                configurarModalToggle(cliente);
                abrirModalConfirm();
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error al cargar datos del cliente');
        }
    }
    
    /**
     * Configurar el modal según si va a inactivar o activar
     */
    function configurarModalToggle(cliente) {
        const icon = document.getElementById('confirmIcon');
        const title = document.getElementById('confirmTitle');
        const message = document.getElementById('confirmMessage');
        const clienteNombre = document.getElementById('confirmNombre');
        const btnConfirm = document.getElementById('confirmBtn');
        
        clienteNombre.textContent = cliente.nombre;
        
        if (cliente.activo == 1) {
            // Va a inactivar
            icon.className = 'modal-confirm-icon icon-inactivo';
            icon.innerHTML = '<i class="fas fa-ban"></i>';
            title.textContent = '¿Inactivar Cliente?';
            message.textContent = 'Se va a inactivar este cliente. No podrá iniciar sesión ni realizar operaciones.';
            btnConfirm.className = 'modal-confirm-btn modal-confirm-btn-confirm btn-inactivo';
            btnConfirm.innerHTML = '<i class="fas fa-ban"></i> Inactivar';
        } else {
            // Va a activar
            icon.className = 'modal-confirm-icon icon-activo';
            icon.innerHTML = '<i class="fas fa-check-circle"></i>';
            title.textContent = '¿Activar Cliente?';
            message.textContent = 'Se va a activar este cliente. Podrá iniciar sesión y realizar operaciones normalmente.';
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
    async function confirmarToggleCliente() {
        if (!clientToggleId) return;
        
        try {
            const response = await fetch('admin/eliminar-cliente.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: clientToggleId })
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
            showToast('error', 'Error', 'Error al cambiar el estado del cliente');
        }
    }

    /**
     * Guardar cliente (crear o actualizar)
     */
    async function guardarCliente() {
        const formData = {
            id: document.getElementById('cliente_id').value || null,
            nombre: document.getElementById('nombre').value,
            email: document.getElementById('email').value,
            rut: document.getElementById('rut').value,
            telefono: document.getElementById('telefono').value,
            direccion: document.getElementById('direccion').value,
            activo: document.getElementById('activo').value,
            password: document.getElementById('password').value
        };
        
        // Si es edición y no hay contraseña, no enviarla
        if (formData.id && !formData.password) {
            delete formData.password;
        }
        
        try {
            const response = await fetch('admin/guardar-cliente.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('success', 'Éxito', result.message);
                cerrarModalCliente();
                // Esperar un poco para que se vea el toast antes de recargar
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error al guardar el cliente');
        }
    }

    /**
     * Toggle de mascotas - Mostrar/ocultar
     */
    async function toggleMascotas(clienteId) {
        const mascotasRow = document.getElementById(`mascotas-${clienteId}`);
        const toggleBtn = event.target.closest('.toggle-btn');
        
        if (!mascotasRow || !toggleBtn) return;
        
        // Alternar estado
        const isVisible = mascotasRow.style.display !== 'none';
        
        if (isVisible) {
            // Ocultar
            mascotasRow.style.display = 'none';
            toggleBtn.classList.remove('active');
        } else {
            // Mostrar
            mascotasRow.style.display = 'table-row';
            toggleBtn.classList.add('active');
            
            // Cargar mascotas si no se han cargado
            const container = mascotasRow.querySelector('.mascotas-container');
            if (container.innerHTML.includes('loading-spinner')) {
                await cargarMascotas(clienteId, container);
            }
        }
    }

    /**
     * Cargar mascotas del cliente
     */
    async function cargarMascotas(clienteId, container) {
        try {
            const response = await fetch(`admin/listar-mascotas-cliente.php?cliente_id=${clienteId}`);
            const result = await response.json();
            
            if (result.success) {
                renderizarMascotas(result.data, container);
            } else {
                container.innerHTML = '<div class="no-mascotas"><i class="fas fa-exclamation-triangle"></i>Error al cargar mascotas</div>';
            }
        } catch (error) {
            console.error('Error:', error);
            container.innerHTML = '<div class="no-mascotas"><i class="fas fa-exclamation-triangle"></i>Error al cargar mascotas</div>';
        }
    }

    /**
     * Renderizar mascotas en el contenedor
     */
    function renderizarMascotas(mascotas, container) {
        if (!mascotas || mascotas.length === 0) {
            container.innerHTML = '<div class="no-mascotas"><i class="fas fa-paw"></i>No tiene mascotas registradas</div>';
            return;
        }

        let html = '<div class="mascotas-list">';
        
        mascotas.forEach(mascota => {
            const iconoSexo = mascota.sexo === 'macho' ? 'mars' : 'venus';
            const iconoEspecie = mascota.especie === 'perro' ? 'dog' : (mascota.especie === 'gato' ? 'cat' : 'paw');
            
            html += `
                <div class="mascota-card">
                    <div class="mascota-header">
                        <div class="mascota-title">
                            <i class="fas fa-${iconoEspecie}"></i>
                            <h3>${escapeHtml(mascota.nombre)}</h3>
                        </div>
                        <div>
                            ${mascota.activo == 1 ? '<span class="mascota-badge activo"><i class="fas fa-check-circle"></i> Activo</span>' : '<span class="mascota-badge inactivo"><i class="fas fa-times-circle"></i> Inactivo</span>'}
                            ${mascota.vacunas_al_dia == 1 ? '<span class="mascota-badge vacunado"><i class="fas fa-syringe"></i> Vacunas al día</span>' : ''}
                        </div>
                    </div>
                    <div class="mascota-info">
                        ${mascota.especie ? `<span><i class="fas fa-tag"></i> <strong>Especie:</strong> ${escapeHtml(mascota.especie)}</span>` : ''}
                        ${mascota.raza ? `<span><i class="fas fa-dna"></i> <strong>Raza:</strong> ${escapeHtml(mascota.raza)}</span>` : ''}
                        ${mascota.edad ? `<span><i class="fas fa-birthday-cake"></i> <strong>Edad:</strong> ${mascota.edad} años</span>` : ''}
                        ${mascota.peso ? `<span><i class="fas fa-weight"></i> <strong>Peso:</strong> ${mascota.peso} kg</span>` : ''}
                        ${mascota.sexo ? `<span><i class="fas fa-${iconoSexo}"></i> <strong>Sexo:</strong> ${escapeHtml(mascota.sexo)}</span>` : ''}
                        ${mascota.color ? `<span><i class="fas fa-palette"></i> <strong>Color:</strong> ${escapeHtml(mascota.color)}</span>` : ''}
                    </div>
                    ${mascota.observaciones ? `<div class="mascota-observaciones"><i class="fas fa-info-circle"></i> ${escapeHtml(mascota.observaciones)}</div>` : ''}
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    }

    /**
     * Función auxiliar para escapar HTML
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
    }

    // Exportar funciones globalmente
    window.abrirModalCrearCliente = abrirModalCrearCliente;
    window.cerrarModalCliente = cerrarModalCliente;
    window.editarCliente = editarCliente;
    window.eliminarCliente = eliminarCliente;
    window.guardarCliente = guardarCliente;
    window.cerrarModalConfirm = cerrarModalConfirm;
    window.confirmarToggleCliente = confirmarToggleCliente;
    window.toggleMascotas = toggleMascotas;

    // Inicializar eventos cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar envío del formulario
        const formCliente = document.getElementById('formCliente');
        if (formCliente) {
            formCliente.addEventListener('submit', async function(e) {
                e.preventDefault();
                await guardarCliente();
            });
        }

        // Cerrar modal al hacer clic fuera
        const modalCliente = document.getElementById('modalCliente');
        if (modalCliente) {
            modalCliente.addEventListener('click', function(e) {
                if (e.target === this) {
                    cerrarModalCliente();
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

