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
     * Toggle cliente (activar/inactivar) - Usar modal de confirmación
     */
    let clientToggleId = null;
    let clientToggleActivo = false;
    
    async function toggleCliente(id) {
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
     * Eliminar cliente definitivamente - Mostrar modal con conteo
     */
    let clientDeleteId = null;
    
    async function eliminarClienteDefinitivo(id) {
        try {
            // Obtener datos del cliente y conteos
            const responseCliente = await fetch(`admin/obtener-cliente.php?id=${id}`);
            const resultCliente = await responseCliente.json();
            
            if (!resultCliente.success) {
                showToast('error', 'Error', resultCliente.message);
                return;
            }
            
            // Obtener conteos de mascotas y citas
            const responseConteos = await fetch(`admin/contar-mascotas-citas.php?cliente_id=${id}`);
            const resultConteos = await responseConteos.json();
            
            if (!resultConteos.success) {
                showToast('error', 'Error', 'Error al obtener información de conteos');
                return;
            }
            
            const cliente = resultCliente.data;
            const conteos = resultConteos.data;
            clientDeleteId = id;
            
            // Configurar el modal de eliminación
            configurarModalDelete(cliente, conteos);
            abrirModalDelete();
            
        } catch (error) {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error al cargar datos');
        }
    }
    
    /**
     * Configurar modal de eliminación definitiva
     */
    function configurarModalDelete(cliente, conteos) {
        const deleteNombre = document.getElementById('deleteNombre');
        const conteoEliminacion = document.getElementById('conteoEliminacion');
        
        deleteNombre.textContent = cliente.nombre;
        
        let html = '';
        if (conteos.mascotas > 0 || conteos.citas > 0) {
            if (conteos.mascotas > 0) {
                html += `<div class="conteo-item"><i class="fas fa-paw"></i> <strong>${conteos.mascotas}</strong> mascota(s) serán eliminada(s)</div>`;
            }
            if (conteos.citas > 0) {
                html += `<div class="conteo-item"><i class="fas fa-calendar-check"></i> <strong>${conteos.citas}</strong> cita(s) será(n) eliminada(s)</div>`;
            }
        } else {
            html = '<div class="conteo-item" style="color: var(--color-gris-claro);"><i class="fas fa-info-circle"></i> No tiene mascotas ni citas registradas</div>';
        }
        
        conteoEliminacion.innerHTML = html;
    }
    
    /**
     * Abrir modal de eliminación
     */
    function abrirModalDelete() {
        const modal = document.getElementById('modalConfirmDelete');
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.classList.add('active');
        }, 10);
    }
    
    /**
     * Cerrar modal de eliminación
     */
    function cerrarModalDelete() {
        const modal = document.getElementById('modalConfirmDelete');
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }
    
    /**
     * Confirmar eliminación definitiva
     */
    async function confirmarEliminarCliente() {
        if (!clientDeleteId) return;
        
        try {
            const response = await fetch('admin/eliminar-cliente-cascada.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: clientDeleteId })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('success', 'Éxito', result.message);
                cerrarModalDelete();
                // Esperar un poco para que se vea el toast antes de recargar
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error al eliminar el cliente');
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
        if (!text) return '';
        // Convertir a string si no lo es
        const textStr = String(text);
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return textStr.replace(/[&<>"']/g, m => map[m]);
    }

    /**
     * Búsqueda dinámica de clientes
     */
    let tablaOriginalHTML = null;
    let searchTimeout = null;
    const DEBOUNCE_DELAY = 300; // milisegundos

    /**
     * Inicializar búsqueda
     */
    function inicializarBusqueda() {
        const searchInput = document.getElementById('searchInput');
        const searchClear = document.getElementById('searchClear');
        const tbody = document.querySelector('.data-table tbody');
        
        if (!searchInput || !tbody) return;
        
        // Guardar HTML original
        if (!tablaOriginalHTML) {
            tablaOriginalHTML = tbody.innerHTML;
        }
        
        // Event listener para búsqueda con debounce
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.trim();
            
            // Mostrar/ocultar botón limpiar
            if (searchTerm.length > 0) {
                searchClear.style.display = 'flex';
            } else {
                searchClear.style.display = 'none';
                limpiarBusqueda();
                return;
            }
            
            // Limpiar timeout anterior
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            
            // Debounce: esperar antes de buscar
            searchTimeout = setTimeout(() => {
                buscarClientes(searchTerm);
            }, DEBOUNCE_DELAY);
        });
        
        // Buscar al presionar Enter
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const searchTerm = e.target.value.trim();
                if (searchTerm.length > 0) {
                    if (searchTimeout) {
                        clearTimeout(searchTimeout);
                    }
                    buscarClientes(searchTerm);
                }
            }
        });
    }
    
    /**
     * Buscar clientes
     */
    async function buscarClientes(termino) {
        const tbody = document.querySelector('.data-table tbody');
        const searchResultsInfo = document.getElementById('searchResultsInfo');
        const searchCount = document.getElementById('searchCount');
        
        if (!tbody) return;
        
        // Mostrar loading
        tbody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--color-dorado);"></i><br><br>Buscando...</td></tr>';
        
        try {
            const response = await fetch(`admin/buscar-clientes.php?q=${encodeURIComponent(termino)}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success && result.data) {
                // Renderizar resultados
                renderizarClientes(result.data, tbody);
                
                // Mostrar conteo
                const count = result.count || result.data.length;
                const texto = count === 1 ? '1 cliente encontrado' : `${count} clientes encontrados`;
                searchCount.textContent = texto;
                searchResultsInfo.style.display = 'block';
            } else {
                // No hay resultados
                tbody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 40px; color: #999;"><i class="fas fa-search" style="font-size: 3rem; margin-bottom: 10px; display: block;"></i>No se encontraron clientes</td></tr>';
                searchCount.textContent = 'No se encontraron resultados';
                searchResultsInfo.style.display = 'block';
            }
        } catch (error) {
            console.error('Error en búsqueda:', error);
            tbody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 40px; color: #e74c3c;"><i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 10px; display: block;"></i>Error al realizar la búsqueda</td></tr>';
            showToast('error', 'Error', 'Error al realizar la búsqueda: ' + error.message);
        }
    }
    
    /**
     * Renderizar clientes en la tabla
     */
    function renderizarClientes(clientes, tbody) {
        if (!clientes || clientes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 40px; color: #999;"><i class="fas fa-search" style="font-size: 3rem; margin-bottom: 10px; display: block;"></i>No se encontraron clientes</td></tr>';
            return;
        }
        
        let html = '';
        
        clientes.forEach(cliente => {
            const ultimoAcceso = cliente.ultimo_acceso 
                ? new Date(cliente.ultimo_acceso).toLocaleDateString('es-ES', { 
                    day: '2-digit', 
                    month: '2-digit', 
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                })
                : '<span style="color: #999;">Nunca</span>';
            
            html += `
                <tr class="cliente-row" data-cliente-id="${cliente.id}">
                    <td class="expand-toggle">
                        <button class="toggle-btn" onclick="toggleMascotas(${cliente.id})">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </td>
                    <td>${escapeHtml(cliente.id)}</td>
                    <td><strong>${escapeHtml(cliente.nombre)}</strong></td>
                    <td>${escapeHtml(cliente.email)}</td>
                    <td>${escapeHtml(cliente.rut)}</td>
                    <td>${escapeHtml(cliente.telefono)}</td>
                    <td>
                        ${cliente.activo == 1 
                            ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>' 
                            : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>'}
                    </td>
                    <td>${new Date(cliente.fecha_registro).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' })}</td>
                    <td>${ultimoAcceso}</td>
                    <td class="actions">
                        <button class="btn-icon" title="Editar" onclick="editarCliente(${cliente.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-icon" title="${cliente.activo ? 'Inactivar' : 'Activar'}" onclick="toggleCliente(${cliente.id})">
                            <i class="${cliente.activo ? 'fas fa-ban' : 'fas fa-check-circle'}"></i>
                        </button>
                        <button class="btn-icon" title="Eliminar Definitivamente" onclick="eliminarClienteDefinitivo(${cliente.id})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                <tr class="mascotas-row" id="mascotas-${cliente.id}" style="display: none;">
                    <td colspan="9" class="mascotas-container">
                        <div class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i> Cargando mascotas...
                        </div>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }
    
    /**
     * Limpiar búsqueda
     */
    function limpiarBusqueda() {
        const searchInput = document.getElementById('searchInput');
        const searchClear = document.getElementById('searchClear');
        const searchResultsInfo = document.getElementById('searchResultsInfo');
        const tbody = document.querySelector('.data-table tbody');
        
        if (searchInput) {
            searchInput.value = '';
        }
        
        if (searchClear) {
            searchClear.style.display = 'none';
        }
        
        if (searchResultsInfo) {
            searchResultsInfo.style.display = 'none';
        }
        
        // Restaurar tabla original
        if (tbody && tablaOriginalHTML) {
            tbody.innerHTML = tablaOriginalHTML;
        }
    }

    // Exportar funciones globalmente
    window.abrirModalCrearCliente = abrirModalCrearCliente;
    window.cerrarModalCliente = cerrarModalCliente;
    window.editarCliente = editarCliente;
    window.toggleCliente = toggleCliente;
    window.guardarCliente = guardarCliente;
    window.cerrarModalConfirm = cerrarModalConfirm;
    window.confirmarToggleCliente = confirmarToggleCliente;
    window.toggleMascotas = toggleMascotas;
    window.eliminarClienteDefinitivo = eliminarClienteDefinitivo;
    window.cerrarModalDelete = cerrarModalDelete;
    window.confirmarEliminarCliente = confirmarEliminarCliente;
    window.limpiarBusqueda = limpiarBusqueda;

    // Inicializar eventos cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar búsqueda dinámica
        inicializarBusqueda();
        
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

        // Cerrar modal de eliminación al hacer clic fuera
        const modalConfirmDelete = document.getElementById('modalConfirmDelete');
        if (modalConfirmDelete) {
            modalConfirmDelete.addEventListener('click', function(e) {
                if (e.target === this) {
                    cerrarModalDelete();
                }
            });
        }
    });
})();

