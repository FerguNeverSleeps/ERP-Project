-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-04-2016 a las 02:24:52
-- Versión del servidor: 10.1.9-MariaDB
-- Versión de PHP: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `planillaexpress_conf`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_host`
--

CREATE TABLE `config_host` (
  `servidor` varchar(75) NOT NULL,
  `usuario` varchar(25) NOT NULL,
  `password` varchar(25) NOT NULL,
  `database` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_install`
--

CREATE TABLE `config_install` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `etapa` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conf_ejecucion_reporte`
--

CREATE TABLE `conf_ejecucion_reporte` (
  `conf_erep_id` int(4) NOT NULL,
  `host` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `puerto` varchar(4) COLLATE utf8_spanish_ci NOT NULL,
  `app` varchar(10) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `conf_ejecucion_reporte`
--

INSERT INTO `conf_ejecucion_reporte` (`conf_erep_id`, `host`, `puerto`, `app`) VALUES
(1, 'localhost', '8080', 'express');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_empresa`
--

CREATE TABLE `datos_empresa` (
  `cod_datos_empresa` int(32) NOT NULL,
  `alias_opcion` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `cod_empresa` int(32) NOT NULL,
  `nombre_empresa` varchar(80) COLLATE utf8_spanish_ci NOT NULL,
  `direccion` varchar(400) COLLATE utf8_spanish_ci NOT NULL,
  `ciudad` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
  `estado` varchar(25) CHARACTER SET latin1 DEFAULT NULL,
  `zona_postal` varchar(12) CHARACTER SET latin1 DEFAULT NULL,
  `telefonos` varchar(100) CHARACTER SET latin1 NOT NULL,
  `rif` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `nit` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `rep_legal` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `img_izq` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT 'logo_selectra.jpg',
  `img_der` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `cod_moneda` varchar(50) CHARACTER SET latin1 NOT NULL,
  `nombre_sistema` varchar(50) CHARACTER SET latin1 NOT NULL,
  `pais_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento_usuario`
--

CREATE TABLE `evento_usuario` (
  `evento_usuario_id` int(11) NOT NULL,
  `evento` varchar(250) NOT NULL,
  `color` varchar(10) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `usuario` varchar(20) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomempresa`
--

CREATE TABLE `nomempresa` (
  `codigo` int(32) NOT NULL,
  `nombre` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `bd` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `bd_contabilidad` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `bd_nomina` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `admis_activo` tinyint(1) NOT NULL DEFAULT '0',
  `contab_activo` tinyint(1) NOT NULL DEFAULT '0',
  `nomina_activo` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomusuarios`
--

CREATE TABLE `nomusuarios` (
  `coduser` int(10) UNSIGNED NOT NULL,
  `descrip` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nivel` tinyint(4) DEFAULT NULL,
  `fecha` int(11) DEFAULT NULL,
  `clave` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `correo` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `acce_usuarios` int(1) DEFAULT NULL,
  `acce_configuracion` int(1) DEFAULT NULL,
  `acce_elegibles` int(1) DEFAULT NULL,
  `acce_personal` int(1) DEFAULT NULL,
  `acce_prestamos` int(1) DEFAULT NULL,
  `acce_consultas` int(1) DEFAULT NULL,
  `acce_transacciones` int(1) DEFAULT NULL,
  `acce_procesos` int(1) DEFAULT NULL,
  `acce_reportes` int(1) DEFAULT NULL,
  `acce_estuaca` int(1) DEFAULT NULL,
  `acce_xestuaca` int(1) DEFAULT NULL,
  `acce_permisos` int(1) DEFAULT NULL,
  `acce_logros` int(1) DEFAULT NULL,
  `acce_penalizacion` int(1) DEFAULT NULL,
  `acce_movpe` int(1) DEFAULT NULL,
  `acce_evalde` int(1) DEFAULT NULL,
  `acce_experiencia` int(1) DEFAULT NULL,
  `acce_antic` int(1) DEFAULT NULL,
  `acce_uniforme` int(1) DEFAULT NULL,
  `contadorvence` tinyint(4) DEFAULT NULL,
  `fecclave` int(11) DEFAULT NULL,
  `encript` tinyint(4) DEFAULT NULL,
  `pregunta` mediumtext COLLATE utf8_spanish_ci,
  `respuesta` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `acctwind` tinyint(4) DEFAULT NULL,
  `borraper` tinyint(4) DEFAULT NULL,
  `dfecha` datetime DEFAULT NULL,
  `dfecclave` datetime DEFAULT NULL,
  `login_usuario` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `acce_autorizar_nom` int(1) DEFAULT NULL,
  `acce_enviar_nom` int(1) DEFAULT NULL,
  `acce_generarordennomina` int(1) DEFAULT NULL,
  `acce_validar_constancias` int(1) DEFAULT NULL,
  `acce_editar_constancias` int(1) DEFAULT NULL,
  `img` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `acceso_sueldo` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nomusuarios`
--

INSERT INTO `nomusuarios` (`coduser`, `descrip`, `nivel`, `fecha`, `clave`, `correo`, `acce_usuarios`, `acce_configuracion`, `acce_elegibles`, `acce_personal`, `acce_prestamos`, `acce_consultas`, `acce_transacciones`, `acce_procesos`, `acce_reportes`, `acce_estuaca`, `acce_xestuaca`, `acce_permisos`, `acce_logros`, `acce_penalizacion`, `acce_movpe`, `acce_evalde`, `acce_experiencia`, `acce_antic`, `acce_uniforme`, `contadorvence`, `fecclave`, `encript`, `pregunta`, `respuesta`, `acctwind`, `borraper`, `dfecha`, `dfecclave`, `login_usuario`, `acce_autorizar_nom`, `acce_enviar_nom`, `acce_generarordennomina`, `acce_validar_constancias`, `acce_editar_constancias`, `img`, `acceso_sueldo`) VALUES
(1, 'Servicio Tecnico', NULL, NULL, '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 'admin@admin', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 1, 1, 1, NULL, NULL, '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomusuario_empresa`
--

CREATE TABLE `nomusuario_empresa` (
  `usuario_empresa_id` int(11) NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `acceso` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `nomusuario_empresa`
--

INSERT INTO `nomusuario_empresa` (`usuario_empresa_id`, `id_usuario`, `id_empresa`, `acceso`) VALUES
(1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomusuario_nomina`
--

CREATE TABLE `nomusuario_nomina` (
  `usuario_nomina_id` int(11) NOT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `id_nomina` int(11) NOT NULL,
  `acceso` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `nomusuario_nomina`
--

INSERT INTO `nomusuario_nomina` (`usuario_nomina_id`, `id_usuario`, `id_nomina`, `acceso`) VALUES
(1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nom_modulos_usuario`
--

CREATE TABLE `nom_modulos_usuario` (
  `id` int(10) UNSIGNED NOT NULL,
  `coduser` int(10) UNSIGNED NOT NULL,
  `cod_modulo` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nom_modulos_usuario`
--

INSERT INTO `nom_modulos_usuario` (`id`, `coduser`, `cod_modulo`) VALUES
(1, 1, 1),
(2, 1, 3),
(3, 1, 4),
(4, 1, 7),
(5, 1, 9),
(6, 1, 10),
(7, 1, 11),
(8, 1, 21),
(9, 1, 45),
(10, 1, 61),
(11, 1, 65),
(12, 1, 66),
(13, 1, 67),
(14, 1, 68),
(15, 1, 69),
(16, 1, 70),
(17, 1, 71),
(18, 1, 72),
(19, 1, 73),
(20, 1, 74),
(21, 1, 75),
(22, 1, 76),
(23, 1, 77),
(24, 1, 78),
(25, 1, 79),
(26, 1, 80),
(27, 1, 81),
(28, 1, 82),
(29, 1, 83),
(30, 1, 84),
(31, 1, 85),
(32, 1, 86),
(33, 1, 87),
(34, 1, 88),
(35, 1, 89),
(36, 1, 90),
(37, 1, 91),
(38, 1, 92),
(39, 1, 93),
(40, 1, 94),
(41, 1, 95),
(42, 1, 96),
(43, 1, 97),
(44, 1, 98),
(45, 1, 99),
(46, 1, 100),
(47, 1, 101),
(48, 1, 102),
(49, 1, 103),
(50, 1, 104),
(51, 1, 106),
(52, 1, 107),
(53, 1, 108),
(54, 1, 109),
(55, 1, 110),
(56, 1, 111),
(57, 1, 112),
(58, 1, 113),
(59, 1, 114),
(60, 1, 115),
(61, 1, 116),
(62, 1, 117),
(63, 1, 118),
(64, 1, 120),
(65, 1, 121),
(66, 1, 122),
(67, 1, 123),
(68, 1, 124),
(69, 1, 125),
(70, 1, 126),
(71, 1, 127),
(72, 1, 128),
(73, 1, 129),
(74, 1, 130),
(75, 1, 131),
(76, 1, 132),
(77, 1, 133),
(78, 1, 134),
(79, 1, 135),
(80, 1, 136),
(81, 1, 137),
(82, 1, 138),
(83, 1, 139),
(84, 1, 140),
(85, 1, 141),
(86, 1, 142),
(87, 1, 143),
(88, 1, 144),
(89, 1, 145),
(90, 1, 146),
(91, 1, 147),
(92, 1, 148),
(93, 1, 149),
(94, 1, 150),
(95, 1, 151),
(96, 1, 152),
(97, 1, 153),
(98, 1, 154),
(99, 1, 155),
(100, 1, 156),
(101, 1, 157),
(102, 1, 158),
(103, 1, 159),
(104, 1, 160),
(105, 1, 161),
(106, 1, 162),
(107, 1, 163),
(108, 1, 164),
(109, 1, 165),
(110, 1, 166),
(111, 1, 167),
(112, 1, 168),
(113, 1, 169),
(114, 1, 170),
(115, 1, 171),
(116, 1, 172),
(117, 1, 177),
(118, 1, 178),
(119, 1, 179),
(120, 1, 180),
(121, 1, 181),
(122, 1, 182),
(123, 1, 183),
(124, 1, 184),
(125, 1, 185),
(126, 1, 186),
(127, 1, 187),
(128, 1, 188),
(129, 1, 192),
(130, 1, 193),
(131, 1, 194),
(132, 1, 195),
(133, 1, 196),
(134, 1, 197),
(135, 1, 198),
(136, 1, 199),
(137, 1, 200),
(138, 1, 201),
(139, 1, 202),
(140, 1, 203),
(141, 1, 204),
(142, 1, 205),
(143, 1, 206),
(144, 1, 207),
(145, 1, 208),
(146, 1, 209),
(147, 1, 210),
(148, 1, 211),
(149, 1, 212),
(150, 1, 213),
(151, 1, 214),
(152, 1, 215),
(153, 1, 216),
(154, 1, 217),
(155, 1, 218),
(156, 1, 219),
(157, 1, 220),
(158, 1, 221),
(159, 1, 222),
(160, 1, 223),
(161, 1, 224),
(162, 1, 225),
(163, 1, 226),
(164, 1, 227),
(165, 1, 228),
(166, 1, 229),
(167, 1, 230),
(168, 1, 231),
(169, 1, 232),
(170, 1, 233),
(171, 1, 234),
(172, 1, 235),
(173, 1, 236),
(174, 1, 237),
(175, 1, 238),
(176, 1, 239),
(177, 1, 240),
(178, 1, 241),
(179, 1, 242),
(180, 1, 243),
(181, 1, 244),
(182, 1, 245),
(183, 1, 246),
(184, 1, 247),
(185, 1, 248),
(186, 1, 249),
(187, 1, 250),
(188, 1, 251),
(189, 1, 252),
(190, 1, 253),
(191, 1, 254),
(192, 1, 255),
(193, 1, 256),
(194, 1, 257),
(195, 1, 258),
(196, 1, 259),
(197, 1, 260),
(198, 1, 261),
(199, 1, 262),
(200, 1, 263),
(201, 1, 264),
(202, 1, 265),
(203, 1, 266),
(204, 1, 267),
(205, 1, 268),
(206, 1, 269),
(207, 1, 270),
(208, 1, 271),
(209, 1, 272),
(210, 1, 273),
(211, 1, 274),
(212, 1, 275),
(213, 1, 276),
(214, 1, 277),
(215, 1, 278),
(216, 1, 279),
(217, 1, 280),
(218, 1, 281),
(219, 1, 282),
(220, 1, 283),
(221, 1, 284),
(222, 1, 285),
(223, 1, 287),
(224, 1, 288),
(225, 1, 289),
(226, 1, 291),
(227, 1, 292),
(228, 1, 293),
(229, 1, 294),
(230, 1, 295),
(231, 1, 296),
(232, 1, 297),
(233, 1, 298),
(234, 1, 299),
(235, 1, 300),
(236, 1, 301),
(237, 1, 302),
(238, 1, 303),
(239, 1, 304),
(240, 1, 305),
(241, 1, 306),
(242, 1, 307),
(243, 1, 308),
(244, 1, 309),
(245, 1, 311),
(246, 1, 312),
(247, 1, 313),
(248, 1, 314),
(249, 1, 316),
(250, 1, 317),
(251, 1, 318),
(252, 1, 319),
(253, 1, 320),
(254, 1, 321),
(255, 1, 322),
(256, 1, 323),
(257, 1, 324),
(258, 1, 325),
(259, 1, 326),
(260, 1, 327),
(261, 1, 328),
(262, 1, 329);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nom_paginas_usuario`
--

CREATE TABLE `nom_paginas_usuario` (
  `coduser` int(11) NOT NULL,
  `id_pagina` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `nom_paginas_usuario`
--

INSERT INTO `nom_paginas_usuario` (`coduser`, `id_pagina`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paises`
--

CREATE TABLE `paises` (
  `id` int(11) NOT NULL,
  `iso` char(5) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombre` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `alt_currency_symbol` varchar(5) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `paises`
--

INSERT INTO `paises` (`id`, `iso`, `nombre`, `alt_currency_symbol`) VALUES
(1, 'AF', 'Afganistan', ''),
(2, 'AX', 'Islas Gland', ''),
(3, 'AL', 'Albania', ''),
(4, 'DE', 'Alemania', ''),
(5, 'AD', 'Andorra', ''),
(6, 'AO', 'Angola', ''),
(7, 'AI', 'Anguilla', ''),
(8, 'AQ', 'Antártida', ''),
(9, 'AG', 'Antigua y Barbuda', ''),
(10, 'AN', 'Antillas Holandesas', ''),
(11, 'SA', 'Arabia Saudí', ''),
(12, 'DZ', 'Argelia', ''),
(13, 'AR', 'Argentina', ''),
(14, 'AM', 'Armenia', ''),
(15, 'AW', 'Aruba', ''),
(16, 'AU', 'Australia', ''),
(17, 'AT', 'Austria', ''),
(18, 'AZ', 'Azerbaiyán', ''),
(19, 'BS', 'Bahamas', ''),
(20, 'BH', 'Bahréin', ''),
(21, 'BD', 'Bangladesh', ''),
(22, 'BB', 'Barbados', ''),
(23, 'BY', 'Bielorrusia', ''),
(24, 'BE', 'Bélgica', ''),
(25, 'BZ', 'Belice', ''),
(26, 'BJ', 'Benin', ''),
(27, 'BM', 'Bermudas', ''),
(28, 'BT', 'Bhután', ''),
(29, 'BO', 'Bolivia', ''),
(30, 'BA', 'Bosnia y Herzegovina', ''),
(31, 'BW', 'Botsuana', ''),
(32, 'BV', 'Isla Bouvet', ''),
(33, 'BR', 'Brasil', ''),
(34, 'BN', 'Brunéi', ''),
(35, 'BG', 'Bulgaria', ''),
(36, 'BF', 'Burkina Faso', ''),
(37, 'BI', 'Burundi', ''),
(38, 'CV', 'Cabo Verde', ''),
(39, 'KY', 'Islas Caimán', ''),
(40, 'KH', 'Camboya', ''),
(41, 'CM', 'Camerún', ''),
(42, 'CA', 'Canadá', ''),
(43, 'CF', 'República Centroafricana', ''),
(44, 'TD', 'Chad', ''),
(45, 'CZ', 'República Checa', ''),
(46, 'CL', 'Chile', ''),
(47, 'CN', 'China', ''),
(48, 'CY', 'Chipre', ''),
(49, 'CX', 'Isla de Navidad', ''),
(50, 'VA', 'Ciudad del Vaticano', ''),
(51, 'CC', 'Islas Cocos', ''),
(52, 'CO', 'Colombia', ''),
(53, 'KM', 'Comoras', ''),
(54, 'CD', 'República Democrática del Congo', ''),
(55, 'CG', 'Congo', ''),
(56, 'CK', 'Islas Cook', ''),
(57, 'KP', 'Corea del Norte', ''),
(58, 'KR', 'Corea del Sur', ''),
(59, 'CI', 'Costa de Marfil', ''),
(60, 'CR', 'Costa Rica', ''),
(61, 'HR', 'Croacia', ''),
(62, 'CU', 'Cuba', ''),
(63, 'DK', 'Dinamarca', ''),
(64, 'DM', 'Dominica', ''),
(65, 'DO', 'República Dominicana', ''),
(66, 'EC', 'Ecuador', ''),
(67, 'EG', 'Egipto', ''),
(68, 'SV', 'El Salvador', ''),
(69, 'AE', 'Emiratos Árabes Unidos', ''),
(70, 'ER', 'Eritrea', ''),
(71, 'SK', 'Eslovaquia', ''),
(72, 'SI', 'Eslovenia', ''),
(73, 'ES', 'España', ''),
(74, 'UM', 'Islas ultramarinas de Estados Unidos', ''),
(75, 'US', 'Estados Unidos', ''),
(76, 'EE', 'Estonia', ''),
(77, 'ET', 'Etiopía', ''),
(78, 'FO', 'Islas Feroe', ''),
(79, 'PH', 'Filipinas', ''),
(80, 'FI', 'Finlandia', ''),
(81, 'FJ', 'Fiyi', ''),
(82, 'FR', 'Francia', ''),
(83, 'GA', 'Gabón', ''),
(84, 'GM', 'Gambia', ''),
(85, 'GE', 'Georgia', ''),
(86, 'GS', 'Islas Georgias del Sur y Sandwich del Sur', ''),
(87, 'GH', 'Ghana', ''),
(88, 'GI', 'Gibraltar', ''),
(89, 'GD', 'Granada', ''),
(90, 'GR', 'Grecia', ''),
(91, 'GL', 'Groenlandia', ''),
(92, 'GP', 'Guadalupe', ''),
(93, 'GU', 'Guam', ''),
(94, 'GT', 'Guatemala', ''),
(95, 'GF', 'Guayana Francesa', ''),
(96, 'GN', 'Guinea', ''),
(97, 'GQ', 'Guinea Ecuatorial', ''),
(98, 'GW', 'Guinea-Bissau', ''),
(99, 'GY', 'Guyana', ''),
(100, 'HT', 'Haití', ''),
(101, 'HM', 'Islas Heard y McDonald', ''),
(102, 'HN', 'Honduras', ''),
(103, 'HK', 'Hong Kong', ''),
(104, 'HU', 'Hungría', ''),
(105, 'IN', 'India', ''),
(106, 'ID', 'Indonesia', ''),
(107, 'IR', 'Irán', ''),
(108, 'IQ', 'Iraq', ''),
(109, 'IE', 'Irlanda', ''),
(110, 'IS', 'Islandia', ''),
(111, 'IL', 'Israel', ''),
(112, 'IT', 'Italia', ''),
(113, 'JM', 'Jamaica', ''),
(114, 'JP', 'Japón', ''),
(115, 'JO', 'Jordania', ''),
(116, 'KZ', 'Kazajstán', ''),
(117, 'KE', 'Kenia', ''),
(118, 'KG', 'Kirguistán', ''),
(119, 'KI', 'Kiribati', ''),
(120, 'KW', 'Kuwait', ''),
(121, 'LA', 'Laos', ''),
(122, 'LS', 'Lesotho', ''),
(123, 'LV', 'Letonia', ''),
(124, 'LB', 'Líbano', ''),
(125, 'LR', 'Liberia', ''),
(126, 'LY', 'Libia', ''),
(127, 'LI', 'Liechtenstein', ''),
(128, 'LT', 'Lituania', ''),
(129, 'LU', 'Luxemburgo', ''),
(130, 'MO', 'Macao', ''),
(131, 'MK', 'ARY Macedonia', ''),
(132, 'MG', 'Madagascar', ''),
(133, 'MY', 'Malasia', ''),
(134, 'MW', 'Malawi', ''),
(135, 'MV', 'Maldivas', ''),
(136, 'ML', 'Malí', ''),
(137, 'MT', 'Malta', ''),
(138, 'FK', 'Islas Malvinas', ''),
(139, 'MP', 'Islas Marianas del Norte', ''),
(140, 'MA', 'Marruecos', ''),
(141, 'MH', 'Islas Marshall', ''),
(142, 'MQ', 'Martinica', ''),
(143, 'MU', 'Mauricio', ''),
(144, 'MR', 'Mauritania', ''),
(145, 'YT', 'Mayotte', ''),
(146, 'MX', 'México', ''),
(147, 'FM', 'Micronesia', ''),
(148, 'MD', 'Moldavia', ''),
(149, 'MC', 'Mónaco', ''),
(150, 'MN', 'Mongolia', ''),
(151, 'MS', 'Montserrat', ''),
(152, 'MZ', 'Mozambique', ''),
(153, 'MM', 'Myanmar', ''),
(154, 'NA', 'Namibia', ''),
(155, 'NR', 'Nauru', ''),
(156, 'NP', 'Nepal', ''),
(157, 'NI', 'Nicaragua', 'C$'),
(158, 'NE', 'Níger', ''),
(159, 'NG', 'Nigeria', ''),
(160, 'NU', 'Niue', ''),
(161, 'NF', 'Isla Norfolk', ''),
(162, 'NO', 'Noruega', ''),
(163, 'NC', 'Nueva Caledonia', ''),
(164, 'NZ', 'Nueva Zelanda', ''),
(165, 'OM', 'Omán', ''),
(166, 'NL', 'Países Bajos', ''),
(167, 'PK', 'Pakistán', ''),
(168, 'PW', 'Palau', ''),
(169, 'PS', 'Palestina', ''),
(170, 'PA', 'Panamá', ''),
(171, 'PG', 'Papúa Nueva Guinea', ''),
(172, 'PY', 'Paraguay', ''),
(173, 'PE', 'Perú', ''),
(174, 'PN', 'Islas Pitcairn', ''),
(175, 'PF', 'Polinesia Francesa', ''),
(176, 'PL', 'Polonia', ''),
(177, 'PT', 'Portugal', ''),
(178, 'PR', 'Puerto Rico', ''),
(179, 'QA', 'Qatar', ''),
(180, 'GB', 'Reino Unido', ''),
(181, 'RE', 'Reunión', ''),
(182, 'RW', 'Ruanda', ''),
(183, 'RO', 'Rumania', ''),
(184, 'RU', 'Rusia', ''),
(185, 'EH', 'Sahara Occidental', ''),
(186, 'SB', 'Islas Salomón', ''),
(187, 'WS', 'Samoa', ''),
(188, 'AS', 'Samoa Americana', ''),
(189, 'KN', 'San Cristóbal y Nevis', ''),
(190, 'SM', 'San Marino', ''),
(191, 'PM', 'San Pedro y Miquelón', ''),
(192, 'VC', 'San Vicente y las Granadinas', ''),
(193, 'SH', 'Santa Helena', ''),
(194, 'LC', 'Santa Lucía', ''),
(195, 'ST', 'Santo Tomé y Príncipe', ''),
(196, 'SN', 'Senegal', ''),
(197, 'CS', 'Serbia y Montenegro', ''),
(198, 'SC', 'Seychelles', ''),
(199, 'SL', 'Sierra Leona', ''),
(200, 'SG', 'Singapur', ''),
(201, 'SY', 'Siria', ''),
(202, 'SO', 'Somalia', ''),
(203, 'LK', 'Sri Lanka', ''),
(204, 'SZ', 'Suazilandia', ''),
(205, 'ZA', 'Sudáfrica', ''),
(206, 'SD', 'Sudán', ''),
(207, 'SE', 'Suecia', ''),
(208, 'CH', 'Suiza', ''),
(209, 'SR', 'Surinam', ''),
(210, 'SJ', 'Svalbard y Jan Mayen', ''),
(211, 'TH', 'Tailandia', ''),
(212, 'TW', 'Taiwán', ''),
(213, 'TZ', 'Tanzania', ''),
(214, 'TJ', 'Tayikistán', ''),
(215, 'IO', 'Territorio Británico del Océano Índico', ''),
(216, 'TF', 'Territorios Australes Franceses', ''),
(217, 'TL', 'Timor Oriental', ''),
(218, 'TG', 'Togo', ''),
(219, 'TK', 'Tokelau', ''),
(220, 'TO', 'Tonga', ''),
(221, 'TT', 'Trinidad y Tobago', ''),
(222, 'TN', 'Túnez', ''),
(223, 'TC', 'Islas Turcas y Caicos', ''),
(224, 'TM', 'Turkmenistán', ''),
(225, 'TR', 'Turquía', ''),
(226, 'TV', 'Tuvalu', ''),
(227, 'UA', 'Ucrania', ''),
(228, 'UG', 'Uganda', ''),
(229, 'UY', 'Uruguay', ''),
(230, 'UZ', 'Uzbekistán', ''),
(231, 'VU', 'Vanuatu', ''),
(232, 'VE', 'Venezuela', ''),
(233, 'VN', 'Vietnam', ''),
(234, 'VG', 'Islas Vírgenes Británicas', ''),
(235, 'VI', 'Islas Vírgenes de los Estados Unidos', ''),
(236, 'WF', 'Wallis y Futuna', ''),
(237, 'YE', 'Yemen', ''),
(238, 'DJ', 'Yibuti', ''),
(239, 'ZM', 'Zambia', ''),
(240, 'ZW', 'Zimbabue', '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `config_install`
--
ALTER TABLE `config_install`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `conf_ejecucion_reporte`
--
ALTER TABLE `conf_ejecucion_reporte`
  ADD PRIMARY KEY (`conf_erep_id`);

--
-- Indices de la tabla `datos_empresa`
--
ALTER TABLE `datos_empresa`
  ADD PRIMARY KEY (`cod_datos_empresa`),
  ADD KEY `cod_empresa` (`cod_empresa`),
  ADD KEY `pais_id` (`pais_id`);

--
-- Indices de la tabla `evento_usuario`
--
ALTER TABLE `evento_usuario`
  ADD PRIMARY KEY (`evento_usuario_id`),
  ADD KEY `usuario` (`usuario`);

--
-- Indices de la tabla `nomempresa`
--
ALTER TABLE `nomempresa`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `nomusuarios`
--
ALTER TABLE `nomusuarios`
  ADD PRIMARY KEY (`coduser`);

--
-- Indices de la tabla `nomusuario_empresa`
--
ALTER TABLE `nomusuario_empresa`
  ADD PRIMARY KEY (`usuario_empresa_id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_nomina` (`id_empresa`);

--
-- Indices de la tabla `nomusuario_nomina`
--
ALTER TABLE `nomusuario_nomina`
  ADD PRIMARY KEY (`usuario_nomina_id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_nomina` (`id_nomina`);

--
-- Indices de la tabla `nom_modulos_usuario`
--
ALTER TABLE `nom_modulos_usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coduser` (`coduser`),
  ADD KEY `cod_modulo` (`cod_modulo`);

--
-- Indices de la tabla `nom_paginas_usuario`
--
ALTER TABLE `nom_paginas_usuario`
  ADD PRIMARY KEY (`coduser`,`id_pagina`);

--
-- Indices de la tabla `paises`
--
ALTER TABLE `paises`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `config_install`
--
ALTER TABLE `config_install`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `conf_ejecucion_reporte`
--
ALTER TABLE `conf_ejecucion_reporte`
  MODIFY `conf_erep_id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `datos_empresa`
--
ALTER TABLE `datos_empresa`
  MODIFY `cod_datos_empresa` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT de la tabla `evento_usuario`
--
ALTER TABLE `evento_usuario`
  MODIFY `evento_usuario_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `nomempresa`
--
ALTER TABLE `nomempresa`
  MODIFY `codigo` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT de la tabla `nomusuarios`
--
ALTER TABLE `nomusuarios`
  MODIFY `coduser` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `nomusuario_empresa`
--
ALTER TABLE `nomusuario_empresa`
  MODIFY `usuario_empresa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `nomusuario_nomina`
--
ALTER TABLE `nomusuario_nomina`
  MODIFY `usuario_nomina_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT de la tabla `nom_modulos_usuario`
--
ALTER TABLE `nom_modulos_usuario`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=263;
--
-- AUTO_INCREMENT de la tabla `paises`
--
ALTER TABLE `paises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
