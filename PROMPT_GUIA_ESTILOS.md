# 📋 PROMPT: Crear Guía de Estilos Completa

> Prompt optimizado para que otra IA genere una guía de estilos (Style Guide) profesional y completa

---

## 🎯 INSTRUCCIONES PARA LA IA

Por favor, crea una **Guía de Estilos (Style Guide) completa y profesional** para el sitio web de la Clínica Veterinaria Alaska Pets Center basándote en el vocabulario visual proporcionado.

---

## 📦 CONTEXTO DEL PROYECTO

### Sobre el Negocio
- **Nombre**: Clínica Veterinaria Alaska Pets Center
- **Ubicación**: Osorno, Los Lagos, Chile
- **Fundación**: 11 de Febrero de 2001
- **Experiencia**: Más de 22 años en el rubro
- **Especialidades**: Cirugía de tejidos blandos, Radiología, Anestesiología
- **Público objetivo**: Dueños de mascotas (perros y gatos) que buscan atención veterinaria de calidad

### Personalidad de Marca
- **Profesional**: Experiencia de más de 2 décadas
- **Cálido**: Amor por los animales
- **Confiable**: Equipamiento moderno y especialización
- **Cercano**: Servicio personalizado

### Valores de Marca
- Excelencia en el servicio
- Compromiso con el bienestar animal
- Profesionalismo médico
- Calidez en la atención
- Innovación tecnológica

---

## 📄 DOCUMENTOS BASE

Tengo dos documentos que debes usar como referencia:

1. **VOCABULARIO_VISUAL.md** - Contiene todos los elementos de diseño técnicos:
   - Paleta de colores completa
   - Tipografía y jerarquías
   - Componentes UI documentados
   - Espaciados y layouts
   - Sombras, bordes, animaciones
   - Sistema responsive

2. **MAPA_NAVEGACION_SITIO.md** - Contiene la estructura del sitio:
   - Arquitectura de información
   - Flujos de navegación
   - Secciones y contenidos

---

## 🎨 QUÉ DEBE INCLUIR LA GUÍA DE ESTILOS

### 1. IDENTIDAD DE MARCA

#### Logo
- Uso correcto del logo
- Variaciones permitidas (color, blanco, negro)
- Área de protección mínima
- Tamaños mínimos y máximos
- Usos incorrectos (ejemplos visuales de lo que NO hacer)

#### Paleta de Colores
- Colores primarios y secundarios
- Códigos en diferentes formatos (Hex, RGB, CMYK para impresos)
- **Significado emocional** de cada color
- **Uso correcto** de cada color (cuándo y dónde usar cada uno)
- Ejemplos visuales de combinaciones correctas e incorrectas
- Accesibilidad: ratios de contraste

---

### 2. TIPOGRAFÍA

#### Familias Tipográficas
- Fuente principal: Poppins
- Dónde conseguirla (Google Fonts)
- Alternativas/fallbacks

#### Jerarquía Tipográfica
- **H1, H2, H3, H4**: Tamaños, pesos, usos específicos
- **Body text**: Tamaños para diferentes contextos
- **Captions y textos pequeños**
- Ejemplos visuales de cada nivel

#### Reglas de Uso
- ¿Cuándo usar cada peso?
- Espaciado entre líneas (line-height)
- Espaciado entre letras (letter-spacing)
- Longitud máxima de línea para legibilidad
- **NO USAR**: Qué evitar (todas mayúsculas en párrafos, subrayado, etc.)

---

### 3. COMPONENTES UI

Para cada componente, incluir:

#### Botones
- **Tipos**: Primario, Secundario, Terciario
- **Estados**: Normal, Hover, Active, Disabled, Focus
- **Tamaños**: Grande, Mediano, Pequeño
- **Cuándo usar cada tipo**
- **Anatomía**: Padding, border-radius, sombras
- Ejemplos visuales de todos los estados
- **NO HACER**: Ejemplos de uso incorrecto

#### Cards
- Tipos de cards (servicio, contacto, destacada)
- Estados (hover, active)
- Contenido máximo/mínimo
- Espaciado interno
- Cuándo usar cada tipo

#### Formularios
- Inputs: Estados (normal, focus, error, success, disabled)
- Labels: Posición y estilo
- Validación: Mensajes de error y éxito
- Iconos en inputs
- Botones de submit

#### Iconos
- Librería usada (Font Awesome 6.0.0)
- Tamaños estándar
- Colores permitidos
- Espaciado con texto
- Iconos principales del sistema

#### Navegación
- Header: Comportamiento normal y al scroll
- Menú móvil (hamburguesa)
- Footer: Estructura y contenido
- Enlaces: Estados y estilos

---

### 4. ESPACIADO Y LAYOUT

#### Sistema de Grids
- Columnas
- Gutters
- Márgenes
- Contenedores

#### Espaciado Consistente
- Sistema de espaciado (8px base)
- Cuándo usar cada nivel
- Ritmo vertical
- Whitespace (espacio en blanco)

#### Secciones
- Padding estándar de secciones
- Separación entre secciones
- Máximo ancho de contenido

---

### 5. IMÁGENES Y MULTIMEDIA

#### Fotografías
- Estilo fotográfico (cálido, profesional, con mascotas)
- Aspect ratios recomendados
- Calidad mínima
- Filtros o ajustes de color
- Dónde conseguir imágenes (bancos de imágenes recomendados)

#### Ilustraciones
- Estilo de ilustraciones (si aplica)
- Colores permitidos

#### Videos
- Formato y dimensiones
- Autoplay (sí/no)
- Controles

---

### 6. TONO Y VOZ

#### Personalidad de la Marca
- Profesional pero cercano
- Experto pero accesible
- Comprometido y cálido

#### Tono de Escritura
- **Formal vs Informal**: Nivel de formalidad
- **Vocabulario**: Palabras que usar / evitar
- **Persona gramatical**: ¿Tú, usted, nosotros?
- **Tiempo verbal**: Presente, futuro

#### Ejemplos de Texto
- **CORRECTO**: "Cuidamos la salud de tu mascota con amor y profesionalismo"
- **INCORRECTO**: "Ofrecemos servicios veterinarios de calidad"

- **CORRECTO**: "Más de 22 años cuidando a tu mejor amigo"
- **INCORRECTO**: "22 años de experiencia en el mercado veterinario"

#### Pautas de Contenido
- Longitud de títulos
- Longitud de párrafos
- Uso de emojis (¿permitido?)
- Puntuación
- Mayúsculas y minúsculas

---

### 7. ANIMACIONES E INTERACCIONES

#### Principios de Movimiento
- Duración: Rápida (0.2s), Normal (0.3s), Lenta (0.5s)
- Timing functions: ease, ease-in-out
- Qué animar: transforms y opacity preferidos

#### Interacciones Específicas
- **Hover en botones**: Elevación + cambio de color
- **Hover en cards**: Elevación + sombra
- **Scroll**: Aparición de elementos
- **Clic**: Feedback visual

#### Micro-interacciones
- Carga de página
- Envío de formularios
- Mensajes de éxito/error (toasts)

---

### 8. RESPONSIVE DESIGN

#### Breakpoints
- Mobile: < 768px
- Tablet: 768px - 1024px
- Desktop: > 1024px

#### Adaptaciones por Dispositivo
- Qué cambia en móvil
- Qué cambia en tablet
- Touch targets mínimos (44px×44px)
- Menú móvil vs desktop

#### Priorización de Contenido
- Qué mostrar primero en móvil
- Qué puede ocultarse

---

### 9. ACCESIBILIDAD (a11y)

#### Estándares
- Cumplimiento WCAG 2.1 AA mínimo
- Contraste de colores
- Tamaño mínimo de fuente
- Espaciado de elementos interactivos

#### Navegación por Teclado
- Tab order lógico
- Focus visible
- Skip links

#### Lectores de Pantalla
- Alt text en imágenes
- ARIA labels
- Roles semánticos

#### Color No Como Único Indicador
- No confiar solo en color para transmitir información
- Usar iconos, textos, patrones

---

### 10. PRINCIPIOS DE DISEÑO

#### Jerarquía Visual
- Cómo guiar al usuario
- Elementos más importantes primero
- Tamaños relativos

#### Consistencia
- Mismos elementos = mismo estilo
- Predecibilidad
- Patrones de diseño

#### Simplicidad
- Menos es más
- Un objetivo por sección
- Whitespace es tu amigo

#### Feedback Visual
- Toda acción debe tener una reacción
- Estados de carga
- Confirmaciones

---

### 11. CASOS DE USO

Incluye ejemplos visuales o descripciones de:

#### Landing Page (Hero Section)
- Qué elementos incluir
- Orden de información
- CTA principal

#### Sección de Servicios
- Cómo presentar un servicio
- Card structure
- Iconos a usar

#### Formulario de Contacto
- Campos requeridos
- Validación
- Mensaje de éxito

#### Footer
- Información obligatoria
- Enlaces importantes
- Redes sociales

---

### 12. DO'S AND DON'TS

Para cada sección importante, incluir ejemplos visuales de:

#### ✅ DO (Hacer)
- Usar espaciado consistente
- Mantener jerarquía visual clara
- Usar los colores de marca correctamente
- Proporcionar feedback visual
- Optimizar para móviles

#### ❌ DON'T (No Hacer)
- Mezclar demasiados estilos de fuente
- Usar colores que no están en la paleta
- Amontonar elementos sin espacio
- Usar imágenes de baja calidad
- Ignorar la accesibilidad

---

### 13. RECURSOS Y HERRAMIENTAS

#### Para Diseñadores
- Archivo Figma/Sketch (si existe)
- Paleta de colores exportable
- Kit UI completo

#### Para Desarrolladores
- Variables CSS
- Clases reutilizables
- Snippets de código

#### Assets
- Dónde encontrar el logo
- Dónde encontrar iconos
- Banco de imágenes aprobado

---

### 14. CHECKLIST DE CALIDAD

Antes de publicar cualquier diseño, verificar:

- [ ] ¿Usa los colores de la paleta?
- [ ] ¿La tipografía es correcta?
- [ ] ¿El espaciado es consistente?
- [ ] ¿Los botones tienen los estados correctos?
- [ ] ¿Es responsive?
- [ ] ¿Es accesible (contraste, alt text)?
- [ ] ¿Las imágenes son de buena calidad?
- [ ] ¿El tono del texto es apropiado?
- [ ] ¿Hay feedback visual en interacciones?
- [ ] ¿Se probó en diferentes navegadores?

---

## 📊 FORMATO DE ENTREGA

Por favor, estructura la guía de estilos de la siguiente manera:

### Documento Principal
```
1. PORTADA
   - Logo
   - Título: "Guía de Estilos - Alaska Pets Center"
   - Versión y fecha
   - Créditos

2. ÍNDICE
   - Navegación clara a todas las secciones

3. INTRODUCCIÓN
   - Propósito de la guía
   - Cómo usar este documento
   - A quién va dirigido

4. SECCIONES PRINCIPALES
   (Todas las listadas arriba)

5. APÉNDICES
   - Glosario de términos
   - Referencias
   - Contacto para dudas
```

### Estilo del Documento
- **Visual**: Incluye ejemplos visuales (aunque sean descripciones detalladas)
- **Claro**: Lenguaje simple y directo
- **Organizado**: Fácil de navegar
- **Completo**: Cubre todos los casos de uso
- **Actualizable**: Facilita agregar nuevas secciones

---

## 🎨 ASPECTOS CLAVE A DESTACAR

### Diferenciadores de Alaska Pets Center
1. **Experiencia**: +22 años - Resaltar profesionalismo
2. **Especialización**: Cirugía de tejidos blandos - Destacar expertise
3. **Cercanía**: Atención personalizada - Transmitir calidez
4. **Modernidad**: Equipamiento de vanguardia - Mostrar innovación

### Emoción que debe transmitir
- **Confianza**: "Mi mascota está en buenas manos"
- **Tranquilidad**: "Son profesionales con experiencia"
- **Calidez**: "Realmente aman a los animales"
- **Profesionalismo**: "Tienen lo mejor en tecnología"

---

## 📚 REFERENCIAS PARA INSPIRACIÓN

### Guías de Estilo Ejemplares
- Material Design (Google)
- Human Interface Guidelines (Apple)
- Carbon Design System (IBM)
- Atlassian Design System

### Estructura Esperada
Similar a guías de estilo profesionales de empresas de salud/servicios que combinan profesionalismo con calidez.

---

## ✅ CRITERIOS DE ÉXITO

Una guía de estilos exitosa debe:

1. **Ser completa**: Cubrir todos los elementos del sitio
2. **Ser clara**: Cualquier persona puede entenderla
3. **Ser visual**: Mostrar ejemplos, no solo describirlos
4. **Ser práctica**: Realmente utilizable por diseñadores y developers
5. **Ser consistente**: Mantener coherencia en toda la guía
6. **Ser accesible**: Incluir consideraciones de accesibilidad
7. **Ser mantenible**: Fácil de actualizar en el futuro

---

## 💡 NOTAS ADICIONALES

### Tono de la Guía
La guía de estilos debe ser:
- Profesional pero amigable
- Instructiva pero no condescendiente
- Detallada pero concisa
- Prescriptiva (esto SE hace así) pero explicando el por qué

### Público de la Guía
- Diseñadores gráficos
- Desarrolladores web
- Creadores de contenido
- Marketing y redes sociales
- Stakeholders del proyecto

---

## 🚀 ENTREGABLE FINAL

**Formato**: Markdown (.md) o PDF profesional
**Extensión esperada**: 30-50 páginas
**Lenguaje**: Español (Chile)
**Incluir**: Tabla de contenidos navegable
**Versión**: 1.0 - Noviembre 2025

---

## 📎 ANEXO: INFORMACIÓN ADICIONAL

### Datos de Contacto de la Clínica
- **Dirección**: Alcalde Saturnino Barril 1380, Osorno, Los Lagos, Chile
- **Teléfono 1**: (+64) 227 0539
- **Teléfono 2**: (+56) 9 9365 1250
- **Email**: osorno@clinicaalaska.cl
- **Facebook**: Alaska Pets Center Ltda

### Horarios
- **Lunes a Viernes**: 10:00-13:00 / 15:00-19:00
- **Sábados**: 11:00-13:00 / 14:00-17:00

### Servicios Principales
1. Consultas Veterinarias
2. Cirugías Generales
3. Cirugía de Tejidos Blandos (Especialidad)
4. Radiología Digital
5. Vacunación
6. Anestesiología

---

**NOTA FINAL**: Esta guía de estilos debe ser un documento vivo que evolucione con la marca. Debe ser lo suficientemente específica para mantener consistencia, pero lo suficientemente flexible para permitir innovación futura.

---

**Desarrollador del sitio**: Claudio del Rio - Web.malgarini®  
**Fecha de solicitud**: Noviembre 2025  
**Versión del prompt**: 1.0

