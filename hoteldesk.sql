-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-04-2026 a las 06:46:39
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
-- Base de datos: `hoteldesk`
--
CREATE DATABASE IF NOT EXISTS `hoteldesk` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `hoteldesk`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hotels`
--

DROP TABLE IF EXISTS `hotels`;
CREATE TABLE `hotels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `pin_hash` varchar(255) NOT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `service_point_url` varchar(255) DEFAULT NULL,
  `status` enum('draft','active','paused','disabled') NOT NULL DEFAULT 'draft',
  `public_requests_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `panel_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `taxi_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `primary_color` varchar(20) DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `slug`, `pin_hash`, `logo_path`, `phone`, `email`, `address`, `service_point_url`, `status`, `public_requests_enabled`, `panel_enabled`, `taxi_enabled`, `primary_color`, `settings`, `created_at`, `updated_at`) VALUES
(1, 'Hotel La Central', 'la-central', '$2y$12$g70jKp/QP2uShbIOblJkteu0sb92JixUmnQUhOzegJtzxM1wDwjt6', 'hotels/1/AQQpxZQnbH9pQysSkCQk5ie6qZhVezk5B9mfrJTh.png', NULL, NULL, NULL, NULL, 'active', 1, 1, 1, '#0F6CBD', NULL, '2026-04-24 05:05:14', '2026-04-25 05:36:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hotel_pin_reset_requests`
--

DROP TABLE IF EXISTS `hotel_pin_reset_requests`;
CREATE TABLE `hotel_pin_reset_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hotel_id` bigint(20) UNSIGNED NOT NULL,
  `requester_name` varchar(120) DEFAULT NULL,
  `requester_phone` varchar(40) DEFAULT NULL,
  `note` varchar(500) DEFAULT NULL,
  `status` enum('pending','completed','rejected','canceled') NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reject_reason` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `hotel_pin_reset_requests`
--

INSERT INTO `hotel_pin_reset_requests` (`id`, `hotel_id`, `requester_name`, `requester_phone`, `note`, `status`, `reviewed_by`, `reviewed_at`, `reject_reason`, `created_at`, `updated_at`) VALUES
(1, 1, 'recepcion', NULL, 'olvidsamos el pin', 'completed', 1, '2026-04-25 05:23:25', NULL, '2026-04-25 05:23:07', '2026-04-25 05:23:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hotel_qr_creation_requests`
--

DROP TABLE IF EXISTS `hotel_qr_creation_requests`;
CREATE TABLE `hotel_qr_creation_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hotel_id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(120) NOT NULL,
  `type` enum('room','lobby','area','restaurant','parking','reception','other') NOT NULL DEFAULT 'other',
  `floor` varchar(30) DEFAULT NULL,
  `mode` enum('menu','limited','direct') NOT NULL DEFAULT 'menu',
  `fixed_request_type` varchar(50) DEFAULT NULL,
  `allowed_request_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allowed_request_types`)),
  `note` varchar(500) DEFAULT NULL,
  `status` enum('pending','approved','rejected','canceled') NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reject_reason` varchar(500) DEFAULT NULL,
  `created_qr_point_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `hotel_qr_creation_requests`
--

INSERT INTO `hotel_qr_creation_requests` (`id`, `hotel_id`, `label`, `type`, `floor`, `mode`, `fixed_request_type`, `allowed_request_types`, `note`, `status`, `reviewed_by`, `reviewed_at`, `reject_reason`, `created_qr_point_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'gimnasio', 'area', '1', 'direct', 'towels', NULL, NULL, 'approved', 1, '2026-04-25 05:16:25', NULL, 25, '2026-04-25 05:16:15', '2026-04-25 05:16:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hotel_qr_points`
--

DROP TABLE IF EXISTS `hotel_qr_points`;
CREATE TABLE `hotel_qr_points` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hotel_id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(120) NOT NULL,
  `type` enum('room','lobby','area','restaurant','parking','reception','other') NOT NULL DEFAULT 'room',
  `floor` varchar(30) DEFAULT NULL,
  `public_code` varchar(40) NOT NULL,
  `mode` enum('menu','limited','direct') NOT NULL DEFAULT 'menu',
  `fixed_request_type` varchar(50) DEFAULT NULL,
  `allowed_request_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allowed_request_types`)),
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `hotel_qr_points`
--

INSERT INTO `hotel_qr_points` (`id`, `hotel_id`, `label`, `type`, `floor`, `public_code`, `mode`, `fixed_request_type`, `allowed_request_types`, `active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Habitación 101', 'room', '1', 'LC101A8K2', 'menu', NULL, NULL, 1, '2026-04-24 05:05:22', '2026-04-24 05:05:22'),
(2, 1, 'Habitación 102', 'room', '1', 'LC102B7M4', 'menu', NULL, NULL, 1, '2026-04-24 05:05:22', '2026-04-24 05:05:22'),
(3, 1, 'Lobby', 'lobby', NULL, 'LCLOBBY01', 'limited', NULL, '[\"taxi\",\"luggage\",\"other\"]', 1, '2026-04-24 05:05:22', '2026-04-24 05:05:22'),
(4, 1, 'Alberca', 'area', NULL, 'LCPOOL01', 'limited', NULL, '[\"towels\",\"amenity\",\"maintenance\",\"other\"]', 1, '2026-04-24 05:05:22', '2026-04-24 05:05:22'),
(5, 1, 'Habitacion 201', 'room', '2', 'HD1YA94H7OE', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(6, 1, 'Habitacion 202', 'room', '2', 'HD1SXGZ84I0', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(7, 1, 'Habitacion 203', 'room', '2', 'HD1NPN03VZA', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(8, 1, 'Habitacion 204', 'room', '2', 'HD13FV83WEV', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(9, 1, 'Habitacion 205', 'room', '2', 'HD1I2YSXIJK', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(10, 1, 'Habitacion 206', 'room', '2', 'HD18GTKSK97', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(11, 1, 'Habitacion 207', 'room', '2', 'HD1HEBWQLXG', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(12, 1, 'Habitacion 208', 'room', '2', 'HD1ZH3AOJKK', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(13, 1, 'Habitacion 209', 'room', '2', 'HD19EBSOEMQ', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(14, 1, 'Habitacion 210', 'room', '2', 'HD1PS7N1YYQ', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(15, 1, 'Habitacion 211', 'room', '2', 'HD1N777Y5MB', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(16, 1, 'Habitacion 212', 'room', '2', 'HD17TZ1WE99', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(17, 1, 'Habitacion 213', 'room', '2', 'HD1XYVSUSN6', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(18, 1, 'Habitacion 214', 'room', '2', 'HD1OEDNC0JD', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(19, 1, 'Habitacion 215', 'room', '2', 'HD1TASXADY5', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(20, 1, 'Habitacion 216', 'room', '2', 'HD1XQFIA92G', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(21, 1, 'Habitacion 217', 'room', '2', 'HD1FIWWJDKV', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(22, 1, 'Habitacion 218', 'room', '2', 'HD1ME3IB3HA', 'menu', NULL, NULL, 1, '2026-04-25 03:10:13', '2026-04-25 03:10:13'),
(23, 1, 'Habitacion 219', 'room', '2', 'HD1ZXODPW6H', 'menu', NULL, NULL, 1, '2026-04-25 03:10:14', '2026-04-25 03:10:14'),
(24, 1, 'Habitacion 220', 'room', '2', 'HD1IWUXHNCJ', 'menu', NULL, NULL, 1, '2026-04-25 03:10:14', '2026-04-25 03:10:14'),
(25, 1, 'gimnasio', 'area', '1', 'HD1SCJGNQNX', 'direct', 'towels', NULL, 1, '2026-04-25 05:16:25', '2026-04-25 05:16:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hotel_requests`
--

DROP TABLE IF EXISTS `hotel_requests`;
CREATE TABLE `hotel_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hotel_id` bigint(20) UNSIGNED NOT NULL,
  `hotel_qr_point_id` bigint(20) UNSIGNED DEFAULT NULL,
  `point_label` varchar(120) NOT NULL,
  `type_key` varchar(50) NOT NULL,
  `title` varchar(120) NOT NULL,
  `note` varchar(500) DEFAULT NULL,
  `status` enum('pending','in_progress','completed','canceled') NOT NULL DEFAULT 'pending',
  `source` enum('qr','panel','admin') NOT NULL DEFAULT 'qr',
  `guest_name` varchar(120) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `taken_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `canceled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `hotel_requests`
--

INSERT INTO `hotel_requests` (`id`, `hotel_id`, `hotel_qr_point_id`, `point_label`, `type_key`, `title`, `note`, `status`, `source`, `guest_name`, `ip_address`, `user_agent`, `taken_at`, `completed_at`, `canceled_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Habitación 101', 'towels', 'Toallas', NULL, 'completed', 'qr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-25 01:45:47', '2026-04-25 01:45:49', NULL, '2026-04-25 01:45:27', '2026-04-25 01:45:49'),
(2, 1, 1, 'Habitación 101', 'towels', 'Toallas', NULL, 'completed', 'qr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '2026-04-25 01:46:37', NULL, '2026-04-25 01:45:57', '2026-04-25 01:46:37'),
(3, 1, 1, 'Habitación 101', 'wakeup', 'Despertador', 'me pueden despertar a las 7 am  porfavor', 'completed', 'qr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '2026-04-25 01:46:39', NULL, '2026-04-25 01:46:16', '2026-04-25 01:46:39'),
(4, 1, 1, 'Habitación 101', 'other', 'Otro', 'me pueden despertar a las 7 am  porfavor', 'completed', 'qr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '2026-04-25 01:47:20', NULL, '2026-04-25 01:46:55', '2026-04-25 01:47:20'),
(5, 1, 1, 'Habitación 101', 'other', 'Otro', 'me pueden despertar a las 7 am  porfavor', 'completed', 'qr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '2026-04-25 02:45:55', NULL, '2026-04-25 01:49:18', '2026-04-25 02:45:55'),
(6, 1, 1, 'Habitación 101', 'cleaning', 'Limpieza', NULL, 'completed', 'qr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '2026-04-25 02:46:14', NULL, '2026-04-25 02:46:04', '2026-04-25 02:46:14'),
(7, 1, 1, 'Habitación 101', 'towels', 'Toallas', NULL, 'completed', 'qr', 'hola', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '2026-04-25 02:47:13', NULL, '2026-04-25 02:46:53', '2026-04-25 02:47:13'),
(8, 1, 1, 'Habitación 101', 'wakeup', 'Despertador', NULL, 'canceled', 'qr', 'hola', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, NULL, '2026-04-24 21:02:24', '2026-04-25 02:47:20', '2026-04-24 21:02:24'),
(9, 1, 1, 'Habitación 101', 'wakeup', 'Despertador', NULL, 'canceled', 'qr', 'hola', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, NULL, '2026-04-24 21:02:24', '2026-04-25 02:50:14', '2026-04-24 21:02:24'),
(10, 1, 1, 'Habitación 101', 'wakeup', 'Despertador', NULL, 'canceled', 'qr', 'hola', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, NULL, '2026-04-24 21:02:24', '2026-04-25 02:50:44', '2026-04-24 21:02:24'),
(11, 1, 1, 'Habitación 101', 'wakeup', 'Despertador', NULL, 'canceled', 'qr', 'hola', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, NULL, '2026-04-24 21:02:24', '2026-04-25 02:51:28', '2026-04-24 21:02:24'),
(12, 1, 1, 'Habitación 101', 'wakeup', 'Despertador', NULL, 'canceled', 'qr', 'hola', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, NULL, '2026-04-24 21:02:24', '2026-04-25 02:54:35', '2026-04-24 21:02:24'),
(13, 1, 1, 'Habitación 101', 'wakeup', 'Despertador', NULL, 'canceled', 'qr', 'hola', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, NULL, '2026-04-24 21:02:24', '2026-04-25 02:54:59', '2026-04-24 21:02:24'),
(14, 1, 1, 'Habitación 101', 'wakeup', 'Despertador', NULL, 'completed', 'qr', 'hola', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '2026-04-25 03:04:21', NULL, '2026-04-25 02:56:37', '2026-04-25 03:04:21'),
(15, 1, 2, 'Habitación 102', 'towels', 'Toallas', NULL, 'completed', 'qr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '2026-04-25 03:04:24', NULL, '2026-04-25 02:57:13', '2026-04-25 03:04:24'),
(16, 1, 2, 'Habitación 102', 'towels', 'Toallas', NULL, 'completed', 'qr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '2026-04-25 03:04:24', NULL, '2026-04-25 03:02:54', '2026-04-25 03:04:24'),
(17, 1, 2, 'Habitación 102', 'towels', 'Toallas', NULL, 'completed', 'qr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-25 03:04:50', '2026-04-25 03:04:58', NULL, '2026-04-25 03:04:40', '2026-04-25 03:04:58'),
(18, 1, 23, 'Habitacion 219', 'maintenance', 'Mantenimiento', NULL, 'completed', 'qr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '2026-04-25 03:10:49', NULL, '2026-04-25 03:10:38', '2026-04-25 03:10:49'),
(19, 1, 5, 'Habitacion 201', 'towels', 'Toallas', NULL, 'completed', 'qr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-25 03:11:24', '2026-04-25 03:11:31', NULL, '2026-04-25 03:11:13', '2026-04-25 03:11:31'),
(20, 1, 23, 'Habitacion 219', 'taxi', 'Taxi', NULL, 'completed', 'qr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '2026-04-25 07:50:33', NULL, '2026-04-25 07:50:06', '2026-04-25 07:50:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('q2Hhxhnjo292lvudVzCgcX2KfT58dRrmxdmGHW8I', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQWI4czVWOVNTeGt3TXdBY25HaklrMDVhcW9LNzYwVjJQRDJGMlZYWiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1777058224),
('tlAwxPyFD8pNMYzNX53hpyE8ye8QxSHOQ06ybL1C', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSGpRbXVSSGY1OGVzTkFST1B6UjFUMjMxbW1VdUJQaXhqaDZid2RBSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9oL2xhLWNlbnRyYWwvcmVxdWVzdHMvZmVlZCI7czo1OiJyb3V0ZSI7czoxOToiaG90ZWwucmVxdWVzdHMuZmVlZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6OToiaG90ZWxkZXNrIjthOjI6e3M6NToiaG90ZWwiO2E6MTp7aToxO2E6MTp7czoxMzoiYXV0aGVudGljYXRlZCI7YjoxO319czo2OiJzeXNhcHAiO2E6NDp7czoxMzoiYXV0aGVudGljYXRlZCI7YjoxO3M6ODoiYWRtaW5faWQiO2k6MTtzOjEwOiJhZG1pbl9uYW1lIjtzOjY6Ikpvc2h1ZSI7czoxMDoiYWRtaW5fcm9sZSI7czo1OiJvd25lciI7fX19', 1777092399),
('X8QVXPbrnyZQDJsKUicUocAw6ttGzK38JfhK3Ufr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMTU1VTBqb1NzcmZkSVJHdlFidzFQZmtEYXk3ZUp1Y1BvczR0eVRHaCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9oL2xhLWNlbnRyYWwvcmVxdWVzdHMvZmVlZCI7czo1OiJyb3V0ZSI7czoxOToiaG90ZWwucmVxdWVzdHMuZmVlZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6OToiaG90ZWxkZXNrIjthOjI6e3M6NToiaG90ZWwiO2E6MTp7aToxO2E6MTp7czoxMzoiYXV0aGVudGljYXRlZCI7YjoxO319czo2OiJzeXNhcHAiO2E6MTp7czoxMzoiYXV0aGVudGljYXRlZCI7YjoxO319fQ==', 1777070319);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sysapp_admins`
--

DROP TABLE IF EXISTS `sysapp_admins`;
CREATE TABLE `sysapp_admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('owner','admin','support') NOT NULL DEFAULT 'admin',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `failed_attempts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sysapp_admins`
--

INSERT INTO `sysapp_admins` (`id`, `name`, `email`, `password_hash`, `role`, `active`, `failed_attempts`, `locked_until`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'Joshue', 'admin@sysapp.local', '$2y$12$VDtptvLSFDpxxdijGSw0Y./0j77sN4LABt1TaTCAVhDIoudRGn7hG', 'owner', 1, 0, NULL, '2026-04-25 04:38:44', '2026-04-25 04:35:44', '2026-04-25 04:38:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sysapp_audit_logs`
--

DROP TABLE IF EXISTS `sysapp_audit_logs`;
CREATE TABLE `sysapp_audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `hotel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(120) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sysapp_audit_logs`
--

INSERT INTO `sysapp_audit_logs` (`id`, `admin_id`, `hotel_id`, `action`, `description`, `ip_address`, `user_agent`, `meta`, `created_at`) VALUES
(1, 1, NULL, 'sysapp_login_success', 'Inicio de sesión correcto.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '{\"email\":\"admin@sysapp.local\"}', '2026-04-25 04:38:44'),
(2, 1, 1, 'hotel_qr_request_approved', 'Solicitud de QR aprobada.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '{\"qr_request_id\":1,\"created_qr_point_id\":25,\"label\":\"gimnasio\"}', '2026-04-25 05:16:25'),
(3, 1, 1, 'hotel_pin_reset_completed', 'PIN de hotel restablecido por SysApp.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '{\"pin_reset_request_id\":1,\"hotel_slug\":\"la-central\"}', '2026-04-25 05:23:25');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hotels_slug_unique` (`slug`),
  ADD KEY `hotels_status_index` (`status`);

--
-- Indices de la tabla `hotel_pin_reset_requests`
--
ALTER TABLE `hotel_pin_reset_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_pin_reset_requests_hotel_id_index` (`hotel_id`),
  ADD KEY `hotel_pin_reset_requests_status_index` (`status`),
  ADD KEY `hotel_pin_reset_requests_reviewer_foreign` (`reviewed_by`);

--
-- Indices de la tabla `hotel_qr_creation_requests`
--
ALTER TABLE `hotel_qr_creation_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_qr_creation_requests_hotel_id_index` (`hotel_id`),
  ADD KEY `hotel_qr_creation_requests_status_index` (`status`),
  ADD KEY `hotel_qr_creation_requests_created_point_foreign` (`created_qr_point_id`),
  ADD KEY `hotel_qr_creation_requests_reviewer_foreign` (`reviewed_by`);

--
-- Indices de la tabla `hotel_qr_points`
--
ALTER TABLE `hotel_qr_points`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hotel_qr_points_public_code_unique` (`public_code`),
  ADD KEY `hotel_qr_points_hotel_id_index` (`hotel_id`),
  ADD KEY `hotel_qr_points_type_index` (`type`),
  ADD KEY `hotel_qr_points_active_index` (`active`);

--
-- Indices de la tabla `hotel_requests`
--
ALTER TABLE `hotel_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_requests_hotel_id_index` (`hotel_id`),
  ADD KEY `hotel_requests_qr_point_id_index` (`hotel_qr_point_id`),
  ADD KEY `hotel_requests_status_index` (`status`),
  ADD KEY `hotel_requests_type_key_index` (`type_key`),
  ADD KEY `hotel_requests_created_at_index` (`created_at`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `sysapp_admins`
--
ALTER TABLE `sysapp_admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sysapp_admins_email_unique` (`email`),
  ADD KEY `sysapp_admins_active_index` (`active`),
  ADD KEY `sysapp_admins_role_index` (`role`);

--
-- Indices de la tabla `sysapp_audit_logs`
--
ALTER TABLE `sysapp_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sysapp_audit_logs_admin_id_index` (`admin_id`),
  ADD KEY `sysapp_audit_logs_hotel_id_index` (`hotel_id`),
  ADD KEY `sysapp_audit_logs_action_index` (`action`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `hotel_pin_reset_requests`
--
ALTER TABLE `hotel_pin_reset_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `hotel_qr_creation_requests`
--
ALTER TABLE `hotel_qr_creation_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `hotel_qr_points`
--
ALTER TABLE `hotel_qr_points`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `hotel_requests`
--
ALTER TABLE `hotel_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `sysapp_admins`
--
ALTER TABLE `sysapp_admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sysapp_audit_logs`
--
ALTER TABLE `sysapp_audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `hotel_pin_reset_requests`
--
ALTER TABLE `hotel_pin_reset_requests`
  ADD CONSTRAINT `hotel_pin_reset_requests_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hotel_pin_reset_requests_reviewer_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `sysapp_admins` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `hotel_qr_creation_requests`
--
ALTER TABLE `hotel_qr_creation_requests`
  ADD CONSTRAINT `hotel_qr_creation_requests_created_point_foreign` FOREIGN KEY (`created_qr_point_id`) REFERENCES `hotel_qr_points` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hotel_qr_creation_requests_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hotel_qr_creation_requests_reviewer_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `sysapp_admins` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `hotel_qr_points`
--
ALTER TABLE `hotel_qr_points`
  ADD CONSTRAINT `hotel_qr_points_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `hotel_requests`
--
ALTER TABLE `hotel_requests`
  ADD CONSTRAINT `hotel_requests_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hotel_requests_qr_point_id_foreign` FOREIGN KEY (`hotel_qr_point_id`) REFERENCES `hotel_qr_points` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `sysapp_audit_logs`
--
ALTER TABLE `sysapp_audit_logs`
  ADD CONSTRAINT `sysapp_audit_logs_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `sysapp_admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sysapp_audit_logs_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
