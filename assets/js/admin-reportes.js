/**
 * ADMIN - Gestión de Reportes
 * Funcionalidad para la página admin/reportes/gestionar-reportes.php
 */

(function () {
    const MODAL_ID = 'modalReporteDiario';
    const FORM_ID = 'formReporteDiario';
    const SELECT_MEDICO_ID = 'medico_id';
    const INPUT_FECHA_ID = 'fecha_reporte';

    let modal;
    let form;
    let selectMedico;
    let inputFecha;

    console.debug('[Reportes] admin-reportes.js cargado');
    document.addEventListener('DOMContentLoaded', initReportes);

    function initReportes() {
        modal = document.getElementById(MODAL_ID);
        form = document.getElementById(FORM_ID);
        selectMedico = document.getElementById(SELECT_MEDICO_ID);
        inputFecha = document.getElementById(INPUT_FECHA_ID);

        if (!modal || !form || !selectMedico || !inputFecha) {
            console.warn('[Reportes] Elementos del modal no encontrados. Verifica la estructura del HTML.');
            return;
        }

        setFechaActual();
        cargarMedicos();
        document.addEventListener('keydown', manejarEscape);
    }

    function setFechaActual() {
        inputFecha.value = new Date().toISOString().split('T')[0];
    }

    async function cargarMedicos() {
        try {
            const response = await fetch('api/obtener-doctores.php');
            const data = await response.json();

            // Reiniciar opciones
            selectMedico.innerHTML = '<option value="">-- Seleccione un médico --</option>';

            const medicos =
                (data?.data && Array.isArray(data.data.doctores) ? data.data.doctores : null) ||
                (Array.isArray(data?.medicos) ? data.medicos : null);

            if (data.success && medicos) {
                // Opción para todos los médicos
                const optionTodos = document.createElement('option');
                optionTodos.value = 'todos';
                optionTodos.textContent = '📋 Todos los médicos';
                selectMedico.appendChild(optionTodos);

                medicos.forEach((medico) => {
                    const option = document.createElement('option');
                    option.value = medico.id;
                    option.textContent = `${medico.nombre}${medico.especialidad ? ' - ' + medico.especialidad : ''}`;
                    selectMedico.appendChild(option);
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
        if (event.key === 'Escape' && modal && modal.classList.contains('show')) {
            cerrarModalReporteDiario();
        }
    }

    // Funciones expuestas globalmente para los atributos onclick
    window.mostrarModalReporteDiario = function mostrarModalReporteDiario() {
        console.debug('[Reportes] mostrarModalReporteDiario llamado');
        if (!modal) return;
        console.debug('[Reportes] Estado previo modal:', modal.className);
        modal.classList.add('show');
        console.debug('[Reportes] Clase agregada, estado actual:', modal.className);
    };

    window.cerrarModalReporteDiario = function cerrarModalReporteDiario() {
        console.debug('[Reportes] cerrarModalReporteDiario llamado');
        if (!modal) return;
        modal.classList.remove('show');
        form.reset();
        setFechaActual();
    };

    window.generarReporteDiario = function generarReporteDiario() {
        console.debug('[Reportes] generarReporteDiario llamado');
        if (!form || !selectMedico || !inputFecha) return;

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const medicoId = selectMedico.value;
        const fecha = inputFecha.value;

        if (!medicoId) {
            alert('Por favor, selecciona un médico');
            return;
        }

        const url = `admin/reportes/generar-reporte-diario.php?medico_id=${encodeURIComponent(medicoId)}&fecha=${encodeURIComponent(fecha)}`;
        console.debug('[Reportes] Abriendo URL:', url);
        window.open(url, '_blank');
        window.cerrarModalReporteDiario();
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

