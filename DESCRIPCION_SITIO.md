# 🏥 Descripción del Sitio Web: Clínica Veterinaria Alaska Pets Center

## 📖 Resumen
Este proyecto corresponde a la **versión pública del sitio web** de la clínica, orientada a mostrar información institucional y contacto.

Actualmente el alcance incluye solo:
- `index.html` (sitio principal)
- `doctores.html` (equipo médico)
- `assets/css/estilos.css`
- `assets/js/script.js`
- recursos de imagen en `assets/img/`

---

## 🎯 Objetivo del Sitio
- Presentar servicios veterinarios y especialidades
- Mostrar ubicación, horarios y canales de contacto
- Redirigir el agendamiento a la plataforma externa **NoahVet**

URL de agendamiento utilizada:
- `https://panel.noahvetspa.cl/noahvet/index.php?slug=alaska`

---

## 🧩 Estructura Actual

```text
/
├── index.html
├── doctores.html
├── assets/
│   ├── css/
│   │   └── estilos.css
│   ├── img/
│   └── js/
│       └── script.js
└── documentos .md de referencia
```

---

## 🖥️ Comportamiento Frontend

### Navegación
- Menú principal con anclas internas en `index.html`
- Navegación responsive con menú hamburguesa
- Enlace a `doctores.html`

### Agendamiento
Los botones de “Agendar” en ambas páginas abren NoahVet en una pestaña nueva.

### Interacciones activas en JavaScript
- Menú móvil
- Estado visual del header con scroll
- Scroll suave para anclas
- Animación de estadísticas en portada
- Estado activo de navegación inferior

---

## ⚠️ Alcance y Exclusiones
Esta versión **no incluye** backend propio ni panel interno.

Se retiró del proyecto:
- Módulo de administración/intranet
- Endpoints y lógica de API
- Configuración de base de datos
- Panel de usuario y flujos de login/registro

---

## 📅 Estado
- Versión documental actualizada: 26 de febrero de 2026
- Proyecto enfocado en presencia web pública + agendamiento externo
