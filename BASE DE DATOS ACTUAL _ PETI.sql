-- --------------------------------------------------------
-- Host:                         nozomi.proxy.rlwy.net
-- Versión del servidor:         9.4.0 - MySQL Community Server - GPL
-- SO del servidor:              Linux
-- HeidiSQL Versión:             12.4.0.6659
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

-- Volcando estructura para tabla railway.project_bcg_analysis
CREATE TABLE IF NOT EXISTS `project_bcg_analysis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_bcg` (`project_id`),
  CONSTRAINT `project_bcg_analysis_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.project_bcg_analysis: ~0 rows (aproximadamente)

-- Volcando estructura para tabla railway.project_bcg_competitors
CREATE TABLE IF NOT EXISTS `project_bcg_competitors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `product_id` int NOT NULL,
  `competitor_name` varchar(255) NOT NULL,
  `competitor_sales` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_max_competitor` tinyint(1) DEFAULT '0',
  `competitor_order` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_product_id` (`product_id`),
  CONSTRAINT `project_bcg_competitors_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_bcg_competitors_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `project_bcg_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.project_bcg_competitors: ~0 rows (aproximadamente)

-- Volcando estructura para tabla railway.project_bcg_market_evolution
CREATE TABLE IF NOT EXISTS `project_bcg_market_evolution` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `product_id` int NOT NULL,
  `period_start_year` int NOT NULL,
  `period_end_year` int NOT NULL,
  `tcm_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `period_order` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_period` (`product_id`,`period_start_year`,`period_end_year`),
  KEY `idx_project_id` (`project_id`),
  KEY `idx_product_id` (`product_id`),
  CONSTRAINT `project_bcg_market_evolution_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_bcg_market_evolution_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `project_bcg_products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.project_bcg_market_evolution: ~0 rows (aproximadamente)

-- Volcando estructura para tabla railway.project_bcg_products
CREATE TABLE IF NOT EXISTS `project_bcg_products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `sales_forecast` decimal(15,2) NOT NULL DEFAULT '0.00',
  `sales_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tcm_calculated` decimal(5,2) NOT NULL DEFAULT '0.00',
  `prm_calculated` decimal(5,2) NOT NULL DEFAULT '0.00',
  `bcg_position` varchar(20) DEFAULT NULL,
  `product_order` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_project_id` (`project_id`),
  CONSTRAINT `project_bcg_products_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.project_bcg_products: ~0 rows (aproximadamente)

-- Volcando estructura para tabla railway.project_foda_analysis
CREATE TABLE IF NOT EXISTS `project_foda_analysis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `type` enum('oportunidad','amenaza','fortaleza','debilidad') COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_order` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_project_foda` (`project_id`,`type`,`item_order`),
  CONSTRAINT `project_foda_analysis_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla railway.project_foda_analysis: ~12 rows (aproximadamente)
INSERT INTO `project_foda_analysis` (`id`, `project_id`, `type`, `item_text`, `item_order`, `created_at`, `updated_at`) VALUES
	(61, 8, 'oportunidad', 'Mercados Emergentes en Crecimiento', 1, '2025-10-01 22:32:15', '2025-10-01 22:32:15'),
	(62, 8, 'oportunidad', 'Transformación Digital Global', 2, '2025-10-01 22:32:15', '2025-10-01 22:32:15'),
	(63, 8, 'oportunidad', 'Demanda de IA Accesible', 3, '2025-10-01 22:32:15', '2025-10-01 22:32:15'),
	(64, 8, 'amenaza', 'Regulación Intensificada', 1, '2025-10-01 22:32:15', '2025-10-01 22:32:15'),
	(65, 8, 'amenaza', 'Competencia Agresiva', 2, '2025-10-01 22:32:15', '2025-10-01 22:32:15'),
	(66, 8, 'amenaza', 'Ciberseguridad y Amenazas Digitales', 3, '2025-10-01 22:32:15', '2025-10-01 22:32:15'),
	(67, 8, 'fortaleza', 'Liderazgo Tecnológico Global', 1, '2025-10-01 22:32:15', '2025-10-01 22:32:15'),
	(68, 8, 'fortaleza', 'Recursos Financieros Sólidos', 2, '2025-10-01 22:32:15', '2025-10-01 22:32:15'),
	(69, 8, 'fortaleza', 'Ecosistema de Productos Integrado', 3, '2025-10-01 22:32:15', '2025-10-01 22:32:15'),
	(70, 8, 'debilidad', 'Percepción de Monopolio', 1, '2025-10-01 22:32:15', '2025-10-01 22:32:15'),
	(71, 8, 'debilidad', 'Complejidad Organizacional', 2, '2025-10-01 22:32:15', '2025-10-01 22:32:15'),
	(72, 8, 'debilidad', 'Dependencia de Publicidad', 3, '2025-10-01 22:32:15', '2025-10-01 22:32:15');

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.project_mission: ~4 rows (aproximadamente)
INSERT INTO `project_mission` (`id`, `project_id`, `mission_text`, `is_completed`, `created_at`, `updated_at`) VALUES
	(1, 2, 'Somos una empresa encargada de la superación de paginas web', 1, '2025-09-18 00:09:59', '2025-09-18 00:09:59'),
	(2, 5, 'fsfsf', 1, '2025-09-18 18:45:44', '2025-09-18 18:45:44'),
	(3, 6, 'Somos una empresa encargada de superación de un platillo típico de las noches turbias de examenes universitario, exactamente, las salchipapas', 1, '2025-09-18 18:48:02', '2025-09-18 18:48:02'),
	(4, 8, 'ggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggsssssssssssssssssssssssssssssssssssss', 1, '2025-10-01 22:24:04', '2025-10-01 22:24:04');

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.project_specific_objectives: ~12 rows (aproximadamente)
INSERT INTO `project_specific_objectives` (`id`, `strategic_objective_id`, `objective_title`, `objective_description`, `objective_order`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Conectividad Global', '', 1, '2025-10-01 22:28:47', '2025-10-01 22:28:47'),
	(2, 1, 'Dispositivos Accesibles', '', 2, '2025-10-01 22:28:47', '2025-10-01 22:28:47'),
	(3, 2, 'Alfabetización Digital', '', 1, '2025-10-01 22:28:47', '2025-10-01 22:28:47'),
	(4, 2, 'Infraestructura Educativa', '', 2, '2025-10-01 22:28:47', '2025-10-01 22:28:47'),
	(5, 3, 'Gobernanza de IA', '', 1, '2025-10-01 22:28:47', '2025-10-01 22:28:47'),
	(6, 3, 'IA para el Bien Social', '', 2, '2025-10-01 22:28:47', '2025-10-01 22:28:47'),
	(7, 4, 'A continuación reflexione sobre la misión, visión y valores definidos y establezca los objetivos estratégicos y específi', '', 1, '2025-10-22 22:40:20', '2025-10-22 22:40:20'),
	(8, 4, 'A continuación reflexione sobre la misión, visión y valores definidos y establezca los objetivos estratégicos y específi', '', 2, '2025-10-22 22:40:20', '2025-10-22 22:40:20'),
	(9, 5, 'A continuación reflexione sobre la misión, visión y valores definidos y establezca los objetivos estratégicos y específi', '', 1, '2025-10-22 22:40:20', '2025-10-22 22:40:20'),
	(10, 5, 'A continuación reflexione sobre la misión, visión y valores definidos y establezca los objetivos estratégicos y específi', '', 2, '2025-10-22 22:40:20', '2025-10-22 22:40:20'),
	(11, 6, 'A continuación reflexione sobre la misión, visión y valores definidos y establezca los objetivos estratégicos y específi', '', 1, '2025-10-22 22:40:20', '2025-10-22 22:40:20'),
	(12, 6, 'A continuación reflexione sobre la misión, visión y valores definidos y establezca los objetivos estratégicos y específi', '', 2, '2025-10-22 22:40:20', '2025-10-22 22:40:20');

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.project_strategic_objectives: ~6 rows (aproximadamente)
INSERT INTO `project_strategic_objectives` (`id`, `project_id`, `objective_title`, `objective_description`, `objective_order`, `created_at`, `updated_at`) VALUES
	(1, 8, 'Democratizar el Acceso a la Tecnología', '', 1, '2025-10-01 22:28:47', '2025-10-01 22:28:47'),
	(2, 8, 'Impulsar la Educación Digital Global', '', 2, '2025-10-01 22:28:47', '2025-10-01 22:28:47'),
	(3, 8, 'Desarrollar IA Ética y Responsable', '', 3, '2025-10-01 22:28:47', '2025-10-01 22:28:47'),
	(4, 5, 'A continuación reflexione sobre la misión, visión y valores definidos y establezca los objetivos estratégicos y específicos de su empresa. Le proponem', '', 1, '2025-10-22 22:40:20', '2025-10-22 22:40:20'),
	(5, 5, 'A continuación reflexione sobre la misión, visión y valores definidos y establezca los objetivos estratégicos y específicos de su empresa. Le proponem', '', 2, '2025-10-22 22:40:20', '2025-10-22 22:40:20'),
	(6, 5, 'A continuación reflexione sobre la misión, visión y valores definidos y establezca los objetivos estratégicos y específicos de su empresa. Le proponem', '', 3, '2025-10-22 22:40:20', '2025-10-22 22:40:20');

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.project_values: ~12 rows (aproximadamente)
INSERT INTO `project_values` (`id`, `project_id`, `value_text`, `value_order`, `created_at`, `updated_at`) VALUES
	(1, 6, 'Integridad', 1, '2025-09-18 18:48:55', '2025-09-18 18:48:55'),
	(2, 6, 'Compromiso', 2, '2025-09-18 18:48:55', '2025-09-18 18:48:55'),
	(3, 6, 'Innovación', 3, '2025-09-18 18:48:55', '2025-09-18 18:48:55'),
	(4, 8, 'Accesibilidad Universal', 1, '2025-10-01 22:26:01', '2025-10-01 22:26:01'),
	(5, 8, 'Privacidad y Seguridad', 2, '2025-10-01 22:26:01', '2025-10-01 22:26:01'),
	(6, 8, 'Innovación Responsable', 3, '2025-10-01 22:26:01', '2025-10-01 22:26:01'),
	(7, 8, 'Educación y Empoderamiento', 4, '2025-10-01 22:26:01', '2025-10-01 22:26:01'),
	(8, 8, 'Impacto Social Positivo', 5, '2025-10-01 22:26:01', '2025-10-01 22:26:01'),
	(9, 8, 'Excelencia y Calidad', 6, '2025-10-01 22:26:01', '2025-10-01 22:26:01'),
	(10, 5, 'Integridad', 1, '2025-10-22 22:37:54', '2025-10-22 22:37:54'),
	(11, 5, 'Compromiso', 2, '2025-10-22 22:37:54', '2025-10-22 22:37:54'),
	(12, 5, 'Innovación', 3, '2025-10-22 22:37:54', '2025-10-22 22:37:54');

-- Volcando estructura para tabla railway.project_value_chain
CREATE TABLE IF NOT EXISTS `project_value_chain` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `question_number` int NOT NULL,
  `rating` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_question` (`project_id`,`question_number`),
  KEY `idx_project_id` (`project_id`),
  CONSTRAINT `project_value_chain_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_value_chain_chk_1` CHECK ((`question_number` between 1 and 25)),
  CONSTRAINT `project_value_chain_chk_2` CHECK ((`rating` between 0 and 4))
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla railway.project_value_chain: ~75 rows (aproximadamente)
INSERT INTO `project_value_chain` (`id`, `project_id`, `question_number`, `rating`, `created_at`, `updated_at`) VALUES
	(1, 7, 1, 2, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(2, 7, 2, 3, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(3, 7, 3, 1, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(4, 7, 4, 3, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(5, 7, 5, 2, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(6, 7, 6, 2, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(7, 7, 7, 2, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(8, 7, 8, 2, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(9, 7, 9, 2, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(10, 7, 10, 2, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(11, 7, 11, 2, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(12, 7, 12, 1, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(13, 7, 13, 2, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(14, 7, 14, 3, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(15, 7, 15, 1, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(16, 7, 16, 2, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(17, 7, 17, 3, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(18, 7, 18, 4, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(19, 7, 19, 4, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(20, 7, 20, 4, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(21, 7, 21, 4, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(22, 7, 22, 4, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(23, 7, 23, 2, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(24, 7, 24, 2, '2025-10-01 04:28:18', '2025-10-01 04:28:18'),
	(25, 7, 25, 2, '2025-10-01 04:28:19', '2025-10-01 04:28:19'),
	(26, 8, 1, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(27, 8, 2, 2, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(28, 8, 3, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(29, 8, 4, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(30, 8, 5, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(31, 8, 6, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(32, 8, 7, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(33, 8, 8, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(34, 8, 9, 2, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(35, 8, 10, 2, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(36, 8, 11, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(37, 8, 12, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(38, 8, 13, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(39, 8, 14, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(40, 8, 15, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(41, 8, 16, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(42, 8, 17, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(43, 8, 18, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(44, 8, 19, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(45, 8, 20, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(46, 8, 21, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(47, 8, 22, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(48, 8, 23, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(49, 8, 24, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(50, 8, 25, 3, '2025-10-01 22:42:00', '2025-10-01 22:42:00'),
	(51, 5, 1, 3, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(52, 5, 2, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(53, 5, 3, 3, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(54, 5, 4, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(55, 5, 5, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(56, 5, 6, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(57, 5, 7, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(58, 5, 8, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(59, 5, 9, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(60, 5, 10, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(61, 5, 11, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(62, 5, 12, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(63, 5, 13, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(64, 5, 14, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(65, 5, 15, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(66, 5, 16, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(67, 5, 17, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(68, 5, 18, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(69, 5, 19, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(70, 5, 20, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(71, 5, 21, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(72, 5, 22, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(73, 5, 23, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(74, 5, 24, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(75, 5, 25, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55');

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.project_vision: ~3 rows (aproximadamente)
INSERT INTO `project_vision` (`id`, `project_id`, `vision_text`, `is_completed`, `created_at`, `updated_at`) VALUES
	(1, 6, 'Ser reconocidos en 2027, como la mejor salchipaperia de Tacna', 1, '2025-09-18 18:48:30', '2025-09-18 18:48:30'),
	(2, 8, 'afdddddddddddddddddddddddddddddddddddddddddddddd ssssssssssssssssssssssssssssssssssssssssssssss sssssssssssssssssssssssssssssssssssssssssssssssss', 1, '2025-10-01 22:24:15', '2025-10-01 22:24:15'),
	(3, 5, 'Imagina tu empresa en 2-3 años: ¿Dónde estará ubicada? ¿Qué productos o servicios ofrecerá? ¿Cómo será reconocida en el mercado? ¿Cuál será su posición competitiva?', 1, '2025-10-22 22:37:42', '2025-10-22 22:37:42');

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla railway.strategic_projects: ~7 rows (aproximadamente)
INSERT INTO `strategic_projects` (`id`, `user_id`, `project_name`, `company_name`, `created_at`, `updated_at`, `completed_at`, `status`, `progress_percentage`) VALUES
	(1, 7, 'dafafaffafaf', 'fafafafaf', '2025-09-17 23:55:51', '2025-09-17 23:55:51', NULL, 'in_progress', NULL),
	(2, 2, 'Plan de Superación de Caida de ventas', 'CAPICODEX', '2025-09-18 00:08:52', '2025-09-18 00:08:52', NULL, 'in_progress', NULL),
	(3, 6, 'proyecto 1', 'proyecto 1', '2025-09-18 18:38:21', '2025-09-18 18:38:21', NULL, 'in_progress', 0.00),
	(4, 2, 'PLAN PARA AUMENTO DE VENTAS DE LA SALCHIPAPERIA DE VICTOR', 'SALCHIPAPEANDO CON VICTOR', '2025-09-18 18:43:36', '2025-09-18 18:43:36', NULL, 'in_progress', NULL),
	(5, 6, 'lk;lk;lk;l', 'kkkkk', '2025-09-18 18:45:17', '2025-09-18 18:45:17', NULL, 'in_progress', NULL),
	(6, 2, 'PLAN ESTRATEGICO PARA AUMENTO DE VENTAS DE LA SALCHIPAPERIA DE VICTOR', 'SALCHIPAPEANDO CON VICTOR', '2025-09-18 18:45:41', '2025-09-18 18:45:41', NULL, 'in_progress', NULL),
	(7, 10, 'dadawdawdadwadaw', 'dwdawdawdawd', '2025-09-18 19:05:32', '2025-09-18 19:05:32', NULL, 'in_progress', NULL),
	(8, 3, 'Plan Estrategico Google 2025-2030: Tecnologia para Todos', 'Google', '2025-10-01 22:23:19', '2025-10-01 22:23:19', NULL, 'in_progress', 0.00);

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

-- Volcando datos para la tabla railway.users: ~10 rows (aproximadamente)
INSERT INTO `users` (`id`, `email`, `password`, `name`, `avatar`, `google_id`, `email_verified`, `verification_token`, `reset_token`, `reset_token_expires`, `created_at`, `updated_at`, `last_login`, `status`) VALUES
	(1, 'admin@planmaster.com', '$2y$10$rCgRXCL8EfE5IUvYwLBVN.6wxPoSCS9QZUTnULXwT2cH4SCcrJ9U.', 'Administrador PlanMaster', NULL, NULL, 1, NULL, NULL, NULL, '2025-09-11 19:04:33', '2025-09-11 23:02:26', NULL, 'active'),
	(2, 'fuentessebastiansa4s@gmail.com', NULL, 'Sebastian Fuentes', 'https://lh3.googleusercontent.com/a/ACg8ocLfEswYK_9p-rBZuBQE7S8VeDn8_qMdo6rVjf2vCrLvDkU9CxBg=s96-c', '118266572871877651902', 1, NULL, NULL, NULL, '2025-09-11 23:08:36', '2025-09-18 18:59:08', '2025-09-18 18:59:08', 'active'),
	(3, 'gg2022074263@virtual.upt.pe', NULL, 'GABRIELA LUZKALID GUTIERREZ MAMANI', 'https://lh3.googleusercontent.com/a/ACg8ocJjxREiRM1D_ZSObeuGt0bZFHXkv4mdBwUTp_BHwvPgg_IZxVpr=s96-c', '115944247263508584295', 1, NULL, NULL, NULL, '2025-09-11 23:10:45', '2025-10-02 22:26:05', '2025-10-02 22:26:05', 'active'),
	(4, 'chevichin2018@gmail.com', '$2y$10$TTSoSkzIGip9IATplJuHy.6Yd7WSb9vDIbU4Cu6B3Uniao05mJ3nC', 'Chebastian Ricolas', NULL, NULL, 1, NULL, NULL, NULL, '2025-09-11 23:58:53', '2025-09-11 23:59:29', '2025-09-11 23:59:29', 'active'),
	(5, 'victoraprendiendocon@gmail.com', NULL, 'Aprendiendo con Victor', 'https://lh3.googleusercontent.com/a/ACg8ocITzx8cXQonIajDFmHtppjavUgNFl2YqzWyXUmeGAps1M3WM7Q=s96-c', '115289334880461933766', 1, NULL, NULL, NULL, '2025-09-12 00:04:32', '2025-09-12 00:04:32', '2025-09-12 00:04:32', 'active'),
	(6, 'nkmelndz@gmail.com', '$2y$10$CZxOAbvuR47a/5rfZ/zqL.TTC4msAvG1WNF.CeLGXKGTPhPlOyQQ.', 'nikolas', NULL, NULL, 1, NULL, NULL, NULL, '2025-09-17 21:59:04', '2025-10-22 22:25:18', '2025-10-22 22:25:18', 'active'),
	(7, 'sf2022073902@virtual.upt.pe', NULL, 'SEBASTIAN NICOLAS FUENTES AVALOS', 'https://lh3.googleusercontent.com/a/ACg8ocIldVbBQckiP7rwOIKiNWrDyrMX8yoUr2wjceuxppk4ahCQpm0=s96-c', '118030351119923353936', 1, NULL, NULL, NULL, '2025-09-17 21:59:09', '2025-10-22 22:43:13', '2025-10-22 22:43:13', 'active'),
	(8, 'ferquatck@gmail.com', NULL, 'fer ,', 'https://lh3.googleusercontent.com/a/ACg8ocJwB9Y4ST5t74ag0w5PyB7qshajRj4NsO-1HvO7QsUIOizrBg=s96-c', '108307062242127529441', 1, NULL, NULL, NULL, '2025-09-17 22:01:35', '2025-09-17 22:01:35', '2025-09-17 22:01:35', 'active'),
	(9, 'cescamac@upt.pe', '$2y$10$KRSRaJ0qScKBdlIBKwpBwukDiVHkbC7FlEOcCdXF4QGBCjs6quv5e', 'cesar camac', NULL, NULL, 1, NULL, NULL, NULL, '2025-09-18 04:08:08', '2025-09-18 04:08:15', '2025-09-18 04:08:15', 'active'),
	(10, 'gagaga@email.com', '$2y$10$S58/gIoNoC9ruw9dia59sOpduIAYei2QBiNMEMwwuoyG33aV.UkdW', 'gagaga', NULL, NULL, 1, NULL, NULL, NULL, '2025-09-18 19:05:09', '2025-10-01 04:27:30', '2025-10-01 04:27:30', 'active');

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
