# 📊 Sistema de Reportes en PDF

Sistema de generación de reportes en formato PDF para la Clínica Veterinaria Alaska Pets Center.

---

## 📁 Estructura de Archivos

```
admin/reportes/
├── README.md                       # Este archivo
├── gestionar-reportes.php          # Interfaz principal de reportes
├── generar-reporte-diario.php      # Generador de PDF del reporte diario
├── generar-reporte-semanal.php     # Generador de PDF del reporte semanal
└── generar-reporte-mensual.php     # Generador de PDF del reporte mensual
```

---

## 🎯 Funcionalidades Implementadas

### ✅ Reporte Diario por Médico

Genera un PDF con todas las citas de un médico en una fecha específica.

**Características:**
- Selección de médico (o "Todos los médicos")
- Selección de fecha puntual
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

### ✅ Reporte Semanal

Genera un PDF con todas las citas dentro de un rango semanal.

**Características:**
- Selección de médico (o "Todos los médicos")
- Selección de fechas de inicio y término del periodo
- Estadísticas resumidas por estado de cita
- Tabla con fecha, hora, cliente, mascota, servicio, motivo y estado
- Identificación del médico en la tabla cuando se consulta "Todos los médicos"

### ✅ Reporte Mensual

Genera un PDF con todas las citas del mes seleccionado.

**Características:**
- Selección de médico (o "Todos los médicos")
- Selección de mes mediante control `type="month"`
- Estadísticas resumidas por estado de cita
- Tabla con fecha, hora, cliente, mascota, servicio, motivo y estado
- Identificación del médico cuando se solicita reporte global

---

## 🚀 Cómo Usar

### Desde el Panel de Administración:

1. Accede al menú lateral y haz clic en **"Reportes"**.
2. Elige la tarjeta correspondiente (Diario, Semanal o Mensual) y pulsa **"Generar"**.
3. Completa los filtros requeridos:
   - **Reporte Diario:** médico + fecha específica.
   - **Reporte Semanal:** médico + fecha inicio + fecha fin.
   - **Reporte Mensual:** médico + mes.
4. Haz clic en **"Generar PDF"**.
5. El PDF se abre automáticamente en una nueva pestaña.

### Opciones de cada reporte:

- **Médico específico:** Muestra solo las citas del médico seleccionado.
- **Todos los médicos:** Incluye todas las citas del periodo y añade una columna con el nombre del médico.

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

1. **Validación de sesión** - Verifica que el usuario esté logueado.
2. **Obtención de parámetros** - Según el reporte (fecha, rango de fechas o mes, y médico).
3. **Consultas SQL** - Obtiene las citas según los filtros seleccionados.
4. **Cálculo de estadísticas** - Cuenta citas por estado.
5. **Generación de HTML** - Crea el documento con estilos inline.
6. **Conversión a PDF** - Dompdf procesa el HTML.
7. **Envío al navegador** - Se realiza mediante `stream`, no se fuerza la descarga.

### Consultas SQL (formato general):

```sql
SELECT
    c.id,
    c.fecha_cita,
    c.hora_cita,
    c.servicio,
    c.motivo,
    c.estado,
    c.duracion_minutos,
    u.nombre AS cliente_nombre,
    m.nombre AS mascota_nombre,
    med.nombre AS medico_nombre
FROM ca_citas c
INNER JOIN ca_usuarios u ON c.usuario_id = u.id
INNER JOIN ca_mascotas m ON c.mascota_id = m.id
LEFT JOIN ca_medicos med ON c.doctor_id = med.id
WHERE c.fecha_cita BETWEEN :fecha_inicio AND :fecha_fin
[AND c.doctor_id = :doctor_id]
ORDER BY c.fecha_cita ASC, c.hora_cita ASC;
```

---

## 🎨 Diseño del PDF

### Secciones del Reporte:

1. **Header** - Título del informe + identificación de la clínica.
2. **Información** - Médico, periodo y fecha/hora de generación.
3. **Estadísticas** - Cajas con contadores por estado (Total, Pendiente, Confirmada, Completada, Cancelada).
4. **Tabla de Citas** - Listado detallado de todas las citas del periodo.
5. **Footer** - Datos de contacto de la clínica.

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

- ✅ Validación de sesión del médico.
- ✅ Parámetros validados y sanitizados.
- ✅ Consultas con prepared statements.
- ✅ Solo médicos logueados pueden acceder.
- ✅ Verificación de formato de fecha/mes según corresponda.
- ✅ Manejo de errores con `try/catch`.

---

## 📈 Reportes Futuros (Planificados)

- 🔒 **Reporte de Ingresos:** Análisis financiero con desglose por servicios.
- 🔒 **Reporte de Clientes:** Historial de visitas por cliente con métricas agregadas.

---

## 🐛 Solución de Problemas

### Error: "Acceso no autorizado"
**Causa:** La sesión del médico no está activa.  
**Solución:** Vuelve a iniciar sesión en el panel de administración.

### Error: "Parámetros incompletos"
**Causa:** Alguno de los filtros no se completó.  
**Solución:** Asegúrate de completar todos los campos obligatorios del modal.

### Error: "No se pueden cargar los médicos"
**Causa:** Problema de conexión con la API.  
**Solución:** Verifica que el archivo `api/obtener-doctores.php` esté funcionando.

### El PDF se ve mal o sin estilos
**Causa:** Dompdf no pudo procesar el CSS inline.  
**Solución:** Verifica que todos los estilos estén dentro de `<style>` tags en el HTML del reporte.

### Fuentes no se muestran correctamente
**Causa:** La fuente especificada no está disponible.  
**Solución:** Dompdf usa "DejaVu Sans" por defecto, que está incluida.

---

## 📝 Notas de Desarrollo

### Modificar el Diseño del PDF:

El HTML del PDF se genera dentro de cada archivo `generar-reporte-*.php`. Los estilos están incrustados en etiquetas `<style>` para asegurar compatibilidad con Dompdf.

### Agregar Nuevos Reportes:

1. Crear un nuevo archivo `generar-reporte-*.php` con la lógica específica.
2. Agregar una tarjeta y modal en `gestionar-reportes.php`.
3. Actualizar `assets/js/admin-reportes.js` para manejar el nuevo flujo.

### Consideraciones de Rendimiento:

- Para periodos muy largos (meses con alta concurrencia), Dompdf puede tardar algunos segundos en renderizar.
- Mantener las consultas lo más específicas posible (uso de índices en `ca_citas`).
- Limitar el periodo máximo consultable si fuese necesario.

---

## 👨‍💻 Información del Desarrollo

**Desarrollado por:** Claudio del Rio - Web.malgarini®  
**Proyecto:** Clínica Veterinaria Alaska Pets Center  
**Versión:** 1.6  
**Fecha:** Noviembre 2025

---

## 📚 Recursos

- [Dompdf Documentation](https://github.com/dompdf/dompdf)
- [Dompdf Wiki](https://github.com/dompdf/dompdf/wiki)
- [HTML to PDF Best Practices](https://github.com/dompdf/dompdf/wiki/Usage)

