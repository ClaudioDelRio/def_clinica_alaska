<?php
/**
 * LOGIN DE INTRANET
 * Clínica Veterinaria Alaska Pets Center
 */

require_once __DIR__ . '/../api/configuracion.php';

// Si ya está logueado, redirigir al panel
if (isset($_SESSION['medico_id'])) {
    header('Location: panel-admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intranet - Clínica Alaska</title>
    <base href="../">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Login específico para admin */
        .login-intranet-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }

        .login-intranet-container {
            position: relative;
            width: 100%;
            max-width: 450px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            animation: modalSlideIn 0.5s ease;
        }

        .login-intranet-bg {
            position: absolute;
            width: 100%;
            height: 200%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 150px;
            left: -40%;
            top: -90%;
            transform-origin: right bottom;
            transform: rotateZ(-40deg);
            z-index: 1;
        }

        .login-intranet-content {
            position: relative;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 50px 40px;
            z-index: 2;
        }

        .form-header-intranet {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header-intranet i {
            font-size: 3.5rem;
            color: #667eea;
            margin-bottom: 15px;
            animation: pulse 2s ease-in-out infinite;
        }

        .form-header-intranet h2 {
            font-size: 2.5rem;
            color: #667eea;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .form-header-intranet p {
            font-size: 1rem;
            color: var(--color-gris-claro);
            font-weight: 300;
        }

        .form-auth-intranet {
            width: 100%;
            max-width: 350px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .input-group-intranet {
            position: relative;
            width: 100%;
        }

        .input-group-intranet input {
            width: 100%;
            height: 50px;
            border: none;
            border-bottom: 2px solid rgba(102, 126, 234, 0.3);
            background: transparent;
            color: var(--color-negro);
            font-size: 1rem;
            padding: 0 40px 0 10px;
            transition: border-color 0.3s ease;
        }

        .input-group-intranet input::placeholder {
            color: var(--color-gris-claro);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 400;
        }

        .input-group-intranet input:focus {
            outline: none;
            border-bottom-color: #667eea;
        }

        .input-group-intranet i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(102, 126, 234, 0.5);
            font-size: 1.1rem;
            pointer-events: none;
        }

        .input-group-intranet input:focus + i {
            color: #667eea;
        }

        .form-btn-intranet {
            width: 100%;
            height: 55px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 30px;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            margin-top: 20px;
        }

        .form-btn-intranet:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .form-btn-intranet:active {
            transform: translateY(0);
        }

        .alert-message {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: none;
        }

        .alert-message.error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
            display: block;
        }

        .alert-message.success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
            display: block;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px) scale(0.9);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        @media (max-width: 500px) {
            .login-intranet-content {
                padding: 40px 25px;
            }

            .form-header-intranet h2 {
                font-size: 2rem;
            }

            .form-header-intranet i {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-intranet-wrapper">
        <div class="login-intranet-container">
            <div class="login-intranet-bg"></div>
            
            <div class="login-intranet-content">
                <div class="form-header-intranet">
                    <i class="fas fa-shield-alt"></i>
                    <h2>Intranet</h2>
                    <p>Panel de Administración</p>
                </div>

                <div class="alert-message" id="alertMessage"></div>

                <form class="form-auth-intranet" id="formIntranetLogin">
                    <div class="input-group-intranet">
                        <input type="email" name="email" id="email" placeholder="Correo electrónico" required />
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="input-group-intranet">
                        <input type="password" name="password" id="password" placeholder="Contraseña" required />
                        <i class="fas fa-lock"></i>
                    </div>
                    <button class="form-btn-intranet" type="submit">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/admin-login.js"></script>
</body>
</html>

