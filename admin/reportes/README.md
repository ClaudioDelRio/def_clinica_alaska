# 📊 Sistema de Reportes en PDF

Sistema de generación de reportes en formato PDF para la Clínica Veterinaria Alaska Pets Center.

---

## 📁 Estructura de Archivos

```
admin/reportes/
├── README.md                       # Este archivo
├── gestionar-reportes.php          # Interfaz principal de reportes
└── generar-reporte-diario.php      # Generador de PDF de reporte diario
```

---

## 🎯 Funcionalidades Implementadas

### ✅ Reporte Diario por Médico

Genera un PDF con todas las citas de un médico en una fecha específica.

**Características:**
- Selección de médico (o "Todos los médicos")
- Selección de fecha
- Estadísticas resumidas (Total, Pendientes, Confirmadas, Completadas, Canceladas)
- Tabla detallada con:
  - Hora de la cita y duración
  - Datos del cliente (nombre, teléfono)
  - Información de la mascota (nombre, especie, raza)
  - Tipo de servicio
  - Motivo de la consulta
  - Estado de la cita
- Diseño profesional con logo y colores corporativos
- Formato optimizado para impresión

---

## 🚀 Cómo Usar

### Desde el Panel de Administración:

1. Accede al menú lateral y haz clic en **"Reportes"**
2. En la página de Gestión de Reportes, haz clic en **"Generar"** en la tarjeta "Reporte Diario"
3. Se abrirá un modal donde debes:
   - **Seleccionar el médico** (o "Todos los médicos")
   - **Seleccionar la fecha** del reporte
4. Haz clic en **"Generar PDF"**
5. El PDF se abrirá automáticamente en una nueva pestaña

### Opciones del Reporte:

- **Médico específico:** Muestra solo las citas de ese médico
- **Todos los médicos:** Muestra todas las citas del día, agrupadas por médico

---

## 🛠️ Dependencias

### Dompdf

El sistema utiliza [Dompdf](https://github.com/dompdf/dompdf) para la generación de PDFs.

**Ubicación:** `/vendor/dompdf/`

**Instalación (si no está instalado):**
```bash
composer require dompdf/dompdf
```

---

## 📋 Detalles Técnicos

### Flujo de Generación del PDF:

1. **Validación de sesión** - Verifica que el usuario esté logueado
2. **Obtención de parámetros** - Médico y fecha desde $_GET
3. **Consulta a BD** - Obtiene las citas según los filtros
4. **Cálculo de estadísticas** - Cuenta citas por estado
5. **Generación de HTML** - Crea el documento con estilos inline
6. **Conversión a PDF** - Dompdf procesa el HTML
7. **Envío al navegador** - Stream del PDF (sin forzar descarga)

### Consultas SQL:

**Para un médico específico:**
```sql
SELECT c.*, u.nombre AS cliente_nombre, m.nombre AS mascota_nombre, ...
FROM ca_citas c
INNER JOIN ca_usuarios u ON c.usuario_id = u.id
INNER JOIN ca_mascotas m ON c.mascota_id = m.id
WHERE c.medico_id = ? AND DATE(c.fecha_hora) = ?
ORDER BY c.fecha_hora ASC
```

**Para todos los médicos:**
```sql
SELECT c.*, u.nombre AS cliente_nombre, m.nombre AS mascota_nombre, 
       med.nombre AS medico_nombre, ...
FROM ca_citas c
INNER JOIN ca_usuarios u ON c.usuario_id = u.id
INNER JOIN ca_mascotas m ON c.mascota_id = m.id
LEFT JOIN ca_medicos med ON c.medico_id = med.id
WHERE DATE(c.fecha_hora) = ?
ORDER BY c.fecha_hora ASC, med.nombre ASC
```

---

## 🎨 Diseño del PDF

### Secciones del Reporte:

1. **Header** - Logo, título y subtítulo con gradiente
2. **Información** - Médico, fecha y hora de generación
3. **Estadísticas** - Cajas con contadores por estado
4. **Tabla de Citas** - Listado detallado de todas las citas
5. **Footer** - Datos de contacto de la clínica

### Colores Utilizados:

- **Primario:** `#2c3e50` (Azul oscuro)
- **Secundario:** `#D4A574` (Dorado)
- **Fondo:** `#f8f9fa` (Gris claro)
- **Estados:**
  - Pendiente: `#e65100` (Naranja)
  - Confirmada: `#2e7d32` (Verde)
  - Completada: `#1565c0` (Azul)
  - Cancelada: `#c62828` (Rojo)

---

## 🔐 Seguridad

- ✅ Validación de sesión del médico
- ✅ Parámetros validados y sanitizados
- ✅ Consultas con prepared statements
- ✅ Solo médicos logueados pueden acceder
- ✅ Verificación de formato de fecha
- ✅ Manejo de errores con try-catch

---

## 📈 Reportes Futuros (Planificados)

### 🔒 Reporte Semanal
Resumen de citas de una semana completa con gráficos.

### 🔒 Reporte Mensual
Estadísticas mensuales con análisis de tendencias.

### 🔒 Reporte de Ingresos
Análisis financiero con desglose por servicios.

### 🔒 Reporte de Clientes
Listado de clientes con su historial de visitas.

---

## 🐛 Solución de Problemas

### Error: "Acceso no autorizado"
**Causa:** La sesión del médico no está activa.
**Solución:** Vuelve a iniciar sesión en el panel de administración.

### Error: "Parámetros incompletos"
**Causa:** Falta el médico o la fecha.
**Solución:** Asegúrate de seleccionar ambos campos en el modal.

### Error: "No se pueden cargar los médicos"
**Causa:** Problema de conexión con la API.
**Solución:** Verifica que el archivo `api/obtener-doctores.php` esté funcionando.

### El PDF se ve mal o sin estilos
**Causa:** Dompdf no pudo procesar el CSS inline.
**Solución:** Verifica que todos los estilos estén dentro de `<style>` tags en el HTML.

### Fuentes no se muestran correctamente
**Causa:** La fuente especificada no está disponible.
**Solución:** Dompdf usa "DejaVu Sans" por defecto, que está incluida.

---

## 📝 Notas de Desarrollo

### Modificar el Diseño del PDF:

El HTML del PDF se genera en la función `generarHTMLReporte()` dentro de `generar-reporte-diario.php`. Los estilos están inline en una etiqueta `<style>`.

### Agregar Nuevos Reportes:

1. Crea el archivo PHP generador en `admin/reportes/`
2. Agrega una nueva tarjeta en `gestionar-reportes.php`
3. Crea la función JavaScript para abrir el modal
4. Implementa la lógica de consulta y generación

### Consideraciones de Rendimiento:

- Los reportes con muchas citas (>100) pueden tardar unos segundos
- Dompdf consume memoria al procesar HTML complejos
- Se recomienda limitar reportes a máximo 1 mes de datos

---

## 👨‍💻 Desarrollado por

**Claudio del Rio** - Web.malgarini®  
**Proyecto:** Clínica Veterinaria Alaska Pets Center  
**Versión:** 1.5  
**Fecha:** Noviembre 2025

---

## 📚 Recursos

- [Dompdf Documentation](https://github.com/dompdf/dompdf)
- [Dompdf Wiki](https://github.com/dompdf/dompdf/wiki)
- [HTML to PDF Best Practices](https://github.com/dompdf/dompdf/wiki/Usage)

