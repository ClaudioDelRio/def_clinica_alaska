-- ============================================
-- MIGRACIÓN: SISTEMA DE CITAS Y MÉDICOS
-- Clínica Veterinaria Alaska Pets Center
-- Este script actualiza la base de datos existente
-- ============================================

-- Seleccionar la base de datos
USE cldelriolaborato_c_alaska;

-- ============================================
-- PASO 1: MODIFICAR TABLA ca_mascotas
-- Agregar el campo vacunas_al_dia si no existe
-- ============================================

-- Agregar columna vacunas_al_dia a ca_mascotas
ALTER TABLE ca_mascotas 
ADD COLUMN IF NOT EXISTS vacunas_al_dia TINYINT(1) NOT NULL DEFAULT 0 
COMMENT 'Indica si las vacunas están al día (1=Sí, 0=No)';

SELECT '✅ Tabla ca_mascotas actualizada con campo vacunas_al_dia' as mensaje;

-- ============================================
-- PASO 2: CREAR TABLA ca_medicos
-- Tabla de médicos veterinarios de la clínica
-- ============================================

CREATE TABLE IF NOT EXISTS ca_medicos (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre completo del médico veterinario',
    especialidad VARCHAR(100) DEFAULT NULL COMMENT 'Especialidad del médico (opcional)',
    telefono VARCHAR(20) DEFAULT NULL COMMENT 'Teléfono de contacto',
    email VARCHAR(100) DEFAULT NULL COMMENT 'Email del médico',
    activo TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de registro en el sistema',
    PRIMARY KEY (id),
    INDEX idx_activo (activo),
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de médicos veterinarios de la clínica';

SELECT '✅ Tabla ca_medicos creada exitosamente' as mensaje;

-- ============================================
-- PASO 3: CREAR TABLA ca_citas
-- Tabla de citas/reservas de horas
-- ============================================

CREATE TABLE IF NOT EXISTS ca_citas (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    usuario_id INT(11) UNSIGNED NOT NULL COMMENT 'ID del usuario que reserva',
    mascota_id INT(11) UNSIGNED NOT NULL COMMENT 'ID de la mascota para la cita',
    doctor_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'Médico preferido (opcional, puede ser NULL)',
    servicio ENUM('consulta', 'vacunacion', 'cirugia', 'radiologia', 'laboratorio', 'peluqueria', 'emergencia') NOT NULL COMMENT 'Tipo de servicio solicitado',
    fecha_cita DATE NOT NULL COMMENT 'Fecha de la cita',
    hora_cita TIME NOT NULL COMMENT 'Hora de la cita',
    motivo TEXT NOT NULL COMMENT 'Motivo de la consulta',
    estado ENUM('pendiente', 'confirmada', 'completada', 'cancelada') NOT NULL DEFAULT 'pendiente' COMMENT 'Estado de la cita',
    observaciones TEXT DEFAULT NULL COMMENT 'Observaciones del médico o la clínica',
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación de la reserva',
    fecha_actualizacion DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última actualización',
    PRIMARY KEY (id),
    FOREIGN KEY (usuario_id) REFERENCES ca_usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (mascota_id) REFERENCES ca_mascotas(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES ca_medicos(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_mascota (mascota_id),
    INDEX idx_doctor (doctor_id),
    INDEX idx_fecha (fecha_cita),
    INDEX idx_estado (estado),
    INDEX idx_fecha_hora (fecha_cita, hora_cita)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de citas y reservas de horas';

SELECT '✅ Tabla ca_citas creada exitosamente' as mensaje;

-- ============================================
-- PASO 4: INSERTAR MÉDICOS DE EJEMPLO
-- Solo si la tabla está vacía
-- ============================================

INSERT INTO ca_medicos (nombre, especialidad, telefono, email, activo)
SELECT * FROM (
    SELECT 'Dr. Claudio del Rio Malgarini' as nombre, 'Cirugía' as especialidad, '+56 9 9365 1250' as telefono, 'osorno@clinicaalaska.cl' as email, 1 as activo
    UNION ALL
    SELECT 'Dr. Daniel Nuñez Peña' as nombre, 'Medicina Interna' as especialidad, '+56 9 5244 7853' as telefono, 'osorno@clinicaalaska.cl' as email, 1 as activo
) as tmp
WHERE NOT EXISTS (
    SELECT 1 FROM ca_medicos LIMIT 1
);

SELECT '✅ Médicos de ejemplo insertados (si la tabla estaba vacía)' as mensaje;

-- ============================================
-- VERIFICACIÓN FINAL
-- ============================================

SELECT 
    '✅ MIGRACIÓN COMPLETADA EXITOSAMENTE' as resultado,
    CONCAT(
        'Tablas actualizadas: ',
        (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'cldelriolaborato_c_alaska' AND table_name LIKE 'ca_%'),
        ' tablas con prefijo ca_'
    ) as info;

SELECT 
    'ca_usuarios' as tabla,
    COUNT(*) as registros,
    '✅ Tabla existente mantenida' as estado
FROM ca_usuarios
UNION ALL
SELECT 
    'ca_mascotas' as tabla,
    COUNT(*) as registros,
    '✅ Tabla actualizada con vacunas_al_dia' as estado
FROM ca_mascotas
UNION ALL
SELECT 
    'ca_medicos' as tabla,
    COUNT(*) as registros,
    '🆕 Tabla nueva creada' as estado
FROM ca_medicos
UNION ALL
SELECT 
    'ca_citas' as tabla,
    COUNT(*) as registros,
    '🆕 Tabla nueva creada' as estado
FROM ca_citas;

-- ============================================
-- FIN DE LA MIGRACIÓN
-- ============================================

