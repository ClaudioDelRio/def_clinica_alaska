/* ============================================
   JAVASCRIPT - FUNCIONALIDAD VETERINARIA
   ============================================ */

// Espero a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    
    /* ============================================
       MENÚ HAMBURGUESA - FUNCIONALIDAD MÓVIL
       ============================================ */
    
    // Selecciono elementos necesarios para el menú móvil
    const menuHamburguesa = document.querySelector('.menu-hamburguesa');
    const menuNavegacion = document.querySelector('.menu-navegacion');
    const enlacesMenu = document.querySelectorAll('.menu-navegacion a');
    
    // Funcionalidad del menú hamburguesa
    if (menuHamburguesa) {
        
        // Toggle del menú al hacer clic en el botón hamburguesa
        menuHamburguesa.addEventListener('click', function() {
            this.classList.toggle('activo');
            menuNavegacion.classList.toggle('activo');
            document.body.style.overflow = menuNavegacion.classList.contains('activo') ? 'hidden' : '';
        });
        
        // Cierre automático del menú al hacer clic en un enlace
        enlacesMenu.forEach(enlace => {
            enlace.addEventListener('click', function() {
                menuHamburguesa.classList.remove('activo');
                menuNavegacion.classList.remove('activo');
                document.body.style.overflow = '';
            });
        });
        
        // Cierre del menú al hacer clic fuera de él
        document.addEventListener('click', function(e) {
            if (!menuNavegacion.contains(e.target) && !menuHamburguesa.contains(e.target)) {
                menuHamburguesa.classList.remove('activo');
                menuNavegacion.classList.remove('activo');
                document.body.style.overflow = '';
            }
        });
    }
    
    /* ============================================
       HEADER CON CAMBIO DE FONDO AL HACER SCROLL
       ============================================ */
    
    const header = document.querySelector('.encabezado');
    let lastScrollTop = 0;
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Cambiar el fondo del header después de 100px de scroll
        if (scrollTop > 100) {
            header.classList.add('scroll-activo');
        } else {
            header.classList.remove('scroll-activo');
        }
        
        lastScrollTop = scrollTop;
    });
    
    /* ============================================
       SCROLL SUAVE - NAVEGACIÓN INTERNA
       ============================================ */
    
    // Scroll suave para enlaces internos (anclas)
    const enlaces = document.querySelectorAll('a[href^="#"]');
    
    enlaces.forEach(enlace => {
        enlace.addEventListener('click', function(e) {
            e.preventDefault();
            const destino = document.querySelector(this.getAttribute('href'));
            if (destino) {
                destino.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    /* ============================================
       GALERÍA DE ANIMALES - EFECTOS HOVER (USO FUTURO)
       ============================================ */
    
    // Efectos hover para imágenes pequeñas de animales
    const animalesPequenos = document.querySelectorAll('.animal-pequeno');
    
    if (animalesPequenos.length > 0) {
        animalesPequenos.forEach(animal => {
            animal.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.1)';
            });
            
            animal.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    }
    
    /* ============================================
       INTERSECTION OBSERVER - ANIMACIONES DE ENTRADA
       ============================================ */
    
    // Observador para animaciones cuando los elementos entran en la vista
    const observador = new IntersectionObserver((entradas) => {
        entradas.forEach(entrada => {
            if (entrada.isIntersecting) {
                entrada.target.style.opacity = '1';
                entrada.target.style.transform = 'translateY(0)';
            }
        });
    });
    
    // Elementos con animación de fade in al aparecer
    const elementosAnimados = document.querySelectorAll('.titulo-principal, .subtitulo, .boton-principal');
    elementosAnimados.forEach(elemento => {
        elemento.style.opacity = '0';
        elemento.style.transform = 'translateY(30px)';
        elemento.style.transition = 'all 0.6s ease';
        observador.observe(elemento);
    });
    
    /* ============================================
       ANIMACIÓN DE NÚMEROS - ESTADÍSTICAS
       ============================================ */
    
    // Función para animar los números de las estadísticas
    function animarNumeros() {
        const numeros = document.querySelectorAll('.stat-numero');
        
        numeros.forEach(numero => {
            const target = parseInt(numero.getAttribute('data-target'));
            const incremento = target / 100; // Duración aproximada de 2 segundos
            let actual = 1;
            
            const timer = setInterval(() => {
                actual += incremento;
                
                if (actual >= target) {
                    clearInterval(timer);
                    // Formatear el número final
                    if (target === 14000) {
                        numero.textContent = '14,000+';
                    } else if (target === 18) {
                        numero.textContent = '18+';
                    } else if (target === 100) {
                        numero.textContent = '100%';
                    }
                } else {
                    // Formatear números durante la animación
                    if (target === 14000) {
                        numero.textContent = Math.floor(actual).toLocaleString('es-CL');
                    } else {
                        numero.textContent = Math.floor(actual);
                    }
                }
            }, 20);
        });
    }
    
    // Observer para detectar cuando la sección de estadísticas entra en vista
    const estadisticasObserver = new IntersectionObserver((entradas) => {
        entradas.forEach(entrada => {
            if (entrada.isIntersecting) {
                animarNumeros();
                estadisticasObserver.unobserve(entrada.target);
            }
        });
    }, { threshold: 0.5 });
    
    // Observar la sección de estadísticas
    const estadisticas = document.querySelector('.estadisticas');
    if (estadisticas) {
        estadisticasObserver.observe(estadisticas);
    }
    
    /* ============================================
       NAVEGACIÓN ACTIVA - FOOTER
       ============================================ */
    
    // Resaltado del icono activo en el footer
    const iconosNav = document.querySelectorAll('.icono-nav');
    iconosNav.forEach(icono => {
        icono.addEventListener('click', function() {
            iconosNav.forEach(i => i.classList.remove('activo'));
            this.classList.add('activo');
        });
    });
    
    /* ============================================
       MODAL DE LOGIN / REGISTRO
       ============================================ */
    
    console.log('🚀 Iniciando configuración del modal...');
    
    // Elementos del modal de login
    const modalLogin = document.getElementById('modalLogin');
    console.log('Modal encontrado:', modalLogin ? '✅' : '❌', modalLogin);
    
    if (!modalLogin) {
        console.error('❌ ERROR: No se encuentra el modal con id="modalLogin"');
        return;
    }
    
    const modalContainer = modalLogin.querySelector('.modal-container');
    const closeModalBtn = document.getElementById('closeModal');
    const signupLink = modalLogin.querySelector('.signup-link');
    const signinLink = modalLogin.querySelector('.signin-link');
    
    // Botones de agendar (usando IDs específicos + clase)
    const botonesAgendar = document.querySelectorAll('#btnAgendarHero, #btnAgendarFooter, .boton-secundario');
    console.log('Botones de agendar encontrados:', botonesAgendar.length, botonesAgendar);
    
    // Función para abrir el modal de login
    function abrirModal() {
        console.log('🔓 Ejecutando abrirModal()...');
        console.log('Modal antes de abrir:', modalLogin);
        console.log('Classes antes:', modalLogin.className);
        
        modalLogin.classList.add('active');
        document.body.style.overflow = 'hidden'; // Evita el scroll del body
        
        console.log('Classes después:', modalLogin.className);
        console.log('✅ Clase "active" agregada');
        
        // Verificar estilos computados
        const estilos = window.getComputedStyle(modalLogin);
        console.log('Opacity:', estilos.opacity);
        console.log('Visibility:', estilos.visibility);
        console.log('Display:', estilos.display);
    }
    
    // Función para cerrar el modal de login
    function cerrarModal() {
        modalLogin.classList.remove('active');
        document.body.style.overflow = '';
        // Resetear a la vista de Sign In después de cerrar
        setTimeout(() => {
            modalContainer.classList.remove('navigate');
        }, 400);
    }
    
    // Abrir modal al hacer clic en botones de "AGENDAR HORA"
    if (botonesAgendar.length === 0) {
        console.warn('⚠️ ADVERTENCIA: No se encontraron botones de agendar');
    }
    
    botonesAgendar.forEach((boton, index) => {
        console.log(`Configurando evento para botón ${index + 1}:`, boton);
        boton.addEventListener('click', function(e) {
            console.log('🎯 Click detectado en botón:', boton);
            e.preventDefault();
            abrirModal();
        });
    });
    
    console.log('✅ Modal configurado correctamente');
    
    // Cerrar modal al hacer clic en el botón X
    closeModalBtn.addEventListener('click', cerrarModal);
    
    // Cerrar modal al hacer clic fuera del contenedor
    modalLogin.addEventListener('click', function(e) {
        if (e.target === modalLogin) {
            cerrarModal();
        }
    });
    
    // Cerrar modal con la tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modalLogin.classList.contains('active')) {
            cerrarModal();
        }
    });
    
    // Cambiar entre Sign Up y Sign In
    signupLink.addEventListener('click', function(e) {
        e.preventDefault();
        modalContainer.classList.add('navigate');
    });
    
    signinLink.addEventListener('click', function(e) {
        e.preventDefault();
        modalContainer.classList.remove('navigate');
    });
    
    // Manejo de formularios con conexión al backend PHP
    const formSignup = document.getElementById('formSignup');
    const formSignin = document.getElementById('formSignin');
    
    // Función para mostrar mensajes de error/éxito
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
            border-radius: 5px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(mensajeDiv);
        
        // Remover después de 4 segundos
        setTimeout(() => {
            mensajeDiv.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => mensajeDiv.remove(), 300);
        }, 4000);
    }
    
    // Función para deshabilitar/habilitar botón de envío
    function toggleBotonEnvio(boton, deshabilitado, texto) {
        boton.disabled = deshabilitado;
        boton.textContent = texto;
        boton.style.opacity = deshabilitado ? '0.6' : '1';
        boton.style.cursor = deshabilitado ? 'not-allowed' : 'pointer';
    }
    
    // REGISTRO DE USUARIOS
    formSignup.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const boton = this.querySelector('.form-btn');
        const textoOriginal = boton.textContent;
        
        // Obtener datos del formulario
        const inputs = this.querySelectorAll('input');
        const datos = {
            nombre: inputs[0].value.trim(),
            email: inputs[1].value.trim(),
            telefono: inputs[2].value.trim(),
            direccion: inputs[3].value.trim(),
            password: inputs[4].value
        };
        
        // Validaciones básicas en frontend
        if (!datos.nombre || !datos.email || !datos.telefono || !datos.direccion || !datos.password) {
            mostrarMensaje('Por favor, completa todos los campos', 'error');
            return;
        }
        
        try {
            // Deshabilitar botón durante la petición
            toggleBotonEnvio(boton, true, 'Registrando...');
            
            // Enviar datos al backend
            const response = await fetch('./api/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(datos)
            });
            
            const resultado = await response.json();
            
            if (resultado.success) {
                mostrarMensaje(resultado.message, 'success');
                // Limpiar formulario
                this.reset();
                // Cerrar modal después de 2 segundos
                setTimeout(() => {
                    cerrarModal();
                    // Recargar la página o actualizar UI según usuario logueado
                    verificarSesion();
                }, 2000);
            } else {
                mostrarMensaje(resultado.message, 'error');
            }
            
        } catch (error) {
            console.error('Error:', error);
            mostrarMensaje('Error de conexión. Por favor, verifica tu servidor PHP.', 'error');
        } finally {
            toggleBotonEnvio(boton, false, textoOriginal);
        }
    });
    
    // INICIO DE SESIÓN
    formSignin.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const boton = this.querySelector('.form-btn');
        const textoOriginal = boton.textContent;
        
        // Obtener datos del formulario
        const inputs = this.querySelectorAll('input');
        const datos = {
            email: inputs[0].value.trim(),
            password: inputs[1].value
        };
        
        // Validaciones básicas en frontend
        if (!datos.email || !datos.password) {
            mostrarMensaje('Por favor, completa todos los campos', 'error');
            return;
        }
        
        try {
            // Deshabilitar botón durante la petición
            toggleBotonEnvio(boton, true, 'Iniciando sesión...');
            
            // Enviar datos al backend
            const response = await fetch('./api/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(datos)
            });
            
            const resultado = await response.json();
            
            if (resultado.success) {
                mostrarMensaje(resultado.message, 'success');
                // Limpiar formulario
                this.reset();
                // Cerrar modal después de 2 segundos
                setTimeout(() => {
                    cerrarModal();
                    // Recargar la página o actualizar UI según usuario logueado
                    verificarSesion();
                }, 2000);
            } else {
                mostrarMensaje(resultado.message, 'error');
            }
            
        } catch (error) {
            console.error('Error:', error);
            mostrarMensaje('Error de conexión. Por favor, verifica tu servidor PHP.', 'error');
        } finally {
            toggleBotonEnvio(boton, false, textoOriginal);
        }
    });
    
    // VERIFICAR SESIÓN AL CARGAR LA PÁGINA
    async function verificarSesion() {
        try {
            const response = await fetch('./api/verificar-sesion.php');
            const resultado = await response.json();
            
            if (resultado.success && resultado.data.logueado) {
                // Usuario está logueado
                actualizarUIUsuarioLogueado(resultado.data.usuario);
            } else {
                // Usuario no está logueado
                actualizarUIUsuarioNoLogueado();
            }
        } catch (error) {
            console.error('Error al verificar sesión:', error);
        }
    }
    
    // ACTUALIZAR UI CUANDO EL USUARIO ESTÁ LOGUEADO
    function actualizarUIUsuarioLogueado(usuario) {
        // Cambiar botón "AGENDAR HORA" por nombre de usuario
        const botonesAgendar = document.querySelectorAll('.boton-secundario');
        botonesAgendar.forEach(boton => {
            boton.textContent = `Hola, ${usuario.nombre.split(' ')[0]}`;
            boton.style.background = 'linear-gradient(135deg, var(--color-dorado) 0%, var(--color-marron) 100%)';
        });
        
        // Agregar opción de cerrar sesión
        console.log('Usuario logueado:', usuario);
    }
    
    // ACTUALIZAR UI CUANDO EL USUARIO NO ESTÁ LOGUEADO
    function actualizarUIUsuarioNoLogueado() {
        // Restaurar botones originales
        const botonesAgendar = document.querySelectorAll('.boton-secundario');
        botonesAgendar.forEach(boton => {
            boton.textContent = 'AGENDAR HORA';
        });
    }
    
    // Verificar sesión al cargar la página
    verificarSesion();
    
    /* ============================================
       EFECTO DE CARGA INICIAL
       ============================================ */
    
    // Fade in suave al cargar la página
    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 100);
});

/* ============================================
   GALERÍA INTERACTIVA - INTERCAMBIO DE IMÁGENES (USO FUTURO)
   ============================================ */

// Función para intercambiar imagen principal con imágenes pequeñas
function cambiarImagenPrincipal() {
    const animalesPequenos = document.querySelectorAll('.animal-pequeno');
    const imagenPrincipal = document.querySelector('.imagen-principal');
    
    if (animalesPequenos.length > 0 && imagenPrincipal) {
        animalesPequenos.forEach(animal => {
            animal.addEventListener('click', function() {
                const nuevaSrc = this.src;
                const srcOriginal = imagenPrincipal.src;
                
                imagenPrincipal.style.opacity = '0.5';
                
                setTimeout(() => {
                    imagenPrincipal.src = nuevaSrc;
                    this.src = srcOriginal;
                    imagenPrincipal.style.opacity = '1';
                }, 200);
            });
        });
    }
}

// Inicializo la funcionalidad de cambio de imágenes
cambiarImagenPrincipal();
