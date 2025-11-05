-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.32-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
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


-- Volcando estructura de base de datos para planmaster
CREATE DATABASE IF NOT EXISTS `planmaster` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `planmaster`;

-- Volcando estructura para tabla planmaster.project_bcg_analysis
CREATE TABLE IF NOT EXISTS `project_bcg_analysis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `analysis_name` varchar(255) DEFAULT 'Análisis BCG',
  `analysis_status` enum('draft','in_progress','completed') DEFAULT 'draft',
  `total_sales_forecast` decimal(15,2) DEFAULT 0.00,
  `average_tcm` decimal(5,2) DEFAULT 0.00,
  `average_prm` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_bcg` (`project_id`),
  KEY `idx_bcg_project` (`project_id`),
  KEY `idx_bcg_status` (`analysis_status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.project_bcg_analysis: ~2 rows (aproximadamente)
INSERT INTO `project_bcg_analysis` (`id`, `project_id`, `analysis_name`, `analysis_status`, `total_sales_forecast`, `average_tcm`, `average_prm`, `created_at`, `updated_at`) VALUES
	(1, 10, 'Análisis BCG', 'completed', 0.00, 0.00, 0.00, '2025-10-30 01:21:51', '2025-10-30 01:26:01'),
	(3, 7, 'Análisis BCG', 'completed', 28700.00, 13.22, 0.55, '2025-10-30 01:31:07', '2025-10-30 02:06:32');

-- Volcando estructura para tabla planmaster.project_bcg_competitors
CREATE TABLE IF NOT EXISTS `project_bcg_competitors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `competitor_name` varchar(255) NOT NULL,
  `competitor_sales` decimal(15,2) NOT NULL DEFAULT 0.00,
  `market_share_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_main_competitor` tinyint(1) DEFAULT 0,
  `is_max_sales` tinyint(1) DEFAULT 0,
  `competitor_order` int(11) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_competitors_project` (`project_id`),
  KEY `idx_competitors_product` (`product_id`),
  KEY `idx_competitors_order` (`product_id`,`competitor_order`),
  KEY `idx_competitors_main` (`product_id`,`is_main_competitor`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.project_bcg_competitors: ~14 rows (aproximadamente)
INSERT INTO `project_bcg_competitors` (`id`, `project_id`, `product_id`, `competitor_name`, `competitor_sales`, `market_share_percentage`, `is_main_competitor`, `is_max_sales`, `competitor_order`, `notes`, `created_at`, `updated_at`) VALUES
	(8, 10, 4, 'Apple iPhone', 25000.00, 0.00, 0, 0, 1, NULL, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(9, 10, 4, 'Samsung Galaxy', 22000.00, 0.00, 0, 0, 2, NULL, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(10, 10, 4, 'Xiaomi Mi', 18000.00, 0.00, 0, 0, 3, NULL, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(11, 10, 4, 'ASUS ROG', 12000.00, 0.00, 0, 0, 4, NULL, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(12, 10, 4, 'MSI Gaming', 10500.00, 0.00, 0, 0, 5, NULL, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(13, 10, 4, 'iPad Pro', 15000.00, 0.00, 0, 0, 6, NULL, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(14, 10, 4, 'Surface Pro', 8500.00, 0.00, 0, 0, 7, NULL, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(52, 7, 23, 'Apple iPhone', 25000.00, 0.00, 0, 1, 1, NULL, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(53, 7, 23, 'Samsung Galaxy', 22000.00, 0.00, 0, 0, 2, NULL, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(54, 7, 23, 'Xiaomi Mi', 18000.00, 0.00, 0, 0, 3, NULL, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(55, 7, 24, 'ASUS ROG', 12000.00, 0.00, 0, 1, 1, NULL, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(56, 7, 24, 'MSI Gaming', 10500.00, 0.00, 0, 0, 2, NULL, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(57, 7, 25, 'iPad Pro', 15000.00, 0.00, 0, 1, 1, NULL, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(58, 7, 25, 'Surface Pro', 8500.00, 0.00, 0, 0, 2, NULL, '2025-10-30 02:06:32', '2025-10-30 02:06:32');

-- Volcando estructura para tabla planmaster.project_bcg_market_growth
CREATE TABLE IF NOT EXISTS `project_bcg_market_growth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `period_name` varchar(100) NOT NULL,
  `period_start_year` int(11) NOT NULL,
  `period_end_year` int(11) NOT NULL,
  `tcm_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `period_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_period` (`product_id`,`period_order`),
  KEY `idx_market_growth_project` (`project_id`),
  KEY `idx_market_growth_product` (`product_id`),
  KEY `idx_market_growth_order` (`product_id`,`period_order`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.project_bcg_market_growth: ~8 rows (aproximadamente)
INSERT INTO `project_bcg_market_growth` (`id`, `project_id`, `product_id`, `period_name`, `period_start_year`, `period_end_year`, `tcm_percentage`, `period_order`, `created_at`, `updated_at`) VALUES
	(3, 10, 4, '2023-2024', 2023, 2024, 0.00, 1, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(4, 10, 4, '2024-2025', 2024, 2025, 0.00, 2, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(35, 7, 23, '2023-2024', 2023, 2024, 15.50, 1, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(36, 7, 24, '2023-2024', 2023, 2024, 8.20, 1, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(37, 7, 25, '2023-2024', 2023, 2024, 12.10, 1, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(38, 7, 23, '2024-2025', 2024, 2025, 18.30, 2, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(39, 7, 24, '2024-2025', 2024, 2025, 10.50, 2, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(40, 7, 25, '2024-2025', 2024, 2025, 14.70, 2, '2025-10-30 02:06:32', '2025-10-30 02:06:32');

-- Volcando estructura para tabla planmaster.project_bcg_matrix_results
CREATE TABLE IF NOT EXISTS `project_bcg_matrix_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `prm_relative_position` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `tcm_market_growth` decimal(5,2) NOT NULL DEFAULT 0.00,
  `bcg_quadrant` enum('estrella','interrogante','vaca_lechera','perro') NOT NULL,
  `quadrant_description` text DEFAULT NULL,
  `strategic_recommendation` text DEFAULT NULL,
  `matrix_position_x` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `matrix_position_y` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `bubble_size` decimal(8,4) DEFAULT 1.0000,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_matrix` (`product_id`),
  KEY `idx_matrix_project` (`project_id`),
  KEY `idx_matrix_product` (`product_id`),
  KEY `idx_matrix_quadrant` (`project_id`,`bcg_quadrant`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.project_bcg_matrix_results: ~15 rows (aproximadamente)
INSERT INTO `project_bcg_matrix_results` (`id`, `project_id`, `product_id`, `prm_relative_position`, `tcm_market_growth`, `bcg_quadrant`, `quadrant_description`, `strategic_recommendation`, `matrix_position_x`, `matrix_position_y`, `bubble_size`, `created_at`, `updated_at`) VALUES
	(1, 7, 11, 0.6000, 16.90, 'interrogante', NULL, NULL, 30.0000, 84.5000, 1.5000, '2025-10-30 01:40:35', '2025-10-30 01:40:35'),
	(2, 7, 12, 0.0000, 9.35, 'perro', NULL, NULL, 0.0000, 46.7500, 0.8500, '2025-10-30 01:40:35', '2025-10-30 01:40:35'),
	(3, 7, 13, 0.0000, 13.40, 'interrogante', NULL, NULL, 0.0000, 67.0000, 0.5200, '2025-10-30 01:40:35', '2025-10-30 01:40:35'),
	(4, 7, 14, 0.6000, 16.90, 'interrogante', NULL, NULL, 30.0000, 84.5000, 1.5000, '2025-10-30 01:41:40', '2025-10-30 01:41:40'),
	(5, 7, 15, 0.0000, 9.35, 'perro', NULL, NULL, 0.0000, 46.7500, 0.8500, '2025-10-30 01:41:40', '2025-10-30 01:41:40'),
	(6, 7, 16, 0.0000, 13.40, 'interrogante', NULL, NULL, 0.0000, 67.0000, 0.5200, '2025-10-30 01:41:40', '2025-10-30 01:41:40'),
	(7, 7, 17, 0.6000, 11.27, 'interrogante', NULL, NULL, 30.0000, 56.3333, 1.5000, '2025-10-30 01:43:08', '2025-10-30 01:43:08'),
	(8, 7, 18, 0.0000, 6.23, 'perro', NULL, NULL, 0.0000, 31.1667, 0.8500, '2025-10-30 01:43:08', '2025-10-30 01:43:08'),
	(9, 7, 19, 0.0000, 8.93, 'perro', NULL, NULL, 0.0000, 44.6667, 0.5200, '2025-10-30 01:43:08', '2025-10-30 01:43:08'),
	(10, 7, 20, 0.6000, 16.90, 'interrogante', NULL, NULL, 30.0000, 84.5000, 1.5000, '2025-10-30 01:45:41', '2025-10-30 01:45:41'),
	(11, 7, 21, 0.0000, 9.35, 'perro', NULL, NULL, 0.0000, 46.7500, 0.8500, '2025-10-30 01:45:41', '2025-10-30 01:45:41'),
	(12, 7, 22, 0.0000, 13.40, 'interrogante', NULL, NULL, 0.0000, 67.0000, 0.5200, '2025-10-30 01:45:41', '2025-10-30 01:45:41'),
	(13, 7, 23, 0.6000, 16.90, 'interrogante', NULL, NULL, 30.0000, 84.5000, 1.5000, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(14, 7, 24, 0.7083, 9.35, 'perro', NULL, NULL, 35.4167, 46.7500, 0.8500, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(15, 7, 25, 0.3467, 13.40, 'interrogante', NULL, NULL, 17.3333, 67.0000, 0.5200, '2025-10-30 02:06:32', '2025-10-30 02:06:32');

-- Volcando estructura para tabla planmaster.project_bcg_products
CREATE TABLE IF NOT EXISTS `project_bcg_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `sales_forecast` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sales_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tcm_calculated` decimal(5,2) NOT NULL DEFAULT 0.00,
  `prm_calculated` decimal(5,2) NOT NULL DEFAULT 0.00,
  `bcg_quadrant` enum('estrella','interrogante','vaca_lechera','perro') DEFAULT NULL,
  `bcg_position_x` decimal(8,4) DEFAULT 0.0000,
  `bcg_position_y` decimal(8,4) DEFAULT 0.0000,
  `product_order` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_products_project` (`project_id`),
  KEY `idx_products_order` (`project_id`,`product_order`),
  KEY `idx_products_active` (`project_id`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.project_bcg_products: ~6 rows (aproximadamente)
INSERT INTO `project_bcg_products` (`id`, `project_id`, `product_name`, `sales_forecast`, `sales_percentage`, `tcm_calculated`, `prm_calculated`, `bcg_quadrant`, `bcg_position_x`, `bcg_position_y`, `product_order`, `is_active`, `created_at`, `updated_at`) VALUES
	(4, 10, 'Smartphone Pro', 15000.00, 52.00, 0.00, 0.00, NULL, 0.0000, 0.0000, 1, 1, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(5, 10, 'Laptop Gaming', 8500.00, 29.00, 0.00, 0.00, NULL, 0.0000, 0.0000, 2, 1, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(6, 10, 'Tablet Ultra', 5200.00, 18.00, 0.00, 0.00, NULL, 0.0000, 0.0000, 3, 1, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(23, 7, 'Smartphone Pro', 15000.00, 52.00, 16.90, 0.60, 'interrogante', 0.0000, 0.0000, 1, 1, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(24, 7, 'Laptop Gaming', 8500.00, 29.00, 9.35, 0.71, 'perro', 0.0000, 0.0000, 2, 1, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(25, 7, 'Tablet Ultra', 5200.00, 18.00, 13.40, 0.35, 'interrogante', 0.0000, 0.0000, 3, 1, '2025-10-30 02:06:32', '2025-10-30 02:06:32');

-- Volcando estructura para tabla planmaster.project_bcg_sector_demand
CREATE TABLE IF NOT EXISTS `project_bcg_sector_demand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `total_sector_demand` decimal(15,2) NOT NULL DEFAULT 0.00,
  `company_participation` decimal(15,2) NOT NULL DEFAULT 0.00,
  `participation_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `market_share_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_sector` (`product_id`),
  KEY `idx_sector_demand_project` (`project_id`),
  KEY `idx_sector_demand_product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.project_bcg_sector_demand: ~3 rows (aproximadamente)
INSERT INTO `project_bcg_sector_demand` (`id`, `project_id`, `product_id`, `total_sector_demand`, `company_participation`, `participation_percentage`, `market_share_notes`, `created_at`, `updated_at`) VALUES
	(46, 7, 23, 12.50, 1.25, 10.00, NULL, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(47, 7, 24, 8.30, 0.83, 10.00, NULL, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(48, 7, 25, 15.70, 1.57, 10.00, NULL, '2025-10-30 02:06:32', '2025-10-30 02:06:32');

-- Volcando estructura para tabla planmaster.project_foda_analysis
CREATE TABLE IF NOT EXISTS `project_foda_analysis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `type` enum('oportunidad','amenaza','fortaleza','debilidad') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_project_foda` (`project_id`,`type`,`item_order`),
  CONSTRAINT `project_foda_analysis_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.project_foda_analysis: ~18 rows (aproximadamente)
INSERT INTO `project_foda_analysis` (`id`, `project_id`, `type`, `item_text`, `item_order`, `created_at`, `updated_at`) VALUES
	(83, 8, 'fortaleza', 'ASSDADSAD', 1, '2025-10-23 23:12:36', '2025-10-23 23:12:36'),
	(84, 8, 'fortaleza', 'ASDASD', 2, '2025-10-23 23:12:36', '2025-10-23 23:12:36'),
	(85, 8, 'fortaleza', 'ASDADSAD', 3, '2025-10-23 23:12:36', '2025-10-23 23:12:36'),
	(86, 8, 'debilidad', 'ADAD', 1, '2025-10-23 23:12:36', '2025-10-23 23:12:36'),
	(87, 8, 'debilidad', 'ASDAD', 2, '2025-10-23 23:12:36', '2025-10-23 23:12:36'),
	(88, 8, 'debilidad', 'ASDASDAD', 3, '2025-10-23 23:12:36', '2025-10-23 23:12:36'),
	(95, 10, 'fortaleza', 'Marca reconocida en el mercado', 1, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(96, 10, 'fortaleza', 'Equipo técnico altamente capacitado', 2, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(97, 10, 'fortaleza', 'Red de distribución consolidada', 3, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(98, 10, 'debilidad', 'Altos costos de producción', 1, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(99, 10, 'debilidad', 'Dependencia de proveedores externos', 2, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(100, 10, 'debilidad', 'Limitada presencia digital', 3, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(131, 7, 'fortaleza', 'Innovación tecnológica avanzada', 1, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(132, 7, 'fortaleza', 'Equipo de desarrollo experimentado', 2, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(133, 7, 'fortaleza', 'Base de clientes leales', 3, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(134, 7, 'debilidad', 'Limitada presencia digital', 1, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(135, 7, 'debilidad', 'Dependencia de pocos proveedores', 2, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(136, 7, 'debilidad', 'Recursos financieros limitados', 3, '2025-10-30 02:06:32', '2025-10-30 02:06:32');

