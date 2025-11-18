-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-11-2025 a las 00:36:48
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inventory_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(4, 'Aros'),
(2, 'Llantas'),
(1, 'Motores'),
(5, 'Repuestos varios'),
(3, 'Tanques');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `tipo` enum('Entrada','Salida') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos`
--

INSERT INTO `movimientos` (`id`, `producto_id`, `tipo`, `cantidad`, `fecha`, `observacion`) VALUES
(1, 1, 'Entrada', 50, '2025-10-27 00:00:01', ''),
(2, 1, 'Entrada', 20, '2025-10-27 00:36:52', 'COMPRA MOTORES'),
(3, 1, 'Salida', 78, '2025-10-27 00:46:02', ''),
(4, 1, 'Entrada', 15, '2025-10-27 00:53:29', ''),
(5, 2, 'Salida', 1, '2025-10-27 01:00:20', ''),
(6, 2, 'Entrada', 1, '2025-10-27 01:06:16', ''),
(7, 3, 'Entrada', 20, '2025-10-27 01:33:22', ''),
(8, 2, 'Entrada', 16, '2025-10-27 01:33:32', ''),
(9, 4, 'Entrada', 15, '2025-10-27 01:36:23', ''),
(10, 2, 'Salida', 10, '2025-10-27 01:55:15', 'COMPRA MOTORES'),
(11, 6, 'Entrada', 14, '2025-10-27 02:03:44', ''),
(12, 4, 'Salida', 5, '2025-10-27 06:37:28', ''),
(13, 7, 'Entrada', 20, '2025-10-27 06:39:52', ''),
(14, 7, 'Salida', 5, '2025-10-27 06:40:10', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `foto` varchar(255) DEFAULT 'uploads/default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `categoria`, `precio`, `stock`, `foto`) VALUES
(1, 'MOTOR LIFAN 150', 'Motores', 1200.00, 15, 'uploads/1761522002_descarga (1).jpg'),
(2, 'MOTOR RONCO 150', 'Motores', 1250.00, 7, 'uploads/1761525862_Ronco_Classic_150S_Galgo_Per___Carrusel_2.jpg'),
(3, 'MOTOR SUMO 150', 'Motores', 1300.00, 20, 'uploads/1761528785_sumo-1.png'),
(4, 'LLANTA 500 X 12', 'Llantas', 100.00, 10, 'uploads/1761528966_LLANTA12X500CENTRAO_700x700.webp'),
(5, 'MOTOR WANXIN 150', 'Motores', 1200.00, 0, 'uploads/1761530552_images.jpg'),
(6, 'ARO 500 X 12', 'Aros', 60.00, 14, 'uploads/1761530606_descarga (2).jpg'),
(7, 'TANQUE GL AZUL', 'Tanques', 160.00, 15, 'uploads/1761547157_3058_20250211085650.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `rol` enum('admin','empleado') DEFAULT 'empleado',
  `nombre_completo` varchar(150) DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'uploads/default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `clave`, `rol`, `nombre_completo`, `foto`) VALUES
(1, 'admin', '827ccb0eea8a706c4c34a16891f84e7b', 'admin', 'Pedro Jesús Andrés Santiago', 'uploads/default.png'),
(3, 'Chato', '827ccb0eea8a706c4c34a16891f84e7b', 'empleado', 'Pedro Sebastian Andres Espinoza', 'uploads/default.png');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
