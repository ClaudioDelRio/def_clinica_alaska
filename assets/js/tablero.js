/* ============================================
   TABLERO JAVASCRIPT - CLÍNICA VETERINARIA ALASKA
   Panel de Usuario - Gestión de Mascotas y Reservas
   Desarrollado por: Claudio del Rio - Web.malgarini®
   ============================================ */

// Configuración de la API
const API_URL = './api/';

// Estado global de la aplicación
let datosUsuario = null;
let datosMascotas = [];
let datosHistorial = [];
let datosDoctores = [];

/* ============================================
   INICIALIZACIÓN
   ============================================ */

document.addEventListener('DOMContentLoaded', async () => {
    console.log('💻 Tablero cargando...');
    
    // Verificar sesión
    await verificarSesion();
    
    // Cargar datos del usuario
    await cargarDatosUsuario();
    
    // Cargar mascotas
    await cargarMascotas();
    
    // Cargar historial
    await cargarHistorial();
    
    // Cargar doctores disponibles
    await cargarDoctores();
    
    // Inicializar event listeners
    inicializarEventListeners();
    
    console.log('✅ Tablero cargado correctamente');
});

/* ============================================
   VERIFICACIÓN DE SESIÓN
   ============================================ */

async function verificarSesion() {
    try {
        const response = await fetch(API_URL + 'verificar-sesion.php');
        const data = await response.json();
        
        if (!data.success || !data.data.logueado) {
            window.location.href = 'index.html';
            return;
        }
    } catch (error) {
        console.error('❌ Error al verificar sesión:', error);
        mostrarMensaje('Error al verificar la sesión', 'error');
        window.location.href = 'index.html';
    }
}

/* ============================================
   CARGAR DATOS DEL USUARIO
   ============================================ */

async function cargarDatosUsuario() {
    try {
        const response = await fetch(API_URL + 'obtener-datos-usuario.php');
        const data = await response.json();
        
        if (data.success) {
            datosUsuario = data.data;
            mostrarDatosUsuario();
        } else {
            console.error('❌ Error al cargar datos:', data.message);
        }
    } catch (error) {
        console.error('❌ Error al cargar datos del usuario:', error);
        mostrarMensaje('Error al cargar los datos del usuario', 'error');
    }
}

function mostrarDatosUsuario() {
    if (!datosUsuario) return;
    
    const { usuario, estadisticas } = datosUsuario;
    
    // Actualizar header
    document.getElementById('userAvatar').textContent = usuario.iniciales;
    document.getElementById('userName').textContent = usuario.nombre;
    
    // Actualizar perfil
    document.getElementById('profileNombre').textContent = usuario.nombre;
    document.getElementById('profileEmail').textContent = usuario.email;
    document.getElementById('profileTelefono').textContent = usuario.telefono;
    document.getElementById('profileDireccion').textContent = usuario.direccion;
    
    // Actualizar estadísticas
    document.getElementById('statMascotas').textContent = `${estadisticas.total_mascotas} mascota${estadisticas.total_mascotas !== 1 ? 's' : ''}`;
    document.getElementById('statCitas').textContent = `${estadisticas.total_citas} cita${estadisticas.total_citas !== 1 ? 's' : ''}`;
    document.getElementById('statProximaCita').textContent = estadisticas.proxima_cita;
}

/* ============================================
   GESTIÓN DE MASCOTAS
   ============================================ */

async function cargarMascotas() {
    try {
        const response = await fetch(API_URL + 'obtener-mascotas.php');
        const data = await response.json();
        
        if (data.success) {
            datosMascotas = data.data.mascotas;
            mostrarMascotas();
            llenarSelectMascotas();
        } else {
            console.error('❌ Error al cargar mascotas:', data.message);
        }
    } catch (error) {
        console.error('❌ Error al cargar mascotas:', error);
        mostrarMensaje('Error al cargar las mascotas', 'error');
    }
}

function mostrarMascotas() {
    const container = document.getElementById('mascotasGrid');
    const addCard = document.getElementById('btnAgregarMascota');
    
    // Limpiar todas las cards excepto la de agregar
    const existingCards = container.querySelectorAll('.dashboard-mascota-card:not(.dashboard-add-pet-card)');
    existingCards.forEach(card => card.remove());
    
    // Si no hay mascotas, mostrar mensaje
    if (datosMascotas.length === 0) {
        const emptyMessage = document.createElement('div');
        emptyMessage.className = 'dashboard-empty-state';
        emptyMessage.style.gridColumn = '1 / -1';
        emptyMessage.innerHTML = `
            <i class="fas fa-paw"></i>
            <h3>No tienes mascotas registradas</h3>
            <p>Haz clic en "Agregar Nueva Mascota" para comenzar</p>
        `;
        container.insertBefore(emptyMessage, addCard);
        return;
    }
    
    // Agregar cada mascota
    datosMascotas.forEach(mascota => {
        const card = crearCardMascota(mascota);
        container.insertBefore(card, addCard);
    });
}

function crearCardMascota(mascota) {
    const card = document.createElement('div');
    card.className = 'dashboard-mascota-card';
    card.dataset.mascotaId = mascota.id;
    
    const vacunasTexto = mascota.vacunas_al_dia ? 'Vacunas al día' : 'Vacunas pendientes';
    const edadTexto = mascota.edad === 1 ? '1 año' : `${mascota.edad} años`;
    
    card.innerHTML = `
        <div class="dashboard-mascota-header">
            <div class="dashboard-mascota-avatar">
                <i class="fas ${mascota.icono}"></i>
            </div>
            <div class="dashboard-mascota-info">
                <h3>${mascota.nombre}</h3>
                <span>${mascota.raza}</span>
            </div>
        </div>
        <div class="dashboard-mascota-details">
            <div class="dashboard-mascota-detail">
                <i class="fas fa-birthday-cake"></i>
                <span>${edadTexto}</span>
            </div>
            <div class="dashboard-mascota-detail">
                <i class="fas fa-venus-mars"></i>
                <span>${mascota.sexo.charAt(0).toUpperCase() + mascota.sexo.slice(1)}</span>
            </div>
            <div class="dashboard-mascota-detail">
                <i class="fas fa-weight"></i>
                <span>${mascota.peso} kg</span>
            </div>
            <div class="dashboard-mascota-detail">
                <i class="fas fa-syringe"></i>
                <span>${vacunasTexto}</span>
            </div>
        </div>
        <div class="dashboard-mascota-actions">
            <button class="dashboard-btn-small dashboard-btn-edit" onclick="editarMascota(${mascota.id})">
                <i class="fas fa-edit"></i>
                Editar
            </button>
            <button class="dashboard-btn-small dashboard-btn-delete" onclick="eliminarMascota(${mascota.id}, '${mascota.nombre}')">
                <i class="fas fa-trash"></i>
                Eliminar
            </button>
        </div>
    `;
    
    return card;
}

function abrirModalMascota() {
    // Limpiar formulario
    document.getElementById('formMascota').reset();
    
    // Mostrar modal
    const modal = document.getElementById('modalMascota');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function cerrarModalMascota() {
    const modal = document.getElementById('modalMascota');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

async function guardarMascota(event) {
    event.preventDefault();
    
    // Obtener valores del formulario
    const formData = new FormData(event.target);
    const nombre = formData.get('nombre').trim();
    const especie = formData.get('especie');
    const raza = formData.get('raza')?.trim() || null;
    const edad = formData.get('edad') ? parseInt(formData.get('edad')) : null;
    const sexo = formData.get('sexo') || null;
    const peso = formData.get('peso') ? parseFloat(formData.get('peso')) : null;
    const color = formData.get('color')?.trim() || null;
    const vacunas_al_dia = parseInt(formData.get('vacunas_al_dia'));
    
    // Validaciones básicas
    if (!nombre || !especie) {
        mostrarMensaje('Por favor, completa los campos obligatorios (Nombre y Especie)', 'error');
        return;
    }
    
    try {
        const response = await fetch(API_URL + 'agregar-mascota.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                nombre,
                especie,
                raza,
                edad,
                sexo,
                peso,
                color,
                vacunas_al_dia
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarMensaje(data.message, 'success');
            await cargarMascotas();
            await cargarDatosUsuario();
            cerrarModalMascota();
        } else {
            mostrarMensaje('Error: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('❌ Error al agregar mascota:', error);
        mostrarMensaje('Error al agregar la mascota', 'error');
    }
}

function editarMascota(id) {
    mostrarMensaje('Función de editar mascota en desarrollo. ID: ' + id, 'info');
    // Aquí se implementará un modal con formulario
}

async function eliminarMascota(id, nombre) {
    if (!confirm(`¿Estás seguro de que deseas eliminar a ${nombre}?`)) {
        return;
    }
    
    try {
        const response = await fetch(API_URL + 'eliminar-mascota.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id })
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarMensaje(data.message, 'success');
            await cargarMascotas();
            await cargarDatosUsuario();
        } else {
            mostrarMensaje('Error: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('❌ Error al eliminar mascota:', error);
        mostrarMensaje('Error al eliminar la mascota', 'error');
    }
}

/* ============================================
   RESERVAR HORA
   ============================================ */

async function reservarHora(event) {
    event.preventDefault();
    
    const mascotaId = document.getElementById('selectMascota').value;
    const servicio = document.getElementById('selectServicio').value;
    const fechaCita = document.getElementById('inputFecha').value;
    const horaCita = document.getElementById('selectHora').value;
    const motivo = document.getElementById('textareaMotivo').value;
    const doctorId = document.getElementById('selectDoctor') ? document.getElementById('selectDoctor').value : '';
    
    // Validaciones básicas
    if (!mascotaId || !servicio || !fechaCita || !horaCita || !motivo) {
        mostrarMensaje('Por favor, completa todos los campos obligatorios', 'error');
        return;
    }
    
    try {
        const response = await fetch(API_URL + 'reservar-hora.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                mascota_id: parseInt(mascotaId),
                doctor_id: doctorId ? parseInt(doctorId) : null,
                servicio,
                fecha_cita: fechaCita,
                hora_cita: horaCita,
                motivo
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            let mensaje = data.message + '\n\n';
            mensaje += `📅 Fecha: ${data.data.fecha}\n`;
            mensaje += `🕐 Hora: ${data.data.hora}\n`;
            mensaje += `🐾 Mascota: ${data.data.mascota}\n`;
            mensaje += `💉 Servicio: ${data.data.servicio}`;
            
            mostrarMensaje(mensaje, 'success');
            
            // Limpiar formulario
            document.getElementById('formReservarHora').reset();
            document.getElementById('selectHora').disabled = true;
            document.getElementById('selectHora').innerHTML = '<option value="">-- Primero selecciona una fecha --</option>';
            
            // Recargar datos
            await cargarHistorial();
            await cargarDatosUsuario();
            
            // Cambiar a pestaña de historial
            document.querySelector('[data-tab="historial"]').click();
        } else {
            mostrarMensaje('Error: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('❌ Error al reservar hora:', error);
        mostrarMensaje('Error al reservar la hora', 'error');
    }
}

/* ============================================
   HORARIOS DISPONIBLES
   ============================================ */

async function cargarHorariosDisponibles(fecha) {
    const horaSelect = document.getElementById('selectHora');
    const horaInfo = document.getElementById('horaInfo');
    
    // Limpiar select
    horaSelect.innerHTML = '<option value="">Cargando horarios...</option>';
    horaSelect.disabled = true;
    
    try {
        const response = await fetch(API_URL + 'obtener-horarios-disponibles.php?fecha=' + fecha);
        const data = await response.json();
        
        if (data.success) {
            horaSelect.innerHTML = '';
            
            const horarios = data.data.horarios_disponibles;
            
            if (horarios.length === 0) {
                horaSelect.innerHTML = '<option value="">No hay horarios disponibles</option>';
                horaInfo.style.display = 'block';
                horaInfo.textContent = '❌ No hay horarios disponibles para esta fecha';
                horaInfo.style.color = '#dc3545';
            } else {
                horaSelect.innerHTML = '<option value="">-- Selecciona una hora --</option>';
                
                horarios.forEach(horario => {
                    const option = document.createElement('option');
                    option.value = horario.hora;
                    option.textContent = horario.texto;
                    horaSelect.appendChild(option);
                });
                
                horaSelect.disabled = false;
                horaInfo.style.display = 'block';
                horaInfo.textContent = `✅ ${horarios.length} horario(s) disponible(s)`;
                horaInfo.style.color = '#28a745';
            }
        } else {
            horaSelect.innerHTML = '<option value="">Error al cargar horarios</option>';
            mostrarMensaje('Error: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('❌ Error al cargar horarios:', error);
        horaSelect.innerHTML = '<option value="">Error al cargar horarios</option>';
        mostrarMensaje('Error al cargar los horarios disponibles', 'error');
    }
}

/* ============================================
   HISTORIAL DE CITAS
   ============================================ */

async function cargarHistorial() {
    try {
        const response = await fetch(API_URL + 'obtener-historial.php');
        const data = await response.json();
        
        if (data.success) {
            datosHistorial = data.data.citas;
            mostrarHistorial();
        } else {
            console.error('❌ Error al cargar historial:', data.message);
        }
    } catch (error) {
        console.error('❌ Error al cargar historial:', error);
        mostrarMensaje('Error al cargar el historial', 'error');
    }
}

function mostrarHistorial() {
    const container = document.getElementById('historialList');
    
    // Limpiar contenedor
    container.innerHTML = '';
    
    if (datosHistorial.length === 0) {
        container.innerHTML = `
            <div class="dashboard-empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>No hay citas registradas</h3>
                <p>Aún no has reservado ninguna hora</p>
            </div>
        `;
        return;
    }
    
    // Agregar cada cita
    datosHistorial.forEach(cita => {
        const item = crearItemHistorial(cita);
        container.appendChild(item);
    });
}

function crearItemHistorial(cita) {
    const item = document.createElement('div');
    item.className = 'dashboard-historial-item';
    
    const titulo = `${cita.servicio} - ${cita.mascota_nombre}`;
    const doctorInfo = cita.doctor_nombre ? cita.doctor_nombre : 'Sin doctor asignado';
    
    item.innerHTML = `
        <div class="dashboard-historial-left">
            <div class="dashboard-historial-icon">
                <i class="fas ${cita.icono}"></i>
            </div>
            <div class="dashboard-historial-info">
                <h4>${titulo}</h4>
                <p>👨‍⚕️ ${doctorInfo}</p>
            </div>
        </div>
        <div>
            <span class="dashboard-badge ${cita.badge_class}">${cita.estado_texto}</span>
            <p class="dashboard-historial-date">${cita.fecha_formateada} - ${cita.hora_cita.substring(0,5)}</p>
        </div>
    `;
    
    return item;
}

/* ============================================
   EVENT LISTENERS
   ============================================ */

function inicializarEventListeners() {
    // Sistema de tabs
    const tabs = document.querySelectorAll('.dashboard-tab');
    const sections = document.querySelectorAll('.dashboard-content-section');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            sections.forEach(s => s.classList.remove('active'));
            const targetSection = document.getElementById(`section-${tab.dataset.tab}`);
            targetSection.classList.add('active');
        });
    });

    // Botón de logout
    document.getElementById('btnLogout').addEventListener('click', async () => {
        if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
            try {
                const response = await fetch(API_URL + 'logout.php', {
                    method: 'POST'
                });
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = 'index.html';
                }
            } catch (error) {
                console.error('❌ Error al cerrar sesión:', error);
                window.location.href = 'index.html';
            }
        }
    });

    // Botón agregar mascota
    document.getElementById('btnAgregarMascota').addEventListener('click', abrirModalMascota);
    
    // Modal de mascota - Cerrar con X
    document.getElementById('closeMascotaModal').addEventListener('click', cerrarModalMascota);
    
    // Modal de mascota - Cerrar con botón cancelar
    document.getElementById('btnCancelarMascota').addEventListener('click', cerrarModalMascota);
    
    // Modal de mascota - Cerrar al hacer clic fuera del modal
    document.getElementById('modalMascota').addEventListener('click', (e) => {
        if (e.target.id === 'modalMascota') {
            cerrarModalMascota();
        }
    });
    
    // Formulario de mascota
    document.getElementById('formMascota').addEventListener('submit', guardarMascota);

    // Formulario de reserva
    document.getElementById('formReservarHora').addEventListener('submit', reservarHora);
    
    // Si existe select de doctor, nada más que preparar contenedor (se llena al cargar)
    
    // Fecha mínima para el input de fecha
    const fechaInput = document.getElementById('inputFecha');
    if (fechaInput) {
        const today = new Date().toISOString().split('T')[0];
        fechaInput.setAttribute('min', today);
        
        // Event listener para cargar horarios cuando se selecciona una fecha
        fechaInput.addEventListener('change', (e) => {
            const fechaSeleccionada = e.target.value;
            if (fechaSeleccionada) {
                cargarHorariosDisponibles(fechaSeleccionada);
            }
        });
    }
}

function llenarSelectMascotas() {
    const select = document.getElementById('selectMascota');
    
    if (!select) return;
    
    // Limpiar opciones existentes (excepto la primera)
    while (select.options.length > 1) {
        select.remove(1);
    }
    
    // Agregar mascotas
    datosMascotas.forEach(mascota => {
        const option = document.createElement('option');
        option.value = mascota.id;
        option.textContent = `${mascota.nombre} - ${mascota.raza}`;
        select.appendChild(option);
    });
}

/* ============================================
   DOCTORES DISPONIBLES
   ============================================ */
async function cargarDoctores() {
    try {
        const response = await fetch(API_URL + 'obtener-doctores.php');
        const data = await response.json();
        if (data.success) {
            datosDoctores = data.data.doctores || [];
            llenarSelectDoctores();
        } else {
            console.error('❌ Error al cargar doctores:', data.message);
        }
    } catch (error) {
        console.error('❌ Error al cargar doctores:', error);
    }
}

function llenarSelectDoctores() {
    const select = document.getElementById('selectDoctor');
    if (!select) return;
    // Limpiar opciones (dejar placeholder)
    while (select.options.length > 1) {
        select.remove(1);
    }
    datosDoctores.forEach(doc => {
        const option = document.createElement('option');
        option.value = doc.id;
        option.textContent = doc.nombre_completo || doc.nombre;
        select.appendChild(option);
    });
}

/* ============================================
   SISTEMA DE MENSAJES
   ============================================ */

function mostrarMensaje(mensaje, tipo = 'info') {
    // Crear elemento de mensaje
    const mensajeDiv = document.createElement('div');
    mensajeDiv.className = `mensaje-${tipo}`;
    mensajeDiv.textContent = mensaje;
    mensajeDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background: ${tipo === 'success' ? '#4CAF50' : tipo === 'error' ? '#f44336' : '#2196F3'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        z-index: 10000;
        animation: slideIn 0.3s ease;
        max-width: 400px;
        font-family: 'Poppins', sans-serif;
    `;
    
    document.body.appendChild(mensajeDiv);
    
    // Remover después de 4 segundos
    setTimeout(() => {
        mensajeDiv.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => mensajeDiv.remove(), 300);
    }, 4000);
}

// Agregar animaciones al head si no existen
if (!document.getElementById('toast-animations')) {
    const style = document.createElement('style');
    style.id = 'toast-animations';
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
}

console.log('📝 Tablero JS - Clínica Veterinaria Alaska v1.0');

