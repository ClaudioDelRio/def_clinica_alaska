-- Script para agregar soporte de duración en citas
-- Clínica Veterinaria Alaska Pets Center

-- Agregar campo de duración en minutos
ALTER TABLE ca_citas ADD COLUMN IF NOT EXISTS duracion_minutos INT DEFAULT 30;

-- Agregar campo para agrupar citas de múltiples bloques
ALTER TABLE ca_citas ADD COLUMN IF NOT EXISTS grupo_cita_id VARCHAR(50) NULL;

-- Agregar índice para búsquedas más rápidas
CREATE INDEX IF NOT EXISTS idx_grupo_cita ON ca_citas(grupo_cita_id);

-- Comentarios
COMMENT ON COLUMN ca_citas.duracion_minutos IS 'Duración de la cita en minutos (30, 60, 90, 120, 150, 180, 210, 240)';
COMMENT ON COLUMN ca_citas.grupo_cita_id IS 'ID único para agrupar citas que ocupan múltiples bloques horarios';

