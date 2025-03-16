SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE IF NOT EXISTS `caa_archivos_datos` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `ficha` int(10) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `tipo_movimiento` enum('Entrada','Salida') DEFAULT NULL,
  `dispositivo` varchar(60) DEFAULT NULL,
  `archivo_reloj` int(11) NOT NULL,
  `corregido` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`codigo`,`archivo_reloj`),
  KEY `fk_caa_archivos_detalle_caa_archivos1_idx` (`archivo_reloj`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caa_archivos_reloj`
--

CREATE TABLE IF NOT EXISTS `caa_archivos_reloj` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_registro` datetime NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `configuracion` int(11) NOT NULL,
  PRIMARY KEY (`codigo`),
  KEY `fk_caa_archivos_caa_configuracion1_idx` (`configuracion`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caa_configuracion`
--

CREATE TABLE IF NOT EXISTS `caa_configuracion` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(60) NOT NULL,
  `formato` enum('csv','excel','txt','log') NOT NULL,
  `delimitador` varchar(2) NOT NULL,
  `primera_linea` smallint(6) NOT NULL,
  `ignorar_columnas` smallint(6) NOT NULL,
  `filas_vacias` smallint(6) NOT NULL,
  `valor_entrada` varchar(15) DEFAULT NULL,
  `valor_salida` varchar(15) DEFAULT NULL,
  `tipo_reloj` int(11) NOT NULL,
  PRIMARY KEY (`codigo`),
  KEY `fk_caa_configuracion_caa_tiporeloj1_idx` (`tipo_reloj`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `caa_configuracion`
--

INSERT INTO `caa_configuracion` (`codigo`, `descripcion`, `formato`, `delimitador`, `primera_linea`, `ignorar_columnas`, `filas_vacias`, `valor_entrada`, `valor_salida`, `tipo_reloj`) VALUES
(1, 'Modelo TK100-C', 'txt', '\\t', 1, 0, 0, 'Entrada', 'Salida', 1),
(2, 'Modelo Ep Series', 'txt', '\\t', 1, 0, 0, '0', '1', 2),
(3, 'Modelo HandPunch', 'log', ',', 0, 0, 0, NULL, NULL, 3),
(4, 'Modelo Chino', 'txt', ' ', 0, 1, 0, 'Entrada', 'Salida', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caa_parametros`
--

CREATE TABLE IF NOT EXISTS `caa_parametros` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) NOT NULL,
  `posicion` int(11) NOT NULL,
  `formato` varchar(15) DEFAULT NULL,
  `configuracion` int(11) NOT NULL,
  PRIMARY KEY (`codigo`),
  KEY `fk_caa_parametros_caa_configuracion1_idx` (`configuracion`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=20 ;

--
-- Volcado de datos para la tabla `caa_parametros`
--

INSERT INTO `caa_parametros` (`codigo`, `nombre`, `posicion`, `formato`, `configuracion`) VALUES
(1, 'numero', 1, NULL, 1),
(2, 'tiempo', 3, 'm/d/Y h:i:s a', 1),
(3, 'tipo_movimiento', 4, NULL, 1),
(4, 'dispositivo', 5, NULL, 1),
(5, 'numero', 1, NULL, 2),
(6, 'tiempo', 2, 'Y-m-d H:i:s', 2),
(7, 'tipo_movimiento', 3, NULL, 2),
(8, 'numero', 4, NULL, 3),
(9, 'hora', 6, 'H', 3),
(10, 'minutos', 7, 'i', 3),
(11, 'mes', 8, 'm', 3),
(12, 'dia', 9, 'd', 3),
(13, 'anio', 10, 'y', 3),
(14, 'dispositivo', 1, NULL, 3),
(15, 'dispositivo', 2, NULL, 3),
(16, 'numero', 1, NULL, 4),
(17, 'fecha', 2, 'd/m/Y', 4),
(18, 'hora', 3, 'G:i', 4),
(19, 'tipo_movimiento', 4, NULL, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caa_tiporeloj`
--

CREATE TABLE IF NOT EXISTS `caa_tiporeloj` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `caa_tiporeloj`
--

INSERT INTO `caa_tiporeloj` (`codigo`, `nombre`) VALUES
(1, 'ZKTeco'),
(2, 'Anviz'),
(3, 'HandPunch'),
(4, 'Sin Marca');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `control_acceso`
--

CREATE TABLE IF NOT EXISTS `control_acceso` (
  `cod_compania` varchar(4) NOT NULL,
  `cod_nomina` varchar(4) NOT NULL,
  `cod_trabajador` varchar(10) NOT NULL,
  `fecha` date NOT NULL,
  `concepto` varchar(5) NOT NULL,
  `valor` varchar(12) NOT NULL,
  `cod_enca` int(11) NOT NULL,
  `conse` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`conse`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `control_encabezado`
--

CREATE TABLE IF NOT EXISTS `control_encabezado` (
  `cod_enca` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_reg` date NOT NULL,
  `fecha_ini` date NOT NULL,
  `fecha_fin` date NOT NULL,
  PRIMARY KEY (`cod_enca`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta`
--

CREATE TABLE IF NOT EXISTS `cuenta` (
  `cedula` varchar(20) NOT NULL,
  `cuenta` varchar(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `cuenta`
--

INSERT INTO `cuenta` (`cedula`, `cuenta`) VALUES
('1', 'Cuenta 1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cwconcue`
--

CREATE TABLE IF NOT EXISTS `cwconcue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Cuenta` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `Nivel` int(11) NOT NULL DEFAULT '0',
  `Tipo` char(2) COLLATE utf8_spanish_ci NOT NULL,
  `Descrip` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `Bancos` int(11) NOT NULL DEFAULT '0',
  `MonPre` float NOT NULL DEFAULT '0',
  `MonModif` float NOT NULL DEFAULT '0',
  `FechaNuevo` date NOT NULL DEFAULT '0000-00-00',
  `CtaNueva` int(11) NOT NULL DEFAULT '0',
  `Auxunico` varchar(4) COLLATE utf8_spanish_ci NOT NULL,
  `Monetaria` int(11) NOT NULL DEFAULT '0',
  `Ctaajuste` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `Marca` int(11) NOT NULL DEFAULT '0',
  `MonPreu` float NOT NULL DEFAULT '0',
  `MonModify` float NOT NULL DEFAULT '0',
  `Ccostos` int(11) NOT NULL DEFAULT '0',
  `Terceros` int(11) NOT NULL DEFAULT '0',
  `Cuentalt` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `Descripalt` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `Fiscaltipo` int(11) NOT NULL DEFAULT '0',
  `Tipocosto` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Cuenta` (`Cuenta`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Maestro en Cuentas Contables' AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `cwconcue`
--

INSERT INTO `cwconcue` (`id`, `Cuenta`, `Nivel`, `Tipo`, `Descrip`, `Bancos`, `MonPre`, `MonModif`, `FechaNuevo`, `CtaNueva`, `Auxunico`, `Monetaria`, `Ctaajuste`, `Marca`, `MonPreu`, `MonModify`, `Ccostos`, `Terceros`, `Cuentalt`, `Descripalt`, `Fiscaltipo`, `Tipocosto`) VALUES
(1, '1', 1, '', 'Cuenta Contable 1', 0, 0, 0, '0000-00-00', 0, '', 0, '', 0, 0, 0, 0, 0, '', '', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cwprecue`
--

CREATE TABLE IF NOT EXISTS `cwprecue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `CodCue` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `Denominacion` text COLLATE utf8_spanish_ci NOT NULL,
  `Tipocta` int(10) unsigned NOT NULL DEFAULT '0',
  `Tipopuc` char(3) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `CodCue` (`CodCue`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `cwprecue`
--

INSERT INTO `cwprecue` (`id`, `CodCue`, `Denominacion`, `Tipocta`, `Tipopuc`) VALUES
(1, '012', 'Cuenta Presupuestaria 1', 0, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento`
--

CREATE TABLE IF NOT EXISTS `departamento` (
  `IdDepartamento` int(11) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(255) NOT NULL,
  `IdJefe` varchar(32) DEFAULT NULL,
  `IdPlanilla` int(11) DEFAULT NULL,
  `id_direccion` int(11) DEFAULT NULL,
  `uid_jefe` varchar(100) DEFAULT NULL,
  `uid_subjefe` varchar(50) DEFAULT NULL,
  `id_parent` int(11) DEFAULT NULL,
  PRIMARY KEY (`IdDepartamento`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descuento_tipo`
--

CREATE TABLE IF NOT EXISTS `descuento_tipo` (
  `id_descuento_tipo` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) DEFAULT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_descuento_tipo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=9 ;

--
-- Volcado de datos para la tabla `descuento_tipo`
--

INSERT INTO `descuento_tipo` (`id_descuento_tipo`, `codigo`, `descripcion`) VALUES
(1, '1', 'MULTAS'),
(2, '2', 'SIACAP'),
(3, '3', 'PRAA'),
(4, '4', 'RECUPERACION'),
(5, '5', 'COIF'),
(6, '6', 'S/COL SEGURIDAD SOCIAL'),
(7, '7', 'LEYES ESPECIALES'),
(8, '8', 'OTRO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dias_incapacidad`
--

CREATE TABLE IF NOT EXISTS `dias_incapacidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod_user` varchar(25) DEFAULT NULL,
  `tipo_justificacion` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `tiempo` float DEFAULT NULL,
  `observacion` text,
  `documento` blob,
  `st` varchar(100) DEFAULT NULL,
  `usr_uid` varchar(32) DEFAULT '',
  `fecha_vence` datetime DEFAULT NULL,
  `dias` varchar(4) DEFAULT NULL,
  `horas` varchar(4) DEFAULT NULL,
  `minutos` varchar(4) DEFAULT NULL,
  `idparent` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IX_DIAS_INCA_USER_FEC_TIPO` (`usr_uid`,`tipo_justificacion`,`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dias_incapacidad2`
--

CREATE TABLE IF NOT EXISTS `dias_incapacidad2` (
  `id` int(11) DEFAULT NULL,
  `cod_user` varchar(75) DEFAULT NULL,
  `tipo_justificacion` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `tiempo` float DEFAULT NULL,
  `observacion` text,
  `documento` blob,
  `st` varchar(300) DEFAULT NULL,
  `usr_uid` varchar(96) DEFAULT NULL,
  `fecha_vence` datetime DEFAULT NULL,
  `dias` varchar(12) DEFAULT NULL,
  `horas` varchar(12) DEFAULT NULL,
  `minutos` varchar(12) DEFAULT NULL,
  `idparent` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dias_incapacidad_historial`
--

CREATE TABLE IF NOT EXISTS `dias_incapacidad_historial` (
  `id` int(11) DEFAULT NULL,
  `cod_user` int(11) DEFAULT NULL,
  `tipo_justificacion` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `tiempo` float DEFAULT NULL,
  `observacion` text,
  `documento` blob,
  `nombre_documento` varchar(765) DEFAULT NULL,
  `tamanio` varchar(150) DEFAULT NULL,
  `st` varchar(300) DEFAULT NULL,
  `usr_uid` varchar(150) DEFAULT NULL,
  `dias` varchar(15) DEFAULT NULL,
  `horas` varchar(15) DEFAULT NULL,
  `minutos` varchar(15) DEFAULT NULL,
  `fecha_vence` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE IF NOT EXISTS `empleado` (
  `IdEmpleado` int(10) DEFAULT NULL,
  `Cedula` varchar(75) DEFAULT NULL,
  `ApellidoPaterno` varchar(150) DEFAULT NULL,
  `ApellidoMaterno` varchar(150) DEFAULT NULL,
  `ApellidoCasada` varchar(150) DEFAULT NULL,
  `PrimerNombre` varchar(150) DEFAULT NULL,
  `SegundoNombre` varchar(150) DEFAULT NULL,
  `FechaNacimiento` date DEFAULT NULL,
  `IdNivelEducativo` int(11) DEFAULT NULL,
  `IdTipoSangre` int(11) DEFAULT NULL,
  `IdPais` char(9) DEFAULT NULL,
  `IdEstadoCivil` int(11) DEFAULT NULL,
  `SeguroSocial` varchar(150) DEFAULT NULL,
  `TelefonoResidencial` varchar(60) DEFAULT NULL,
  `TelefonoCelular` varchar(60) DEFAULT NULL,
  `Direccion` varchar(765) DEFAULT NULL,
  `ContactoEmergencia` varchar(300) DEFAULT NULL,
  `TelefonoContactoEmergencia` varchar(60) DEFAULT NULL,
  `Hijos` int(11) DEFAULT NULL,
  `EnfermedadesYAlergias` varchar(600) DEFAULT NULL,
  `IdProfesion` int(11) DEFAULT NULL,
  `IdSexo` char(3) DEFAULT NULL,
  `IdEstadoEmpleado` int(11) DEFAULT NULL,
  `useruid` varchar(300) DEFAULT NULL,
  `email` varchar(300) DEFAULT NULL,
  `resolucion` varchar(300) DEFAULT NULL,
  `activo` char(1) DEFAULT NULL,
  `idmotivosalida` int(11) DEFAULT NULL,
  `decreto` varchar(600) DEFAULT NULL,
  `jefe` char(1) DEFAULT NULL,
  `uid_user_aprueba` varchar(300) DEFAULT NULL,
  `condicion` char(3) DEFAULT NULL,
  `tiene_discapacidad` char(1) DEFAULT NULL,
  `tiene_familiar_disca` char(1) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `personal_externo` char(1) DEFAULT NULL,
  `comentario` varchar(765) DEFAULT NULL,
  `id_institucion` int(11) DEFAULT NULL,
  `fecha_permanencia` datetime DEFAULT NULL,
  `sobresueldo1` decimal(12,0) DEFAULT NULL,
  `sobresueldo2` decimal(12,0) DEFAULT NULL,
  `sobresueldo3` decimal(12,0) DEFAULT NULL,
  `sobresueldo4` decimal(12,0) DEFAULT NULL,
  `clave_isr` varchar(15) DEFAULT NULL,
  `fecha_resol` date DEFAULT NULL,
  `num_resol_carrera` varchar(60) DEFAULT NULL,
  `fecha_noti_carrera` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado_cargo`
--

CREATE TABLE IF NOT EXISTS `empleado_cargo` (
  `ID` int(11) DEFAULT NULL,
  `IdEmpleado` int(11) DEFAULT NULL,
  `IdDepartamento` int(11) DEFAULT NULL,
  `FechaInicio` date DEFAULT NULL,
  `FechaFinal` date DEFAULT NULL,
  `TipoMovimiento` char(3) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `usuario_crea` varchar(150) DEFAULT NULL,
  `fecha_memo` date DEFAULT NULL,
  `num_memo` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `expediente`
--

CREATE TABLE IF NOT EXISTS `expediente` (
  `cod_expediente_det` int(11) NOT NULL,
  `cedula` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `descripcion` text COLLATE utf8_spanish_ci NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `monto_nuevo` decimal(10,2) NOT NULL,
  `dias` int(3) NOT NULL,
  `fecha_retorno` date NOT NULL,
  `fecha_salida` date NOT NULL,
  `cod_cargo` varchar(9) COLLATE utf8_spanish_ci NOT NULL,
  `cod_cargo_nuevo` varchar(9) COLLATE utf8_spanish_ci NOT NULL,
  `fecha` date NOT NULL,
  `usuario` varchar(30) COLLATE utf8_spanish_ci NOT NULL,
  `pagado_por_emp` varchar(2) COLLATE utf8_spanish_ci NOT NULL,
  `institucion` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `tipo_estudio` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `nivel_actual` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `costo_persona` decimal(17,2) NOT NULL,
  `num_participantes` int(4) NOT NULL,
  `nombre_especialista` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `gerencia_anterior` int(6) NOT NULL,
  `gerencia_nueva` int(6) NOT NULL,
  `nomina_anterior` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `nomina_nueva` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `puntaje` decimal(4,2) NOT NULL,
  `calificacion` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `labor` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `institucion_publica` int(1) NOT NULL,
  `tcamisa` varchar(4) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tchaqueta` varchar(4) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tbata` varchar(4) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tpantalon` varchar(4) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tmono` varchar(4) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tzapato` varchar(4) COLLATE utf8_spanish_ci DEFAULT NULL,
  `desde` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `hasta` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `horas` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `minutos` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `aprobado` date DEFAULT NULL,
  `enterado` date DEFAULT NULL,
  `resol1` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `resol2` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `resol3` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha1` date DEFAULT NULL,
  `fecha2` date DEFAULT NULL,
  `fecha3` date DEFAULT NULL,
  `dr1` int(11) DEFAULT NULL,
  `dr2` int(11) DEFAULT NULL,
  `dr3` int(11) DEFAULT NULL,
  `numero_resolucion` int(11) NOT NULL,
  `numero_decreto` int(11) NOT NULL,
  `tipo` int(11) NOT NULL,
  `subtipo` int(11) DEFAULT NULL,
  PRIMARY KEY (`cod_expediente_det`),
  KEY `fk_expediente_expediente_tipo1_idx` (`tipo`),
  KEY `fk_expediente_expediente_subtipo1_idx` (`subtipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='contiene todos los datos de expediente del personal ';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `expediente_adjunto`
--

CREATE TABLE IF NOT EXISTS `expediente_adjunto` (
  `id_adjunto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_adjunto` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `archivo` varchar(100) NOT NULL,
  `tipo` varchar(45) NOT NULL,
  `tamano` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `expediente_cod_expediente_det` int(11) NOT NULL,
  PRIMARY KEY (`id_adjunto`),
  KEY `fk_expediente_adjunto_expediente1_idx` (`expediente_cod_expediente_det`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `expediente_documento`
--

CREATE TABLE IF NOT EXISTS `expediente_documento` (
  `id_documento` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_documento` varchar(60) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `url_documento` varchar(255) NOT NULL,
  `fecha_registro` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `expediente_cod_expediente_det` int(11) NOT NULL,
  PRIMARY KEY (`id_documento`),
  KEY `fk_expediente_documento_expediente1_idx` (`expediente_cod_expediente_det`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `expediente_subtipo`
--

CREATE TABLE IF NOT EXISTS `expediente_subtipo` (
  `id_expediente_subtipo` int(11) NOT NULL AUTO_INCREMENT,
  `id_expediente_tipo` int(11) NOT NULL,
  `nombre_subtipo` varchar(100) NOT NULL,
  PRIMARY KEY (`id_expediente_subtipo`),
  KEY `fk_expediente_subtipo_expediente_tipo1_idx` (`id_expediente_tipo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=55 ;

--
-- Volcado de datos para la tabla `expediente_subtipo`
--

INSERT INTO `expediente_subtipo` (`id_expediente_subtipo`, `id_expediente_tipo`, `nombre_subtipo`) VALUES
(1, 1, 'Primaria'),
(2, 1, 'Basico'),
(3, 1, 'Diversificado'),
(4, 1, 'Tecnico Universitario'),
(5, 1, 'Pre-Grado'),
(6, 1, 'Post-Grado'),
(7, 2, 'Curso'),
(8, 2, 'Taller'),
(9, 2, 'Seminario'),
(10, 2, 'Charla'),
(11, 2, 'Jornada'),
(17, 4, 'Enfermedad'),
(18, 4, 'Duelo'),
(19, 4, 'Matrimonio'),
(20, 4, 'Nacimiento'),
(21, 4, 'Enfermedad'),
(22, 4, 'Eventos Academicos'),
(23, 4, 'Otros asuntos personales'),
(24, 5, 'Verbal'),
(25, 5, 'Escrita'),
(26, 9, 'Traslado  Cargo'),
(27, 9, 'Traslado Planilla'),
(28, 9, 'Traslado Estructura'),
(29, 9, 'Personal Otra InstituciÃ³n'),
(30, 9, 'AsignaciÃ³n'),
(31, 9, 'DesignaciÃ³n'),
(32, 9, 'Prestamo Interinstitucional'),
(33, 9, 'Traslado'),
(34, 10, 'No Satisfactorio'),
(35, 10, 'Regular'),
(36, 10, 'Bueno'),
(37, 10, 'Excelente'),
(38, 12, 'Disminuye'),
(39, 12, 'Aumenta'),
(40, 13, 'CÃ©dula/RUC'),
(41, 13, 'Licencia'),
(42, 14, 'Trabajo Realizado'),
(43, 14, 'Labor Realizada'),
(44, 15, 'Estudios'),
(45, 15, 'CapacitaciÃ³n'),
(46, 15, 'RepresentaciÃ³n de la InstituciÃ³n, Estado o PaÃ­s'),
(47, 15, 'RepresentaciÃ³n de la asociaciÃ³n de servidores'),
(48, 16, 'Asumir cargo de elecciÃ³n popular'),
(49, 16, 'Asumir cargo de libre nobramiento y remociÃ³n'),
(50, 16, 'Estudiar'),
(51, 16, 'Asuntos Personales'),
(52, 17, 'Gravidez'),
(53, 17, 'Enfermedad incapacidad superior quinde dÃ­as'),
(54, 17, 'Riesgos Profesionales');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `expediente_tipo`
--

CREATE TABLE IF NOT EXISTS `expediente_tipo` (
  `id_expediente_tipo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_tipo` varchar(45) NOT NULL,
  PRIMARY KEY (`id_expediente_tipo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Volcado de datos para la tabla `expediente_tipo`
--

INSERT INTO `expediente_tipo` (`id_expediente_tipo`, `nombre_tipo`) VALUES
(1, 'Estudios Académicos'),
(2, 'Estudios Extra-Académicos'),
(4, 'Permisos'),
(5, 'Amonestaciones'),
(6, 'Suspensiones'),
(7, 'Renuncias'),
(8, 'Destituciones'),
(9, 'Movimiento de Personal'),
(10, 'Evaluación de Desempeño'),
(11, 'Vacaciones'),
(12, 'Tiempo Compensatorio'),
(13, 'Documento'),
(14, 'Experiencia'),
(15, 'Licencias con Sueldo'),
(16, 'Licencias sin Sueldo'),
(17, 'Licencias Especiales');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `funcioninstitucional`
--

CREATE TABLE IF NOT EXISTS `funcioninstitucional` (
  `IdFuncionInstitucional` int(11) DEFAULT NULL,
  `Descripcion` varchar(765) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `funcioninstitucional`
--

INSERT INTO `funcioninstitucional` (`IdFuncionInstitucional`, `Descripcion`) VALUES
(1, 'ASISTENTE DEL ADMINISTRADOR'),
(2, 'ABOGADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incapacidadprocesada`
--

CREATE TABLE IF NOT EXISTS `incapacidadprocesada` (
  `anio` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `dia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inclusion`
--

CREATE TABLE IF NOT EXISTS `inclusion` (
  `id_inclusion` int(11) NOT NULL AUTO_INCREMENT,
  `personal_id` int(11) DEFAULT NULL,
  `quincena` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `ano` int(11) DEFAULT NULL,
  `num_decreto` varchar(30) DEFAULT NULL,
  `fecha_decreto` date DEFAULT NULL,
  `nomposicion_id` int(11) DEFAULT NULL,
  `cedula` varchar(20) DEFAULT NULL,
  `seguro_social` varchar(20) DEFAULT NULL,
  `clave_ir` varchar(20) DEFAULT NULL,
  `sexo` varchar(20) DEFAULT NULL,
  `nombres` varchar(30) DEFAULT NULL,
  `apellido_paterno` varchar(30) DEFAULT NULL,
  `apellido_materno` varchar(30) DEFAULT NULL,
  `apellido_casada` varchar(30) DEFAULT NULL,
  `fecing` date DEFAULT NULL,
  `titular_interino` varchar(20) DEFAULT NULL,
  `tipemp` varchar(50) DEFAULT NULL,
  `dias_pagar` decimal(11,2) DEFAULT NULL,
  `suesal` decimal(20,2) DEFAULT NULL,
  `quincenas_pagar` decimal(11,2) DEFAULT NULL,
  `dias` decimal(11,2) DEFAULT NULL,
  `c001` decimal(20,2) DEFAULT NULL,
  `c002` decimal(20,2) DEFAULT NULL,
  `c003` decimal(20,2) DEFAULT NULL,
  `c011` decimal(20,2) DEFAULT NULL,
  `c012` decimal(20,2) DEFAULT NULL,
  `c013` decimal(20,2) DEFAULT NULL,
  `c019` decimal(20,2) DEFAULT NULL,
  `c080` decimal(20,2) DEFAULT NULL,
  `c030` decimal(20,2) DEFAULT NULL,
  `diferencia` decimal(20,2) DEFAULT NULL,
  `diferencia_quincena` decimal(20,2) DEFAULT NULL,
  `tipnom` int(11) DEFAULT NULL,
  `descrip_centro_pago` varchar(50) DEFAULT NULL,
  `codnivel1` varchar(8) DEFAULT NULL,
  `des_car` varchar(50) DEFAULT NULL,
  `grado` varchar(20) DEFAULT NULL,
  `codcargo` varchar(12) DEFAULT NULL,
  `descuento_fecha` date DEFAULT NULL,
  `id_descuento_tipo` int(11) DEFAULT NULL,
  `observacion` text,
  `descuento_monto_pendiente` decimal(11,2) DEFAULT NULL,
  `descuento_porcentaje` decimal(11,2) DEFAULT NULL,
  `licencia_tipo` int(11) DEFAULT NULL COMMENT '0 con sueldo, 1 sin sueldo',
  `licencia_meses` int(11) DEFAULT NULL COMMENT 'Cantidad de meses de licencia',
  `licencia_dias` int(11) DEFAULT NULL COMMENT 'Cantidad de dias de licencia',
  `licencia_desde` date DEFAULT NULL,
  `licencia_hasta` date DEFAULT NULL,
  `licencia_descripcion` varchar(200) DEFAULT NULL,
  `licencia_observaciones` varchar(200) DEFAULT NULL,
  `ajuste_dias` int(11) DEFAULT NULL,
  `ajuste_monto` decimal(11,2) DEFAULT NULL,
  `ajuste_observaciones` varchar(200) DEFAULT NULL,
  `retorno_fecha` date DEFAULT NULL,
  `retorno_dias` int(11) DEFAULT NULL,
  `retorno_monto` decimal(11,2) DEFAULT NULL,
  `retorno_observaciones` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_inclusion`),
  UNIQUE KEY `id_inclusion` (`id_inclusion`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instituciones`
--

CREATE TABLE IF NOT EXISTS `instituciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `instituciones`
--

INSERT INTO `instituciones` (`id`, `descripcion`) VALUES
(1, 'POLICIA NACIONAL'),
(2, 'FISCALIA SEXTA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_transacciones`
--

CREATE TABLE IF NOT EXISTS `log_transacciones` (
  `cod_log` int(12) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(60) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `modulo` varchar(30) NOT NULL,
  `url` varchar(30) NOT NULL,
  `accion` varchar(20) NOT NULL,
  `valor` varchar(9) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `usuario` varchar(30) NOT NULL,
  PRIMARY KEY (`cod_log`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mes`
--

CREATE TABLE IF NOT EXISTS `mes` (
  `id` int(11) DEFAULT NULL,
  `mes` varchar(75) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `mes`
--

INSERT INTO `mes` (`id`, `mes`) VALUES
(1, 'Enero'),
(2, 'Febrero'),
(3, 'Marzo'),
(4, 'Abril'),
(5, 'Mayo'),
(6, 'Junio'),
(7, 'Julio'),
(8, 'Agosto'),
(9, 'Septiembre'),
(10, 'Octubre'),
(11, 'Noviembre'),
(12, 'Diciembre');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mov_adicional`
--

CREATE TABLE IF NOT EXISTS `mov_adicional` (
  `id_mov_adicional` int(11) NOT NULL AUTO_INCREMENT,
  `id_mov_contraloria` int(11) DEFAULT NULL,
  `adicional_tipopago` int(11) DEFAULT NULL COMMENT '0 Adicional, 1 Diferencia, 2 Cancelacion de Pago',
  `adicional_quincenas` int(11) DEFAULT NULL,
  `adicional_dias` int(11) DEFAULT NULL,
  `adicional_monto` decimal(11,0) DEFAULT NULL,
  PRIMARY KEY (`id_mov_adicional`),
  UNIQUE KEY `id_inclusion` (`id_mov_adicional`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mov_ajuste`
--

CREATE TABLE IF NOT EXISTS `mov_ajuste` (
  `id_mov_ajuste` int(11) NOT NULL AUTO_INCREMENT,
  `id_mov_contraloria` int(11) DEFAULT NULL,
  `ajuste_dias` int(11) DEFAULT NULL,
  `ajuste_monto` decimal(11,2) DEFAULT NULL,
  `ajuste_observaciones` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_mov_ajuste`),
  UNIQUE KEY `id_inclusion` (`id_mov_ajuste`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mov_contraloria`
--

CREATE TABLE IF NOT EXISTS `mov_contraloria` (
  `id_mov_contraloria` int(11) NOT NULL AUTO_INCREMENT,
  `personal_id` int(11) DEFAULT NULL,
  `quincena` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `ano` int(11) DEFAULT NULL,
  `num_decreto` varchar(30) DEFAULT NULL,
  `fecha_decreto` date DEFAULT NULL,
  `nomposicion_id` int(11) DEFAULT NULL,
  `cedula` varchar(20) DEFAULT NULL,
  `seguro_social` varchar(20) DEFAULT NULL,
  `clave_ir` varchar(20) DEFAULT NULL,
  `sexo` varchar(20) DEFAULT NULL,
  `nombres` varchar(30) DEFAULT NULL,
  `apellido_paterno` varchar(30) DEFAULT NULL,
  `apellido_materno` varchar(30) DEFAULT NULL,
  `apellido_casada` varchar(30) DEFAULT NULL,
  `fecing` date DEFAULT NULL,
  `titular_interino` varchar(20) DEFAULT NULL,
  `tipemp` varchar(50) DEFAULT NULL,
  `observacion` text,
  `id_mov_tipo` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`id_mov_contraloria`),
  UNIQUE KEY `id_inclusion` (`id_mov_contraloria`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mov_descuento`
--

CREATE TABLE IF NOT EXISTS `mov_descuento` (
  `id_mov_descuento` int(11) NOT NULL AUTO_INCREMENT,
  `id_mov_contraloria` int(11) DEFAULT NULL,
  `id_descuento_tipo` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `descuento_monto_pendiente` decimal(11,2) DEFAULT NULL,
  `descuento_porcentaje` decimal(11,2) DEFAULT NULL,
  PRIMARY KEY (`id_mov_descuento`),
  UNIQUE KEY `id_inclusion` (`id_mov_descuento`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mov_descuento_tipo`
--

CREATE TABLE IF NOT EXISTS `mov_descuento_tipo` (
  `id_descuento_tipo` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) DEFAULT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_descuento_tipo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Volcado de datos para la tabla `mov_descuento_tipo`
--

INSERT INTO `mov_descuento_tipo` (`id_descuento_tipo`, `codigo`, `descripcion`) VALUES
(1, '1', 'MULTAS'),
(2, '2', 'SIACAP'),
(3, '3', 'PRAA'),
(4, '4', 'RECUPERACION'),
(5, '5', 'COIF'),
(6, '6', 'S/COL SEGURIDAD SOCIAL'),
(7, '7', 'LEYES ESPECIALES'),
(8, '8', 'OTRO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mov_inclusiones`
--

CREATE TABLE IF NOT EXISTS `mov_inclusiones` (
  `id_mov_inclusiones` int(11) NOT NULL AUTO_INCREMENT,
  `id_mov_contraloria` int(11) DEFAULT NULL,
  `dias_pagar` int(4) DEFAULT NULL,
  `suesal` decimal(20,2) DEFAULT NULL,
  `quincenas_pagar` int(4) DEFAULT NULL,
  `dias` int(4) DEFAULT NULL,
  `c001` decimal(20,2) DEFAULT NULL,
  `c002` decimal(20,2) DEFAULT NULL,
  `c003` decimal(20,2) DEFAULT NULL,
  `c011` decimal(20,2) DEFAULT NULL,
  `c012` decimal(20,2) DEFAULT NULL,
  `c013` decimal(20,2) DEFAULT NULL,
  `c019` decimal(20,2) DEFAULT NULL,
  `c080` decimal(20,2) DEFAULT NULL,
  `c030` decimal(20,2) DEFAULT NULL,
  `diferencia` decimal(20,2) DEFAULT NULL,
  `diferencia_quincena` decimal(20,2) DEFAULT NULL,
  `tipnom` int(11) DEFAULT NULL,
  `descrip_centro_pago` varchar(50) DEFAULT NULL,
  `codnivel1` varchar(8) DEFAULT NULL,
  `des_car` varchar(50) DEFAULT NULL,
  `grado` varchar(20) DEFAULT NULL,
  `codcargo` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id_mov_inclusiones`),
  UNIQUE KEY `id_inclusion` (`id_mov_inclusiones`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mov_licencia`
--

CREATE TABLE IF NOT EXISTS `mov_licencia` (
  `id_mov_licencia` int(11) NOT NULL AUTO_INCREMENT,
  `id_mov_contraloria` int(11) DEFAULT NULL,
  `licencia_tipo` int(11) DEFAULT NULL COMMENT '0 con sueldo, 1 sin sueldo',
  `licencia_meses` int(11) DEFAULT NULL COMMENT 'Cantidad de meses de licencia',
  `licencia_dias` int(11) DEFAULT NULL COMMENT 'Cantidad de dias de licencia',
  `licencia_desde` date DEFAULT NULL,
  `licencia_hasta` date DEFAULT NULL,
  `licencia_descripcion` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_mov_licencia`),
  UNIQUE KEY `id_inclusion` (`id_mov_licencia`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mov_modificaciones`
--

CREATE TABLE IF NOT EXISTS `mov_modificaciones` (
  `id_mov_modificaciones` int(11) NOT NULL AUTO_INCREMENT,
  `id_mov_contraloria` int(11) DEFAULT NULL,
  `dias_pagar` decimal(11,2) DEFAULT NULL,
  `suesal` decimal(20,2) DEFAULT NULL,
  `quincenas_pagar` decimal(11,2) DEFAULT NULL,
  `dias` decimal(11,2) DEFAULT NULL,
  `c001` decimal(20,2) DEFAULT NULL,
  `c002` decimal(20,2) DEFAULT NULL,
  `c003` decimal(20,2) DEFAULT NULL,
  `c011` decimal(20,2) DEFAULT NULL,
  `c012` decimal(20,2) DEFAULT NULL,
  `c013` decimal(20,2) DEFAULT NULL,
  `c019` decimal(20,2) DEFAULT NULL,
  `c080` decimal(20,2) DEFAULT NULL,
  `c030` decimal(20,2) DEFAULT NULL,
  `diferencia` decimal(20,2) DEFAULT NULL,
  `diferencia_quincena` decimal(20,2) DEFAULT NULL,
  `tipnom` int(11) DEFAULT NULL,
  `descrip_centro_pago` varchar(50) DEFAULT NULL,
  `codnivel1` varchar(8) DEFAULT NULL,
  `des_car` varchar(50) DEFAULT NULL,
  `grado` varchar(20) DEFAULT NULL,
  `codcargo` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id_mov_modificaciones`),
  UNIQUE KEY `id_inclusion` (`id_mov_modificaciones`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mov_retorno`
--

CREATE TABLE IF NOT EXISTS `mov_retorno` (
  `id_mov_retorno` int(11) NOT NULL AUTO_INCREMENT,
  `id_mov_contraloria` int(11) DEFAULT NULL,
  `retorno_fecha` date DEFAULT NULL,
  `retorno_dias` int(11) DEFAULT NULL,
  `retorno_monto` decimal(11,2) DEFAULT NULL,
  PRIMARY KEY (`id_mov_retorno`),
  UNIQUE KEY `id_inclusion` (`id_mov_retorno`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mov_tipo`
--

CREATE TABLE IF NOT EXISTS `mov_tipo` (
  `id_mov_tipo` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) DEFAULT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_mov_tipo`),
  UNIQUE KEY `id_inclusion` (`id_mov_tipo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Volcado de datos para la tabla `mov_tipo`
--

INSERT INTO `mov_tipo` (`id_mov_tipo`, `codigo`, `descripcion`) VALUES
(1, '1', 'Inclusiones'),
(2, '2', 'Envio de Licencia'),
(3, '3', 'Ajuste al Sueldo segun Planilla'),
(4, '4', 'Retorno de Licencia'),
(5, '5', 'Descuentos'),
(6, '6', 'Adicional, Diferencia y Cancelacion de Pago'),
(7, '7', 'Modificaciones');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveleducativo`
--

CREATE TABLE IF NOT EXISTS `niveleducativo` (
  `IdNivelEducativo` int(11) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(255) NOT NULL,
  PRIMARY KEY (`IdNivelEducativo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Volcado de datos para la tabla `niveleducativo`
--

INSERT INTO `niveleducativo` (`IdNivelEducativo`, `Descripcion`) VALUES
(1, 'PRIMARIA'),
(2, 'PRIMER CICLO'),
(5, 'BACHILLER'),
(6, 'PROFESORADO'),
(8, 'TÃ‰CNICO'),
(10, 'LICENCIATURA'),
(11, 'INGENIERÃA'),
(13, 'POST GRADO'),
(14, 'MAESTRÃA'),
(15, 'DOCTORADO'),
(16, 'DIPLOMADO'),
(17, 'EN DERECHO ADMINISTRATIVO'),
(18, 'NO ESPECIFICADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomacumulados`
--

CREATE TABLE IF NOT EXISTS `nomacumulados` (
  `cod_tac` varchar(6) COLLATE utf8_spanish_ci NOT NULL,
  `des_tac` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `markar` tinyint(4) DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`cod_tac`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nomacumulados`
--

INSERT INTO `nomacumulados` (`cod_tac`, `des_tac`, `markar`, `ee`) VALUES
('AHE', 'ACUMULADO HORAS EXTRAS', 0, 0),
('AN', 'AHORRO NAVIDEÃ‘O', 0, 0),
('ANEX03', 'ANEXO 03', 0, 0),
('CON', 'POR CONCEPTO', 0, 0),
('DECIMO', 'DECIMO TERCER MES', 0, 0),
('DGR', 'DECIMO TERCER MES GR', 0, 0),
('ISLR', 'IMPUESTO SOBRE LA RENTA', 0, 0),
('ISLRGR', 'IMPUESTO SOBRE LA RENTA GR', 0, 0),
('ISP', 'INTERESES S/PRESTACIONES', 0, 0),
('LIQ', 'LIQUIDACION', 0, 0),
('OD', 'OTRAS DEDUCCIONES', 0, 0),
('PRES', 'PRESTAMOS', 0, 0),
('SI', 'SUELDO INTEGRAL', 0, 0),
('SIGR', 'SUELDO INTEGRAL GR', 0, 0),
('SIPE', 'SIPE', 0, 0),
('ST', 'SOBRETIEMPO', 0, 0),
('VAC', 'VACACIONES', 0, 0),
('VACGR', 'VACACIONES GR', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomacumulados_det`
--

CREATE TABLE IF NOT EXISTS `nomacumulados_det` (
  `ceduda` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `ficha` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `anioa` int(4) NOT NULL,
  `mesa` int(2) NOT NULL,
  `dia` int(2) NOT NULL,
  `cod_tac` varchar(7) CHARACTER SET latin1 NOT NULL,
  `montototal` float(17,2) NOT NULL,
  `montobase` float(17,2) NOT NULL,
  `refer` float(17,2) NOT NULL,
  `montoresul` float(17,2) NOT NULL,
  `codnom` int(5) NOT NULL,
  `tipnom` int(11) NOT NULL,
  `operacion` varchar(2) CHARACTER SET latin1 NOT NULL,
  `codcon` int(5) NOT NULL,
  `unidad` varchar(11) CHARACTER SET latin1 NOT NULL,
  `tipcon` int(2) NOT NULL,
  `sfecha` varchar(9) CHARACTER SET latin1 NOT NULL,
  `montootros` float(17,2) NOT NULL,
  `ee` int(2) NOT NULL,
  `numcontrol` int(6) NOT NULL,
  `codsuc` int(6) NOT NULL,
  `coddir` int(6) NOT NULL,
  `codvp` int(6) NOT NULL,
  `codger` int(6) NOT NULL,
  `coddep` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomaumentos`
--

CREATE TABLE IF NOT EXISTS `nomaumentos` (
  `codlogro` int(11) NOT NULL AUTO_INCREMENT,
  `descrip` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`codlogro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomaumentos_det`
--

CREATE TABLE IF NOT EXISTS `nomaumentos_det` (
  `cod_aumento` int(6) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(10) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `estatus` varchar(9) NOT NULL,
  `fecha` date NOT NULL,
  `fecha_aplica` date NOT NULL,
  `monto` decimal(6,0) NOT NULL,
  `cod_nomina` varchar(2) NOT NULL,
  `cod_categoria` varchar(2) NOT NULL,
  `cod_cargo` varchar(12) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `ficha` int(6) DEFAULT NULL,
  `usuario` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`cod_aumento`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='registros de los aumentos realizados o a realizar al persona' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nombancos`
--

CREATE TABLE IF NOT EXISTS `nombancos` (
  `cod_ban` int(11) NOT NULL,
  `des_ban` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `suc_ban` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `direccion` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `gerente` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `cuentacob` varchar(22) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipocuenta` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `textoinicial` mediumtext COLLATE utf8_spanish_ci,
  `textofinal` mediumtext COLLATE utf8_spanish_ci,
  `markar` tinyint(4) DEFAULT NULL,
  `cod_gban` int(11) NOT NULL,
  `ctacon` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  `ruta` varchar(9) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`cod_ban`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nombancos`
--

INSERT INTO `nombancos` (`cod_ban`, `des_ban`, `suc_ban`, `direccion`, `gerente`, `cuentacob`, `tipocuenta`, `textoinicial`, `textofinal`, `markar`, `cod_gban`, `ctacon`, `ee`, `ruta`) VALUES
(1, 'BAC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nombaremos`
--

CREATE TABLE IF NOT EXISTS `nombaremos` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `tipodato` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `nombaremos`
--

INSERT INTO `nombaremos` (`codigo`, `descripcion`, `tipodato`) VALUES
(1, 'Impuesto Sobre la Renta', 'Otros');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomcalendarios_empresa`
--

CREATE TABLE IF NOT EXISTS `nomcalendarios_empresa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod_empresa` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `dia_fiesta` int(11) NOT NULL,
  `descripcion_dia_fiesta` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomcalendarios_personal`
--

CREATE TABLE IF NOT EXISTS `nomcalendarios_personal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod_empresa` int(11) NOT NULL,
  `ficha` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `fecha` date NOT NULL,
  `dia_fiesta` int(11) NOT NULL,
  `descripcion_dia_fiesta` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `turno_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ficha` (`ficha`,`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomcalendarios_tiposnomina`
--

CREATE TABLE IF NOT EXISTS `nomcalendarios_tiposnomina` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod_empresa` int(11) NOT NULL,
  `cod_tiponomina` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `dia_fiesta` int(11) NOT NULL,
  `descripcion_dia_fiesta` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cod_tiponomina` (`cod_tiponomina`,`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomcampos_adicionales`
--

CREATE TABLE IF NOT EXISTS `nomcampos_adicionales` (
  `archivo` varchar(15) COLLATE utf8_spanish_ci DEFAULT NULL,
  `id` int(11) NOT NULL,
  `descrip` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `etiqueta` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipo` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codorgh` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `valdefecto1` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `particular` tinyint(4) DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  `busca` tinyint(1) DEFAULT NULL,
  `tipocamposadic` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nomcampos_adicionales`
--

INSERT INTO `nomcampos_adicionales` (`archivo`, `id`, `descrip`, `etiqueta`, `tipo`, `codorgh`, `valdefecto1`, `particular`, `ee`, `busca`, `tipocamposadic`) VALUES
('nompersonal', 1, 'GASTOS DE REPRESENTACION', 'GASTOS DE REPRESENTACION', 'N', NULL, '', 0, 0, 0, 3),
('nompersonal', 2, 'COMBUSTIBLE', 'COMBUSTIBLE', 'N', NULL, '', 0, 0, 0, 3),
('nompersonal', 3, 'DIETAS', 'DIETAS', 'N', NULL, '', 0, 0, 0, 3),
('nompersonal', 4, 'EMPLEADO DECLARA (SI/NO) ISLR', '', 'A', NULL, 'NO', 0, 0, 0, 3),
('nompersonal', 5, 'Marcacion', 'MARCA', 'A', NULL, 'SI', 0, 0, 0, 3),
('nompersonal', 20, 'Talla Camisa', 'camisa', 'A', NULL, '', 0, 0, 0, 3),
('nompersonal', 21, 'Talla Pantalon', 'pantalon', 'N', NULL, '', 0, 0, 0, 3),
('nompersonal', 22, 'Talla Chaqueta', 'chaqueta', 'A', NULL, '', 0, 0, 0, 3),
('nompersonal', 23, 'Talla Calzado', 'calzado', 'N', NULL, '', 0, 0, 0, 3),
('nompersonal', 24, 'Talla Bata Laboratorio', 'bata', 'A', NULL, '', 0, 0, 0, 3),
('nompersonal', 25, 'Talla Mono Deportivo', 'mono', 'N', NULL, '', 0, 0, 0, 3),
('nompersonal', 50, 'IMPUESTO MENSUAL', 'IMPUESTOMENSUAL', 'A', NULL, 'SI', 0, 0, 0, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomcampos_adic_personal`
--

CREATE TABLE IF NOT EXISTS `nomcampos_adic_personal` (
  `ficha` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `id` int(11) NOT NULL,
  `valor` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `mascara` varchar(9) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipo` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codorgd` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codorgh` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  `tiponom` int(11) NOT NULL,
  PRIMARY KEY (`ficha`,`id`,`tiponom`),
  KEY `fc_idx_133` (`codorgd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomcargos`
--

CREATE TABLE IF NOT EXISTS `nomcargos` (
  `cod_cargo` int(11) NOT NULL AUTO_INCREMENT,
  `cod_car` varchar(12) COLLATE utf8_spanish_ci NOT NULL,
  `des_car` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `grado` varchar(2) COLLATE utf8_spanish_ci NOT NULL,
  `perfil` mediumtext COLLATE utf8_spanish_ci,
  `markar` tinyint(4) DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  `gremio` int(11) NOT NULL,
  `clave` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `sueldo` decimal(10,2) NOT NULL,
  `antiguedad` decimal(10,2) NOT NULL,
  `zona_apartada` decimal(10,2) NOT NULL,
  `otros` decimal(10,2) NOT NULL,
  PRIMARY KEY (`cod_cargo`),
  UNIQUE KEY `cod_car` (`cod_car`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `nomcargos`
--

INSERT INTO `nomcargos` (`cod_cargo`, `cod_car`, `des_car`, `grado`, `perfil`, `markar`, `ee`, `gremio`, `clave`, `sueldo`, `antiguedad`, `zona_apartada`, `otros`) VALUES
(1, '1', 'Cargo', '', NULL, NULL, NULL, 0, NULL, 0.00, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomcategorias`
--

CREATE TABLE IF NOT EXISTS `nomcategorias` (
  `codorg` int(10) unsigned NOT NULL,
  `descrip` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `gr` varchar(6) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  `ocupacion` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`codorg`),
  KEY `fc_idx_22` (`descrip`),
  KEY `fc_idx_23` (`gr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nomcategorias`
--

INSERT INTO `nomcategorias` (`codorg`, `descrip`, `gr`, `ee`, `ocupacion`) VALUES
(1, 'CARRERA ADMINISTRATIVA', '', 0, ''),
(2, 'PROFESIONALES Y TECNICOS DE LA SALUD', '', 0, ''),
(3, 'CARGOS DE LIBRE NOMBRAMIENTO Y REMOCION', '', 0, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomchequera`
--

CREATE TABLE IF NOT EXISTS `nomchequera` (
  `chequera_id` int(11) NOT NULL AUTO_INCREMENT,
  `cantidad` int(5) NOT NULL,
  `inicio` int(32) NOT NULL,
  `situacion` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `banco` smallint(16) NOT NULL,
  PRIMARY KEY (`chequera_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `nomchequera`
--

INSERT INTO `nomchequera` (`chequera_id`, `cantidad`, `inicio`, `situacion`, `banco`) VALUES
(1, 1, 25, 'D', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomcheques`
--

CREATE TABLE IF NOT EXISTS `nomcheques` (
  `cheque_id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(2) COLLATE utf8_spanish_ci NOT NULL,
  `cheque` int(32) NOT NULL,
  `beneficiario` varchar(120) COLLATE utf8_spanish_ci NOT NULL,
  `monto` decimal(20,5) NOT NULL,
  `cedula_rif` varchar(12) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` date NOT NULL,
  `fecha_anulacion` date DEFAULT NULL,
  `justificacion` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `chequera` smallint(16) NOT NULL,
  `concepto` varchar(1000) COLLATE utf8_spanish_ci DEFAULT NULL,
  `log_usr` varchar(12) COLLATE utf8_spanish_ci NOT NULL,
  `tipo` int(2) NOT NULL COMMENT '1 = neto emp, 2 = pago acreedor',
  `codnom` int(6) NOT NULL,
  `personal_id` int(11) NOT NULL,
  `codcon` int(11) NOT NULL,
  PRIMARY KEY (`cheque_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `nomcheques`
--

INSERT INTO `nomcheques` (`cheque_id`, `status`, `cheque`, `beneficiario`, `monto`, `cedula_rif`, `fecha`, `fecha_anulacion`, `justificacion`, `chequera`, `concepto`, `log_usr`, `tipo`, `codnom`, `personal_id`, `codcon`) VALUES
(1, 'D', 25, '', 0.00000, NULL, '0000-00-00', NULL, NULL, 1, NULL, '', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomconceptos`
--

CREATE TABLE IF NOT EXISTS `nomconceptos` (
  `codcon` int(11) NOT NULL,
  `descrip` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipcon` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `unidad` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ctacon` varchar(22) COLLATE utf8_spanish_ci DEFAULT NULL,
  `contractual` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `impdet` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `proratea` tinyint(1) DEFAULT NULL,
  `usaalter` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `descalter` mediumtext COLLATE utf8_spanish_ci,
  `formula` mediumtext COLLATE utf8_spanish_ci,
  `modifdef` tinyint(1) DEFAULT NULL,
  `markar` tinyint(4) DEFAULT NULL,
  `tercero` tinyint(4) DEFAULT NULL,
  `ccosto` tinyint(4) DEFAULT NULL,
  `codccosto` int(11) DEFAULT NULL,
  `debcre` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `bonificable` tinyint(1) DEFAULT NULL,
  `htiempo` tinyint(1) DEFAULT NULL,
  `valdefecto` decimal(17,2) DEFAULT NULL,
  `con_cu_cc` tinyint(4) DEFAULT NULL,
  `con_mcun_cc` tinyint(4) DEFAULT NULL,
  `con_mcuc_cc` tinyint(4) DEFAULT NULL,
  `con_cu_mccn` tinyint(4) DEFAULT NULL,
  `con_cu_mccc` tinyint(4) DEFAULT NULL,
  `con_mcun_mccn` tinyint(4) DEFAULT NULL,
  `con_mcuc_mccc` tinyint(4) DEFAULT NULL,
  `con_mcun_mccc` tinyint(4) DEFAULT NULL,
  `con_mcuc_mccn` tinyint(4) DEFAULT NULL,
  `nivelescuenta` tinyint(4) DEFAULT NULL,
  `nivelesccosto` tinyint(4) DEFAULT NULL,
  `semodifica` tinyint(1) DEFAULT NULL,
  `verref` tinyint(1) DEFAULT NULL,
  `vermonto` tinyint(1) DEFAULT NULL,
  `particular` tinyint(4) DEFAULT NULL,
  `montocero` tinyint(1) DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  `fmodif` tinyint(1) DEFAULT NULL,
  `aplicaexcel` tinyint(4) DEFAULT NULL,
  `descripexcel` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ctacon1` varchar(22) COLLATE utf8_spanish_ci DEFAULT NULL,
  `carga_masiva` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `orden_reporte_contable` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`codcon`),
  KEY `fc_idx_53` (`descrip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nomconceptos`
--

INSERT INTO `nomconceptos` (`codcon`, `descrip`, `tipcon`, `unidad`, `ctacon`, `contractual`, `impdet`, `proratea`, `usaalter`, `descalter`, `formula`, `modifdef`, `markar`, `tercero`, `ccosto`, `codccosto`, `debcre`, `bonificable`, `htiempo`, `valdefecto`, `con_cu_cc`, `con_mcun_cc`, `con_mcuc_cc`, `con_cu_mccn`, `con_cu_mccc`, `con_mcun_mccn`, `con_mcuc_mccc`, `con_mcun_mccc`, `con_mcuc_mccn`, `nivelescuenta`, `nivelesccosto`, `semodifica`, `verref`, `vermonto`, `particular`, `montocero`, `ee`, `fmodif`, `aplicaexcel`, `descripexcel`, `ctacon1`, `carga_masiva`, `orden_reporte_contable`) VALUES
(90, 'SALARIO NO PAGADO', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '$MONTO=$REF;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 'SUELNP', '', 'N', 0),
(91, 'VACACIONES PROPORCIONALES', 'A', 'M', '640000000', '1', 'S', 1, '0', '', '$T01=acumcomvacfrac("VAC",$FECHAINGRESO,$FECHAFINNOM);\r\n$MONTO=$T01/11;\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 'VACPR', '', 'N', 0),
(92, 'XIII MES PROPORCIONAL', 'A', 'M', '640000000', '1', 'S', 1, '0', '', 'echo$T01 = acumcomtip("SI",$FECHANOMINA,$FECHAFINNOM);\r\n$MONTO = ($T01/4)/3;\r\n\r\n\r\n#$T01 = acumcomtip("SI",$FECHANOMINA,$FECHAFINNOM);\r\n#echo$T02 = ACUMCOM(102,fecha_inicio,fecha_fin);\r\n#$REF = $T01-$T02;    \r\n#$T03=$REF+CONCEPTO(99);\r\n#$MONTO = $T03/ 12;     \r\n\r\n#$T01 = acumcomtip("SI","2014-12-08","2015-04-10");\r\n#echo $REF = acumcomtip("SI",$FECHANOMINA,$FECHAFINNOM);\r\n#$REF = $T01+CONCEPTO(99); \r\n#$T02=$REF / 12;\r\n#$MONTO = $T02;\r\n\r\n#echo $T01 = acumcomtip("SI","2014-12-11","2015-04-10");\r\n#$T02 = ACUMCOM("102",$FECHANOMINA,$FECHAFINNOM);\r\n#$T03 = CONCEPTO(99);\r\n#$REF = ($T01 - $T02) + $T03;\r\n#$MONTO = $REF/ 12;\r\n#$MONTO = $T01;\r\n \r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, '', '', 'N', 0),
(93, 'PRIMA ANTIGUEDAD', 'A', 'M', '640000000', '1', 'S', 1, '0', '', '$T01=acumcomvacfrac("SI",$FECHAINGRESO,$FECHAFINNOM);\r\n$MONTO=$T01*0.0192;\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 'PA', '', 'N', 0),
(94, 'INDEMNIZACION', 'A', 'M', '640000000', '1', 'S', 1, '0', '', '# Antiguedad en Dias\r\n$T01=ANTIGUEDAD($FECHAINGRESO,$FECHANOMINA,"D");\r\n$T02=$T01/365;\r\n\r\n#Salario en los ultimos 6 meses\r\n$T03= acumsalseismeses("SI",$FECHANOMINA,$FECHANOMINA);\r\n$T04=($T03/6)/4.333;\r\n\r\n#Salario ultimo fecha\r\n$T05= salariointegralultimomes("SI",$FECHANOMINA,$FECHANOMINA);\r\n$T06=$T05/4.333;\r\n\r\n$T07=SI("$T02>10",($T02-10),0);\r\n$T08=SI("$T06>$T04",$T06,$T04);\r\n\r\n$MONTO=SI("$T02>10",(($T08*10*3.4)+($T08*$T07)),($T08*$T02*3.4));\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, '', '000000000000000', 'N', 0),
(95, 'PREAVISO', 'A', 'M', '640000000', '1', 'S', 1, '0', '', '$MONTO=$SALARIO*$REF;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 'PRE', '', 'N', 0),
(100, 'SALARIO', 'A', 'M', '640000000', '1', 'S', 1, '0', '', '$T01=ANTIGUEDAD($FECHAINGRESO,$FECHAFINNOM,"D");\r\n$REF=SI("$T01>=12",12,$T01);\r\n$T02=(($SUELDO/24)*$REF);\r\n$MONTO=$T02;\r\n#$MONTO=($SUELDO/2);', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'SUEL', '000000000000000', 'N', 0),
(102, 'XIII MES', 'A', 'M', '640000000', '1', 'S', 1, '0', '', '$T01=acumcomtip("SI",$FECHANOMINA$,FECHAFINNOM);\r\n$MONTO=$T01/12;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, '', '000000000000000', 'N', 0),
(103, 'HORAS SEMANALES', 'A', 'M', '640000000', '1', 'S', 1, '0', '', '# CANTIDAD_HORAS_TRABAJADAS;\r\n$T01=$REF;\r\n\r\n# 12 = meses, 52 = cantidad de semanas al aÃ±o, \r\n$MONTO=(($SUELDO*12)/52)/$T01;\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, '', '', 'N', 0),
(104, 'HORAS TRABAJADAS', 'A', 'M', '640000000', '1', 'S', 1, '0', '', '$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,1);\r\n$MONTO=$REF*CAMPOADICIONALPER(4);', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HRS TR', '', 'N', 0),
(106, 'HORA EXTRA DIURNA (25%)', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,2);\r\n\r\n# CALCULO DE HORA EXTRA DIURNA\r\n$T01=(($SUELDO)/24)/8;\r\n$T02=$T01*1.25;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF);\r\n\r\n\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'H EXTR', '000000000000000', 'N', 0),
(107, 'HORA EXTRA NOCTURNA (50%)', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,5);\r\n\r\n# CALCULO DE HORA EXTRA NOCTURNA\r\n$T01=(($SUELDO)/24)/8;\r\n$T02=$T01*1.5;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF);\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'EX NOC', '000000000000000', 'N', 0),
(108, 'HORA EXTRA DIURNA RECARGO (25%/75%)', 'A', 'M', '478000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,4);\r\n\r\n# CALCULO DE HORA EXTRA DIURNA Extendida\r\n$T01=(($SUELDO)/24)/8;\r\n$T02=$T01*1.25*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF);\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, '', '000000000000000', 'N', 0),
(109, 'HORA EXTRA NOCTURNAS RECARGO (50 / 75%)', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,6);\r\n\r\n# CALCULO DE HORA EXTRA EXT Nocturna\r\n\r\n$T01=(($SUELDO)/24)/8;\r\n$T02=$T01*1.50*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF);\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'EX EXT', '000000000000000', 'N', 0),
(111, 'DIAS DOMINGOS', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,3);\r\n\r\n# CALCULO DE HORA \r\n$T01=(($SUELDO*12)/52)/44;\r\n$T02=$T01*0.50;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF);\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'DOM', '000000000000000', 'N', 0),
(112, 'HORA EXTRA DOMINGO DIURNA (50 / 25%)', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,7);\r\n\r\n# CALCULO DE HORA EXTRA Ext Domingo DIURNA\r\n$T01=(($SUELDO)/24)/8;\r\n$T02=$T01*1.25*1.50;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF);\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'DOM EX D', '000000000000000', 'N', 0),
(113, 'HORA EXTRA DOMINGO NOCT(50 / 50%)', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,8);\r\n\r\n# CALCULO DE HORA EXTRA DIURNA\r\n\r\n$T01=(($SUELDO)24/8;\r\n$T02=$T01*1.50*1.50;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF);\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEDN', '000000000000000', 'N', 0),
(114, 'VACACIONES', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '$T01=acumcomvac("SI",$FECHAINGRESO,$FECHAFINNOM);\r\n$MONTO=$T01/11;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, '', '', 'N', 0),
(115, 'DIAS PENDIENTES', 'A', 'H', '640000000', '1', 'S', 1, '0', '', '#DIAS PENDIENTE EN HORAS\r\n\r\n$T01=$REF;\r\n$T02=($SUELDO/4.333)/48;\r\n$T03=$T02*$T01;\r\n$MONTO=$T03;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, '', '', 'N', 0),
(119, 'DIA DESCANSO', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '#ESTE CONCEPTO APLICA PARA CUANDO EL EMPLEADO TRABAJA EN SU DIA DE DESCANSO Y #NO SE LE DA\r\n#SU DIA LIBRE \r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,34);\r\n# CALCULO DE DIA DESCANSO\r\n$T01=(($SUELDO)/24)/8;\r\n$T02=$T01*0.50;\r\n#RESULTADO\r\n$MONTO=($T02*$REF);\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'DLT', '000000000000000', 'N', 0),
(120, 'DIA NACIONAL EXTRA NOCTURNA', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,12);\r\n\r\n# CALCULO DE HORA EXTRA NACIONAL NOCTURNO\r\n$T01=(($SUELDO)/24)/8;\r\n$T02=$T01*1.5*2.5;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF);\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'DNEN', '000000000000000', 'N', 0),
(121, 'DIA NACIONAL EXTRAS DIURNAS', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,11);\r\n\r\n# CALCULO DE HORA EXTRA Nacional Diurno\r\n$T01=(($SUELDO*12)/52)/44;\r\n$T02=$T01*1.25*2.50;\r\n\r\n#RESULTADO\r\n$MONTO=$T02*$REF;\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'DNED', '', 'N', 0),
(122, 'DIA NACIONAL', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,10);\r\n\r\n# CALCULO DE Dia Nacional\r\n$T01=(($SUELDO)/24)/8;\r\n$T02=$T01*1.50;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF);\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'NAC', '000000000000000', 'N', 0),
(123, 'Hora Extra Mixto Diurna (50%)', 'A', 'M', '640000000', '', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,30);\r\n\r\n# CALCULO DE HORA EXTRA Mixta DIURNA\r\n\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*1.50;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEMD', '000000000000000', 'N', 0),
(124, 'Hora Extra Mixto Diurna Recargo (50/75%)', 'A', 'M', '640000000', '', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,31);\r\n\r\n# CALCULO DE HORA EXTRA DIURNA\r\n$T01=(($SUELDO)/24)/8;\r\n$T02=$T01*1.50*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF);\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEMDR', '000000000000000', 'N', 0),
(125, 'Hora Extra Mixto Nocturna (75%)', 'A', 'M', '640000000', '', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,32);\r\n\r\n# CALCULO DE HORA EXTRA DIURNA\r\n$T01=(($SUELDO*12)/52)/44;\r\n$T02=$T01*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEMN', '000000000000000', 'N', 0),
(126, 'Hora Extra Mixto Nocturna Recargo (75/75%)', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,33);\r\n\r\n# CALCULO DE HORA EXTRA DIURNA\r\n$T01=(($SUELDO*12)/52)/44;\r\n$T02=$T01*1.75*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEEMN', '', 'N', 0),
(127, 'Hora Extra Diurna Domingo Recargo (50/25/75%)', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,14);\r\n\r\n# CALCULO DE HORA EXTRA DIURNA\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*1.50*1.25*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEEDD', '', 'N', 0),
(128, 'Hora Extra Mixto Diurna Domingo (50/50%)', 'A', 'M', '640000000', '', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,16);\r\n\r\n# CALCULO DE HORA EXTRA DIURNA\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*1.50*1.50;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEMDD', '000000000000000', 'N', 0),
(129, 'Hora Extra Mixto Diurna Domingo Recargo (50/50/75%)', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,17);\r\n\r\n# CALCULO DE HORA EXTRA DIURNA\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*1.50*1.50*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEMDD', '', 'N', 0),
(130, 'Hora Extra Mixto Nocturna Domingo (50/75%)', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,18);\r\n\r\n# CALCULO DE HORA EXTRA DIURNA\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*1.50*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEMND', '', 'N', 0),
(131, 'Hora Extra Mixto Nocturna Domingo Recargo(50/75/75%)', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,19);\r\n\r\n# CALCULO DE HORA EXTRA DIURNA\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*1.50*1.75*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEMNDR', '', 'N', 0),
(132, 'Hora Extra Nocturna Domingo Recargo (50/50/75%)', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,15);\r\n\r\n# CALCULO DE HORA EXTRA\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*1.50*1.50*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HENDR', '', 'N', 0),
(133, 'Hora Extra Diurna Nacional Recargo (150/25/75%)', 'A', 'M', '640000000', '', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,);\r\n\r\n# CALCULO DE HORA EXTRA \r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*2.50*1.25*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEDNR', '', 'N', 0),
(134, 'Hora Extra Mixto Diurna Nacional (150/50%)', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,23);\r\n\r\n# CALCULO DE HORA EXTRA \r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*2.50*1.50;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEMDN', '', 'N', 0),
(135, 'Hora Extra Mixto Diurna Nacional Recargo (150/50/75%)', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,24);\r\n\r\n# CALCULO DE HORA EXTRA DIURNA\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*2.50*1.50*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEMDNR', '', 'N', 0),
(136, 'Hora Extra Mixto Nocturna Nacional (150/75%)', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,25);\r\n\r\n# CALCULO DE HORA EXTRA\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*2.50*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEMNN', '', 'N', 0),
(138, 'Hora Extra Mixto Nocturna Nacional Recargo(150/75/75%)', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,26);\r\n\r\n# CALCULO DE HORA EXTRA \r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*2.50*1.75*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEEMNN', '', 'N', 0),
(139, 'Hora Extra Nocturna Nacional Recargo (150/50/75%)', 'A', 'M', '640000000', '', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,30);\r\n\r\n# CALCULO DE HORA EXTRA\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*2.50*1.50*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HENNR', '', 'N', 0),
(140, 'INCAPACIDAD', 'A', 'H', '640000000', '', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n$T01=$REF;\r\n$T02=($SUELDO/4.333)/48;\r\n$T03=$T02*$T01;\r\n$MONTO=$T03;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'INCAP', '', 'N', 0),
(141, 'COMISION', 'A', 'M', '640000000', '', 'S', 0, '0', '', '$T01=$REF;\r\n$MONTO=$T01;\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'CLUB', '000000000000000', 'N', 0),
(142, 'HORA EXTRA DIURNA Descanso (50 / 25%)', 'A', 'M', '640000000', '', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,38);\r\n\r\n# CALCULO DE HORA EXTRA Ext Descanso DIURNA\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*1.25*1.50;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEDDS', '', 'N', 0),
(143, 'Hora Extra Diurna Descanso Recargo(50/25/75%)', 'A', 'M', '640000000', '', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,);\r\n\r\n# CALCULO DE HORA EXTRA EXT DIURNA Descanso\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*1.50*1.25*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEDDR', '', 'N', 0),
(144, 'Hora Extra Mixto Diurna Descanso (50/50%)', 'A', 'M', '640000000', '', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,40);\r\n\r\n# CALCULO DE HORA Extra Mixto Diurna Descanso\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*1.50*1.50;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEMDDS', '', 'N', 0),
(145, 'GASTOS DE REPRESENTACION', 'A', 'M', '640000000', '', 'S', 0, '0', '', '$T01=CAMPOADICIONALPER(1)/2;\r\n$MONTO=$T01;\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 'GASTOS D', '000000000000000', 'N', 0),
(146, 'ADELANTO DE PAGO', 'D', 'M', '640000000', '', 'S', 1, '0', '', '$MONTO=$REF;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'SUEL', '', 'N', 0),
(147, 'COMBUSTIBLE', 'A', 'M', '629000000', '1', 'S', 0, '0', '', '$T01=CAMPOADICIONALPER(2);\r\n$MONTO=$T01;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'CB', '', 'N', 0),
(148, 'DIETA', 'A', 'M', '629000000', '1', 'S', 0, '0', '', '$T01=CAMPOADICIONALPER(3)/2;\r\n$MONTO=$T01;\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'DT', '', 'N', 0),
(149, 'Hora Extra EXT Mixto Diurna Descanso (50/50/75%)', 'A', 'M', '640000000', '', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,41);\r\n\r\n# CALCULO DE HORA Extra EXT Mixto Diurna Descanso (50/50/75%)\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*1.50*1.50*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEEMDDS', '', 'N', 0),
(150, 'Hora Extra Mixto Nocturna Descanso (50/75%)', 'A', 'M', '640000000', '', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,42);\r\n\r\n# CALCULO DE HORA Extra Mixto Nocturna Descanso (50/75%)\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*1.50*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEMNDS', '', 'N', 0),
(151, 'Hora Extra Mixto Nocturna Descanso Recargo (50/75/75%)', 'A', 'M', '640000000', '', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,43);\r\n\r\n# CALCULO DE Hora Extra EXT Mixto Nocturna Descanso (50/75/75%)\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*1.50*1.75*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEMNDR', '', 'N', 0),
(152, 'Hora Extra Nocturna Descanso (50/50%)', 'A', 'M', '640000000', '', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,39);\r\n\r\n# CALCULO DE Hora Extra Nocturna Descanso (50/50%)\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*1.50*1.50;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEND', '', 'N', 0),
(153, 'Hora Extra Nocturna Descanso Recargo(50/50/75%)', 'A', 'M', '640000000', '', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,);\r\n\r\n# CALCULO DE HORA Extra EXT Nocturna Descanso (50/50/75%)\r\n$T01=(($SUELDO)/26)/8;\r\n$T02=$T01*1.50*1.50*1.75;\r\n\r\n#RESULTADO\r\n$MONTO=($T02*$REF)/2;\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HENDR', '', 'N', 0),
(154, 'HORA EXTRA LLAMADO DE EMERGENCIA', 'A', 'M', '640000000', '', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n$T01=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,21);\r\n$T02=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,28);\r\n$T03=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,35);\r\n$T04=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,45);\r\n\r\n$REF=$T01+$T02+$T03+$T04;\r\n\r\n# CALCULO DE HORA EXTRA \r\n$T05=(($SUELDO)/26)/8;\r\n$T06=SI($REF!="",$REF+3,0);\r\n\r\n#RESULTADO\r\n$MONTO=($T05*$T06)/2;\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'LLEM', '', 'N', 0),
(155, 'HORA DESCANSO INCOMPLETO', 'A', 'M', '640000000', '1', 'S', 0, '0', '', '# CANTIDAD DE HORAS\r\n\r\n$T01=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,22);\r\n$T02=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,29);\r\n$T03=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,36);\r\n$T04=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,46);\r\n\r\n$REF=$T01+$T02+$T03+$T04;\r\n\r\n# CALCULO DE DESCANSO INCOMPLETO\r\n$T05=(($SUELDO)/26)/8;\r\n$T06=SI($REF!="",12-$REF,0);\r\n\r\n#RESULTADO\r\n$MONTO=($T05*$T06)/2;\r\n\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HDI', '', 'N', 0),
(199, 'TARDANZA', 'D', 'H', '640000000', '1', 'S', 0, '0', '', '$REF=HORASTRABAJADAS($FICHA,$FECHANOMINA,$FECHAFINNOM,9);\r\n$T01=(($SUELDO*12)/52)/44;\r\n$MONTO=$T01*$REF;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'TARDN', '000000000000000', 'N', 0),
(200, 'SEGURO SOCIAL (S.S.)', 'D', 'M', '642000000', '1', 'S', 0, '0', '', '#TRABAJADOR: 9.75%\r\n\r\n$T01=0.0975;\r\n\r\n$T02=CONCEPTO(100);\r\n$MONTO=$T02*$T01;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'S.S', '000000000000000', 'N', 0),
(201, 'SEGURO EDUCATIVO (S.E.)', 'D', 'M', '642000000', '1', 'S', 1, '0', '', '#TRABAJADOR: 1.25%\r\n\r\n$T01=0.0125;\r\n\r\n$T02=CONCEPTO(100);\r\n$T04=$T02*$T01; \r\n\r\n$MONTO=$T04;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'S.E.', '000000000000000', 'N', 0),
(202, 'IMPUESTO SOBRE LA RENTA (I.S.R.)', 'D', 'M', '476000000', '', 'S', 0, '0', '', '$T01=PERIODO($FECHANOMINA,$FRECUENCIA);\r\n$T02=27-$T01;\r\n$T03=CONCEPTO(100)+CONCEPTO(106)+CONCEPTO(108)+CONCEPTO(123)+CONCEPTO(124)+CONCEPTO(125)+CONCEPTO(126)+CONCEPTO(107)+CONCEPTO(109)+CONCEPTO(111)+CONCEPTO(112)+CONCEPTO(127)+CONCEPTO(128)+CONCEPTO(129)+CONCEPTO(130)+CONCEPTO(131)+CONCEPTO(113)+CONCEPTO(132)+CONCEPTO(122)+CONCEPTO(121)+CONCEPTO(133)+CONCEPTO(134)+CONCEPTO(135)+CONCEPTO(136)+CONCEPTO(138)+CONCEPTO(120)+CONCEPTO(139)+CONCEPTO(119)+CONCEPTO(142)+CONCEPTO(143)+CONCEPTO(144)+CONCEPTO(149)+CONCEPTO(150)+CONCEPTO(151)+CONCEPTO(152)+CONCEPTO(153)+CONCEPTO(154)+CONCEPTO(155)+CONCEPTO(91)+CONCEPTO(92)+CONCEPTO(102)+CONCEPTO(114)+CONCEPTO(600)+CONCEPTO(603);\r\n\r\n$T04=CONCEPTOSANUAL("608",$FECHANOMINA);\r\n$T08=CONCEPTOSANUAL("609",$FECHANOMINA);\r\n$T09=CONCEPTOSANUAL("610",$FECHANOMINA);\r\n$T10=CONCEPTOSANUAL("600",$FECHANOMINA);\r\n$T11=CONCEPTOSANUAL("100",$FECHANOMINA)-CONCEPTO(100);\r\n$T12=CONCEPTOSANUAL("199",$FECHANOMINA)-CONCEPTO(199);\r\n$T05=($T03*$T02)+$T04+$T08+$T09+$T10+$T11-$T12;\r\n$T06=BAREMO(1,$T05);\r\n$T07=CONCEPTOSANUAL("605",$FECHANOMINA);\r\n\r\n$MONTO=SI("$T05>50000",(((($T05-50000)*($T06/100)+(5850))-$T07)/$T02),((($T05-11000)*($T06/100)-$T07)/$T02));', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'I.S.L.R', '000000000000000', 'N', 0),
(203, 'AUSENCIA', 'D', 'M', '640000000', '', 'S', 1, '0', '', '#AUSENCIA EN HORAS\r\n$T01=$REF;\r\n$T02=($SUELDO/4.333)/44;\r\n$T03=$T02*$T01;\r\n$MONTO=$T03;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'AUSENCIA', '000000000000000', 'N', 0),
(204, 'VALE									', 'D', 'M', '1.001.001.', '1', 'S', 0, '0', '', '$T01=$REF;\r\n$MONTO=$T01;\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'VALE', '', '', 0),
(205, 'SEGURO SOCIAL (S.S.) 7.25%', 'D', 'M', '476000000', '1', 'S', 1, '0', '', '#TRABAJADOR: 7.25%\r\n\r\n$T01=0.0725;\r\n\r\n$T02=CONCEPTO(92)+CONCEPTO(102);\r\n\r\n$MONTO=$T02*$T01;\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'S.S', '', 'N', 0),
(206, 'SEGURO EDUCATIVO (S.E.) LQ', 'D', 'M', '476000000', '1', 'S', 1, '0', '', '#TRABAJADOR: 1.25%\r\n$T01=(CONCEPTO(90)+CONCEPTO(91))*0.0125;\r\n$MONTO=$T01;\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'S.E.', '', 'N', 0),
(207, 'IMPUESTO SOBRE LA RENTA MENSUAL(I.S.R.)', 'D', 'M', '476000000', '', 'S', 0, '0', '', '$T01=PERIODO($FECHANOMINA,$FRECUENCIA);\r\necho $T02=27-$T01;\r\n$T03=CONCEPTO(100)+CONCEPTO(106)+CONCEPTO(108)+CONCEPTO(123)+CONCEPTO(124)+CONCEPTO(125)+CONCEPTO(126)+CONCEPTO(107)+CONCEPTO(109)+CONCEPTO(111)+CONCEPTO(112)+CONCEPTO(127)+CONCEPTO(128)+CONCEPTO(129)+CONCEPTO(130)+CONCEPTO(131)+CONCEPTO(113)+CONCEPTO(132)+CONCEPTO(122)+CONCEPTO(121)+CONCEPTO(133)+CONCEPTO(134)+CONCEPTO(135)+CONCEPTO(136)+CONCEPTO(138)+CONCEPTO(120)+CONCEPTO(139)+CONCEPTO(119)+CONCEPTO(142)+CONCEPTO(143)+CONCEPTO(144)+CONCEPTO(149)+CONCEPTO(150)+CONCEPTO(151)+CONCEPTO(152)+CONCEPTO(153)+CONCEPTO(154)+CONCEPTO(155)+CONCEPTO(91)+CONCEPTO(92)+CONCEPTO(102)+CONCEPTO(114)+CONCEPTO(600)+CONCEPTO(603)+CONCEPTO(141)-(CONCEPTO(199)+CONCEPTO(203));\r\n\r\n$T04=CONCEPTOSANUAL("608",$FECHANOMINA);\r\n$T08=CONCEPTOSANUAL("609",$FECHANOMINA);\r\n$T09=CONCEPTOSANUAL("610",$FECHANOMINA);\r\n$T10=CONCEPTOSANUAL("600",$FECHANOMINA);\r\n$T11=CONCEPTOSANUAL("100",$FECHANOMINA)-CONCEPTO(100);\r\n$T12=CONCEPTOSANUAL("199",$FECHANOMINA)-CONCEPTO(199);\r\n$T05=($T03*$T02)+$T04+$T08+$T09+$T10+$T11-$T12;\r\n$T06=BAREMO(1,$T05);\r\n$T07=CONCEPTOSANUAL("605",$FECHANOMINA);\r\n\r\n$T13=SI("$T05>50000",(((($T05-50000)*($T06/100)+(5850))-$T07)/$T02),((($T05-11000)*($T06/100)-$T07)/$T02));\r\n\r\n$T14=CAMPOADICIONALPER(50);\r\n$MONTO=SI("$T14==SI",($T13*2),0);\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'I.S.L.R', '000000000000000', 'N', 0),
(208, 'IMPUESTO SOBRE LA RENTA QUINCENAL(I.S.R.)', 'D', 'M', '476000000', '', 'S', 0, '0', '', '$T01=PERIODO($FECHANOMINA,$FRECUENCIA);\r\n$T02=27-$T01;\r\n$T03=CONCEPTO(100)+CONCEPTO(106)+CONCEPTO(108)+CONCEPTO(123)+CONCEPTO(124)+CONCEPTO(125)+CONCEPTO(126)+CONCEPTO(107)+CONCEPTO(109)+CONCEPTO(111)+CONCEPTO(112)+CONCEPTO(127)+CONCEPTO(128)+CONCEPTO(129)+CONCEPTO(130)+CONCEPTO(131)+CONCEPTO(113)+CONCEPTO(132)+CONCEPTO(122)+CONCEPTO(121)+CONCEPTO(133)+CONCEPTO(134)+CONCEPTO(135)+CONCEPTO(136)+CONCEPTO(138)+CONCEPTO(120)+CONCEPTO(139)+CONCEPTO(119)+CONCEPTO(142)+CONCEPTO(143)+CONCEPTO(144)+CONCEPTO(149)+CONCEPTO(150)+CONCEPTO(151)+CONCEPTO(152)+CONCEPTO(153)+CONCEPTO(154)+CONCEPTO(155)+CONCEPTO(91)+CONCEPTO(92)+CONCEPTO(102)+CONCEPTO(114)+CONCEPTO(600)+CONCEPTO(603);\r\n\r\n$T04=CONCEPTOSANUAL("608",$FECHANOMINA);\r\n$T08=CONCEPTOSANUAL("609",$FECHANOMINA);\r\n$T09=CONCEPTOSANUAL("610",$FECHANOMINA);\r\n$T10=CONCEPTOSANUAL("600",$FECHANOMINA);\r\n$T11=CONCEPTOSANUAL("100",$FECHANOMINA)-CONCEPTO(100);\r\n$T12=CONCEPTOSANUAL("199",$FECHANOMINA)-CONCEPTO(199);\r\n$T05=($T03*$T02)+$T04+$T08+$T09+$T10+$T11-$T12;\r\n$T06=BAREMO(1,$T05);\r\n$T07=CONCEPTOSANUAL("605",$FECHANOMINA);\r\n\r\n$T13=SI("$T05>50000",(((($T05-50000)*($T06/100)+(5850))-$T07)/$T02),((($T05-11000)*($T06/100)-$T07)/$T02));\r\n\r\n$T14=CAMPOADICIONALPER(50);\r\n$MONTO=SI("$T14==NO",($T13*1),0);\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'I.S.L.R', '000000000000000', 'N', 0),
(250, 'LIQUIDACION', 'A', 'M', '640000000', '', 'S', 1, '0', '', '$MONTO=$REF;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, '', '', 'N', 0),
(501, 'BAC PANAMA', 'D', 'M', '478000000', '1', 'S', 0, '0', '', '$T01=CUOTAPRETIP($FICHA,$FECHANOMINA,$FECHAFINNOM,1);\r\n\r\n$T02=$T01;\r\n\r\n$MONTO=$T02;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'BAC', '', 'N', 0),
(502, 'GLOBAL BANK', 'D', 'M', '478000000', '1', 'S', 1, '0', '', '$T01=CUOTAPRETIP($FICHA,$FECHANOMINA,$FECHAFINNOM,2);\r\n\r\n$T02=$T01;\r\n\r\n$MONTO=$T02;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'GB', '', 'N', 0),
(503, 'BANCO GENERAL', 'D', 'M', '640000000', '1', 'S', 0, '0', '', '$T01=CUOTAPRETIP($FICHA,$FECHANOMINA,$FECHAFINNOM,3);\r\n\r\n$T02=$T01;\r\n\r\n$MONTO=$T02;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'BG', '', 'N', 0),
(504, 'LA HIPOTECARIA', 'D', 'M', '478000000', '1', 'S', 0, '0', '', '$T01=CUOTAPRETIP($FICHA,$FECHANOMINA,$FECHAFINNOM,4);\r\n\r\n$T02=$T01;\r\n\r\n$MONTO=$T02;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'LAH', '', 'N', 0),
(505, 'MULTIBANK', 'D', 'M', '478000000', '1', 'S', 0, '0', '', '$T01=CUOTAPRETIP($FICHA,$FECHANOMINA,$FECHAFINNOM,5);\r\n\r\n$T02=$T01;\r\n\r\n$MONTO=$T02;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'MB', '', 'N', 0),
(506, 'BANVIVIENDA', 'D', 'M', '478000000', '1', 'S', 0, '0', '', '$T01=CUOTAPRETIP($FICHA,$FECHANOMINA,$FECHAFINNOM,6);\r\n\r\n$T02=$T01;\r\n\r\n$MONTO=$T02;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'BV', '', 'N', 0),
(507, 'FINACIERA EL SALVADOR', 'D', 'M', '478000000', '1', 'S', 0, '0', '', '$T01=CUOTAPRETIP($FICHA,$FECHANOMINA,$FECHAFINNOM,12);\r\n\r\n$T02=$T01;\r\n\r\n$MONTO=$T02;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'PF', '000000000000000', 'N', 0),
(508, 'FINANCIERA NATA, S.A. (FINASA)', 'D', 'M', '478000000', '1', 'S', 0, '0', '', '$T01=CUOTAPRETIP($FICHA,$FECHANOMINA,$FECHAFINNOM,8);\r\n\r\n$T02=$T01;\r\n\r\n$MONTO=$T02;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'fi', '000000000000000', 'N', 0),
(509, 'CAJA DE AHORRO', 'D', 'M', '478000000', '1', 'S', 0, '0', '', '$T01=CUOTAPRETIP($FICHA,$FECHANOMINA,$FECHAFINNOM,9);\r\n\r\n$T02=$T01;\r\n\r\n$MONTO=$T02;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'CAH', '', 'N', 0),
(510, 'BANISTMO, S. A.', 'D', 'M', '640000000', '1', 'S', 0, '0', '', '$T01=CUOTAPRETIP($FICHA,$FECHANOMINA,$FECHAFINNOM,10);\r\n\r\n$T02=$T01;\r\n\r\n$MONTO=$T02;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'PU', '000000000000000', 'N', 0),
(511, 'BANESCO', 'D', 'M', '640000000', '1', 'S', 0, '0', '', '$T01=CUOTAPRETIP($FICHA,$FECHANOMINA,$FECHAFINNOM,11);\r\n\r\n$T02=$T01;\r\n\r\n$MONTO=$T02;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'BA', '000000000000000', 'N', 0),
(512, 'POR USAR', 'D', 'M', '110101001', '1', 'S', 0, '0', '', '$T01=CAMPOADICIONALPER(5);\r\n$MONTO=$T01;\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'PU', '', '', 0),
(513, 'POR USAR', 'D', 'M', '110101001', '1', 'S', 0, '0', '', '$T01=CAMPOADICIONALPER(4);\r\n$MONTO=$T01;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'PU', '', '', 0),
(514, 'POR USAR', 'D', 'M', '110101001', '1', 'S', 0, '0', '', '$T01=CAMPOADICIONALPER(8);\r\n$MONTO=$T01;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'PU', '', '', 0),
(515, 'SUKIMOTOR', 'D', 'M', '640000000', '1', 'S', 1, '0', '', '$MONTO=$REF;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'PU', '000000000000000', 'N', 0),
(600, 'HORAS EXTRAS', 'A', 'M', '640000000', '', 'S', 0, '0', '', '$MONTO=$REF;\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HE', '', 'N', 0),
(601, 'IMPUESTO SOBRE LA RENTA', 'D', 'M', '476000000', '', 'S', 0, '0', '', '$MONTO=$REF;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'I.S.L.R', '', 'N', 0),
(602, 'HORAS EXTRAS GR', 'A', 'M', '640000000', '', 'S', 0, '0', '', '$MONTO=$REF;\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'HEGR', '', 'N', 0),
(603, 'AGUINALDOS', 'A', 'M', '640000000', '', 'S', 0, '0', '', '$MONTO=$REF;\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'AG', '', 'N', 0),
(604, 'AJUSTE', 'D', 'M', '640000000', '', 'S', 0, '0', '', '$MONTO=$REF;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'AJUSTE', '', 'N', 0),
(605, 'IMPUESTO SOBRE LA RENTA ACUMULADO', 'D', 'M', '476000000', '', 'S', 0, '0', '', '$MONTO=$REF;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'ISLRA', '', 'N', 0),
(608, 'Acumulado de Salarios', 'A', 'M', '476000000', '', 'S', 0, '0', '', '$MONTO=$REF;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'AS', '', 'S', 0),
(609, 'Acumulado Decimo Salario', 'A', 'M', '476000000', '', 'S', 0, '0', '', '$MONTO=$REF;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'AD', '', 'S', 0),
(610, 'Acumulado de Vacaciones', 'A', 'M', '476000000', '', 'S', 0, '0', '', '$MONTO=$REF;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'AD', '', 'N', 0),
(3000, 'Patronal C.S.S.', 'P', 'M', '476000000', '1', 'N', 1, '0', '', '#EMPLEADOR: % 12.25\r\n$T01=0.1225;\r\n\r\n$T02=SI(CONCEPTO(102)!="",CONCEPTO(102),0); # XIII MES\r\n$T03=$T02*$T01;\r\n\r\n\r\n$T04=($SUELDO/2)*$T01;\r\n\r\n$MONTO=$T03+$T04;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'AP SS', '', 'N', 0),
(3001, 'Patronal  S.E', 'P', 'M', '476000000', '1', 'N', 0, '0', '', '#TRABAJADOR: 1.50%\r\n$T01=0.015;\r\n\r\n$T02=SI(CONCEPTO(102)!="",CONCEPTO(102),0); # XIII MES\r\n$T00=mes($FECHANOMINA);\r\n$T03 = SI($T00!=12,$T02*$T01,0);\r\n\r\n\r\n$T04=($SUELDO/2)*$T01;\r\n\r\n$MONTO=$T03+$T04;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 'AP SE', '', 'N', 0),
(9000, 'ACUMULADO INTEGRAL', 'A', 'M', '640000000', '', 'S', 0, '0', '', '$MONTO= $REF;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, '', '', 'N', 0),
(9001, 'ACUMULADO XII MES', 'A', 'M', '640000000', '', 'S', 0, '1', '', '$T01= $REF /12;\r\n$MONTO= $T01;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, '', '', 'N', 0),
(9002, 'ACUMULADO DE VACACIONES', 'A', 'M', '640000000', '', 'S', 0, '0', '', '$MONTO= $REF/11;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, '', '', 'N', 0),
(9003, 'SEGURO SOCIAL', 'D', 'M', '476000000', '1', 'S', 1, '0', '', '$T01= CONCEPTO(9001) * 0.0725;\r\n$T02= CONCEPTO(9002) * 0.0975;\r\n\r\n$MONTO=$T01+$T02;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'S.S', '', 'N', 0),
(9004, 'SEGURO EDUCATIVO', 'D', 'M', '640000000', '1', 'S', 1, '0', '', '$T02= CONCEPTO(9002) * 0.0125;\r\n\r\n$MONTO=$T02;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'S.S', '', 'N', 0),
(9005, 'Prima de Antiguedad', 'P', 'M', '476000000', '1', 'N', 0, '0', '', '$REF=0.01923;\r\n$T01=($SUELDO+CAMPOADICIONALPER(1)) /2;\r\n$MONTO=$T01*$REF;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'PA', '', 'N', 0),
(9006, 'IndemnizaciÃ³n', 'P', 'M', '476000000', '1', 'N', 0, '0', '', '$REF=0.00327;\r\n$T01=($SUELDO+CAMPOADICIONALPER(1))/2;\r\n$MONTO=$T01*$REF;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'Indemniz', '', 'N', 0),
(9007, 'DÃ©cimo tercer Mes Salario', 'P', 'M', '476000000', '1', 'N', 0, '0', '', '#$REF=$SUELDO/2;\r\n\r\n$T01=CONCEPTO(100)+CONCEPTO(106)+CONCEPTO(108)+CONCEPTO(123)+CONCEPTO(124)+CONCEPTO(125)+CONCEPTO(126)+CONCEPTO(107)+CONCEPTO(109)+CONCEPTO(111)+CONCEPTO(112)+CONCEPTO(127)+CONCEPTO(128)+CONCEPTO(129)+CONCEPTO(130)+CONCEPTO(131)+CONCEPTO(113)+CONCEPTO(132)+CONCEPTO(122)+CONCEPTO(121)+CONCEPTO(133)+CONCEPTO(134)+CONCEPTO(135)+CONCEPTO(136)+CONCEPTO(138)+CONCEPTO(120)+CONCEPTO(139)+CONCEPTO(119)+CONCEPTO(142)+CONCEPTO(143)+CONCEPTO(144)+CONCEPTO(149)+CONCEPTO(150)+CONCEPTO(151)+CONCEPTO(152)+CONCEPTO(153)+CONCEPTO(154)+CONCEPTO(155)+CONCEPTO(600);\r\n\r\n$MONTO=$T01/12;\r\n\r\n\r\n\r\n\r\n\r\n', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'dtms', '', 'N', 0),
(9009, 'Riesgo Profesional', 'P', 'M', '640000000', '1', 'N', 0, '0', '', '$REF=2.10;\r\n$T01=$SUELDO;\r\n$MONTO=($T01*$REF)/2;', 0, 0, 0, 0, 0, '', 0, 0, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 'RiesgoP', '000000000000000', 'N', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomconceptos_acumulados`
--

CREATE TABLE IF NOT EXISTS `nomconceptos_acumulados` (
  `codcon` int(11) NOT NULL,
  `cod_tac` varchar(6) COLLATE utf8_spanish_ci NOT NULL,
  `operacion` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`codcon`,`cod_tac`),
  KEY `fc_idx_60` (`cod_tac`,`codcon`),
  KEY `fc_idx_61` (`codcon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nomconceptos_acumulados`
--

INSERT INTO `nomconceptos_acumulados` (`codcon`, `cod_tac`, `operacion`, `ee`) VALUES
(90, 'ANEX03', 'S', 0),
(90, 'CON', 'S', 0),
(91, 'ANEX03', 'S', 0),
(91, 'CON', 'S', 0),
(91, 'SIPE', 'S', 0),
(92, 'ANEX03', 'S', 0),
(92, 'CON', 'S', 0),
(92, 'SIPE', 'S', 0),
(93, 'ANEX03', 'S', 0),
(93, 'CON', 'S', 0),
(93, 'LIQ', 'S', 0),
(93, 'SIPE', 'S', 0),
(94, 'ANEX03', 'S', 0),
(94, 'CON', 'S', 0),
(94, 'SIPE', 'S', 0),
(95, 'ANEX03', 'S', 0),
(95, 'CON', 'S', 0),
(95, 'LIQ', 'S', 0),
(95, 'SIPE', 'S', 0),
(100, 'ANEX03', 'S', 0),
(100, 'CON', 'S', 0),
(100, 'DECIMO', 'S', 0),
(100, 'ISLR', 'S', 0),
(100, 'SI', 'S', 0),
(100, 'SIPE', 'S', 0),
(100, 'VAC', 'S', 0),
(100, 'XIII M', 'S', 0),
(102, 'ANEX03', 'S', 0),
(102, 'CON', 'S', 0),
(102, 'DECIMO', 'S', 0),
(102, 'SIPE', 'S', 0),
(103, 'CON', 'S', 0),
(103, 'ISP', 'S', 0),
(103, 'PS', 'S', 0),
(103, 'SI', 'S', 0),
(104, 'CON', 'S', 0),
(104, 'ISP', 'S', 0),
(104, 'PS', 'S', 0),
(104, 'SI', 'S', 0),
(106, 'ANEX03', 'S', 0),
(106, 'CON', 'S', 0),
(106, 'SI', 'S', 0),
(106, 'SIPE', 'S', 0),
(106, 'XIII M', 'S', 0),
(107, 'ANEX03', 'S', 0),
(107, 'CON', 'S', 0),
(107, 'SI', 'S', 0),
(107, 'SIPE', 'S', 0),
(107, 'XIII M', 'S', 0),
(108, 'ANEX03', 'S', 0),
(108, 'CON', 'S', 0),
(108, 'SI', 'S', 0),
(108, 'SIPE', 'S', 0),
(108, 'XIII M', 'S', 0),
(109, 'ANEX03', 'S', 0),
(109, 'CON', 'S', 0),
(109, 'SI', 'S', 0),
(109, 'SIPE', 'S', 0),
(109, 'XIII M', 'S', 0),
(111, 'ANEX03', 'S', 0),
(111, 'CON', 'S', 0),
(111, 'SI', 'S', 0),
(111, 'SIPE', 'S', 0),
(111, 'XIII M', 'S', 0),
(112, 'ANEX03', 'S', 0),
(112, 'CON', 'S', 0),
(112, 'SI', 'S', 0),
(112, 'SIPE', 'S', 0),
(112, 'XIII M', 'S', 0),
(113, 'ANEX03', 'S', 0),
(113, 'CON', 'S', 0),
(113, 'SI', 'S', 0),
(113, 'SIPE', 'S', 0),
(113, 'XIII M', 'S', 0),
(114, 'ANEX03', 'S', 0),
(114, 'CON', 'S', 0),
(114, 'SI', 'S', 0),
(114, 'SIPE', 'S', 0),
(114, 'VAC', 'S', 0),
(115, 'CON', 'S', 0),
(115, 'SI', 'S', 0),
(119, 'ANEX03', 'S', 0),
(119, 'CON', 'S', 0),
(119, 'SI', 'S', 0),
(119, 'SIPE', 'S', 0),
(119, 'XIII M', 'S', 0),
(120, 'ANEX03', 'S', 0),
(120, 'CON', 'S', 0),
(120, 'SI', 'S', 0),
(120, 'SIPE', 'S', 0),
(120, 'XIII M', 'S', 0),
(121, 'ANEX03', 'S', 0),
(121, 'CON', 'S', 0),
(121, 'SI', 'S', 0),
(121, 'SIPE', 'S', 0),
(121, 'XIII M', 'S', 0),
(122, 'ANEX03', 'S', 0),
(122, 'CON', 'S', 0),
(122, 'SI', 'S', 0),
(122, 'SIPE', 'S', 0),
(122, 'XIII M', 'S', 0),
(123, 'ANEX03', 'S', 0),
(123, 'CON', 'S', 0),
(123, 'SI', 'S', 0),
(123, 'SIPE', 'S', 0),
(123, 'XIII M', 'S', 0),
(124, 'ANEX03', 'S', 0),
(124, 'CON', 'S', 0),
(124, 'SI', 'S', 0),
(124, 'SIPE', 'S', 0),
(124, 'XIII M', 'S', 0),
(125, 'ANEX03', 'S', 0),
(125, 'CON', 'S', 0),
(125, 'SI', 'S', 0),
(125, 'SIPE', 'S', 0),
(125, 'XIII M', 'S', 0),
(126, 'ANEX03', 'S', 0),
(126, 'CON', 'S', 0),
(126, 'SI', 'S', 0),
(126, 'SIPE', 'S', 0),
(126, 'XIII M', 'S', 0),
(127, 'ANEX03', 'S', 0),
(127, 'CON', 'S', 0),
(127, 'SI', 'S', 0),
(127, 'SIPE', 'S', 0),
(127, 'XIII M', 'S', 0),
(128, 'ANEX03', 'S', 0),
(128, 'CON', 'S', 0),
(128, 'SI', 'S', 0),
(128, 'SIPE', 'S', 0),
(128, 'XIII M', 'S', 0),
(129, 'ANEX03', 'S', 0),
(129, 'CON', 'S', 0),
(129, 'SI', 'S', 0),
(129, 'SIPE', 'S', 0),
(129, 'XIII M', 'S', 0),
(130, 'ANEX03', 'S', 0),
(130, 'CON', 'S', 0),
(130, 'SI', 'S', 0),
(130, 'SIPE', 'S', 0),
(130, 'XIII M', 'S', 0),
(131, 'ANEX03', 'S', 0),
(131, 'CON', 'S', 0),
(131, 'SI', 'S', 0),
(131, 'SIPE', 'S', 0),
(131, 'XIII M', 'S', 0),
(132, 'ANEX03', 'S', 0),
(132, 'CON', 'S', 0),
(132, 'SI', 'S', 0),
(132, 'SIPE', 'S', 0),
(132, 'XIII M', 'S', 0),
(133, 'ANEX03', 'S', 0),
(133, 'CON', 'S', 0),
(133, 'SI', 'S', 0),
(133, 'SIPE', 'S', 0),
(133, 'XIII M', 'S', 0),
(134, 'ANEX03', 'S', 0),
(134, 'CON', 'S', 0),
(134, 'SI', 'S', 0),
(134, 'SIPE', 'S', 0),
(134, 'XIII M', 'S', 0),
(135, 'ANEX03', 'S', 0),
(135, 'CON', 'S', 0),
(135, 'SI', 'S', 0),
(135, 'SIPE', 'S', 0),
(135, 'XIII M', 'S', 0),
(136, 'ANEX03', 'S', 0),
(136, 'CON', 'S', 0),
(136, 'SI', 'S', 0),
(136, 'SIPE', 'S', 0),
(136, 'XIII M', 'S', 0),
(138, 'ANEX03', 'S', 0),
(138, 'CON', 'S', 0),
(138, 'SI', 'S', 0),
(138, 'SIPE', 'S', 0),
(138, 'XIII M', 'S', 0),
(139, 'ANEX03', 'S', 0),
(139, 'CON', 'S', 0),
(139, 'SI', 'S', 0),
(139, 'SIPE', 'S', 0),
(139, 'XIII M', 'S', 0),
(140, 'CON', 'S', 0),
(140, 'ISP', 'S', 0),
(140, 'PS', 'S', 0),
(140, 'SI', 'S', 0),
(141, 'CON', 'S', 0),
(141, 'SI', 'S', 0),
(142, 'ANEX03', 'S', 0),
(142, 'CON', 'S', 0),
(142, 'SI', 'S', 0),
(142, 'SIPE', 'S', 0),
(142, 'XIII M', 'S', 0),
(143, 'ANEX03', 'S', 0),
(143, 'CON', 'S', 0),
(143, 'SI', 'S', 0),
(143, 'SIPE', 'S', 0),
(143, 'XIII M', 'S', 0),
(144, 'ANEX03', 'S', 0),
(144, 'CON', 'S', 0),
(144, 'SI', 'S', 0),
(144, 'SIPE', 'S', 0),
(144, 'XIII M', 'S', 0),
(145, 'ANEX03', 'S', 0),
(145, 'CON', 'S', 0),
(145, 'DGR', 'S', 0),
(145, 'SIGR', 'S', 0),
(145, 'SIPE', 'S', 0),
(145, 'VACGR', 'S', 0),
(145, 'XIII M', 'S', 0),
(147, 'CON', 'S', 0),
(147, 'SI', 'S', 0),
(148, 'CON', 'S', 0),
(148, 'SI', 'S', 0),
(149, 'ANEX03', 'S', 0),
(149, 'CON', 'S', 0),
(149, 'SI', 'S', 0),
(149, 'SIPE', 'S', 0),
(149, 'XIII M', 'S', 0),
(150, 'ANEX03', 'S', 0),
(150, 'CON', 'S', 0),
(150, 'SI', 'S', 0),
(150, 'SIPE', 'S', 0),
(150, 'XIII M', 'S', 0),
(151, 'ANEX03', 'S', 0),
(151, 'CON', 'S', 0),
(151, 'SI', 'S', 0),
(151, 'SIPE', 'S', 0),
(151, 'XIII M', 'S', 0),
(152, 'ANEX03', 'S', 0),
(152, 'CON', 'S', 0),
(152, 'SI', 'S', 0),
(152, 'SIPE', 'S', 0),
(152, 'XIII M', 'S', 0),
(153, 'ANEX03', 'S', 0),
(153, 'CON', 'S', 0),
(153, 'SI', 'S', 0),
(153, 'SIPE', 'S', 0),
(153, 'XIII M', 'S', 0),
(154, 'ANEX03', 'S', 0),
(154, 'CON', 'S', 0),
(154, 'SI', 'S', 0),
(154, 'SIPE', 'S', 0),
(154, 'XIII M', 'S', 0),
(155, 'ANEX03', 'S', 0),
(155, 'CON', 'S', 0),
(155, 'SI', 'S', 0),
(155, 'SIPE', 'S', 0),
(155, 'XIII M', 'S', 0),
(199, 'ANEX03', 'S', 0),
(199, 'CON', 'S', 0),
(199, 'ISP', 'S', 0),
(199, 'PRES', 'S', 0),
(199, 'PS', 'S', 0),
(199, 'SI', 'S', 0),
(199, 'SIPE', 'S', 0),
(200, 'CON', 'S', 0),
(201, 'CON', 'S', 0),
(202, 'CON', 'S', 0),
(202, 'ISLR', 'S', 0),
(203, 'CON', 'S', 0),
(204, 'CON', 'S', 0),
(205, 'CON', 'S', 0),
(206, 'CON', 'S', 0),
(207, 'ANEX03', 'S', 0),
(207, 'CON', 'S', 0),
(207, 'ISLR', 'S', 0),
(208, 'ANEX03', 'S', 0),
(208, 'CON', 'S', 0),
(208, 'ISLR', 'S', 0),
(250, 'ANEX03', 'S', 0),
(300, 'CON', 'S', 0),
(300, 'PRES', 'S', 0),
(302, 'CON', 'S', 0),
(302, 'PRES', 'S', 0),
(305, 'CON', 'S', 0),
(305, 'PRES', 'S', 0),
(310, 'CON', 'S', 0),
(310, 'PRES', 'S', 0),
(311, 'CON', 'S', 0),
(311, 'PRES', 'S', 0),
(312, 'CON', 'S', 0),
(312, 'PRES', 'S', 0),
(313, 'CON', 'S', 0),
(313, 'PRES', 'S', 0),
(315, 'CON', 'S', 0),
(315, 'PRES', 'S', 0),
(316, 'CON', 'S', 0),
(316, 'PRES', 'S', 0),
(317, 'CON', 'S', 0),
(317, 'PRES', 'S', 0),
(320, 'CON', 'S', 0),
(320, 'PRES', 'S', 0),
(341, 'CON', 'S', 0),
(341, 'PRES', 'S', 0),
(350, 'CON', 'S', 0),
(350, 'PRES', 'S', 0),
(351, 'CON', 'S', 0),
(351, 'PRES', 'S', 0),
(501, 'CON', 'S', 0),
(501, 'OD', 'S', 0),
(501, 'PRES', 'S', 0),
(502, 'CON', 'S', 0),
(502, 'OD', 'S', 0),
(502, 'PRES', 'S', 0),
(503, 'CON', 'S', 0),
(503, 'OD', 'S', 0),
(503, 'PRES', 'S', 0),
(504, 'CON', 'S', 0),
(504, 'PRES', 'S', 0),
(505, 'CON', 'S', 0),
(505, 'PRES', 'S', 0),
(506, 'CON', 'S', 0),
(506, 'PRES', 'S', 0),
(507, 'CON', 'S', 0),
(508, 'CON', 'S', 0),
(508, 'PRES', 'S', 0),
(509, 'CON', 'S', 0),
(509, 'PRES', 'S', 0),
(510, 'CON', 'S', 0),
(510, 'PRES', 'S', 0),
(511, 'CON', 'S', 0),
(511, 'PRES', 'S', 0),
(512, 'CON', 'S', 0),
(512, 'PRES', 'S', 0),
(513, 'CON', 'S', 0),
(513, 'PRES', 'S', 0),
(514, 'AN', 'S', 0),
(514, 'CON', 'S', 0),
(514, 'PRES', 'S', 0),
(515, 'CON', 'S', 0),
(515, 'PRES', 'S', 0),
(600, 'CON', 'S', 0),
(601, 'CON', 'S', 0),
(601, 'ISLR', 'S', 0),
(604, 'ANEX03', 'S', 0),
(604, 'CON', 'S', 0),
(604, 'SI', 'S', 0),
(604, 'SIPE', 'S', 0),
(604, 'XIII M', 'S', 0),
(605, 'CON', 'S', 0),
(605, 'ISLR', 'S', 0),
(608, 'CON', 'S', 0),
(609, 'CON', 'S', 0),
(610, 'CON', 'S', 0),
(3000, 'CON', 'S', 0),
(3001, 'CON', 'S', 0),
(9000, 'CON', 'S', 0),
(9001, 'CON', 'S', 0),
(9002, 'CON', 'S', 0),
(9005, 'CON', 'S', 0),
(9006, 'CON', 'S', 0),
(9007, 'CON', 'S', 0),
(9009, 'CON', 'S', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomconceptos_ctager`
--

CREATE TABLE IF NOT EXISTS `nomconceptos_ctager` (
  `codcon` int(5) NOT NULL,
  `codnivel4` int(7) NOT NULL,
  `ctacon` varchar(50) NOT NULL,
  `tipcon` varchar(1) NOT NULL,
  PRIMARY KEY (`codcon`,`codnivel4`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomconceptos_frecuencias`
--

CREATE TABLE IF NOT EXISTS `nomconceptos_frecuencias` (
  `codcon` int(11) NOT NULL,
  `codfre` int(11) NOT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`codcon`,`codfre`),
  UNIQUE KEY `fc_idx_43` (`codfre`,`codcon`),
  KEY `fc_idx_44` (`codcon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nomconceptos_frecuencias`
--

INSERT INTO `nomconceptos_frecuencias` (`codcon`, `codfre`, `ee`) VALUES
(27, 2, 0),
(27, 3, 0),
(90, 10, 0),
(91, 10, 0),
(92, 10, 0),
(93, 10, 0),
(94, 10, 0),
(95, 10, 0),
(100, 2, 0),
(100, 3, 0),
(102, 7, 0),
(103, 2, 0),
(103, 3, 0),
(104, 2, 0),
(104, 3, 0),
(104, 16, 0),
(104, 17, 0),
(104, 18, 0),
(104, 19, 0),
(104, 20, 0),
(106, 2, 0),
(106, 3, 0),
(106, 17, 0),
(106, 18, 0),
(106, 19, 0),
(107, 2, 0),
(107, 3, 0),
(107, 16, 0),
(107, 17, 0),
(107, 18, 0),
(107, 19, 0),
(108, 2, 0),
(108, 3, 0),
(109, 2, 0),
(109, 3, 0),
(111, 2, 0),
(111, 3, 0),
(111, 17, 0),
(111, 18, 0),
(111, 19, 0),
(112, 2, 0),
(112, 3, 0),
(112, 17, 0),
(112, 18, 0),
(112, 19, 0),
(113, 2, 0),
(113, 3, 0),
(113, 17, 0),
(113, 18, 0),
(113, 19, 0),
(114, 8, 0),
(114, 16, 0),
(115, 2, 0),
(115, 3, 0),
(119, 2, 0),
(119, 3, 0),
(120, 2, 0),
(120, 3, 0),
(120, 17, 0),
(120, 18, 0),
(120, 19, 0),
(120, 20, 0),
(121, 2, 0),
(121, 3, 0),
(121, 17, 0),
(121, 18, 0),
(121, 19, 0),
(121, 20, 0),
(122, 2, 0),
(122, 3, 0),
(122, 17, 0),
(122, 18, 0),
(122, 19, 0),
(122, 20, 0),
(123, 2, 0),
(123, 3, 0),
(123, 8, 0),
(124, 2, 0),
(124, 3, 0),
(125, 2, 0),
(125, 3, 0),
(126, 2, 0),
(126, 3, 0),
(127, 2, 0),
(127, 3, 0),
(128, 2, 0),
(128, 3, 0),
(129, 2, 0),
(129, 3, 0),
(130, 2, 0),
(130, 3, 0),
(131, 2, 0),
(131, 3, 0),
(132, 2, 0),
(132, 3, 0),
(133, 2, 0),
(133, 3, 0),
(134, 2, 0),
(134, 3, 0),
(135, 2, 0),
(135, 3, 0),
(136, 2, 0),
(136, 3, 0),
(138, 2, 0),
(138, 3, 0),
(139, 2, 0),
(139, 3, 0),
(140, 2, 0),
(140, 3, 0),
(140, 16, 0),
(140, 17, 0),
(140, 18, 0),
(140, 19, 0),
(141, 2, 0),
(141, 3, 0),
(141, 16, 0),
(141, 17, 0),
(141, 18, 0),
(141, 19, 0),
(142, 2, 0),
(142, 3, 0),
(143, 2, 0),
(143, 3, 0),
(144, 2, 0),
(144, 3, 0),
(145, 2, 0),
(145, 3, 0),
(146, 2, 0),
(146, 3, 0),
(147, 2, 0),
(147, 3, 0),
(148, 2, 0),
(148, 3, 0),
(149, 2, 0),
(149, 3, 0),
(150, 2, 0),
(150, 3, 0),
(151, 2, 0),
(151, 3, 0),
(152, 2, 0),
(152, 3, 0),
(153, 2, 0),
(153, 3, 0),
(154, 2, 0),
(154, 3, 0),
(155, 2, 0),
(155, 3, 0),
(199, 2, 0),
(199, 3, 0),
(200, 2, 0),
(200, 3, 0),
(200, 8, 0),
(200, 10, 0),
(200, 16, 0),
(200, 17, 0),
(200, 18, 0),
(200, 19, 0),
(201, 2, 0),
(201, 3, 0),
(201, 8, 0),
(201, 16, 0),
(201, 17, 0),
(201, 18, 0),
(201, 19, 0),
(202, 2, 0),
(202, 3, 0),
(203, 2, 0),
(203, 3, 0),
(204, 2, 0),
(204, 3, 0),
(204, 16, 0),
(204, 17, 0),
(204, 18, 0),
(204, 19, 0),
(205, 7, 0),
(205, 10, 0),
(205, 21, 0),
(206, 10, 0),
(206, 21, 0),
(250, 10, 0),
(250, 21, 0),
(300, 2, 0),
(300, 3, 0),
(300, 16, 0),
(300, 17, 0),
(300, 18, 0),
(300, 19, 0),
(300, 20, 0),
(302, 2, 0),
(302, 3, 0),
(302, 16, 0),
(302, 17, 0),
(302, 18, 0),
(302, 19, 0),
(302, 20, 0),
(305, 2, 0),
(305, 3, 0),
(305, 16, 0),
(305, 17, 0),
(305, 18, 0),
(305, 19, 0),
(305, 20, 0),
(310, 2, 0),
(310, 3, 0),
(310, 16, 0),
(310, 17, 0),
(310, 18, 0),
(310, 19, 0),
(310, 20, 0),
(311, 2, 0),
(311, 3, 0),
(311, 16, 0),
(311, 17, 0),
(311, 18, 0),
(311, 19, 0),
(311, 20, 0),
(312, 2, 0),
(312, 3, 0),
(312, 16, 0),
(312, 17, 0),
(312, 18, 0),
(312, 19, 0),
(312, 20, 0),
(313, 2, 0),
(313, 3, 0),
(313, 16, 0),
(313, 17, 0),
(313, 18, 0),
(313, 19, 0),
(313, 20, 0),
(315, 2, 0),
(315, 3, 0),
(315, 16, 0),
(315, 17, 0),
(315, 18, 0),
(315, 19, 0),
(315, 20, 0),
(316, 2, 0),
(316, 3, 0),
(316, 16, 0),
(316, 17, 0),
(316, 18, 0),
(316, 19, 0),
(316, 20, 0),
(317, 2, 0),
(317, 3, 0),
(317, 16, 0),
(317, 17, 0),
(317, 18, 0),
(317, 19, 0),
(317, 20, 0),
(320, 2, 0),
(320, 3, 0),
(320, 16, 0),
(320, 17, 0),
(320, 18, 0),
(320, 19, 0),
(320, 20, 0),
(341, 2, 0),
(341, 3, 0),
(341, 16, 0),
(341, 17, 0),
(341, 18, 0),
(341, 19, 0),
(341, 20, 0),
(350, 2, 0),
(350, 3, 0),
(350, 16, 0),
(350, 17, 0),
(350, 18, 0),
(350, 19, 0),
(350, 20, 0),
(351, 2, 0),
(351, 3, 0),
(351, 16, 0),
(351, 17, 0),
(351, 18, 0),
(351, 19, 0),
(351, 20, 0),
(501, 2, 0),
(501, 3, 0),
(501, 16, 0),
(502, 2, 0),
(502, 3, 0),
(502, 8, 0),
(502, 16, 0),
(502, 17, 0),
(502, 18, 0),
(502, 19, 0),
(502, 20, 0),
(503, 2, 0),
(503, 3, 0),
(504, 2, 0),
(504, 3, 0),
(504, 8, 0),
(504, 16, 0),
(505, 2, 0),
(505, 3, 0),
(506, 2, 0),
(506, 3, 0),
(507, 2, 0),
(507, 3, 0),
(508, 2, 0),
(508, 3, 0),
(509, 2, 0),
(509, 3, 0),
(510, 2, 0),
(510, 3, 0),
(511, 2, 0),
(511, 3, 0),
(512, 2, 0),
(512, 3, 0),
(513, 2, 0),
(513, 3, 0),
(514, 2, 0),
(514, 3, 0),
(515, 2, 0),
(515, 3, 0),
(600, 2, 0),
(600, 3, 0),
(601, 2, 0),
(601, 3, 0),
(604, 2, 0),
(604, 3, 0),
(605, 2, 0),
(605, 3, 0),
(605, 11, 0),
(608, 11, 0),
(609, 11, 0),
(610, 11, 0),
(3000, 2, 0),
(3000, 3, 0),
(3001, 2, 0),
(3001, 3, 0),
(9000, 11, 0),
(9001, 2, 0),
(9001, 3, 0),
(9002, 11, 0),
(9003, 2, 0),
(9003, 3, 0),
(9003, 6, 0),
(9003, 7, 0),
(9003, 8, 0),
(9003, 10, 0),
(9003, 11, 0),
(9003, 15, 0),
(9003, 16, 0),
(9003, 17, 0),
(9003, 18, 0),
(9003, 19, 0),
(9003, 20, 0),
(9003, 21, 0),
(9004, 2, 0),
(9004, 3, 0),
(9004, 6, 0),
(9004, 7, 0),
(9004, 8, 0),
(9004, 10, 0),
(9004, 11, 0),
(9004, 15, 0),
(9004, 16, 0),
(9004, 17, 0),
(9004, 18, 0),
(9004, 19, 0),
(9004, 20, 0),
(9004, 21, 0),
(9005, 2, 0),
(9005, 3, 0),
(9006, 2, 0),
(9006, 3, 0),
(9007, 2, 0),
(9007, 3, 0),
(9009, 2, 0),
(9009, 3, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomconceptos_situaciones`
--

CREATE TABLE IF NOT EXISTS `nomconceptos_situaciones` (
  `codcon` int(11) NOT NULL,
  `estado` varchar(30) COLLATE utf8_spanish_ci NOT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`codcon`,`estado`),
  KEY `fc_idx_40` (`codcon`),
  KEY `estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nomconceptos_situaciones`
--

INSERT INTO `nomconceptos_situaciones` (`codcon`, `estado`, `ee`) VALUES
(27, 'Activo', 0),
(90, 'Activo', 0),
(91, 'Activo', 0),
(92, 'Activo', 0),
(93, 'Activo', 0),
(94, 'Activo', 0),
(95, 'Activo', 0),
(100, 'Activo', 0),
(100, 'Nuevo', 0),
(102, 'Activo', 0),
(103, 'Activo', 0),
(104, 'Activo', 0),
(106, 'Activo', 0),
(107, 'Activo', 0),
(108, 'Activo', 0),
(109, 'Activo', 0),
(111, 'Activo', 0),
(112, 'Activo', 0),
(113, 'Activo', 0),
(114, 'Activo', 0),
(115, 'Activo', 0),
(115, 'Vacaciones', 0),
(119, 'Activo', 0),
(120, 'Activo', 0),
(121, 'Activo', 0),
(122, 'Activo', 0),
(123, 'Activo', 0),
(124, 'Activo', 0),
(125, 'Activo', 0),
(126, 'Activo', 0),
(127, 'Activo', 0),
(128, 'Activo', 0),
(129, 'Activo', 0),
(130, 'Activo', 0),
(131, 'Activo', 0),
(132, 'Activo', 0),
(133, 'Activo', 0),
(134, 'Activo', 0),
(135, 'Activo', 0),
(136, 'Activo', 0),
(138, 'Activo', 0),
(139, 'Activo', 0),
(140, 'Activo', 0),
(141, 'Activo', 0),
(142, 'Activo', 0),
(143, 'Activo', 0),
(144, 'Activo', 0),
(145, 'Activo', 0),
(146, 'Activo', 0),
(147, 'Activo', 0),
(148, 'Activo', 0),
(149, 'Activo', 0),
(150, 'Activo', 0),
(151, 'Activo', 0),
(152, 'Activo', 0),
(153, 'Activo', 0),
(154, 'Activo', 0),
(155, 'Activo', 0),
(199, 'Activo', 0),
(200, 'Activo', 0),
(200, 'Nuevo', 0),
(201, 'Activo', 0),
(201, 'Nuevo', 0),
(202, 'Activo', 0),
(203, 'Activo', 0),
(204, 'Activo', 0),
(205, 'Activo', 0),
(206, 'Activo', 0),
(207, 'Activo', 0),
(208, 'Activo', 0),
(208, 'Nuevo', 0),
(250, 'Activo', 0),
(300, 'Activo', 0),
(302, 'Activo', 0),
(305, 'Activo', 0),
(310, 'Activo', 0),
(311, 'Activo', 0),
(312, 'Activo', 0),
(313, 'Activo', 0),
(315, 'Activo', 0),
(316, 'Activo', 0),
(317, 'Activo', 0),
(320, 'Activo', 0),
(341, 'Activo', 0),
(350, 'Activo', 0),
(351, 'Activo', 0),
(501, 'Activo', 0),
(502, 'Activo', 0),
(503, 'Activo', 0),
(504, 'Activo', 0),
(505, 'Activo', 0),
(506, 'Activo', 0),
(507, 'Activo', 0),
(508, 'Activo', 0),
(509, 'Activo', 0),
(510, 'Activo', 0),
(511, 'Activo', 0),
(512, 'Activo', 0),
(513, 'Activo', 0),
(514, 'Activo', 0),
(515, 'Activo', 0),
(600, 'Activo', 0),
(601, 'Activo', 0),
(604, 'Activo', 0),
(605, 'Activo', 0),
(608, 'Activo', 0),
(609, 'Activo', 0),
(610, 'Activo', 0),
(3000, 'Activo', 0),
(3001, 'Activo', 0),
(9000, 'Activo', 0),
(9000, 'Egresado', 0),
(9000, 'Egresado de Nomina de Pago', 0),
(9000, 'Nuevo', 0),
(9000, 'Vacaciones', 0),
(9001, 'Activo', 0),
(9002, 'Activo', 0),
(9002, 'Egresado', 0),
(9002, 'Egresado de Nomina de Pago', 0),
(9002, 'Nuevo', 0),
(9002, 'Vacaciones', 0),
(9003, 'Activo', 0),
(9003, 'Egresado', 0),
(9003, 'Egresado de Nomina de Pago', 0),
(9004, 'Activo', 0),
(9005, 'Activo', 0),
(9006, 'Activo', 0),
(9007, 'Activo', 0),
(9009, 'Activo', 0),
(9009, 'Nuevo', 0),
(9009, 'Vacaciones', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomconceptos_tiponomina`
--

CREATE TABLE IF NOT EXISTS `nomconceptos_tiponomina` (
  `codcon` int(11) NOT NULL,
  `codtip` int(11) NOT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`codcon`,`codtip`),
  UNIQUE KEY `fc_idx_64` (`codtip`,`codcon`),
  KEY `fc_idx_65` (`codcon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `nomconceptos_tiponomina`
--

INSERT INTO `nomconceptos_tiponomina` (`codcon`, `codtip`, `ee`) VALUES
(27, 2, 0),
(27, 3, 0),
(90, 1, 0),
(90, 2, 0),
(91, 1, 0),
(91, 2, 0),
(92, 1, 0),
(92, 2, 0),
(93, 1, 0),
(93, 2, 0),
(94, 1, 0),
(94, 2, 0),
(95, 1, 0),
(95, 2, 0),
(100, 1, 0),
(100, 2, 0),
(100, 4, 0),
(102, 1, 0),
(102, 2, 0),
(102, 3, 0),
(102, 4, 0),
(106, 1, 0),
(106, 2, 0),
(107, 1, 0),
(107, 2, 0),
(108, 1, 0),
(108, 2, 0),
(109, 1, 0),
(109, 2, 0),
(111, 1, 0),
(111, 2, 0),
(111, 3, 0),
(111, 4, 0),
(112, 1, 0),
(112, 2, 0),
(112, 3, 0),
(112, 4, 0),
(113, 1, 0),
(113, 2, 0),
(113, 3, 0),
(113, 4, 0),
(114, 1, 0),
(114, 2, 0),
(114, 3, 0),
(115, 1, 0),
(119, 1, 0),
(119, 2, 0),
(120, 1, 0),
(120, 2, 0),
(120, 3, 0),
(120, 4, 0),
(121, 1, 0),
(121, 2, 0),
(121, 3, 0),
(121, 4, 0),
(122, 1, 0),
(122, 2, 0),
(122, 3, 0),
(122, 4, 0),
(123, 1, 0),
(123, 2, 0),
(124, 1, 0),
(124, 2, 0),
(125, 1, 0),
(125, 2, 0),
(126, 1, 0),
(126, 2, 0),
(127, 1, 0),
(127, 2, 0),
(128, 1, 0),
(128, 2, 0),
(129, 1, 0),
(129, 2, 0),
(130, 1, 0),
(131, 1, 0),
(131, 2, 0),
(132, 1, 0),
(132, 2, 0),
(133, 1, 0),
(133, 2, 0),
(134, 1, 0),
(134, 2, 0),
(135, 1, 0),
(135, 2, 0),
(136, 1, 0),
(136, 2, 0),
(138, 1, 0),
(138, 2, 0),
(139, 1, 0),
(139, 2, 0),
(140, 1, 0),
(140, 3, 0),
(140, 4, 0),
(141, 1, 0),
(141, 3, 0),
(142, 1, 0),
(142, 2, 0),
(143, 1, 0),
(143, 2, 0),
(144, 1, 0),
(144, 2, 0),
(145, 1, 0),
(145, 3, 0),
(146, 3, 0),
(147, 1, 0),
(148, 1, 0),
(149, 1, 0),
(149, 2, 0),
(150, 1, 0),
(150, 2, 0),
(151, 1, 0),
(151, 2, 0),
(152, 1, 0),
(152, 2, 0),
(153, 1, 0),
(153, 2, 0),
(154, 1, 0),
(154, 2, 0),
(155, 1, 0),
(155, 2, 0),
(199, 1, 0),
(199, 2, 0),
(199, 3, 0),
(199, 4, 0),
(200, 1, 0),
(200, 2, 0),
(200, 3, 0),
(200, 4, 0),
(201, 1, 0),
(201, 2, 0),
(201, 3, 0),
(201, 4, 0),
(202, 1, 0),
(203, 1, 0),
(203, 2, 0),
(203, 3, 0),
(203, 4, 0),
(204, 1, 0),
(204, 2, 0),
(204, 3, 0),
(204, 4, 0),
(205, 1, 0),
(205, 2, 0),
(205, 3, 0),
(205, 4, 0),
(206, 1, 0),
(206, 2, 0),
(206, 3, 0),
(206, 4, 0),
(250, 1, 0),
(250, 2, 0),
(250, 3, 0),
(250, 4, 0),
(300, 1, 0),
(300, 2, 0),
(300, 3, 0),
(300, 4, 0),
(302, 1, 0),
(302, 2, 0),
(302, 3, 0),
(302, 4, 0),
(305, 1, 0),
(305, 2, 0),
(305, 3, 0),
(305, 4, 0),
(310, 1, 0),
(310, 2, 0),
(310, 3, 0),
(310, 4, 0),
(311, 1, 0),
(311, 2, 0),
(311, 3, 0),
(311, 4, 0),
(312, 1, 0),
(312, 2, 0),
(312, 3, 0),
(312, 4, 0),
(313, 1, 0),
(313, 2, 0),
(313, 3, 0),
(313, 4, 0),
(315, 1, 0),
(315, 2, 0),
(315, 3, 0),
(315, 4, 0),
(316, 1, 0),
(316, 2, 0),
(316, 3, 0),
(316, 4, 0),
(317, 1, 0),
(317, 2, 0),
(317, 3, 0),
(317, 4, 0),
(320, 1, 0),
(320, 2, 0),
(320, 3, 0),
(320, 4, 0),
(341, 1, 0),
(341, 2, 0),
(341, 3, 0),
(341, 4, 0),
(350, 1, 0),
(350, 2, 0),
(350, 3, 0),
(350, 4, 0),
(351, 1, 0),
(351, 2, 0),
(351, 3, 0),
(351, 4, 0),
(501, 1, 0),
(501, 2, 0),
(501, 3, 0),
(501, 4, 0),
(502, 1, 0),
(502, 2, 0),
(502, 3, 0),
(502, 4, 0),
(503, 1, 0),
(504, 1, 0),
(504, 2, 0),
(504, 3, 0),
(504, 4, 0),
(505, 1, 0),
(506, 1, 0),
(507, 1, 0),
(508, 1, 0),
(509, 1, 0),
(510, 1, 0),
(511, 1, 0),
(512, 1, 0),
(513, 1, 0),
(514, 1, 0),
(515, 1, 0),
(600, 1, 0),
(600, 2, 0),
(601, 1, 0),
(601, 2, 0),
(604, 1, 0),
(604, 2, 0),
(605, 1, 0),
(605, 2, 0),
(608, 1, 0),
(608, 2, 0),
(609, 1, 0),
(609, 2, 0),
(610, 1, 0),
(610, 2, 0),
(3000, 1, 0),
(3000, 2, 0),
(3001, 1, 0),
(3001, 2, 0),
(9000, 1, 0),
(9000, 2, 0),
(9000, 3, 0),
(9000, 4, 0),
(9001, 1, 0),
(9001, 2, 0),
(9001, 3, 0),
(9001, 4, 0),
(9002, 1, 0),
(9002, 2, 0),
(9002, 3, 0),
(9002, 4, 0),
(9003, 1, 0),
(9003, 2, 0),
(9003, 3, 0),
(9003, 4, 0),
(9004, 1, 0),
(9004, 2, 0),
(9004, 3, 0),
(9004, 4, 0),
(9005, 1, 0),
(9005, 2, 0),
(9006, 1, 0),
(9006, 2, 0),
(9007, 1, 0),
(9007, 2, 0),
(9009, 1, 0),
(9009, 2, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomconf_constancia`
--

CREATE TABLE IF NOT EXISTS `nomconf_constancia` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `encabezado` text,
  `pie_pagina` text,
  `titulo` varchar(70) DEFAULT NULL,
  `slogan` text,
  `cargo_gerente` varchar(255) DEFAULT NULL,
  `abreviatura` varchar(10) DEFAULT NULL,
  `observaciones` text,
  `cantidad_mensual` int(11) NOT NULL DEFAULT '3',
  `tipo_validacion` enum('General','Modelo') NOT NULL DEFAULT 'General',
  `validar_constancias` enum('Si','No') DEFAULT 'No',
  `codigo_verificacion` enum('Si','No') DEFAULT 'No',
  `posicionx_codigo` int(11) DEFAULT NULL,
  `posiciony_codigo` int(11) DEFAULT NULL,
  `texto_codigo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `nomconf_constancia`
--

INSERT INTO `nomconf_constancia` (`codigo`, `encabezado`, `pie_pagina`, `titulo`, `slogan`, `cargo_gerente`, `abreviatura`, `observaciones`, `cantidad_mensual`, `tipo_validacion`, `validar_constancias`, `codigo_verificacion`, `posicionx_codigo`, `posiciony_codigo`, `texto_codigo`) VALUES
(1, '<p><img alt="logo" src="imagenes/header.png" style="height:195px" /></p>\r\n', '<p><img alt="logo" src="imagenes/footer.png" style="height:162px" /></p>\r\n', NULL, NULL, NULL, NULL, NULL, 3, 'General', 'No', 'No', NULL, NULL, '$CODIGO_VERIFICACION');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomcursos`
--

CREATE TABLE IF NOT EXISTS `nomcursos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_curso` varchar(255) NOT NULL,
  `fecha_curso` date NOT NULL,
  `descripcion_curso` varchar(255) NOT NULL,
  `instructor_curso` varchar(255) NOT NULL,
  `duracion` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `nomcursos`
--

INSERT INTO `nomcursos` (`id`, `nombre_curso`, `fecha_curso`, `descripcion_curso`, `instructor_curso`, `duracion`) VALUES
(1, 'PHP', '2015-10-15', 'LENGUAJE PROGRAMACION WEB PHP', 'JOSE MORALES', 20),
(2, 'MYSQL', '2015-10-22', 'BASE DE DATOS MYSQL', 'JOSE MORALES', 10),
(3, 'HTML 5', '2015-10-15', 'HTML WEB', 'JOSE MORALES', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomcursos_personal`
--

CREATE TABLE IF NOT EXISTS `nomcursos_personal` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_personal` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomdesempeno`
--

CREATE TABLE IF NOT EXISTS `nomdesempeno` (
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(60) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomelegibles`
--

CREATE TABLE IF NOT EXISTS `nomelegibles` (
  `cedula` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `apellidos` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `nombres` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `sexo` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `fecnac` date NOT NULL,
  `lugarnac` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `telefono` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `cod_profesion` int(11) NOT NULL,
  `grado_instruccion` int(11) NOT NULL,
  `area_desempeno` int(11) NOT NULL,
  `anios_exp` int(11) NOT NULL,
  `observacion` varchar(200) COLLATE utf8_spanish_ci NOT NULL,
  `fecha_reg` date NOT NULL,
  `direccion` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `foto` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomempresa`
--

CREATE TABLE IF NOT EXISTS `nomempresa` (
  `cod_emp` int(11) NOT NULL,
  `nom_emp` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `dir_emp` varchar(250) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ciu_emp` varchar(25) COLLATE utf8_spanish_ci DEFAULT NULL,
  `edo_emp` varchar(25) COLLATE utf8_spanish_ci DEFAULT NULL,
  `zon_emp` varchar(12) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tel_emp` varchar(25) COLLATE utf8_spanish_ci DEFAULT NULL,
  `rif` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nit` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `pre_sid` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ger_rrhh` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `edadmax` int(11) DEFAULT NULL,
  `amonemax` int(11) DEFAULT NULL,
  `redontip` tinyint(4) DEFAULT NULL,
  `unidadtrib` decimal(17,2) DEFAULT NULL,
  `tipopres` tinyint(4) DEFAULT NULL,
  `munidadtrib` decimal(17,2) DEFAULT NULL,
  `diasbonvac` smallint(6) DEFAULT NULL,
  `diasutilidad` smallint(6) DEFAULT NULL,
  `nivel1` tinyint(1) DEFAULT NULL,
  `nivel2` tinyint(1) DEFAULT NULL,
  `nivel3` tinyint(1) DEFAULT NULL,
  `nivel4` tinyint(1) DEFAULT NULL,
  `nivel5` tinyint(1) DEFAULT NULL,
  `entfederal` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `distrito` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `municipio` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codacteco` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nomacteco` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecfunda` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `capital` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `degravunico` decimal(17,2) DEFAULT NULL,
  `mescambiari` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `utcargafam` decimal(17,2) DEFAULT NULL,
  `monsalmin` decimal(17,2) DEFAULT NULL,
  `codcon` int(11) DEFAULT NULL,
  `codcons` int(11) DEFAULT NULL,
  `demo` tinyint(4) DEFAULT NULL,
  `rutacontab` varchar(254) COLLATE utf8_spanish_ci DEFAULT NULL,
  `rutadatoscontab` varchar(254) COLLATE utf8_spanish_ci DEFAULT NULL,
  `serial` varchar(59) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ctacheque` varchar(22) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ctaefectivo` varchar(22) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nrocompro` int(11) DEFAULT NULL,
  `contratos` tinyint(1) DEFAULT NULL,
  `nomniv1` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nomniv2` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nomniv3` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nomniv4` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nomniv5` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `recibovac` text COLLATE utf8_spanish_ci,
  `reciboliq` text COLLATE utf8_spanish_ci,
  `ee` tinyint(4) DEFAULT NULL,
  `fax_emp` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `num_emp` int(11) DEFAULT NULL,
  `num_est` int(11) DEFAULT NULL,
  `num_sso` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `parroquia` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `localidad` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `e_mail` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `cod_entfed` varchar(2) COLLATE utf8_spanish_ci DEFAULT NULL,
  `cod_distri` varchar(2) COLLATE utf8_spanish_ci DEFAULT NULL,
  `cod_munici` varchar(2) COLLATE utf8_spanish_ci DEFAULT NULL,
  `cod_sector` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `cod_acteco` varchar(4) COLLATE utf8_spanish_ci DEFAULT NULL,
  `cod_orden` varchar(4) COLLATE utf8_spanish_ci DEFAULT NULL,
  `utilidad` decimal(17,2) DEFAULT NULL,
  `reportdiff` tinyint(4) DEFAULT NULL,
  `porcdiff` decimal(6,2) DEFAULT NULL,
  `netoneg` tinyint(1) DEFAULT NULL,
  `impresora` mediumtext COLLATE utf8_spanish_ci,
  `selector` tinyint(4) DEFAULT NULL,
  `nosueldocero` tinyint(1) DEFAULT NULL,
  `mediajornada` tinyint(1) DEFAULT NULL,
  `nuevassituaciones` tinyint(1) DEFAULT NULL,
  `tipoficha` tinyint(4) NOT NULL,
  `conprestamos` int(11) DEFAULT NULL,
  `confamiliares` int(11) DEFAULT NULL,
  `conficha` int(11) DEFAULT NULL,
  `nomcampo1` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nomcampo2` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nomcampo3` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `recibonom` varchar(120) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipcontab` tinyint(4) NOT NULL,
  `contadorbanesco` int(11) NOT NULL,
  `ctapatronales` varchar(22) COLLATE utf8_spanish_ci DEFAULT NULL,
  `recibopago` text COLLATE utf8_spanish_ci NOT NULL,
  `nivel6` tinyint(1) NOT NULL,
  `nivel7` tinyint(1) NOT NULL,
  `nomniv6` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `nomniv7` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `imagen_izq` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `imagen_der` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `cod_material` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `unidad` varchar(6) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ccosto` varchar(6) COLLATE utf8_spanish_ci DEFAULT NULL,
  `proveedor` varchar(6) COLLATE utf8_spanish_ci DEFAULT NULL,
  `moneda` varchar(80) COLLATE utf8_spanish_ci NOT NULL,
  `ced_rrhh` varchar(30) COLLATE utf8_spanish_ci NOT NULL,
  `tipo_empresa` smallint(1) NOT NULL DEFAULT '0' COMMENT '0:privada 1:publica',
  PRIMARY KEY (`cod_emp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nomempresa`
--

INSERT INTO `nomempresa` (`cod_emp`, `nom_emp`, `dir_emp`, `ciu_emp`, `edo_emp`, `zon_emp`, `tel_emp`, `rif`, `nit`, `pre_sid`, `ger_rrhh`, `edadmax`, `amonemax`, `redontip`, `unidadtrib`, `tipopres`, `munidadtrib`, `diasbonvac`, `diasutilidad`, `nivel1`, `nivel2`, `nivel3`, `nivel4`, `nivel5`, `entfederal`, `distrito`, `municipio`, `codacteco`, `nomacteco`, `fecfunda`, `capital`, `degravunico`, `mescambiari`, `utcargafam`, `monsalmin`, `codcon`, `codcons`, `demo`, `rutacontab`, `rutadatoscontab`, `serial`, `ctacheque`, `ctaefectivo`, `nrocompro`, `contratos`, `nomniv1`, `nomniv2`, `nomniv3`, `nomniv4`, `nomniv5`, `recibovac`, `reciboliq`, `ee`, `fax_emp`, `num_emp`, `num_est`, `num_sso`, `estado`, `parroquia`, `localidad`, `e_mail`, `cod_entfed`, `cod_distri`, `cod_munici`, `cod_sector`, `cod_acteco`, `cod_orden`, `utilidad`, `reportdiff`, `porcdiff`, `netoneg`, `impresora`, `selector`, `nosueldocero`, `mediajornada`, `nuevassituaciones`, `tipoficha`, `conprestamos`, `confamiliares`, `conficha`, `nomcampo1`, `nomcampo2`, `nomcampo3`, `recibonom`, `tipcontab`, `contadorbanesco`, `ctapatronales`, `recibopago`, `nivel6`, `nivel7`, `nomniv6`, `nomniv7`, `imagen_izq`, `imagen_der`, `cod_material`, `unidad`, `ccosto`, `proveedor`, `moneda`, `ced_rrhh`, `tipo_empresa`) VALUES
(1, 'ORGANIZACION ', 'Cerro Ancon', 'Panama', 'San Francisco', ' +507', '', '1234567-1-123456 DV 12', '', '', 'Lic.', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'zkteco', NULL, NULL, NULL, 488.80, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '1.Region', '2.Sub-Regiones', '3.Provincia', '4.Municipio', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, '', 0, 0, NULL, '', 0, 0, '', '', '', '', '', '', '', '', 'B/.', '8-111-1111', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomexpediente`
--

CREATE TABLE IF NOT EXISTS `nomexpediente` (
  `cod_expediente_det` int(8) NOT NULL AUTO_INCREMENT,
  `cedula` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `tipo_registro` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `tipo_tiporegistro` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `descripcion` text COLLATE utf8_spanish_ci NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `monto_nuevo` decimal(10,2) NOT NULL,
  `dias` int(3) NOT NULL,
  `fecha_retorno` date NOT NULL,
  `fecha_salida` date NOT NULL,
  `cod_cargo` varchar(9) COLLATE utf8_spanish_ci NOT NULL,
  `cod_cargo_nuevo` varchar(9) COLLATE utf8_spanish_ci NOT NULL,
  `fecha` date NOT NULL,
  `usuario` varchar(30) COLLATE utf8_spanish_ci NOT NULL,
  `pagado_por_emp` varchar(2) COLLATE utf8_spanish_ci NOT NULL,
  `institucion` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `tipo_estudio` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `nivel_actual` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `costo_persona` decimal(17,2) NOT NULL,
  `num_participantes` int(4) NOT NULL,
  `nombre_especialista` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `gerencia_anterior` int(6) NOT NULL,
  `gerencia_nueva` int(6) NOT NULL,
  `nomina_anterior` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `nomina_nueva` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `puntaje` decimal(4,2) NOT NULL,
  `calificacion` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `labor` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `institucion_publica` int(1) NOT NULL,
  `tcamisa` varchar(4) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tchaqueta` varchar(4) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tbata` varchar(4) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tpantalon` varchar(4) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tmono` varchar(4) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tzapato` varchar(4) COLLATE utf8_spanish_ci DEFAULT NULL,
  `desde` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `hasta` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `horas` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `minutos` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `aprobado` date DEFAULT NULL,
  `enterado` date DEFAULT NULL,
  `resol1` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `resol2` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `resol3` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha1` date DEFAULT NULL,
  `fecha2` date DEFAULT NULL,
  `fecha3` date DEFAULT NULL,
  `dr1` int(11) DEFAULT NULL,
  `dr2` int(11) DEFAULT NULL,
  `dr3` int(11) DEFAULT NULL,
  `numero_resolucion` int(11) NOT NULL,
  `numero_decreto` int(11) NOT NULL,
  PRIMARY KEY (`cod_expediente_det`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='contiene todos los datos de expediente del personal ' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomexpediente_adjuntos`
--

CREATE TABLE IF NOT EXISTS `nomexpediente_adjuntos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `file` varchar(100) NOT NULL,
  `type` varchar(45) NOT NULL,
  `size` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `cod_expediente_det` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomexpediente_documentos`
--

CREATE TABLE IF NOT EXISTS `nomexpediente_documentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_documento` varchar(60) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `url_documento` varchar(255) NOT NULL,
  `fecha_registro` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `cod_expediente_det` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomfacturas_cabecera`
--

CREATE TABLE IF NOT EXISTS `nomfacturas_cabecera` (
  `numpre` int(9) NOT NULL,
  `ficha` varchar(11) COLLATE utf8_spanish_ci NOT NULL,
  `meses` int(2) NOT NULL,
  `fechaapro` date NOT NULL,
  `fecpricup` date NOT NULL,
  `tipint` int(2) NOT NULL,
  `monto` float(17,2) NOT NULL,
  `tasa` float(7,2) NOT NULL,
  `estadopre` varchar(21) COLLATE utf8_spanish_ci NOT NULL,
  `detalle` varchar(1000) COLLATE utf8_spanish_ci NOT NULL,
  `codigopr` varchar(7) COLLATE utf8_spanish_ci NOT NULL,
  `markar` int(2) NOT NULL,
  `codnom` int(9) NOT NULL,
  `totpres` float(17,2) NOT NULL,
  `sfechaapro` date NOT NULL,
  `sfecpricup` date NOT NULL,
  `ee` int(2) NOT NULL,
  `cuotas` int(3) DEFAULT NULL,
  `mtocuota` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomfacturas_detalles`
--

CREATE TABLE IF NOT EXISTS `nomfacturas_detalles` (
  `numpre` int(9) NOT NULL,
  `ficha` varchar(11) COLLATE utf8_spanish_ci NOT NULL,
  `tipocuo` varchar(2) COLLATE utf8_spanish_ci NOT NULL,
  `numcuo` int(9) NOT NULL,
  `fechaven` date NOT NULL,
  `anioven` int(4) NOT NULL,
  `mesven` int(2) NOT NULL,
  `dias` int(3) NOT NULL,
  `salinicial` float(17,2) NOT NULL,
  `montocuo` float(17,2) NOT NULL,
  `montoint` float(17,2) NOT NULL,
  `montocap` float(17,2) NOT NULL,
  `salfinal` float(17,2) NOT NULL,
  `fechacan` date NOT NULL,
  `estadopre` varchar(21) COLLATE utf8_spanish_ci NOT NULL,
  `detalle` varchar(1000) COLLATE utf8_spanish_ci NOT NULL,
  `dedespecial` int(2) NOT NULL,
  `codnom` int(9) NOT NULL,
  `sfechaven` date NOT NULL,
  `sfechacan` date NOT NULL,
  `ee` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomfamiliares`
--

CREATE TABLE IF NOT EXISTS `nomfamiliares` (
  `correl` int(11) NOT NULL AUTO_INCREMENT,
  `cedula` varchar(20) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0',
  `ficha` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombre` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codpar` int(11) DEFAULT NULL,
  `sexo` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_nac` datetime DEFAULT NULL,
  `codgua` int(10) unsigned NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `nacionalidad` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  `afiliado` tinyint(1) NOT NULL,
  `tipnom` int(11) NOT NULL,
  `cedula_beneficiario` int(11) NOT NULL,
  `apellido` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `niveledu` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `institucion` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `tallafranela` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `tallamono` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `fam_telf` varchar(15) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_beca` date DEFAULT NULL,
  `beca` int(1) DEFAULT NULL,
  `promedionota` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`correl`),
  KEY `ficha` (`ficha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomfrecuencias`
--

CREATE TABLE IF NOT EXISTS `nomfrecuencias` (
  `codfre` int(11) NOT NULL,
  `descrip` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `diasperiodo` int(11) DEFAULT NULL,
  `markar` tinyint(4) DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  `fecha_ini` int(11) DEFAULT NULL,
  `fecha_fin` int(11) DEFAULT NULL,
  `periodos` tinyint(1) DEFAULT NULL,
  `dfecha_ini` datetime DEFAULT NULL,
  `dfecha_fin` datetime DEFAULT NULL,
  `orden` int(3) DEFAULT NULL,
  PRIMARY KEY (`codfre`),
  KEY `fc_idx_104` (`descrip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nomfrecuencias`
--

INSERT INTO `nomfrecuencias` (`codfre`, `descrip`, `diasperiodo`, `markar`, `ee`, `fecha_ini`, `fecha_fin`, `periodos`, `dfecha_ini`, `dfecha_fin`, `orden`) VALUES
(2, '1era Quincena', 0, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL),
(3, '2da Quincena', 0, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL),
(6, 'Prestaciones', 0, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL),
(7, 'XIII MES', 0, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL),
(8, 'Vacaciones', 0, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL),
(10, 'Liquidaciones', 0, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL),
(11, 'INGRESO DE ACUMULADOS', 0, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL),
(15, 'Horas Extras y Dias Feriados', 0, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomfuncion`
--

CREATE TABLE IF NOT EXISTS `nomfuncion` (
  `nomfuncion_id` int(6) NOT NULL AUTO_INCREMENT,
  `descripcion_funcion` varchar(60) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`nomfuncion_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `nomfuncion`
--

INSERT INTO `nomfuncion` (`nomfuncion_id`, `descripcion_funcion`) VALUES
(1, 'Alcalde'),
(2, 'Gerente Admiistracion');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomgradospasos`
--

CREATE TABLE IF NOT EXISTS `nomgradospasos` (
  `grado` varchar(2) COLLATE utf8_spanish_ci NOT NULL,
  `p1` decimal(10,2) NOT NULL,
  `p2` decimal(10,2) NOT NULL,
  `p3` decimal(10,2) NOT NULL,
  `p4` decimal(10,2) NOT NULL,
  `p5` decimal(10,2) NOT NULL,
  `p6` decimal(10,2) NOT NULL,
  `p7` decimal(10,2) NOT NULL,
  `p8` decimal(10,2) NOT NULL,
  `p9` decimal(10,2) NOT NULL,
  `p10` decimal(10,2) NOT NULL,
  `p11` decimal(10,2) NOT NULL,
  `p12` decimal(10,2) NOT NULL,
  `p13` decimal(10,2) NOT NULL,
  `p14` decimal(10,2) NOT NULL,
  `p15` decimal(10,2) NOT NULL,
  PRIMARY KEY (`grado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nomgradospasos`
--

INSERT INTO `nomgradospasos` (`grado`, `p1`, `p2`, `p3`, `p4`, `p5`, `p6`, `p7`, `p8`, `p9`, `p10`, `p11`, `p12`, `p13`, `p14`, `p15`) VALUES
('1', 515000.00, 5.00, 9.00, 14.00, 19.00, 24.00, 29.00, 34.00, 40.00, 46.00, 52.00, 58.00, 64.00, 70.00, 77.00),
('10', 1160.00, 9.00, 19.00, 29.00, 39.00, 49.00, 60.00, 71.00, 82.00, 94.00, 106.00, 118.00, 131.00, 144.00, 158.00),
('11', 1160.00, 11.00, 22.00, 33.00, 45.00, 57.00, 69.00, 82.00, 95.00, 109.00, 122.00, 137.00, 152.00, 167.00, 183.00),
('12', 1160.00, 12.00, 25.00, 38.00, 52.00, 66.00, 80.00, 95.00, 110.00, 125.00, 141.00, 158.00, 175.00, 193.00, 211.00),
('13', 1160.00, 14.00, 29.00, 44.00, 60.00, 76.00, 92.00, 109.00, 127.00, 145.00, 163.00, 183.00, 202.00, 223.00, 244.00),
('14', 1160.00, 16.00, 33.00, 51.00, 69.00, 87.00, 106.00, 126.00, 146.00, 167.00, 182.00, 196.00, 210.00, 224.00, 245.00),
('15', 1160.00, 18.00, 35.00, 53.00, 71.00, 89.00, 108.00, 128.00, 148.00, 169.00, 184.00, 198.00, 212.00, 226.00, 247.00),
('16', 1182.00, 20.00, 37.00, 55.00, 73.00, 91.00, 110.00, 130.00, 150.00, 171.00, 186.00, 200.00, 214.00, 228.00, 249.00),
('17', 1221.00, 22.00, 39.00, 57.00, 75.00, 93.00, 112.00, 132.00, 152.00, 173.00, 188.00, 202.00, 216.00, 230.00, 251.00),
('18', 1265.00, 24.00, 41.00, 59.00, 77.00, 95.00, 114.00, 134.00, 154.00, 175.00, 190.00, 204.00, 218.00, 232.00, 253.00),
('19', 1314.00, 26.00, 43.00, 61.00, 79.00, 97.00, 116.00, 136.00, 156.00, 177.00, 192.00, 206.00, 220.00, 237.00, 258.00),
('2', 1160.00, 5.00, 9.00, 14.00, 19.00, 24.00, 29.00, 34.00, 40.00, 46.00, 52.00, 58.00, 64.00, 70.00, 77.00),
('20', 1366.00, 28.00, 45.00, 63.00, 81.00, 99.00, 118.00, 138.00, 158.00, 179.00, 193.00, 215.00, 237.00, 260.00, 284.00),
('21', 1424.00, 30.00, 47.00, 65.00, 83.00, 101.00, 121.00, 143.00, 165.00, 188.00, 212.00, 236.00, 261.00, 287.00, 313.00),
('25', 1722.00, 38.00, 56.00, 85.00, 115.00, 146.00, 177.00, 209.00, 242.00, 276.00, 310.00, 346.00, 382.00, 420.00, 458.00),
('26', 1814.00, 40.00, 62.00, 94.00, 127.00, 160.00, 195.00, 230.00, 266.00, 303.00, 341.00, 381.00, 420.00, 461.00, 504.00),
('3', 1160.00, 5.00, 9.00, 14.00, 19.00, 24.00, 29.00, 34.00, 40.00, 46.00, 52.00, 58.00, 64.00, 70.00, 77.00),
('4', 1160.00, 5.00, 9.00, 14.00, 19.00, 24.00, 29.00, 34.00, 40.00, 46.00, 52.00, 58.00, 64.00, 70.00, 77.00),
('5', 1160.00, 5.00, 11.00, 16.00, 22.00, 28.00, 34.00, 40.00, 46.00, 53.00, 60.00, 67.00, 74.00, 81.00, 89.00),
('6', 1160.00, 5.00, 11.00, 16.00, 22.00, 28.00, 34.00, 40.00, 46.00, 53.00, 60.00, 67.00, 74.00, 81.00, 89.00),
('7', 1160.00, 6.00, 12.00, 19.00, 25.00, 32.00, 39.00, 46.00, 53.00, 61.00, 69.00, 77.00, 85.00, 94.00, 103.00),
('8', 1160.00, 7.00, 14.00, 21.00, 29.00, 37.00, 45.00, 53.00, 62.00, 70.00, 79.00, 89.00, 98.00, 108.00, 118.00),
('9', 1160.00, 8.00, 16.00, 25.00, 34.00, 43.00, 52.00, 61.00, 71.00, 81.00, 92.00, 103.00, 114.00, 125.00, 137.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomgrupos_categorias`
--

CREATE TABLE IF NOT EXISTS `nomgrupos_categorias` (
  `gr` int(10) unsigned NOT NULL,
  `salario` decimal(17,2) DEFAULT NULL,
  `bonomes` decimal(17,2) DEFAULT NULL,
  `bonodia` decimal(17,2) DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`gr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomgrupo_bancos`
--

CREATE TABLE IF NOT EXISTS `nomgrupo_bancos` (
  `cod_gban` int(11) NOT NULL,
  `des_ban` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `suc_ban` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `direccion` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `gerente` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `cuentacob` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipocuenta` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `markar` tinyint(4) DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  `textoinicial` text COLLATE utf8_spanish_ci NOT NULL,
  `textofinal` text COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`cod_gban`),
  KEY `fc_idx_107` (`des_ban`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nomgrupo_bancos`
--

INSERT INTO `nomgrupo_bancos` (`cod_gban`, `des_ban`, `suc_ban`, `direccion`, `gerente`, `cuentacob`, `tipocuenta`, `markar`, `ee`, `textoinicial`, `textofinal`) VALUES
(1, 'GRUPO UNICO', '', '', '', '', '', 0, 0, '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomguarderias`
--

CREATE TABLE IF NOT EXISTS `nomguarderias` (
  `codorg` int(11) NOT NULL,
  `codsuc` int(11) DEFAULT NULL,
  `rif` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `descrip` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `dir_emp` varchar(120) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tel_emp` varchar(25) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipo_ins` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codinst` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `montinscr` decimal(17,2) DEFAULT NULL,
  `montmen` decimal(17,2) DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`codorg`),
  KEY `fc_idx_117` (`descrip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nominstruccion`
--

CREATE TABLE IF NOT EXISTS `nominstruccion` (
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(60) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nominstruccion`
--

INSERT INTO `nominstruccion` (`codigo`, `descripcion`) VALUES
(1, 'Magister'),
(2, 'Doctorado'),
(3, 'Universitario'),
(4, 'Tecnico Superior'),
(5, 'Tecnico Medio'),
(6, 'Bachiller'),
(7, 'Escolar'),
(8, 'Sin Estudios Terminados'),
(9, 'Primaria Incompleta'),
(10, 'Sin Terminar Bachillerato');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomliquidaciones`
--

CREATE TABLE IF NOT EXISTS `nomliquidaciones` (
  `cod_tli` int(10) unsigned NOT NULL,
  `des_tli` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `markar` tinyint(4) DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`cod_tli`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nomliquidaciones`
--

INSERT INTO `nomliquidaciones` (`cod_tli`, `des_tli`, `markar`, `ee`) VALUES
(1, 'Contrato Definido Periodo de Prueba o Culminacion', 0, 0),
(2, 'Contrato Definido Renuncia o Despido Justificado', 0, 0),
(3, 'Contrato Indefinido Renuncia o Justificado', 0, 0),
(4, 'Contrato Indefinido Despido Injustificado', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomnivel1`
--

CREATE TABLE IF NOT EXISTS `nomnivel1` (
  `codorg` int(8) NOT NULL,
  `descrip` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `gerencia` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `markar` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `ee` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion_corta` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion_completa` varchar(150) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipo_presup` varchar(1) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `programa` varchar(1) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `fuente_finan` varchar(3) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `sub_programa` varchar(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `actividad` varchar(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `objeto_gasto` varchar(3) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `estatus` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`codorg`),
  KEY `fc_idx_165` (`markar`),
  KEY `fc_idx_166` (`descrip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `nomnivel1`
--

INSERT INTO `nomnivel1` (`codorg`, `descrip`, `gerencia`, `markar`, `ee`, `descripcion_corta`, `descripcion_completa`, `tipo_presup`, `programa`, `fuente_finan`, `sub_programa`, `actividad`, `objeto_gasto`, `estatus`) VALUES
(1, 'Nivel 1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomnivel2`
--

CREATE TABLE IF NOT EXISTS `nomnivel2` (
  `codorg` int(8) NOT NULL,
  `descrip` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `gerencia` int(8) DEFAULT NULL,
  `markar` varchar(30) DEFAULT NULL,
  `ee` varchar(30) DEFAULT NULL,
  `descripcion_corta` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion_completa` varchar(150) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipo_presup` varchar(1) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `programa` varchar(1) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `fuente_finan` varchar(3) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `sub_programa` varchar(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `actividad` varchar(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `objeto_gasto` varchar(3) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `estatus` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`codorg`),
  KEY `fc_idx_165` (`markar`),
  KEY `fc_idx_166` (`descrip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomnivel3`
--

CREATE TABLE IF NOT EXISTS `nomnivel3` (
  `codorg` int(8) NOT NULL,
  `descrip` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `gerencia` int(8) DEFAULT NULL,
  `markar` varchar(30) DEFAULT NULL,
  `ee` varchar(30) DEFAULT NULL,
  `descripcion_corta` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion_completa` varchar(150) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipo_presup` varchar(1) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `programa` varchar(1) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `fuente_finan` varchar(3) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `sub_programa` varchar(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `actividad` varchar(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `objeto_gasto` varchar(3) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `estatus` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`codorg`),
  KEY `fc_idx_184` (`descrip`),
  KEY `fc_idx_185` (`markar`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomnivel4`
--

CREATE TABLE IF NOT EXISTS `nomnivel4` (
  `codorg` int(8) NOT NULL,
  `descrip` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `gerencia` int(8) DEFAULT NULL,
  `markar` varchar(30) DEFAULT NULL,
  `ee` varchar(30) DEFAULT NULL,
  `descripcion_corta` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `descripcion_completa` varchar(150) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipo_presup` varchar(1) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `programa` varchar(1) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `fuente_finan` varchar(3) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `sub_programa` varchar(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `actividad` varchar(2) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `objeto_gasto` varchar(3) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `estatus` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`codorg`),
  KEY `fc_idx_110` (`descrip`),
  KEY `fc_idx_111` (`markar`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomnivel5`
--

CREATE TABLE IF NOT EXISTS `nomnivel5` (
  `codorg` int(8) NOT NULL,
  `descrip` varchar(60) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `gerencia` int(8) DEFAULT NULL,
  `markar` varchar(30) DEFAULT NULL,
  `ee` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`codorg`),
  KEY `fc_idx_72` (`descrip`),
  KEY `fc_idx_73` (`markar`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomnivel6`
--

CREATE TABLE IF NOT EXISTS `nomnivel6` (
  `codorg` int(8) NOT NULL,
  `descrip` varchar(60) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `gerencia` int(8) DEFAULT NULL,
  `markar` varchar(30) DEFAULT NULL,
  `ee` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`codorg`),
  KEY `fc_idx_72` (`descrip`),
  KEY `fc_idx_73` (`markar`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomnivel7`
--

CREATE TABLE IF NOT EXISTS `nomnivel7` (
  `codorg` int(8) NOT NULL,
  `descrip` varchar(60) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `gerencia` int(8) DEFAULT NULL,
  `markar` varchar(30) DEFAULT NULL,
  `ee` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`codorg`),
  KEY `fc_idx_72` (`descrip`),
  KEY `fc_idx_73` (`markar`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomparentescos`
--

CREATE TABLE IF NOT EXISTS `nomparentescos` (
  `codorg` varchar(6) COLLATE utf8_spanish_ci NOT NULL,
  `descrip` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`codorg`),
  KEY `fc_idx_153` (`descrip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nomparentescos`
--

INSERT INTO `nomparentescos` (`codorg`, `descrip`, `ee`) VALUES
('1', 'Madre', 0),
('2', 'Padre', 0),
('3', 'Hijo(a)', 0),
('4', 'Conyuge', 0),
('5', 'Concubino(a)', 0),
('6', 'Nieto(a)', 0),
('7', 'Esposo(a)', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomperiodos`
--

CREATE TABLE IF NOT EXISTS `nomperiodos` (
  `codfre` int(5) NOT NULL,
  `anio` int(4) NOT NULL,
  `nper` int(2) NOT NULL,
  `finicio` date NOT NULL,
  `ffin` date NOT NULL,
  `status` varchar(7) CHARACTER SET latin1 NOT NULL,
  `semfin` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`codfre`,`anio`,`nper`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nompersonal`
--

CREATE TABLE IF NOT EXISTS `nompersonal` (
  `personal_id` int(11) NOT NULL AUTO_INCREMENT,
  `cedula` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `apenom` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `sexo` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `estado_civil` varchar(13) COLLATE utf8_spanish_ci DEFAULT NULL,
  `direccion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `zonapos` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `telefonos` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `email` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecnac` date DEFAULT NULL,
  `lugarnac` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codpro` int(11) DEFAULT NULL,
  `foto` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipnom` int(11) DEFAULT '0',
  `codnivel1` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnivel2` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnivel3` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnivel4` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnivel5` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ficha` int(10) DEFAULT NULL,
  `fecing` date DEFAULT NULL,
  `codcat` varchar(6) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codcargo` varchar(12) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nomposicion_id` int(11) DEFAULT NULL,
  `forcob` varchar(39) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codbancob` int(11) DEFAULT NULL,
  `cuentacob` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codbanlph` int(11) DEFAULT NULL,
  `cuentalph` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `estado` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipemp` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecfin` int(11) DEFAULT NULL,
  `sueldopro` decimal(20,2) DEFAULT NULL,
  `fechaplica` date DEFAULT NULL,
  `codidi` int(11) DEFAULT NULL,
  `fecnacr` varchar(5) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipopres` tinyint(4) DEFAULT NULL,
  `fechasus` date DEFAULT NULL,
  `fechareisus` date DEFAULT NULL,
  `fechavac` date DEFAULT NULL,
  `fechareivac` date DEFAULT NULL,
  `fecharetiro` date DEFAULT NULL,
  `aplicalogro` tinyint(4) DEFAULT NULL,
  `aplicasuspension` tinyint(4) DEFAULT NULL,
  `ctacontab` varchar(22) COLLATE utf8_spanish_ci DEFAULT NULL,
  `periodo` int(11) DEFAULT NULL,
  `markar` tinyint(4) DEFAULT NULL,
  `cod_tli` varchar(19) COLLATE utf8_spanish_ci DEFAULT NULL,
  `motivo_liq` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `preaviso` varchar(2) COLLATE utf8_spanish_ci DEFAULT NULL,
  `suesal` decimal(20,2) DEFAULT NULL,
  `contrato` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombres` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `apellidos` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `montoliq` decimal(17,2) DEFAULT NULL,
  `sfecnac` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `sfecing` date DEFAULT NULL,
  `sfecfin` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `sfechaplica` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `sfechasus` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `sfechareisus` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `sfechavac` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `sfechareivac` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `sfecharetiro` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nacionalidad` tinyint(4) DEFAULT NULL,
  `diascontrato` int(11) DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  `dfecnac` date DEFAULT NULL,
  `dfecing` date DEFAULT NULL,
  `dfecfin` date DEFAULT NULL,
  `dfechaplica` date DEFAULT NULL,
  `dfechasus` date DEFAULT NULL,
  `dfechareisus` date DEFAULT NULL,
  `dfechavac` date DEFAULT NULL,
  `dfechareivac` date DEFAULT NULL,
  `dfecharetiro` date DEFAULT NULL,
  `nrocuadrilla` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnivel6` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnivel7` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `inicio_periodo` date DEFAULT NULL,
  `fin_periodo` date DEFAULT NULL,
  `fechajubipensi` date DEFAULT NULL,
  `porjubipensi` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `antiguedadap` varchar(2) COLLATE utf8_spanish_ci DEFAULT NULL,
  `paso` int(2) DEFAULT NULL,
  `motivo_retiro` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `turno_id` bigint(32) DEFAULT NULL,
  `seguro_social` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `hora_base` decimal(10,2) DEFAULT NULL,
  `segurosocial_sipe` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `dv` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `num_decreto` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_decreto` date DEFAULT NULL,
  `num_decreto_baja` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_decreto_baja` date DEFAULT NULL,
  `siacap` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `puesto_id` int(11) DEFAULT NULL,
  `imagen_cedula` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `mes1` int(11) DEFAULT NULL,
  `sueld2` decimal(11,2) DEFAULT NULL,
  `mes2` int(11) DEFAULT NULL,
  `sueld3` decimal(11,2) DEFAULT NULL,
  `mes3` int(11) DEFAULT NULL,
  `sueldp` decimal(11,2) DEFAULT NULL,
  `cta_presupuestaria` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipo_empleado` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL COMMENT 'Interino o Titular',
  `clave_ir` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `IdTipoSangre` int(11) DEFAULT NULL,
  `TelefonoResidencial` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `TelefonoCelular` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ContactoEmergencia` varchar(300) COLLATE utf8_spanish_ci DEFAULT NULL,
  `TelefonoContactoEmergencia` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `Hijos` int(11) DEFAULT NULL,
  `EnfermedadesYAlergias` varchar(600) COLLATE utf8_spanish_ci DEFAULT NULL,
  `useruid` varchar(300) COLLATE utf8_spanish_ci DEFAULT NULL,
  `jefe` char(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `uid_user_aprueba` varchar(300) COLLATE utf8_spanish_ci DEFAULT NULL,
  `condicion` char(3) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tiene_discapacidad` char(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tiene_familiar_disca` char(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `personal_externo` char(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `comentario` varchar(765) COLLATE utf8_spanish_ci DEFAULT NULL,
  `id_institucion` int(11) DEFAULT NULL,
  `fecha_permanencia` datetime DEFAULT NULL,
  `apellido_materno` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `apellido_casada` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `observaciones` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `direccion2` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`personal_id`),
  UNIQUE KEY `tipnom` (`tipnom`,`ficha`),
  UNIQUE KEY `ficha` (`ficha`,`cedula`),
  KEY `codcargo` (`codcargo`),
  KEY `turno_id` (`turno_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AVG_ROW_LENGTH=290 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nompersonal_constancias`
--

CREATE TABLE IF NOT EXISTS `nompersonal_constancias` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_constancia` int(11) NOT NULL,
  `codigo_validacion` varchar(16) NOT NULL,
  `ficha` int(10) NOT NULL,
  `fecha_emision` datetime NOT NULL,
  `validada` varchar(2) DEFAULT 'No',
  `fecha_validacion` datetime DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomposicion`
--

CREATE TABLE IF NOT EXISTS `nomposicion` (
  `nomposicion_id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion_posicion` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  `sueldo_propuesto` decimal(10,2) DEFAULT NULL,
  `sueldo_anual` decimal(10,2) DEFAULT NULL,
  `creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `modificacion` timestamp NULL DEFAULT NULL,
  `partida` varchar(25) COLLATE utf8_spanish_ci DEFAULT NULL,
  `gastos_representacion` decimal(19,2) DEFAULT NULL,
  `paga_gr` tinyint(1) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `cargo_id` int(11) DEFAULT NULL,
  `cod_nivel1` int(8) DEFAULT NULL,
  `cod_nivel2` int(8) DEFAULT NULL,
  `cod_nivel3` int(8) DEFAULT NULL,
  `cod_nivel4` int(8) DEFAULT NULL,
  `cod_nivel5` int(8) DEFAULT NULL,
  `cod_nivel6` int(8) DEFAULT NULL,
  `cod_nivel7` int(8) DEFAULT NULL,
  `sueldo_2` decimal(10,2) DEFAULT NULL,
  `sueldo_3` decimal(10,2) DEFAULT NULL,
  `sueldo_4` decimal(10,2) DEFAULT NULL,
  `mes_1` int(2) DEFAULT NULL,
  `mes_2` int(2) DEFAULT NULL,
  `mes_3` int(2) DEFAULT NULL,
  `mes_4` int(2) DEFAULT NULL,
  `sobresueldo_antiguedad_1` decimal(10,2) DEFAULT NULL,
  `sobresueldo_antiguedad_2` decimal(10,2) DEFAULT NULL,
  `sobresueldo_zona_apartada_1` decimal(10,2) DEFAULT NULL,
  `sobresueldo_zona_apartada_2` decimal(10,2) DEFAULT NULL,
  `sobresueldo_jefatura_1` decimal(10,2) DEFAULT NULL,
  `sobresueldo_jefatura_2` decimal(10,2) DEFAULT NULL,
  `sobresueldo_otros_1` decimal(10,2) DEFAULT NULL,
  `sobresueldo_otros_2` decimal(10,2) DEFAULT NULL,
  `mes_ant_1` int(2) DEFAULT NULL,
  `mes_ant_2` int(2) DEFAULT NULL,
  `mes_za_1` int(2) DEFAULT NULL,
  `mes_za_2` int(2) DEFAULT NULL,
  `mes_jef_1` int(2) DEFAULT NULL,
  `mes_jef_2` int(2) DEFAULT NULL,
  `mes_ot_1` int(2) DEFAULT NULL,
  `mes_ot_2` int(2) DEFAULT NULL,
  `status` enum('Vacante','Ocupado') COLLATE utf8_spanish_ci DEFAULT NULL,
  `partida011` varchar(25) COLLATE utf8_spanish_ci DEFAULT NULL,
  `partida012` varchar(25) COLLATE utf8_spanish_ci DEFAULT NULL,
  `partida013` varchar(25) COLLATE utf8_spanish_ci DEFAULT NULL,
  `partida019` varchar(25) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`nomposicion_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `nomposicion`
--

INSERT INTO `nomposicion` (`nomposicion_id`, `descripcion_posicion`, `sueldo_propuesto`, `sueldo_anual`, `creacion`, `modificacion`, `partida`, `gastos_representacion`, `paga_gr`, `categoria_id`, `cargo_id`, `cod_nivel1`, `cod_nivel2`, `cod_nivel3`, `cod_nivel4`, `cod_nivel5`, `cod_nivel6`, `cod_nivel7`, `sueldo_2`, `sueldo_3`, `sueldo_4`, `mes_1`, `mes_2`, `mes_3`, `mes_4`, `sobresueldo_antiguedad_1`, `sobresueldo_antiguedad_2`, `sobresueldo_zona_apartada_1`, `sobresueldo_zona_apartada_2`, `sobresueldo_jefatura_1`, `sobresueldo_jefatura_2`, `sobresueldo_otros_1`, `sobresueldo_otros_2`, `mes_ant_1`, `mes_ant_2`, `mes_za_1`, `mes_za_2`, `mes_jef_1`, `mes_jef_2`, `mes_ot_1`, `mes_ot_2`, `status`, `partida011`, `partida012`, `partida013`, `partida019`) VALUES
(1, 'Posicion 1', 1000.00, NULL, '2016-04-18 19:13:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomprestamos`
--

CREATE TABLE IF NOT EXISTS `nomprestamos` (
  `codigopr` int(10) unsigned NOT NULL,
  `descrip` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `formula` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `markar` tinyint(4) DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  `subclave` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`codigopr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomprestamos_cabecera`
--

CREATE TABLE IF NOT EXISTS `nomprestamos_cabecera` (
  `numpre` int(9) NOT NULL,
  `ficha` int(11) NOT NULL,
  `meses` int(2) NOT NULL,
  `fechaapro` date NOT NULL,
  `fecpricup` date NOT NULL,
  `tipint` int(2) NOT NULL,
  `monto` float(17,2) NOT NULL,
  `tasa` float(7,2) NOT NULL,
  `estadopre` varchar(21) COLLATE utf8_spanish_ci NOT NULL,
  `detalle` varchar(1000) COLLATE utf8_spanish_ci NOT NULL,
  `codigopr` varchar(7) COLLATE utf8_spanish_ci NOT NULL,
  `markar` int(2) NOT NULL,
  `codnom` int(9) NOT NULL,
  `totpres` float(17,2) NOT NULL,
  `sfechaapro` date NOT NULL,
  `sfecpricup` date NOT NULL,
  `ee` int(2) NOT NULL,
  `cuotas` int(3) DEFAULT NULL,
  `mtocuota` decimal(10,2) DEFAULT NULL,
  `usuarioanu` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `fechaanu` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `diciembre` int(1) NOT NULL,
  PRIMARY KEY (`numpre`),
  KEY `ficha` (`ficha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomprestamos_detalles`
--

CREATE TABLE IF NOT EXISTS `nomprestamos_detalles` (
  `numpre` int(9) NOT NULL,
  `ficha` int(11) NOT NULL,
  `tipocuo` varchar(2) COLLATE utf8_spanish_ci NOT NULL,
  `numcuo` int(9) NOT NULL,
  `fechaven` date NOT NULL,
  `anioven` int(4) NOT NULL,
  `mesven` int(2) NOT NULL,
  `dias` int(3) NOT NULL,
  `salinicial` float(17,2) NOT NULL,
  `montocuo` float(17,2) NOT NULL,
  `montoint` float(17,2) NOT NULL,
  `montocap` float(17,2) NOT NULL,
  `salfinal` float(17,2) NOT NULL,
  `fechacan` date NOT NULL,
  `estadopre` varchar(21) COLLATE utf8_spanish_ci NOT NULL,
  `detalle` varchar(1000) COLLATE utf8_spanish_ci NOT NULL,
  `dedespecial` int(2) NOT NULL,
  `codnom` int(9) NOT NULL,
  `sfechaven` date NOT NULL,
  `sfechacan` date NOT NULL,
  `ee` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomprofesiones`
--

CREATE TABLE IF NOT EXISTS `nomprofesiones` (
  `codorg` int(11) NOT NULL AUTO_INCREMENT,
  `descrip` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`codorg`),
  KEY `fc_idx_158` (`descrip`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=25 ;

--
-- Volcado de datos para la tabla `nomprofesiones`
--

INSERT INTO `nomprofesiones` (`codorg`, `descrip`, `ee`) VALUES
(4, 'LICENCIADO', NULL),
(11, 'TECNICO SUPERIOR UNIVERSITARIO (TSU)', NULL),
(12, 'TECNICO MEDIO', NULL),
(13, 'TECNICO', NULL),
(15, 'BACHILLER', NULL),
(16, 'EDUCACION BASICA COMPLETA', NULL),
(17, 'PRIMARIA COMPLETA', NULL),
(18, 'PRIMARIA INCOMPLETA', NULL),
(19, 'UNIVERSITARIO INCOMPLETO', 0),
(20, 'UNIVERSITARIO ', 0),
(21, 'POST GRADO', 0),
(22, 'MAESTRIA', 0),
(23, 'DOCTORADO', 0),
(24, 'N/A', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nompuestos`
--

CREATE TABLE IF NOT EXISTS `nompuestos` (
  `id_puestos` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  PRIMARY KEY (`id_puestos`),
  UNIQUE KEY `id_puestos` (`id_puestos`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomseguro`
--

CREATE TABLE IF NOT EXISTS `nomseguro` (
  `id_seguro` int(11) NOT NULL AUTO_INCREMENT,
  `desde_seg` int(11) NOT NULL,
  `hasta_seg` int(11) NOT NULL,
  `monto_seg` float(6,2) NOT NULL,
  PRIMARY KEY (`id_seguro`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED AUTO_INCREMENT=7 ;

--
-- Volcado de datos para la tabla `nomseguro`
--

INSERT INTO `nomseguro` (`id_seguro`, `desde_seg`, `hasta_seg`, `monto_seg`) VALUES
(1, 0, 17, 184.75),
(3, 18, 39, 229.00),
(4, 40, 49, 309.75),
(5, 50, 59, 407.67),
(6, 60, 80, 580.17);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomsituaciones`
--

CREATE TABLE IF NOT EXISTS `nomsituaciones` (
  `codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `situacion` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`codigo`),
  KEY `situacion` (`situacion`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=10 ;

--
-- Volcado de datos para la tabla `nomsituaciones`
--

INSERT INTO `nomsituaciones` (`codigo`, `situacion`) VALUES
(2, 'COMPROMETIDA'),
(9, 'CONGELADA'),
(8, 'LEGALIZAR'),
(4, 'LICENCIA CON SUELDO'),
(5, 'LICENCIA SIN SUELDO'),
(7, 'POR ELIMINAR'),
(6, 'REGULAR'),
(3, 'VACACIONES'),
(1, 'VACANTE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomsuspenciones`
--

CREATE TABLE IF NOT EXISTS `nomsuspenciones` (
  `codigo` int(11) NOT NULL,
  `descrip` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`codigo`),
  KEY `fc_idx_143` (`descrip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nomsuspenciones`
--

INSERT INTO `nomsuspenciones` (`codigo`, `descrip`, `ee`) VALUES
(1, 'Enfermedad', 0),
(2, 'Duelo', 0),
(3, 'Matrimonio', 0),
(4, 'Nacimiento de hijo', 0),
(5, 'Enfermedad de parientes cercanos', 0),
(6, 'Eventos acadÃƒÂ©micos puntuales', 0),
(7, 'Otros asuntos personales', 0),
(8, 'Estudios', 1),
(9, 'CapacitaciÃƒÂ³n', 1),
(10, 'RepresentaciÃƒÂ³n de la instituciÃƒÂ³n, el Estado o el PaÃƒÂ', 1),
(11, 'RepresentaciÃƒÂ³n de la AsociaciÃƒÂ³n de Servidores PÃƒÂºbli', 1),
(12, 'Para comparecer ante autoridades judiciales o administrativa', 1),
(13, 'Asumir un cargo de elecciÃƒÂ³n popular', 1),
(14, 'Asumir un cargo de libre nombramiento y remociÃƒÂ³n', 1),
(15, 'Asuntos personales', 1),
(16, 'Gravidez', 1),
(17, 'Enfermedad con incapacidad superior a los quince (15) dÃƒÂ­a', 1),
(18, 'Riesgos profesionales', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomtarifas`
--

CREATE TABLE IF NOT EXISTS `nomtarifas` (
  `limite_menor` decimal(18,2) NOT NULL,
  `limite_mayor` decimal(18,2) NOT NULL,
  `monto` decimal(18,2) NOT NULL,
  `codigo` int(11) NOT NULL,
  PRIMARY KEY (`limite_mayor`,`codigo`),
  KEY `nomtarifas_ibfk_1` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nomtarifas`
--

INSERT INTO `nomtarifas` (`limite_menor`, `limite_mayor`, `monto`, `codigo`) VALUES
(0.00, 11000.00, 0.00, 1),
(11001.00, 50000.00, 15.00, 1),
(50001.00, 585000.00, 25.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomtasas_interes`
--

CREATE TABLE IF NOT EXISTS `nomtasas_interes` (
  `tasa` decimal(7,2) DEFAULT NULL,
  `anio` int(11) NOT NULL,
  `mes` int(11) NOT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`anio`,`mes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomterceros`
--

CREATE TABLE IF NOT EXISTS `nomterceros` (
  `codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomtipos_constancia`
--

CREATE TABLE IF NOT EXISTS `nomtipos_constancia` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) NOT NULL,
  `tipo_documento` enum('pdf','docx') NOT NULL DEFAULT 'pdf' COMMENT 'Tipo de documento que se genera',
  `contenido1` text,
  `contenido2` text,
  `contenido3` text,
  `titulo` varchar(70) DEFAULT NULL,
  `observaciones` text,
  `configuracion` enum('Header','Template','Ninguno') DEFAULT 'Template',
  `template` varchar(255) DEFAULT NULL,
  `formula` mediumtext,
  `archivo` varchar(255) DEFAULT NULL,
  `fuente` enum('Calibri') DEFAULT NULL,
  `margen_sup` int(11) DEFAULT NULL,
  `margen_izq` int(11) DEFAULT NULL,
  `margen_der` int(11) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=3 ;

--
-- Volcado de datos para la tabla `nomtipos_constancia`
--

INSERT INTO `nomtipos_constancia` (`codigo`, `nombre`, `tipo_documento`, `contenido1`, `contenido2`, `contenido3`, `titulo`, `observaciones`, `configuracion`, `template`, `formula`, `archivo`, `fuente`, `margen_sup`, `margen_izq`, `margen_der`) VALUES
(1, 'Sueldo Básico', 'pdf', NULL, '<p>Panam&aacute;, $DIA de $MES del $ANIO.</p>\r\n\r\n<p style="line-height:195%"><strong>E.S.M.</strong></p>\r\n\r\n<p style="line-height:90%"><strong>A quien concierna:</strong></p>\r\n\r\n<p>Por este medio, la <u>empresa</u> Electron Investment, S.A. (EISA) certifica que:<br />\r\nEl <strong>Sr. $NOMBRE</strong>, var&oacute;n, Paname&ntilde;o, mayor de edad, con C&eacute;dula de identidad&nbsp;<strong>N&deg; $CEDULA</strong>, es trabajador activo de nuestra empresa desde el&nbsp;&nbsp;<strong>$FECHA_INGRESO</strong><strong> y continua laborando a la fecha.&nbsp;</strong>El Sr. $PRIMER_APELLIDO desempe&ntilde;a el cargo de <strong>$CARGO</strong>, devengando actualmente un salario mensual de:&nbsp;<strong>$SUELDO_LETRAS_SALARIOBRUTO(\\$$SALARIO_BRUTO)</strong> en concepto de <strong>COMPENSACI&Oacute;N TOTAL BRUTA </strong>con las siguientes deducciones establecidas por la ley<strong>:</strong></p>\r\n\r\n<table border="0" cellpadding="1" cellspacing="1" style="width:549px">\r\n	<tbody>\r\n		<tr>\r\n			<td style="width:175px"><strong>Salario:</strong></td>\r\n			<td style="text-align:right; width:70px"><strong>$SUELDO</strong></td>\r\n			<td style="width:60px">&nbsp;</td>\r\n			<td style="width:169px"><strong>Menos Descuentos</strong></td>\r\n			<td style="text-align:right; width:70px">&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="width:175px"><strong>Gastos de Representaci&oacute;n:</strong></td>\r\n			<td style="text-align:right; width:70px"><strong>$GASTOS_REPRESENTACION</strong></td>\r\n			<td style="width:60px">&nbsp;</td>\r\n			<td style="width:169px">Seguro Social:</td>\r\n			<td style="text-align:right; width:70px"><strong>$SEGURO_SOCIAL</strong></td>\r\n		</tr>\r\n		<tr>\r\n			<td style="width:175px"><strong>Salario Bruto:</strong></td>\r\n			<td style="text-align:right; width:70px"><strong>$SALARIO_BRUTO</strong></td>\r\n			<td style="width:60px">&nbsp;</td>\r\n			<td style="width:169px">Seguro Educativo:</td>\r\n			<td style="text-align:right; width:70px"><strong>$SEGURO_EDUCATIVO</strong></td>\r\n		</tr>\r\n		<tr>\r\n			<td style="width:175px">&nbsp;</td>\r\n			<td style="width:70px">&nbsp;</td>\r\n			<td style="width:60px">&nbsp;</td>\r\n			<td style="width:169px">Impuesto Sobre la Renta:</td>\r\n			<td style="text-align:right; width:70px"><strong>$IMPUESTO_SOBRE_RENTA</strong></td>\r\n		</tr>\r\n		<tr>\r\n			<td style="width:175px">&nbsp;</td>\r\n			<td style="width:70px">&nbsp;</td>\r\n			<td style="width:60px">&nbsp;</td>\r\n			<td style="width:169px"><strong>Casa Comercial:</strong></td>\r\n			<td style="text-align:right; width:70px"><strong>$CASA_COMERCIAL</strong></td>\r\n		</tr>\r\n		<tr>\r\n			<td style="width:175px">&nbsp;</td>\r\n			<td style="width:70px">&nbsp;</td>\r\n			<td style="width:60px">&nbsp;</td>\r\n			<td style="width:169px"><strong>Total descuentos:</strong></td>\r\n			<td style="text-align:right; width:70px"><strong>$TOTAL_DESCUENTOS</strong></td>\r\n		</tr>\r\n		<tr>\r\n			<td style="width:175px">&nbsp;</td>\r\n			<td style="width:70px">&nbsp;</td>\r\n			<td style="width:60px">&nbsp;</td>\r\n			<td style="width:169px"><strong>Salario Mensual Neto:</strong></td>\r\n			<td style="text-align:right; width:70px"><u><strong>$SALARIO_NETO</strong></u></td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n\r\n<p>Sin m&aacute;s a que hacer referencia<br />\r\nSe despide de usted atentamente,</p>\r\n', NULL, 'CARTA DE TRABAJO', NULL, 'Ninguno', 'carta_de_trabajo.pdf', '', NULL, NULL, NULL, NULL, NULL),
(2, 'Certificación de Salarios', 'pdf', NULL, '<p style="text-align:center"><span style="font-size:15px"><strong><span style="font-family:times new roman,times,serif">CERTIFICACION</span></strong></span></p>\r\n\r\n<p style="line-height:40%">&nbsp;</p>\r\n\r\n<p style="line-height:150%; text-align:center"><span style="font-size:15px"><span style="font-family:times new roman,times,serif">RETENCIONES EFECTUADAS POR EL EMPLEADOR</span></span></p>\r\n\r\n<p style="line-height:130%"><span style="font-size:15px"><span style="font-family:times new roman,times,serif">La suscrita Gloria Vega con c&eacute;dula de identidad personal 4-142-1447 Contador P&uacute;blico Autorizado, en representaci&oacute;n Electron Investment, S.A. con R.U.C. 12453-217-123692, para la presente y con pleno conocimiento de las responsabilidades que se&ntilde;ala las leyes de la Rep&uacute;blica.</span></span></p>\r\n\r\n<p style="line-height:10%; text-align:center"><span style="font-size:15px"><strong><span style="font-family:times new roman,times,serif">C E R T I F I C O</span></strong></span></p>\r\n\r\n<p style="line-height:130%"><span style="font-size:15px"><span style="font-family:times new roman,times,serif">Que $PRIMER_NOMBRE $APELLIDOS</span>&nbsp;</span><span style="font-size:15px"><span style="font-family:times new roman,times,serif">con c&eacute;dula de identidad personal No. $CEDULA, es empleado de esta empresa, y deveng&oacute; las siguientes remuneraciones y se le retuvo durante el a&ntilde;o fiscal&nbsp; $ANIO_FISCAL&nbsp;las siguientes sumas:</span></span></p>\r\n\r\n<table border="0" cellpadding="1" cellspacing="1" style="width:398px">\r\n	<tbody>\r\n		<tr>\r\n			<td style="width:164px">\r\n			<p style="line-height:150%"><span style="font-size:14px"><strong>CONCEPTO</strong></span></p>\r\n			</td>\r\n			<td style="width:13px">\r\n			<p style="line-height:40%">&nbsp;</p>\r\n			</td>\r\n			<td style="width:71px">\r\n			<p style="line-height:110%; text-align:right"><span style="font-size:14px"><strong>INGRESOS &nbsp;RECIBIDOS</strong></span></p>\r\n			</td>\r\n			<td style="width:25px">\r\n			<p style="line-height:40%; text-align:center">&nbsp;</p>\r\n			</td>\r\n			<td style="width:93px">\r\n			<p style="line-height:110%; text-align:right"><span style="font-size:14px"><strong>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; SUMA &nbsp; &nbsp; &nbsp;RETENIDA</strong></span></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="width:164px">\r\n			<p style="line-height:100%">&nbsp;</p>\r\n			</td>\r\n			<td style="width:13px">\r\n			<p style="line-height:100%">&nbsp;</p>\r\n			</td>\r\n			<td style="width:71px">\r\n			<p style="line-height:100%">&nbsp;</p>\r\n			</td>\r\n			<td style="width:25px">\r\n			<p style="line-height:100%">&nbsp;</p>\r\n			</td>\r\n			<td style="text-align:center; width:93px">\r\n			<p style="line-height:160%">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; I.S.R.</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="width:164px">\r\n			<p style="line-height:100%">&nbsp;</p>\r\n			</td>\r\n			<td style="width:13px">\r\n			<p style="line-height:100%">&nbsp;</p>\r\n			</td>\r\n			<td style="width:71px">\r\n			<p style="line-height:100%">&nbsp;</p>\r\n			</td>\r\n			<td style="width:25px">\r\n			<p style="line-height:100%">&nbsp;</p>\r\n			</td>\r\n			<td style="text-align:center; width:93px">&nbsp;</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="width:164px">\r\n			<p style="line-height:90%"><span style="font-size:14px">Salarios</span></p>\r\n			</td>\r\n			<td style="width:13px">\r\n			<p style="line-height:90%">&nbsp;</p>\r\n			</td>\r\n			<td style="text-align:right; width:71px">\r\n			<p style="line-height:90%"><span style="font-size:14px">$SALARIOS_ANIO_FISCAL</span></p>\r\n			</td>\r\n			<td style="width:25px">\r\n			<p style="line-height:90%">&nbsp;</p>\r\n			</td>\r\n			<td style="text-align:right; width:93px">\r\n			<p style="line-height:90%"><span style="font-size:14px">$ISR_SALARIOS</span></p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="width:164px">\r\n			<p style="line-height:90%"><span style="font-size:14px">D&eacute;cimo tercer Mes Salario</span></p>\r\n			</td>\r\n			<td style="width:13px">\r\n			<p style="line-height:90%">&nbsp;</p>\r\n			</td>\r\n			<td style="text-align:right; width:71px">\r\n			<p style="line-height:90%"><span style="font-size:14px">$DECIMO_TERCER_MES_SALARIO</span></p>\r\n			</td>\r\n			<td style="width:25px">\r\n			<p style="line-height:90%">&nbsp;</p>\r\n			</td>\r\n			<td style="text-align:right; width:93px">\r\n			<p style="line-height:90%">&nbsp;</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="width:164px">\r\n			<p style="line-height:90%"><span style="font-size:14px">D&eacute;cimo tercer Mes G. Rep.</span></p>\r\n			</td>\r\n			<td style="width:13px">\r\n			<p style="line-height:90%">&nbsp;</p>\r\n			</td>\r\n			<td style="text-align:right; width:71px">\r\n			<p style="line-height:90%"><span style="font-size:14px">$DECIMO_TERCER_MES_GASTOS</span></p>\r\n			</td>\r\n			<td style="width:25px">\r\n			<p style="line-height:90%">&nbsp;</p>\r\n			</td>\r\n			<td style="text-align:right; width:93px">\r\n			<p style="line-height:90%">&nbsp;</p>\r\n			</td>\r\n		</tr>\r\n		<tr>\r\n			<td style="width:164px">\r\n			<p style="line-height:90%"><span style="font-size:14px">Gasto de Representaci&oacute;n</span></p>\r\n			</td>\r\n			<td style="width:13px">\r\n			<p style="line-height:90%">&nbsp;</p>\r\n			</td>\r\n			<td style="text-align:right; width:71px">\r\n			<p style="line-height:90%"><span style="font-size:14px">$GASTOS_REPR_ANIO_FISCAL</span></p>\r\n			</td>\r\n			<td style="width:25px">\r\n			<p style="line-height:90%">&nbsp;</p>\r\n			</td>\r\n			<td style="text-align:right; width:93px">\r\n			<p style="line-height:90%"><span style="color:#000000"><span style="font-size:14px">$ISR_GASTOS_REP</span></span></p>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n\r\n<p style="line-height:130%"><span style="font-size:15px"><span style="font-family:times new roman,times,serif">La informaci&oacute;n contenida en esta certificaci&oacute;n es totalmente cierta y reposa en nuestros archivos la cual pongo a disposici&oacute;n de la Direcci&oacute;n General de Ingresos.</span></span></p>\r\n\r\n<p style="line-height:140%"><span style="font-size:15px"><span style="font-family:times new roman,times,serif">Expedida y formada hoy $DIA&nbsp;de $MES&nbsp;$ANIO</span></span></p>\r\n\r\n<p style="line-height:50%">&nbsp;</p>\r\n\r\n<p style="line-height:120%"><span style="font-size:15px">Gloria Vega<br />\r\nC&eacute;dula 4-142-1447<br />\r\nC. P. A.</span></p>\r\n', NULL, '', NULL, 'Template', 'certificacion_de_salarios_kt_2015.pdf', '$ANIO_FISCAL=2015;', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomtipos_empresa`
--

CREATE TABLE IF NOT EXISTS `nomtipos_empresa` (
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Volcado de datos para la tabla `nomtipos_empresa`
--

INSERT INTO `nomtipos_empresa` (`codigo`, `descripcion`) VALUES
(0, 'Empresa Privada\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0'),
(1, 'Empresa Publica\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0'),
(2, 'Empresa de Vigilancia\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomtipos_nomina`
--

CREATE TABLE IF NOT EXISTS `nomtipos_nomina` (
  `codtip` int(11) NOT NULL,
  `descrip` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `prioridad` tinyint(4) DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `fecha_ini` datetime DEFAULT NULL,
  `codnom` int(11) DEFAULT NULL,
  `diasbonvac` smallint(6) DEFAULT NULL,
  `diasutilidad` smallint(6) DEFAULT NULL,
  `diasdisfrute` smallint(6) DEFAULT NULL,
  `tipodisfrute` varchar(2) COLLATE utf8_spanish_ci DEFAULT NULL,
  `diasincrem` smallint(6) DEFAULT NULL,
  `diasmaxinc` smallint(6) DEFAULT NULL,
  `diasincremdis` smallint(6) DEFAULT NULL,
  `diasmaxincdis` smallint(6) DEFAULT NULL,
  `tiempoor` int(11) DEFAULT NULL,
  `diasantiguedad` int(11) DEFAULT NULL,
  `antigincremvac` int(2) DEFAULT NULL,
  `markar` tinyint(4) DEFAULT NULL,
  `usatablas` tinyint(1) DEFAULT NULL,
  `baremo01` smallint(6) DEFAULT NULL,
  `baremo02` smallint(6) DEFAULT NULL,
  `baremo03` smallint(6) DEFAULT NULL,
  `baremo04` smallint(6) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `ruta` varchar(119) COLLATE utf8_spanish_ci DEFAULT NULL,
  `basesuelsal` int(11) DEFAULT NULL,
  `sfecha_fin` datetime DEFAULT NULL,
  `sfecha_ini` datetime DEFAULT NULL,
  `sfecha` datetime DEFAULT NULL,
  `base30` tinyint(4) DEFAULT NULL,
  `detdes` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnomant` int(11) DEFAULT NULL,
  `fechabon` int(11) DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  `owner` varchar(254) COLLATE utf8_spanish_ci DEFAULT NULL,
  `bdgenerada` tinyint(4) DEFAULT NULL,
  `codgrupo` varchar(7) COLLATE utf8_spanish_ci DEFAULT NULL,
  `conceptosglopar` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipocamposadic` tinyint(4) DEFAULT NULL,
  `dfecha_ini` datetime DEFAULT NULL,
  `dfecha_fin` datetime DEFAULT NULL,
  `dfecha` datetime DEFAULT NULL,
  `dfechabon` datetime DEFAULT NULL,
  `desglose_moneda` text COLLATE utf8_spanish_ci,
  `tipo_ingreso` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codigo_banco` varchar(2) COLLATE utf8_spanish_ci DEFAULT NULL,
  `quinquenio` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`codtip`),
  KEY `fc_idx_321` (`descrip`),
  KEY `fc_idx_322` (`codgrupo`,`codtip`),
  KEY `fc_idx_323` (`bdgenerada`,`codtip`),
  KEY `fc_idx_324` (`prioridad`,`codtip`),
  KEY `fc_idx_325` (`fecha_fin`,`codtip`),
  KEY `fc_idx_326` (`fecha_ini`,`codtip`),
  KEY `fc_idx_327` (`codnom`,`codtip`),
  KEY `fc_idx_328` (`diasbonvac`,`codtip`),
  KEY `fc_idx_329` (`diasutilidad`,`codtip`),
  KEY `fc_idx_330` (`diasdisfrute`,`codtip`),
  KEY `fc_idx_331` (`tipodisfrute`,`codtip`),
  KEY `fc_idx_332` (`diasincrem`,`codtip`),
  KEY `fc_idx_333` (`diasmaxinc`,`codtip`),
  KEY `fc_idx_334` (`diasincremdis`,`codtip`),
  KEY `fc_idx_335` (`diasmaxincdis`,`codtip`),
  KEY `fc_idx_336` (`tiempoor`,`codtip`),
  KEY `fc_idx_337` (`diasantiguedad`,`codtip`),
  KEY `fc_idx_338` (`markar`,`codtip`),
  KEY `fc_idx_339` (`usatablas`,`codtip`),
  KEY `fc_idx_340` (`baremo01`,`codtip`),
  KEY `fc_idx_341` (`baremo02`,`codtip`),
  KEY `fc_idx_342` (`baremo03`,`codtip`),
  KEY `fc_idx_343` (`baremo04`,`codtip`),
  KEY `fc_idx_344` (`fecha`,`codtip`),
  KEY `fc_idx_345` (`ruta`,`codtip`),
  KEY `fc_idx_346` (`basesuelsal`,`codtip`),
  KEY `fc_idx_347` (`sfecha_fin`,`codtip`),
  KEY `fc_idx_348` (`sfecha_ini`,`codtip`),
  KEY `fc_idx_349` (`sfecha`,`codtip`),
  KEY `fc_idx_350` (`base30`,`codtip`),
  KEY `fc_idx_351` (`detdes`,`codtip`),
  KEY `fc_idx_352` (`codnomant`,`codtip`),
  KEY `fc_idx_353` (`fechabon`,`codtip`),
  KEY `fc_idx_354` (`ee`,`codtip`),
  KEY `fc_idx_355` (`owner`,`codtip`),
  KEY `fc_idx_356` (`conceptosglopar`,`codtip`),
  KEY `fc_idx_357` (`tipocamposadic`,`codtip`),
  KEY `fc_idx_358` (`dfecha_ini`,`codtip`),
  KEY `fc_idx_359` (`dfecha_fin`,`codtip`),
  KEY `fc_idx_360` (`dfecha`,`codtip`),
  KEY `fc_idx_361` (`dfechabon`,`codtip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomturnos`
--

CREATE TABLE IF NOT EXISTS `nomturnos` (
  `turno_id` bigint(32) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(80) NOT NULL,
  `entrada` time NOT NULL DEFAULT '00:00:00',
  `tolerancia_entrada` time NOT NULL DEFAULT '00:00:00',
  `inicio_descanso` time NOT NULL DEFAULT '00:00:00',
  `salida_descanso` time NOT NULL DEFAULT '00:00:00',
  `tolerancia_descanso` time NOT NULL DEFAULT '00:00:00',
  `salida` time NOT NULL DEFAULT '00:00:00',
  `tolerancia_salida` time NOT NULL DEFAULT '00:00:00',
  `tolerancia_llegada` time NOT NULL,
  `libre` int(1) NOT NULL,
  `nocturno` int(1) NOT NULL,
  `tipo` int(11) NOT NULL,
  `descpago` int(1) NOT NULL,
  PRIMARY KEY (`turno_id`),
  KEY `fk_nomturnos_nomturnos_tipo` (`tipo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `nomturnos`
--

INSERT INTO `nomturnos` (`turno_id`, `descripcion`, `entrada`, `tolerancia_entrada`, `inicio_descanso`, `salida_descanso`, `tolerancia_descanso`, `salida`, `tolerancia_salida`, `tolerancia_llegada`, `libre`, `nocturno`, `tipo`, `descpago`) VALUES
(1, 'Oficina', '08:00:00', '08:05:00', '12:00:00', '13:00:00', '13:00:00', '17:00:00', '17:15:00', '07:30:00', 1, 0, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomturnos_horarios`
--

CREATE TABLE IF NOT EXISTS `nomturnos_horarios` (
  `turnohorario_id` int(11) NOT NULL AUTO_INCREMENT,
  `turno_id` bigint(32) NOT NULL,
  `dia1` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Lunes',
  `dia2` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Martes',
  `dia3` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Miercoles',
  `dia4` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Jueves',
  `dia5` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Viernes',
  `dia6` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Sabado',
  `dia7` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Domingo',
  `hora_desde` time DEFAULT NULL,
  `hora_hasta` time DEFAULT NULL,
  `dialibre` smallint(6) NOT NULL DEFAULT '0' COMMENT '0=Falso 1=Verdadero',
  `entrada_desde` time DEFAULT NULL,
  `entrada_hasta` time DEFAULT NULL,
  `salida_desde` time DEFAULT NULL,
  `salida_hasta` time DEFAULT NULL,
  `paga_desde` time DEFAULT NULL,
  `paga_hasta` time DEFAULT NULL,
  `tolerancia_entrada` int(11) DEFAULT NULL,
  `tolerancia_salida` int(11) DEFAULT NULL,
  PRIMARY KEY (`turnohorario_id`),
  KEY `fk_nomturnos_horarios_nomturnos_idx` (`turno_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomturnos_rotacion`
--

CREATE TABLE IF NOT EXISTS `nomturnos_rotacion` (
  `codigo` int(11) NOT NULL,
  `descripcion` varchar(60) NOT NULL,
  `frecuencia` enum('Semanal','Diaria','Mensual') NOT NULL,
  `inicio` int(11) NOT NULL COMMENT '1=Lun, 2=Mar, 3=Mie, 4=Jue, 5=Vie, 6=Sab, 7=Dom',
  `turnotipo_id` int(11) NOT NULL,
  PRIMARY KEY (`codigo`),
  KEY `fk_nomturnos_rotacion_nomturnos_tipo_idx` (`turnotipo_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomturnos_rotacion_detalle`
--

CREATE TABLE IF NOT EXISTS `nomturnos_rotacion_detalle` (
  `codigo_rotacion` int(11) NOT NULL,
  `turno_actual` bigint(32) NOT NULL,
  `turno_sucesor` bigint(32) NOT NULL,
  KEY `fk_nomturnos_sucesor_nomturnos1_idx` (`turno_actual`) USING BTREE,
  KEY `fk_nomturnos_sucesor_nomturnos2_idx` (`turno_sucesor`) USING BTREE,
  KEY `fk_nomturnos_rotacion_detalle_nomturnos_rotacion_idx` (`codigo_rotacion`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomturnos_tipo`
--

CREATE TABLE IF NOT EXISTS `nomturnos_tipo` (
  `turnotipo_id` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) NOT NULL,
  `rotativo` tinyint(4) NOT NULL,
  PRIMARY KEY (`turnotipo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=7 ;

--
-- Volcado de datos para la tabla `nomturnos_tipo`
--

INSERT INTO `nomturnos_tipo` (`turnotipo_id`, `descripcion`, `rotativo`) VALUES
(1, 'Diurno', 0),
(2, 'Norturno', 0),
(3, 'Mixto-Diurno-Nocturno', 0),
(4, 'Mixto-Nocturno-Diurno', 0),
(5, 'Diurno-Corrido', 0),
(6, 'Libre', 0);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `nomvis_conceptos_acumulado`
--
CREATE TABLE IF NOT EXISTS `nomvis_conceptos_acumulado` (
`codcon` int(11)
,`cod_tac` varchar(6)
,`descrip` varchar(60)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `nomvis_conceptos_frecuencia`
--
CREATE TABLE IF NOT EXISTS `nomvis_conceptos_frecuencia` (
`codcon` int(11)
,`codfre` int(11)
,`descrip` varchar(60)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `nomvis_conceptos_situacion`
--
CREATE TABLE IF NOT EXISTS `nomvis_conceptos_situacion` (
`codcon` int(11)
,`descrip` varchar(30)
,`situacion` varchar(30)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `nomvis_conceptos_tiposnomina`
--
CREATE TABLE IF NOT EXISTS `nomvis_conceptos_tiposnomina` (
`codcon` int(11)
,`codtip` int(11)
,`descrip` varchar(60)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `nomvis_integrantes`
--
CREATE TABLE IF NOT EXISTS `nomvis_integrantes` (
`personal_id` int(11)
,`nomposicion_id` int(11)
,`cedula` varchar(20)
,`ficha` int(10)
,`apellidos` varchar(30)
,`nombres` varchar(30)
,`estado` varchar(30)
,`descrip` varchar(60)
,`foto` varchar(80)
,`sueldo` decimal(20,2)
,`apenom` varchar(60)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `nomvis_per_movimiento`
--
CREATE TABLE IF NOT EXISTS `nomvis_per_movimiento` (
`codnom` int(11)
,`tipnom` int(11)
,`foto` varchar(80)
,`fec_ing` date
,`cedula` varchar(20)
,`ficha` int(10)
,`apenom` varchar(60)
,`sueldopro` decimal(20,2)
,`codnivel1` varchar(8)
,`codnivel2` varchar(8)
,`codnivel3` varchar(8)
,`cargo` varchar(100)
);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nom_dato_liquidacion`
--

CREATE TABLE IF NOT EXISTS `nom_dato_liquidacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ficha` int(6) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `tiponom` int(2) NOT NULL,
  `codnom` int(4) NOT NULL,
  `motivo_retiro_id` int(2) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `fecha_egreso` date NOT NULL,
  `tiempo_servicio` varchar(8) NOT NULL,
  `observacion` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nom_eval_desemp`
--

CREATE TABLE IF NOT EXISTS `nom_eval_desemp` (
  `id_eval_desemp` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `evaluador` varchar(255) NOT NULL,
  `evaluado` varchar(255) NOT NULL,
  `fecha_desemp` date NOT NULL,
  `pregunta112` int(11) NOT NULL,
  `pregunta122` int(11) NOT NULL,
  `pregunta212` int(11) NOT NULL,
  `pregunta222` int(11) NOT NULL,
  `pregunta232` int(11) NOT NULL,
  `pregunta242` int(11) NOT NULL,
  `pregunta252` int(11) NOT NULL,
  `pregunta312` int(11) NOT NULL,
  `pregunta322` int(11) NOT NULL,
  `pregunta412` int(11) NOT NULL,
  `pregunta422` int(11) NOT NULL,
  `pregunta432` int(11) NOT NULL,
  `pregunta512` int(11) NOT NULL,
  `pregunta522` int(11) NOT NULL,
  `pregunta532` int(11) NOT NULL,
  `pregunta542` int(11) NOT NULL,
  `pregunta612` int(11) NOT NULL,
  `pregunta622` int(11) NOT NULL,
  `pregunta632` int(11) NOT NULL,
  `pregunta642` int(11) NOT NULL,
  `pregunta712` int(11) NOT NULL,
  `pregunta722` int(11) NOT NULL,
  `pregunta732` int(11) NOT NULL,
  `pregunta742` int(11) NOT NULL,
  `pregunta113` int(11) NOT NULL,
  `opinion1` varchar(511) NOT NULL,
  `opinion2` varchar(511) NOT NULL,
  `opinion3` varchar(511) NOT NULL,
  `comentario1` varchar(511) NOT NULL,
  `estado` varchar(255) NOT NULL,
  UNIQUE KEY `id_eval_desemp` (`id_eval_desemp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nom_eval_personal`
--

CREATE TABLE IF NOT EXISTS `nom_eval_personal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_personal` int(11) NOT NULL,
  `fecha_personal` date NOT NULL,
  `Estado` varchar(31) NOT NULL,
  `pregunta111` int(11) NOT NULL,
  `pregunta121` int(11) NOT NULL,
  `frecuencia` int(11) NOT NULL,
  `pregunta112` int(11) NOT NULL,
  `pregunta122` int(11) NOT NULL,
  `pregunta132` int(11) NOT NULL,
  `pregunta142` int(11) NOT NULL,
  `pregunta212` int(11) NOT NULL,
  `pregunta222` int(11) NOT NULL,
  `pregunta232` int(11) NOT NULL,
  `pregunta242` int(11) NOT NULL,
  `pregunta252` int(11) NOT NULL,
  `pregunta262` int(11) NOT NULL,
  `pregunta312` int(11) NOT NULL,
  `pregunta322` int(11) NOT NULL,
  `comentario1` varchar(255) NOT NULL,
  `pregunta113` int(11) NOT NULL,
  `pregunta123` int(11) NOT NULL,
  `pregunta133` int(11) NOT NULL,
  `pregunta143` int(11) NOT NULL,
  `pregunta153` int(11) NOT NULL,
  `pregunta213` int(11) NOT NULL,
  `pregunta223` int(11) NOT NULL,
  `pregunta233` int(11) NOT NULL,
  `comentario2` varchar(255) NOT NULL,
  `comentario3` varchar(255) NOT NULL,
  `pregunta114` int(11) NOT NULL,
  `pregunta124` int(11) NOT NULL,
  `pregunta134` int(11) NOT NULL,
  `pregunta144` int(11) NOT NULL,
  `pregunta154` int(11) NOT NULL,
  `pregunta164` int(11) NOT NULL,
  `pregunta174` int(11) NOT NULL,
  `comentario4` varchar(255) NOT NULL,
  `pregunta115` int(11) NOT NULL,
  `pregunta125` int(11) NOT NULL,
  `pregunta135` int(11) NOT NULL,
  `pregunta145` int(11) NOT NULL,
  `pregunta155` int(11) NOT NULL,
  `pregunta165` int(11) NOT NULL,
  `pregunta175` int(11) NOT NULL,
  `comentario5` varchar(255) NOT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nom_modulos`
--

CREATE TABLE IF NOT EXISTS `nom_modulos` (
  `cod_modulo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cod_modulo_padre` int(11) DEFAULT NULL,
  `nom_menu` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `archivo` varchar(100) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  `tabla` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `alias_opcion` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `img` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `alt` varchar(250) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `glyphicon` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`cod_modulo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=335 ;

--
-- Volcado de datos para la tabla `nom_modulos`
--

INSERT INTO `nom_modulos` (`cod_modulo`, `cod_modulo_padre`, `nom_menu`, `archivo`, `orden`, `tabla`, `alias_opcion`, `activo`, `img`, `alt`, `glyphicon`) VALUES
(1, NULL, 'Procesos', 'menu_procesos.php', 6, NULL, 'acce_procesos', 1, '', '', 'glyphicon-cog'),
(3, NULL, 'Seleccionar Planilla', 'seleccionar_nomina2.php', 10, NULL, '', 1, '', '', 'glyphicon-hand-right'),
(4, NULL, 'Reportes', 'menu_reportes.php', 7, NULL, 'acce_reportes', 1, '', '', 'glyphicon-stats'),
(7, NULL, 'Transacciones', 'menu_transacciones.php', 5, NULL, 'acce_transacciones', 1, '', '', 'glyphicon glyphicon-sort'),
(9, NULL, 'Colaboradores', 'menu_personal.php', 2, NULL, 'acce_personal', 1, '', '', 'glyphicon-user'),
(10, NULL, 'Configuraci&oacute;n', 'menu_configuracion.php', 0, NULL, 'acce_configuracion', 1, '', '', 'glyphicon-wrench'),
(11, 10, 'Datos de la empresa', 'empresa.php', 1, '', '', 1, '', '', NULL),
(21, 10, 'Usuarios', 'usuarios_list.php', 11, 'usuarios', '', 1, '', '', NULL),
(32, 2, 'Reportes', NULL, NULL, NULL, '', 1, '', '', NULL),
(45, 4, 'Planilla', 'submenu_reportes_nomina.php', 2, '', '', 1, '', '', NULL),
(60, NULL, 'Selección y Empleo', 'menu_elegibles.php', 33, NULL, 'acce_elegibles', 1, '', '', 'glyphicon-user'),
(61, NULL, 'Gestión de Personal', 'menu_consultas.php', 34, NULL, 'acce_consultas', 1, '', '', 'glyphicon-search'),
(65, NULL, 'Acreedores Prestamos', '../prestamos/menu_prestamos.php', 3, NULL, 'acce_prestamos', 1, '', '', 'glyphicon-usd'),
(66, 10, 'Niveles Funcionales', 'niveles_funcionales.php', 2, '', '', 1, '', '', NULL),
(67, 66, 'Listar SubNiveles', 'subniveles.php', NULL, '', '', 1, '', '', NULL),
(68, 67, 'Agregar', 'ag_subniveles.php', NULL, '', '', 1, '', '', NULL),
(69, 67, 'Editar', 'ag_subniveles.php', NULL, '', '', 1, '', '', NULL),
(70, 67, 'Eliminar', 'subniveles.php', NULL, '', '', 1, '', '', NULL),
(71, 10, 'Tipos', 'submenu_tipos.php', 3, '', '', 1, '', '', NULL),
(72, 71, 'Tipos de Nomina', 'tipos_nominas.php', 1, '', '', 1, '', '', NULL),
(73, 71, 'Tipos de Frecuencias de Pago', 'frecuencias.php', 2, '', '', 1, '', '', NULL),
(74, 71, 'Tipos de Acumulados', 'acumulados.php', 3, '', '', 1, '', '', NULL),
(75, 71, 'Tipos de Parentescos', 'parentescos.php', 4, '', '', 1, '', '', NULL),
(76, 71, 'Tipos de Suspensiones y Permisos', 'suspenciones.php', 5, '', '', 1, '', '', NULL),
(77, 71, 'Tipos de Aumentos', 'aumentos.php', 6, '', '', 1, '', '', NULL),
(78, 71, 'Tipos de Prestamos', 'prestamos.php', 7, '', '', 1, '', '', NULL),
(79, 71, 'Situaciones', 'situaciones.php', 8, '', '', 1, '', '', NULL),
(80, 72, 'Agregar', 'ag_tiposnomina.php', NULL, '', '', 1, '', '', NULL),
(81, 72, 'Editar', 'ag_tiposnomina.php', NULL, '', '', 1, '', '', NULL),
(82, 72, 'Eliminar', 'tipos_nominas.php', NULL, '', '', 1, '', '', NULL),
(83, 73, 'Agregar', 'ag_frecuencias.php', NULL, '', '', 1, '', '', NULL),
(84, 73, 'Editar', 'ag_frecuencias.php', NULL, '', '', 1, '', '', NULL),
(85, 73, 'Eliminar', 'frecuencias.php', NULL, '', '', 1, '', '', NULL),
(86, 74, 'Agregar', 'ag_acumulados.php', NULL, '', '', 1, '', '', NULL),
(87, 74, 'Editar', 'ag_acumulados.php', NULL, '', '', 1, '', '', NULL),
(88, 74, 'Eliminar', 'acumulados.php', NULL, '', '', 1, '', '', NULL),
(89, 75, 'Agregar', 'ag_parentescos.php', NULL, '', '', 1, '', '', NULL),
(90, 75, 'Editar', 'ag_parentescos.php', NULL, '', '', 1, '', '', NULL),
(91, 75, 'Eliminar', 'parentescos.php', NULL, '', '', 1, '', '', NULL),
(92, 76, 'Agregar', 'ag_suspenciones.php', NULL, '', '', 1, '', '', NULL),
(93, 76, 'Editar', 'ag_suspenciones.php', NULL, '', '', 1, '', '', NULL),
(94, 76, 'Eliminar', 'suspenciones.php', NULL, '', '', 1, '', '', NULL),
(95, 77, 'Agregar', 'ag_aumentos.php', NULL, '', '', 1, '', '', NULL),
(96, 77, 'Editar', 'ag_aumentos.php ', NULL, '', '', 1, '', '', NULL),
(97, 77, 'Eliminar', 'aumentos.php', NULL, '', '', 1, '', '', NULL),
(98, 78, 'Agregar', 'ag_prestamos.php', NULL, '', '', 1, '', '', NULL),
(99, 78, 'Editar', 'ag_prestamos.php', NULL, '', '', 1, '', '', NULL),
(100, 78, 'Eliminar', 'prestamos.php', NULL, '', '', 1, '', '', NULL),
(101, 79, 'Agregar', 'ag_situaciones.php', NULL, '', '', 1, '', '', NULL),
(102, 79, 'Editar', 'ag_situaciones.php', NULL, '', '', 1, '', '', NULL),
(103, 79, 'Eliminar', 'situaciones.php', NULL, '', '', 1, '', '', NULL),
(104, 10, 'Calendario', 'submenu_calendarios.php', 4, '', '', 1, '', '', NULL),
(106, 104, 'Generar Calendario', 'generar_calendarios.php', NULL, '', '', 1, '', '', NULL),
(107, 104, 'Seleccion de Año Calendario', 'consultar_calendarios.php', NULL, '', '', 1, '', '', NULL),
(108, 104, 'Ver y modificar calendario', 'calendarios.php', NULL, '', '', 1, '', '', NULL),
(109, 104, 'Grabar Fecha Calendario', 'modificar_calendarios.php', NULL, '', '', 1, '', '', NULL),
(110, 10, 'Bancos', 'submenu_bancos.php', 5, '', '', 1, '', '', NULL),
(111, 110, 'Grupo de Bancos ', 'grupos_bancos.php', NULL, '', '', 1, '', '', NULL),
(112, 110, 'Bancos', 'bancos.php', NULL, '', '', 1, '', '', NULL),
(113, 110, 'Tasas de Interes', 'tasas_interes.php', NULL, '', '', 1, '', '', NULL),
(114, 111, 'Agregar', 'ag_grupos_bancos.php', NULL, '', '', 1, '', '', NULL),
(115, 111, 'Editar', 'ag_grupos_bancos.php', NULL, '', '', 1, '', '', NULL),
(116, 111, 'Eliminar', 'grupos_bancos.php', NULL, '', '', 1, '', '', NULL),
(117, 112, 'Agregar', 'ag_bancos.php', NULL, '', '', 1, '', '', NULL),
(118, 112, 'Editar', 'ag_bancos.php', NULL, '', '', 1, '', '', NULL),
(120, 112, 'Eliminar', 'bancos.php', NULL, '', '', 1, '', '', NULL),
(121, 113, 'Agregar', 'ag_tasas_interes.php', NULL, '', '', 1, '', '', NULL),
(122, 113, 'Editar', 'ag_tasas_interes.php', NULL, '', '', 1, '', '', NULL),
(123, 113, 'Eliminar', 'tasas_interes.php', NULL, '', '', 1, '', '', NULL),
(124, 10, 'Profesiones u Ocupaciones ', 'maestro_profesion.php', 6, '', '', 1, '', '', NULL),
(125, 124, 'Agregar', 'ag_profesiones.php', NULL, '', '', 1, '', '', NULL),
(126, 124, 'Editar', 'ag_profesiones.php', NULL, '', '', 1, '', '', NULL),
(127, 124, 'Eliminar', 'profesiones.php', NULL, '', '', 1, '', '', NULL),
(128, 10, 'Cargos', 'cargos.php', 7, '', '', 1, '', '', NULL),
(129, 128, 'Agregar', 'ag_cargos.php', NULL, '', '', 1, '', '', NULL),
(130, 128, 'Editar', 'ag_cargos.php', NULL, '', '', 1, '', '', NULL),
(131, 128, 'Eliminar', 'cargos.php', NULL, '', '', 1, '', '', NULL),
(132, 10, 'Grado de instrucción', 'instruccion.php', 12, '', '', 1, '', '', NULL),
(133, 132, 'Agregar', 'ag_instruccion.php', NULL, '', '', 1, '', '', NULL),
(134, 132, 'Editar', 'ag_instruccion.php', NULL, '', '', 1, '', '', NULL),
(135, 132, 'Eliminar', 'instruccion.php', NULL, '', '', 1, '', '', NULL),
(136, 10, 'Categorías', 'categorias.php', 9, '', '', 1, '', '', NULL),
(137, 136, 'Agregar', 'ag_categorias.php', NULL, '', '', 1, '', '', NULL),
(138, 136, 'Editar', 'ag_categorias.php', NULL, '', '', 1, '', '', NULL),
(139, 136, 'Eliminar', 'categorias.php', NULL, '', '', 1, '', '', NULL),
(140, 10, 'Formulación de Conceptos', 'submenu_formulacion_conceptos.php', 10, '', '', 1, '', '', NULL),
(141, 140, 'Conceptos de Nomina de Pago', 'conceptos_nomina_pago.php', NULL, '', '', 1, '', '', NULL),
(142, 140, 'Campos Adicionales (Trabajador)', 'constantes_personal.php', NULL, '', '', 1, '', '', NULL),
(143, 140, 'Baremos', 'baremos.php', NULL, '', '', 1, '', '', NULL),
(144, 141, 'Agregar', 'ag_conceptos_nomina_pago.php', NULL, '', '', 1, '', '', NULL),
(145, 141, 'Editar', 'ag_conceptos_nomina_pago.php', NULL, '', '', 1, '', '', NULL),
(146, 141, 'Campos del Personal y Nómina', 'campos_personal.php', NULL, '', '', 1, '', '', NULL),
(147, 141, 'Funciones de la Aplicación', 'funciones_aplicacion.php', NULL, '', '', 1, '', '', NULL),
(148, 141, 'Eliminar', 'conceptos_nomina_pago.php', NULL, '', '', 1, '', '', NULL),
(149, 141, 'Copiar Concepto', 'copiar_concepto.php', NULL, '', '', 1, '', '', NULL),
(150, 142, 'Agregar', 'ag_constantes_personal.php', NULL, '', '', 1, '', '', NULL),
(151, 142, 'Editar', 'ag_constantes_personal.php', NULL, '', '', 1, '', '', NULL),
(152, 142, 'Eliminar', 'constantes_personal.php', NULL, '', '', 1, '', '', NULL),
(153, 142, 'Restablece al valor predeterminado', 'constantes_personal.php', NULL, '', '', 1, '', '', NULL),
(154, 142, 'Agregar Campo a Ficha de nom actual', 'constantes_personal.php', NULL, '', '', 1, '', '', NULL),
(155, 143, 'Agregar', 'baremos_agregar.php', NULL, '', '', 1, '', '', NULL),
(156, 143, 'Editar', 'baremos_editar.php', NULL, '', '', 1, '', '', NULL),
(157, 143, 'Eliminar', 'baremos_eliminar.php', NULL, '', '', 1, '', '', NULL),
(158, 143, 'Detalle del Baremo', 'baremos_detalles.php', NULL, '', '', 1, '', '', NULL),
(159, 158, 'Agregar', 'mantenimiento_dinamico.php', NULL, '', '', 1, '', '', NULL),
(160, 158, 'Editar', 'mantenimiento_dinamico.php', NULL, '', '', 1, '', '', NULL),
(161, 158, 'Eliminar', 'mantenimiento_dinamico.php', NULL, '', '', 1, '', '', NULL),
(162, 21, 'Agregar', 'usuarios_add.php', NULL, '', '', 1, '', '', NULL),
(163, 21, 'Editar', 'usuarios_edit.php', NULL, '', '', 1, '', '', NULL),
(164, 21, 'Eliminar', 'usuarios_delete.php', NULL, '', '', 1, '', '', NULL),
(165, 10, 'Area de desempeño ', 'desempeno.php', 13, '', '', 1, '', '', NULL),
(166, 165, 'Agregar', 'ag_desempeno.php', NULL, '', '', 1, '', '', NULL),
(167, 165, 'Editar', 'ag_desempeno.php', NULL, '', '', 1, '', '', NULL),
(168, 165, 'Eliminar', 'desempeno.php', NULL, '', '', 1, '', '', NULL),
(169, 10, 'Turnos', 'turnos.php', 16, '', '', 1, '', '', NULL),
(170, 169, 'Agregar', 'ag_turnos.php', NULL, '', '', 1, '', '', NULL),
(171, 169, 'Editar', 'ag_turnos.php', NULL, '', '', 1, '', '', NULL),
(172, 169, 'Eliminar', 'turnos.php', NULL, '', '', 1, '', '', NULL),
(173, 175, 'Agregar', 'ing_curriculum2.php', NULL, '', '', 1, '', '', NULL),
(174, 175, 'Editar', 'curriculum_edit.php', NULL, '', '', 1, '', '', NULL),
(175, 60, 'Elegibles', 'elegibles_list.php', NULL, '', '', 1, '', '', NULL),
(176, 175, 'Eliminar', 'elegibles_list.php', NULL, '', '', 1, '', '', NULL),
(177, 9, 'Datos de Integrantes', 'maestro_personal.php', 1, '', '', 1, '', '', NULL),
(178, 177, 'Agregar', 'ag_maestro_integrantes.php', NULL, '', '', 1, '', '', NULL),
(179, 177, 'Editar', 'ed_maestro_integrantes.php', NULL, '', '', 1, '', '', NULL),
(180, 177, 'Mostrar Foto', 'mostrar_foto_empleado.php', NULL, '', '', 1, '', '', NULL),
(181, 177, 'Eliminar', 'maestro_personal.php', NULL, '', '', 1, '', '', NULL),
(182, 177, 'Carga Familiar', 'familiares.php', NULL, '', '', 1, '', '', NULL),
(183, 182, 'Agregar', 'ag_familiares.php', NULL, '', '', 1, '', '', NULL),
(184, 182, 'Editar', 'ag_familiares.php', NULL, '', '', 1, '', '', NULL),
(185, 182, 'Eliminar', 'familiares.php', NULL, '', '', 1, '', '', NULL),
(186, 177, 'Campos adicionales', 'otrosdatos_integrantes.php', NULL, '', '', 1, '', '', NULL),
(187, 177, 'Imprimir', 'datos_personal.php', NULL, '', '', 1, '', '', NULL),
(188, 177, 'Expediente', 'expediente_list.php', NULL, '', '', 1, '', '', NULL),
(192, 9, 'Acumulados por concepto', 'acumulados_concepto_sel.php', 3, '', '', 1, '', '', NULL),
(193, 9, 'Acumulados por tipos de acumulados', 'acumulados_tipo_sel.php', 4, '', '', 1, '', '', NULL),
(194, 9, 'Aumentos', 'aumento_list.php', 5, '', '', 1, '', '', NULL),
(195, 194, 'Crear Aumento', 'aumento_agregar.php', NULL, '', '', 1, '', '', NULL),
(196, 65, 'Lista Prestamos ', '../prestamos/prestamos_list.php', 2, '', '', 1, '', '', NULL),
(197, 196, 'Agregar', 'prestamos_agregar.php', NULL, '', '', 1, '', '', NULL),
(198, 196, 'Editar', 'prestamos_edit.php', NULL, '', '', 1, '', '', NULL),
(199, 196, 'Eliminar', 'prestamos_list.php', NULL, '', '', 1, '', '', NULL),
(200, 61, 'Gestión de Personal', 'cedula.php', NULL, '', '', 1, '', '', NULL),
(201, 60, 'Ingresa tu Curriculum', 'ing_curriculum.php', NULL, '', '', 1, '', '', NULL),
(202, 61, 'Datos de Integrantes', 'maestro_personal.php', NULL, '', '', 1, '', '', NULL),
(203, 7, 'Planilla de Pago', 'nomina_de_pago.php', 1, '', '', 1, '', '', NULL),
(204, 203, 'Agregar', 'ag_nomina_pago.php', NULL, '', '', 1, '', '', NULL),
(205, 203, 'Generar Nómina', 'barraprogreso_1.php', NULL, '', '', 1, '', '', NULL),
(206, 203, 'Agregar Variantes', 'movimientos_agregar_masivo.php', NULL, '', '', 1, '', '', NULL),
(207, 203, 'Agregar Variantes', 'movimientos_agregar_masivo_nom.php', NULL, '', '', 1, '', '', NULL),
(208, 203, 'Modificar Planilla de Pago ', 'ag_nomina_pago.php', NULL, '', '', 1, '', '', NULL),
(209, 203, 'Eliminar', 'nomina_de_pago.php', NULL, '', '', 1, '', '', NULL),
(210, 203, 'Cerrar Nómina', 'nomina_de_pago.php', NULL, '', '', 1, '', '', NULL),
(211, 7, 'Prestaciones', 'nomina_de_prestaciones.php', 2, '', '', 1, '', '', NULL),
(212, 211, 'Agregar', 'ag_nomina_prestaciones.php', NULL, '', '', 1, '', '', NULL),
(213, 211, 'Generar Planilla', 'barraprogreso_1.php', NULL, '', '', 1, '', '', NULL),
(214, 211, 'Modificar', 'ag_nomina_pago.php', NULL, '', '', 1, '', '', NULL),
(215, 211, 'Eliminar', 'nomina_de_prestaciones.php', NULL, '', '', 1, '', '', NULL),
(216, 211, 'Cerrar Planilla', 'nomina_de_prestaciones.php', NULL, '', '', 1, '', '', NULL),
(217, 7, 'Vacaciones', 'submenu_vacaciones.php', 3, '', '', 1, '', '', NULL),
(218, 217, 'Generar vacaciones', 'vacaciones_generar.php', NULL, '', '', 1, '', '', NULL),
(219, 217, 'Generar vacaciones por trabajador', 'vacaciones_generar.php', NULL, '', '', 1, '', '', NULL),
(220, 217, 'Mantenimiento de vacaciones', 'vacaciones_mantenimiento.php', NULL, '', '', 1, '', '', NULL),
(221, 217, 'Nómina de vacaciones', 'nomina_de_vacaciones.php', NULL, '', '', 1, '', '', NULL),
(222, 7, 'Liquidaciones', 'nomina_de_liquidaciones.php', 4, '', '', 1, '', '', NULL),
(223, 222, 'Agregar', 'ag_nomina_liquidaciones.php', NULL, '', '', 1, '', '', NULL),
(224, 222, 'Editar', 'ag_nomina_liquidaciones.php', NULL, '', '', 1, '', '', NULL),
(225, 222, 'Eliminar', 'nomina_de_liquidaciones.php', NULL, '', '', 1, '', '', NULL),
(226, 222, 'Cerrar Nómina', 'nomina_de_liquidaciones.php', NULL, '', '', 1, '', '', NULL),
(227, 222, 'Movimientos', 'buscar_empleado2.php', NULL, '', '', 1, '', '', NULL),
(228, 227, 'Movimiento Planilla', 'movimientos_nomina_liquidaciones.php', NULL, '', '', 1, '', '', NULL),
(229, 3, 'Seleccionar Nómina', 'seleccionar_nomina2.php', NULL, '', '', 1, '', '', NULL),
(230, 45, 'Reporte de Planilla', 'config_rpt_nomina.php', NULL, '', '', 1, '', '', NULL),
(231, 45, 'Resumen de Planilla', 'config_rpt_nomina.php', NULL, '', '', 1, '', '', NULL),
(232, 45, 'Recibos de Pago', 'config_rpt_nomina.php', NULL, '', '', 1, '', '', NULL),
(233, 45, 'Análisis Por Conceptos', 'config_rpt_nomina.php', NULL, '', '', 1, '', '', NULL),
(234, 45, 'Relacion de conformidad', 'config_rpt_nomina.php', NULL, '', '', 1, '', '', NULL),
(235, 45, 'Reporte consolidado de nominas', 'filtro_nomina2.php', NULL, '', '', 1, '', '', NULL),
(236, 188, 'Agregar registro Estudios academicos', 'expediente_agregar.php', NULL, NULL, 'acce_estuaca', 1, '', '', NULL),
(237, 188, 'Agregar registro Estudios extra academicos', 'expediente_agregar.php ', NULL, NULL, 'acce_xestuaca', 1, '', '', NULL),
(238, 188, 'Agregar registro Permisos', 'expediente_agregar.php', NULL, NULL, 'acce_permisos', 1, '', '', NULL),
(239, 188, 'Agregar registro Logros', 'expediente_agregar.php', NULL, NULL, 'acce_logros', 1, '', '', NULL),
(240, 188, 'Agregar registro Penalizaciones', 'expediente_agregar.php', NULL, NULL, 'acce_penalizacion', 1, '', '', NULL),
(241, 188, 'Agregar registro Movimientos de personal', NULL, NULL, 'expediente_agregar.php', 'acce_movpe', 1, '', '', NULL),
(242, 188, 'Agregar registro Evaluacion de desempeño', 'expediente_agregar.php', NULL, NULL, 'acce_evalde', 1, '', '', NULL),
(243, 188, 'Agregar registro Experiencia', 'expediente_agregar.php', NULL, NULL, 'acce_experiencia', 1, '', '', NULL),
(244, 188, 'Agregar registro Anticipo de prest. sociales', 'expediente_agregar.php', NULL, NULL, 'acce_antic', 1, '', '', NULL),
(245, 188, 'Agregar registro Entrega de uniforme', 'expediente_agregar.php', NULL, NULL, 'acce_uniforme', 1, '', '', NULL),
(246, 230, 'Reporte', 'rpt_reporte_de_nomina.php', NULL, '', '', 1, '', '', NULL),
(247, 231, 'Reporte', 'resumen_conceptospdf.php', NULL, '', '', 1, '', '', NULL),
(248, 232, 'Reporte', 'recibos_por_lote_csbpdf.php', NULL, '', '', 1, '', '', NULL),
(249, 233, 'Reporte', 'reporte_analisis_concepto.php', NULL, '', '', 1, '', '', NULL),
(250, 234, 'Reporte', 'relacion_conformidad_csbpdf.php', NULL, '', '', 1, '', '', NULL),
(251, 235, 'Reporte', 'reporte_consolidado_nomina.php', NULL, '', '', 1, '', '', NULL),
(252, 192, 'Buscar por ficha', 'buscar_empleado_acumulados.php', NULL, '', '', 1, '', '', NULL),
(253, 192, 'Buscar por concepto', 'buscar_concepto_acumulados.php', NULL, '', '', 1, '', '', NULL),
(254, 192, 'Mostrar acumulos', 'mostrar_acumulados.php', NULL, '', '', 1, '', '', NULL),
(255, 197, 'Buscar por ficha', 'buscar_empleado.php', NULL, '', '', 1, '', '', NULL),
(256, 197, 'Seleccionar prestamo', 'buscar_tipo_prestamo.php', NULL, '', '', 1, '', '', NULL),
(257, 197, 'Generador de calculo', 'prestamos_calc_cuotas.php', NULL, '', '', 1, '', '', NULL),
(258, 200, 'Consulta de Datos Personales', 'datos_personales.php', NULL, '', '', 1, '', '', NULL),
(259, 200, 'Constancia de trabajo', 'rpt_constancia_personal2.php', NULL, '', '', 1, '', '', NULL),
(260, 200, 'Configurador Recibos de pago', 'config_rpt_nomina2.php', NULL, '', '', 1, '', '', NULL),
(261, 203, 'Movimientos', 'movimientos_nomina_pago.php', NULL, '', '', 1, '', '', NULL),
(262, 261, 'Buscar nomina', 'buscar_nomina_pago.php', NULL, '', '', 1, '', '', NULL),
(263, 261, 'Imprimir Recibo', 'rpt_recibo_pago.php', NULL, '', '', 1, '', '', NULL),
(264, 261, 'Agregar Concepto', 'movimientos_nomina_pago_agregar.php', NULL, '', '', 1, '', '', NULL),
(265, 261, 'Generar Movimiento individual', 'movimientos_nomina_persona_generar.php', NULL, '', '', 1, '', '', NULL),
(266, 261, 'Eliminar conceptos del empleado', 'movimientos_nomina_pago_eliminar.php', NULL, '', '', 1, '', '', NULL),
(267, 220, 'Editar', 'vacaciones_mantenimiento_editar.php', NULL, '', '', 1, '', '', NULL),
(268, 220, 'Imprimir', 'vacaciones_persona.php', NULL, '', '', 1, '', '', NULL),
(269, 220, 'Imprimir nómina', 'vacaciones_nomina.php', NULL, '', '', 1, '', '', NULL),
(270, 228, 'Agregar concepto', 'movimientos_nomina_liquidacion_agregar.php', NULL, '', '', 1, '', '', NULL),
(271, 228, 'Imprimir recibo', 'rpt_recibo_pago_pdf.php', NULL, '', '', 1, '', '', NULL),
(272, 162, 'Selección de planilla', 'buscar_tipos_nominas.php', NULL, '', '', 1, '', '', NULL),
(273, 162, 'Selección de frecuencia', 'buscar_tipos_frecuencias.php', NULL, '', '', 1, '', '', NULL),
(274, 162, 'Selección de situación', 'buscar_tipos_situaciones.php', NULL, '', '', 1, '', '', NULL),
(275, 162, 'Selección de acumulado', 'buscar_tipos_acumulados.php', NULL, '', '', 1, '', '', NULL),
(276, 221, 'Agregar', 'ag_nomina_vacaciones.php', NULL, '', '', 1, '', '', NULL),
(277, 221, 'Procesar Nomina', 'barraprogreso_vacaciones.php', NULL, '', '', 1, '', '', NULL),
(278, 227, 'Procesar Ficha', 'barraprogreso_liq_vac.php', NULL, '', '', 1, '', '', NULL),
(279, 228, 'Datos de liquidacion', 'liquidacion.php', NULL, '', '', 1, '', '', NULL),
(280, 188, 'Procesos', 'procesos.php', NULL, '', '', 1, '', '', NULL),
(281, 200, 'Recibos de pago', 'rpt_recibo_pago2.php', NULL, '', '', 1, '', '', NULL),
(282, NULL, 'Tesoreria', '', 9, NULL, '', 1, '', '', 'glyphicon-credit-card'),
(283, 282, 'Bancos', '../tesoreria/bancos.php', 1, NULL, '', 1, '', '', NULL),
(284, 282, 'Consulta de Cheques', '../tesoreria/consulta_cheques.php', 4, NULL, '', 1, '', '', NULL),
(285, 282, 'Cheques por Acreedores y Empleados', '../tesoreria/seleccionar_banco.php?entrada=1', 2, NULL, '', 1, '', '', NULL),
(287, 10, 'Tabulador Salarial', 'tabulador_categorias.php', 8, NULL, '', 1, '', '', NULL),
(288, 10, 'Grado y Pasos', 'gradospasos_list.php', 14, NULL, '', 1, '', '', NULL),
(289, 10, 'Tabulador', 'tabulador_seguro_list.php', 15, NULL, '', 1, '', '', NULL),
(290, 9, 'Datos de Integrantes (Egresados)', 'maestro_personal_egresados.php', 2, NULL, '', 0, '', '', NULL),
(291, 65, 'Facturas', '../prestamos/facturas_list.php', 1, NULL, '', 1, '', '', NULL),
(292, 7, 'Control de Acceso', 'control_acceso.php', 5, NULL, '', 1, '', '', NULL),
(293, 7, 'Control de Acceso TXT', 'control_acceso2.php', 6, NULL, '', 1, '', '', NULL),
(294, 4, 'Integrantes (Personal)', 'submenu_reportes_integrantes.php', 1, NULL, '', 1, '', '', NULL),
(295, 4, 'Reportes de Configuración', 'submenu_reportes_configuracion.php', 3, NULL, '', 1, '', '', NULL),
(296, 312, 'SIPE', '../procesos/filtro_nomina.php?opcion=7', 1, NULL, '', 1, '../img_sis/menu/sipe.jpg', 'Generar TXT de Planilla para el SIPE', NULL),
(297, 312, 'ACH', '../procesos/filtro_nomina.php?opcion=1', 2, NULL, '', 1, '../img_sis/menu/bancogeneral.jpg', 'Generar ACH de Planila para el General', NULL),
(298, 312, 'PEACHTREE', '../procesos/filtro_nomina.php?opcion=12', 3, NULL, '', 1, '../img_sis/menu/PEACHTREE.jpg', 'Generar TXT de Planilla para PEACHTREE', NULL),
(299, 312, 'EXCEL', '../procesos/filtro_nomina.php?opcion=13', 4, NULL, '', 1, '../img_sis/menu/banitsmo.jpg', 'Generar Excel de Planilla para BANISTMO', NULL),
(300, 1, 'Contabilización Planilla', '../procesos/filtro_nomina4.php', 5, NULL, '', 1, '../img_sis/menu/conta bilidad.jpg', 'Contabilizar Planillas', NULL),
(301, 1, 'Recibos de pago a correo', '../procesos/filtro_nomina.php?opcion=11', 6, NULL, '', 1, '../img_sis/menu/e mail.jpg', 'Generar Correos Planilla', NULL),
(302, 312, 'EXCEL', '../procesos/filtro_nomina.php?opcion=14', 7, NULL, '', 1, '../img_sis/menu/sipe.jpg', 'Generar TXT de Planilla para el SIPE', NULL),
(303, 1, 'MEF', '../procesos/filtro_nomina.php?opcion=15', 8, NULL, '', 1, '../img_sis/menu/mef.jpg', 'Generar TXT de Planilla para MEF', NULL),
(304, 1, 'EXCEL PAGO POR SOBRE', '../procesos/filtro_nomina.php?opcion=16', 9, NULL, '', 1, '', '', NULL),
(305, 1, 'EXCEL PAGO POR SOBRE SIN CTA', '../procesos/filtro_nomina.php?opcion=17', 10, NULL, '', 1, '', '', NULL),
(306, 1, 'IMPORTAR EMPLEADOS', '../procesos/migrar_empleados.php', 11, NULL, '', 1, '', '', NULL),
(307, 10, 'Catalogo Cuentas Contables', 'cuenta_contable.php', 17, NULL, '', 1, '', '', NULL),
(308, 10, 'Catalogo Cuenta Presupuestaria', 'cuenta_presupuesto.php', 18, NULL, '', 1, '', '', NULL),
(309, 10, 'Posiciones', 'maestro_posicion.php', 19, NULL, '', 1, '', '', NULL),
(310, 312, 'ACH Banco Nacional de Panama', '../procesos/ach_bnp.php', 11, NULL, '', 1, '../img_sis/menu/bnp.jpg', 'Genera TXT de Banco Nacional de Panama', NULL),
(311, 1, 'Txt Especiales', '../procesos/txt_especiales.php', 12, NULL, '', 1, '', 'Genera TXT personalizados', NULL),
(312, 1, 'ACH', '../procesos/menu_ach.php', 1, NULL, '', 1, '', '', NULL),
(313, 10, 'Constancias', 'submenu_constancias.php', 20, '', '', 1, '', '', NULL),
(314, 282, 'Impresion de Cheques', '../tesoreria/seleccionar_nomina2.php', 4, NULL, '', 1, '', '', NULL),
(316, 10, 'Funciones', 'maestro_funcion.php', 20, NULL, '', 1, '', '', NULL),
(317, 1, 'Recibos excel a correo', '../procesos/filtro_nomina.php?opcion=21', 6, NULL, '', 1, '../img_sis/menu/e mail.jpg', 'Generar Correos Excel', NULL),
(318, NULL, 'Evaluaciones', 'menu_procesos.php', 30, NULL, '', 1, '', '', 'glyphicon-user'),
(319, NULL, 'Capacitaciones', 'capacitaciones.php', 31, NULL, 'acce_elegibles', 1, '', '', 'glyphicon-user'),
(320, NULL, 'Presupuesto', 'presupuesto.php', 32, NULL, 'acce_elegibles', 1, '', '', 'glyphicon-user'),
(321, 318, 'Evaluación de Desempeño', 'evaluacion_empleado_list.php', 6, NULL, '', 1, '../img_sis/menu/e mail.jpg', 'Evaluación de Desempeño', NULL),
(322, 318, 'Evaluación 360 Grados', 'evaluacion_personal_list.php', 6, NULL, '', 1, '../img_sis/menu/e mail.jpg', 'Evaluación 360 Grados', NULL),
(323, 319, 'Capacitaciones y Entrenamientos', 'capacitacion_list.php', 1, NULL, 'acce_elegibles', 1, '', 'Capacitaciones y Entrenamientos', 'glyphicon-user'),
(324, 320, 'Ejecución Presupuestaria ', 'presupuesto.php', 1, NULL, 'acce_elegibles', 1, '', '', 'glyphicon-user'),
(325, 9, 'Datos de Integrantes Contralorí­a', 'datos_integrantes/listado_integrantes_contraloria.php', 2, '', '', 1, '', '', NULL),
(326, 1, 'Importar Empleados Excel', '../procesos/importar_empleados_excel.php', 13, NULL, '', 1, '', '', NULL),
(327, 10, 'Log Transacciones', 'maestro_log.php', 23, NULL, '', 1, '', '', NULL),
(328, 1, 'Exportar Colaboradores Excel', '../procesos/exportar_empleados_excel.php', 14, NULL, '', 1, '', '', NULL),
(329, 1, 'Exportar Ficha Colaboradores Excel', '../procesos/exportar_ficha_colaboradores_excel.php', 15, NULL, '', 1, '', '', NULL),
(330, 10, 'Formatos de reloj', 'control_asistencia/configuracion_reloj.php', 20, NULL, '', 1, '', '', NULL),
(331, 7, 'Control de Asistencia', 'control_asistencia/listado_archivos.php', 7, NULL, '', 1, '', '', NULL),
(332, 10, 'Horarios Rotativos', 'horarios_rotativos.php', 20, NULL, '', 1, '', '', NULL),
(333, 61, 'Constancias Generadas', 'cartas_trabajo/listado_constancias_generadas.php', NULL, '', '', 1, '', '', NULL),
(334, 61, 'Validar Constancia', 'cartas_trabajo/buscar_constancia.php', NULL, '', '', 1, '', '', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nom_motivos_retiros`
--

CREATE TABLE IF NOT EXISTS `nom_motivos_retiros` (
  `codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `nom_motivos_retiros`
--

INSERT INTO `nom_motivos_retiros` (`codigo`, `descripcion`) VALUES
(1, 'Traslado a Otra Empresa'),
(2, 'Renuncia'),
(4, 'Despido Justificado'),
(5, 'Reestructuracion Organizacion');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nom_movimientos_historico`
--

CREATE TABLE IF NOT EXISTS `nom_movimientos_historico` (
  `id_historico` int(11) NOT NULL AUTO_INCREMENT,
  `codnom` int(11) NOT NULL,
  `tipnom` int(11) NOT NULL,
  `codnivel1` int(11) NOT NULL,
  `codnivel2` int(11) NOT NULL,
  `codnivel3` int(11) NOT NULL,
  `codnivel4` int(11) NOT NULL,
  `codnivel5` int(11) NOT NULL,
  `codnivel6` int(11) NOT NULL,
  `codnivel7` int(11) NOT NULL,
  `ficha` int(11) NOT NULL,
  `sueldo` float(10,2) NOT NULL,
  `codcargo` int(11) NOT NULL,
  `situacion` varchar(50) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  PRIMARY KEY (`id_historico`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nom_movimientos_nomina`
--

CREATE TABLE IF NOT EXISTS `nom_movimientos_nomina` (
  `codnom` int(11) NOT NULL,
  `codcon` int(11) NOT NULL,
  `ficha` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `tipcon` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `valor` decimal(17,2) DEFAULT NULL,
  `monto` decimal(17,2) DEFAULT NULL,
  `unidad` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `impdet` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `descrip` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `montobase` decimal(17,2) DEFAULT NULL,
  `codbancob` int(11) DEFAULT NULL,
  `cuentacob` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codbanlph` int(11) DEFAULT NULL,
  `cuentalph` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `refcheque` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `montototal` decimal(17,2) DEFAULT NULL,
  `contrato` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `bonificable` tinyint(4) DEFAULT NULL,
  `htiempo` tinyint(4) DEFAULT NULL,
  `cedula` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `saldopre` decimal(17,2) DEFAULT NULL,
  `montootros` decimal(17,2) DEFAULT NULL,
  `modificar` tinyint(4) DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  `ref1` decimal(5,2) DEFAULT NULL,
  `ref2` decimal(5,2) DEFAULT NULL,
  `ref3` decimal(5,2) DEFAULT NULL,
  `ref4` decimal(5,2) DEFAULT NULL,
  `ref5` decimal(5,2) DEFAULT NULL,
  `ref6` decimal(5,2) DEFAULT NULL,
  `ref7` decimal(5,2) DEFAULT NULL,
  `codnivel1` varchar(7) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnivel2` varchar(7) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnivel3` varchar(7) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnivel4` varchar(7) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnivel5` varchar(7) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnivel6` varchar(7) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnivel7` varchar(7) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipnom` int(11) NOT NULL,
  `contractual` varchar(1) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`codnom`,`codcon`,`ficha`,`tipnom`),
  KEY `codcon` (`codcon`),
  KEY `ficha` (`ficha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nom_nominas_pago`
--

CREATE TABLE IF NOT EXISTS `nom_nominas_pago` (
  `codnom` int(11) NOT NULL,
  `descrip` varchar(70) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `fechapago` date DEFAULT NULL,
  `periodo_ini` date DEFAULT NULL,
  `periodo_fin` date DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `codtip` int(11) NOT NULL DEFAULT '0',
  `frecuencia` int(11) DEFAULT NULL,
  `status` varchar(1) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipnom` tinyint(4) DEFAULT NULL,
  `libre` tinyint(4) DEFAULT NULL,
  `codsuc` int(11) DEFAULT NULL,
  `coddir` int(11) DEFAULT NULL,
  `codvp` int(11) DEFAULT NULL,
  `codger` int(11) DEFAULT NULL,
  `coddep` int(11) DEFAULT NULL,
  `nivel1` tinyint(4) DEFAULT NULL,
  `nivel2` tinyint(4) DEFAULT NULL,
  `nivel3` tinyint(4) DEFAULT NULL,
  `nivel4` tinyint(4) DEFAULT NULL,
  `nivel5` tinyint(4) DEFAULT NULL,
  `codcargo` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `todocargo` tinyint(4) DEFAULT NULL,
  `vacprograma` tinyint(4) DEFAULT NULL,
  `markar` tinyint(4) DEFAULT NULL,
  `vaccolectivas` tinyint(4) DEFAULT NULL,
  `contrato` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `sfecha` datetime DEFAULT NULL,
  `sfechapago` datetime DEFAULT NULL,
  `speriodo_ini` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `speriodo_fin` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `cod_tli` varchar(19) COLLATE utf8_spanish_ci NOT NULL,
  `periodo` int(11) DEFAULT NULL,
  `codht1` int(11) DEFAULT NULL,
  `codht2` int(11) DEFAULT NULL,
  `ee` tinyint(4) DEFAULT NULL,
  `nperiodo` smallint(6) DEFAULT NULL,
  `codht3` int(11) DEFAULT NULL,
  `comprometida` int(1) NOT NULL,
  `contabilizada` int(1) NOT NULL,
  `status_acreedores` int(1) NOT NULL,
  `status_cheques` int(1) NOT NULL,
  PRIMARY KEY (`codnom`,`codtip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nom_nomina_netos`
--

CREATE TABLE IF NOT EXISTS `nom_nomina_netos` (
  `codnom` int(11) NOT NULL,
  `tipnom` int(11) NOT NULL,
  `ficha` int(10) NOT NULL,
  `cedula` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `cta_ban` varchar(21) COLLATE utf8_spanish_ci NOT NULL,
  `neto` float(20,2) NOT NULL,
  PRIMARY KEY (`codnom`,`tipnom`,`ficha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nom_paginas`
--

CREATE TABLE IF NOT EXISTS `nom_paginas` (
  `id_pagina` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) NOT NULL,
  `archivo` varchar(255) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `icono` varchar(50) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  `cod_modulo` int(11) NOT NULL,
  PRIMARY KEY (`id_pagina`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Volcado de datos para la tabla `nom_paginas`
--

INSERT INTO `nom_paginas` (`id_pagina`, `descripcion`, `archivo`, `activo`, `icono`, `orden`, `cod_modulo`) VALUES
(1, 'Generar Vacaciones', 'vacaciones_generar.php', 1, 'fa fa-plane', NULL, 217),
(2, 'Generar vacaciones por trabajado', 'vacaciones_generar.php?opcion=1', 1, 'fa fa-calendar', NULL, 217),
(3, 'Mantenimiento de vacaciones', 'vacaciones_mantenimiento.php', 1, 'fa fa-calendar-o', NULL, 217),
(4, 'Listado de vacaciones', '../tcpdf/reportes/reporte_vacaciones_persona.php', 1, 'icon-calendar', NULL, 217),
(5, 'Planilla de Vacaciones', 'nomina_de_vacaciones.php', 1, 'glyphicon glyphicon-calendar', NULL, 217),
(6, 'Reporte Desglose Monedas', 'config_rpt_desglose.php', 1, 'fa fa-money', NULL, 45),
(7, 'Análisis por conceptos', 'config_rpt_nomina.php?opcion=analisis', 1, 'fa fa-bars', NULL, 45),
(8, 'Reporte de Planilla', 'config_rpt_nomina.php?opcion=general_csb', 1, 'fa fa-file-text', NULL, 45),
(9, 'Comprobante de Pago', 'config_rpt_nomina.php?opcion=recibos_csb', 1, 'fa fa-file', NULL, 45),
(10, 'Relacion de conformidad', 'config_rpt_nomina.php?opcion=relacion_conformidad_csb', 1, 'glyphicon glyphicon-list-alt', NULL, 45),
(11, 'Deducciones con patronales (por gerencias)', 'filtro_nomina.php?opcion=10', 1, 'fa fa-list', NULL, 45),
(12, 'Anexo 03', 'filtro_nomina.php?opcion=12', 1, 'fa fa-list', NULL, 45),
(13, 'Relación de Permisos y Vacaciones', 'filtro_pv.php', 1, 'glyphicon glyphicon-list-alt', NULL, 45),
(14, 'Acreedores', 'consulta_acreedor.php', 1, 'fa fa-list', NULL, 45),
(15, 'Comprobante de Diario', 'config_rpt_nomina_comprobante.php', 1, 'fa fa-file-excel-o', NULL, 45),
(16, 'Horizontal de Planilla', 'config_rpt_nomina_horizontal2.php', 1, 'fa fa-file-excel-o', NULL, 45),
(17, 'Analisis de Conceptos Consolidados', 'config_rpt_nomina.php?opcion=analisisConsolidado&ban=1', 1, 'glyphicon glyphicon-list', NULL, 45),
(18, 'Consulta de Acumulado', 'consulta_acumulado.php', 1, 'fa fa-file-text', NULL, 45),
(19, 'Detallado de Planilla (ADP)', 'config_rpt_nomina.php?opcion=general_adp', 1, 'glyphicon glyphicon-list-alt', NULL, 45),
(20, 'Recapitulación de planilla ADP', 'config_rpt_nomina.php?opcion=recapitulacion_adp', 1, 'fa fa-file', NULL, 45),
(21, 'Prestamos y saldos', 'config_rpt_nomina.php?opcion=prestamos', 1, 'fa fa-file-excel-o', NULL, 45),
(22, 'Fondo de Cesantía (Trimestral)', 'config_rpt_fondo_cesantia_trimestral.php', 1, 'fa fa-file-excel-o', NULL, 45),
(23, 'Horizontal de Planilla Quincenal', 'config_rpt_nomina_horizontal_modelo2.php', 1, 'fa fa-file-excel-o', NULL, 45),
(24, 'Comprobante Contable', 'config_rpt_comprobante_contable.php?opcion=comprobante', 1, 'fa fa-file', NULL, 45),
(25, 'Planilla Anexo 03 / Resumen Preelaborada', 'filtro_nomina.php?opcion=13', 1, 'fa fa-file-excel-o', NULL, 45),
(26, 'Comprobante de Pago', 'config_rpt_comprobante_pago.php', 1, 'fa fa-file-excel-o', 0, 45),
(27, 'Reporte de Planilla Quincenal', 'config_rpt_nomina_horizontal_modelo3.php', 1, 'fa fa-smile-o', 0, 45),
(28, 'Relación de Depósitos', 'config_rpt_nomina.php?opcion=banco', 1, 'glyphicon glyphicon-list', 0, 45),
(29, 'Relación de Efectivo', 'config_rpt_nomina.php?opcion=efectivo', 1, 'fa fa-money', 0, 45),
(30, 'Resumen de Planilla', 'config_rpt_nomina.php?opcion=resumen_conceptos', 1, 'fa fa-list', 0, 45),
(31, 'Reporte Desglose Monedas Brentwood', 'config_rpt_desglose.php', 1, 'fa fa-money', NULL, 45),
(32, 'Estructura de Personal San Miguelito', 'cartas_trabajo/estructura_personal.php', 1, 'fa fa-file-excel-o', 0, 45),
(33, 'Listado de Planilla San Miguelito', 'config_rpt_nomina.php?opcion=listado_planilla_sanmiguelito', 1, 'glyphicon glyphicon-list-alt', 0, 45),
(34, 'Recapitulacion de Planilla San Miguelito', 'config_rpt_nomina.php?opcion=recapitulacion_planilla_sanmiguelito', 1, 'glyphicon glyphicon-list-alt', 0, 45),
(35, 'Transferencias de Planilla San Miguelito', 'config_rpt_nomina.php?opcion=transferencias_planilla_sanmiguelito', 1, 'glyphicon glyphicon-list-alt', 0, 45),
(36, 'Siacap-Aportes Mensuales San Miguelito', 'config_rpt_nomina.php?opcion=siacap_aportes_mensuales_planilla_sanmiguelito', 1, 'glyphicon glyphicon-list-alt', 0, 45),
(37, 'Listado Alfabetico San Miguelito', 'config_rpt_nomina.php?opcion=listado_alfabetico_sanmiguelito', 1, 'fa fa-file-excel-o', 0, 45),
(38, 'Posiciones vacantes', '../../reportes/rpt_posiciones_vacantes.php', 1, 'fa fa-list', 0, 45);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nom_progvacaciones`
--

CREATE TABLE IF NOT EXISTS `nom_progvacaciones` (
  `periodo` int(11) NOT NULL,
  `ficha` int(10) NOT NULL,
  `ceduda` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `ddisfrute` decimal(7,2) NOT NULL,
  `dpago` decimal(7,2) NOT NULL,
  `dpagob` decimal(7,2) NOT NULL,
  `fechavac` date NOT NULL,
  `fechareivac` date NOT NULL,
  `operacion` varchar(3) COLLATE utf8_spanish_ci NOT NULL,
  `fechaopr` date NOT NULL,
  `estado` varchar(21) COLLATE utf8_spanish_ci NOT NULL,
  `monto` decimal(17,2) NOT NULL,
  `tipnom` int(2) NOT NULL,
  `codsuc` varchar(6) COLLATE utf8_spanish_ci NOT NULL,
  `coddir` varchar(6) COLLATE utf8_spanish_ci NOT NULL,
  `codvp` varchar(6) COLLATE utf8_spanish_ci NOT NULL,
  `codger` varchar(6) COLLATE utf8_spanish_ci NOT NULL,
  `coddep` varchar(6) COLLATE utf8_spanish_ci NOT NULL,
  `detalle` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `codnom` int(4) NOT NULL,
  `sfechavac` date NOT NULL,
  `sfechareivac` date NOT NULL,
  `sfechaopr` date NOT NULL,
  `tipooper` varchar(2) COLLATE utf8_spanish_ci NOT NULL,
  `desoper` varchar(30) COLLATE utf8_spanish_ci NOT NULL,
  `ee` varchar(2) COLLATE utf8_spanish_ci NOT NULL,
  `fecha_venc` date DEFAULT NULL,
  UNIQUE KEY `periodo` (`periodo`,`ficha`,`tipnom`,`tipooper`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nom_variables_personal`
--

CREATE TABLE IF NOT EXISTS `nom_variables_personal` (
  `nombre` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `descripcion` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `parametros` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `indicador` varchar(2) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `nom_variables_personal`
--

INSERT INTO `nom_variables_personal` (`nombre`, `descripcion`, `parametros`, `indicador`) VALUES
('$ANIOVACACION', 'Periodos de Vacaciones', '', 'V'),
('$ANTIGUEDAD', 'Calcula la Antiguedad segun tipo ''A''=aÃ±os, ''M''=meses, ''D''=dias', 'ANTIGUEDAD($FECHA1, $FECHA2,TIPO)', 'F'),
('$BONODIACAT', 'Bono Diario CategorÃ­a', '', 'V'),
('$BONOMESCAT', 'Bono Mesual CategorÃ­a', '', 'V'),
('$CAMPOADICIONALPER', 'Retorna el valor del campo adicional2', 'CAMPOADICIONALPER(<CÃ³digo>)', 'F'),
('$CEDULA', 'No. de CÃ©dula de Identidad', '', 'V'),
('$CODCARGO', 'CÃ³digo de Cargo', '', 'V'),
('$CODCATEGORIA', 'CÃ³digo de la Categoria', '', 'V'),
('$CODIGOSUSP', 'CÃ³digo de la Ultima SuspenciÃ³n', '', 'V'),
('$CODPROFESION', 'CÃ³digo de ProfesiÃ³n', '', 'V'),
('$CONTRATO', 'Contrato', '', 'V'),
('$DIAFMES', 'DÃ­as Feriados del Mes (desde el Inicio de NÃ³mina), segÃºn calendario del empresa', '', 'V'),
('$DIAFMESTIP', 'DÃ­as Feriados del Mes (desde el Inicio de NÃ³mina), segÃºn tipo de nÃ³mina', '', 'V'),
('$DIAFPER', 'DÃ­as Feriados del PerÃ­odo (desde el Inicio, fin de NÃ³mina), segÃºn calendario de la empresa', '', 'V'),
('$DIAFPERPER', 'DÃ­as Feriados del PerÃ­odo (desde el Inicio, fin de NÃ³mina), segÃºn calendario del trabajador', '', 'V'),
('$DIAFPERTIP', 'DÃ­as Feriados del PerÃ­odo (desde el Inicio, fin de NÃ³mina), segÃºn tipo de nÃ³mina', '', 'V'),
('$DIAHMES', 'Dias HÃ¡biles del Mes (desde inicio de nÃ³mina),segÃºn calendario de la empresa', '', 'V'),
('$DIAHMESPER', 'Dias HÃ¡biles del Mes (desde inicio de nÃ³mina),segÃºn calendario del trabajador', '', 'V'),
('$DIAHMESTIP', 'Dias HÃ¡biles del Mes (desde inicio de nÃ³mina),segÃºn tipo de nÃ³mina', '', 'V'),
('$DIAHPER', 'Dias HÃ¡biles del perÃ­odo (desde inicio,fin de nÃ³mina),segÃºn calendario de la empresa', '', 'V'),
('$DIAHPERPER', 'Dias HÃ¡biles del perÃ­odo (desde inicio,fin de nÃ³mina),segÃºn calendario del trabajador', '', 'V'),
('$DIAHPERTIP', 'Dias HÃ¡biles del perÃ­odo (desde inicio,fin de nÃ³mina),segÃºn tipo de nÃ³mina', '', 'V'),
('$EDAD', 'Edad del Trabajador', '', 'V'),
('$FECFFINVAC', 'Fecha Retorno Vacaciones', '', 'V'),
('$FECFINIVAC', 'Fecha Salida Vacaciones', '', 'V'),
('$FECHAAPLICACION', 'Fecha de la aplicaciÃ³n del sueldo propuesto', '', 'V'),
('$FECHAFINCONTRATO', 'Fecha final del contrato, si no es fijo', '', 'V'),
('$FECHAFINNOM', 'Fecha final del periodo de NÃ³mina', '', 'V'),
('$FECHAFINSUSP', 'Fecha final de suspenciÃ³n', '', 'V'),
('$FECHAHOY', 'Fecha de hoy (fecha del sistema)', '', 'V'),
('$FECHAINGRESO', 'Fecha de Ingreso del Trabajador', '', 'V'),
('$FECHAINISUSP', 'Fecha Inicio de SuspenciÃ³n', '', 'V'),
('$FECHANACIMIENTO', 'Fecha de Nacimiento del trabajador', '', 'V'),
('$FECHANOMINA', 'Fecha inicial del periodo de la nomina', '', 'V'),
('$FECHAPAGNOM', 'Fecha de pago de la nÃ³mina', '', 'V'),
('$FECLIQ', 'Fecha de LiquidaciÃ³n', '', 'V'),
('$FICHA', 'Ficha del Trabajador', '', 'V'),
('$FORMACOBRO', 'Forma de cobro', '', 'V'),
('$FRECUENCIANOM', 'Codigo del Tipo de frecuencia de la nÃ³mina', '', 'V'),
('$GR', 'Grupo Categoria', '', 'V'),
('$LUNES', 'Cantidad de Lunes del mes de Proceso', '', 'V'),
('$LUNESPER', 'Cantidad de lunes del periodo (inicio, fin de nomina)', '', 'V'),
('$NIVEL1', 'Codigo nivel funcional 1', '', 'V'),
('$NIVEL2', 'Codigo nivel funcional 2', '', 'V'),
('$NIVEL3', 'Codigo nivel funcional 3', '', 'V'),
('$NIVEL4', 'Codigo nivel funcional 4', '', 'V'),
('$NIVEL5', 'Codigo nivel funcional 5', '', 'V'),
('$NIVEL6', 'Codigo nivel funcional 6', '', 'V'),
('$NIVEL7', 'Codigo nivel funcional 7', '', 'V'),
('$PERIODOVAC', 'AÃ±o para el calculo de Vacaciones', '', 'V'),
('$SALCAT', 'Salario CategorÃ­a', '', 'V'),
('$SEXO', 'Sexo del Trabajador', '', 'V'),
('$SITUACION', 'SituaciÃ³n del Trabajador', '', 'V'),
('$SUELDO', 'Sueldo del Trabajador', '', 'V'),
('$SUELDOPROPUESTO', 'Sueldo Propuesto del Trabajador', '', 'V'),
('$T01=', 'Variable de uso libre', '', 'V'),
('$TIPOCONTRATO', 'Tipo de Contrato', '', 'V'),
('$TIPOLIQUIDACION', 'Tipo de liquidacion segun tasa de tipos de liquidaciÃ³n', '', 'V'),
('$TIPONOMINA', 'Tipo de NÃ³mina a la que pertenece el trabajador', '', 'V'),
('$TIPOPRESTACION', 'Tipo de Prestacion del trabajador', '', 'V'),
('ACUMCOM', 'ACUMCOM(codigo_concepto,fecha_inicio,fecha_fin); devuelve el monto acumulado segun el codigo del con', 'ACUMCOM(codcon,fecha_inicio,fecha_fin)', 'F'),
('BAREMO', 'BAREMO($codigo_baremo,$valor); retorna el resultado del baremo indicado, segÃºn el rango del valor.', 'BAREMO($codigo_baremo,$valor)', 'F'),
('CONCEPTO', 'CONCEPTO(codigo_concepto); devuelve el valor del monto del concepto de la nomina actual, segun el co', 'CONCEPTO(codigo_concepto)', 'F'),
('CONCEPTONOMANT', 'CONCEPTONOMANT(cÃ³digo_concepto,opciÃ³n); Retorna el resultado del concepto indicado de la nÃ³mina a', 'CONCEPTONOMANT(cÃ³digo_concepto,opciÃ³n)', 'F'),
('DIA', 'DIA($fecha); Devuelve el dÃ­a en nÃºmero segÃºn la fecha indicada.', 'DIA()', 'F'),
('MENSAJECON', 'MENSAJECON(VARIABLE); Devuelve el valor que contenga una variable $T01..$T10,$MONTO.', 'MENSAJECON($VARIABLE)', 'F'),
('Mes', 'devuelve el mes de una fecha dada', 'mes(AAAA/MM/DD)', 'F'),
('SI', 'SI("condicion",verdadero,falso); Retorna un valor verdadero o falso segÃºn la condiciÃ³n', 'SI(condicion,V,F)', 'F'),
('SUMA_POR_CONCEPTO', 'SUMA_POR_CONCEPTO($codcon,$fecha_inicio,$fecha_fin,$frecuencias); retorna la sumatoria de segÃºn codigo de concepto. Ej.: (100,$FECHAINGRESO,$FECHANOMINA,"2,3");', 'SUMA_POR_CONCEPTO($codcon,$fecha_inicio,$fecha_fin,$frecuencias)', 'F');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parametro_inclusion`
--

CREATE TABLE IF NOT EXISTS `parametro_inclusion` (
  `id_parametro_inclusion` int(11) NOT NULL AUTO_INCREMENT,
  `ministerio` varchar(100) DEFAULT NULL,
  `area` varchar(20) DEFAULT NULL,
  `nombre_entidad` varchar(100) DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_parametro_inclusion`),
  UNIQUE KEY `id_parametro_inclusion` (`id_parametro_inclusion`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `parametro_inclusion`
--

INSERT INTO `parametro_inclusion` (`id_parametro_inclusion`, `ministerio`, `area`, `nombre_entidad`, `logo`) VALUES
(1, '12', '00', 'MINISTERIO DE SALUD', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `param_ws`
--

CREATE TABLE IF NOT EXISTS `param_ws` (
  `usuario` varchar(150) DEFAULT NULL,
  `user_p` varchar(150) DEFAULT NULL,
  `dir_ws` varchar(3000) DEFAULT NULL,
  `id_g_empleados` varchar(300) DEFAULT NULL,
  `dir_res` varchar(600) DEFAULT NULL,
  `dir_doc` varchar(600) DEFAULT NULL,
  `acepta_vac_neg` char(1) DEFAULT NULL,
  `url_jasper` varchar(600) DEFAULT NULL,
  `nombre_institucion` varchar(300) DEFAULT NULL,
  `nombre_pais` varchar(300) DEFAULT NULL,
  `nombre_departamento` varchar(750) DEFAULT NULL,
  `presidente` varchar(300) DEFAULT NULL,
  `ministro` varchar(300) DEFAULT NULL,
  `director` varchar(300) DEFAULT NULL,
  `ced_director` varchar(60) DEFAULT NULL,
  `valida_tiempo_e` char(1) DEFAULT NULL,
  `val_e` varchar(300) DEFAULT NULL,
  `pm_ver` varchar(15) DEFAULT NULL,
  `valida_tiempo_c` int(5) DEFAULT NULL,
  `id` int(5) DEFAULT NULL,
  `gen_tiempo_i` char(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `param_ws`
--

INSERT INTO `param_ws` (`usuario`, `user_p`, `dir_ws`, `id_g_empleados`, `dir_res`, `dir_doc`, `acepta_vac_neg`, `url_jasper`, `nombre_institucion`, `nombre_pais`, `nombre_departamento`, `presidente`, `ministro`, `director`, `ced_director`, `valida_tiempo_e`, `val_e`, `pm_ver`, `valida_tiempo_c`, `id`, `gen_tiempo_i`) VALUES
('admin', '21232f297a57a5a743894a0e4a801fc3', 'http://186.74.196.171:8080/sysminsa_rh/en/classic/services/wsdl2', '4274621964a7889e7b71672027549229', 'http://186.74.196.172/rechumanos/res.php', 'http://186.74.196.172/rechumanos/doc.php', '0', 'http://186.74.196.173:8989/jasperserver/flow.html?_flowId=viewReportFlow&reportUnit=/Migracion/Reporte/', 'AUTORIDAD DE PROTECCION AL CONSUMIDOR Y DEFENSA', 'REPUBLICA DE PANAMÃ', 'OFICINA INSTITUCIONAL DE RECURSOS HUMANOS', NULL, NULL, 'FERNANDO ECHEVERRIA', NULL, '1', 'Copyright Â© 2010-2015 Ginteven Servicios, S.A. Todos os derechos reservados. Version Comunitaria', '2.5', 3, 1, 'F'),
('', '', '', '', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, NULL, 0, 1, 'A');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pase`
--

CREATE TABLE IF NOT EXISTS `pase` (
  `cedula` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `apenom` varchar(60) COLLATE utf8_spanish_ci DEFAULT NULL,
  `sexo` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `estado_civil` varchar(13) COLLATE utf8_spanish_ci NOT NULL,
  `direccion` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `zonapos` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `telefonos` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `email` varchar(40) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecnac` date DEFAULT NULL,
  `lugarnac` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codpro` int(11) DEFAULT NULL,
  `foto` varchar(80) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipnom` int(11) NOT NULL DEFAULT '0',
  `codnivel1` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnivel2` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnivel3` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnivel4` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codnivel5` varchar(8) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ficha` int(10) NOT NULL,
  `fecing` date DEFAULT NULL,
  `codcat` varchar(6) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codcargo` varchar(12) COLLATE utf8_spanish_ci DEFAULT NULL,
  `forcob` varchar(39) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codbancob` int(11) DEFAULT NULL,
  `cuentacob` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `codbanlph` int(11) DEFAULT NULL,
  `cuentalph` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `estado` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipemp` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecfin` int(11) DEFAULT NULL,
  `sueldopro` decimal(20,5) DEFAULT NULL,
  `fechaplica` date DEFAULT NULL,
  `codidi` int(11) DEFAULT NULL,
  `fecnacr` varchar(5) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tipopres` tinyint(4) DEFAULT NULL,
  `fechasus` date DEFAULT NULL,
  `fechareisus` date DEFAULT NULL,
  `fechavac` date DEFAULT NULL,
  `fechareivac` date DEFAULT NULL,
  `fecharetiro` date DEFAULT NULL,
  `aplicalogro` tinyint(4) DEFAULT NULL,
  `aplicasuspension` tinyint(4) DEFAULT NULL,
  `ctacontab` varchar(22) COLLATE utf8_spanish_ci DEFAULT NULL,
  `periodo` int(11) DEFAULT NULL,
  `markar` tinyint(4) DEFAULT NULL,
  `cod_tli` varchar(19) COLLATE utf8_spanish_ci NOT NULL,
  `motivo_liq` varchar(8) COLLATE utf8_spanish_ci NOT NULL,
  `preaviso` varchar(2) COLLATE utf8_spanish_ci NOT NULL,
  `suesal` decimal(20,2) DEFAULT NULL,
  `contrato` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombres` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  `apellidos` varchar(30) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`tipnom`,`ficha`),
  UNIQUE KEY `ficha` (`ficha`,`cedula`),
  KEY `codcargo` (`codcargo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `posicionempleado`
--

CREATE TABLE IF NOT EXISTS `posicionempleado` (
  `IdEmpleado` int(11) DEFAULT NULL,
  `FechaInicio` date DEFAULT NULL,
  `Posicion` varchar(75) DEFAULT NULL,
  `FechaFin` date DEFAULT NULL,
  `IdTituloInstitucional` varchar(33) DEFAULT NULL,
  `IdFuncion` int(11) DEFAULT NULL,
  `Status` varchar(30) DEFAULT NULL,
  `Resolucion` varchar(300) DEFAULT NULL,
  `Planilla` int(11) DEFAULT NULL,
  `CuentaContable` varchar(150) DEFAULT NULL,
  `IdMotivoSalida` int(11) DEFAULT NULL,
  `IdDepartamento` int(11) DEFAULT NULL,
  `IdTipoEmpleado` int(11) DEFAULT NULL,
  `ObservacionesPosicion` varchar(765) DEFAULT NULL,
  `Salario` decimal(12,0) DEFAULT NULL,
  `gastos_repre` decimal(12,0) DEFAULT NULL,
  `decre_nombra` varchar(750) DEFAULT NULL,
  `fecha_decre` date DEFAULT NULL,
  `nota_asigna` varchar(750) DEFAULT NULL,
  `fecha_nota_asigna` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Disparadores `posicionempleado`
--
DROP TRIGGER IF EXISTS `tgr_ins_posicionempleado`;
DELIMITER //
CREATE TRIGGER `tgr_ins_posicionempleado` AFTER INSERT ON `posicionempleado`
 FOR EACH ROW BEGIN
 DECLARE v_usruid VARCHAR(100);
 DECLARE v_anio VARCHAR(10);
 DECLARE v_tiempo INT;
 DECLARE v_fec_ini DATE;
 DECLARE v_param CHAR(1);
   
 UPDATE tituloinstitucional
 SET total = total + 1,
 dispo = dispo - 1
 WHERE IdTituloInstitucional = NEW.IdTituloInstitucional;
  
 SET v_anio = CONVERT(YEAR(NOW()), CHAR(5));
 
 SELECT gen_tiempo_i INTO v_param FROM param_ws LIMIT 1;
 
 IF v_param = 'A' THEN
  SET v_fec_ini = CURDATE();
 ELSE
  SELECT STR_TO_DATE(CONCAT(YEAR(NOW()), '-', MONTH(fecing), '-', DAY(fecing)), '%Y-%m-%d')
  INTO v_fec_ini  FROM nompersonal  WHERE personal_id = NEW.IdEmpleado LIMIT 1;  
 END IF;
 
 SELECT tiempo INTO v_tiempo FROM tipo_justificacion WHERE idtipo =  5 LIMIT 1; 
 
 SELECT useruid INTO v_usruid FROM nompersonal WHERE personal_id = NEW.IdEmpleado LIMIT 1; 
 
 INSERT INTO dias_incapacidad
 (cod_user, tipo_justificacion, fecha, tiempo, observacion, documento, st, usr_uid, dias, horas, minutos) 
 VALUES(NEW.Posicion, 5, v_fec_ini, v_tiempo, CONCAT('REGISTRO INICIAL ', v_anio), NULL, NULL, v_usruid, (v_tiempo/8), 0,0); 
 
 /*TIEMPO DE INCAPACIDAD POR FAMILIAR DISCAPACITADO*/
 SELECT tiempo INTO v_tiempo FROM tipo_justificacion WHERE idtipo =  8 LIMIT 1; 
 INSERT INTO dias_incapacidad
 (cod_user, tipo_justificacion, fecha, tiempo, observacion, usr_uid, dias, horas, minutos)
 SELECT NEW.posicion,  8, v_fec_ini, v_tiempo, CONCAT('REGISTRO INICIAL ', v_anio), v_usruid, (v_tiempo/8), 0,0 
 FROM nompersonal 
 WHERE personal_id = NEW.IdEmpleado
 AND tiene_familiar_disca = 1;
 
 SELECT tiempo INTO v_tiempo FROM tipo_justificacion WHERE idtipo =  6 LIMIT 1;  
 INSERT INTO dias_incapacidad
 (cod_user, tipo_justificacion, fecha, tiempo, observacion, usr_uid, dias, horas, minutos)
 SELECT NEW.posicion,  6, v_fec_ini, v_tiempo, CONCAT('REGISTRO INICIAL ', v_anio), v_usruid, (v_tiempo/8), 0,0 
 FROM nompersonal 
 WHERE personal_id = NEW.IdEmpleado
 AND tiene_discapacidad = 1;
 
 INSERT INTO empleado_cargo
 (IdEmpleado, IdDepartamento, FechaInicio, FechaFinal, TipoMovimiento, fecha_creacion)
 VALUES(NEW.IdEmpleado, NEW.IdDepartamento, NEW.FechaInicio, new.FechaFin, 'N', CURDATE());
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuesto`
--

CREATE TABLE IF NOT EXISTS `presupuesto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cuenta` int(11) NOT NULL,
  `id_nomnivel` int(11) NOT NULL,
  `anio` int(11) NOT NULL,
  `Cuenta` varchar(20) NOT NULL,
  `MontoInicial` decimal(22,2) NOT NULL,
  `Comprometido` decimal(22,2) NOT NULL,
  `Disponible` decimal(22,2) NOT NULL,
  `Enero` decimal(22,2) NOT NULL,
  `Febrero` decimal(22,2) NOT NULL,
  `Marzo` decimal(22,2) NOT NULL,
  `Abril` decimal(22,2) NOT NULL,
  `Mayo` decimal(22,2) NOT NULL,
  `Junio` decimal(22,2) NOT NULL,
  `Julio` decimal(22,2) NOT NULL,
  `Agosto` decimal(22,2) NOT NULL,
  `Septiembre` decimal(22,2) NOT NULL,
  `Octubre` decimal(22,2) NOT NULL,
  `Noviembre` decimal(22,2) NOT NULL,
  `Diciembre` decimal(22,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `procesar`
--

CREATE TABLE IF NOT EXISTS `procesar` (
  `concepto` varchar(8) NOT NULL,
  `valor` int(11) NOT NULL,
  `trabajador` int(11) NOT NULL,
  `cod_pro` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`cod_pro`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reloj_datos`
--

CREATE TABLE IF NOT EXISTS `reloj_datos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ficha` int(6) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `disp_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reloj_detalle`
--

CREATE TABLE IF NOT EXISTS `reloj_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_encabezado` int(11) NOT NULL,
  `ficha` int(6) NOT NULL,
  `fecha` date NOT NULL,
  `entrada` varchar(8) NOT NULL,
  `salmuerzo` varchar(8) NOT NULL,
  `ealmuerzo` varchar(8) NOT NULL,
  `salida` varchar(8) NOT NULL,
  `ordinaria` varchar(8) NOT NULL,
  `extra` varchar(8) NOT NULL,
  `extraext` varchar(8) NOT NULL,
  `extranoc` varchar(8) NOT NULL,
  `extraextnoc` varchar(8) NOT NULL,
  `domingo` varchar(8) NOT NULL,
  `tardanza` varchar(8) NOT NULL,
  `nacional` varchar(8) NOT NULL,
  `extranac` varchar(8) NOT NULL,
  `extranocnac` varchar(8) NOT NULL,
  `descextra1` varchar(8) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `mixtodiurna` varchar(8) NOT NULL,
  `mixtonoc` varchar(8) NOT NULL,
  `mixtoextdiurna` varchar(8) NOT NULL,
  `mixtoextnoc` varchar(8) NOT NULL,
  `dialibre` varchar(8) NOT NULL,
  `emergencia` varchar(8) NOT NULL,
  `descansoincompleto` varchar(8) NOT NULL,
  `marcacion_disp_id` varchar(20) DEFAULT NULL,
  `ent_emer` varchar(8) NOT NULL,
  `sal_emer` varchar(8) NOT NULL,
  `salida_diasiguiente` varchar(8) NOT NULL,
  `observacion` text,
  PRIMARY KEY (`id`),
  KEY `ficha` (`ficha`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reloj_encabezado`
--

CREATE TABLE IF NOT EXISTS `reloj_encabezado` (
  `cod_enca` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_reg` date NOT NULL,
  `fecha_ini` date NOT NULL,
  `fecha_fin` date NOT NULL,
  PRIMARY KEY (`cod_enca`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reloj_procesar`
--

CREATE TABLE IF NOT EXISTS `reloj_procesar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ficha` int(6) NOT NULL,
  `fecha` date NOT NULL,
  `minutos` int(10) NOT NULL,
  `concepto` varchar(60) NOT NULL,
  `id_encabezado` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Completo` (`ficha`,`fecha`,`minutos`,`concepto`,`id_encabezado`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipoempleado`
--

CREATE TABLE IF NOT EXISTS `tipoempleado` (
  `IdTipoEmpleado` int(11) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`IdTipoEmpleado`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Volcado de datos para la tabla `tipoempleado`
--

INSERT INTO `tipoempleado` (`IdTipoEmpleado`, `Descripcion`) VALUES
(1, 'CONTRATO'),
(2, 'TRANSITORIO'),
(3, 'INTERINO'),
(6, 'PERMANENTE'),
(7, 'EVENTUAL');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipolicencia`
--

CREATE TABLE IF NOT EXISTS `tipolicencia` (
  `codigo` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Volcado de datos para la tabla `tipolicencia`
--

INSERT INTO `tipolicencia` (`codigo`, `descripcion`) VALUES
(1, 'GRAVIDEZ'),
(2, 'SIN SUELDO'),
(3, 'CON SUELDO'),
(4, 'ACCIDENTES DE TRABAJO'),
(5, 'ENFERMEDAD NO PROFESIONAL');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposangre`
--

CREATE TABLE IF NOT EXISTS `tiposangre` (
  `IdTipoSangre` int(11) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(255) NOT NULL,
  PRIMARY KEY (`IdTipoSangre`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Volcado de datos para la tabla `tiposangre`
--

INSERT INTO `tiposangre` (`IdTipoSangre`, `Descripcion`) VALUES
(1, 'O+'),
(2, 'O-'),
(3, 'AB+'),
(4, 'A+'),
(5, 'B+'),
(6, 'A-'),
(7, 'A1+'),
(8, 'A2+'),
(9, 'B-');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_condicion`
--

CREATE TABLE IF NOT EXISTS `tipo_condicion` (
  `tipo` char(3) DEFAULT NULL,
  `descripcion` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tipo_condicion`
--

INSERT INTO `tipo_condicion` (`tipo`, `descripcion`) VALUES
('L', 'LICENCIA'),
('S', 'SUSPENDIDO'),
('N', 'NORMAL'),
('V', 'VACACIONES');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_justificacion`
--

CREATE TABLE IF NOT EXISTS `tipo_justificacion` (
  `idtipo` int(11) DEFAULT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `tiempo` int(11) DEFAULT NULL,
  `viewinapp` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tipo_justificacion`
--

INSERT INTO `tipo_justificacion` (`idtipo`, `descripcion`, `tiempo`, `viewinapp`) VALUES
(1, 'REINCORPORACION', 0, '1'),
(2, 'TIEMPO PERSONAL', 24, '0'),
(3, 'TIEMPO COMPENSATORIO', 40, '1'),
(4, 'MISION OFICIAL', 0, '1'),
(5, 'INCAPACIDAD', 144, '1'),
(6, 'INCAPACIDAD POR DISCAPACIDAD', 144, '1'),
(7, 'VACACIONES', 0, '1'),
(8, 'INCAPACIDAD POR FAMILIAR DISCAPACITADO', 144, '1'),
(1, 'REINCORPORACION', 0, '1'),
(2, 'TIEMPO PERSONAL', 24, '0'),
(3, 'TIEMPO COMPENSATORIO', 40, '1'),
(4, 'MISION OFICIAL', 0, '1'),
(5, 'INCAPACIDAD', 144, '1'),
(6, 'INCAPACIDAD POR DISCAPACIDAD', 144, '1'),
(7, 'VACACIONES', 0, '1'),
(8, 'INCAPACIDAD POR FAMILIAR DISCAPACITADO', 144, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_movimiento`
--

CREATE TABLE IF NOT EXISTS `tipo_movimiento` (
  `idtipo` char(3) DEFAULT NULL,
  `descripcion` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tipo_movimiento`
--

INSERT INTO `tipo_movimiento` (`idtipo`, `descripcion`) VALUES
('A', 'APOYO TEMPORAL'),
('N', 'NOMBRAMIENTO'),
('R', 'ROTACION'),
('T', 'TRASLADO'),
('A', 'APOYO TEMPORAL'),
('N', 'NOMBRAMIENTO'),
('R', 'ROTACION'),
('T', 'TRASLADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tituloinstitucional`
--

CREATE TABLE IF NOT EXISTS `tituloinstitucional` (
  `IdTituloInstitucional` varchar(11) NOT NULL,
  `Descripcion` varchar(255) NOT NULL,
  `salario` decimal(10,2) DEFAULT NULL,
  `gastos` decimal(10,2) DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `dispo` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`IdTituloInstitucional`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura para la vista `nomvis_conceptos_acumulado`
--
DROP TABLE IF EXISTS `nomvis_conceptos_acumulado`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `nomvis_conceptos_acumulado` AS select `ca`.`codcon` AS `codcon`,`ca`.`cod_tac` AS `cod_tac`,`a`.`des_tac` AS `descrip` from (`nomconceptos_acumulados` `ca` join `nomacumulados` `a` on((`ca`.`cod_tac` = `a`.`cod_tac`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `nomvis_conceptos_frecuencia`
--
DROP TABLE IF EXISTS `nomvis_conceptos_frecuencia`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `nomvis_conceptos_frecuencia` AS select `cf`.`codcon` AS `codcon`,`cf`.`codfre` AS `codfre`,`f`.`descrip` AS `descrip` from (`nomconceptos_frecuencias` `cf` join `nomfrecuencias` `f` on((`cf`.`codfre` = `f`.`codfre`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `nomvis_conceptos_situacion`
--
DROP TABLE IF EXISTS `nomvis_conceptos_situacion`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `nomvis_conceptos_situacion` AS select `cs`.`codcon` AS `codcon`,`cs`.`estado` AS `descrip`,`s`.`situacion` AS `situacion` from (`nomconceptos_situaciones` `cs` join `nomsituaciones` `s` on((`cs`.`estado` = `s`.`situacion`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `nomvis_conceptos_tiposnomina`
--
DROP TABLE IF EXISTS `nomvis_conceptos_tiposnomina`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `nomvis_conceptos_tiposnomina` AS select `ct`.`codcon` AS `codcon`,`ct`.`codtip` AS `codtip`,`n`.`descrip` AS `descrip` from (`nomconceptos_tiponomina` `ct` join `nomtipos_nomina` `n` on((`ct`.`codtip` = `n`.`codtip`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `nomvis_integrantes`
--
DROP TABLE IF EXISTS `nomvis_integrantes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `nomvis_integrantes` AS select `per`.`personal_id` AS `personal_id`,`per`.`nomposicion_id` AS `nomposicion_id`,`per`.`cedula` AS `cedula`,`per`.`ficha` AS `ficha`,`per`.`apellidos` AS `apellidos`,`per`.`nombres` AS `nombres`,`per`.`estado` AS `estado`,`tip`.`descrip` AS `descrip`,`per`.`foto` AS `foto`,`per`.`sueldopro` AS `sueldo`,`per`.`apenom` AS `apenom` from (`nomtipos_nomina` `tip` join `nompersonal` `per` on((`tip`.`codtip` = `per`.`tipnom`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `nomvis_per_movimiento`
--
DROP TABLE IF EXISTS `nomvis_per_movimiento`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `nomvis_per_movimiento` AS select `mn`.`codnom` AS `codnom`,`pe`.`tipnom` AS `tipnom`,`pe`.`foto` AS `foto`,`pe`.`fecing` AS `fec_ing`,`pe`.`cedula` AS `cedula`,`pe`.`ficha` AS `ficha`,`pe`.`apenom` AS `apenom`,`pe`.`suesal` AS `sueldopro`,`pe`.`codnivel1` AS `codnivel1`,`pe`.`codnivel2` AS `codnivel2`,`pe`.`codnivel3` AS `codnivel3`,`car`.`des_car` AS `cargo` from (((`nom_movimientos_nomina` `mn` join `nompersonal` `pe` on((`mn`.`ficha` = `pe`.`ficha`))) left join `nomcargos` `car` on((`pe`.`codcargo` = `car`.`cod_car`))) join `nomconceptos` `c` on((`c`.`codcon` = `mn`.`codcon`))) group by `pe`.`ficha`,`mn`.`codnom` order by `pe`.`apenom`;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `caa_archivos_datos`
--
ALTER TABLE `caa_archivos_datos`
  ADD CONSTRAINT `fk_caa_archivos_detalle_caa_archivos1` FOREIGN KEY (`archivo_reloj`) REFERENCES `caa_archivos_reloj` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `caa_archivos_reloj`
--
ALTER TABLE `caa_archivos_reloj`
  ADD CONSTRAINT `fk_caa_archivos_caa_configuracion1` FOREIGN KEY (`configuracion`) REFERENCES `caa_configuracion` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `caa_parametros`
--
ALTER TABLE `caa_parametros`
  ADD CONSTRAINT `fk_caa_parametros_caa_configuracion1` FOREIGN KEY (`configuracion`) REFERENCES `caa_configuracion` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `expediente`
--
ALTER TABLE `expediente`
  ADD CONSTRAINT `fk_expediente_expediente_subtipo1` FOREIGN KEY (`subtipo`) REFERENCES `expediente_subtipo` (`id_expediente_subtipo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_expediente_expediente_tipo1` FOREIGN KEY (`tipo`) REFERENCES `expediente_tipo` (`id_expediente_tipo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `expediente_adjunto`
--
ALTER TABLE `expediente_adjunto`
  ADD CONSTRAINT `fk_expediente_adjunto_expediente1` FOREIGN KEY (`expediente_cod_expediente_det`) REFERENCES `expediente` (`cod_expediente_det`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `expediente_documento`
--
ALTER TABLE `expediente_documento`
  ADD CONSTRAINT `fk_expediente_documento_expediente1` FOREIGN KEY (`expediente_cod_expediente_det`) REFERENCES `expediente` (`cod_expediente_det`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `expediente_subtipo`
--
ALTER TABLE `expediente_subtipo`
  ADD CONSTRAINT `fk_expediente_subtipo_expediente_tipo1` FOREIGN KEY (`id_expediente_tipo`) REFERENCES `expediente_tipo` (`id_expediente_tipo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `nompersonal`
--
ALTER TABLE `nompersonal`
  ADD CONSTRAINT `nompersonal_ibfk_2` FOREIGN KEY (`turno_id`) REFERENCES `nomturnos` (`turno_id`);

--
-- Filtros para la tabla `nomturnos`
--
ALTER TABLE `nomturnos`
  ADD CONSTRAINT `fk_nomturnos_nomturnos_tipo` FOREIGN KEY (`tipo`) REFERENCES `nomturnos_tipo` (`turnotipo_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `nomturnos_horarios`
--
ALTER TABLE `nomturnos_horarios`
  ADD CONSTRAINT `fk_nomturnos_horarios_nomturnos` FOREIGN KEY (`turno_id`) REFERENCES `nomturnos` (`turno_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `nomturnos_rotacion`
--
ALTER TABLE `nomturnos_rotacion`
  ADD CONSTRAINT `fk_nomturnos_rotacion_nomturnos_tipo` FOREIGN KEY (`turnotipo_id`) REFERENCES `nomturnos_tipo` (`turnotipo_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `nomturnos_rotacion_detalle`
--
ALTER TABLE `nomturnos_rotacion_detalle`
  ADD CONSTRAINT `fk_nomturnos_rotacion_detalle_nomturnos_rotacion` FOREIGN KEY (`codigo_rotacion`) REFERENCES `nomturnos_rotacion` (`codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_nomturnos_sucesor_nomturnos1` FOREIGN KEY (`turno_actual`) REFERENCES `nomturnos` (`turno_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_nomturnos_sucesor_nomturnos2` FOREIGN KEY (`turno_sucesor`) REFERENCES `nomturnos` (`turno_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
