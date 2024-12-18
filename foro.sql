-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 03-12-2024 a las 14:03:42
-- Versión del servidor: 8.3.0
-- Versión de PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `foro`
--
CREATE DATABASE IF NOT EXISTS `foro` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `foro`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `amigo`
--

DROP TABLE IF EXISTS `amigo`;
CREATE TABLE IF NOT EXISTS `amigo` (
  `id_amigo` int NOT NULL AUTO_INCREMENT,
  `emisor` int DEFAULT NULL,
  `receptor` int DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_amigo`),
  KEY `emisor_fk_idx` (`emisor`),
  KEY `receptor_fk_idx` (`receptor`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `amigo`
--

INSERT INTO `amigo` (`id_amigo`, `emisor`, `receptor`, `estado`) VALUES
(1, 2, 3, 'amigo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensaje`
--

DROP TABLE IF EXISTS `mensaje`;
CREATE TABLE IF NOT EXISTS `mensaje` (
  `id_mensaje` int NOT NULL AUTO_INCREMENT,
  `emisor` int DEFAULT NULL,
  `receptor` int DEFAULT NULL,
  `mensaje_chat` varchar(250) DEFAULT NULL,
  `fecha_chat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mensaje`),
  KEY `usuario_emisor_fk_idx` (`emisor`),
  KEY `usuario_receptor_fk_idx` (`receptor`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pregunta`
--

DROP TABLE IF EXISTS `pregunta`;
CREATE TABLE IF NOT EXISTS `pregunta` (
  `id_pregunta` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text,
  `id_usuario` int DEFAULT NULL,
  `fecha_pregunta` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pregunta`),
  KEY `id_usuario2_fk_idx` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `pregunta`
--

INSERT INTO `pregunta` (`id_pregunta`, `titulo`, `descripcion`, `id_usuario`, `fecha_pregunta`) VALUES
(1, '¿Cómo instalar MySQL?', 'Necesito ayuda para instalar MySQL en Windows.', 2, '2024-12-03 07:39:44'),
(2, 'Problema con claves foráneas', 'Estoy recibiendo errores al crear claves foráneas.', 3, '2024-12-03 07:39:44'),
(3, '¿Qué es una base de datos relacional?', 'Busco una explicación sencilla sobre bases de datos relacionales.', 2, '2024-12-03 07:39:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta`
--

DROP TABLE IF EXISTS `respuesta`;
CREATE TABLE IF NOT EXISTS `respuesta` (
  `id_respuesta` int NOT NULL AUTO_INCREMENT,
  `descripcion` text,
  `id_pregunta` int DEFAULT NULL,
  `id_usuario` int DEFAULT NULL,
  `fecha_respuesta` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_respuesta`),
  KEY `id_pregunta_fk` (`id_pregunta`),
  KEY `id_usuario_fk_idx` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

DROP TABLE IF EXISTS `rol`;
CREATE TABLE IF NOT EXISTS `rol` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) NOT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `Nombre`) VALUES
(1, 'Administrador'),
(2, 'Usuario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `apellidos` varchar(255) DEFAULT NULL,
  `correo` varchar(255) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `id_rol` int DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  KEY `id_rol_fk_idx` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `username`, `nombre`, `apellidos`, `correo`, `contraseña`, `id_rol`) VALUES
(1, 'Marcolo', 'Marc', 'Colomé Cuenca', 'marcolo@gmail.com', '$2y$10$bczUxPJ75JyGwrT6YSAsre241bzCO0FnZqoEnFygxtmEJuzhtdFnq', 1),
(2, 'Polmonro', 'PolMarc', 'Montero Roca', 'polmarc@gmail.com', '$2y$10$bczUxPJ75JyGwrT6YSAsre241bzCO0FnZqoEnFygxtmEJuzhtdFnq', 2),
(3, 'ElPepe', 'Pepe', 'Ete Sech', 'elpepeetesech@gmail.com', '$2y$10$bczUxPJ75JyGwrT6YSAsre241bzCO0FnZqoEnFygxtmEJuzhtdFnq', 2);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `amigo`
--
ALTER TABLE `amigo`
  ADD CONSTRAINT `emisor_fk` FOREIGN KEY (`emisor`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `receptor_fk` FOREIGN KEY (`receptor`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `mensaje`
--
ALTER TABLE `mensaje`
  ADD CONSTRAINT `usuario_emisor_fk` FOREIGN KEY (`emisor`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `usuario_receptor_fk` FOREIGN KEY (`receptor`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `pregunta`
--
ALTER TABLE `pregunta`
  ADD CONSTRAINT `id_usuario2_fk` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `respuesta`
--
ALTER TABLE `respuesta`
  ADD CONSTRAINT `id_pregunta_fk` FOREIGN KEY (`id_pregunta`) REFERENCES `pregunta` (`id_pregunta`),
  ADD CONSTRAINT `id_usuario_fk` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `id_rol_fk` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
