# 🗄️ Instrucciones para Crear la Base de Datos

## 📄 Archivo a Importar

**Usa este archivo:** `clinica_veterinaria_base.sql`

Este archivo contiene lo **esencial** para empezar: usuarios y mascotas.

---

## 🚀 Método 1: phpMyAdmin (Más Fácil)

### Pasos:

1. **Abre XAMPP Control Panel**
   - Inicia **Apache** ✅
   - Inicia **MySQL** ✅

2. **Abre phpMyAdmin**
   - En tu navegador: `http://localhost/phpmyadmin`

3. **Importa el archivo**
   - Haz clic en la pestaña **"Importar"** (arriba)
   - Clic en **"Seleccionar archivo"**
   - Busca: `database/clinica_veterinaria_base.sql`
   - Clic en **"Continuar"** (abajo)
   - Espera 5-10 segundos

4. **¡Listo!** ✅
   - Verás el mensaje: "Base de datos creada exitosamente"
   - En el panel izquierdo aparecerá `clinica_veterinaria`

---

## 💻 Método 2: Línea de Comandos (Avanzado)

### Para Windows (CMD):
```cmd
cd "D:\Desktop\Mi sitios Web\trabajando\clinicaakasjadef"
"C:\xampp\mysql\bin\mysql.exe" -u root -p < database\clinica_veterinaria_base.sql
```

### Para Mac/Linux:
```bash
cd "/ruta/a/tu/proyecto/clinicaakasjadef"
mysql -u root -p < database/clinica_veterinaria_base.sql
```

Si te pide contraseña y no la has configurado, solo presiona **Enter**.

---

## ✅ ¿Qué Se Crea?

### 📊 Tablas (2):
1. **ca_usuarios** - Clientes registrados
   - Campos: id, nombre, email, teléfono, **dirección**, password, fecha_registro, ultimo_acceso, activo
   
2. **ca_mascotas** - Mascotas de los clientes (Relación 1:N)
   - Campos: id, usuario_id, nombre, especie, raza, edad, peso, sexo, color, observaciones, fecha_registro, activo
   - **Relación:** Un usuario puede tener MUCHAS mascotas
   - **Foreign Key:** ON DELETE CASCADE (si se elimina el usuario, se eliminan sus mascotas)

### 🔗 Relación Entre Tablas:
```
ca_usuarios (1) ←─────→ (N) ca_mascotas
   id                      usuario_id
```

**Ejemplo:**
- Usuario "Juan" (id=1) puede tener → Firulais, Michi, Rocky
- Usuario "María" (id=2) puede tener → Max, Luna

---

## 📝 Estado de las Tablas

Las tablas están **VACÍAS** y listas para que agregues tus propios datos.

- ✅ Tabla `ca_usuarios` creada (vacía)
- ✅ Tabla `ca_mascotas` creada (vacía)
- ✅ Relación 1:N configurada correctamente
- 🎯 Listas para empezar a trabajar

---

## 🔍 Verificar que Funcionó

### En phpMyAdmin:
1. Busca `clinica_veterinaria` en el panel izquierdo
2. Haz clic en ella
3. Deberías ver 2 tablas: `ca_usuarios` y `ca_mascotas`

### Consulta de Prueba:
En phpMyAdmin, ve a la pestaña **SQL** y ejecuta:
```sql
SELECT * FROM ca_usuarios;
```

Debería estar vacía (0 resultados).

---

## ❌ Si Algo Sale Mal

### Error: "Base de datos ya existe"
```sql
-- Ejecuta esto primero en phpMyAdmin (pestaña SQL):
DROP DATABASE IF EXISTS clinica_veterinaria;
-- Luego importa el archivo de nuevo
```

### Error: "Tabla ya existe"
Si ya creaste las tablas con otros nombres:
```sql
-- Renombrar tablas existentes:
RENAME TABLE usuarios TO ca_usuarios;
RENAME TABLE mascotas TO ca_mascotas;
```

### Error: "Archivo muy grande"
Edita `php.ini` (en XAMPP: `C:\xampp\php\php.ini`):
```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
```
Reinicia Apache.

### Error: "Acceso denegado"
Tu MySQL tiene contraseña. Edita `api/config.php`:
```php
define('DB_PASS', 'tu_contraseña_aqui');
```

---

## 📞 Próximos Pasos

Después de crear la base de datos:

1. ✅ Verifica en `api/config.php` que las credenciales sean correctas
2. ✅ Abre tu proyecto: `http://localhost/clinicaakasjadef/`
3. ✅ Haz clic en "AGENDAR HORA" → "Regístrate"
4. ✅ Crea tu primer usuario
5. ✅ Empieza a trabajar con tu propia data

---

## 📝 Notas Importantes

- ✅ **Base simple** - Solo usuarios y mascotas por ahora
- ✅ **Relación 1:N** - Un usuario puede tener múltiples mascotas
- ✅ **Campo dirección** incluido en usuarios
- ✅ **Datos de prueba** para empezar a trabajar
- 📦 **Preparado para crecer** - Cuando necesites citas, servicios, productos, los agregamos

### 🔮 ¿Qué Falta?
Lo agregaremos cuando lo necesites:
- 📅 Sistema de citas
- 🏥 Catálogo de servicios
- 🛒 E-commerce (productos)
- 💳 Sistema de pagos
- 📊 Dashboard administrativo

**Vamos paso a paso, sin complicar de más** ✨

---

**¡Todo listo para empezar!** 🐾

