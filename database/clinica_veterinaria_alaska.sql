-- ============================================
-- BASE DE DATOS: CLÍNICA VETERINARIA ALASKA
-- Desarrollado por: Claudio del Rio - Web.malgarini®
-- ============================================

-- Seleccionar la base de datos
USE cldelriolaborato_c_alaska;

-- ============================================
-- TABLA: ca_usuarios
-- ============================================
CREATE TABLE IF NOT EXISTS ca_usuarios (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    direccion VARCHAR(200) DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME DEFAULT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY email (email),
    INDEX idx_email (email),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: ca_mascotas
-- ============================================
CREATE TABLE IF NOT EXISTS ca_mascotas (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    usuario_id INT(11) UNSIGNED NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    especie ENUM('perro', 'gato', 'otro') NOT NULL,
    raza VARCHAR(50) DEFAULT NULL,
    edad INT(2) UNSIGNED DEFAULT 0,
    sexo ENUM('macho', 'hembra') NOT NULL,
    peso DECIMAL(5,2) DEFAULT NULL COMMENT 'Peso en kilogramos',
    color VARCHAR(50) DEFAULT NULL,
    vacunas_al_dia TINYINT(1) NOT NULL DEFAULT 0,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (usuario_id) REFERENCES ca_usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_especie (especie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: ca_citas
-- ============================================
CREATE TABLE IF NOT EXISTS ca_citas (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    usuario_id INT(11) UNSIGNED NOT NULL,
    mascota_id INT(11) UNSIGNED NOT NULL,
    doctor_id INT(11) UNSIGNED DEFAULT NULL COMMENT 'Médico preferido (puede ser NULL)',
    servicio ENUM('consulta', 'vacunacion', 'cirugia', 'radiologia', 'laboratorio', 'peluqueria', 'emergencia') NOT NULL,
    fecha_cita DATE NOT NULL,
    hora_cita TIME NOT NULL,
    motivo TEXT NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'completada', 'cancelada') NOT NULL DEFAULT 'pendiente',
    observaciones TEXT DEFAULT NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: ca_medicos (opcional para futuro)
-- ============================================
CREATE TABLE IF NOT EXISTS ca_medicos (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    especialidad VARCHAR(100) DEFAULT NULL,
    telefono VARCHAR(20) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATOS DE EJEMPLO (OPCIONAL)
-- ============================================

-- Insertar un usuario de prueba (contraseña: 123456)
INSERT INTO ca_usuarios (nombre, email, telefono, direccion, password) VALUES
('Juan Pérez González', 'juan.perez@email.com', '+56912345678', 'Los Aromos 1234, Osorno', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('María González', 'maria.gonzalez@email.com', '+56987654321', 'Av. Principal 567, Osorno', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insertar mascotas de ejemplo (ajustar usuario_id según corresponda)
INSERT INTO ca_mascotas (usuario_id, nombre, especie, raza, edad, sexo, peso, color, vacunas_al_dia) VALUES
(1, 'Max', 'perro', 'Golden Retriever', 4, 'macho', 28.5, 'Dorado', 1),
(1, 'Luna', 'gato', 'Gato Persa', 2, 'hembra', 4.2, 'Blanco', 1),
(1, 'Rocky', 'perro', 'Bulldog Francés', 1, 'macho', 12.0, 'Atigrado', 0);

-- Insertar citas de ejemplo
INSERT INTO ca_citas (usuario_id, mascota_id, doctor_id, servicio, fecha_cita, hora_cita, motivo, estado) VALUES
(1, 1, 1, 'consulta', '2024-10-15', '10:00:00', 'Revisión general y chequeo anual', 'completada'),
(1, 2, 2, 'vacunacion', '2024-11-20', '15:30:00', 'Vacuna antirrábica anual', 'pendiente'),
(1, 3, 1, 'radiologia', '2024-10-05', '16:00:00', 'Radiografía de cadera por cojera', 'completada'),
(1, 1, NULL, 'peluqueria', '2024-09-28', '11:00:00', 'Baño y corte de pelo', 'completada'),
(1, 2, 2, 'consulta', '2024-09-12', '10:30:00', 'Revisión por falta de apetito', 'cancelada');

-- Insertar médicos de ejemplo
INSERT INTO ca_medicos (nombre, especialidad, telefono, email) VALUES
('Dr. Carlos Muñoz', 'Cirugía General', '+56912111111', 'carlos.munoz@clinicaalaska.cl'),
('Dra. María González', 'Medicina Interna', '+56912222222', 'maria.gonzalez@clinicaalaska.cl'),
('Dr. Pedro Soto', 'Radiología', '+56912333333', 'pedro.soto@clinicaalaska.cl');

-- ============================================
-- FIN DEL SCRIPT
-- ============================================

