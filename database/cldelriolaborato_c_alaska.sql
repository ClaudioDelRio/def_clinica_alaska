-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 31-10-2025 a las 02:59:52
-- Versión del servidor: 10.11.14-MariaDB
-- Versión de PHP: 8.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cldelriolaborato_c_alaska`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ca_citas`
--

CREATE TABLE `ca_citas` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED NOT NULL COMMENT 'ID del usuario que reserva',
  `mascota_id` int(11) UNSIGNED NOT NULL COMMENT 'ID de la mascota para la cita',
  `doctor_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Médico preferido (opcional, puede ser NULL)',
  `servicio` enum('consulta','vacunacion','cirugia','radiologia','laboratorio','peluqueria','emergencia') NOT NULL COMMENT 'Tipo de servicio solicitado',
  `fecha_cita` date NOT NULL COMMENT 'Fecha de la cita',
  `hora_cita` time NOT NULL COMMENT 'Hora de la cita',
  `motivo` text NOT NULL COMMENT 'Motivo de la consulta',
  `estado` enum('pendiente','confirmada','completada','cancelada') NOT NULL DEFAULT 'pendiente' COMMENT 'Estado de la cita',
  `observaciones` text DEFAULT NULL COMMENT 'Observaciones del médico o la clínica',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de creación de la reserva',
  `fecha_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Última actualización'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de citas y reservas de horas';

--
-- Volcado de datos para la tabla `ca_citas`
--

INSERT INTO `ca_citas` (`id`, `usuario_id`, `mascota_id`, `doctor_id`, `servicio`, `fecha_cita`, `hora_cita`, `motivo`, `estado`, `observaciones`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 2, 1, NULL, 'vacunacion', '2025-10-30', '10:00:00', 'mi paciente tiene dolor de guata', 'pendiente', NULL, '2025-10-29 17:51:23', NULL),
(2, 2, 1, NULL, 'consulta', '2025-10-30', '10:30:00', 'hola comomaoamoaa', 'pendiente', NULL, '2025-10-29 18:42:24', NULL),
(4, 2, 4, 1, 'consulta', '2025-10-31', '11:30:00', 'ppepepeeefff', 'pendiente', NULL, '2025-10-30 15:00:47', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ca_mascotas`
--

CREATE TABLE `ca_mascotas` (
  `id` int(11) UNSIGNED NOT NULL,
  `usuario_id` int(11) UNSIGNED NOT NULL COMMENT 'ID del dueño de la mascota',
  `nombre` varchar(100) NOT NULL COMMENT 'Nombre de la mascota',
  `especie` enum('perro','gato','otro') NOT NULL DEFAULT 'perro' COMMENT 'Tipo de mascota',
  `raza` varchar(100) DEFAULT NULL COMMENT 'Raza de la mascota (opcional)',
  `edad` int(3) DEFAULT NULL COMMENT 'Edad en años (opcional)',
  `peso` decimal(5,2) DEFAULT NULL COMMENT 'Peso en kilogramos (opcional)',
  `sexo` enum('macho','hembra') DEFAULT NULL COMMENT 'Sexo de la mascota (opcional)',
  `color` varchar(50) DEFAULT NULL COMMENT 'Color predominante (opcional)',
  `observaciones` text DEFAULT NULL COMMENT 'Notas adicionales, alergias, condiciones especiales',
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de registro de la mascota',
  `activo` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo (fallecido o dado de baja)',
  `vacunas_al_dia` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica si las vacunas están al día (1=Sí, 0=No)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de mascotas - Relación 1:N con usuarios (un usuario puede tener muchas mascotas)';

--
-- Volcado de datos para la tabla `ca_mascotas`
--

INSERT INTO `ca_mascotas` (`id`, `usuario_id`, `nombre`, `especie`, `raza`, `edad`, `peso`, `sexo`, `color`, `observaciones`, `fecha_registro`, `activo`, `vacunas_al_dia`) VALUES
(1, 2, 'Mulan', 'perro', 'Teckel', 1, 9.70, 'hembra', 'Cafe', NULL, '2025-10-29 17:32:31', 1, 1),
(4, 2, 'Scard', 'perro', 'Labrador', 5, 25.00, 'macho', 'Negro', NULL, '2025-10-30 15:00:23', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ca_medicos`
--

CREATE TABLE `ca_medicos` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL COMMENT 'Nombre completo del médico veterinario',
  `especialidad` varchar(100) DEFAULT NULL COMMENT 'Especialidad del médico (opcional)',
  `telefono` varchar(20) DEFAULT NULL COMMENT 'Teléfono de contacto',
  `email` varchar(100) DEFAULT NULL COMMENT 'Email del médico',
  `es_admin` tinyint(1) NOT NULL DEFAULT 0,
  `password_hash` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de registro en el sistema'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de médicos veterinarios de la clínica';

--
-- Volcado de datos para la tabla `ca_medicos`
--

INSERT INTO `ca_medicos` (`id`, `nombre`, `especialidad`, `telefono`, `email`, `es_admin`, `password_hash`, `activo`, `fecha_registro`) VALUES
(1, 'Dr. Claudio del Rio Malgarini', 'Cirugía', '+56 9 9365 1250', 'osorno@clinicaalaska.cl', 1, NULL, 1, '2025-10-29 17:02:13'),
(2, 'Dr. Daniel Nuñez Peña', 'Medicina Interna', '+56 9 5244 7853', 'osorno@clinicaalaska.cl', 0, NULL, 1, '2025-10-29 17:02:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ca_usuarios`
--

CREATE TABLE `ca_usuarios` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL COMMENT 'Nombre completo del usuario',
  `email` varchar(150) NOT NULL COMMENT 'Correo electrónico - usado para login',
  `rut` varchar(12) NOT NULL COMMENT 'RUT del usuario (formato: 12.345.678-9)',
  `telefono` varchar(20) NOT NULL COMMENT 'Teléfono de contacto formato chileno',
  `direccion` varchar(200) NOT NULL COMMENT 'Dirección completa del usuario',
  `password` varchar(255) NOT NULL COMMENT 'Contraseña encriptada con bcrypt',
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de registro del usuario',
  `ultimo_acceso` datetime DEFAULT NULL COMMENT 'Última vez que inició sesión',
  `activo` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de usuarios registrados en la plataforma';

--
-- Volcado de datos para la tabla `ca_usuarios`
--

INSERT INTO `ca_usuarios` (`id`, `nombre`, `email`, `rut`, `telefono`, `direccion`, `password`, `fecha_registro`, `ultimo_acceso`, `activo`) VALUES
(2, 'Claudio del Rio Malgarini', 'cadrm00@gmail.com', '12.870.251-2', '993651250', 'Alcalde Saturnino Barril 1380 Osorno', '$2y$10$0E52Bm9zdvf7NTIZ4PYW0udbiqgPdCYFTxn3QFx7dR.rRegmG7WuK', '2025-10-29 03:25:26', '2025-10-31 01:40:46', 1),
(3, 'Daniel Nuñez Peña', 'daniel@nunez.cl', '13.338.495-2', '+56993651250', 'Las Aralias 1560', '$2y$10$LVqpxgmKMI0.0zXvnJ1ZO./FKVGFxalR/g5iaBdnL2Ii/pPHK2VsC', '2025-10-29 23:51:30', NULL, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ca_citas`
--
ALTER TABLE `ca_citas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_mascota` (`mascota_id`),
  ADD KEY `idx_doctor` (`doctor_id`),
  ADD KEY `idx_fecha` (`fecha_cita`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha_hora` (`fecha_cita`,`hora_cita`);

--
-- Indices de la tabla `ca_mascotas`
--
ALTER TABLE `ca_mascotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_especie` (`especie`),
  ADD KEY `idx_activo` (`activo`);

--
-- Indices de la tabla `ca_medicos`
--
ALTER TABLE `ca_medicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- Indices de la tabla `ca_usuarios`
--
ALTER TABLE `ca_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_rut` (`rut`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_activo` (`activo`),
  ADD KEY `idx_fecha_registro` (`fecha_registro`),
  ADD KEY `idx_rut` (`rut`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ca_citas`
--
ALTER TABLE `ca_citas`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `ca_mascotas`
--
ALTER TABLE `ca_mascotas`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ca_medicos`
--
ALTER TABLE `ca_medicos`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ca_usuarios`
--
ALTER TABLE `ca_usuarios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ca_citas`
--
ALTER TABLE `ca_citas`
  ADD CONSTRAINT `ca_citas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `ca_usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ca_citas_ibfk_2` FOREIGN KEY (`mascota_id`) REFERENCES `ca_mascotas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ca_citas_ibfk_3` FOREIGN KEY (`doctor_id`) REFERENCES `ca_medicos` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `ca_mascotas`
--
ALTER TABLE `ca_mascotas`
  ADD CONSTRAINT `ca_mascotas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `ca_usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
