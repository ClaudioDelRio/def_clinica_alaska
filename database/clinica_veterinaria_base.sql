-- ============================================
-- BASE DE DATOS CLÍNICA VETERINARIA ALASKA PETS CENTER
-- Versión Base - Solo Usuarios y Mascotas
-- ============================================

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS clinica_veterinaria 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE clinica_veterinaria;

-- ============================================
-- TABLA DE USUARIOS
-- ============================================

CREATE TABLE IF NOT EXISTS ca_usuarios (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre completo del usuario',
    email VARCHAR(150) NOT NULL UNIQUE COMMENT 'Correo electrónico - usado para login',
    telefono VARCHAR(20) NOT NULL COMMENT 'Teléfono de contacto formato chileno',
    direccion VARCHAR(200) NOT NULL COMMENT 'Dirección completa del usuario',
    password VARCHAR(255) NOT NULL COMMENT 'Contraseña encriptada con bcrypt',
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de registro del usuario',
    ultimo_acceso DATETIME NULL COMMENT 'Última vez que inició sesión',
    activo TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
    INDEX idx_email (email),
    INDEX idx_activo (activo),
    INDEX idx_fecha_registro (fecha_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de usuarios registrados en la plataforma';

-- ============================================
-- TABLA DE MASCOTAS
-- Relación: 1 Usuario puede tener MUCHAS Mascotas (1:N)
-- ============================================

CREATE TABLE IF NOT EXISTS ca_mascotas (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT(11) UNSIGNED NOT NULL COMMENT 'ID del dueño de la mascota',
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre de la mascota',
    especie ENUM('perro', 'gato', 'otro') NOT NULL DEFAULT 'perro' COMMENT 'Tipo de mascota',
    raza VARCHAR(100) NULL COMMENT 'Raza de la mascota (opcional)',
    edad INT(3) NULL COMMENT 'Edad en años (opcional)',
    peso DECIMAL(5,2) NULL COMMENT 'Peso en kilogramos (opcional)',
    sexo ENUM('macho', 'hembra') NULL COMMENT 'Sexo de la mascota (opcional)',
    color VARCHAR(50) NULL COMMENT 'Color predominante (opcional)',
    observaciones TEXT NULL COMMENT 'Notas adicionales, alergias, condiciones especiales',
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de registro de la mascota',
    activo TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo (fallecido o dado de baja)',
    
    -- Relación con usuarios
    FOREIGN KEY (usuario_id) REFERENCES ca_usuarios(id) ON DELETE CASCADE,
    
    -- Índices para optimizar consultas
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_especie (especie),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de mascotas - Relación 1:N con ca_usuarios (un usuario puede tener muchas mascotas)';


SELECT '✅ Base de datos creada exitosamente!' as mensaje;
SELECT 'Tablas: ca_usuarios (con dirección) y ca_mascotas (relación 1:N)' as info;
SELECT 'Las tablas están vacías y listas para usar' as estado;

