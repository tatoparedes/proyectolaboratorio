-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-08-2025 a las 06:39:21
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `laboratorio_db`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_login_usuario` (IN `pDNI` VARCHAR(8), IN `pPassword` VARCHAR(255))   BEGIN
    SELECT * FROM usuario
    WHERE cDNI = pDNI;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_usuario` (IN `pDNI` VARCHAR(8), IN `pNombres` VARCHAR(100), IN `pApePaterno` VARCHAR(50), IN `pApeMaterno` VARCHAR(50), IN `pCorreo` VARCHAR(100), IN `pContrasena` VARCHAR(255), IN `pRol` INT)   BEGIN
    INSERT INTO usuario (
        cDNI,
        cNombres,
        cApePaterno,
        cApeMaterno,
        cCorreo,
        cContrasena,
        nRol
    ) VALUES (
        pDNI,
        pNombres,
        pApePaterno,
        pApeMaterno,
        pCorreo,
        pContrasena,
        pRol
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_verificar_rol` (IN `_dni` VARCHAR(8))   BEGIN
    SELECT 
        nUsuario,
        cNombres,
        nRol,
        cDNI,
        cCorreo
    FROM usuario
    WHERE cDNI = _dni;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificacion`
--

CREATE TABLE `calificacion` (
  `nCalificacion` int(11) NOT NULL,
  `cCalificacion` decimal(4,2) DEFAULT NULL,
  `nExamen` int(11) NOT NULL,
  `nUsuario` int(11) NOT NULL,
  `fechaRegistro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calificacion`
--

INSERT INTO `calificacion` (`nCalificacion`, `cCalificacion`, `nExamen`, `nUsuario`, `fechaRegistro`) VALUES
(1, NULL, 1, 3, '2025-08-16 19:33:06'),
(2, NULL, 3, 3, '2025-08-16 19:38:30'),
(3, NULL, 4, 3, '2025-08-16 19:51:20'),
(4, NULL, 6, 3, '2025-08-16 20:10:20'),
(5, NULL, 9, 3, '2025-08-16 22:21:15'),
(6, NULL, 9, 3, '2025-08-16 22:57:54'),
(7, NULL, 9, 3, '2025-08-16 23:37:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especie`
--

CREATE TABLE `especie` (
  `nEspecie` int(11) NOT NULL,
  `cEspecie` varchar(100) NOT NULL,
  `nGenero` int(11) NOT NULL,
  `nUsuario` int(11) NOT NULL,
  `dtFechaRegistro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `especie`
--

INSERT INTO `especie` (`nEspecie`, `cEspecie`, `nGenero`, `nUsuario`, `dtFechaRegistro`) VALUES
(1, 'alumno', 2, 6, '2025-08-16 15:45:02'),
(2, 'estudiante', 1, 6, '2025-08-16 15:45:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `examen`
--

CREATE TABLE `examen` (
  `nExamen` int(11) NOT NULL,
  `cExamen` varchar(100) NOT NULL,
  `cCodigoExamen` char(6) NOT NULL,
  `nUsuario` int(11) NOT NULL,
  `fechaRegistro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `examen`
--

INSERT INTO `examen` (`nExamen`, `cExamen`, `cCodigoExamen`, `nUsuario`, `fechaRegistro`) VALUES
(1, 'examenprueba', '070889', 6, '2025-08-16 21:09:47'),
(3, 'examen2', '083635', 6, '2025-08-16 21:25:21'),
(4, 'examen5', '658924', 6, '2025-08-16 21:34:39'),
(5, 'gato', '081161', 6, '2025-08-16 21:44:08'),
(6, 'examenconelprofe', '877509', 6, '2025-08-17 01:09:10'),
(7, 'tumama', '464528', 6, '2025-08-17 01:34:41'),
(8, 'dali', '564737', 6, '2025-08-17 01:49:08'),
(9, 'canchancho para ingreso U', '491946', 6, '2025-08-17 03:19:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `familia`
--

CREATE TABLE `familia` (
  `nFamilia` int(11) NOT NULL,
  `cFamilia` varchar(100) NOT NULL,
  `nUsuario` int(11) NOT NULL,
  `dtFechaRegistro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `familia`
--

INSERT INTO `familia` (`nFamilia`, `cFamilia`, `nUsuario`, `dtFechaRegistro`) VALUES
(1, 'esnayder', 6, '2025-08-16 15:43:17'),
(2, 'cristian', 6, '2025-08-16 15:43:22'),
(3, 'humberto', 6, '2025-08-16 20:29:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `genero`
--

CREATE TABLE `genero` (
  `nGenero` int(11) NOT NULL,
  `cGenero` varchar(100) NOT NULL,
  `nFamilia` int(11) NOT NULL,
  `nUsuario` int(11) NOT NULL,
  `dtFechaRegistro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `genero`
--

INSERT INTO `genero` (`nGenero`, `cGenero`, `nFamilia`, `nUsuario`, `dtFechaRegistro`) VALUES
(1, 'hombre', 1, 6, '2025-08-16 15:43:39'),
(2, 'masculino', 2, 6, '2025-08-16 15:43:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pregunta`
--

CREATE TABLE `pregunta` (
  `nPregunta` int(11) NOT NULL,
  `cPregunta` text NOT NULL,
  `nPrueba` int(11) DEFAULT NULL,
  `nExamen` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pregunta`
--

INSERT INTO `pregunta` (`nPregunta`, `cPregunta`, `nPrueba`, `nExamen`) VALUES
(1, 'ajajajjajaj al fin csmr', 1, 1),
(2, 'jajajaj alfin ptmr', 2, 1),
(3, 'pregunta 1 jajaja', 1, 3),
(4, 'jajajasjsa', 1, 3),
(5, '1wefa', 1, 3),
(6, 'priemra pregunta', 1, 4),
(7, '123123', 1, 4),
(8, 'aesd', 1, 4),
(9, 'asdasdas', 1, 5),
(10, 'quien es ella?', 3, 6),
(11, 'que es esto?', 1, 6),
(12, 'wtf?', 2, 7),
(13, 'qqqq', 3, 8),
(14, 'quien este individuo', 4, 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prueba`
--

CREATE TABLE `prueba` (
  `nPrueba` int(11) NOT NULL,
  `nEspecie` int(11) NOT NULL,
  `cFoto` varchar(255) DEFAULT NULL,
  `cDescripcion` text DEFAULT NULL,
  `cResultado` text DEFAULT NULL,
  `cBacteria` varchar(100) DEFAULT NULL,
  `nUsuario` int(11) NOT NULL,
  `dtFechaRegistro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prueba`
--

INSERT INTO `prueba` (`nPrueba`, `nEspecie`, `cFoto`, `cDescripcion`, `cResultado`, `cBacteria`, `nUsuario`, `dtFechaRegistro`) VALUES
(1, 1, 'prueba_68a0ee27da71b2.67670887.jpeg', 'sisoi', 'verdad', 'hetero', 6, '2025-08-16 15:45:30'),
(2, 2, 'prueba_68a0ef39ae3fa0.55090430.jpeg', 'sies', 'verdero', 'homo', 6, '2025-08-16 15:51:05'),
(3, 1, 'prueba_68a0ffbe7144b6.92459021.jpeg', 'asdas', 'asdas', 'yaceli', 6, '2025-08-16 17:01:34'),
(4, 2, 'prueba_68a14a1011a8c3.15387786.jpeg', 'si es', 'verdadero', 'black esnayder', 6, '2025-08-16 22:18:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta`
--

CREATE TABLE `respuesta` (
  `nRespuesta` int(11) NOT NULL,
  `nPregunta` int(11) NOT NULL,
  `cRespuesta` text NOT NULL,
  `nCalificacion` int(11) DEFAULT NULL,
  `cComentario` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuesta`
--

INSERT INTO `respuesta` (`nRespuesta`, `nPregunta`, `cRespuesta`, `nCalificacion`, `cComentario`) VALUES
(1, 1, 'asda', 1, NULL),
(2, 2, 'asdas', 1, NULL),
(3, 3, 'asdaasd', 2, NULL),
(4, 4, 'asdasd', 2, NULL),
(5, 5, 'asdas', 2, NULL),
(6, 6, 'respuesta 1', 3, NULL),
(7, 7, 'respuetsa 2', 3, NULL),
(8, 8, 'respuesta 3', 3, NULL),
(9, 10, 'es mi ex', 4, NULL),
(10, 11, 'un escudo', 4, NULL),
(11, 14, 'Esnayder Josbeth Aguilar Canchachi', 5, NULL),
(12, 14, 'kinkleon', 6, NULL),
(13, 14, 'asdasda', 7, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `nRol` int(11) NOT NULL,
  `cRol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`nRol`, `cRol`) VALUES
(1, 'Alumno'),
(2, 'Docente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `nUsuario` int(11) NOT NULL,
  `cContrasena` varchar(255) NOT NULL,
  `nRol` int(11) DEFAULT NULL,
  `cDNI` char(8) NOT NULL,
  `cApePaterno` varchar(50) DEFAULT NULL,
  `cApeMaterno` varchar(50) DEFAULT NULL,
  `cNombres` varchar(100) DEFAULT NULL,
  `cCorreo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`nUsuario`, `cContrasena`, `nRol`, `cDNI`, `cApePaterno`, `cApeMaterno`, `cNombres`, `cCorreo`) VALUES
(3, '$2y$10$RfPZajyGj61aDRJQ4OwZ6.6KDx1.GWu416s8G.l.VR0yJmsE64EUK', 1, '46862829', 'MILLER', 'HERRERA', 'JESUS ALFONSO  ', 'articulosydis@gmail.com'),
(6, '$2y$10$NpT8n/4/SAxvDbSUm/SBFuJDXxVtJ9STRz/i.m3wsY72Y9Z8U9V8C', 2, '75109606', 'VASQUEZ', 'MILLER', 'CRISTIAN SEBASTIAN', 'cristiansvm17@gmail.com'),
(7, '$2y$10$xOTKZze.lTvZE.bPFN8naONkUwMOCZaWuxcB9O8JmGR2caLtdMGm2', 2, '74022913', 'AGUILAR', 'CANCHACHI', 'JHOSBETH ESNAYDER', 'aguilarcanchachij@iestptrujillo.net'),
(10, '$2y$10$J5S3Phw1GAUnlSCUpO/bJObfnWgESofRWHL5i9c0uEYS99IpQQLtG', 1, '75338379', 'RODRIGUEZ', 'HUAMÁN', 'ADA ABIGAIL', 'ada@gmail.com');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calificacion`
--
ALTER TABLE `calificacion`
  ADD PRIMARY KEY (`nCalificacion`),
  ADD KEY `nExamen` (`nExamen`),
  ADD KEY `nUsuario` (`nUsuario`);

--
-- Indices de la tabla `especie`
--
ALTER TABLE `especie`
  ADD PRIMARY KEY (`nEspecie`),
  ADD KEY `nGenero` (`nGenero`),
  ADD KEY `nUsuario` (`nUsuario`);

--
-- Indices de la tabla `examen`
--
ALTER TABLE `examen`
  ADD PRIMARY KEY (`nExamen`),
  ADD UNIQUE KEY `cCodigoExamen` (`cCodigoExamen`),
  ADD KEY `nUsuario` (`nUsuario`);

--
-- Indices de la tabla `familia`
--
ALTER TABLE `familia`
  ADD PRIMARY KEY (`nFamilia`),
  ADD KEY `nUsuario` (`nUsuario`);

--
-- Indices de la tabla `genero`
--
ALTER TABLE `genero`
  ADD PRIMARY KEY (`nGenero`),
  ADD KEY `nFamilia` (`nFamilia`),
  ADD KEY `nUsuario` (`nUsuario`);

--
-- Indices de la tabla `pregunta`
--
ALTER TABLE `pregunta`
  ADD PRIMARY KEY (`nPregunta`),
  ADD KEY `nExamen` (`nExamen`),
  ADD KEY `nPrueba` (`nPrueba`);

--
-- Indices de la tabla `prueba`
--
ALTER TABLE `prueba`
  ADD PRIMARY KEY (`nPrueba`),
  ADD KEY `nEspecie` (`nEspecie`),
  ADD KEY `nUsuario` (`nUsuario`);

--
-- Indices de la tabla `respuesta`
--
ALTER TABLE `respuesta`
  ADD PRIMARY KEY (`nRespuesta`),
  ADD KEY `nPregunta` (`nPregunta`),
  ADD KEY `nCalificacion` (`nCalificacion`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`nRol`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`nUsuario`),
  ADD KEY `nRol` (`nRol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calificacion`
--
ALTER TABLE `calificacion`
  MODIFY `nCalificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `especie`
--
ALTER TABLE `especie`
  MODIFY `nEspecie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `examen`
--
ALTER TABLE `examen`
  MODIFY `nExamen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `familia`
--
ALTER TABLE `familia`
  MODIFY `nFamilia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `genero`
--
ALTER TABLE `genero`
  MODIFY `nGenero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pregunta`
--
ALTER TABLE `pregunta`
  MODIFY `nPregunta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `prueba`
--
ALTER TABLE `prueba`
  MODIFY `nPrueba` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `respuesta`
--
ALTER TABLE `respuesta`
  MODIFY `nRespuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `nRol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `nUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calificacion`
--
ALTER TABLE `calificacion`
  ADD CONSTRAINT `calificacion_ibfk_1` FOREIGN KEY (`nExamen`) REFERENCES `examen` (`nExamen`),
  ADD CONSTRAINT `calificacion_ibfk_2` FOREIGN KEY (`nUsuario`) REFERENCES `usuario` (`nUsuario`);

--
-- Filtros para la tabla `especie`
--
ALTER TABLE `especie`
  ADD CONSTRAINT `especie_ibfk_1` FOREIGN KEY (`nGenero`) REFERENCES `genero` (`nGenero`),
  ADD CONSTRAINT `especie_ibfk_2` FOREIGN KEY (`nUsuario`) REFERENCES `usuario` (`nUsuario`);

--
-- Filtros para la tabla `examen`
--
ALTER TABLE `examen`
  ADD CONSTRAINT `examen_ibfk_1` FOREIGN KEY (`nUsuario`) REFERENCES `usuario` (`nUsuario`);

--
-- Filtros para la tabla `familia`
--
ALTER TABLE `familia`
  ADD CONSTRAINT `familia_ibfk_1` FOREIGN KEY (`nUsuario`) REFERENCES `usuario` (`nUsuario`);

--
-- Filtros para la tabla `genero`
--
ALTER TABLE `genero`
  ADD CONSTRAINT `genero_ibfk_1` FOREIGN KEY (`nFamilia`) REFERENCES `familia` (`nFamilia`),
  ADD CONSTRAINT `genero_ibfk_2` FOREIGN KEY (`nUsuario`) REFERENCES `usuario` (`nUsuario`);

--
-- Filtros para la tabla `pregunta`
--
ALTER TABLE `pregunta`
  ADD CONSTRAINT `pregunta_ibfk_1` FOREIGN KEY (`nExamen`) REFERENCES `examen` (`nExamen`),
  ADD CONSTRAINT `pregunta_ibfk_2` FOREIGN KEY (`nPrueba`) REFERENCES `prueba` (`nPrueba`);

--
-- Filtros para la tabla `prueba`
--
ALTER TABLE `prueba`
  ADD CONSTRAINT `prueba_ibfk_1` FOREIGN KEY (`nEspecie`) REFERENCES `especie` (`nEspecie`),
  ADD CONSTRAINT `prueba_ibfk_2` FOREIGN KEY (`nUsuario`) REFERENCES `usuario` (`nUsuario`);

--
-- Filtros para la tabla `respuesta`
--
ALTER TABLE `respuesta`
  ADD CONSTRAINT `respuesta_ibfk_1` FOREIGN KEY (`nPregunta`) REFERENCES `pregunta` (`nPregunta`),
  ADD CONSTRAINT `respuesta_ibfk_2` FOREIGN KEY (`nCalificacion`) REFERENCES `calificacion` (`nCalificacion`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`nRol`) REFERENCES `rol` (`nRol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
