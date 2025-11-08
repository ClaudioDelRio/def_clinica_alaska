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
├── api/            # Endpoints REST para clientes
├── assets/         # Recursos estáticos (CSS, JS, imágenes)
├── config/         # Configuración y conexión a BD (compartido)
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
- **Duración en minutos** (30, 60, 90, 120, 150, 180, 210, 240) 🆕
- **ID de grupo de cita** (para vincular bloques de citas largas) 🆕
- Motivo de consulta
- Estado: pendiente, confirmada, completada, cancelada
- Observaciones médicas

**Nota sobre duración:** Las citas de más de 30 minutos se dividen en múltiples bloques de 30 minutos vinculados por el mismo `grupo_cita_id`, asegurando reservas atómicas.

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
- **Navegación lateral adaptativa:**
  - Desktop: Sidebar fijo visible
  - Mobile: Menú hamburguesa con overlay
  - Transiciones suaves y animaciones
  - Cierre automático al seleccionar opción
  - Accesible con tecla ESC

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

### Panel de Administración (Admin Endpoints)
#### Gestión de Citas
- `GET /admin/obtener-cita.php` - Obtener detalles de una cita específica
- `POST /admin/actualizar-cita.php` - Actualizar información de una cita
- `DELETE /admin/eliminar-cita.php` - Eliminar una cita
- `POST /admin/crear-cita.php` - Crear cita (con soporte para duración múltiple)
- `GET /admin/obtener-horarios-disponibles.php` - Horarios disponibles (versión admin)
- `GET /admin/obtener-citas-calendario.php` - Citas para vista de calendario

#### Gestión de Clientes
- `GET /admin/buscar-clientes.php` - Buscar clientes por nombre, RUT o mascota
- `POST /admin/crear-cliente-rapido.php` - Crear cliente desde flujo de reserva

#### Gestión de Mascotas
- `GET /admin/listar-mascotas-cliente.php` - Listar mascotas de un cliente
- `POST /admin/crear-mascota-rapido.php` - Crear mascota desde flujo de reserva

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

### 4. Gestión Avanzada de Citas (Calendario Diario) 🆕
El sistema incluye funcionalidades administrativas avanzadas en el calendario diario:

#### Edición de Citas
- Modal interactivo con todos los campos editables
- Selector de fecha con actualización automática de horarios disponibles
- Selector de horas disponibles dinámico
- Lista de médicos disponibles
- Validación en tiempo real

#### Eliminación de Citas
- Modal de confirmación personalizado con diseño consistente
- Eliminación segura con verificación de permisos
- Feedback visual inmediato

#### Reserva Rápida desde Calendario
**Flujo completo en 3 modales:**

1. **Modal de Confirmación de Hora:**
   - Activado por doble clic en horario libre
   - Muestra fecha y hora seleccionada
   - Opción de aceptar o cancelar

2. **Modal de Búsqueda de Cliente:**
   - Búsqueda por nombre, RUT o nombre de mascota
   - Resultados en tiempo real
   - Opción de seleccionar cliente existente
   - Botón para crear nuevo cliente si no existe

3. **Modal de Formulario de Cita:**
   - Selección de mascota del cliente
   - Selección de servicio
   - Selección de médico (opcional)
   - Campo de motivo/observaciones
   - **Selector de duración** (30 min a 4 horas)
   - Vista de bloques horarios ocupados
   - Validación antes de guardar

#### Creación Rápida de Cliente y Mascota
Desde el flujo de reserva, sin salir del calendario:
- **Modal de Nuevo Cliente:** Formulario compacto con campos esenciales
- **Modal de Nueva Mascota:** Se activa automáticamente después de crear cliente
- Transiciones fluidas entre modales
- Retorno automático al flujo de reserva

#### Sistema de Duración de Citas
- Soporte para citas de duración variable (30 min a 4 horas)
- Visualización de bloques horarios en intervalos de 30 minutos
- Validación de disponibilidad para todos los bloques
- Agrupación de citas largas con `grupo_cita_id`
- Prevención de solapamientos
- Ideal para cirugías, consultas especializadas, etc.

#### Visualización Mejorada
- Cada cita muestra: hora, cliente, mascota, servicio, **motivo** y estado
- Badge de estado alineado a la derecha
- Botones de acción (editar/eliminar) solo para administradores
- Diseño responsive y accesible

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

### Transacciones de Base de Datos 🆕
Para operaciones complejas como crear citas con múltiples bloques:
```php
$pdo->beginTransaction();
try {
    // Múltiples operaciones relacionadas
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    // Manejar error
}
```
Garantiza integridad de datos en operaciones atómicas.

### Arquitectura de JavaScript Modular
**`admin-calendario.js`** - Módulo principal del calendario:
- Renderizado dinámico de citas
- Sistema de modales para flujos complejos
- Gestión de estado de la aplicación
- Llamadas asíncronas a API con Fetch
- Event listeners optimizados

**`admin-panel.js`** - Navegación y utilidades:
- Control de sidebar responsive
- Funciones globales para menú hamburguesa
- Manejo de eventos de teclado (ESC)

### Compilación de Estilos
- **SCSS** para estilos personalizados (`assets/scss/custom.scss`)
- Compilación manual por desarrollador
- Separación clara entre estilos base y personalizados
- Variables y mixins para consistencia

### Esquema de Base de Datos Actualizado
Campos recientes en `ca_citas`:
```sql
duracion_minutos INT DEFAULT 30,
grupo_cita_id VARCHAR(50) NULL,
INDEX idx_grupo_cita (grupo_cita_id)
```

---

## 📈 Estado del Proyecto

### Implementado ✅
- Sistema completo de autenticación
- CRUD de mascotas
- Sistema de reservas de citas
- Panel administrativo funcional
- **Gestión avanzada de citas desde calendario diario (NUEVO)**
  - Editar citas con modal interactivo
  - Eliminar citas con confirmación personalizada
  - Reservar citas por doble clic en horario libre
  - Selector de duración para citas largas (hasta 4 horas)
  - Búsqueda rápida de clientes
  - Creación de clientes y mascotas desde el flujo de reserva
- Validación de RUT chileno
- Diseño responsive con menú hamburguesa en móviles
- API REST documentada

### Características Adicionales
- Sistema de logging de errores
- Migraciones de base de datos
- Código comentado en español
- Implementación de buenas prácticas de seguridad
- Transacciones de base de datos para operaciones complejas
- Sistema de grupos de citas para citas largas

---

## 👨‍💻 Información del Desarrollo

**Desarrollado por:** Claudio del Rio - Web.malgarini®  
**Sitio web:** https://web.malgarini.cl  
**Versión del sistema:** 1.5  
**Fecha última actualización:** Noviembre 2025  
**Licencia:** Propietario para Clínica Veterinaria Alaska Pets Center

---

## 🎯 Resumen para IAs

Este es un **sistema de gestión de clínica veterinaria** completo con:
- Backend PHP 8.4+ con MySQL usando PDO y prepared statements
- Autenticación de dos tipos de usuarios (clientes y médicos)
- Sistema de reservas con validación de disponibilidad
- **Gestión avanzada de citas desde calendario:**
  - Edición/eliminación con modales interactivos
  - Reserva rápida por doble clic en horario libre
  - Flujo modal de búsqueda y creación de clientes/mascotas
  - **Sistema de duración flexible** (30 min a 4 horas) con bloques vinculados
  - Transacciones de BD para operaciones atómicas
- Gestión de mascotas, citas y médicos
- Validación específica chilena (RUT con módulo 11)
- Panel administrativo con:
  - Calendario diario interactivo
  - **Navegación responsive con menú hamburguesa en móviles**
  - Dashboard de estadísticas
  - CRUD completo de todas las entidades
- API REST separada para frontend (carpeta `api/`) y admin (carpeta `admin/`)
- Diseño responsive y moderno con SCSS personalizado
- Enfoque en seguridad, validación de datos y UX fluida
- Arquitectura JavaScript modular con manejo de estado

### Estructura de Archivos Clave
```
├── admin/                          # Backend administrativo
│   ├── nav-panel.php              # Navegación con menú hamburguesa
│   ├── gestionar-citas-calendario.php
│   ├── crear-cita.php             # Soporte duración múltiple
│   ├── actualizar-cita.php
│   ├── eliminar-cita.php
│   ├── crear-cliente-rapido.php   # Creación desde flujo
│   └── crear-mascota-rapido.php
├── api/                           # Backend cliente (endpoints públicos)
│   ├── reservar-hora.php
│   ├── obtener-mascotas.php
│   └── ...
├── config/                        # Configuración compartida
│   ├── configuracion.php          # Config principal y funciones
│   └── db.php                     # Clase DB MySQLi (legacy)
├── assets/
│   ├── js/
│   │   ├── admin-calendario.js    # ~1000 líneas, lógica completa
│   │   └── admin-panel.js         # Sidebar responsive
│   └── scss/
│       └── custom.scss            # Estilos personalizados
└── database/
    └── agregar-duracion-citas.sql # Migración reciente
```

### Notas Técnicas Importantes
1. **Configuración centralizada:** Carpeta `config/` con archivos compartidos por todo el proyecto
2. **SCSS:** Se compila manualmente, no automáticamente
3. **Citas largas:** Se dividen en bloques de 30 min vinculados por `grupo_cita_id`
4. **Modales:** Sistema completo con validación, transiciones y cierre con ESC
5. **Permisos:** Solo administradores pueden modificar citas desde calendario
6. **Responsive:** Breakpoint mobile ~768px, sidebar se convierte en hamburguesa

El sistema está diseñado para ser escalable, seguro y fácil de mantener, con código bien documentado en español y estructura modular.



