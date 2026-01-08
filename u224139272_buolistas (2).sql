-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 07-01-2026 a las 20:25:18
-- Versión del servidor: 11.8.3-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u224139272_buolistas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id_alumno` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `id_grupo` int(11) DEFAULT NULL,
  `id_carrera` int(11) NOT NULL,
  `status` enum('activo','baja') NOT NULL DEFAULT 'activo',
  `motivo_baja` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id_alumno`, `nombre`, `id_grupo`, `id_carrera`, `status`, `motivo_baja`) VALUES
(1, 'Caballero Luján Sol', 1, 1, 'activo', NULL),
(2, 'Campos Silva Luis Antonio', 1, 1, 'activo', NULL),
(3, 'Días Ríos Geymmy Lawmy', 1, 1, 'activo', NULL),
(4, 'García Lázaro Iván Alexander', 1, 1, 'activo', NULL),
(5, 'García Lázaro José Manuel', 1, 1, 'activo', NULL),
(6, 'García Matías Luis Antonio', 1, 1, 'activo', NULL),
(7, 'García Méndez Idali Adriana', 1, 1, 'activo', NULL),
(8, 'Gualberto Herrera Zurisadai', 1, 1, 'activo', NULL),
(9, 'Guerrero Carrasco Emilio De Jesús', 1, 1, 'activo', NULL),
(10, 'Hernández Soriano Caleb', 1, 1, 'activo', NULL),
(11, 'López López Daniel Gilberto', 1, 1, 'activo', NULL),
(12, 'Miguel Ortega Sandra Nidia', 1, 1, 'activo', NULL),
(13, 'Osorio Ramírez Karol Sinaí', 1, 1, 'activo', NULL),
(14, 'Pérez Guzmán Karen Linette', 1, 1, 'activo', NULL),
(15, 'Ramos Sumano Stefany Alin', 1, 1, 'activo', NULL),
(16, 'Rodríguez Altamirano Uriel', 1, 1, 'activo', NULL),
(17, 'Sánchez Martínez Karla Denisse', 1, 1, 'activo', NULL),
(18, 'Solís Velásquez Ytzel Yamilet', 1, 1, 'activo', NULL),
(19, 'Velásquez Cortes Ashley Getsemaní', 1, 1, 'activo', NULL),
(20, 'Lucas Matadamas Vanesa', NULL, 1, 'baja', 'cambio de escuela'),
(22, 'prueba alumno medicicna grupo A', 2, 3, 'activo', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carga_academica`
--

CREATE TABLE `carga_academica` (
  `id_carga` int(11) NOT NULL,
  `id_carrera` int(11) NOT NULL,
  `clave_oficio` varchar(50) DEFAULT NULL,
  `texto_presentacion` text NOT NULL,
  `ciclo_escolar` varchar(20) DEFAULT NULL,
  `claustro_texto` varchar(150) NOT NULL,
  `texto_pie` text NOT NULL,
  `nombre_director` varchar(150) NOT NULL,
  `cargo_director` varchar(200) NOT NULL,
  `archivo_firma` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carreras`
--

CREATE TABLE `carreras` (
  `id_carrera` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `clave` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `carreras`
--

INSERT INTO `carreras` (`id_carrera`, `nombre`, `clave`) VALUES
(1, 'Derecho', 'DER2025'),
(2, 'Teología', 'TEO2025'),
(3, 'Medicina', 'MED2025'),
(4, 'Enfermería', 'ENF2025'),
(5, 'Psicología', 'PSI2025');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `docentes`
--

CREATE TABLE `docentes` (
  `id_docente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `id_carrera` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `docentes`
--

INSERT INTO `docentes` (`id_docente`, `nombre`, `apellidos`, `correo`, `telefono`, `id_carrera`) VALUES
(1, 'Mtro.Isaías', 'Hernández Santiago', 'aa@gmail.com', '951111111', 1),
(2, 'Adrian', 'Quiroga Avendaño', '', '', 1),
(3, 'Maria del Carmen', 'Avendaño Rito', '', '', 1),
(4, 'MTRO Carlos Alberto', 'Moreno Alcántara', 'buo@local.com', '', 1),
(5, 'José Antonio', 'Álvarez Hernández', '', '', 1),
(6, 'Coral del Carmen', 'Ordaz Fuentes', '', '', 1),
(7, 'Paula', 'Cruz Carrasco', '', '', 1),
(8, 'Jesus Alberto', 'Cervantes Ramirez', '', '', 1),
(9, 'Saulo', 'Chávez Alvarado', '', '', 1),
(10, 'Ricardo', 'Hernández Aguilar', '', '', 1),
(11, 'José de Jesús', 'Jijón Santiago', '', '', 1),
(12, 'Jair', 'Silva Hernández', '', '', 1),
(13, 'Agustin', 'Hernandez Vargues', '', '', 1),
(14, 'Antonio', 'Alceda Cruz', '', '', 1),
(15, 'Mateo', 'Martinez Martinez', '', '', 1),
(16, 'Mauricio', 'Gijón Cernas', '', '', 1),
(17, 'Juan Pablo', 'Vasconcelos Méndez', '', '', 1),
(18, 'Jarumy Esmeralda', 'Méndez Reyes', '', '', 1),
(19, 'Victor Hugo', 'Cortes', '', '', 1),
(20, 'Milka del Valle', 'Pérez', '', '', 1),
(21, 'Celso Alberto', 'Robles Villatoro', '', '', 1),
(22, 'Everardo Jaime', 'Casas López', '', '', 1),
(23, 'Luis Armando', 'Yudico Colin', '', '', 1),
(24, 'Nancy', 'Flemming Tello', '', '', 1),
(25, 'Eduardo Ezequiel', 'Martínez Gutiérrez', '', '', 1),
(26, 'Elizabeth', 'Bautista Velasco', '', '', 1),
(27, 'Adán', 'Córdova Trujillo', '', '', 1),
(28, 'Porfirio de Jesús', 'Santiago Santaella', '', '', 1),
(29, 'Gaudel', 'Reyes Lemus', '', '', 1),
(30, 'León Isaac', 'Hernández Luna', '', '', 1),
(31, 'Rolando', 'Ruíz Reyes', '', '', 1),
(32, 'Viridiana', 'Reyes Ricárdez', '', '', 1),
(33, 'Antonio Magno', 'González', '', '', 1),
(34, 'Jessica Maribel', 'Arango Bravo', '', '', 1),
(35, 'Edgar Said', 'Ruíz Lopez', '', '', 1),
(36, 'Carlos', 'Morales Sánchez', '', '', 1),
(37, 'Loretta', '', '', '', 1),
(38, 'Dr. Ulises ', 'Pérez Carrera', 'buodocente@gmail.com', '', 3),
(39, 'Dr. Alejandro ', 'Vásquez Hernández', 'buodocente@gmail.com', '', 3),
(40, 'Dr. Irving ', 'Escobar Vasquez', 'buodocente@gmail.com', '', 3),
(41, 'Dra. Natallie ', 'Martínez Sosa', 'buodocente@gmail.com', '', 3),
(42, 'Dr. Ivan Antonio ', 'López', 'buodocente@gmail.com', '', 3),
(43, 'Dr. Guillermo ', 'Ochoa Mota', 'buodocente@gmail.com', '', 3),
(44, 'Dr. ', 'Arreola', 'buodocente@gmail.com', '', 3),
(45, 'Dra. Patricia ', 'Zerón García', 'buodocente@gmail.com', '', 3),
(46, 'Dr. Christian Joshua ', 'Diaz Hernández', 'buodocente@gmail.com', '', 3),
(47, 'Dra. Leticia ', 'Rosales', 'buodocente@gmail.com', '', 3),
(48, 'Dr. Oscar ', 'Cuevas Cruz', 'buodocente@gmail.com', '', 3),
(52, 'Dr. Carlos Mario', 'Peregrino Santos', 'buodocente@gmail.com', '', 3),
(54, 'Dra. Gabriela', ' ', 'buodocente@gmail.com', '', 3),
(55, 'Dr. Abelardo Augusto ', 'Ramirez Davila', 'buodocente@gmail.com', '', 3),
(58, 'Dr. Vicente ', 'Julian', 'buodocente@gmail.com', '', 3),
(59, 'Dra. Alejandra ', 'Mendez Ruiz', 'buodocente@gmail.com', '', 3),
(60, 'Dr. Juan de Dios ', 'Hernández Hidalgo', 'buodocente@gmail.com', '', 3),
(65, 'Dr. Jose Antonio ', 'Pérez Casas', 'buodocente@gmail.com', '', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id_grupo` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `id_semestre` int(11) NOT NULL,
  `id_carrera` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`id_grupo`, `nombre`, `id_semestre`, `id_carrera`) VALUES
(1, '1ER SEMESTRE', 1, 1),
(2, '8° A', 8, 3),
(3, '8° B', 8, 3),
(4, '8° C', 8, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id_horario` int(11) NOT NULL,
  `id_docente` int(11) NOT NULL,
  `id_carrera` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `id_grupo` int(11) NOT NULL,
  `horario_texto` varchar(200) DEFAULT NULL,
  `horarioscol` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`id_horario`, `id_docente`, `id_carrera`, `id_materia`, `id_grupo`, `horario_texto`, `horarioscol`) VALUES
(1, 4, 1, 9, 1, 'LUNES DE 11:00 A 11:50, MIERCOLES DE 13:00 a 13:50, viernes de 12:00 a 12:50', NULL),
(2, 1, 1, 3, 1, 'MARTES DE 11:00 A 11:50, MIERCOLES DE 09:00 a 10:50, JUEVES de 11:00 a 12:50', NULL),
(3, 4, 1, 5, 1, 'MARTES DE 12:00 A 13:50, MIERCOLES DE 12:00 a 12:50', NULL),
(4, 4, 1, 7, 1, 'martes DE 08:00 A 08:50, viernes de 07:00 a 08:50', NULL),
(5, 38, 3, 75, 2, 'LUNES DE 07:00 A 08:00, MIÉRCOLES DE 07:00 A 08:00, VIERNES DE 07:00 A 08:00 HRS ', NULL),
(6, 45, 3, 76, 2, 'LUNES DE 08:00 A 09:00, MARTES DE 07:00 A 09:00', NULL),
(7, 40, 3, 77, 2, 'LUNES DE 09:00 A 10:00, MIÉRCOLES DE 09:00 A 10:00, JUEVES DE 09:00 A 10:00', NULL),
(8, 40, 3, 79, 2, 'LUNES DE 15:00 A 17:00', NULL),
(9, 42, 3, 78, 2, 'LUNES DE 10:00 A 12:00, MIÉRCOLES DE 12:00 A 13:00, VIERNES DE 11:00 A 13:00', NULL),
(10, 39, 3, 80, 2, 'MARTES DE 09:00 A 10:00, MIÉRCOLES DE 08:00 A 09:00, JUEVES DE 07:00 A 09:00, VIERNES DE 08:00 A 09:00', NULL),
(11, 43, 3, 81, 2, 'MARTES DE 10:00 A 12:00, JUEVES DE 10:00 A 11:00, VIERNES DE 09:00 A 10:00', NULL),
(12, 44, 3, 84, 2, 'MARTES DE 18:00 A 20:00,MIÉRCOLES DE 10:00 A 12:00', NULL),
(13, 46, 3, 82, 2, 'MARTES DE 13:00 A 15:00, MIÉRCOLES DE 14:00 A 15:00', NULL),
(14, 41, 3, 83, 2, 'MARTES DE 16:00 A 18:00, MIÉRCOLES DE 16:00 A 18:00', NULL),
(15, 47, 3, 85, 2, 'SABADO DE 13:00 A 16:00', NULL),
(16, 40, 3, 77, 3, 'LUNES DE 07:00 A 08:00, MIÉRCOLES DE 08:00 A 09:00, JUEVES DE 07:00 A 08:00, VIERNES DE 07:00 A 09:00', NULL),
(17, 40, 3, 77, 3, 'LUNES DE 07:00 A 08:00, MIÉRCOLES DE 08:00 A 09:00, JUEVES DE 07:00 A 08:00, VIERNES DE 07:00 A 09:00', NULL),
(18, 52, 3, 78, 3, 'LUNES DE 09:00 A 10:00,MARTES DE 08:00 A 9:00, MIÉRCOLES DE 09:00 A 10:00, JUEVES DE 08:00 A 09:00,VIERNES DE 09:00 A 10:00', NULL),
(19, 43, 3, 81, 3, 'LUNES DE 10:00 A 11:00, MIÉRCOLES DE 10:00 A 11:00, VIERNES DE 10:00 A 12:00', NULL),
(20, 39, 3, 80, 3, 'LUNES DE 11:00 A 12:00, MARTES DE 10:00 A 12:00, JUEVES DE 09:00 A 11:00', NULL),
(21, 46, 3, 82, 3, 'LUNES DE 13:00 A 15:00, MARTES DE 15:00 A 16:00', NULL),
(22, 48, 3, 75, 3, 'LUNES DE 16:00 A 17:00, MARTES DE 16:00 A 18:00', NULL),
(23, 54, 3, 84, 3, 'JUEVES DE 16:00 A 18:00, VIERNES DE 16:00 A 18:00', NULL),
(24, 41, 3, 83, 3, 'JUEVES DE 18:00 A 19:00, VIERNES DE 18:00 A 19:00', NULL),
(25, 55, 3, 76, 3, 'MARTES DE 14:00 A 15:00, VIERNES DE 14:00 A 16:00', NULL),
(26, 47, 3, 85, 3, 'SÁBADO DE 13:00 A 16:00', NULL),
(27, 45, 3, 76, 4, 'LUNES DE 07:00 A 08:00, JUEVES DE 07:00 A 08:00, VIERNES DE 07:00 A 08:00', NULL),
(28, 60, 3, 77, 4, 'LUNES DE 08:00 A 09:00, MARTES DE 07:00 A 09:00, MIÉRCOLES DE 07:00 A 08:00, JUEVES DE 08:00 A 09:00', NULL),
(29, 43, 3, 81, 4, 'LUNES DE 11:00 A 12:00, MIÉRCOLES DE 08:00 A 09:00, VIERNES DE 08:00 A 09:00, MIÉRCOLES DE 11:00 A 12:00', NULL),
(30, 59, 3, 80, 4, 'LUNES DE 09:00 A 11:00, MARTES DE 09:00 A 10:00, JUEVES DE 09:00 A 11:00', NULL),
(31, 42, 3, 78, 4, 'MIÉRCOLES DE 09:00 A 11:00, VIERNES DE 09:00 A 11:00', NULL),
(32, 44, 3, 84, 4, 'MARTES DE 10:00 A 12:00, JUEVES DE 18:00 A 20:00', NULL),
(33, 65, 3, 82, 4, 'JUEVES DE 11:00 A 13:00, VIERNES DE 11:00 A 12:00', NULL),
(34, 58, 3, 75, 4, 'SÁBADO DE 13:00 A 16:00', NULL),
(35, 41, 3, 83, 4, 'JUEVES DE 16:00 A 18:00, VIERNES DE 16:00 A 18:00', NULL),
(36, 47, 3, 85, 4, 'JUEVES DE 20:00 A 21:00, VIERNES DE 18:00 A 20:00', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horario_dias`
--

CREATE TABLE `horario_dias` (
  `id_dia` int(11) NOT NULL,
  `id_horario` int(11) NOT NULL,
  `dia` enum('L','M','X','J','V') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `horario_dias`
--

INSERT INTO `horario_dias` (`id_dia`, `id_horario`, `dia`) VALUES
(1, 1, 'L'),
(2, 1, 'X'),
(3, 1, 'V'),
(7, 3, 'M'),
(8, 3, 'X'),
(11, 5, 'L'),
(12, 5, 'X'),
(13, 5, 'V'),
(16, 7, 'L'),
(17, 7, 'X'),
(18, 7, 'J'),
(19, 8, 'L'),
(20, 9, 'L'),
(21, 9, 'X'),
(22, 9, 'V'),
(27, 11, 'M'),
(28, 11, 'J'),
(29, 11, 'V'),
(30, 12, 'M'),
(31, 12, 'X'),
(37, 6, 'L'),
(38, 6, 'M'),
(39, 10, 'M'),
(40, 10, 'X'),
(41, 10, 'J'),
(42, 10, 'V'),
(43, 14, 'M'),
(44, 14, 'X'),
(46, 13, 'M'),
(47, 13, 'X'),
(48, 16, 'L'),
(49, 16, 'X'),
(50, 16, 'J'),
(51, 16, 'V'),
(52, 17, 'L'),
(53, 17, 'X'),
(54, 17, 'J'),
(55, 17, 'V'),
(68, 24, 'J'),
(69, 24, 'V'),
(73, 25, 'M'),
(74, 25, 'V'),
(75, 22, 'L'),
(76, 22, 'M'),
(77, 19, 'L'),
(78, 19, 'X'),
(79, 19, 'V'),
(80, 18, 'L'),
(81, 18, 'M'),
(82, 18, 'X'),
(83, 18, 'J'),
(84, 18, 'V'),
(85, 23, 'J'),
(86, 23, 'V'),
(90, 20, 'L'),
(91, 20, 'M'),
(92, 20, 'J'),
(93, 21, 'L'),
(94, 21, 'M'),
(110, 32, 'M'),
(111, 32, 'J'),
(114, 35, 'J'),
(115, 35, 'V'),
(116, 36, 'J'),
(117, 36, 'V'),
(118, 27, 'L'),
(119, 27, 'J'),
(120, 27, 'V'),
(121, 29, 'L'),
(122, 29, 'X'),
(123, 29, 'V'),
(124, 31, 'X'),
(125, 31, 'V'),
(126, 30, 'L'),
(127, 30, 'M'),
(128, 30, 'J'),
(129, 33, 'J'),
(130, 33, 'V'),
(131, 28, 'L'),
(132, 28, 'M'),
(133, 28, 'X'),
(134, 28, 'J'),
(135, 26, ''),
(143, 15, ''),
(144, 4, 'M'),
(145, 4, 'V'),
(149, 2, 'M'),
(150, 2, 'X'),
(151, 2, 'J');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id_materia` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `clave` varchar(50) DEFAULT NULL,
  `horas_semana` int(11) DEFAULT NULL,
  `horas_semestre` int(11) DEFAULT NULL,
  `id_carrera` int(11) NOT NULL,
  `id_semestre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id_materia`, `nombre`, `clave`, `horas_semana`, `horas_semestre`, `id_carrera`, `id_semestre`) VALUES
(2, 'Instituciones del Derecho Romano I', 'BLD01', 4, 72, 1, 1),
(3, 'Acto Jurídico, Derecho de las Personas y Familia', 'BLD02', 5, 80, 1, 1),
(4, 'Metodología para la Investigación y Redacción Jurídica', 'BLD03', 3, 54, 1, 1),
(5, 'Teorías del Estado', 'BLD04', 3, 54, 1, 1),
(6, 'Teorías del Derecho', 'BLD05', 3, 54, 1, 1),
(7, 'Sociología Jurídica', 'BLD06', 3, 54, 1, 1),
(8, 'Teoría Económica', 'BLD07', 3, 54, 1, 1),
(9, 'Cultura General del Abogado', 'BLD08', 3, 54, 1, 1),
(10, 'Instituciones del Derecho Romano II', 'BLD09', 4, 72, 1, 2),
(11, 'Derecho Constitucional', 'BLD10', 5, 80, 1, 2),
(12, 'Patrimonio y Derechos Reales', 'BLD11', 4, 72, 1, 2),
(13, 'Teoría del Delito', 'BLD12', 5, 80, 1, 2),
(14, 'Teoría General del Proceso', 'BLD13', 5, 80, 1, 2),
(15, 'Derechos Humanos', 'BLD14', 3, 54, 1, 2),
(16, 'Ideas e Instituciones Políticas', 'BLD15', 3, 54, 1, 2),
(17, 'Lógica y Argumentación Jurídica', 'BLD16', 4, 72, 1, 2),
(18, 'Filosofía del Derecho', 'BLD17', 3, 54, 1, 2),
(19, 'Taller de Oratoria II', 'BLDT2', 2, 36, 1, 2),
(20, 'Seminario de Escritura y Redacción', 'BLDS1', 2, 36, 1, 2),
(21, 'Derecho Hereditario', 'BLD18', 5, 80, 1, 3),
(22, 'Comercio y Sociedades Mercantiles', 'BLD19', 4, 72, 1, 3),
(23, 'Sistemas Jurídicos Contemporáneos', 'BLD20', 3, 54, 1, 3),
(24, 'Delitos en Particular', 'BLD21', 4, 72, 1, 3),
(25, 'Derecho Administrativo I', 'BLD22', 4, 72, 1, 3),
(26, 'Mecanismos de Protección de los Derechos Humanos', 'BLD23', 4, 72, 1, 3),
(27, 'Derecho Agrario', 'BLD24', 3, 54, 1, 3),
(28, 'Antropología Jurídica', 'BLD25', 2, 36, 1, 3),
(29, 'Cultura General del Abogado II', 'BLD26', 3, 54, 1, 3),
(30, 'Taller de Oratoria III', 'BLDT3', 2, 36, 1, 3),
(31, 'Derecho de las Obligaciones', 'BLD18', 5, 80, 1, 4),
(32, 'Contratos Mercantiles', 'BLD28', 4, 72, 1, 4),
(33, 'Derecho Informático', 'BLD29', 3, 54, 1, 4),
(34, 'Delitos Especiales', 'BLD30', 4, 72, 1, 4),
(35, 'Derecho Administrativo II', 'BLD31', 4, 72, 1, 4),
(36, 'Derecho Internacional Público y Privado', 'BLD32', 4, 72, 1, 4),
(37, 'Derecho Municipal', 'BLD33', 4, 72, 1, 4),
(38, 'Derecho Individual de Trabajo', 'BLD34', 4, 72, 1, 4),
(39, 'Cultura General del Abogado III', 'BLD35', 3, 54, 1, 4),
(40, 'Derecho Contractual Civil', 'BLD36', 5, 80, 1, 5),
(41, 'Títulos y Operaciones de Crédito', 'BLD37', 4, 72, 1, 5),
(42, 'Derecho Procesal Penal Acusatorio I', 'BLD38', 5, 80, 1, 5),
(43, 'Derecho Ambiental', 'BLD39', 3, 54, 1, 5),
(44, 'Derecho Fiscal I', 'BLD40', 4, 72, 1, 5),
(45, 'Tratados Internacionales', 'BLD41', 3, 54, 1, 5),
(46, 'Derecho Notarial y Correduría Pública', 'BLD42', 3, 54, 1, 5),
(47, 'Derecho Colectivo del Trabajo', 'BLD43', 4, 72, 1, 5),
(48, 'Cultura General del Abogado IV', 'BLD44', 3, 54, 1, 5),
(49, 'Derecho Bancario Bursátil', 'BLD45', 3, 54, 1, 6),
(50, 'Derecho Procesal Mercantil', 'BLD46', 4, 72, 1, 6),
(51, 'Procesi Penal Acusatorio II', 'BLD47', 5, 72, 1, 6),
(52, 'Técnicas de Litigación y Debate Jurídico', 'BLD48', 4, 80, 1, 6),
(53, 'Derecho Fiscal II', 'BLD49', 4, 72, 1, 6),
(54, 'Amparo y Medios de Control Constitucional', 'BLD50', 5, 80, 1, 6),
(55, 'Derecho Procesal Civil', 'BLD51', 4, 72, 1, 6),
(56, 'Derecho Procesal Laboral', 'BLD52', 4, 72, 1, 6),
(57, 'Deontología y Ética Jurídica', 'BLD53', 3, 54, 1, 6),
(58, 'Juicios Orales en Materia Mercantil', 'BLD54', 4, 72, 1, 7),
(59, 'Juicios Orales en Materia Civil y Familiar', 'BLD55', 4, 72, 1, 7),
(60, 'Práctica del Derecho Fiscal y Administrativo', 'BLD56', 4, 72, 1, 7),
(61, 'Práctica Forense del Juicio Constitucional de Amparo', 'BLD57', 5, 80, 1, 7),
(62, 'Medios Alternos de Solución de Conflictos', 'BLD58', 4, 72, 1, 7),
(63, 'Derecho Indígena', 'BLD59', 3, 54, 1, 7),
(64, 'Derecho de Seguridad Social', 'BLD60', 4, 72, 1, 7),
(65, 'Retórica y Oratoria Forense', 'BLD61', 4, 72, 1, 7),
(66, 'Seminario de Tesis I', 'BLD62', 3, 54, 1, 7),
(67, 'Criminalística y Medicina Forense', 'BLD63', 4, 72, 1, 8),
(68, 'Mecanismos de Combate a la Corrupción', 'BLD64', 4, 72, 1, 8),
(69, 'Estudio de Casos', 'BLD65', 4, 72, 1, 8),
(70, 'Derecho Parlamentario Mexicano', 'BLD66', 4, 54, 1, 8),
(71, 'Derecho Electoral y Sistemas Normativos Indígenas', 'BLD67', 4, 72, 1, 8),
(72, 'Fiscalización Superior', 'BLD68', 3, 54, 1, 8),
(73, 'Desarrollo de Negocios', 'BLD69', 3, 54, 1, 8),
(74, 'Seminario de Tesis II', 'BLD70', 4, 72, 1, 8),
(75, 'MEDICINA CRITICA', 'BLMC52', 3, 54, 3, 8),
(76, 'ADMINISTRACIÓN DE CLINICAS Y EMPRESAS', 'BLMC66', 3, 54, 3, 8),
(77, 'UROLOGÍA', 'BLMC60', 5, 90, 3, 8),
(78, 'NEUROLOGÍA', 'BLMC62', 5, 90, 3, 8),
(79, 'UROLOGÍA (PRACTICA)', 'MD26', 2, 20, 3, 8),
(80, 'PEDIATRÍA II', 'BLMC59', 5, 90, 3, 8),
(81, 'NEFROLOGÍA', 'BLMC63', 4, 72, 3, 8),
(82, 'SEMINARIO DE DESARROLLO DE EMPRESAS MEDICAS', 'BLMC67', 3, 54, 3, 8),
(83, 'HEMATOLOGÍA', 'BLMC61', 4, 72, 3, 8),
(84, 'ONCOLOGÍA', 'BLMC64', 4, 72, 3, 8),
(85, 'OTORRINOLARINGOLOGÍA', 'BLLMC54', 3, 54, 3, 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parciales`
--

CREATE TABLE `parciales` (
  `id_parcial` int(11) NOT NULL,
  `id_carrera` int(11) NOT NULL,
  `numero_parcial` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `parciales`
--

INSERT INTO `parciales` (`id_parcial`, `id_carrera`, `numero_parcial`, `fecha_inicio`, `fecha_fin`) VALUES
(1, 1, 1, '2025-08-18', '2025-09-12'),
(2, 1, 2, '2025-09-15', '2025-10-18'),
(3, 1, 3, '2025-10-20', '2025-11-22'),
(4, 3, 1, '2026-01-05', '2026-02-13'),
(5, 3, 2, '2026-02-16', '2026-03-27'),
(6, 3, 3, '2026-03-30', '2026-05-01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `semestres`
--

CREATE TABLE `semestres` (
  `id_semestre` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `id_carrera` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `semestres`
--

INSERT INTO `semestres` (`id_semestre`, `numero`, `id_carrera`) VALUES
(1, 1, NULL),
(2, 2, NULL),
(3, 3, NULL),
(4, 4, NULL),
(5, 5, NULL),
(6, 6, NULL),
(7, 7, NULL),
(8, 8, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones_clase`
--

CREATE TABLE `sesiones_clase` (
  `id_sesion` int(11) NOT NULL,
  `id_horario` int(11) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','coordinador') NOT NULL,
  `id_carrera` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `usuario`, `password`, `rol`, `id_carrera`, `estado`) VALUES
(1, 'Administrador General', 'admin1@local', 'admin', '$2y$10$EfMDmRL1vPO8L7KeLA.wmu4iSxWex/AfNP3hayjjgwx2CqsQcHffm', 'admin', 1, 1),
(2, 'Coordinador Derecho', 'coord_derecho@local', 'coord_derecho', '$2y$10$gRjyp5yMp3sDuHFmYHO/8ONdEXKK0LMCVuXwLNFYraXu3XRHe5ISi', 'coordinador', 1, 1),
(3, 'Coordinador Enfermería', 'coord_enfermeria@local', 'coord_enfermeria', '$2y$10$jYd78jV6P1yH.HKEBgwRNu/YlFF.EUUSvajBsPxrk0SerTGPB2P1W', 'coordinador', 4, 1),
(5, 'Coordinador Psicología', 'coord_psicologia@local', 'coord_psicologia', '$2y$10$0ucFvSfOelaF3hIOcluWV.9I4Y0arDet818El3Qi/H/bEu02hTzsq', 'coordinador', 5, 1),
(6, 'Coordinador Medicina', 'coord_medicina@local', 'coord_medicina', '$2y$10$czCyhjvHeveJ.C/xgnWfAO1F12gCrhlApe7N.pyCuJwBjoMi8eRmm', 'coordinador', 3, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id_alumno`),
  ADD KEY `id_grupo` (`id_grupo`),
  ADD KEY `fk_alumno_carrera` (`id_carrera`);

--
-- Indices de la tabla `carga_academica`
--
ALTER TABLE `carga_academica`
  ADD PRIMARY KEY (`id_carga`),
  ADD KEY `fk_config_carrera` (`id_carrera`);

--
-- Indices de la tabla `carreras`
--
ALTER TABLE `carreras`
  ADD PRIMARY KEY (`id_carrera`);

--
-- Indices de la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD PRIMARY KEY (`id_docente`),
  ADD KEY `fk_docente_carrera` (`id_carrera`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id_grupo`),
  ADD KEY `id_semestre` (`id_semestre`),
  ADD KEY `fk_grupos_carreras` (`id_carrera`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `id_docente` (`id_docente`),
  ADD KEY `id_materia` (`id_materia`),
  ADD KEY `id_grupo` (`id_grupo`);

--
-- Indices de la tabla `horario_dias`
--
ALTER TABLE `horario_dias`
  ADD PRIMARY KEY (`id_dia`),
  ADD KEY `id_horario` (`id_horario`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id_materia`);

--
-- Indices de la tabla `parciales`
--
ALTER TABLE `parciales`
  ADD PRIMARY KEY (`id_parcial`),
  ADD KEY `fk_parcial_carrera` (`id_carrera`);

--
-- Indices de la tabla `semestres`
--
ALTER TABLE `semestres`
  ADD PRIMARY KEY (`id_semestre`),
  ADD KEY `fk_semestre_carrera` (`id_carrera`);

--
-- Indices de la tabla `sesiones_clase`
--
ALTER TABLE `sesiones_clase`
  ADD PRIMARY KEY (`id_sesion`),
  ADD KEY `id_horario` (`id_horario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `fk_usuario_carrera` (`id_carrera`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id_alumno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `carga_academica`
--
ALTER TABLE `carga_academica`
  MODIFY `id_carga` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `carreras`
--
ALTER TABLE `carreras`
  MODIFY `id_carrera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `docentes`
--
ALTER TABLE `docentes`
  MODIFY `id_docente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id_grupo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `horario_dias`
--
ALTER TABLE `horario_dias`
  MODIFY `id_dia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT de la tabla `parciales`
--
ALTER TABLE `parciales`
  MODIFY `id_parcial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `semestres`
--
ALTER TABLE `semestres`
  MODIFY `id_semestre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `sesiones_clase`
--
ALTER TABLE `sesiones_clase`
  MODIFY `id_sesion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carga_academica`
--
ALTER TABLE `carga_academica`
  ADD CONSTRAINT `fk_config_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `docentes`
--
ALTER TABLE `docentes`
  ADD CONSTRAINT `fk_docente_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`);

--
-- Filtros para la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `fk_grupos_carreras` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`),
  ADD CONSTRAINT `grupos_ibfk_1` FOREIGN KEY (`id_semestre`) REFERENCES `semestres` (`id_semestre`);

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`id_docente`) REFERENCES `docentes` (`id_docente`),
  ADD CONSTRAINT `horarios_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`),
  ADD CONSTRAINT `horarios_ibfk_3` FOREIGN KEY (`id_grupo`) REFERENCES `grupos` (`id_grupo`);

--
-- Filtros para la tabla `horario_dias`
--
ALTER TABLE `horario_dias`
  ADD CONSTRAINT `horario_dias_ibfk_1` FOREIGN KEY (`id_horario`) REFERENCES `horarios` (`id_horario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `parciales`
--
ALTER TABLE `parciales`
  ADD CONSTRAINT `fk_parcial_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`) ON DELETE CASCADE;

--
-- Filtros para la tabla `semestres`
--
ALTER TABLE `semestres`
  ADD CONSTRAINT `fk_semestre_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`) ON DELETE SET NULL;

--
-- Filtros para la tabla `sesiones_clase`
--
ALTER TABLE `sesiones_clase`
  ADD CONSTRAINT `sesiones_clase_ibfk_1` FOREIGN KEY (`id_horario`) REFERENCES `horarios` (`id_horario`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_carrera` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
