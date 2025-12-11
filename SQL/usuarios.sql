-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 11-12-2025 a las 17:04:57
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
-- Base de datos: `cruphp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tipo` enum('Admin','Secretaria') NOT NULL,
  `imagen` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `password`, `tipo`, `imagen`) VALUES
(0, 'admin', 'admin@gmail.com', '$2y$10$iPt/kbmOwvhP6P9R.cIFEeooT4PU5ip1kxTmyXfOnTzH9OesbOO2i', 'Admin', 'default.jpg'),
(0, 'admin', 'admin@gmail.com', '$2y$10$iPt/kbmOwvhP6P9R.cIFEeooT4PU5ip1kxTmyXfOnTzH9OesbOO2i', 'Admin', 'default.jpg'),
(0, 'secre', 'secre@gmail.com', '$2y$10$bZhGOyEZZ9Xnckk0qA.PgOGeqkOKkvHZX9hamX061YA1xhXsiuErO', 'Secretaria', 'default.jpg'),
(0, '', '', '', '', 'default.jpg'),
(0, 'secre', 'secre@gmail.com', '$2y$10$bZhGOyEZZ9Xnckk0qA.PgOGeqkOKkvHZX9hamX061YA1xhXsiuErO', 'Secretaria', 'default.jpg');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
