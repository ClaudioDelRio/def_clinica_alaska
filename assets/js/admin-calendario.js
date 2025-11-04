/**
 * CALENDARIO DE CITAS - ADMIN PANEL
 * Clínica Veterinaria Alaska Pets Center
 */

(function() {
    'use strict';

    // Variables globales
    let mesActual = new Date().getMonth() + 1;
    let anoActual = new Date().getFullYear();
    let diaActual = new Date().getDate();
    let fechaActualmenteVista = null; // Para la vista diaria
    let citasPorFecha = {}; // Cache de citas organizadas por fecha
    let enVistaDiaria = false; // Control de navegación

    /**
     * Inicializar calendario
     */
    function inicializarCalendario() {
        // Establecer mes y año actuales
        document.getElementById('selectMes').value = mesActual;
        document.getElementById('selectAno').value = anoActual;
        
        // Cargar calendario del mes actual
        cargarCalendario(mesActual, anoActual);
    }

    /**
     * Cargar calendario del mes y año especificados
     */
    async function cargarCalendario(mes, ano) {
        // Calcular fechas de inicio y fin del mes
        const fechaInicio = `${ano}-${String(mes).padStart(2, '0')}-01`;
        const fechaFin = new Date(ano, mes, 0).toISOString().split('T')[0];
        
        try {
            // Cargar citas
            const response = await fetch(`admin/obtener-citas-calendario.php?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`);
            const result = await response.json();
            
            if (result.success) {
                // Organizar citas por fecha
                citasPorFecha = {};
                result.data.forEach(cita => {
                    if (!citasPorFecha[cita.fecha_cita]) {
                        citasPorFecha[cita.fecha_cita] = [];
                    }
                    citasPorFecha[cita.fecha_cita].push(cita);
                });
                
                // Renderizar calendario
                renderizarCalendario(mes, ano);
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error al cargar citas:', error);
            showToast('error', 'Error', 'Error al cargar las citas del calendario');
        }
    }

    /**
     * Renderizar calendario mensual
     */
    function renderizarCalendario(mes, ano) {
        const calendarGrid = document.getElementById('calendarGrid');
        const nombresMeses = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        
        // Calcular día de la semana del primer día del mes
        const primerDia = new Date(ano, mes - 1, 1).getDay();
        const diasEnMes = new Date(ano, mes, 0).getDate();
        
        let html = '';
        
        // Encabezados de días de la semana
        html += '<div class="calendar-header">';
        const diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        diasSemana.forEach(dia => {
            html += `<div class="calendar-day-header">${dia}</div>`;
        });
        html += '</div>';
        
        // Días del mes
        let diaActual = 1;
        let finSemana = false;
        
        // Semanas
        while (diaActual <= diasEnMes) {
            html += '<div class="calendar-week">';
            
            // Días de la semana
            for (let i = 0; i < 7; i++) {
                if (diaActual === 1 && i < primerDia) {
                    // Celda vacía antes del primer día
                    html += '<div class="calendar-day empty"></div>';
                } else if (diaActual > diasEnMes) {
                    // Celda vacía después del último día
                    html += '<div class="calendar-day empty"></div>';
                } else {
                    // Día con contenido
                    const fechaStr = `${ano}-${String(mes).padStart(2, '0')}-${String(diaActual).padStart(2, '0')}`;
                    const citasDelDia = citasPorFecha[fechaStr] || [];
                    const esHoy = esFechaHoy(mes, diaActual, ano);
                    
                    let clases = 'calendar-day';
                    if (esHoy) {
                        clases += ' today';
                    }
                    if (citasDelDia.length > 0) {
                        clases += ' has-citas';
                    }
                    
                    html += `
                        <div class="${clases}" onclick="verDiaDetalle('${fechaStr}')">
                            <div class="day-number">${diaActual}</div>
                            ${citasDelDia.length > 0 ? `<div class="day-citas-count">${citasDelDia.length} <i class="fas fa-calendar-check"></i></div>` : ''}
                        </div>
                    `;
                    diaActual++;
                }
            }
            
            html += '</div>';
        }
        
        calendarGrid.innerHTML = html;
    }

    /**
     * Verificar si una fecha es hoy
     */
    function esFechaHoy(mes, dia, ano) {
        const hoy = new Date();
        return hoy.getDate() === dia && 
               hoy.getMonth() + 1 === mes && 
               hoy.getFullYear() === ano;
    }

    /**
     * Ver detalle de un día específico
     */
    window.verDiaDetalle = async function(fecha) {
        try {
            // Cargar citas del día
            const response = await fetch(`admin/obtener-citas-calendario.php?fecha=${fecha}`);
            const result = await response.json();
            
            if (result.success) {
                renderizarVistaDiaria(fecha, result.data);
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error al cargar día:', error);
            showToast('error', 'Error', 'Error al cargar las citas del día');
        }
    };

    /**
     * Renderizar vista diaria
     */
    function renderizarVistaDiaria(fecha, citas) {
        const vistaMensual = document.getElementById('vistaMensual');
        const vistaDiaria = document.getElementById('vistaDiaria');
        const dayHoursGrid = document.getElementById('dayHoursGrid');
        const selectMes = document.getElementById('selectMes');
        const selectAno = document.getElementById('selectAno');
        const selectDia = document.getElementById('selectDia');
        
        // Guardar fecha actualmente vista
        fechaActualmenteVista = fecha;
        
        // Mostrar vista diaria y ocultar mensual
        vistaMensual.style.display = 'none';
        vistaDiaria.style.display = 'block';
        
        // Formatear fecha
        const fechaObj = new Date(fecha + 'T00:00:00');
        const dia = fechaObj.getDate();
        const mes = fechaObj.getMonth() + 1;
        const ano = fechaObj.getFullYear();
        
        // Actualizar selectores para mostrar el día
        selectMes.value = mes;
        selectAno.value = ano;
        
        // Llenar selector de día
        const diasEnMes = new Date(ano, mes, 0).getDate();
        let optionsHTML = '';
        for (let i = 1; i <= diasEnMes; i++) {
            optionsHTML += `<option value="${i}">${i}</option>`;
        }
        selectDia.innerHTML = optionsHTML;
        selectDia.value = dia;
        selectDia.style.display = 'inline-block';
        
        // Cambiar flag de vista
        enVistaDiaria = true;
        
        // Organizar citas por hora
        const citasPorHora = {};
        citas.forEach(cita => {
            const hora = cita.hora_cita.substring(0, 5); // HH:MM
            if (!citasPorHora[hora]) {
                citasPorHora[hora] = [];
            }
            citasPorHora[hora].push(cita);
        });
        
        // Generar horas del día (9:00 a 19:30)
        let html = '';
        for (let hora = 9; hora <= 19; hora++) {
            const horaStr = `${String(hora).padStart(2, '0')}:00`;
            const hora30Str = `${String(hora).padStart(2, '0')}:30`;
            
            // Hora en punto
            html += `<div class="hour-slot">
                <div class="hour-label">${horaStr}</div>
                <div class="hour-citas">
                    ${renderizarCitasDelHorario(horaStr, citasPorHora[horaStr])}
                </div>
            </div>`;
            
            // Media hora (excepto en la última)
            if (hora < 19) {
                html += `<div class="hour-slot">
                    <div class="hour-label">${hora30Str}</div>
                    <div class="hour-citas">
                        ${renderizarCitasDelHorario(hora30Str, citasPorHora[hora30Str])}
                    </div>
                </div>`;
            }
        }
        
        dayHoursGrid.innerHTML = html;
    }

    /**
     * Renderizar citas de un horario específico
     */
    function renderizarCitasDelHorario(hora, citas) {
        if (!citas || citas.length === 0) {
            return `<div class="no-cita" ondblclick="abrirModalNuevaCita('${hora}')">Libre (doble clic para agendar)</div>`;
        }
        
        return citas.map(cita => {
            const estadoClass = getEstadoClass(cita.estado);
            return `
                <div class="cita-item ${estadoClass}">
                    <div class="cita-header">
                        <i class="fas fa-user"></i> ${escapeHtml(cita.cliente_nombre)}
                        <div class="cita-acciones">
                            <button class="btn-accion-cita btn-editar" onclick="editarCita(${cita.id})" title="Editar cita">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-accion-cita btn-eliminar" onclick="eliminarCita(${cita.id})" title="Eliminar cita">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="cita-info">
                        <div><i class="fas fa-paw"></i> ${escapeHtml(cita.mascota_nombre)}</div>
                        ${cita.doctor_nombre ? `<div><i class="fas fa-user-md"></i> ${escapeHtml(cita.doctor_nombre)}</div>` : ''}
                        <div><i class="fas fa-stethoscope"></i> ${getNombreServicio(cita.servicio)}</div>
                    </div>
                    <div class="cita-estado">
                        <span class="badge-estado ${estadoClass}">${getNombreEstado(cita.estado)}</span>
                    </div>
                </div>
            `;
        }).join('');
    }

    /**
     * Obtener clase CSS según estado
     */
    function getEstadoClass(estado) {
        const map = {
            'pendiente': 'estado-pendiente',
            'confirmada': 'estado-confirmada',
            'completada': 'estado-completada',
            'cancelada': 'estado-cancelada'
        };
        return map[estado] || '';
    }

    /**
     * Obtener nombre del estado
     */
    function getNombreEstado(estado) {
        const map = {
            'pendiente': 'Pendiente',
            'confirmada': 'Confirmada',
            'completada': 'Completada',
            'cancelada': 'Cancelada'
        };
        return map[estado] || estado;
    }

    /**
     * Obtener nombre del servicio
     */
    function getNombreServicio(servicio) {
        const map = {
            'consulta': 'Consulta',
            'vacunacion': 'Vacunación',
            'cirugia': 'Cirugía',
            'radiologia': 'Radiología',
            'laboratorio': 'Laboratorio',
            'peluqueria': 'Peluquería',
            'emergencia': 'Emergencia'
        };
        return map[servicio] || servicio;
    }

    /**
     * Escapar HTML
     */
    function escapeHtml(text) {
        if (!text) return '';
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
     * Navegar anterior (función unificada)
     */
    window.navegarAnterior = function() {
        if (enVistaDiaria) {
            cambiarDia(-1);
        } else {
            cambiarMes(-1);
        }
    };

    /**
     * Navegar siguiente (función unificada)
     */
    window.navegarSiguiente = function() {
        if (enVistaDiaria) {
            cambiarDia(1);
        } else {
            cambiarMes(1);
        }
    };

    /**
     * Ir hoy (función unificada)
     */
    window.irHoy = function() {
        if (enVistaDiaria) {
            irAHoy();
        } else {
            irA_();
        }
    };

    /**
     * Cambiar mes
     */
    window.cambiarMes = function(incremento) {
        // Verificar si estamos en vista diaria para volver a mensual
        if (enVistaDiaria) {
            document.getElementById('vistaMensual').style.display = 'block';
            document.getElementById('vistaDiaria').style.display = 'none';
            document.getElementById('selectDia').style.display = 'none';
            enVistaDiaria = false;
        }
        
        mesActual += incremento;
        if (mesActual > 12) {
            mesActual = 1;
            anoActual++;
        } else if (mesActual < 1) {
            mesActual = 12;
            anoActual--;
        }
        document.getElementById('selectMes').value = mesActual;
        document.getElementById('selectAno').value = anoActual;
        cargarCalendario(mesActual, anoActual);
    };

    /**
     * Cambiar mes y año desde selectores
     */
    window.cambiarMesAno = function() {
        if (enVistaDiaria) {
            // Vista diaria
            window.cambiarDia();
        } else {
            // Vista mensual
            mesActual = parseInt(document.getElementById('selectMes').value);
            anoActual = parseInt(document.getElementById('selectAno').value);
            cargarCalendario(mesActual, anoActual);
        }
    };

    /**
     * Cambiar día (navegación en vista diaria)
     */
    window.cambiarDia = function(incremento) {
        const selectDia = document.getElementById('selectDia');
        const selectMes = document.getElementById('selectMes');
        const selectAno = document.getElementById('selectAno');
        
        if (incremento !== undefined) {
            // Navegación con flechas
            const fechaObj = new Date(fechaActualmenteVista + 'T00:00:00');
            fechaObj.setDate(fechaObj.getDate() + incremento);
            
            const fecha = fechaObj.toISOString().split('T')[0];
            verDiaDetalle(fecha);
        } else {
            // Cambio desde selector
            const dia = parseInt(selectDia.value);
            const mes = parseInt(selectMes.value);
            const ano = parseInt(selectAno.value);
            
            const fecha = `${ano}-${String(mes).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
            verDiaDetalle(fecha);
        }
    };

    /**
     * Ir a hoy
     */
    window.irA_ = function() {
        // Verificar si estamos en vista diaria para volver a mensual
        if (enVistaDiaria) {
            document.getElementById('vistaMensual').style.display = 'block';
            document.getElementById('vistaDiaria').style.display = 'none';
            document.getElementById('selectDia').style.display = 'none';
            enVistaDiaria = false;
        }
        
        const hoy = new Date();
        mesActual = hoy.getMonth() + 1;
        anoActual = hoy.getFullYear();
        document.getElementById('selectMes').value = mesActual;
        document.getElementById('selectAno').value = anoActual;
        cargarCalendario(mesActual, anoActual);
    };

    /**
     * Ir a hoy desde vista diaria
     */
    function irAHoy() {
        const hoy = new Date();
        const fecha = hoy.toISOString().split('T')[0];
        verDiaDetalle(fecha);
    };

    /**
     * Editar cita
     */
    window.editarCita = async function(citaId) {
        try {
            // Cargar datos de la cita
            const response = await fetch(`admin/obtener-cita.php?id=${citaId}`);
            const result = await response.json();
            
            if (result.success) {
                mostrarModalEditarCita(result.data);
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error al cargar cita:', error);
            showToast('error', 'Error', 'Error al cargar la cita');
        }
    };

    /**
     * Eliminar cita
     */
    window.eliminarCita = function(citaId) {
        console.log('Eliminando cita ID:', citaId);
        mostrarModalConfirmacionEliminar(citaId);
    };

    /**
     * Mostrar modal de confirmación para eliminar cita
     */
    function mostrarModalConfirmacionEliminar(citaId) {
        // Crear modal si no existe
        let modal = document.getElementById('modalConfirmEliminarCita');
        if (!modal) {
            crearModalConfirmacionEliminar();
            modal = document.getElementById('modalConfirmEliminarCita');
        }
        
        // Guardar el ID de la cita a eliminar
        modal.dataset.citaId = citaId;
        
        // Mostrar modal
        modal.classList.add('active');
    }

    /**
     * Crear modal de confirmación para eliminar
     */
    function crearModalConfirmacionEliminar() {
        const modalHtml = `
            <div id="modalConfirmEliminarCita" class="modal-confirm-overlay">
                <div class="modal-confirm-container">
                    <div class="modal-confirm-content">
                        <div class="modal-confirm-icon delete">
                            <i class="fas fa-trash-alt"></i>
                        </div>
                        <h3 class="modal-confirm-title">¿Eliminar Cita?</h3>
                        <p class="modal-confirm-message">
                            Esta acción no se puede deshacer. La cita será eliminada permanentemente del sistema.
                        </p>
                        <div class="modal-confirm-buttons">
                            <button type="button" class="modal-confirm-btn modal-confirm-btn-cancel" onclick="cerrarModalConfirmEliminar()">
                                <i class="fas fa-times"></i>
                                Cancelar
                            </button>
                            <button type="button" class="modal-confirm-btn modal-confirm-btn-delete" onclick="confirmarEliminarCita()">
                                <i class="fas fa-trash-alt"></i>
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Cerrar al hacer clic fuera del modal
        const overlay = document.getElementById('modalConfirmEliminarCita');
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                cerrarModalConfirmEliminar();
            }
        });
        
        // Cerrar con tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModalConfirmEliminar();
            }
        });
    }

    /**
     * Cerrar modal de confirmación
     */
    window.cerrarModalConfirmEliminar = function() {
        const modal = document.getElementById('modalConfirmEliminarCita');
        if (modal) {
            modal.classList.remove('active');
        }
    };

    /**
     * Confirmar y ejecutar eliminación de cita
     */
    window.confirmarEliminarCita = async function() {
        const modal = document.getElementById('modalConfirmEliminarCita');
        const citaId = modal.dataset.citaId;
        
        try {
            const response = await fetch('admin/eliminar-cita.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ cita_id: parseInt(citaId) })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('success', 'Éxito', result.message);
                cerrarModalConfirmEliminar();
                // Recargar vista diaria
                if (fechaActualmenteVista) {
                    verDiaDetalle(fechaActualmenteVista);
                }
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error al eliminar cita:', error);
            showToast('error', 'Error', 'Error al eliminar la cita');
        }
    };

    /**
     * Mostrar modal para editar cita
     */
    function mostrarModalEditarCita(cita) {
        // Crear modal si no existe
        let modal = document.getElementById('modalEditarCita');
        if (!modal) {
            crearModalEditarCita();
            modal = document.getElementById('modalEditarCita');
        }
        
        // Llenar formulario con datos de la cita
        document.getElementById('editCitaId').value = cita.id;
        document.getElementById('editCitaFecha').value = cita.fecha_cita;
        document.getElementById('editCitaHoraOriginal').value = cita.hora_cita;
        document.getElementById('editCitaEstado').value = cita.estado;
        document.getElementById('editCitaServicio').value = cita.servicio;
        document.getElementById('editCitaDoctorId').value = cita.doctor_id || '';
        document.getElementById('editCitaMotivo').value = cita.motivo || '';
        
        // Mostrar información del cliente y mascota (solo lectura)
        document.getElementById('editCitaClienteNombre').textContent = cita.cliente_nombre;
        document.getElementById('editCitaMascotaNombre').textContent = cita.mascota_nombre;
        document.getElementById('editCitaMascotaEspecie').textContent = cita.mascota_especie;
        
        // Mostrar modal
        modal.style.display = 'flex';
        
        // Cargar doctores y horarios disponibles
        cargarDoctoresEnSelect();
        cargarHorariosDisponibles(cita.fecha_cita, cita.hora_cita);
    }

    /**
     * Crear modal de edición
     */
    function crearModalEditarCita() {
        const modalHtml = `
            <div id="modalEditarCita" class="modal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2><i class="fas fa-edit"></i> Editar Cita</h2>
                        <button class="close-modal" onclick="cerrarModalEditarCita()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="info-cliente-mascota">
                            <div class="info-item">
                                <label><i class="fas fa-user"></i> Cliente:</label>
                                <span id="editCitaClienteNombre"></span>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-paw"></i> Mascota:</label>
                                <span id="editCitaMascotaNombre"></span> (<span id="editCitaMascotaEspecie"></span>)
                            </div>
                        </div>
                        
                        <form id="formEditarCita" onsubmit="guardarCambiosCita(event)">
                            <input type="hidden" id="editCitaId">
                            <input type="hidden" id="editCitaHoraOriginal">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="editCitaFecha">Fecha *</label>
                                    <input type="date" id="editCitaFecha" required onchange="cambioFechaCita()">
                                </div>
                                <div class="form-group">
                                    <label for="editCitaHora">Hora *</label>
                                    <select id="editCitaHora" required>
                                        <option value="">Seleccione una hora</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="editCitaEstado">Estado *</label>
                                    <select id="editCitaEstado" required>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="confirmada">Confirmada</option>
                                        <option value="completada">Completada</option>
                                        <option value="cancelada">Cancelada</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="editCitaServicio">Servicio *</label>
                                    <select id="editCitaServicio" required>
                                        <option value="consulta">Consulta</option>
                                        <option value="vacunacion">Vacunación</option>
                                        <option value="cirugia">Cirugía</option>
                                        <option value="radiologia">Radiología</option>
                                        <option value="laboratorio">Laboratorio</option>
                                        <option value="peluqueria">Peluquería</option>
                                        <option value="emergencia">Emergencia</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="editCitaDoctorId">Doctor</label>
                                <select id="editCitaDoctorId">
                                    <option value="">Sin asignar</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="editCitaMotivo">Motivo</label>
                                <textarea id="editCitaMotivo" rows="3"></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" class="btn-secondary" onclick="cerrarModalEditarCita()">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    /**
     * Cargar doctores en el select
     */
    async function cargarDoctoresEnSelect() {
        try {
            const response = await fetch('api/obtener-doctores.php');
            const result = await response.json();
            
            if (result.success) {
                const select = document.getElementById('editCitaDoctorId');
                const selectedValue = select.value;
                
                // Mantener opción "Sin asignar"
                select.innerHTML = '<option value="">Sin asignar</option>';
                
                // Agregar doctores - La API devuelve en result.data.doctores
                const doctores = result.data.doctores || result.data || [];
                doctores.forEach(doctor => {
                    const option = document.createElement('option');
                    option.value = doctor.id;
                    option.textContent = doctor.nombre_completo || (doctor.nombre + (doctor.especialidad ? ` - ${doctor.especialidad}` : ''));
                    select.appendChild(option);
                });
                
                // Restaurar valor seleccionado
                select.value = selectedValue;
            } else {
                console.error('Error al cargar doctores:', result.message);
                showToast('error', 'Error', result.message || 'Error al cargar doctores');
            }
        } catch (error) {
            console.error('Error al cargar doctores:', error);
            showToast('error', 'Error', 'Error al cargar la lista de doctores');
        }
    }

    /**
     * Cargar horarios disponibles para una fecha
     */
    async function cargarHorariosDisponibles(fecha, horaActual) {
        try {
            const response = await fetch(`admin/obtener-horarios-disponibles.php?fecha=${fecha}`);
            const result = await response.json();
            
            const select = document.getElementById('editCitaHora');
            select.innerHTML = '<option value="">Seleccione una hora</option>';
            
            if (result.success && result.data.horarios_disponibles) {
                // Agregar horarios disponibles
                result.data.horarios_disponibles.forEach(horario => {
                    const option = document.createElement('option');
                    option.value = horario.hora;
                    option.textContent = horario.texto;
                    select.appendChild(option);
                });
                
                // Agregar también la hora actual de la cita (aunque esté ocupada)
                if (horaActual) {
                    const horaFormateada = horaActual.substring(0, 5);
                    // Verificar si ya existe en las opciones
                    const exists = Array.from(select.options).some(opt => opt.value === horaFormateada);
                    if (!exists) {
                        const option = document.createElement('option');
                        option.value = horaFormateada;
                        option.textContent = horaFormateada + ' hrs (Hora actual)';
                        select.appendChild(option);
                    }
                    select.value = horaFormateada;
                }
            }
        } catch (error) {
            console.error('Error al cargar horarios:', error);
            showToast('error', 'Error', 'Error al cargar los horarios disponibles');
        }
    }

    /**
     * Manejar cambio de fecha en el modal de edición
     */
    window.cambioFechaCita = function() {
        const fecha = document.getElementById('editCitaFecha').value;
        const horaOriginal = document.getElementById('editCitaHoraOriginal').value;
        
        if (fecha) {
            cargarHorariosDisponibles(fecha, horaOriginal);
        }
    };

    /**
     * Cerrar modal de edición
     */
    window.cerrarModalEditarCita = function() {
        const modal = document.getElementById('modalEditarCita');
        if (modal) {
            modal.style.display = 'none';
        }
    };

    /**
     * Guardar cambios en la cita
     */
    window.guardarCambiosCita = async function(event) {
        event.preventDefault();
        
        const datos = {
            cita_id: parseInt(document.getElementById('editCitaId').value),
            fecha_cita: document.getElementById('editCitaFecha').value,
            hora_cita: document.getElementById('editCitaHora').value,
            estado: document.getElementById('editCitaEstado').value,
            servicio: document.getElementById('editCitaServicio').value,
            doctor_id: document.getElementById('editCitaDoctorId').value ? parseInt(document.getElementById('editCitaDoctorId').value) : null,
            motivo: document.getElementById('editCitaMotivo').value
        };
        
        try {
            const response = await fetch('admin/actualizar-cita.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('success', 'Éxito', result.message);
                cerrarModalEditarCita();
                
                // Recargar vista (diaria o mensual según corresponda)
                if (fechaActualmenteVista) {
                    verDiaDetalle(fechaActualmenteVista);
                } else {
                    cargarCalendario(mesActual, anoActual);
                }
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error al guardar cambios:', error);
            showToast('error', 'Error', 'Error al guardar los cambios');
        }
    };

    /**
     * FUNCIONALIDAD DE NUEVA CITA
     */
    
    // Variables globales para el proceso de creación de cita
    let citaNuevaFecha = null;
    let citaNuevaHora = null;
    let citaNuevaDuracion = 30; // Duración en minutos (por defecto 30 min)
    let citaNuevoClienteId = null;
    let citaNuevoClienteNombre = null;

    /**
     * Abrir modal de confirmación para nueva cita
     */
    window.abrirModalNuevaCita = function(hora) {
        if (!fechaActualmenteVista) {
            showToast('error', 'Error', 'No se pudo determinar la fecha');
            return;
        }
        
        citaNuevaFecha = fechaActualmenteVista;
        citaNuevaHora = hora;
        
        mostrarModalConfirmarHora(citaNuevaFecha, citaNuevaHora);
    };

    /**
     * Modal 1: Confirmar fecha y hora
     */
    function mostrarModalConfirmarHora(fecha, hora) {
        let modal = document.getElementById('modalConfirmarHoraCita');
        if (!modal) {
            crearModalConfirmarHora();
            modal = document.getElementById('modalConfirmarHoraCita');
        }
        
        // Formatear fecha para mostrar
        const fechaObj = new Date(fecha + 'T00:00:00');
        const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const fechaFormateada = fechaObj.toLocaleDateString('es-ES', opciones);
        
        // Actualizar contenido
        document.getElementById('confirmarHoraFecha').textContent = fechaFormateada;
        document.getElementById('confirmarHoraHora').textContent = hora + ' hrs';
        
        // Establecer duración por defecto
        citaNuevaDuracion = 30;
        document.getElementById('duracionCita').value = '30';
        
        // Actualizar bloques horarios
        actualizarBloquesHorarios(hora, 30);
        
        // Mostrar modal
        modal.classList.add('active');
    }

    /**
     * Crear modal de confirmación de hora
     */
    function crearModalConfirmarHora() {
        const modalHtml = `
            <div id="modalConfirmarHoraCita" class="modal-confirm-overlay">
                <div class="modal-confirm-container modal-confirm-duracion">
                    <div class="modal-confirm-content">
                        <div class="modal-confirm-icon icon-activo">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3 class="modal-confirm-title">¿Agendar Cita?</h3>
                        <p class="modal-confirm-message">
                            <strong id="confirmarHoraFecha"></strong><br>
                            <strong id="confirmarHoraHora"></strong>
                        </p>
                        
                        <div class="duracion-selector">
                            <label for="duracionCita">
                                <i class="fas fa-clock"></i> Duración de la cita:
                            </label>
                            <select id="duracionCita" onchange="cambiarDuracionCita(this.value)">
                                <option value="30">30 minutos</option>
                                <option value="60">1 hora</option>
                                <option value="90">1 hora 30 minutos</option>
                                <option value="120">2 horas</option>
                                <option value="150">2 horas 30 minutos</option>
                                <option value="180">3 horas</option>
                                <option value="210">3 horas 30 minutos</option>
                                <option value="240">4 horas</option>
                            </select>
                        </div>
                        
                        <div class="bloques-horarios" id="bloquesHorarios">
                            <p class="bloques-titulo"><i class="fas fa-list"></i> Horarios que ocupará:</p>
                            <div class="bloques-lista" id="bloquesLista"></div>
                        </div>
                        
                        <div class="modal-confirm-buttons">
                            <button type="button" class="modal-confirm-btn modal-confirm-btn-cancel" onclick="cerrarModalConfirmarHora()">
                                <i class="fas fa-times"></i>
                                Cancelar
                            </button>
                            <button type="button" class="modal-confirm-btn modal-confirm-btn-confirm" onclick="validarYContinuar()">
                                <i class="fas fa-check"></i>
                                Continuar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    /**
     * Cambiar duración de la cita
     */
    window.cambiarDuracionCita = function(duracion) {
        citaNuevaDuracion = parseInt(duracion);
        const horaInicio = document.getElementById('confirmarHoraHora').textContent.replace(' hrs', '');
        actualizarBloquesHorarios(horaInicio, citaNuevaDuracion);
    };

    /**
     * Actualizar visualización de bloques horarios
     */
    function actualizarBloquesHorarios(horaInicio, duracionMinutos) {
        const bloques = calcularBloquesNecesarios(horaInicio, duracionMinutos);
        const bloquesLista = document.getElementById('bloquesLista');
        
        let html = '';
        bloques.forEach((bloque, index) => {
            const icono = index === 0 ? 'fa-play' : 'fa-clock';
            html += `
                <div class="bloque-item">
                    <i class="fas ${icono}"></i>
                    <span>${bloque}</span>
                </div>
            `;
        });
        
        bloquesLista.innerHTML = html;
    }

    /**
     * Calcular bloques horarios necesarios
     */
    function calcularBloquesNecesarios(horaInicio, duracionMinutos) {
        const bloques = [];
        const [hora, minuto] = horaInicio.split(':').map(Number);
        
        let minutoActual = hora * 60 + minuto;
        const minutosTotal = duracionMinutos;
        const bloquesNecesarios = Math.ceil(minutosTotal / 30);
        
        for (let i = 0; i < bloquesNecesarios; i++) {
            const h = Math.floor(minutoActual / 60);
            const m = minutoActual % 60;
            const horaStr = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
            bloques.push(horaStr);
            minutoActual += 30;
        }
        
        return bloques;
    }

    /**
     * Validar disponibilidad y continuar
     */
    window.validarYContinuar = async function() {
        const horaInicio = citaNuevaHora;
        const bloques = calcularBloquesNecesarios(horaInicio, citaNuevaDuracion);
        
        // Verificar disponibilidad de todos los bloques
        try {
            const response = await fetch(`admin/validar-bloques-disponibles.php?fecha=${citaNuevaFecha}&bloques=${bloques.join(',')}`);
            const result = await response.json();
            
            if (result.success && result.data.todos_disponibles) {
                // Todos los bloques están disponibles, continuar
                abrirModalBuscarCliente();
            } else {
                // Algunos bloques no están disponibles
                const bloquesOcupados = result.data.bloques_ocupados || [];
                showToast('error', 'Error', `Los siguientes horarios ya están ocupados: ${bloquesOcupados.join(', ')}`);
            }
        } catch (error) {
            console.error('Error al validar bloques:', error);
            showToast('error', 'Error', 'Error al validar disponibilidad de horarios');
        }
    };

    /**
     * Cerrar modal de confirmación de hora
     */
    window.cerrarModalConfirmarHora = function() {
        const modal = document.getElementById('modalConfirmarHoraCita');
        if (modal) {
            modal.classList.remove('active');
        }
    };

    /**
     * Modal 2: Buscar y seleccionar cliente
     */
    window.abrirModalBuscarCliente = function() {
        cerrarModalConfirmarHora();
        
        let modal = document.getElementById('modalBuscarCliente');
        if (!modal) {
            crearModalBuscarCliente();
            modal = document.getElementById('modalBuscarCliente');
        }
        
        // Limpiar búsqueda anterior
        document.getElementById('searchClienteInput').value = '';
        document.getElementById('resultadosBusquedaCliente').innerHTML = '';
        
        // Mostrar modal
        modal.style.display = 'flex';
    };

    /**
     * Crear modal de búsqueda de cliente
     */
    function crearModalBuscarCliente() {
        const modalHtml = `
            <div id="modalBuscarCliente" class="modal" style="display: none;">
                <div class="modal-content modal-buscar-cliente">
                    <div class="modal-header">
                        <h2><i class="fas fa-search"></i> Buscar Cliente</h2>
                        <button class="close-modal" onclick="cerrarModalBuscarCliente()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="search-box-container">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="searchClienteInput" placeholder="Buscar por nombre, RUT, teléfono o nombre de mascota..." onkeyup="buscarClienteParaCita(this.value)">
                            </div>
                            <button class="btn-primary" onclick="redirigirNuevoCliente()">
                                <i class="fas fa-user-plus"></i>
                                Nuevo Cliente
                            </button>
                        </div>
                        <div id="resultadosBusquedaCliente" class="resultados-busqueda">
                            <p class="texto-info">Ingrese un término de búsqueda para encontrar clientes</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    /**
     * Cerrar modal de búsqueda de cliente
     */
    window.cerrarModalBuscarCliente = function() {
        const modal = document.getElementById('modalBuscarCliente');
        if (modal) {
            modal.style.display = 'none';
        }
    };

    /**
     * Buscar cliente para nueva cita
     */
    let timeoutBusqueda = null;
    window.buscarClienteParaCita = function(termino) {
        clearTimeout(timeoutBusqueda);
        
        const resultadosDiv = document.getElementById('resultadosBusquedaCliente');
        
        if (termino.length < 2) {
            resultadosDiv.innerHTML = '<p class="texto-info">Ingrese al menos 2 caracteres para buscar</p>';
            return;
        }
        
        resultadosDiv.innerHTML = '<p class="texto-info"><i class="fas fa-spinner fa-spin"></i> Buscando...</p>';
        
        timeoutBusqueda = setTimeout(async () => {
            try {
                const response = await fetch(`admin/buscar-clientes.php?q=${encodeURIComponent(termino)}`);
                const result = await response.json();
                
                if (result.success) {
                    if (result.data.length === 0) {
                        resultadosDiv.innerHTML = '<p class="texto-info">No se encontraron clientes</p>';
                    } else {
                        mostrarResultadosClientes(result.data);
                    }
                } else {
                    resultadosDiv.innerHTML = '<p class="texto-error">Error al buscar clientes</p>';
                }
            } catch (error) {
                console.error('Error:', error);
                resultadosDiv.innerHTML = '<p class="texto-error">Error al realizar la búsqueda</p>';
            }
        }, 300);
    };

    /**
     * Mostrar resultados de búsqueda de clientes
     */
    function mostrarResultadosClientes(clientes) {
        const resultadosDiv = document.getElementById('resultadosBusquedaCliente');
        
        let html = '<div class="lista-clientes">';
        clientes.forEach(cliente => {
            html += `
                <div class="cliente-item">
                    <div class="cliente-info">
                        <div class="cliente-nombre">
                            <i class="fas fa-user"></i> ${escapeHtml(cliente.nombre)}
                        </div>
                        <div class="cliente-detalles">
                            ${cliente.rut ? `<span><i class="fas fa-id-card"></i> ${escapeHtml(cliente.rut)}</span>` : ''}
                            ${cliente.telefono ? `<span><i class="fas fa-phone"></i> ${escapeHtml(cliente.telefono)}</span>` : ''}
                            ${cliente.email ? `<span><i class="fas fa-envelope"></i> ${escapeHtml(cliente.email)}</span>` : ''}
                        </div>
                    </div>
                    <button class="btn-accion-seleccionar" onclick="seleccionarCliente(${cliente.id}, '${escapeHtml(cliente.nombre)}')">
                        <i class="fas fa-check"></i>
                        Seleccionar
                    </button>
                </div>
            `;
        });
        html += '</div>';
        
        resultadosDiv.innerHTML = html;
    }

    /**
     * Seleccionar cliente y abrir modal de formulario de cita
     */
    window.seleccionarCliente = async function(clienteId, clienteNombre) {
        citaNuevoClienteId = clienteId;
        citaNuevoClienteNombre = clienteNombre;
        
        // Cargar mascotas del cliente
        try {
            const response = await fetch(`admin/listar-mascotas-cliente.php?cliente_id=${clienteId}`);
            const result = await response.json();
            
            if (result.success) {
                cerrarModalBuscarCliente();
                mostrarModalFormularioCita(result.data);
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error al cargar las mascotas del cliente');
        }
    };

    /**
     * Abrir modal de nuevo cliente
     */
    window.redirigirNuevoCliente = function() {
        cerrarModalBuscarCliente();
        mostrarModalNuevoCliente();
    };

    /**
     * Mostrar modal de nuevo cliente
     */
    function mostrarModalNuevoCliente() {
        let modal = document.getElementById('modalNuevoCliente');
        if (!modal) {
            crearModalNuevoCliente();
            modal = document.getElementById('modalNuevoCliente');
        }
        
        // Limpiar formulario
        document.getElementById('formNuevoCliente').reset();
        
        // Mostrar modal
        modal.style.display = 'flex';
    }

    /**
     * Crear modal de nuevo cliente
     */
    function crearModalNuevoCliente() {
        const modalHtml = `
            <div id="modalNuevoCliente" class="modal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2><i class="fas fa-user-plus"></i> Nuevo Cliente</h2>
                        <button class="close-modal" onclick="cerrarModalNuevoCliente()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formNuevoCliente" onsubmit="guardarNuevoCliente(event)">
                            <div class="form-group">
                                <label for="nuevoClienteNombre">Nombre Completo *</label>
                                <input type="text" id="nuevoClienteNombre" required placeholder="Ej: Juan Pérez">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nuevoClienteEmail">Email *</label>
                                    <input type="email" id="nuevoClienteEmail" required placeholder="ejemplo@correo.com">
                                </div>
                                <div class="form-group">
                                    <label for="nuevoClienteRut">RUT</label>
                                    <input type="text" id="nuevoClienteRut" placeholder="12.345.678-9">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nuevoClienteTelefono">Teléfono *</label>
                                    <input type="tel" id="nuevoClienteTelefono" required placeholder="+56 9 1234 5678">
                                </div>
                                <div class="form-group">
                                    <label for="nuevoClienteDireccion">Dirección</label>
                                    <input type="text" id="nuevoClienteDireccion" placeholder="Calle, número, comuna">
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" class="btn-secondary" onclick="volverABuscarCliente()">
                                    <i class="fas fa-arrow-left"></i>
                                    Volver
                                </button>
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-arrow-right"></i>
                                    Continuar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    /**
     * Cerrar modal de nuevo cliente
     */
    window.cerrarModalNuevoCliente = function() {
        const modal = document.getElementById('modalNuevoCliente');
        if (modal) {
            modal.style.display = 'none';
        }
    };

    /**
     * Volver a modal de búsqueda desde nuevo cliente
     */
    window.volverABuscarCliente = function() {
        cerrarModalNuevoCliente();
        abrirModalBuscarCliente();
    };

    /**
     * Guardar nuevo cliente y pasar a crear mascota
     */
    window.guardarNuevoCliente = async function(event) {
        event.preventDefault();
        
        const datos = {
            nombre: document.getElementById('nuevoClienteNombre').value,
            email: document.getElementById('nuevoClienteEmail').value,
            rut: document.getElementById('nuevoClienteRut').value,
            telefono: document.getElementById('nuevoClienteTelefono').value,
            direccion: document.getElementById('nuevoClienteDireccion').value
        };
        
        try {
            const response = await fetch('admin/crear-cliente-rapido.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('success', 'Éxito', 'Cliente creado exitosamente');
                
                // Guardar datos del cliente
                citaNuevoClienteId = result.data.id;
                citaNuevoClienteNombre = result.data.nombre;
                
                // Cerrar modal de cliente y abrir modal de mascota
                cerrarModalNuevoCliente();
                mostrarModalNuevaMascota();
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error al crear el cliente');
        }
    };

    /**
     * Mostrar modal de nueva mascota
     */
    function mostrarModalNuevaMascota() {
        let modal = document.getElementById('modalNuevaMascota');
        if (!modal) {
            crearModalNuevaMascota();
            modal = document.getElementById('modalNuevaMascota');
        }
        
        // Limpiar formulario
        document.getElementById('formNuevaMascota').reset();
        
        // Mostrar nombre del cliente
        document.getElementById('nuevaMascotaClienteNombre').textContent = citaNuevoClienteNombre;
        
        // Mostrar modal
        modal.style.display = 'flex';
    }

    /**
     * Crear modal de nueva mascota
     */
    function crearModalNuevaMascota() {
        const modalHtml = `
            <div id="modalNuevaMascota" class="modal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2><i class="fas fa-paw"></i> Nueva Mascota</h2>
                        <button class="close-modal" onclick="cerrarModalNuevaMascota()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="info-cliente-mascota">
                            <div class="info-item">
                                <label><i class="fas fa-user"></i> Dueño:</label>
                                <span id="nuevaMascotaClienteNombre"></span>
                            </div>
                        </div>
                        
                        <form id="formNuevaMascota" onsubmit="guardarNuevaMascota(event)">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nuevaMascotaNombre">Nombre de la Mascota *</label>
                                    <input type="text" id="nuevaMascotaNombre" required placeholder="Ej: Firulais">
                                </div>
                                <div class="form-group">
                                    <label for="nuevaMascotaEspecie">Especie *</label>
                                    <select id="nuevaMascotaEspecie" required>
                                        <option value="">Seleccione...</option>
                                        <option value="perro">Perro</option>
                                        <option value="gato">Gato</option>
                                        <option value="ave">Ave</option>
                                        <option value="roedor">Roedor</option>
                                        <option value="reptil">Reptil</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nuevaMascotaRaza">Raza</label>
                                    <input type="text" id="nuevaMascotaRaza" placeholder="Ej: Labrador">
                                </div>
                                <div class="form-group">
                                    <label for="nuevaMascotaSexo">Sexo</label>
                                    <select id="nuevaMascotaSexo">
                                        <option value="">Seleccione...</option>
                                        <option value="macho">Macho</option>
                                        <option value="hembra">Hembra</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nuevaMascotaEdad">Edad (años)</label>
                                    <input type="number" id="nuevaMascotaEdad" min="0" max="30" step="1" placeholder="Ej: 3">
                                </div>
                                <div class="form-group">
                                    <label for="nuevaMascotaPeso">Peso (kg)</label>
                                    <input type="number" id="nuevaMascotaPeso" min="0" max="200" step="0.1" placeholder="Ej: 15.5">
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" class="btn-secondary" onclick="cerrarModalNuevaMascota()">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-check"></i>
                                    Crear Mascota y Continuar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    /**
     * Cerrar modal de nueva mascota
     */
    window.cerrarModalNuevaMascota = function() {
        const modal = document.getElementById('modalNuevaMascota');
        if (modal) {
            modal.style.display = 'none';
        }
    };

    /**
     * Guardar nueva mascota y continuar con formulario de cita
     */
    window.guardarNuevaMascota = async function(event) {
        event.preventDefault();
        
        const datos = {
            usuario_id: citaNuevoClienteId,
            nombre: document.getElementById('nuevaMascotaNombre').value,
            especie: document.getElementById('nuevaMascotaEspecie').value,
            raza: document.getElementById('nuevaMascotaRaza').value,
            sexo: document.getElementById('nuevaMascotaSexo').value,
            edad: document.getElementById('nuevaMascotaEdad').value ? parseInt(document.getElementById('nuevaMascotaEdad').value) : null,
            peso: document.getElementById('nuevaMascotaPeso').value ? parseFloat(document.getElementById('nuevaMascotaPeso').value) : null
        };
        
        try {
            const response = await fetch('admin/crear-mascota-rapido.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('success', 'Éxito', 'Mascota creada exitosamente');
                
                // Cerrar modal de mascota y abrir formulario de cita con la nueva mascota
                cerrarModalNuevaMascota();
                mostrarModalFormularioCita([result.data]);
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('error', 'Error', 'Error al crear la mascota');
        }
    };

    /**
     * Modal 3: Formulario de cita
     */
    function mostrarModalFormularioCita(mascotas) {
        let modal = document.getElementById('modalFormularioCita');
        if (!modal) {
            crearModalFormularioCita();
            modal = document.getElementById('modalFormularioCita');
        }
        
        // Actualizar información
        document.getElementById('formCitaClienteNombre').textContent = citaNuevoClienteNombre;
        document.getElementById('formCitaFecha').value = citaNuevaFecha;
        document.getElementById('formCitaHora').value = citaNuevaHora;
        
        // Llenar select de mascotas
        const selectMascota = document.getElementById('formCitaMascota');
        selectMascota.innerHTML = '<option value="">Seleccione una mascota</option>';
        mascotas.forEach(mascota => {
            const option = document.createElement('option');
            option.value = mascota.id;
            option.textContent = `${mascota.nombre} (${mascota.especie})`;
            selectMascota.appendChild(option);
        });
        
        // Cargar doctores
        cargarDoctoresEnFormularioCita();
        
        // Mostrar modal
        modal.style.display = 'flex';
    }

    /**
     * Crear modal de formulario de cita
     */
    function crearModalFormularioCita() {
        const modalHtml = `
            <div id="modalFormularioCita" class="modal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2><i class="fas fa-calendar-plus"></i> Nueva Cita</h2>
                        <button class="close-modal" onclick="cerrarModalFormularioCita()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="info-cliente-mascota">
                            <div class="info-item">
                                <label><i class="fas fa-user"></i> Cliente:</label>
                                <span id="formCitaClienteNombre"></span>
                            </div>
                        </div>
                        
                        <form id="formNuevaCita" onsubmit="guardarNuevaCita(event)">
                            <input type="hidden" id="formCitaFecha">
                            <input type="hidden" id="formCitaHora">
                            
                            <div class="form-group">
                                <label for="formCitaMascota">Mascota *</label>
                                <select id="formCitaMascota" required>
                                    <option value="">Seleccione una mascota</option>
                                </select>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="formCitaServicio">Servicio *</label>
                                    <select id="formCitaServicio" required>
                                        <option value="">Seleccione un servicio</option>
                                        <option value="consulta">Consulta</option>
                                        <option value="vacunacion">Vacunación</option>
                                        <option value="cirugia">Cirugía</option>
                                        <option value="radiologia">Radiología</option>
                                        <option value="laboratorio">Laboratorio</option>
                                        <option value="peluqueria">Peluquería</option>
                                        <option value="emergencia">Emergencia</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="formCitaDoctor">Doctor</label>
                                    <select id="formCitaDoctor">
                                        <option value="">Sin asignar</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="formCitaMotivo">Motivo de la consulta</label>
                                <textarea id="formCitaMotivo" rows="3" placeholder="Describa el motivo de la consulta..."></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" class="btn-secondary" onclick="cerrarModalFormularioCita()">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-check"></i> Crear Cita
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    /**
     * Cerrar modal de formulario de cita
     */
    window.cerrarModalFormularioCita = function() {
        const modal = document.getElementById('modalFormularioCita');
        if (modal) {
            modal.style.display = 'none';
        }
    };

    /**
     * Cargar doctores en formulario de cita
     */
    async function cargarDoctoresEnFormularioCita() {
        try {
            const response = await fetch('api/obtener-doctores.php');
            const result = await response.json();
            
            if (result.success) {
                const select = document.getElementById('formCitaDoctor');
                
                const doctores = result.data.doctores || result.data || [];
                doctores.forEach(doctor => {
                    const option = document.createElement('option');
                    option.value = doctor.id;
                    option.textContent = doctor.nombre_completo || (doctor.nombre + (doctor.especialidad ? ` - ${doctor.especialidad}` : ''));
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error al cargar doctores:', error);
        }
    }

    /**
     * Guardar nueva cita
     */
    window.guardarNuevaCita = async function(event) {
        event.preventDefault();
        
        const datos = {
            usuario_id: citaNuevoClienteId,
            mascota_id: parseInt(document.getElementById('formCitaMascota').value),
            fecha_cita: document.getElementById('formCitaFecha').value,
            hora_cita: document.getElementById('formCitaHora').value,
            duracion_minutos: citaNuevaDuracion,
            servicio: document.getElementById('formCitaServicio').value,
            doctor_id: document.getElementById('formCitaDoctor').value ? parseInt(document.getElementById('formCitaDoctor').value) : null,
            motivo: document.getElementById('formCitaMotivo').value
        };
        
        try {
            const response = await fetch('admin/crear-cita.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('success', 'Éxito', result.message);
                cerrarModalFormularioCita();
                
                // Recargar vista diaria
                if (fechaActualmenteVista) {
                    verDiaDetalle(fechaActualmenteVista);
                }
            } else {
                showToast('error', 'Error', result.message);
            }
        } catch (error) {
            console.error('Error al crear cita:', error);
            showToast('error', 'Error', 'Error al crear la cita');
        }
    };

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        inicializarCalendario();
    });
})();

