-- ============================================
-- MIGRACIÓN: Agregar campo RUT a tabla ca_usuarios
-- Base de datos: CLÍNICA VETERINARIA ALASKA
-- ============================================

USE cldelriolaborato_c_alaska;

-- Agregar columna RUT a la tabla ca_usuarios
ALTER TABLE ca_usuarios 
ADD COLUMN rut VARCHAR(12) NOT NULL COMMENT 'RUT del usuario (formato: 12.345.678-9)' AFTER email,
ADD UNIQUE KEY unique_rut (rut),
ADD INDEX idx_rut (rut);

-- ============================================
-- NOTAS:
-- - El campo RUT es OBLIGATORIO (NOT NULL)
-- - Se agrega índice único para evitar RUTs duplicados
-- - Formato esperado: XX.XXX.XXX-X (con puntos y guión)
-- - El RUT se valida en el backend antes de insertar usando algoritmo Módulo 11
-- - Si tienes registros existentes, primero actualízalos con un RUT válido
-- ============================================

