# 🗺️ Mapa de Navegación - Clínica Veterinaria Alaska Pets Center

## 📋 Estructura General del Sitio

```
🏠 SITIO WEB ALASKA PETS CENTER
│
├── 📱 HEADER (Navegación Principal)
│   ├── Logo (Icono huella)
│   ├── Menú Hamburguesa (móvil)
│   └── Navegación Principal
│       ├── Inicio (#inicio)
│       ├── Acerca de Nosotros (#acerca)
│       ├── Servicios (#servicios)
│       ├── Donde estamos (#contacto)
│       └── Intranet (admin/panel-admin.php)
│
├── 🎯 MAIN CONTENT (Contenido Principal)
│   │
│   ├── 1️⃣ SECCIÓN HERO (#inicio)
│   │   ├── Logo principal
│   │   ├── Título: "Clínica Veterinaria"
│   │   ├── Subtítulo
│   │   └── Botones CTA:
│   │       ├── [CONTACTO] → mailto:osorno@clinicaalaska.cl
│   │       └── [AGENDAR HORA] → Abre modal login
│   │
│   ├── 2️⃣ SECCIÓN ACERCA (#acerca)
│   │   ├── Historia de la clínica
│   │   ├── Servicios especializados destacados:
│   │   │   ├── Imagenología
│   │   │   ├── Cirugía de Tejidos Blandos
│   │   │   └── Anestesiología
│   │   └── Estadísticas:
│   │       ├── 23 años de experiencia
│   │       ├── 14,000 mascotas atendidas
│   │       └── 100% compromiso
│   │
│   ├── 3️⃣ SECCIÓN SERVICIOS (#servicios)
│   │   ├── Consultas Veterinarias
│   │   ├── Cirugías
│   │   ├── Cirugías de Tejido Blando [DESTACADO]
│   │   ├── Radiología
│   │   ├── Vacunación
│   │   ├── Anestesiología
│   │   └── [CTA: Contáctanos] → mailto:osorno@clinicaalaska.cl
│   │
│   └── 4️⃣ SECCIÓN UBICACIÓN (#contacto)
│       ├── Mapa de Google (iframe)
│       ├── Información de contacto:
│       │   ├── 📍 Dirección física
│       │   │   └── Link: Google Maps externo
│       │   ├── 📞 Teléfonos (2)
│       │   ├── 🕐 Horarios de atención
│       │   └── 📧 Email
│       └── Botones de acción:
│           ├── [Llamar Ahora] → tel:+56642270539
│           └── [WhatsApp] → https://wa.me/56993651250
│
├── 🦶 FOOTER PRINCIPAL (footer-principal)
│   ├── Grid de 4 columnas:
│   │   │
│   │   ├── Columna 1: IDENTIDAD
│   │   │   ├── Logo
│   │   │   ├── Nombre de la clínica
│   │   │   └── Descripción institucional
│   │   │
│   │   ├── Columna 2: ENLACES RÁPIDOS
│   │   │   ├── → #inicio
│   │   │   ├── → #acerca
│   │   │   ├── → #servicios
│   │   │   ├── → #contacto
│   │   │   └── → admin/panel-admin.php
│   │   │
│   │   ├── Columna 3: ESPECIALIDADES
│   │   │   ├── Consultas Veterinarias
│   │   │   ├── Cirugía de Tejidos Blandos
│   │   │   ├── Radiología Digital
│   │   │   ├── Vacunación y Prevención
│   │   │   └── Anestesiología Avanzada
│   │   │
│   │   └── Columna 4: REDES SOCIALES
│   │       ├── Facebook → https://www.facebook.com/people/Alaska-Pets-Center-Ltda/...
│   │       ├── Instagram → #
│   │       └── WhatsApp → https://wa.me/56993651250
│   │
│   └── Copyright & Créditos
│       ├── © 2025 Clínica Veterinaria Alaska Pets Center
│       └── Desarrollado por Claudio del Rio - Web.malgarini®
│
├── 🔽 NAVEGACIÓN INFERIOR FIJA (pie-pagina)
│   ├── 🛍️ Tienda
│   ├── 👨‍⚕️ Doctores
│   ├── ❤️ Cuidados
│   ├── 📅 AGENDAR → Abre modal
│   ├── 📘 FACEBOOK → https://www.facebook.com/...
│   └── 📷 INSTAGRAM
│
└── 💬 MODAL LOGIN/REGISTRO (modalLogin)
    ├── Panel izquierdo: REGISTRO
    │   └── Formulario Sign Up:
    │       ├── Nombre completo
    │       ├── Email
    │       ├── RUT
    │       ├── Teléfono
    │       ├── Dirección
    │       ├── Contraseña
    │       └── [Botón: Registrarse]
    │
    └── Panel derecho: LOGIN
        └── Formulario Sign In:
            ├── Email
            ├── Contraseña
            ├── ¿Olvidaste contraseña?
            └── [Botón: Iniciar Sesión]
```

---

## 🔗 Enlaces Externos del Sitio

### Redes Sociales
- **Facebook**: https://www.facebook.com/people/Alaska-Pets-Center-Ltda/100042341516292/
- **Instagram**: # (pendiente)
- **WhatsApp**: https://wa.me/56993651250

### Mapas y Ubicación
- **Google Maps**: https://maps.app.goo.gl/a7tPCZieKsFi2Wpg9

### Contacto Directo
- **Email**: osorno@clinicaalaska.cl
- **Teléfono 1**: (+64) 227 0539
- **Teléfono 2**: (+56) 9 9365 1250

### Admin/Intranet
- **Panel Admin**: admin/panel-admin.php

---

## 📱 Flujos de Navegación del Usuario

### Flujo 1: Usuario nuevo que busca agendar
```
Inicio → [AGENDAR HORA] → Modal Login → Crear cuenta → [Registrarse]
```

### Flujo 2: Usuario que busca información
```
Inicio → Acerca de Nosotros → Servicios → Donde estamos → Ver ubicación en mapa
```

### Flujo 3: Usuario que quiere contactar
```
Cualquier sección → [CONTACTO] → Email directo
                  → [WhatsApp] → Chat directo
                  → [Llamar] → Llamada telefónica
```

### Flujo 4: Navegación por footer
```
Cualquier sección → Footer → Enlaces Rápidos → Sección deseada
                           → Redes Sociales → Plataforma externa
```

### Flujo 5: Navegación móvil
```
Cualquier sección → Navegación inferior fija → Sección/Red social
```

---

## 🎨 Elementos Interactivos

### Botones de Acción Principal (CTA)
1. **CONTACTO** (Hero) → mailto
2. **AGENDAR HORA** (Hero) → Modal
3. **Contáctanos** (Servicios) → mailto
4. **Llamar Ahora** (Ubicación) → tel
5. **WhatsApp** (Ubicación) → WhatsApp web
6. **AGENDAR** (Footer inferior) → Modal

### Menús y Navegación
- Menú hamburguesa (móvil)
- Navegación horizontal (desktop)
- Navegación inferior fija (siempre visible)
- Enlaces footer (scroll suave)

### Modales
- Modal Login/Registro (con toggle entre formularios)
- Botón cerrar modal

---

## 📊 Jerarquía de Información

### Nivel 1: Navegación Principal
- Acceso a todas las secciones principales
- Siempre visible (header fijo)

### Nivel 2: Contenido Principal
- 4 secciones principales (Hero, Acerca, Servicios, Ubicación)
- Información organizada jerárquicamente

### Nivel 3: Footer
- Información complementaria
- Enlaces de navegación rápida
- Redes sociales y contacto

### Nivel 4: Navegación Inferior
- Acceso rápido en móvil
- Funciones secundarias

---

## 🎯 Puntos de Conversión

1. **Email**: 3 puntos de acceso
2. **WhatsApp**: 3 puntos de acceso
3. **Teléfono**: 3 puntos de acceso
4. **Agendar hora**: 2 puntos de acceso
5. **Redes sociales**: 6 puntos de acceso (footer + nav inferior)

---

## 📱 Adaptación Responsive

### Desktop
- Navegación horizontal completa
- Grid de 4 columnas en footer
- Todas las secciones visibles

### Tablet
- Navegación adaptada
- Grid de 2-4 columnas flexible
- Contenido reorganizado

### Móvil
- Menú hamburguesa
- Navegación inferior fija visible
- Grid de 1-2 columnas en footer
- Contenido apilado verticalmente

---

## 🔐 Áreas Protegidas

- **Intranet** (admin/panel-admin.php)
  - Requiere autenticación
  - Acceso desde menú principal

---

## 🏷️ Etiquetas Semánticas HTML5

- `<header>` - Navegación principal
- `<main>` - Contenido principal
- `<section>` - Cada sección de contenido
- `<footer>` - 2 footers (principal e inferior)
- `<nav>` - Navegaciones
- `<article>` - Cards de servicios

---

**Fecha de creación**: Noviembre 2025  
**Versión**: 1.0  
**Desarrollador**: Claudio del Rio - Web.malgarini®

