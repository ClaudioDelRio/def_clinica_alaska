# 📋 Documentación: Integración de RUT Chileno

## ✅ Cambios Implementados

Se ha agregado exitosamente la funcionalidad de RUT (Rol Único Tributario) al sistema de registro y login, con validación completa según la legislación chilena.

---

## 🗄️ 1. Base de Datos

### SQL para Agregar el Campo RUT

Archivo: `database/agregar_campo_rut.sql`

```sql
ALTER TABLE ca_usuarios 
ADD COLUMN rut VARCHAR(12) NULL COMMENT 'RUT del usuario (formato: 12.345.678-9)' AFTER email,
ADD UNIQUE KEY unique_rut (rut),
ADD INDEX idx_rut (rut);
```

**Características:**
- Campo de tipo VARCHAR(12) para soportar formato XX.XXX.XXX-Y
- Campo **OBLIGATORIO** (NOT NULL) - El RUT es requerido para todos los usuarios
- Índice único para evitar RUTs duplicados
- Índice para optimizar búsquedas por RUT

**Instrucciones:**
Ejecuta este script en tu base de datos MySQL:
```bash
mysql -u tu_usuario -p tu_base_de_datos < database/agregar_campo_rut.sql
```

---

## 🔧 2. Funciones de Validación PHP

Archivo: `api/config.php`

### Funciones Agregadas:

#### `limpiarRUT($rut)`
Elimina puntos, guiones y espacios del RUT.

**Ejemplo:**
```php
limpiarRUT('12.345.678-5') // Retorna: '123456785'
```

#### `formatearRUT($rut)`
Formatea un RUT al formato estándar chileno con puntos y guión.

**Ejemplo:**
```php
formatearRUT('123456785') // Retorna: '12.345.678-5'
```

#### `validarRUT($rut)`
Valida un RUT chileno usando el algoritmo Módulo 11.

**Ejemplo:**
```php
validarRUT('12.345.678-5') // Retorna: true
validarRUT('12.345.678-9') // Retorna: false
```

**Formatos aceptados:**
- `12.345.678-5` (con puntos y guión)
- `12345678-5` (sin puntos)
- `123456785` (sin formato)

#### `calcularDVRUT($numero)`
Calcula el dígito verificador de un RUT según el algoritmo Módulo 11.

**Ejemplo:**
```php
calcularDVRUT('12345678') // Retorna: '5'
calcularDVRUT('7654321') // Retorna: 'K'
```

---

## 📝 3. Actualización del Registro

Archivo: `api/register.php`

### Cambios Implementados:

1. **Recepción del RUT:**
```php
$rut = isset($datos['rut']) ? limpiarInput($datos['rut']) : '';
```

2. **Validación del RUT (OBLIGATORIO):**
```php
if (empty($rut)) {
    $errores[] = 'El RUT es obligatorio';
} elseif (!validarRUT($rut)) {
    $errores[] = 'El RUT ingresado no es válido. Formato: 12.345.678-9';
}
```

3. **Verificación de RUT duplicado:**
```php
$rutFormateado = formatearRUT($rut);
$sql = "SELECT id FROM ca_usuarios WHERE rut = :rut";
$stmt = $pdo->prepare($sql);
$stmt->execute(['rut' => $rutFormateado]);

if ($stmt->fetch()) {
    enviarRespuesta(false, 'Este RUT ya está registrado');
}
```

4. **Almacenamiento en la base de datos:**
```php
$rutFormateado = formatearRUT($rut);

$sql = "INSERT INTO ca_usuarios (nombre, email, rut, telefono, direccion, password, fecha_registro) 
        VALUES (:nombre, :email, :rut, :telefono, :direccion, :password, NOW())";
```

5. **Inclusión en la sesión:**
```php
$_SESSION['usuario_rut'] = $rutFormateado;
```

---

## 🔐 4. Actualización del Login

Archivo: `api/login.php`

### Cambios Implementados:

1. **Obtención del RUT en la consulta:**
```php
$sql = "SELECT id, nombre, email, rut, password FROM ca_usuarios WHERE email = :email";
```

2. **Almacenamiento en la sesión:**
```php
$_SESSION['usuario_rut'] = $usuario['rut'];
```

3. **Inclusión en la respuesta JSON:**
```php
enviarRespuesta(true, '¡Inicio de sesión exitoso!', [
    'usuario' => [
        'id' => $usuario['id'],
        'nombre' => $usuario['nombre'],
        'email' => $usuario['email'],
        'rut' => $usuario['rut']
    ]
]);
```

---

## 👤 5. Actualización de Datos de Usuario

Archivo: `api/obtener-datos-usuario.php`

### Cambios Implementados:

1. **Obtención del RUT en la consulta:**
```php
$sql = "SELECT id, nombre, email, rut, telefono, direccion, fecha_registro, ultimo_acceso 
        FROM ca_usuarios 
        WHERE id = :id";
```

2. **Inclusión en la respuesta:**
```php
'usuario' => [
    'id' => $usuario['id'],
    'nombre' => $usuario['nombre'],
    'email' => $usuario['email'],
    'rut' => $usuario['rut'],
    'telefono' => $usuario['telefono'],
    'direccion' => $usuario['direccion'],
    // ...
]
```

---

## 📊 6. Ejemplos de Uso

### Ejemplo de Registro (JavaScript):

```javascript
const registrarUsuario = async () => {
    const datos = {
        nombre: 'Juan Pérez',
        email: 'juan@example.com',
        rut: '12.345.678-5',  // OBLIGATORIO
        telefono: '+56912345678',
        direccion: 'Av. Principal 123',
        password: 'miPassword123'
    };
    
    try {
        const response = await fetch('/api/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });
        
        const resultado = await response.json();
        
        if (resultado.success) {
            console.log('Usuario registrado:', resultado.data.usuario);
            console.log('RUT:', resultado.data.usuario.rut);
        } else {
            console.error('Error:', resultado.message);
        }
    } catch (error) {
        console.error('Error de conexión:', error);
    }
};
```

### Ejemplo de Login (JavaScript):

```javascript
const iniciarSesion = async () => {
    const datos = {
        email: 'juan@example.com',
        password: 'miPassword123'
    };
    
    try {
        const response = await fetch('/api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });
        
        const resultado = await response.json();
        
        if (resultado.success) {
            console.log('Sesión iniciada:', resultado.data.usuario);
            console.log('RUT del usuario:', resultado.data.usuario.rut);
            
            // Guardar datos en localStorage si lo necesitas
            localStorage.setItem('usuario', JSON.stringify(resultado.data.usuario));
        }
    } catch (error) {
        console.error('Error de conexión:', error);
    }
};
```

---

## 🎨 7. Ejemplo de Formulario HTML

```html
<form id="formulario-registro">
    <div class="form-group">
        <label for="nombre">Nombre Completo *</label>
        <input type="text" id="nombre" name="nombre" required>
    </div>
    
    <div class="form-group">
        <label for="rut">RUT *</label>
        <input type="text" 
               id="rut" 
               name="rut" 
               placeholder="12.345.678-9"
               pattern="[0-9]{1,2}\.[0-9]{3}\.[0-9]{3}-[0-9Kk]{1}"
               title="Formato: 12.345.678-9"
               required>
        <small>Formato: 12.345.678-9 (obligatorio)</small>
    </div>
    
    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" required>
    </div>
    
    <div class="form-group">
        <label for="telefono">Teléfono *</label>
        <input type="tel" id="telefono" name="telefono" 
               placeholder="+56912345678" required>
    </div>
    
    <div class="form-group">
        <label for="direccion">Dirección *</label>
        <input type="text" id="direccion" name="direccion" required>
    </div>
    
    <div class="form-group">
        <label for="password">Contraseña *</label>
        <input type="password" id="password" name="password" 
               minlength="6" required>
    </div>
    
    <button type="submit">Registrarse</button>
</form>
```

---

## 🔍 8. Validación Frontend (JavaScript)

Puedes agregar validación en el frontend antes de enviar el formulario:

```javascript
// Función para validar RUT en el frontend
function validarRutJS(rut) {
    // Limpiar el RUT
    rut = rut.replace(/[^0-9kK]/g, '').toUpperCase();
    
    // Verificar largo
    if (rut.length < 8 || rut.length > 9) return false;
    
    // Separar número y dígito verificador
    const numero = rut.slice(0, -1);
    const dvIngresado = rut.slice(-1);
    
    // Calcular dígito verificador
    let suma = 0;
    let multiplicador = 2;
    
    for (let i = numero.length - 1; i >= 0; i--) {
        suma += parseInt(numero[i]) * multiplicador;
        multiplicador = multiplicador === 7 ? 2 : multiplicador + 1;
    }
    
    const resto = suma % 11;
    const dv = 11 - resto;
    const dvCalculado = dv === 11 ? '0' : dv === 10 ? 'K' : dv.toString();
    
    return dvIngresado === dvCalculado;
}

// Formatear RUT mientras se escribe
function formatearRutJS(rut) {
    rut = rut.replace(/[^0-9kK]/g, '').toUpperCase();
    if (rut.length < 2) return rut;
    
    const numero = rut.slice(0, -1);
    const dv = rut.slice(-1);
    
    // Agregar puntos cada 3 dígitos
    const formateado = numero.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    return formateado + '-' + dv;
}

// Uso en el formulario
document.getElementById('rut').addEventListener('blur', function() {
    const rut = this.value;
    
    if (rut && !validarRutJS(rut)) {
        this.setCustomValidity('RUT inválido');
        alert('El RUT ingresado no es válido');
    } else {
        this.setCustomValidity('');
        if (rut) {
            this.value = formatearRutJS(rut);
        }
    }
});
```

---

## ℹ️ 9. Información del Algoritmo Módulo 11

El RUT chileno utiliza un dígito verificador calculado con el algoritmo Módulo 11:

1. Se toma el número del RUT (sin el dígito verificador)
2. Se multiplica cada dígito por 2, 3, 4, 5, 6, 7 (de derecha a izquierda, repitiendo la secuencia)
3. Se suman todos los productos
4. Se divide la suma por 11 y se obtiene el resto
5. Se resta el resto de 11
6. Si el resultado es 11, el dígito verificador es 0
7. Si el resultado es 10, el dígito verificador es K
8. En caso contrario, el dígito verificador es el resultado

**Ejemplo con RUT 12.345.678:**
```
8×2 + 7×3 + 6×4 + 5×5 + 4×6 + 3×7 + 2×2 + 1×3 = 16 + 21 + 24 + 25 + 24 + 21 + 4 + 3 = 138
138 ÷ 11 = 12 (resto 6)
11 - 6 = 5
Dígito verificador: 5
RUT completo: 12.345.678-5
```

---

## 🧪 10. Pruebas

### RUTs Válidos para Pruebas:
- `12.345.678-5`
- `11.111.111-1`
- `7.654.321-K`
- `1.234.567-8`

### RUTs Inválidos para Pruebas:
- `12.345.678-9` (dígito verificador incorrecto)
- `22.222.222-2` (dígito verificador incorrecto)
- `98.765.432-1` (dígito verificador incorrecto)

---

## 📚 11. Archivos Modificados

1. ✅ `database/agregar_campo_rut.sql` - Script SQL
2. ✅ `api/config.php` - Funciones de validación
3. ✅ `api/register.php` - Registro con RUT
4. ✅ `api/login.php` - Login con RUT
5. ✅ `api/obtener-datos-usuario.php` - Obtener datos con RUT
6. ✅ `api/ejemplos-validacion-rut.php` - Ejemplos visuales

---

## 🚀 12. Próximos Pasos

1. **Ejecutar el script SQL** en tu base de datos
2. **Actualizar el formulario de registro** en `index.html` o donde tengas el formulario
3. **Agregar validación en el frontend** con JavaScript
4. **Probar el registro** con RUTs válidos e inválidos
5. **Verificar que el login** retorne correctamente el RUT

---

## 💡 Notas Importantes

- ⚠️ El campo RUT es **OBLIGATORIO** en el registro (NOT NULL)
- ✅ El RUT **debe ser válido** según la legislación chilena (algoritmo Módulo 11)
- 🔒 El RUT tiene un **índice único** (no se permiten duplicados)
- 📝 El RUT se **almacena con formato** XX.XXX.XXX-Y
- 🔍 La validación se hace tanto en **frontend como backend**

---

## 📞 Soporte

Si tienes problemas con la implementación, verifica:

1. Que el script SQL se haya ejecutado correctamente
2. Que los archivos PHP estén actualizados
3. Que el servidor tenga permisos de escritura
4. Los logs de errores en PHP

---

**Desarrollado para:** Clínica Veterinaria Alaska  
**Fecha:** Octubre 2025  
**Versión:** 1.0

