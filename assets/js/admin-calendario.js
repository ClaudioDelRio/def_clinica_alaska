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
            return '<div class="no-cita">Libre</div>';
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
    window.eliminarCita = async function(citaId) {
        if (!confirm('¿Está seguro de que desea eliminar esta cita? Esta acción no se puede deshacer.')) {
            return;
        }
        
        try {
            const response = await fetch('admin/eliminar-cita.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ cita_id: citaId })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('success', 'Éxito', result.message);
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
        modal.style.display = 'block';
        
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
                
                // Agregar doctores
                result.data.forEach(doctor => {
                    const option = document.createElement('option');
                    option.value = doctor.id;
                    option.textContent = doctor.nombre + (doctor.especialidad ? ` - ${doctor.especialidad}` : '');
                    select.appendChild(option);
                });
                
                // Restaurar valor seleccionado
                select.value = selectedValue;
            }
        } catch (error) {
            console.error('Error al cargar doctores:', error);
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

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        inicializarCalendario();
    });
})();

