document.addEventListener('DOMContentLoaded', () => {
    const urlNoahVet = 'https://panel.noahvetspa.cl/noahvet/index.php?slug=alaska';

    const menuHamburguesa = document.querySelector('.menu-hamburguesa');
    const menuNavegacion = document.querySelector('.menu-navegacion');
    const enlacesMenu = document.querySelectorAll('.menu-navegacion a');

    if (menuHamburguesa && menuNavegacion) {
        menuHamburguesa.addEventListener('click', function () {
            this.classList.toggle('activo');
            menuNavegacion.classList.toggle('activo');
            document.body.style.overflow = menuNavegacion.classList.contains('activo') ? 'hidden' : '';
        });

        enlacesMenu.forEach((enlace) => {
            enlace.addEventListener('click', () => {
                menuHamburguesa.classList.remove('activo');
                menuNavegacion.classList.remove('activo');
                document.body.style.overflow = '';
            });
        });

        document.addEventListener('click', (event) => {
            if (!menuNavegacion.contains(event.target) && !menuHamburguesa.contains(event.target)) {
                menuHamburguesa.classList.remove('activo');
                menuNavegacion.classList.remove('activo');
                document.body.style.overflow = '';
            }
        });
    }

    const header = document.querySelector('.encabezado');
    if (header) {
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            header.classList.toggle('scroll-activo', scrollTop > 100);
        });
    }

    const enlacesInternos = document.querySelectorAll('a[href^="#"]');
    enlacesInternos.forEach((enlace) => {
        enlace.addEventListener('click', (event) => {
            const destinoId = enlace.getAttribute('href');
            if (!destinoId || destinoId === '#') {
                return;
            }

            const destino = document.querySelector(destinoId);
            if (!destino) {
                return;
            }

            event.preventDefault();
            destino.scrollIntoView({ behavior: 'smooth' });
        });
    });

    const estadisticas = document.querySelector('.estadisticas');
    if (estadisticas && 'IntersectionObserver' in window) {
        const animarNumeros = () => {
            const numeros = document.querySelectorAll('.stat-numero');
            numeros.forEach((numero) => {
                const target = parseInt(numero.getAttribute('data-target'), 10);
                if (!target || Number.isNaN(target)) {
                    return;
                }

                let actual = 1;
                const incremento = target / 100;

                const timer = setInterval(() => {
                    actual += incremento;
                    if (actual >= target) {
                        clearInterval(timer);
                        if (target === 14000) {
                            numero.textContent = '14,000+';
                        } else if (target === 100) {
                            numero.textContent = '100%';
                        } else {
                            numero.textContent = `${target}+`;
                        }
                        return;
                    }

                    if (target === 14000) {
                        numero.textContent = Math.floor(actual).toLocaleString('es-CL');
                    } else {
                        numero.textContent = Math.floor(actual).toString();
                    }
                }, 20);
            });
        };

        const observer = new IntersectionObserver((entradas, observador) => {
            entradas.forEach((entrada) => {
                if (entrada.isIntersecting) {
                    animarNumeros();
                    observador.unobserve(entrada.target);
                }
            });
        }, { threshold: 0.5 });

        observer.observe(estadisticas);
    }

    const iconosNav = document.querySelectorAll('.icono-nav');
    iconosNav.forEach((icono) => {
        icono.addEventListener('click', function () {
            iconosNav.forEach((item) => item.classList.remove('activo'));
            this.classList.add('activo');
        });
    });

    const botonesAgendar = document.querySelectorAll('#btnAgendarHero, #btnAgendarFooter, #btnAgendarDoctores');
    botonesAgendar.forEach((boton) => {
        boton.addEventListener('click', (event) => {
            event.preventDefault();
            window.open(urlNoahVet, '_blank', 'noopener,noreferrer');
        });
    });

    document.body.style.opacity = '1';
});
