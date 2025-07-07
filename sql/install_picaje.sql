--
-- Estructura de tabla para la tabla `llx_picaje`
--

DROP TABLE IF EXISTS `llx_picaje`;
CREATE TABLE `llx_picaje` (
  `id` int(11) NOT NULL,
  `tipo` enum('entrada','salida','vacaciones','baja','permiso','otra') NOT NULL,
  `fk_incidencia` int(11) DEFAULT NULL,
  `fk_user` int(11) NOT NULL,
  `latitud` varchar(50) DEFAULT NULL,
  `longitud` varchar(50) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `salida_manual` tinyint(1) DEFAULT 0,
  `comentario` varchar(255) DEFAULT NULL,
  `tipo_registro` varchar(50) DEFAULT 'manual',
  `entity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_picaje_incidencias`
--

DROP TABLE IF EXISTS `llx_picaje_incidencias`;
CREATE TABLE `llx_picaje_incidencias` (
  `rowid` int(11) NOT NULL,
  `fk_user` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `tipo` enum('entrada_anticipada','salida_anticipada','horas_extra','olvido_picaje','otro') DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `fk_picaje` int(11) DEFAULT NULL,
  `status` enum('pendiente','revisada','resuelta') DEFAULT 'pendiente',
  `entity` int(11) NOT NULL DEFAULT 1,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `resolucion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_picaje_resumen_envios`
--

DROP TABLE IF EXISTS `llx_picaje_resumen_envios`;
CREATE TABLE `llx_picaje_resumen_envios` (
  `rowid` int(11) NOT NULL,
  `fk_user` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `archivo_url` varchar(255) NOT NULL,
  `fecha_envio` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llx_modificacion_picaje`
--

DROP TABLE IF EXISTS `llx_modificacion_picaje`;
CREATE TABLE `llx_modificacion_picaje` (
  `id` int(11) NOT NULL,
  `picaje_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `comentario` text NOT NULL,
  `fecha_modificacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------
