-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-11-2025 a las 20:15:57
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
-- Base de datos: `crm`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `email` varchar(75) NOT NULL,
  `tlf` int(15) NOT NULL,
  `empresa` varchar(100) NOT NULL,
  `fecha_registro` date NOT NULL DEFAULT current_timestamp(),
  `usuario_responsable` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nombre_completo`, `email`, `tlf`, `empresa`, `fecha_registro`, `usuario_responsable`) VALUES
(9, 'Simó', 'simocli@gmail.com', 123456789, 'Riot Games', '2025-11-18', 1),
(10, 'Paco', 'paco@gmail.com', 123456789, 'Microsoft', '2025-11-18', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oportunidad`
--

CREATE TABLE `oportunidad` (
  `id_oportunidad` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  `valor_estimado` double(10,2) NOT NULL,
  `estado` enum('progreso','ganada','perdida') NOT NULL DEFAULT 'progreso',
  `f_creacion` date NOT NULL DEFAULT current_timestamp(),
  `usuario_responsable` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `oportunidad`
--

INSERT INTO `oportunidad` (`id_oportunidad`, `id_cliente`, `titulo`, `descripcion`, `valor_estimado`, `estado`, `f_creacion`, `usuario_responsable`) VALUES
(10, 9, 'ERP - Java', 'asd', 90.00, 'perdida', '2025-11-18', 1),
(11, 9, 'Venta de paquete de software ERP a Logística Andina S.A.', 'asdasd', 50.00, 'progreso', '2025-11-18', 1),
(12, 9, '123', '123', 123.00, 'progreso', '2025-11-18', 1),
(13, 10, 'Katarina', 'skin', 50.00, 'progreso', '2025-11-18', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `id_tarea` int(11) NOT NULL,
  `id_oportunidad` int(11) NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  `fecha` date NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','completada') NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tareas`
--

INSERT INTO `tareas` (`id_tarea`, `id_oportunidad`, `descripcion`, `fecha`, `estado`) VALUES
(1, 13, 'Llamada al cliente', '2025-11-18', 'completada'),
(2, 13, 'Segunda llamada', '2025-11-18', 'pendiente'),
(3, 10, 'Primera llamada', '2025-11-18', 'completada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `email` varchar(75) NOT NULL,
  `contra` varchar(100) NOT NULL,
  `role` enum('admin','vendedor') NOT NULL DEFAULT 'vendedor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre_completo`, `email`, `contra`, `role`) VALUES
(1, 'asd', 'a@gmail.com', '$2y$10$2.BxkkJRLHct3rfSx2ewy.98kGn1DRLJnsXPHPELiRmW1LMXj2Mza', 'vendedor'),
(2, 'Marc Hunter', 'b@gmail.com', '$2y$10$SrwSZyA.C/kZ4MZvyokhCu0mK3aPJS3iaEDx9y5IwoDY5kglH337.', 'vendedor'),
(3, 'Liqiang Yang', 'admin@gmail.com', '$2y$10$UzRI2kluTfWffRRyDw6kS.swoePOSGSEW.Bj4LpYmmPMMnkIrrWLK', 'admin'),
(7, 'jordi', 'jordi@gmail.com', '$2y$10$76gKDwfOLINBKBYtlzWk0.GsBydffQKokmGQZiOKsc2pmV7pxU8ly', 'vendedor'),
(8, 'jordiasd', 'asdas@gmail.com', '$2y$10$s/lp5093ugAu06VT2iTJaOtDaR2862GjWmFaRP/rlj8..2ptOX2P6', 'admin');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `usuario_responsable` (`usuario_responsable`) USING BTREE;

--
-- Indices de la tabla `oportunidad`
--
ALTER TABLE `oportunidad`
  ADD PRIMARY KEY (`id_oportunidad`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `usuario_responsable` (`usuario_responsable`);

--
-- Indices de la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD PRIMARY KEY (`id_tarea`),
  ADD KEY `id_oportunidad` (`id_oportunidad`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `oportunidad`
--
ALTER TABLE `oportunidad`
  MODIFY `id_oportunidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `id_tarea` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`usuario_responsable`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `oportunidad`
--
ALTER TABLE `oportunidad`
  ADD CONSTRAINT `oportunidad_ibfk_1` FOREIGN KEY (`usuario_responsable`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `oportunidad_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD CONSTRAINT `tareas_ibfk_1` FOREIGN KEY (`id_oportunidad`) REFERENCES `oportunidad` (`id_oportunidad`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
