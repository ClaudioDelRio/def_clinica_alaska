-- ============================================
-- MIGRACIÓN: Agregar RUT a usuarios existentes
-- Base de datos: CLÍNICA VETERINARIA ALASKA
-- ============================================
-- 
-- ⚠️ IMPORTANTE: Este script es para cuando ya tienes usuarios registrados
--    y necesitas agregar el campo RUT de forma obligatoria.
--
-- Opciones:
-- 1. Si NO tienes usuarios registrados: Usa agregar_campo_rut.sql directamente
-- 2. Si SÍ tienes usuarios registrados: Usa este script primero
-- ============================================

USE cldelriolaborato_c_alaska;

-- ============================================
-- OPCIÓN A: Agregar RUT como NULL temporalmente
-- ============================================
-- Primero agregamos el campo como NULL para no afectar registros existentes
ALTER TABLE ca_usuarios 
ADD COLUMN rut VARCHAR(12) NULL COMMENT 'RUT del usuario (formato: 12.345.678-9)' AFTER email;

-- ⚠️ AHORA DEBES ACTUALIZAR MANUALMENTE LOS RUTs DE TUS USUARIOS EXISTENTES
-- Ejemplo de actualización:
-- UPDATE ca_usuarios SET rut = '12.345.678-5' WHERE id = 1;
-- UPDATE ca_usuarios SET rut = '11.111.111-1' WHERE id = 2;

-- ============================================
-- Después de actualizar TODOS los RUTs, ejecuta esto:
-- ============================================
-- Hacer el campo NOT NULL (después de que todos tengan RUT)
-- ALTER TABLE ca_usuarios MODIFY COLUMN rut VARCHAR(12) NOT NULL;

-- Agregar índice único
-- ALTER TABLE ca_usuarios ADD UNIQUE KEY unique_rut (rut);

-- Agregar índice para búsquedas
-- ALTER TABLE ca_usuarios ADD INDEX idx_rut (rut);

-- ============================================
-- OPCIÓN B: Eliminar usuarios existentes (CUIDADO - IRREVERSIBLE)
-- ============================================
-- Si tus usuarios son solo de prueba y quieres empezar de cero:
-- TRUNCATE TABLE ca_citas;       -- Elimina todas las citas primero (por foreign key)
-- TRUNCATE TABLE ca_mascotas;    -- Elimina todas las mascotas primero (por foreign key)
-- TRUNCATE TABLE ca_usuarios;    -- Elimina todos los usuarios

-- Luego puedes ejecutar agregar_campo_rut.sql para agregar el campo como NOT NULL

-- ============================================
-- OPCIÓN C: Generar RUTs temporales para pruebas (NO USAR EN PRODUCCIÓN)
-- ============================================
-- Solo para ambientes de desarrollo/prueba
-- Asigna RUTs temporales válidos a usuarios existentes

-- Algunos RUTs válidos para pruebas:
-- 12.345.678-5
-- 11.111.111-1
-- 7.654.321-K
-- 9.876.543-2
-- 16.123.456-7
-- 18.456.789-2
-- 20.123.456-9
-- 15.987.654-K

-- Ejemplo: Asignar RUTs temporales a los primeros usuarios
-- UPDATE ca_usuarios SET rut = '12.345.678-5' WHERE id = 1;
-- UPDATE ca_usuarios SET rut = '11.111.111-1' WHERE id = 2;

-- ============================================
-- VERIFICAR QUE TODOS LOS USUARIOS TENGAN RUT
-- ============================================
-- Ejecuta esta consulta para verificar:
-- SELECT id, nombre, email, rut FROM ca_usuarios WHERE rut IS NULL;

-- Si esta consulta NO retorna filas, puedes hacer el campo NOT NULL:
-- ALTER TABLE ca_usuarios MODIFY COLUMN rut VARCHAR(12) NOT NULL;
-- ALTER TABLE ca_usuarios ADD UNIQUE KEY unique_rut (rut);
-- ALTER TABLE ca_usuarios ADD INDEX idx_rut (rut);

-- ============================================
-- FIN DEL SCRIPT
-- ============================================

