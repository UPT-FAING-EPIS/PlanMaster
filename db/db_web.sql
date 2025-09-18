-- --------------------------------------------------------
-- Host:                         trolley.proxy.rlwy.net
-- Versión del servidor:         9.4.0 - MySQL Community Server - GPL
-- SO del servidor:              Linux
-- HeidiSQL Versión:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para railway
CREATE DATABASE IF NOT EXISTS `railway` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `railway`;

-- Volcando estructura para tabla railway.project_mission
CREATE TABLE IF NOT EXISTS `project_mission` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `mission_text` text NOT NULL,
  `is_completed` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_mission` (`project_id`),
  CONSTRAINT `project_mission_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.project_mission: ~3 rows (aproximadamente)
INSERT INTO `project_mission` (`id`, `project_id`, `mission_text`, `is_completed`, `created_at`, `updated_at`) VALUES
	(1, 2, 'Somos una empresa encargada de la superación de paginas web', 1, '2025-09-18 00:09:59', '2025-09-18 00:09:59'),
	(2, 5, 'fsfsf', 1, '2025-09-18 18:45:44', '2025-09-18 18:45:44'),
	(3, 6, 'Somos una empresa encargada de superación de un platillo típico de las noches turbias de examenes universitario, exactamente, las salchipapas', 1, '2025-09-18 18:48:02', '2025-09-18 18:48:02');

-- Volcando estructura para tabla railway.project_specific_objectives
CREATE TABLE IF NOT EXISTS `project_specific_objectives` (
  `id` int NOT NULL AUTO_INCREMENT,
  `strategic_objective_id` int NOT NULL,
  `objective_title` varchar(255) NOT NULL,
  `objective_description` text,
  `objective_order` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_specific_objectives` (`strategic_objective_id`,`objective_order`),
  CONSTRAINT `project_specific_objectives_ibfk_1` FOREIGN KEY (`strategic_objective_id`) REFERENCES `project_strategic_objectives` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.project_specific_objectives: ~0 rows (aproximadamente)

-- Volcando estructura para tabla railway.project_strategic_objectives
CREATE TABLE IF NOT EXISTS `project_strategic_objectives` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `objective_title` varchar(255) NOT NULL,
  `objective_description` text,
  `objective_order` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_project_objectives` (`project_id`,`objective_order`),
  CONSTRAINT `project_strategic_objectives_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.project_strategic_objectives: ~0 rows (aproximadamente)

-- Volcando estructura para tabla railway.project_values
CREATE TABLE IF NOT EXISTS `project_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `value_text` varchar(255) NOT NULL,
  `value_order` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_project_values` (`project_id`,`value_order`),
  CONSTRAINT `project_values_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.project_values: ~0 rows (aproximadamente)
INSERT INTO `project_values` (`id`, `project_id`, `value_text`, `value_order`, `created_at`, `updated_at`) VALUES
	(1, 6, 'Integridad', 1, '2025-09-18 18:48:55', '2025-09-18 18:48:55'),
	(2, 6, 'Compromiso', 2, '2025-09-18 18:48:55', '2025-09-18 18:48:55'),
	(3, 6, 'Innovación', 3, '2025-09-18 18:48:55', '2025-09-18 18:48:55');

-- Volcando estructura para tabla railway.project_vision
CREATE TABLE IF NOT EXISTS `project_vision` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `vision_text` text NOT NULL,
  `is_completed` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_vision` (`project_id`),
  CONSTRAINT `project_vision_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.project_vision: ~1 rows (aproximadamente)
INSERT INTO `project_vision` (`id`, `project_id`, `vision_text`, `is_completed`, `created_at`, `updated_at`) VALUES
	(1, 6, 'Ser reconocidos en 2027, como la mejor salchipaperia de Tacna', 1, '2025-09-18 18:48:30', '2025-09-18 18:48:30');

-- Volcando estructura para tabla railway.strategic_projects
CREATE TABLE IF NOT EXISTS `strategic_projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  `status` enum('draft','in_progress','completed') DEFAULT 'draft',
  `progress_percentage` decimal(5,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `strategic_projects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.strategic_projects: ~6 rows (aproximadamente)
INSERT INTO `strategic_projects` (`id`, `user_id`, `project_name`, `company_name`, `created_at`, `updated_at`, `completed_at`, `status`, `progress_percentage`) VALUES
	(1, 7, 'dafafaffafaf', 'fafafafaf', '2025-09-17 23:55:51', '2025-09-17 23:55:51', NULL, 'in_progress', NULL),
	(2, 2, 'Plan de Superación de Caida de ventas', 'CAPICODEX', '2025-09-18 00:08:52', '2025-09-18 00:08:52', NULL, 'in_progress', NULL),
	(3, 6, 'proyecto 1', 'proyecto 1', '2025-09-18 18:38:21', '2025-09-18 18:38:21', NULL, 'in_progress', 0.00),
	(4, 2, 'PLAN PARA AUMENTO DE VENTAS DE LA SALCHIPAPERIA DE VICTOR', 'SALCHIPAPEANDO CON VICTOR', '2025-09-18 18:43:36', '2025-09-18 18:43:36', NULL, 'in_progress', NULL),
	(5, 6, 'lk;lk;lk;l', 'kkkkk', '2025-09-18 18:45:17', '2025-09-18 18:45:17', NULL, 'in_progress', NULL),
	(6, 2, 'PLAN ESTRATEGICO PARA AUMENTO DE VENTAS DE LA SALCHIPAPERIA DE VICTOR', 'SALCHIPAPEANDO CON VICTOR', '2025-09-18 18:45:41', '2025-09-18 18:45:41', NULL, 'in_progress', NULL),
	(7, 10, 'dadawdawdadwadaw', 'dwdawdawdawd', '2025-09-18 19:05:32', '2025-09-18 19:05:32', NULL, 'in_progress', NULL);

-- Volcando estructura para tabla railway.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `avatar` varchar(500) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT '0',
  `verification_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `google_id` (`google_id`),
  KEY `idx_users_email` (`email`),
  KEY `idx_users_google_id` (`google_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.users: ~8 rows (aproximadamente)
INSERT INTO `users` (`id`, `email`, `password`, `name`, `avatar`, `google_id`, `email_verified`, `verification_token`, `reset_token`, `reset_token_expires`, `created_at`, `updated_at`, `last_login`, `status`) VALUES
	(1, 'admin@planmaster.com', '$2y$10$rCgRXCL8EfE5IUvYwLBVN.6wxPoSCS9QZUTnULXwT2cH4SCcrJ9U.', 'Administrador PlanMaster', NULL, NULL, 1, NULL, NULL, NULL, '2025-09-11 19:04:33', '2025-09-11 23:02:26', NULL, 'active'),
	(2, 'fuentessebastiansa4s@gmail.com', NULL, 'Sebastian Fuentes', 'https://lh3.googleusercontent.com/a/ACg8ocLfEswYK_9p-rBZuBQE7S8VeDn8_qMdo6rVjf2vCrLvDkU9CxBg=s96-c', '118266572871877651902', 1, NULL, NULL, NULL, '2025-09-11 23:08:36', '2025-09-18 18:59:08', '2025-09-18 18:59:08', 'active'),
	(3, 'gg2022074263@virtual.upt.pe', NULL, 'GABRIELA LUZKALID GUTIERREZ MAMANI', 'https://lh3.googleusercontent.com/a/ACg8ocJjxREiRM1D_ZSObeuGt0bZFHXkv4mdBwUTp_BHwvPgg_IZxVpr=s96-c', '115944247263508584295', 1, NULL, NULL, NULL, '2025-09-11 23:10:45', '2025-09-17 23:14:49', '2025-09-17 23:14:49', 'active'),
	(4, 'chevichin2018@gmail.com', '$2y$10$TTSoSkzIGip9IATplJuHy.6Yd7WSb9vDIbU4Cu6B3Uniao05mJ3nC', 'Chebastian Ricolas', NULL, NULL, 1, NULL, NULL, NULL, '2025-09-11 23:58:53', '2025-09-11 23:59:29', '2025-09-11 23:59:29', 'active'),
	(5, 'victoraprendiendocon@gmail.com', NULL, 'Aprendiendo con Victor', 'https://lh3.googleusercontent.com/a/ACg8ocITzx8cXQonIajDFmHtppjavUgNFl2YqzWyXUmeGAps1M3WM7Q=s96-c', '115289334880461933766', 1, NULL, NULL, NULL, '2025-09-12 00:04:32', '2025-09-12 00:04:32', '2025-09-12 00:04:32', 'active'),
	(6, 'nkmelndz@gmail.com', '$2y$10$CZxOAbvuR47a/5rfZ/zqL.TTC4msAvG1WNF.CeLGXKGTPhPlOyQQ.', 'nikolas', NULL, NULL, 1, NULL, NULL, NULL, '2025-09-17 21:59:04', '2025-09-18 18:45:02', '2025-09-18 18:45:02', 'active'),
	(7, 'sf2022073902@virtual.upt.pe', NULL, 'SEBASTIAN NICOLAS FUENTES AVALOS', 'https://lh3.googleusercontent.com/a/ACg8ocIldVbBQckiP7rwOIKiNWrDyrMX8yoUr2wjceuxppk4ahCQpm0=s96-c', '118030351119923353936', 1, NULL, NULL, NULL, '2025-09-17 21:59:09', '2025-09-18 19:00:55', '2025-09-18 19:00:55', 'active'),
	(8, 'ferquatck@gmail.com', NULL, 'fer ,', 'https://lh3.googleusercontent.com/a/ACg8ocJwB9Y4ST5t74ag0w5PyB7qshajRj4NsO-1HvO7QsUIOizrBg=s96-c', '108307062242127529441', 1, NULL, NULL, NULL, '2025-09-17 22:01:35', '2025-09-17 22:01:35', '2025-09-17 22:01:35', 'active'),
	(9, 'cescamac@upt.pe', '$2y$10$KRSRaJ0qScKBdlIBKwpBwukDiVHkbC7FlEOcCdXF4QGBCjs6quv5e', 'cesar camac', NULL, NULL, 1, NULL, NULL, NULL, '2025-09-18 04:08:08', '2025-09-18 04:08:15', '2025-09-18 04:08:15', 'active'),
	(10, 'gagaga@email.com', '$2y$10$S58/gIoNoC9ruw9dia59sOpduIAYei2QBiNMEMwwuoyG33aV.UkdW', 'gagaga', NULL, NULL, 1, NULL, NULL, NULL, '2025-09-18 19:05:09', '2025-09-18 19:05:18', '2025-09-18 19:05:18', 'active');

-- Volcando estructura para tabla railway.user_sessions
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` int NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sessions_user_id` (`user_id`),
  KEY `idx_sessions_expires` (`expires_at`),
  CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.user_sessions: ~0 rows (aproximadamente)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
