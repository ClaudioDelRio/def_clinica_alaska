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
