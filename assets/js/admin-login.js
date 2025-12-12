/**
 * LOGIN DE INTRANET - ADMIN PANEL
 * Clínica Veterinaria Alaska Pets Center
 * Funciona tanto en modal como en página independiente
 */

(function() {
    'use strict';

    // Esperar a que el DOM esté cargado
    document.addEventListener('DOMContentLoaded', function() {
        const formLogin = document.getElementById('formIntranetLogin');
        
        // Si no existe el formulario, salir (página sin modal de intranet)
        if (!formLogin) {
            return;
        }

        // Detectar si estamos en modal o página independiente
        const isModal = document.getElementById('modalIntranet') !== null;
        const alertMessage = isModal 
            ? document.getElementById('alertMessageIntranet')
            : document.getElementById('alertMessage');
        
        // Obtener campos de email y password según el contexto
        function getEmailField() {
            return document.getElementById('emailIntranet') || document.getElementById('email');
        }
        
        function getPasswordField() {
            return document.getElementById('passwordIntranet') || document.getElementById('password');
        }

        /**
         * Mostrar mensaje de alerta
         */
        function mostrarAlerta(mensaje, tipo = 'error') {
            if (!alertMessage) return;
            
            alertMessage.textContent = mensaje;
            alertMessage.className = 'alert-message ' + tipo;
            alertMessage.style.display = 'block';
            
            // Ocultar después de 5 segundos
            setTimeout(() => {
                alertMessage.style.display = 'none';
            }, 5000);
        }

        /**
         * Manejar el envío del formulario
         */
        formLogin.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Obtener datos del formulario
            const emailField = getEmailField();
            const passwordField = getPasswordField();
            
            if (!emailField || !passwordField) {
                console.error('No se encontraron los campos de email o password');
                return;
            }
            
            const email = emailField.value.trim();
            const password = passwordField.value;

            // Validaciones básicas
            if (!email || !password) {
                mostrarAlerta('Por favor, complete todos los campos');
                return;
            }

            // Deshabilitar botón de envío
            const submitBtn = formLogin.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Iniciando sesión...';

            try {
                // Determinar la ruta correcta según el contexto
                const loginUrl = isModal 
                    ? './admin/login-process.php'
                    : 'admin/login-process.php';
                
                const response = await fetch(loginUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password
                    })
                });

                const result = await response.json();

                if (result.success) {
                    mostrarAlerta(result.message, 'success');
                    // Abrir el panel en una nueva pestaña después de 1 segundo
                    setTimeout(() => {
                        const panelUrl = isModal 
                            ? './admin/panel-admin.php'
                            : 'admin/panel-admin.php';
                        window.open(panelUrl, '_blank');
                        
                        // Si estamos en modal, cerrarlo después de abrir la nueva pestaña
                        if (isModal) {
                            const modalIntranet = document.getElementById('modalIntranet');
                            if (modalIntranet) {
                                modalIntranet.classList.remove('active', 'show');
                                document.body.style.overflow = '';
                                // Limpiar formulario
                                formLogin.reset();
                                // Limpiar mensajes de alerta
                                if (alertMessage) {
                                    alertMessage.style.display = 'none';
                                }
                            }
                        }
                    }, 1000);
                } else {
                    mostrarAlerta(result.message, 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta('Error de conexión. Por favor, intente nuevamente.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    });
})();

