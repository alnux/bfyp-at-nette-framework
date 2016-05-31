-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Servidor: localhost:3306
-- Tiempo de generación: 31-05-2016 a las 21:16:06
-- Versión del servidor: 5.5.42
-- Versión de PHP: 5.6.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `nette_innoDB`
--
CREATE DATABASE IF NOT EXISTS `nette_innoDB` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `nette_innoDB`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `blackip`
--

DROP TABLE IF EXISTS `blackip`;
CREATE TABLE `blackip` (
  `id` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
  `key` varchar(2) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `language`
--

INSERT INTO `language` (`key`, `name`) VALUES
('cs', 'čeština'),
('en', 'English'),
('es', 'Español');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permission`
--

DROP TABLE IF EXISTS `permission`;
CREATE TABLE `permission` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `privilege_id` int(11) DEFAULT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `access` int(11) DEFAULT NULL,
  `verification` varchar(40) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `permission`
--

INSERT INTO `permission` (`id`, `role_id`, `privilege_id`, `resource_id`, `access`, `verification`) VALUES
(1, 2, NULL, 3, 1, 'd5fe33ef4d054ad190121506fa0b40725000cfd5');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `privilege`
--

DROP TABLE IF EXISTS `privilege`;
CREATE TABLE `privilege` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `key_name` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `comment` varchar(250) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `privilege`
--

INSERT INTO `privilege` (`id`, `parent_id`, `key_name`, `name`, `comment`) VALUES
(1, NULL, 'basic_privileges', 'Basic Privileges', 'Basic privileges'),
(2, 1, 'view', 'View', 'View privilege'),
(3, 1, 'create', 'Create', 'Create privilege'),
(4, 1, 'edit', 'Edit', 'Edit privilege'),
(5, 1, 'delete', 'Delete', 'Delete privilege');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resource`
--

DROP TABLE IF EXISTS `resource`;
CREATE TABLE `resource` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `key_name` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `comment` varchar(250) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `resource`
--

INSERT INTO `resource` (`id`, `parent_id`, `key_name`, `name`, `comment`) VALUES
(1, NULL, 'group_backend', 'Group Backend', 'GROUP group_backend'),
(2, NULL, 'group_front', 'Group Front', 'GROUP group_front'),
(3, 2, 'group_front_users', 'Group Front Users', 'GROUP group_front_users'),
(4, 3, 'app_front_users_presenters_indexpresenter', 'Front Users IndexPresenter', 'RESOURCE app_front_users_presenters_indexpresenter'),
(6, 1, 'group_backend_acl', 'Group Backend Acl', 'GROUP group_backend_acl'),
(7, 6, 'app_backend_acl_presenters_useraclpresenter', 'UserAclPresenter', 'RESOURCE app_backend_acl_presenters_useraclpresenter'),
(8, 6, 'app_backend_acl_presenters_permissionpresenter', 'PermissionPresenter', 'RESOURCE app_backend_acl_presenters_permissionpresenter'),
(9, 6, 'app_backend_acl_presenters_rolepresenter', 'RolePresenter', 'RESOURCE app_backend_acl_presenters_rolepresenter'),
(10, 6, 'app_backend_acl_presenters_resourcepresenter', 'ResourcePresenter', 'RESOURCE app_backend_acl_presenters_resourcepresenter'),
(11, 6, 'app_backend_acl_presenters_privilegepresenter', 'PrivilegePresenter', 'RESOURCE app_backend_acl_presenters_privilegepresenter'),
(12, 1, 'app_backend_presenters_indexpresenter', 'Backend IndexPresenter', 'RESOURCE app_backend_presenters_indexpresenter'),
(13, 3, 'app_common_presenters_profilepresenter', 'Front Users ProfilePresenter', 'RESOURCE app_common_presenters_profilepresenter'),
(14, 1, 'group_backend_ipprotection', 'Group Backend IpProtection', 'GROUP group_backend_ipprotection'),
(15, 14, 'app_backend_ipprotection_presenters_whiteproxypresenter', 'WhiteProxyPresenter', 'RESOURCE app_backend_ipprotection_presenters_whiteproxypresenter'),
(16, 14, 'app_backend_ipprotection_presenters_blackippresenter', 'BlackIpPresenter', 'RESOURCE app_backend_ipprotection_presenters_blackippresenter'),
(17, 1, 'group_backend_appsettings', 'GROUP Backend App Settings', 'GROUP group_backend_appsettings'),
(18, 17, 'app_backend_presenters_appkeypresenter', 'AppKeyPresenter', 'RESOURCE app_backend_presenters_appkeypresenter'),
(19, 3, 'app_front_users_example_presenters_example2presenter', 'Front Users Example2Presenter', 'RESOURCE app_front_users_example_presenters_example2presenter');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `key_name` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `comment` varchar(250) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `role`
--

INSERT INTO `role` (`id`, `parent_id`, `key_name`, `name`, `comment`) VALUES
(1, NULL, 'guest', 'Guest', ' Guest users'),
(2, 1, 'authenticated', 'Authenticated', 'Authenticated users'),
(3, 2, 'administrators', 'Administrators', 'Administrators users');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(64) NOT NULL,
  `auth_key` varchar(32) DEFAULT NULL,
  `password_hash` varchar(250) NOT NULL,
  `password_reset_token` varchar(250) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `language_key` varchar(2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `status_value` smallint(6) DEFAULT NULL,
  `user_type_value` smallint(6) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `language_key`, `created_at`, `updated_at`, `status_value`, `user_type_value`) VALUES
(1, 'superadministrator', '', '$2y$10$c.BMHSZ3h2ZmrlsvQBR.j.huvO/JTZ6P2mrcYwa/1SgkfjnbJjYcu', '', 'alnux@ya.ru', 'es', '2015-03-11 16:37:02', '2015-03-11 16:37:02', 10, 3),
(2, 'alnux', '', '$2y$10$WCIKe5w4zlZHdoFHRkoK7ODn8K7TBG7rL2so7WbYqNT3nk/IHvVoq', NULL, 'alnux@yandex.com', 'es', '2015-05-14 09:41:52', '2015-05-14 09:41:52', 10, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_role`
--

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `verification` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `user_role`
--

INSERT INTO `user_role` (`user_id`, `role_id`, `verification`) VALUES
(1, 3, '5b96d4897c768b661cf7faa8b01784742059127d'),
(2, 2, 'c814635bfca146f9e63d585464a648fa8fc571c6');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `whiteproxy`
--

DROP TABLE IF EXISTS `whiteproxy`;
CREATE TABLE `whiteproxy` (
  `id` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `blackip`
--
ALTER TABLE `blackip`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `privilege_id_index` (`privilege_id`),
  ADD KEY `resource_id_index` (`resource_id`),
  ADD KEY `role_id_index` (`role_id`);

--
-- Indices de la tabla `privilege`
--
ALTER TABLE `privilege`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_name_unique` (`key_name`),
  ADD UNIQUE KEY `name_unique` (`name`),
  ADD KEY `privileges_privileges_parent_id_id` (`parent_id`);

--
-- Indices de la tabla `resource`
--
ALTER TABLE `resource`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_name_unique` (`key_name`),
  ADD UNIQUE KEY `name_unique` (`name`),
  ADD KEY `parent_id_index` (`parent_id`);

--
-- Indices de la tabla `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_name_unique` (`key_name`),
  ADD UNIQUE KEY `name_unique` (`name`),
  ADD KEY `parent_id_index` (`parent_id`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_unique` (`username`),
  ADD KEY `user_language_languagekey_key` (`language_key`);

--
-- Indices de la tabla `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `roles_id_index` (`role_id`);

--
-- Indices de la tabla `whiteproxy`
--
ALTER TABLE `whiteproxy`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `blackip`
--
ALTER TABLE `blackip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `permission`
--
ALTER TABLE `permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `privilege`
--
ALTER TABLE `privilege`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `resource`
--
ALTER TABLE `resource`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT de la tabla `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `whiteproxy`
--
ALTER TABLE `whiteproxy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
