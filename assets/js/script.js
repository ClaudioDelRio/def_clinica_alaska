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
