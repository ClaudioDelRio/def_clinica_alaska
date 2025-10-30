-- Migración: agregar campos de administrador y password para médicos
-- Fecha: 2025-10-30

ALTER TABLE ca_medicos
  ADD COLUMN es_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER email,
  ADD COLUMN password_hash VARCHAR(255) NULL AFTER es_admin;

-- Opcional: Si manejas relación entre usuarios y médicos (usuario_id), asegúrate de que exista la columna
-- ALTER TABLE ca_medicos ADD COLUMN usuario_id INT NULL AFTER id;
-- Y su índice
-- ALTER TABLE ca_medicos ADD INDEX idx_medicos_usuario_id (usuario_id);

-- Verificación rápida
-- DESCRIBE ca_medicos;


