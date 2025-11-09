/* ============================================
   JAVASCRIPT - FUNCIONALIDAD VETERINARIA
   ============================================ */

/* ============================================
   FUNCIÓN DE VALIDACIÓN DE RUT CHILENO
   ============================================ */

// Función para validar RUT chileno según algoritmo Módulo 11
function validarRutJS(rut) {
    // Limpiar el RUT (eliminar puntos, guiones y espacios)
    rut = rut.replace(/[^0-9kK]/g, '').toUpperCase();
    
    // Verificar largo (mínimo 8 caracteres: 1234567 + dígito verificador)
    if (rut.length < 8 || rut.length > 9) {
        return false;
    }
    
    // Separar número y dígito verificador
    const numero = rut.slice(0, -1);
    const dvIngresado = rut.slice(-1);
    
    // Calcular dígito verificador usando módulo 11
    let suma = 0;
    let multiplicador = 2;
    
    for (let i = numero.length - 1; i >= 0; i--) {
        suma += parseInt(numero[i]) * multiplicador;
        multiplicador = multiplicador === 7 ? 2 : multiplicador + 1;
    }
    
    const resto = suma % 11;
    const dv = 11 - resto;
    const dvCalculado = dv === 11 ? '0' : dv === 10 ? 'K' : dv.toString();
    
    // Comparar dígito verificador ingresado con el calculado
    return dvIngresado === dvCalculado;
}

// Función para formatear RUT con puntos y guión
function formatearRutJS(rut) {
    // Limpiar el RUT
    rut = rut.replace(/[^0-9kK]/g, '').toUpperCase();
    
    if (rut.length < 2) {
        return rut;
    }
    
    // Separar número y dígito verificador
    const numero = rut.slice(0, -1);
    const dv = rut.slice(-1);
    
    // Agregar puntos cada 3 dígitos de derecha a izquierda
    const formateado = numero.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    
    return formateado + '-' + dv;
}

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
    
    // Elementos del modal de login
    const modalLogin = document.getElementById('modalLogin');
    
    if (!modalLogin) {
        console.error('❌ ERROR: No se encuentra el modal de login');
        return;
    }
    
    const modalContainer = modalLogin.querySelector('.modal-container');
    const closeModalBtn = document.getElementById('closeModal');
    const signupLink = modalLogin.querySelector('.signup-link');
    const signinLink = modalLogin.querySelector('.signin-link');
    
    // Botones de agendar (usando IDs específicos + clase)
    const botonesAgendar = document.querySelectorAll('#btnAgendarHero, #btnAgendarFooter, .boton-secundario');
    
    // Función para abrir el modal de login
    function abrirModal() {
        modalLogin.classList.add('active', 'show');
        document.body.style.overflow = 'hidden'; // Evita el scroll del body
        
        // En móviles, asegurar que muestra el formulario de login centrado
        if (window.innerWidth <= 600) {
            setTimeout(() => {
                const loginForm = modalLogin.querySelector('.login-wrapper-right');
                if (loginForm) {
                    loginForm.scrollIntoView({ 
                        behavior: 'auto', // Sin animación al abrir
                        block: 'center',
                        inline: 'nearest'
                    });
                }
            }, 50);
        }
    }
    
    // Función para cerrar el modal de login
    function cerrarModal() {
        modalLogin.classList.remove('active', 'show');
        document.body.style.overflow = '';
        // Resetear a la vista de Sign In después de cerrar
        setTimeout(() => {
            modalContainer.classList.remove('navigate');
        }, 400);
    }
    
    // Abrir modal al hacer clic en botones de "AGENDAR HORA"
    botonesAgendar.forEach(boton => {
        boton.addEventListener('click', function(e) {
            e.preventDefault();
            abrirModal();
        });
    });
    
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
    
    // Cambiar entre Sign Up y Sign In con scroll automático en móviles
    signupLink.addEventListener('click', function(e) {
        e.preventDefault();
        modalContainer.classList.add('navigate');
        
        // Scroll automático al formulario de registro en móviles
        setTimeout(() => {
            const registroForm = modalLogin.querySelector('.login-wrapper-left');
            if (registroForm && window.innerWidth <= 600) {
                registroForm.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center',
                    inline: 'nearest'
                });
            }
        }, 100); // Pequeño delay para que la transición comience
    });
    
    signinLink.addEventListener('click', function(e) {
        e.preventDefault();
        modalContainer.classList.remove('navigate');
        
        // Scroll automático al formulario de inicio de sesión en móviles
        setTimeout(() => {
            const loginForm = modalLogin.querySelector('.login-wrapper-right');
            if (loginForm && window.innerWidth <= 600) {
                loginForm.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center',
                    inline: 'nearest'
                });
            }
        }, 100);
    });
    
    // Mejorar experiencia cuando aparece el teclado en móviles
    if (window.innerWidth <= 600) {
        const todosLosInputs = modalLogin.querySelectorAll('input');
        todosLosInputs.forEach(input => {
            input.addEventListener('focus', function() {
                // Pequeño delay para que el teclado aparezca primero
                setTimeout(() => {
                    this.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center'
                    });
                }, 300);
            });
        });
    }
    
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
            rut: inputs[2].value.trim(),
            telefono: inputs[3].value.trim(),
            direccion: inputs[4].value.trim(),
            password: inputs[5].value
        };
        
        // Validaciones básicas en frontend
        if (!datos.nombre || !datos.email || !datos.rut || !datos.telefono || !datos.direccion || !datos.password) {
            mostrarMensaje('Por favor, completa todos los campos', 'error');
            return;
        }
        
        // Validar formato del RUT
        if (!validarRutJS(datos.rut)) {
            mostrarMensaje('El RUT ingresado no es válido', 'error');
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
                // Cerrar modal y redirigir al panel de usuario
                setTimeout(() => {
                    cerrarModal();
                    window.location.href = 'usuarios/panel-usuario.php';
                }, 1000);
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
                // Cerrar modal y redirigir al panel de usuario
                setTimeout(() => {
                    cerrarModal();
                    window.location.href = 'usuarios/panel-usuario.php';
                }, 1000);
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
       FORMATEO AUTOMÁTICO DEL RUT AL ESCRIBIR
       ============================================ */
    
    // Obtener el campo de RUT del formulario de registro
    const inputRut = formSignup.querySelector('input[type="text"]:nth-of-type(2)'); // Tercer input (nombre, email, RUT)
    
    if (inputRut) {
        // Formatear RUT mientras se escribe
        inputRut.addEventListener('input', function(e) {
            let valor = e.target.value;
            // Eliminar todo excepto números y K
            valor = valor.replace(/[^0-9kK]/g, '').toUpperCase();
            
            // Limitar a 9 caracteres (sin formato)
            if (valor.length > 9) {
                valor = valor.slice(0, 9);
            }
            
            // Formatear si tiene al menos 2 caracteres
            if (valor.length >= 2) {
                e.target.value = formatearRutJS(valor);
            } else {
                e.target.value = valor;
            }
        });
        
        // Validar al perder el foco
        inputRut.addEventListener('blur', function(e) {
            const valor = e.target.value.trim();
            
            if (valor && !validarRutJS(valor)) {
                e.target.style.borderColor = '#f44336';
                mostrarMensaje('El RUT ingresado no es válido', 'error');
            } else {
                e.target.style.borderColor = '';
            }
        });
    }
    
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
