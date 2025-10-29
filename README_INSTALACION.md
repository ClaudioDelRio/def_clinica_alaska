# 🐾 Sistema de Autenticación - Clínica Veterinaria Alaska

Sistema completo de registro y login implementado para la Clínica Veterinaria Alaska Pets Center.

## 📋 Requisitos

- **XAMPP, WAMP o servidor local con:**
  - PHP 7.4 o superior
  - MySQL 5.7 o superior
  - Apache con mod_rewrite habilitado

## 🚀 Instalación

### 1. Configurar el Servidor Local

1. Instala **XAMPP** desde [https://www.apachefriends.org](https://www.apachefriends.org)
2. Inicia los servicios de **Apache** y **MySQL**

### 2. Crear la Base de Datos

**Opción A: Con phpMyAdmin (Recomendado)**
1. Abre **phpMyAdmin** en tu navegador: `http://localhost/phpmyadmin`
2. Haz clic en la pestaña **"Importar"** (no necesitas crear la BD primero)
3. Selecciona el archivo `database/clinica_veterinaria_base.sql`
4. Haz clic en **"Continuar"** y espera que termine

**Opción B: Por línea de comandos**
```bash
mysql -u root -p < database/clinica_veterinaria_base.sql
```

Esto creará:
- ✅ Base de datos `clinica_veterinaria`
- ✅ Tabla `ca_usuarios` (con campo dirección) - VACÍA
- ✅ Tabla `ca_mascotas` (relación 1:N con ca_usuarios) - VACÍA
- 🎯 Lista para que agregues tus propios datos

### 3. Configurar la Conexión a la Base de Datos

Abre el archivo `api/config.php` y ajusta las credenciales si es necesario:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'clinica_veterinaria');
define('DB_USER', 'root');      // Tu usuario de MySQL
define('DB_PASS', '');          // Tu contraseña de MySQL
```

### 4. Configurar el Proyecto en el Servidor

Copia todo el proyecto a la carpeta de tu servidor web:

- **XAMPP**: `C:\xampp\htdocs\clinicaakasjadef\`
- **WAMP**: `C:\wamp64\www\clinicaakasjadef\`

### 5. Acceder al Sitio

Abre tu navegador y ve a:
```
http://localhost/clinicaakasjadef/
```

## 📁 Estructura del Proyecto

```
clinicaakasjadef/
├── api/                          # Backend PHP
│   ├── config.php               # Configuración y funciones
│   ├── db.php                   # Clase DB (MySQLi wrapper)
│   ├── register.php             # Endpoint de registro
│   ├── login.php                # Endpoint de login
│   ├── logout.php               # Endpoint de logout
│   ├── verificar-sesion.php     # Verificación de sesión
│   └── ejemplos_uso_db.php      # Ejemplos de uso de la clase DB
│
├── database/                     # Base de datos
│   └── crear_base_datos.sql     # Script SQL
│
├── assets/
│   ├── css/
│   │   └── estilos.css         # Estilos (incluye modal login)
│   ├── js/
│   │   └── script.js           # JavaScript (incluye lógica login)
│   └── img/
│
├── index.html                   # Página principal
├── .htaccess                    # Configuración Apache
├── .gitignore                   # Archivos ignorados por Git
└── README_INSTALACION.md        # Este archivo
```

## 🎯 Funcionalidades Implementadas

### ✅ Sistema de Registro
- Validación de campos (nombre, email, teléfono, contraseña)
- Encriptación de contraseñas con bcrypt
- Verificación de emails duplicados
- Inicio de sesión automático después del registro

### ✅ Sistema de Login
- Autenticación segura con contraseñas encriptadas
- Sesiones PHP
- Verificación de credenciales

### ✅ Gestión de Sesiones
- Verificación automática al cargar la página
- UI dinámica según estado de sesión
- Cierre de sesión seguro

### ✅ Características de Seguridad
- Contraseñas encriptadas con `password_hash()`
- Prepared statements para prevenir SQL Injection
- Validación en frontend y backend
- Sesiones con configuración segura
- Sanitización de inputs

## 🎯 Primeros Pasos

Las tablas están **vacías**. Para empezar:

1. Abre tu proyecto: `http://localhost/clinicaakasjadef/`
2. Haz clic en **"AGENDAR HORA"**
3. Selecciona **"¿No tienes cuenta? Regístrate"**
4. Crea tu primer usuario

## 📝 Uso del Sistema

### Registro de Nuevos Usuarios

1. Haz clic en el botón **"AGENDAR HORA"**
2. En el modal, haz clic en **"¿No tienes cuenta? Regístrate"**
3. Completa el formulario:
   - Nombre completo
   - Correo electrónico
   - Teléfono (formato chileno: +56912345678)
   - Contraseña (mínimo 6 caracteres)
4. Haz clic en **"Registrarse"**

### Inicio de Sesión

1. Haz clic en el botón **"AGENDAR HORA"**
2. Ingresa tu email y contraseña
3. Haz clic en **"Iniciar Sesión"**

Cuando inicies sesión correctamente, el botón "AGENDAR HORA" cambiará a "Hola, [TuNombre]".

## 🔧 Personalización

### Cambiar Mensajes de Validación

Edita `api/register.php` o `api/login.php` para modificar los mensajes de error.

### Cambiar Estilos del Modal

Edita `assets/css/estilos.css` en la sección "MODAL DE LOGIN / REGISTRO".

### Modificar Validaciones

- **Frontend**: `assets/js/script.js`
- **Backend**: `api/register.php` y `api/login.php`

## 💾 Uso de la Clase DB

El proyecto incluye una clase `DB` (MySQLi wrapper) muy completa en `api/db.php`. Esta clase ofrece:

### Características:
- ✅ Consultas preparadas (prepared statements)
- ✅ Métodos útiles: `fetchAll()`, `fetchOne()`, `fetchArray()`
- ✅ Soporte para transacciones
- ✅ Manejo automático de tipos de datos
- ✅ Contador de consultas
- ✅ Métodos de utilidad: `numRows()`, `affectedRows()`, `getInsertId()`

### Ejemplo de Uso:

```php
<?php
require_once 'api/config.php';

// Obtener todos los usuarios
$usuarios = $db->query("SELECT * FROM usuarios")->fetchAll();

// Buscar por parámetro
$email = 'usuario@ejemplo.cl';
$usuario = $db->query("SELECT * FROM usuarios WHERE email = ?", $email)->fetchOne();

// Insertar
$db->query(
    "INSERT INTO usuarios (nombre, email, telefono, password) VALUES (?, ?, ?, ?)",
    $nombre, $email, $telefono, $passwordHash
);
$nuevoId = $db->getInsertId();

// Transacciones
$db->beginTransaction();
try {
    $db->query("INSERT INTO ...", $params);
    $db->query("UPDATE ...", $params);
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
}
?>
```

### Ver Más Ejemplos:

Abre en tu navegador: `http://localhost/clinicaakasjadef/api/ejemplos_uso_db.php`

### Conexiones Disponibles:

El proyecto ofrece **dos formas** de conectar a la base de datos:

1. **MySQLi** (con clase DB) - `$db` - **RECOMENDADO**
   - Más funcionalidades
   - Mejor para este proyecto
   - Métodos útiles incluidos

2. **PDO** - `$pdo` - Disponible por compatibilidad
   - Los endpoints actuales usan PDO
   - Puedes migrarlos a MySQLi cuando quieras

## 🐛 Solución de Problemas

### Error: "No se recibieron datos"
- Verifica que el servidor Apache esté corriendo
- Asegúrate de que PHP está correctamente instalado

### Error de conexión a la base de datos
- Verifica las credenciales en `api/config.php`
- Asegúrate de que MySQL está corriendo
- Confirma que la base de datos existe

### El modal no se abre
- Abre la consola del navegador (F12)
- Verifica si hay errores de JavaScript
- Asegúrate de que `script.js` se está cargando correctamente

### Fetch no funciona
- Verifica que estés accediendo vía `http://localhost/` y no como archivo local
- Los archivos PHP deben ejecutarse en un servidor web

## 📚 Próximos Pasos

El sistema está listo para:

1. **Crear sistema de citas**: Los usuarios pueden agendar citas
2. **Gestión de mascotas**: Añadir/editar información de mascotas
3. **Panel de administración**: Para gestionar usuarios y citas
4. **Recuperación de contraseña**: Sistema de "olvidé mi contraseña"
5. **Verificación de email**: Enviar correo de confirmación
6. **E-commerce**: Para la tienda de productos para mascotas

## 🔐 Notas de Seguridad para Producción

Antes de publicar en un servidor real:

1. Cambiar `display_errors` a `0` en `config.php`
2. Usar HTTPS (cambiar `session.cookie_secure` a `1`)
3. Configurar contraseñas fuertes para MySQL
4. Implementar rate limiting para evitar ataques de fuerza bruta
5. Agregar CAPTCHA en los formularios
6. Implementar CSP (Content Security Policy)

## 📞 Soporte

Si tienes problemas con la instalación o configuración, revisa:
1. Los logs de error de Apache: `xampp/apache/logs/error.log`
2. Los logs de PHP: `xampp/php/logs/php_error_log`
3. La consola del navegador (F12)

---

**Desarrollado por Claudio del Rio - Web.malgarini®**

