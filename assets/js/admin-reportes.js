/**
 * ADMIN - Gestión de Reportes
 * Funcionalidad para la página admin/reportes/gestionar-reportes.php
 */

(function () {
    console.debug('[Reportes] admin-reportes.js cargado');

    const modals = {
        diario: document.getElementById('modalReporteDiario'),
        semanal: document.getElementById('modalReporteSemanal'),
        mensual: document.getElementById('modalReporteMensual')
    };

    const forms = {
        diario: document.getElementById('formReporteDiario'),
        semanal: document.getElementById('formReporteSemanal'),
        mensual: document.getElementById('formReporteMensual')
    };

    const selectsMedico = {
        diario: document.getElementById('medico_diario'),
        semanal: document.getElementById('medico_semanal'),
        mensual: document.getElementById('medico_mensual')
    };

    const inputsFecha = {
        diario: document.getElementById('fecha_diaria'),
        semanaInicio: document.getElementById('fecha_inicio_semanal'),
        semanaFin: document.getElementById('fecha_fin_semanal'),
        mes: document.getElementById('mes_mensual')
    };

    document.addEventListener('DOMContentLoaded', initReportes);

    function initReportes() {
        if (!modals.diario || !forms.diario || !selectsMedico.diario || !inputsFecha.diario) {
            console.warn('[Reportes] Elementos base del reporte diario no encontrados. Verifica la estructura del HTML.');
            return;
        }

        establecerFechasIniciales();
        cargarMedicos();
        document.addEventListener('keydown', manejarEscape);
    }

    function establecerFechasIniciales() {
        setFechaDiaria();
        setSemanaActual();
        setMesActual();
    }

    function setFechaDiaria() {
        if (!inputsFecha.diario) return;
        inputsFecha.diario.value = formatearFechaInput(new Date());
    }

    function setSemanaActual() {
        if (!inputsFecha.semanaInicio || !inputsFecha.semanaFin) return;
        const hoy = new Date();
        const diaSemana = hoy.getDay();
        const diferenciaInicio = diaSemana === 0 ? -6 : 1 - diaSemana;
        const fechaInicio = new Date(hoy);
        fechaInicio.setDate(hoy.getDate() + diferenciaInicio);
        const fechaFin = new Date(fechaInicio);
        fechaFin.setDate(fechaInicio.getDate() + 6);
        inputsFecha.semanaInicio.value = formatearFechaInput(fechaInicio);
        inputsFecha.semanaFin.value = formatearFechaInput(fechaFin);
    }

    function setMesActual() {
        if (!inputsFecha.mes) return;
        inputsFecha.mes.value = formatearMesInput(new Date());
    }

    function formatearFechaInput(fecha) {
        return fecha.toISOString().split('T')[0];
    }

    function formatearMesInput(fecha) {
        const year = fecha.getFullYear();
        const month = String(fecha.getMonth() + 1).padStart(2, '0');
        return `${year}-${month}`;
    }

    async function cargarMedicos() {
        try {
            const response = await fetch('api/obtener-doctores.php');
            const data = await response.json();

            const medicos =
                (data?.data && Array.isArray(data.data.doctores) ? data.data.doctores : null) ||
                (Array.isArray(data?.medicos) ? data.medicos : null);

            if (data.success && medicos) {
                Object.values(selectsMedico).forEach((select) => {
                    if (!select) return;
                    select.innerHTML = '<option value="">-- Seleccione un médico --</option>';

                    const optionTodos = document.createElement('option');
                    optionTodos.value = 'todos';
                    optionTodos.textContent = '📋 Todos los médicos';
                    select.appendChild(optionTodos);

                    medicos.forEach((medico) => {
                        const option = document.createElement('option');
                        option.value = medico.id;
                        option.textContent = `${medico.nombre}${medico.especialidad ? ' - ' + medico.especialidad : ''}`;
                        select.appendChild(option);
                    });
                });
            } else {
                mostrarToast('info', 'Información', 'No se encontraron médicos activos.');
            }
        } catch (error) {
            console.error('[Reportes] Error al cargar médicos:', error);
            mostrarToast('error', 'Error', 'No se pudieron cargar los médicos. Intenta nuevamente.');
        }
    }

    function manejarEscape(event) {
        if (event.key !== 'Escape') return;

        Object.entries(modals).forEach(([clave, modal]) => {
            if (modal && modal.classList.contains('show')) {
                cerrarModal(clave);
            }
        });
    }

    function abrirModal(tipo) {
        const modal = modals[tipo];
        if (!modal) return;
        modal.classList.add('show');
    }

    function cerrarModal(tipo) {
        const modal = modals[tipo];
        const form = forms[tipo];
        if (!modal || !form) return;
        modal.classList.remove('show');
        form.reset();

        switch (tipo) {
            case 'diario':
                setFechaDiaria();
                break;
            case 'semanal':
                setSemanaActual();
                break;
            case 'mensual':
                setMesActual();
                break;
            default:
                break;
        }
    }

    // Funciones expuestas globalmente para los atributos onclick
    window.mostrarModalReporteDiario = () => abrirModal('diario');
    window.cerrarModalReporteDiario = () => cerrarModal('diario');

    window.mostrarModalReporteSemanal = () => abrirModal('semanal');
    window.cerrarModalReporteSemanal = () => cerrarModal('semanal');

    window.mostrarModalReporteMensual = () => abrirModal('mensual');
    window.cerrarModalReporteMensual = () => cerrarModal('mensual');

    window.generarReporteDiario = function generarReporteDiario() {
        console.debug('[Reportes] generarReporteDiario llamado');
        const form = forms.diario;
        const selectMedico = selectsMedico.diario;
        const fecha = inputsFecha.diario;
        if (!form || !selectMedico || !fecha) return;

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const medicoId = selectMedico.value;

        if (!medicoId) {
            alert('Por favor, selecciona un médico');
            return;
        }

        const url = `admin/reportes/generar-reporte-diario.php?medico_id=${encodeURIComponent(medicoId)}&fecha=${encodeURIComponent(fecha.value)}`;
        window.open(url, '_blank');
        cerrarModal('diario');
    };

    window.generarReporteSemanal = function generarReporteSemanal() {
        console.debug('[Reportes] generarReporteSemanal llamado');
        const form = forms.semanal;
        const selectMedico = selectsMedico.semanal;
        const fechaInicio = inputsFecha.semanaInicio;
        const fechaFin = inputsFecha.semanaFin;

        if (!form || !selectMedico || !fechaInicio || !fechaFin) return;

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        if (!selectMedico.value) {
            alert('Por favor, selecciona un médico');
            return;
        }

        if (fechaFin.value < fechaInicio.value) {
            alert('La fecha de término no puede ser anterior a la fecha de inicio');
            return;
        }

        const url = `admin/reportes/generar-reporte-semanal.php?medico_id=${encodeURIComponent(selectMedico.value)}&fecha_inicio=${encodeURIComponent(fechaInicio.value)}&fecha_fin=${encodeURIComponent(fechaFin.value)}`;
        window.open(url, '_blank');
        cerrarModal('semanal');
    };

    window.generarReporteMensual = function generarReporteMensual() {
        console.debug('[Reportes] generarReporteMensual llamado');
        const form = forms.mensual;
        const selectMedico = selectsMedico.mensual;
        const inputMes = inputsFecha.mes;

        if (!form || !selectMedico || !inputMes) return;

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        if (!selectMedico.value) {
            alert('Por favor, selecciona un médico');
            return;
        }

        const url = `admin/reportes/generar-reporte-mensual.php?medico_id=${encodeURIComponent(selectMedico.value)}&mes=${encodeURIComponent(inputMes.value)}`;
        window.open(url, '_blank');
        cerrarModal('mensual');
    };

    // Fallback simple para mostrar toasts si la función global no existe
    function mostrarToast(tipo, titulo, mensaje) {
        if (typeof window.mostrarToastGlobal === 'function') {
            window.mostrarToastGlobal(tipo, titulo, mensaje);
        } else {
            console.log(`[${tipo.toUpperCase()}] ${titulo}: ${mensaje}`);
        }
    }
})();
