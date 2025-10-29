<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Endpoints API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        button {
            background: #D4A574;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            font-size: 14px;
        }
        button:hover {
            background: #C89960;
        }
        .response {
            background: #f9f9f9;
            padding: 15px;
            border-left: 4px solid #D4A574;
            margin-top: 10px;
            white-space: pre-wrap;
            font-family: monospace;
            font-size: 12px;
        }
        .success {
            border-left-color: #4CAF50;
            background: #e8f5e9;
        }
        .error {
            border-left-color: #f44336;
            background: #ffebee;
        }
        h2 {
            color: #D4A574;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group {
            margin: 10px 0;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
    </style>
</head>
<body>
    <h1>🧪 Test de Endpoints API - Clínica Alaska</h1>
    
    <!-- TEST 1: REGISTRO -->
    <div class="test-section">
        <h2>📝 Test 1: Registro de Usuario</h2>
        <p>Registra un nuevo usuario de prueba</p>
        
        <div class="form-group">
            <label>Nombre completo:</label>
            <input type="text" id="regNombre" value="Juan Pérez Test">
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" id="regEmail" value="test@example.com">
        </div>
        <div class="form-group">
            <label>Teléfono:</label>
            <input type="tel" id="regTelefono" value="912345678">
        </div>
        <div class="form-group">
            <label>Dirección:</label>
            <input type="text" id="regDireccion" value="Calle Falsa 123, Santiago">
        </div>
        <div class="form-group">
            <label>Contraseña:</label>
            <input type="password" id="regPassword" value="test123">
        </div>
        
        <button onclick="testRegistro()">🚀 Probar Registro</button>
        <div id="responseRegistro"></div>
    </div>

    <!-- TEST 2: LOGIN -->
    <div class="test-section">
        <h2>🔐 Test 2: Login de Usuario</h2>
        <p>Inicia sesión con el usuario de prueba</p>
        
        <div class="form-group">
            <label>Email:</label>
            <input type="email" id="loginEmail" value="test@example.com">
        </div>
        <div class="form-group">
            <label>Contraseña:</label>
            <input type="password" id="loginPassword" value="test123">
        </div>
        
        <button onclick="testLogin()">🚀 Probar Login</button>
        <div id="responseLogin"></div>
    </div>

    <!-- TEST 3: VERIFICAR SESIÓN -->
    <div class="test-section">
        <h2>✅ Test 3: Verificar Sesión</h2>
        <p>Verifica si hay una sesión activa</p>
        
        <button onclick="testVerificarSesion()">🚀 Verificar Sesión</button>
        <div id="responseSesion"></div>
    </div>

    <!-- TEST 4: LOGOUT -->
    <div class="test-section">
        <h2>🚪 Test 4: Cerrar Sesión</h2>
        <p>Cierra la sesión actual</p>
        
        <button onclick="testLogout()">🚀 Cerrar Sesión</button>
        <div id="responseLogout"></div>
    </div>

    <script>
        // Test de Registro
        async function testRegistro() {
            const responseDiv = document.getElementById('responseRegistro');
            responseDiv.innerHTML = '⏳ Enviando solicitud...';
            
            const datos = {
                nombre: document.getElementById('regNombre').value,
                email: document.getElementById('regEmail').value,
                telefono: document.getElementById('regTelefono').value,
                direccion: document.getElementById('regDireccion').value,
                password: document.getElementById('regPassword').value
            };
            
            try {
                const response = await fetch('./register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(datos)
                });
                
                const resultado = await response.json();
                
                responseDiv.className = 'response ' + (resultado.success ? 'success' : 'error');
                responseDiv.innerHTML = JSON.stringify(resultado, null, 2);
            } catch (error) {
                responseDiv.className = 'response error';
                responseDiv.innerHTML = '❌ Error de conexión: ' + error.message;
            }
        }

        // Test de Login
        async function testLogin() {
            const responseDiv = document.getElementById('responseLogin');
            responseDiv.innerHTML = '⏳ Enviando solicitud...';
            
            const datos = {
                email: document.getElementById('loginEmail').value,
                password: document.getElementById('loginPassword').value
            };
            
            try {
                const response = await fetch('./login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(datos)
                });
                
                const resultado = await response.json();
                
                responseDiv.className = 'response ' + (resultado.success ? 'success' : 'error');
                responseDiv.innerHTML = JSON.stringify(resultado, null, 2);
            } catch (error) {
                responseDiv.className = 'response error';
                responseDiv.innerHTML = '❌ Error de conexión: ' + error.message;
            }
        }

        // Test de Verificar Sesión
        async function testVerificarSesion() {
            const responseDiv = document.getElementById('responseSesion');
            responseDiv.innerHTML = '⏳ Verificando...';
            
            try {
                const response = await fetch('./verificar-sesion.php');
                const resultado = await response.json();
                
                responseDiv.className = 'response ' + (resultado.success ? 'success' : 'error');
                responseDiv.innerHTML = JSON.stringify(resultado, null, 2);
            } catch (error) {
                responseDiv.className = 'response error';
                responseDiv.innerHTML = '❌ Error de conexión: ' + error.message;
            }
        }

        // Test de Logout
        async function testLogout() {
            const responseDiv = document.getElementById('responseLogout');
            responseDiv.innerHTML = '⏳ Cerrando sesión...';
            
            try {
                const response = await fetch('./logout.php', {
                    method: 'POST'
                });
                const resultado = await response.json();
                
                responseDiv.className = 'response ' + (resultado.success ? 'success' : 'error');
                responseDiv.innerHTML = JSON.stringify(resultado, null, 2);
            } catch (error) {
                responseDiv.className = 'response error';
                responseDiv.innerHTML = '❌ Error de conexión: ' + error.message;
            }
        }
    </script>
</body>
</html>

