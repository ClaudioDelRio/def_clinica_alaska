/**
 * LOGIN DE INTRANET - ADMIN PANEL
 * Clínica Veterinaria Alaska Pets Center
 */

(function() {
    'use strict';

    const formLogin = document.getElementById('formIntranetLogin');
    const alertMessage = document.getElementById('alertMessage');

    /**
     * Mostrar mensaje de alerta
     */
    function mostrarAlerta(mensaje, tipo = 'error') {
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
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;

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
            const response = await fetch('admin/login-process.php', {
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
                // Redirigir al panel después de 1 segundo
                setTimeout(() => {
                    window.location.href = 'admin/panel-admin.php';
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
})();

