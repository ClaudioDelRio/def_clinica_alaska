# 🏥 Descripción del Sitio Web: Clínica Veterinaria Alaska Pets Center

## 📖 Resumen Ejecutivo

**Clínica Veterinaria Alaska Pets Center** es un sistema web integral de gestión para una clínica veterinaria ubicada en Osorno, Chile. Fundada el 11 de febrero de 2001, la clínica se especializa en cirugías de tejidos blandos, radiología y anestesiología, atendiendo principalmente perros y gatos.

El sistema proporciona dos interfaces principales:
1. **Portal público** para clientes que permite registro, agendamiento de citas y gestión de mascotas
2. **Panel de administración** para médicos veterinarios que gestionan citas, clientes y médicos

---

## 🎯 Propósito y Funcionalidad Principal

### Objetivo del Sistema
Automatizar y digitalizar los procesos de una clínica veterinaria, permitiendo que los clientes agenden citas en línea mientras los administradores gestionan eficientemente las operaciones diarias.

### Público Objetivo
- **Usuarios finales:** Dueños de perros y gatos en Osorno y alrededores
- **Administradores:** Médicos veterinarios y personal administrativo de la clínica

---

## 🏗️ Arquitectura del Sistema

### Stack Tecnológico
- **Frontend:** HTML5, CSS3 (con SCSS), JavaScript vanilla
- **Backend:** PHP 8.4+
- **Base de Datos:** MySQL/MariaDB
- **Servidor:** Apache con hosting en `cldelrio.laboratoriodiseno.cl`
- **Sistema de Sesiones:** PHP Sessions con seguridad mejorada

### Estructura de Directorios
```
clinicaakasjadef/
├── admin/          # Panel administrativo (médicos)
├── api/            # Endpoints REST para frontend
├── assets/         # Recursos estáticos (CSS, JS, imágenes)
├── database/       # Scripts SQL y esquemas
├── usuarios/       # Panel de usuario (clientes)
└── index.html      # Landing page pública
```

---

## 👥 Roles y Usuarios del Sistema

### 1. **Usuarios Clientes** (`ca_usuarios`)
Datos almacenados:
- Nombre completo
- Email (usado para login)
- RUT chileno (formato: 12.345.678-9) - validado con algoritmo módulo 11
- Teléfono
- Dirección
- Contraseña encriptada con bcrypt
- Fecha de registro
- Último acceso
- Estado activo/inactivo

### 2. **Médicos Veterinarios** (`ca_medicos`)
Datos almacenados:
- Nombre completo
- Especialidad
- Teléfono y email
- Credenciales de acceso (admin o no admin)
- Estado activo/inactivo
- Fecha de registro

---

## 🐾 Entidades Principales

### **Mascotas** (`ca_mascotas`)
Relación: Muchas mascotas pertenecen a un usuario (1:N)
- Nombre, especie (perro/gato/otro), raza
- Edad, peso, sexo, color
- Observaciones (alergias, condiciones especiales)
- Vacunas al día (sí/no)
- Estado activo
- Fecha de registro

### **Citas** (`ca_citas`)
Relación: N:M entre usuarios y médicos
- Usuario que reserva
- Mascota para la cual es la cita
- Médico asignado (opcional)
- Tipo de servicio: consulta, vacunación, cirugía, radiología, laboratorio, peluquería, emergencia
- Fecha y hora de la cita
- Motivo de consulta
- Estado: pendiente, confirmada, completada, cancelada
- Observaciones médicas

---

## 🛡️ Características de Seguridad

### Autenticación
- Hash de contraseñas con bcrypt
- Sesiones PHP seguras con configuración HTTPOnly
- Validación de credenciales en cada página admin
- Rate limiting básico para prevenir ataques de fuerza bruta

### Validación de Datos
- Sanitización de inputs contra XSS
- Validación de RUT chileno con algoritmo oficial
- Validación de emails y teléfonos chilenos
- Prepared statements para prevenir SQL injection
- CORS y headers de seguridad HTTP

### Campos Obligatorios
- RUT es **obligatorio** y único por usuario
- Validación de formato chileno (XX.XXX.XXX-Y)
- Verificación de duplicados

---

## 📅 Horarios y Disponibilidad

### Horario de Atención
**Lunes a Viernes:**
- Mañana: 10:00 - 13:00 hrs
- Tarde: 15:00 - 19:00 hrs

**Sábados:**
- Mañana: 11:00 - 13:00 hrs
- Tarde: 14:00 - 17:00 hrs

### Bloques de Tiempo
Las citas se agendan en intervalos de 30 minutos dentro de estos horarios.

---

## 🎨 Interfaz de Usuario

### Landing Page Pública (`index.html`)
Secciones principales:
1. **Hero section:** Call-to-action para agendar citas
2. **Acerca de Nosotros:** Historia, especialidades y estadísticas
3. **Servicios:** Cards con descripciones
4. **Ubicación:** Mapa de Google y datos de contacto
5. **Footer:** Redes sociales, horarios y créditos

### Funcionalidades Interactivas
- Modal de login/registro
- Formulario de registro con validación de RUT
- Navegación inferior fija para móviles
- Responsive design para todos los dispositivos

### Panel Administrativo
- Dashboard con estadísticas
- Gestión de citas (calendario y lista)
- Gestión de clientes (CRUD completo)
- Gestión de médicos (activar/desactivar)
- Navegación lateral con iconos

---

## 🔌 API REST Endpoints

### Autenticación
- `POST /api/register.php` - Registro de usuarios
- `POST /api/login.php` - Inicio de sesión
- `POST /api/logout.php` - Cerrar sesión
- `GET /api/verificar-sesion.php` - Verificar sesión activa

### Usuarios
- `GET /api/obtener-datos-usuario.php` - Obtener perfil de usuario

### Mascotas
- `GET /api/obtener-mascotas.php` - Listar mascotas del usuario
- `POST /api/agregar-mascota.php` - Agregar nueva mascota
- `PUT /api/actualizar-mascota.php` - Editar mascota
- `DELETE /api/eliminar-mascota.php` - Eliminar mascota

### Citas
- `GET /api/obtener-citas-pendientes-por-mascota.php` - Historial de citas
- `GET /api/obtener-horarios-disponibles.php` - Horarios libres
- `POST /api/reservar-hora.php` - Crear nueva cita

### Médicos
- `GET /api/obtener-doctores.php` - Listar médicos disponibles

### Otros
- `GET /api/obtener-historial.php` - Historial médico completo

---

## 📊 Funciones de Negocio Clave

### 1. Sistema de Reservas
- Validación de horarios disponibles
- Prevención de doble reserva
- Asignación opcional de médico preferido
- Estados de seguimiento de cita

### 2. Gestión de Mascotas
- Registro de múltiples mascotas por cliente
- Historial médico vinculado
- Seguimiento de vacunas
- Registro de observaciones médicas

### 3. Panel de Administración
- Vista de calendario para gestión de citas
- Búsqueda de clientes por nombre o RUT
- Eliminación con verificación de cascada
- Activación/desactivación de recursos

---

## 🇨🇱 Localización y Contexto Chileno

### Adaptaciones para Chile
- **RUT (Rol Único Tributario):** Validación completa con algoritmo módulo 11
- **Formato de teléfono:** +56 9 XXXXXXXX
- **Zona horaria:** America/Santiago
- **Ubicación:** Osorno, Región de Los Lagos

### Datos de Contacto
- **Dirección:** Alcalde Saturnino Barril 1380, Osorno
- **Teléfono:** (+64) 227 0539 / +56 9 9365 1250
- **Email:** osorno@clinicaalaska.cl
- **Redes sociales:** Facebook, WhatsApp

---

## 🎯 Casos de Uso Principales

### Para Clientes:
1. Registrarse en el sistema
2. Agregar información de sus mascotas
3. Buscar horarios disponibles
4. Reservar una cita para su mascota
5. Ver historial de citas previas

### Para Administradores:
1. Iniciar sesión en panel admin
2. Ver calendario de citas del día/semana
3. Confirmar o cancelar citas
4. Gestionar información de clientes
5. Agregar o modificar médicos en el sistema
6. Ver estadísticas de la clínica

---

## 🔧 Características Técnicas Destacadas

### Validación RUT Chileno
Implementación completa del algoritmo módulo 11:
- Limpieza de formato (acepta con/sin puntos y guiones)
- Cálculo del dígito verificador
- Validación de duplicados
- Formateo automático para almacenamiento

### Respuestas API Estándar
```json
{
  "success": true/false,
  "message": "Mensaje descriptivo",
  "data": { /* datos adicionales */ }
}
```

### Manejo de Sesiones
- Variables de sesión para usuarios: `usuario_id`, `usuario_nombre`, `usuario_email`, `usuario_rut`
- Variables de sesión para médicos: `medico_id`, `medico_nombre`, `medico_es_admin`
- Headers de seguridad configurados

---

## 📈 Estado del Proyecto

### Implementado ✅
- Sistema completo de autenticación
- CRUD de mascotas
- Sistema de reservas de citas
- Panel administrativo funcional
- Validación de RUT chileno
- Diseño responsive
- API REST documentada

### Características Adicionales
- Sistema de logging de errores
- Migraciones de base de datos
- Código comentado en español
- Implementación de buenas prácticas de seguridad

---

## 👨‍💻 Información del Desarrollo

**Desarrollado por:** Claudio del Rio - Web.malgarini®  
**Sitio web:** https://web.malgarini.cl  
**Versión del sistema:** 1.0  
**Fecha última actualización:** Octubre 2025  
**Licencia:** Propietario para Clínica Veterinaria Alaska Pets Center

---

## 🎯 Resumen para IAs

Este es un **sistema de gestión de clínica veterinaria** completo con:
- Backend PHP con MySQL
- Autenticación de dos tipos de usuarios (clientes y médicos)
- Sistema de reservas con validación de disponibilidad
- Gestión de mascotas, citas y médicos
- Validación específica chilena (RUT)
- Panel administrativo con calendario
- API REST para operaciones CRUD
- Diseño responsive y moderno
- Enfoque en seguridad y validación de datos

El sistema está diseñado para ser escalable, seguro y fácil de mantener, con código bien documentado y estructura modular.



