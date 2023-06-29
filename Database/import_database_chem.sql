-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-06-2023 a las 14:56:46
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `chem_stoff`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chemicals`
--

CREATE TABLE `chemicals` (
  `ID_NEW` int(11) NOT NULL,
  `CHEMICAL_NAME` varchar(100) NOT NULL,
  `MANUFACTURER_ID` int(38) NOT NULL,
  `STORAGE_ID` int(38) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `chemicals`
--

INSERT INTO `chemicals` (`ID_NEW`, `CHEMICAL_NAME`, `MANUFACTURER_ID`, `STORAGE_ID`) VALUES
(1000, 'Chemical_1', 500, 100),
(1397, 'Chemical_2', 500, 100),
(1398, 'Chemical_3', 500, 100),
(1399, 'Chemical_4', 500, 100),
(1400, 'Chemical_5', 500, 100);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dangers`
--

CREATE TABLE `dangers` (
  `CHEMICAL_ID` int(11) NOT NULL,
  `FLAME` enum('nicht','ach','gef','enfällt') DEFAULT NULL,
  `FLAME_OVER_CIRCLE` enum('nicht','ach','gef','enfällt') DEFAULT NULL,
  `CORROSION` enum('nicht','ach','gef','enfällt') DEFAULT NULL,
  `GAS_CYLINDER` enum('nicht','ach','gef','enfällt') DEFAULT NULL,
  `SKULL_AND_CROSSBONES` enum('nicht','ach','gef','enfällt') DEFAULT NULL,
  `EXCLAMATION_MARK` enum('nicht','ach','gef','enfällt') DEFAULT NULL,
  `HEALTH_HAZARD` enum('nicht','ach','gef','enfällt') DEFAULT NULL,
  `ENVIRONMENT` enum('nicht','ach','gef','enfällt') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `dangers`
--

INSERT INTO `dangers` (`CHEMICAL_ID`, `FLAME`, `FLAME_OVER_CIRCLE`, `CORROSION`, `GAS_CYLINDER`, `SKULL_AND_CROSSBONES`, `EXCLAMATION_MARK`, `HEALTH_HAZARD`, `ENVIRONMENT`) VALUES
(1000, 'nicht', 'nicht', 'nicht', 'nicht', 'nicht', 'gef', 'gef', 'nicht'),
(1397, 'nicht', 'nicht', 'nicht', 'nicht', 'nicht', 'gef', 'gef', 'nicht'),
(1398, 'nicht', 'nicht', 'nicht', 'nicht', 'nicht', 'gef', 'gef', 'nicht'),
(1399, 'nicht', 'nicht', 'nicht', 'nicht', 'nicht', 'gef', 'gef', 'nicht'),
(1400, 'nicht', 'nicht', 'nicht', 'nicht', 'nicht', 'gef', 'gef', 'nicht');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `internal_ids`
--

CREATE TABLE `internal_ids` (
  `ID_NEW` int(38) NOT NULL,
  `ID_OLD` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `internal_ids`
--

INSERT INTO `internal_ids` (`ID_NEW`, `ID_OLD`) VALUES
(1000, 'dummy_id-1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `manufacturer`
--

CREATE TABLE `manufacturer` (
  `MANUFACTURER_ID` int(11) NOT NULL,
  `MANUFACTURER_NAME` varchar(50) NOT NULL,
  `LINK` varchar(255) DEFAULT NULL,
  `LINK_2` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `manufacturer`
--

INSERT INTO `manufacturer` (`MANUFACTURER_ID`, `MANUFACTURER_NAME`, `LINK`, `LINK_2`) VALUES
(500, 'Manufacturer_1', NULL, NULL),
(527, 'Manufacturer_2', NULL, NULL),
(528, 'Manufacturer_3', NULL, NULL),
(529, 'Manufacturer_4', NULL, NULL),
(530, 'Manufacturer_5', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `Role_Name` varchar(50) NOT NULL,
  `Role_Id` int(11) NOT NULL,
  `Description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`Role_Name`, `Role_Id`, `Description`) VALUES
('Admin', 1, 'Delete modify and insert chemicals, manufacturers, storages and all fields related in the database'),
('NormalUser', 2, 'Simple access to the database in order to be able to make queries to find chemicals and all their information');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sdb`
--

CREATE TABLE `sdb` (
  `CHEMICAL_ID` int(38) NOT NULL,
  `DATE` date DEFAULT NULL,
  `LINK` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sdb`
--

INSERT INTO `sdb` (`CHEMICAL_ID`, `DATE`, `LINK`) VALUES
(1000, '2019-04-05', NULL),
(1397, '2019-04-05', NULL),
(1398, '2019-04-05', NULL),
(1399, '2019-04-05', NULL),
(1400, '2019-04-05', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `storage`
--

CREATE TABLE `storage` (
  `STORAGE_ID` int(11) NOT NULL,
  `STORAGE_NAME` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `storage`
--

INSERT INTO `storage` (`STORAGE_ID`, `STORAGE_NAME`) VALUES
(100, 'Storage_1'),
(153, 'Storage_2'),
(154, 'Storage_3'),
(155, 'Storage_4'),
(156, 'Storage_5');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `Username` varchar(255) NOT NULL,
  `Passwords` varchar(255) NOT NULL,
  `Id` int(11) NOT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Role` int(11) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`Username`, `Passwords`, `Id`, `Email`, `Role`) VALUES
('Admin', '$2y$10$ijaZCGo8ZqbwQA.ecezlaepomYbI9H2IoSdAW164mSF1eUIwrPT/u', 3, NULL, 1),
('Ariel', '$2y$10$cnpSjTNpy0vK742VJHidiuUYArKue0sdjI50BVMOwEGGUza/67rkq', 4, NULL, 2),
('Pol', '$2y$10$A2ccSuVanG2iDmpKZy1iu.fOhXVC14CUdaTpQeT9aYEDXn7s8oW5K', 5, NULL, 2),
('Edgar', '$2y$10$6rHoUm4/KfqBQiqAULITJuwTyf3/FY/rXtoJadwu.TWHLMBMd1986', 6, NULL, 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `chemicals`
--
ALTER TABLE `chemicals`
  ADD PRIMARY KEY (`ID_NEW`),
  ADD KEY `MANUFACTURER_ID` (`MANUFACTURER_ID`),
  ADD KEY `STORAGE_ID` (`STORAGE_ID`);

--
-- Indices de la tabla `dangers`
--
ALTER TABLE `dangers`
  ADD PRIMARY KEY (`CHEMICAL_ID`);

--
-- Indices de la tabla `internal_ids`
--
ALTER TABLE `internal_ids`
  ADD PRIMARY KEY (`ID_NEW`);

--
-- Indices de la tabla `manufacturer`
--
ALTER TABLE `manufacturer`
  ADD PRIMARY KEY (`MANUFACTURER_ID`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`Role_Id`);

--
-- Indices de la tabla `sdb`
--
ALTER TABLE `sdb`
  ADD PRIMARY KEY (`CHEMICAL_ID`);

--
-- Indices de la tabla `storage`
--
ALTER TABLE `storage`
  ADD PRIMARY KEY (`STORAGE_ID`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD KEY `Role` (`Role`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `chemicals`
--
ALTER TABLE `chemicals`
  MODIFY `ID_NEW` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1401;

--
-- AUTO_INCREMENT de la tabla `dangers`
--
ALTER TABLE `dangers`
  MODIFY `CHEMICAL_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1401;

--
-- AUTO_INCREMENT de la tabla `manufacturer`
--
ALTER TABLE `manufacturer`
  MODIFY `MANUFACTURER_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=531;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `Role_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `storage`
--
ALTER TABLE `storage`
  MODIFY `STORAGE_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `chemicals`
--
ALTER TABLE `chemicals`
  ADD CONSTRAINT `chemicals_ibfk_1` FOREIGN KEY (`MANUFACTURER_ID`) REFERENCES `manufacturer` (`MANUFACTURER_ID`),
  ADD CONSTRAINT `chemicals_ibfk_2` FOREIGN KEY (`STORAGE_ID`) REFERENCES `storage` (`STORAGE_ID`);

--
-- Filtros para la tabla `dangers`
--
ALTER TABLE `dangers`
  ADD CONSTRAINT `dangers_ibfk_1` FOREIGN KEY (`CHEMICAL_ID`) REFERENCES `chemicals` (`ID_NEW`);

--
-- Filtros para la tabla `internal_ids`
--
ALTER TABLE `internal_ids`
  ADD CONSTRAINT `internal_ids_ibfk_1` FOREIGN KEY (`ID_NEW`) REFERENCES `chemicals` (`ID_NEW`);

--
-- Filtros para la tabla `sdb`
--
ALTER TABLE `sdb`
  ADD CONSTRAINT `sdb_ibfk_1` FOREIGN KEY (`CHEMICAL_ID`) REFERENCES `chemicals` (`ID_NEW`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`Role`) REFERENCES `roles` (`Role_Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
