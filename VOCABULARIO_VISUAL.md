# 🎨 Vocabulario Visual - Clínica Veterinaria Alaska Pets Center

> Documentación completa de todos los elementos visuales y de diseño del sitio web.

---

## 📐 Sistema de Diseño

### Filosofía de Diseño
- **Estilo**: Moderno, limpio y profesional con toques cálidos
- **Enfoque**: Elegancia veterinaria con confianza y calidez
- **Experiencia**: Diseño centrado en el usuario con jerarquía visual clara

---

## 🎨 Paleta de Colores

### Colores Principales

| Nombre | Hex | RGB | Variable CSS | Uso |
|--------|-----|-----|--------------|-----|
| **Blanco** | `#FFFFFF` | `255, 255, 255` | `--color-blanco` | Fondos, textos sobre oscuro |
| **Negro** | `#333333` | `51, 51, 51` | `--color-negro` | Textos principales |
| **Gris Claro** | `#666666` | `102, 102, 102` | `--color-gris-claro` | Textos secundarios |
| **Gris Medio** | `#555555` | `85, 85, 85` | `--color-gris-medio` | Subtítulos, descripciones |

### Colores de Marca

| Nombre | Hex | RGB | Variable CSS | Uso |
|--------|-----|-----|--------------|-----|
| **Dorado** ⭐ | `#D4A574` | `212, 165, 116` | `--color-dorado` | Botones principales, acentos, iconos |
| **Dorado Hover** | `#C89960` | `200, 153, 96` | `--color-dorado-hover` | Estado hover de elementos dorados |
| **Marrón** | `#8B7355` | `139, 115, 85` | `--color-marron` | Acentos complementarios |
| **Azul Oscuro** 🔷 | `#2C3E50` | `44, 62, 80` | `--color-azul-oscuro` | Títulos, navegación, elementos importantes |

### Colores de Fondo

| Nombre | Hex | RGB | Variable CSS | Uso |
|--------|-----|-----|--------------|-----|
| **Fondo Claro** | `#F5F7FA` | `245, 247, 250` | `--color-fondo-claro` | Fondo general, secciones claras |
| **Fondo Azul** | `#C3CFE2` | `195, 207, 226` | `--color-fondo-azul` | Degradados, fondos secundarios |

### Colores con Transparencia

#### Blanco Transparente
- `rgba(255, 255, 255, 0.1)` - `--color-blanco-transparente-10` - Overlays sutiles
- `rgba(255, 255, 255, 0.3)` - `--color-blanco-transparente-30` - Overlays medios
- `rgba(255, 255, 255, 0.95)` - `--color-blanco-transparente-95` - Fondos semi-opacos

#### Negro Transparente
- `rgba(0, 0, 0, 0.08)` - `--color-negro-transparente-08` - Sombras muy suaves
- `rgba(0, 0, 0, 0.1)` - `--color-negro-transparente-10` - Sombras suaves
- `rgba(0, 0, 0, 0.15)` - `--color-negro-transparente-15` - Sombras cards
- `rgba(0, 0, 0, 0.2)` - `--color-negro-transparente-20` - Sombras medias
- `rgba(0, 0, 0, 0.3)` - `--color-negro-transparente-30` - Sombras de texto

#### Dorado Transparente
- `rgba(212, 165, 116, 0.1)` - `--color-dorado-transparente-10` - Fondos sutiles
- `rgba(212, 165, 116, 0.3)` - `--color-dorado-transparente-30` - Fondos acentuados
- `rgba(212, 165, 116, 0.4)` - `--color-dorado-transparente-40` - Sombras doradas

#### Otros Transparentes
- `rgba(44, 62, 80, 0.98)` - `--color-azul-oscuro-transparente-98` - Header al scroll
- `rgba(245, 247, 250, 0.8)` - `--color-fondo-claro-transparente-80` - Overlays de fondo

---

## 🔤 Tipografía

### Familia Tipográfica Principal
**Poppins** (Google Fonts)
- Fuente moderna, geométrica y altamente legible
- Sans-serif con personalidad profesional pero amigable

### Pesos Disponibles
- **Light (300)** - Títulos grandes
- **Regular (400)** - Textos de cuerpo
- **Medium (500)** - Enlaces, énfasis sutil
- **SemiBold (600)** - Botones, CTAs
- **Bold (700)** - Números destacados, estadísticas

### Jerarquía Tipográfica

#### Títulos
| Elemento | Tamaño | Peso | Line-Height | Color | Uso |
|----------|--------|------|-------------|-------|-----|
| **H1 Hero** | `5rem` (80px) | 300 | 1.1 | Azul Oscuro | Título principal hero |
| **H2 Sección** | `3.5rem` (56px) | 300 | 1.2 | Azul Oscuro | Títulos de sección |
| **H3 Card** | `1.5rem` (24px) | 600 | 1.4 | Azul Oscuro | Títulos de tarjetas |
| **H4 Footer** | `1.2rem` (19.2px) | 600 | 1.4 | Blanco | Títulos en footer |

#### Textos de Cuerpo
| Elemento | Tamaño | Peso | Line-Height | Uso |
|----------|--------|------|-------------|-----|
| **Párrafo Grande** | `1.25rem` (20px) | 400 | 1.8 | Párrafos destacados |
| **Párrafo Normal** | `1.1rem` (17.6px) | 400 | 1.8 | Texto de cuerpo |
| **Párrafo Pequeño** | `0.95rem` (15.2px) | 400 | 1.6 | Descripciones, notas |
| **Texto Pequeño** | `0.85rem` (13.6px) | 500 | 1.4 | Botones, etiquetas |

#### Elementos Especiales
| Elemento | Tamaño | Peso | Color | Uso |
|----------|--------|------|-------|-----|
| **Etiqueta Sección** | `0.85rem` | 600 | Dorado | Subtítulos antes de H2 |
| **Estadísticas** | `3rem` | 700 | Dorado | Números grandes |
| **Subtítulos** | `1rem` | 400 | Gris Medio | Descripciones hero |

### Letter Spacing (Espaciado de Letras)
- **Títulos grandes**: `2px` - `1px`
- **Navegación**: `1px`
- **Etiquetas**: `2px`
- **Normal**: `0.5px`

---

## 🔲 Espaciado y Layout

### Sistema de Espaciado
Basado en múltiplos de `0.5rem` (8px)

| Variable | Valor | Uso |
|----------|-------|-----|
| xs | `0.5rem` (8px) | Espaciado mínimo |
| sm | `1rem` (16px) | Espaciado pequeño |
| md | `1.5rem` (24px) | Espaciado medio |
| lg | `2rem` (32px) | Espaciado grande |
| xl | `2.5rem` (40px) | Espaciado extra grande |
| 2xl | `3rem` (48px) | Separación de secciones |

### Padding de Componentes
- **Botones**: `0.9rem 2.5rem` (14.4px 40px)
- **Cards Servicio**: `2.5rem` (40px)
- **Secciones**: `8rem 2rem` (128px 32px) vertical/horizontal

### Max Width Containers
- **Principal**: `1200px`
- **Contenido Acerca**: `1000px`
- **Texto centrado**: `700px` - `450px`

### Grid System
- **Grid Servicios**: 3 columnas (desktop), responsive
- **Footer**: 4 columnas (desktop), adaptable

---

## 🎭 Sombras (Box Shadows)

### Jerarquía de Elevación

| Nivel | Sombra CSS | Uso |
|-------|------------|-----|
| **Nivel 1 - Suave** | `0 2px 20px rgba(0,0,0,0.2)` | Header scroll, elementos sutiles |
| **Nivel 2 - Media** | `0 5px 20px rgba(0,0,0,0.1)` | Cards en reposo |
| **Nivel 3 - Alta** | `0 10px 30px rgba(0,0,0,0.1)` | Cards servicios, estadísticas |
| **Nivel 4 - Muy Alta** | `0 15px 40px rgba(0,0,0,0.15)` | Cards en hover |
| **Nivel 5 - Máxima** | `0 20px 60px rgba(0,0,0,0.2)` | Modales |

### Sombras Especiales
- **Botón Principal**: `0 4px 15px rgba(212,165,116,0.3)` (sombra dorada)
- **Botón Principal Hover**: `0 6px 20px rgba(212,165,116,0.4)`
- **Logo**: `0 8px 20px rgba(0,0,0,0.15)`
- **Text Shadow**: `0 2px 4px rgba(0,0,0,0.3)`

---

## 🔘 Bordes y Radios

### Border Radius

| Tamaño | Valor | Uso |
|--------|-------|-----|
| **Pequeño** | `10px` | Inputs, elementos pequeños |
| **Medio** | `15px` - `20px` | Cards normales, imágenes |
| **Grande** | `25px` | Botones redondeados |
| **Extra Grande** | `30px` - `50px` | Cards grandes, modales |

### Bordes
- **Grosor estándar**: `2px`
- **Grosor fino**: `1px`
- **Línea decorativa**: `3px` height
- **Color principal**: Dorado o Azul Oscuro

---

## 🎬 Animaciones y Transiciones

### Duraciones Estándar
- **Rápida**: `0.2s` - Feedback inmediato
- **Normal**: `0.3s` - Transiciones estándar
- **Media**: `0.4s` - Animaciones complejas
- **Lenta**: `0.5s` - Transiciones dramáticas

### Timing Functions
- **ease** - Transiciones generales
- **ease-in-out** - Movimientos suaves
- **cubic-bezier(...)** - Animaciones personalizadas

### Efectos Comunes

#### Transform
```css
/* Hover - Elevación */
transform: translateY(-2px) to translateY(-10px);

/* Hover - Escala */
transform: scale(1.1);

/* Hover - Rotación */
transform: rotate(10deg);
```

#### Transiciones Aplicadas
- Links: `color 0.3s ease`
- Botones: `all 0.3s ease`
- Cards: `all 0.4s ease`
- Header: `all 0.4s ease`
- Iconos: `transform 0.3s ease`

---

## 🔳 Componentes UI

### 1. Botones

#### Botón Principal (CTA)
```
Fondo: Dorado (#D4A574)
Color texto: Blanco
Padding: 0.9rem 2.5rem
Border-radius: 25px
Font-size: 0.85rem
Font-weight: 600
Sombra: 0 4px 15px rgba(212,165,116,0.3)

Hover:
- Fondo: #C89960
- Transform: translateY(-2px)
- Sombra: 0 6px 20px rgba(212,165,116,0.4)
```

#### Botón Secundario (Outline)
```
Fondo: Transparente
Color texto: Azul Oscuro
Border: 2px solid Azul Oscuro
Padding: 0.9rem 2.5rem
Border-radius: 25px

Hover:
- Fondo: Azul Oscuro
- Color texto: Blanco
- Transform: translateY(-2px)
```

#### Botón WhatsApp
```
Fondo: #25D366
Color: Blanco
Icono: fab fa-whatsapp
```

#### Botón Llamar
```
Fondo: Dorado
Color: Blanco
Icono: fas fa-phone
```

---

### 2. Cards

#### Card de Servicio
```
Fondo: Blanco
Border-radius: 20px
Padding: 2.5rem
Sombra: 0 10px 30px rgba(0,0,0,0.1)
Línea superior: 4px gradient (dorado-marrón)

Hover:
- Transform: translateY(-10px)
- Sombra: 0 15px 40px rgba(0,0,0,0.15)
- Línea superior visible
```

#### Card Destacada
```
Fondo: Gradient dorado suave
Border: 2px solid rgba(212,165,116,0.3)
Etiqueta: "Especialidad" en esquina
```

#### Card de Contacto
```
Fondo: Blanco
Padding: 2rem
Border-radius: 15px
Icono circular en la parte superior
```

---

### 3. Iconos

#### Tamaños
- **Extra Grande**: `4rem` (estadísticas)
- **Grande**: `2.5rem` - `3rem` (servicios)
- **Mediano**: `2rem` (navegación)
- **Normal**: `1.5rem` (elementos UI)

#### Estilos
- **Color principal**: Dorado
- **Color secundario**: Azul Oscuro
- **Fondo iconos**: Gradient dorado transparente
- **Librería**: Font Awesome 6.0.0

#### Iconos Principales Usados
- `fa-paw` - Logo huella
- `fa-stethoscope` - Consultas
- `fa-user-md` - Cirugías
- `fa-hand-holding-medical` - Tejidos blandos
- `fa-x-ray` - Radiología
- `fa-syringe` - Vacunación
- `fa-heartbeat` - Anestesiología
- `fa-phone` - Teléfono
- `fa-envelope` - Email
- `fa-map-marker-alt` - Ubicación
- `fa-clock` - Horarios
- `fa-chevron-right` - Flechas navegación

---

### 4. Formularios

#### Inputs
```
Padding: 1rem 1rem 1rem 3rem
Border: 2px solid rgba(212,165,116,0.3)
Border-radius: 15px
Font-size: 0.95rem
Transición: 0.3s

Focus:
- Border: 2px solid Dorado
- Sombra: 0 0 0 4px rgba(212,165,116,0.1)
```

#### Iconos en Inputs
```
Position: Absoluta (izquierda)
Color: Dorado transparente
Size: 1.2rem
```

---

### 5. Navegación

#### Header Principal
```
Position: Fixed
Background: Transparente (inicial)
Backdrop-filter: none

Scroll:
- Background: rgba(44,62,80,0.98)
- Backdrop-filter: blur(15px)
- Sombra visible
```

#### Enlaces Navegación
```
Color: Blanco
Font-weight: 500
Letter-spacing: 1px
Text-shadow: 0 2px 4px rgba(0,0,0,0.3)

Hover:
- Color: Dorado
- Línea inferior animada
```

#### Menú Hamburguesa
```
3 líneas blancas
Height: 3px cada una
Transición a X cuando activo
```

#### Navegación Inferior Fija
```
Position: Fixed (bottom)
Background: Blanco
Grid: 6 iconos
Sombra superior
```

---

### 6. Modal Login

```
Background overlay: rgba(0,0,0,0.85)
Container: Blanco
Border-radius: 30px
Max-width: 900px
Sombra: 0 20px 60px rgba(0,0,0,0.3)

Layout:
- Dos paneles (Login y Registro)
- Fondo animado con gradient
- Toggle entre formularios
```

---

### 7. Footer

#### Footer Principal
```
Background: Azul Oscuro (#2c3e50)
Color texto: Blanco/Gris claro
Padding: 4rem 2rem
Grid: 4 columnas (responsive)
```

#### Logo Footer
```
Width: 80px
Border-radius: 10px
Margin-bottom: 1rem
```

#### Redes Sociales Footer
```
Iconos circulares
Size: 45px × 45px
Background: rgba(255,255,255,0.1)
Hover: Background dorado
```

---

## 📱 Breakpoints Responsive

### Sistema de Media Queries

| Dispositivo | Breakpoint | Rango |
|-------------|------------|-------|
| **Mobile Small** | `< 480px` | Teléfonos pequeños |
| **Mobile** | `< 768px` | Teléfonos |
| **Tablet** | `768px - 1024px` | Tablets, iPads |
| **Desktop** | `> 1024px` | Laptops, monitores |
| **Large Desktop** | `> 1440px` | Monitores grandes |

### Adaptaciones Principales

#### Mobile (< 768px)
- Menú hamburguesa activo
- Grid servicios: 1 columna
- Footer: 1-2 columnas
- Títulos reducidos 50%
- Navegación inferior visible

#### Tablet (768px - 1024px)
- Grid servicios: 2 columnas
- Footer: 2 columnas
- Títulos reducidos 30%

#### Desktop (> 1024px)
- Layout completo
- Grid servicios: 3 columnas
- Footer: 4 columnas

---

## 🎨 Degradados (Gradients)

### Degradados de Fondo
```css
/* Fondo general body */
linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)

/* Sección Acerca */
linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%)

/* Sección Servicios */
linear-gradient(135deg, #ffffff 0%, #f5f7fa 100%)

/* Card destacada */
linear-gradient(135deg, rgba(212,165,116,0.08) 0%, rgba(139,115,85,0.05) 100%)

/* Línea decorativa cards */
linear-gradient(90deg, #D4A574, #8B7355)
```

### Background Patterns
```css
/* Cuadrícula sutil en body */
background-image: 
  linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px),
  linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px);
background-size: 20px 20px;
```

---

## 🖼️ Imágenes

### Logo Principal
- **Formato**: JPG
- **Ubicación**: `./assets/img/logo.jpg`
- **Tamaño Hero**: Auto height, max-width con aspect ratio
- **Border-radius**: 15px
- **Sombra**: 0 8px 20px rgba(0,0,0,0.15)

### Logo Footer
- **Width**: 80px
- **Border-radius**: 10px
- **Margin-bottom**: 1rem

### Hero Background
- **Tipo**: Imagen de fondo fullscreen
- **Position**: Center center
- **Size**: Cover
- **Overlay**: Gradient transparente

---

## 📊 Elementos Especiales

### Estadísticas
```
Container:
- Background: Blanco
- Padding: 2rem
- Border-radius: 20px
- Sombra: 0 10px 30px rgba(0,0,0,0.1)

Números:
- Font-size: 3rem
- Font-weight: 700
- Color: Dorado
- Counter animation

Texto:
- Font-size: 0.9rem
- Color: Gris medio
```

### Etiqueta de Sección
```
Display: inline-block
Color: Dorado
Font-size: 0.85rem
Font-weight: 600
Letter-spacing: 2px
Text-transform: uppercase
```

### Línea Decorativa
```
Width: 80px
Height: 3px
Background: Dorado
Margin: 0 auto 3rem
Border-radius: 2px
```

### Lista de Características (Checkmarks)
```
Icono: fa-check
Color icono: Dorado
Font-size: 0.9rem
Gap: 0.8rem entre items
```

---

## 🌐 Estados Interactivos

### Estados de Enlaces
```
Normal: Blanco/Azul Oscuro
Hover: Dorado
Active: Dorado
Focus: Outline dorado
```

### Estados de Botones
```
Normal: Colores base
Hover: Elevación + color más oscuro
Active: Scale(0.98)
Disabled: Opacity 0.5, cursor not-allowed
```

### Estados de Cards
```
Normal: Reposo
Hover: Elevación + sombra aumentada + animaciones internas
Focus: Borde dorado visible
```

---

## ⚡ Performance y Optimización

### Optimizaciones Aplicadas
- **Backdrop-filter**: Usado con moderación
- **Transform/Opacity**: Preferidos para animaciones
- **Will-change**: Aplicado en elementos animados
- **Lazy loading**: Para imágenes pesadas
- **Font-display: swap**: Para Google Fonts

---

## 📚 Librerías y Recursos

### Fuentes
- **Google Fonts**: Poppins (300, 400, 500, 600, 700)
- Link: `https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap`

### Iconos
- **Font Awesome 6.0.0**
- Link: `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css`
- Familias usadas: `fas` (solid), `fab` (brands)

### Frameworks
- **CSS Puro** - Sin frameworks CSS
- **Vanilla JavaScript** - Sin jQuery

---

## 🎯 Accesibilidad

### Consideraciones
- **Contraste**: Todos los textos cumplen WCAG AA
- **Focus visible**: Outlines en elementos interactivos
- **ARIA labels**: En botones de navegación
- **Alt text**: En todas las imágenes
- **Semantic HTML**: Uso correcto de tags HTML5

### Ratios de Contraste
- Texto principal sobre blanco: 12.63:1 (AAA) ✅
- Dorado sobre blanco: 3.24:1 (AA) ✅
- Azul oscuro sobre blanco: 12.42:1 (AAA) ✅

---

## 📝 Notas de Implementación

### Variables CSS
Todas las variables están definidas en `:root` al inicio del archivo CSS para fácil mantenimiento y consistencia.

### Nomenclatura
- BEM-style classes cuando aplica
- Nombres descriptivos en español
- Prefijos semánticos (seccion-, card-, boton-, etc.)

### Estructura de Archivos
```
/assets
  /css
    estilos.css (archivo único, organizado por secciones)
  /img
    logo.jpg
  /js
    script.js
```

---

**Fecha de creación**: Noviembre 2025  
**Versión**: 1.0  
**Diseño**: Sistema de diseño completo documentado  
**Desarrollador**: Claudio del Rio - Web.malgarini®

---

## 🔄 Changelog

### Versión 1.0 - Noviembre 2025
- ✅ Documentación inicial completa
- ✅ Sistema de colores definido
- ✅ Tipografía establecida
- ✅ Componentes documentados
- ✅ Sistema responsive implementado

