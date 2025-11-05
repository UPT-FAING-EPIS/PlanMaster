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

-- Volcando datos para la tabla planmaster.project_bcg_analysis: ~3 rows (aproximadamente)
INSERT INTO `project_bcg_analysis` (`id`, `project_id`, `analysis_name`, `analysis_status`, `total_sales_forecast`, `average_tcm`, `average_prm`, `created_at`, `updated_at`) VALUES
	(1, 10, 'Análisis BCG', 'completed', 0.00, 0.00, 0.00, '2025-10-30 01:21:51', '2025-10-30 01:26:01'),
	(3, 7, 'Análisis BCG', 'completed', 28700.00, 13.22, 0.55, '2025-10-30 01:31:07', '2025-11-05 17:55:30'),
	(13, 11, 'Análisis BCG', 'completed', 28700.00, 13.15, 0.55, '2025-11-05 17:02:14', '2025-11-05 19:30:12');

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

-- Volcando datos para la tabla planmaster.project_bcg_competitors: ~27 rows (aproximadamente)
INSERT INTO `project_bcg_competitors` (`id`, `project_id`, `product_id`, `competitor_name`, `competitor_sales`, `market_share_percentage`, `is_main_competitor`, `is_max_sales`, `competitor_order`, `notes`, `created_at`, `updated_at`) VALUES
	(8, 10, 4, 'Apple iPhone', 25000.00, 0.00, 0, 0, 1, NULL, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(9, 10, 4, 'Samsung Galaxy', 22000.00, 0.00, 0, 0, 2, NULL, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(10, 10, 4, 'Xiaomi Mi', 18000.00, 0.00, 0, 0, 3, NULL, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(11, 10, 4, 'ASUS ROG', 12000.00, 0.00, 0, 0, 4, NULL, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(12, 10, 4, 'MSI Gaming', 10500.00, 0.00, 0, 0, 5, NULL, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(13, 10, 4, 'iPad Pro', 15000.00, 0.00, 0, 0, 6, NULL, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(14, 10, 4, 'Surface Pro', 8500.00, 0.00, 0, 0, 7, NULL, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(138, 7, 54, 'Apple iPhone', 25000.00, 0.00, 0, 1, 1, NULL, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(139, 7, 54, 'Samsung Galaxy', 22000.00, 0.00, 0, 0, 2, NULL, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(140, 7, 54, 'Xiaomi Mi', 18000.00, 0.00, 0, 0, 3, NULL, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(141, 7, 55, 'ASUS ROG', 12000.00, 0.00, 0, 1, 1, NULL, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(142, 7, 55, 'MSI Gaming', 10500.00, 0.00, 0, 0, 2, NULL, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(143, 7, 56, 'iPad Pro', 15000.00, 0.00, 0, 1, 1, NULL, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(144, 7, 56, 'Surface Pro', 8500.00, 0.00, 0, 0, 2, NULL, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(152, 11, 60, 'Apple iPhone', 25000.00, 0.00, 0, 1, 1, NULL, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(153, 11, 60, 'Samsung Galaxy', 22000.00, 0.00, 0, 0, 2, NULL, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(154, 11, 60, 'Xiaomi Mi', 18000.00, 0.00, 0, 0, 3, NULL, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(155, 11, 61, 'ASUS ROG', 12000.00, 0.00, 0, 1, 1, NULL, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(156, 11, 61, 'MSI Gaming', 10500.00, 0.00, 0, 0, 2, NULL, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(157, 11, 62, 'iPad Pro', 15000.00, 0.00, 0, 1, 1, NULL, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(158, 11, 62, 'Surface Pro', 8500.00, 0.00, 0, 0, 2, NULL, '2025-11-05 19:30:12', '2025-11-05 19:30:12');

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

-- Volcando datos para la tabla planmaster.project_bcg_market_growth: ~23 rows (aproximadamente)
INSERT INTO `project_bcg_market_growth` (`id`, `project_id`, `product_id`, `period_name`, `period_start_year`, `period_end_year`, `tcm_percentage`, `period_order`, `created_at`, `updated_at`) VALUES
	(3, 10, 4, '2023-2024', 2023, 2024, 0.00, 1, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(4, 10, 4, '2024-2025', 2024, 2025, 0.00, 2, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(119, 7, 54, '2025-2026', 2025, 2026, 15.50, 1, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(120, 7, 55, '2025-2026', 2025, 2026, 8.20, 1, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(121, 7, 56, '2025-2026', 2025, 2026, 12.10, 1, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(122, 7, 54, '2024-2025', 2024, 2025, 18.30, 2, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(123, 7, 55, '2024-2025', 2024, 2025, 10.50, 2, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(124, 7, 56, '2024-2025', 2024, 2025, 14.70, 2, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(131, 11, 60, '2025-2026', 2025, 2026, 15.50, 1, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(132, 11, 61, '2025-2026', 2025, 2026, 8.20, 1, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(133, 11, 62, '2025-2026', 2025, 2026, 12.10, 1, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(134, 11, 60, '2024-2025', 2024, 2025, 18.30, 2, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(135, 11, 61, '2024-2025', 2024, 2025, 10.50, 2, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(136, 11, 62, '2024-2025', 2024, 2025, 14.70, 2, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(137, 11, 60, '2023-2024', 2023, 2024, 13.00, 3, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(138, 11, 61, '2023-2024', 2023, 2024, 13.00, 3, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(139, 11, 62, '2023-2024', 2023, 2024, 13.00, 3, '2025-11-05 19:30:12', '2025-11-05 19:30:12');

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

-- Volcando datos para la tabla planmaster.project_bcg_matrix_results: ~34 rows (aproximadamente)
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
	(15, 7, 25, 0.3467, 13.40, 'interrogante', NULL, NULL, 17.3333, 67.0000, 0.5200, '2025-10-30 02:06:32', '2025-10-30 02:06:32'),
	(16, 7, 26, 0.6000, 15.60, 'interrogante', NULL, NULL, 30.0000, 78.0000, 1.5000, '2025-11-05 16:59:24', '2025-11-05 16:59:24'),
	(17, 7, 27, 0.7083, 10.57, 'interrogante', NULL, NULL, 35.4167, 52.8333, 0.8500, '2025-11-05 16:59:24', '2025-11-05 16:59:24'),
	(18, 7, 28, 0.2600, 13.27, 'interrogante', NULL, NULL, 13.0000, 66.3333, 0.5200, '2025-11-05 16:59:24', '2025-11-05 16:59:24'),
	(19, 7, 29, 0.6000, 15.60, 'interrogante', NULL, NULL, 30.0000, 78.0000, 1.5000, '2025-11-05 17:00:25', '2025-11-05 17:00:25'),
	(20, 7, 30, 0.7083, 10.57, 'interrogante', NULL, NULL, 35.4167, 52.8333, 0.8500, '2025-11-05 17:00:25', '2025-11-05 17:00:25'),
	(21, 7, 31, 0.2600, 13.27, 'interrogante', NULL, NULL, 13.0000, 66.3333, 0.5200, '2025-11-05 17:00:25', '2025-11-05 17:00:25'),
	(22, 7, 32, 0.6000, 15.60, 'interrogante', NULL, NULL, 30.0000, 78.0000, 1.5000, '2025-11-05 17:01:13', '2025-11-05 17:01:13'),
	(23, 7, 33, 0.7083, 10.57, 'interrogante', NULL, NULL, 35.4167, 52.8333, 0.8500, '2025-11-05 17:01:13', '2025-11-05 17:01:13'),
	(24, 7, 34, 0.2600, 13.27, 'interrogante', NULL, NULL, 13.0000, 66.3333, 0.5200, '2025-11-05 17:01:13', '2025-11-05 17:01:13'),
	(25, 11, 35, 0.6000, 16.90, 'interrogante', NULL, NULL, 30.0000, 84.5000, 1.5000, '2025-11-05 17:02:14', '2025-11-05 17:02:14'),
	(26, 11, 36, 0.7083, 9.35, 'perro', NULL, NULL, 35.4167, 46.7500, 0.8500, '2025-11-05 17:02:14', '2025-11-05 17:02:14'),
	(27, 11, 37, 0.3467, 13.40, 'interrogante', NULL, NULL, 17.3333, 67.0000, 0.5200, '2025-11-05 17:02:14', '2025-11-05 17:02:14'),
	(28, 11, 38, 0.6000, 15.33, 'interrogante', NULL, NULL, 30.0000, 76.6667, 1.5000, '2025-11-05 17:04:41', '2025-11-05 17:04:41'),
	(29, 11, 39, 0.7083, 10.30, 'interrogante', NULL, NULL, 35.4167, 51.5000, 0.8500, '2025-11-05 17:04:41', '2025-11-05 17:04:41'),
	(30, 11, 40, 0.3467, 13.00, 'interrogante', NULL, NULL, 17.3333, 65.0000, 0.5200, '2025-11-05 17:04:41', '2025-11-05 17:04:41'),
	(31, 11, 41, 0.6000, 15.33, 'interrogante', NULL, NULL, 30.0000, 76.6667, 1.5000, '2025-11-05 17:08:48', '2025-11-05 17:08:48'),
	(32, 11, 42, 0.7083, 10.30, 'interrogante', NULL, NULL, 35.4167, 51.5000, 0.8500, '2025-11-05 17:08:48', '2025-11-05 17:08:48'),
	(33, 11, 43, 0.3467, 13.00, 'interrogante', NULL, NULL, 17.3333, 65.0000, 0.5200, '2025-11-05 17:08:48', '2025-11-05 17:08:48'),
	(34, 11, 44, 1.2000, 13.13, 'estrella', NULL, NULL, 60.0000, 65.6667, 0.6000, '2025-11-05 17:08:48', '2025-11-05 17:08:48'),
	(35, 7, 45, 0.6000, 15.60, 'interrogante', NULL, NULL, 30.0000, 78.0000, 1.5000, '2025-11-05 17:54:10', '2025-11-05 17:54:10'),
	(36, 7, 46, 0.7083, 10.57, 'interrogante', NULL, NULL, 35.4167, 52.8333, 0.8500, '2025-11-05 17:54:10', '2025-11-05 17:54:10'),
	(37, 7, 47, 0.2600, 13.27, 'interrogante', NULL, NULL, 13.0000, 66.3333, 0.5200, '2025-11-05 17:54:10', '2025-11-05 17:54:10'),
	(38, 7, 48, 0.6000, 15.60, 'interrogante', NULL, NULL, 30.0000, 78.0000, 1.5000, '2025-11-05 17:54:31', '2025-11-05 17:54:31'),
	(39, 7, 49, 0.7083, 10.57, 'interrogante', NULL, NULL, 35.4167, 52.8333, 0.8500, '2025-11-05 17:54:31', '2025-11-05 17:54:31'),
	(40, 7, 50, 0.2600, 13.27, 'interrogante', NULL, NULL, 13.0000, 66.3333, 0.5200, '2025-11-05 17:54:31', '2025-11-05 17:54:31'),
	(41, 7, 51, 0.6000, 16.90, 'interrogante', NULL, NULL, 30.0000, 84.5000, 1.5000, '2025-11-05 17:55:00', '2025-11-05 17:55:00'),
	(42, 7, 52, 0.7083, 9.35, 'perro', NULL, NULL, 35.4167, 46.7500, 0.8500, '2025-11-05 17:55:00', '2025-11-05 17:55:00'),
	(43, 7, 53, 0.3467, 13.40, 'interrogante', NULL, NULL, 17.3333, 67.0000, 0.5200, '2025-11-05 17:55:00', '2025-11-05 17:55:00'),
	(44, 7, 54, 0.6000, 16.90, 'interrogante', NULL, NULL, 30.0000, 84.5000, 1.5000, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(45, 7, 55, 0.7083, 9.35, 'perro', NULL, NULL, 35.4167, 46.7500, 0.8500, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(46, 7, 56, 0.3467, 13.40, 'interrogante', NULL, NULL, 17.3333, 67.0000, 0.5200, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(47, 11, 57, 0.6000, 16.90, 'interrogante', NULL, NULL, 30.0000, 84.5000, 1.5000, '2025-11-05 19:28:37', '2025-11-05 19:28:37'),
	(48, 11, 58, 0.7083, 9.35, 'perro', NULL, NULL, 35.4167, 46.7500, 0.8500, '2025-11-05 19:28:37', '2025-11-05 19:28:37'),
	(49, 11, 59, 0.3467, 13.40, 'interrogante', NULL, NULL, 17.3333, 67.0000, 0.5200, '2025-11-05 19:28:37', '2025-11-05 19:28:37'),
	(50, 11, 60, 0.6000, 15.60, 'interrogante', NULL, NULL, 30.0000, 78.0000, 1.5000, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(51, 11, 61, 0.7083, 10.57, 'interrogante', NULL, NULL, 35.4167, 52.8333, 0.8500, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(52, 11, 62, 0.3467, 13.27, 'interrogante', NULL, NULL, 17.3333, 66.3333, 0.5200, '2025-11-05 19:30:12', '2025-11-05 19:30:12');

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

-- Volcando datos para la tabla planmaster.project_bcg_products: ~10 rows (aproximadamente)
INSERT INTO `project_bcg_products` (`id`, `project_id`, `product_name`, `sales_forecast`, `sales_percentage`, `tcm_calculated`, `prm_calculated`, `bcg_quadrant`, `bcg_position_x`, `bcg_position_y`, `product_order`, `is_active`, `created_at`, `updated_at`) VALUES
	(4, 10, 'Smartphone Pro', 15000.00, 52.00, 0.00, 0.00, NULL, 0.0000, 0.0000, 1, 1, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(5, 10, 'Laptop Gaming', 8500.00, 29.00, 0.00, 0.00, NULL, 0.0000, 0.0000, 2, 1, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(6, 10, 'Tablet Ultra', 5200.00, 18.00, 0.00, 0.00, NULL, 0.0000, 0.0000, 3, 1, '2025-10-30 01:26:01', '2025-10-30 01:26:01'),
	(54, 7, 'Smartphone Pro', 15000.00, 52.00, 16.90, 0.60, 'interrogante', 0.0000, 0.0000, 1, 1, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(55, 7, 'Laptop Gaming', 8500.00, 29.00, 9.35, 0.71, 'perro', 0.0000, 0.0000, 2, 1, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(56, 7, 'Tablet Ultra', 5200.00, 18.00, 13.40, 0.35, 'interrogante', 0.0000, 0.0000, 3, 1, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(60, 11, 'Smartphone Pro', 15000.00, 52.00, 15.60, 0.60, 'interrogante', 0.0000, 0.0000, 1, 1, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(61, 11, 'Laptop Gaming', 8500.00, 29.00, 10.57, 0.71, 'interrogante', 0.0000, 0.0000, 2, 1, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(62, 11, 'Tablet Ultra', 5200.00, 18.00, 13.27, 0.35, 'interrogante', 0.0000, 0.0000, 3, 1, '2025-11-05 19:30:12', '2025-11-05 19:30:12');

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

-- Volcando datos para la tabla planmaster.project_bcg_sector_demand: ~7 rows (aproximadamente)
INSERT INTO `project_bcg_sector_demand` (`id`, `project_id`, `product_id`, `total_sector_demand`, `company_participation`, `participation_percentage`, `market_share_notes`, `created_at`, `updated_at`) VALUES
	(80, 7, 54, 13.80, 1.38, 10.00, NULL, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(81, 7, 55, 9.10, 0.91, 10.00, NULL, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(82, 7, 56, 17.30, 1.73, 10.00, NULL, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(86, 11, 60, 14.00, 1.40, 10.00, NULL, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(87, 11, 61, 14.00, 1.40, 10.00, NULL, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(88, 11, 62, 14.00, 1.40, 10.00, NULL, '2025-11-05 19:30:12', '2025-11-05 19:30:12');

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

-- Volcando datos para la tabla planmaster.project_foda_analysis: ~27 rows (aproximadamente)
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
	(202, 7, 'fortaleza', 'Marca reconocida en el mercado', 1, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(203, 7, 'fortaleza', 'Equipo técnico altamente capacitado', 2, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(204, 7, 'fortaleza', 'Red de distribución consolidada', 3, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(205, 7, 'debilidad', 'Altos costos de producción', 1, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(206, 7, 'debilidad', 'Dependencia de proveedores externos', 2, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(207, 7, 'debilidad', 'Limitada presencia digital', 3, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(208, 7, 'debilidad', 'gaaaa', 4, '2025-11-05 17:55:30', '2025-11-05 17:55:30'),
	(215, 11, 'fortaleza', 'Marca reconocida en el mercado', 1, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(216, 11, 'fortaleza', 'Equipo técnico altamente capacitado', 2, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(217, 11, 'fortaleza', 'Red de distribución consolidada', 3, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(218, 11, 'debilidad', 'Altos costos de producción', 1, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(219, 11, 'debilidad', 'Dependencia de proveedores externos', 2, '2025-11-05 19:30:12', '2025-11-05 19:30:12'),
	(220, 11, 'debilidad', 'Limitada presencia digital', 3, '2025-11-05 19:30:12', '2025-11-05 19:30:12');

-- Volcando estructura para tabla planmaster.project_mission
CREATE TABLE IF NOT EXISTS `project_mission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `mission_text` text NOT NULL,
  `is_completed` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_mission` (`project_id`),
  CONSTRAINT `project_mission_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.project_mission: ~6 rows (aproximadamente)
INSERT INTO `project_mission` (`id`, `project_id`, `mission_text`, `is_completed`, `created_at`, `updated_at`) VALUES
	(1, 2, 'Somos una empresa encargada de la superación de paginas web', 1, '2025-09-18 00:09:59', '2025-09-18 00:09:59'),
	(2, 5, 'fsfsf', 1, '2025-09-18 18:45:44', '2025-09-18 18:45:44'),
	(3, 6, 'Somos una empresa encargada de superación de un platillo típico de las noches turbias de examenes universitario, exactamente, las salchipapas', 1, '2025-09-18 18:48:02', '2025-09-18 18:48:02'),
	(4, 8, 'ggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggggsssssssssssssssssssssssssssssssssssss', 1, '2025-10-01 22:24:04', '2025-10-01 22:24:04'),
	(5, 9, 'habhdhabdhabhdbandnman ajbdbahbdahbdhasd  ahbdhjabdhba', 1, '2025-10-23 23:07:04', '2025-10-23 23:07:04'),
	(6, 11, 'Mision de la empresa es ofrecer productos farmaceuticos de primera calidad para las personas de la ciudad de Tacna', 1, '2025-11-05 16:52:05', '2025-11-05 16:52:05');

-- Volcando estructura para tabla planmaster.project_pest_analysis
CREATE TABLE IF NOT EXISTS `project_pest_analysis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `question_number` int(11) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_pest_question` (`project_id`,`question_number`),
  CONSTRAINT `project_pest_analysis_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_pest_analysis_chk_1` CHECK (`question_number` between 1 and 25),
  CONSTRAINT `project_pest_analysis_chk_2` CHECK (`rating` between 0 and 4)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.project_pest_analysis: ~0 rows (aproximadamente)

-- Volcando estructura para tabla planmaster.project_specific_objectives
CREATE TABLE IF NOT EXISTS `project_specific_objectives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `strategic_objective_id` int(11) NOT NULL,
  `objective_title` varchar(255) NOT NULL,
  `objective_description` text DEFAULT NULL,
  `objective_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_specific_objectives` (`strategic_objective_id`,`objective_order`),
  CONSTRAINT `project_specific_objectives_ibfk_1` FOREIGN KEY (`strategic_objective_id`) REFERENCES `project_strategic_objectives` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.project_specific_objectives: ~24 rows (aproximadamente)
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
	(12, 6, 'A continuación reflexione sobre la misión, visión y valores definidos y establezca los objetivos estratégicos y específi', '', 2, '2025-10-22 22:40:20', '2025-10-22 22:40:20'),
	(13, 7, '3 Objetivos Estratégicos × 2 Objetivos Específicos cada uno = 6 Objetivos Específicos totales D', '3 Objetivos Estratégicos × 2 Objetivos Específicos cada uno = 6 Objetivos Específicos totales\r\n\r\nW', 1, '2025-10-23 23:10:41', '2025-10-23 23:10:41'),
	(14, 7, '3 Objetivos Estratégicos × 2 Objetivos Específicos cada uno = 6 Objetivos Específicos totalesFF', '3 Objetivos Estratégicos × 2 Objetivos Específicos cada uno = 6 Objetivos Específicos totales\r\n\r\nDD', 2, '2025-10-23 23:10:41', '2025-10-23 23:10:41'),
	(15, 8, '3 Objetivos Estratégicos × 2 Objetivos Específicos cada uno = 6 Objetivos Específicos totalesHJJJ', '', 1, '2025-10-23 23:10:41', '2025-10-23 23:10:41'),
	(16, 8, '3 Objetivos Estratégicos × 2 Objetivos Específicos cada uno = 6 Objetivos Específicos totalesOOOO', '', 2, '2025-10-23 23:10:41', '2025-10-23 23:10:41'),
	(17, 9, '3 Objetivos Estratégicos × 2 Objetivos Específicos cada uno = 6 Objetivos Específicos totalesNNNMM', '', 1, '2025-10-23 23:10:41', '2025-10-23 23:10:41'),
	(18, 9, '3 Objetivos Estratégicos × 2 Objetivos Específicos cada uno = 6 Objetivos Específicos totalesKKK', '', 2, '2025-10-23 23:10:41', '2025-10-23 23:10:41'),
	(19, 10, 'Objetivo 1,1,', '', 1, '2025-11-05 16:54:36', '2025-11-05 16:54:36'),
	(20, 10, 'Objetivo 1.2.', '', 2, '2025-11-05 16:54:36', '2025-11-05 16:54:36'),
	(21, 11, 'Objetivo 2.1', '', 1, '2025-11-05 16:54:36', '2025-11-05 16:54:36'),
	(22, 11, 'Objetivo 2.2', '', 2, '2025-11-05 16:54:36', '2025-11-05 16:54:36'),
	(23, 12, 'Objetivo 3.1', '', 1, '2025-11-05 16:54:36', '2025-11-05 16:54:36'),
	(24, 12, 'Objetivo 3.2', '', 2, '2025-11-05 16:54:36', '2025-11-05 16:54:36');

-- Volcando estructura para tabla planmaster.project_strategic_objectives
CREATE TABLE IF NOT EXISTS `project_strategic_objectives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `objective_title` varchar(255) NOT NULL,
  `objective_description` text DEFAULT NULL,
  `objective_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_project_objectives` (`project_id`,`objective_order`),
  CONSTRAINT `project_strategic_objectives_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.project_strategic_objectives: ~12 rows (aproximadamente)
INSERT INTO `project_strategic_objectives` (`id`, `project_id`, `objective_title`, `objective_description`, `objective_order`, `created_at`, `updated_at`) VALUES
	(1, 8, 'Democratizar el Acceso a la Tecnología', '', 1, '2025-10-01 22:28:47', '2025-10-01 22:28:47'),
	(2, 8, 'Impulsar la Educación Digital Global', '', 2, '2025-10-01 22:28:47', '2025-10-01 22:28:47'),
	(3, 8, 'Desarrollar IA Ética y Responsable', '', 3, '2025-10-01 22:28:47', '2025-10-01 22:28:47'),
	(4, 5, 'A continuación reflexione sobre la misión, visión y valores definidos y establezca los objetivos estratégicos y específicos de su empresa. Le proponem', '', 1, '2025-10-22 22:40:20', '2025-10-22 22:40:20'),
	(5, 5, 'A continuación reflexione sobre la misión, visión y valores definidos y establezca los objetivos estratégicos y específicos de su empresa. Le proponem', '', 2, '2025-10-22 22:40:20', '2025-10-22 22:40:20'),
	(6, 5, 'A continuación reflexione sobre la misión, visión y valores definidos y establezca los objetivos estratégicos y específicos de su empresa. Le proponem', '', 3, '2025-10-22 22:40:20', '2025-10-22 22:40:20'),
	(7, 9, '3 Objetivos Estratégicos × 2 Objetivos Específicos cada uno = 6 Objetivos Específicos totales', '', 1, '2025-10-23 23:10:41', '2025-10-23 23:10:41'),
	(8, 9, '3 Objetivos Estratégicos × 2 Objetivos Específicos cada uno = 6 Objetivos Específicos totalesFFFF', '', 2, '2025-10-23 23:10:41', '2025-10-23 23:10:41'),
	(9, 9, '3 Objetivos Estratégicos × 2 Objetivos Específicos cada uno = 6 Objetivos Específicos totalesNNNNN', '', 3, '2025-10-23 23:10:41', '2025-10-23 23:10:41'),
	(10, 11, 'Objetivo 1', '', 1, '2025-11-05 16:54:36', '2025-11-05 16:54:36'),
	(11, 11, 'Objetivo 2', '', 2, '2025-11-05 16:54:36', '2025-11-05 16:54:36'),
	(12, 11, 'Objetivo 3', '', 3, '2025-11-05 16:54:36', '2025-11-05 16:54:36');

-- Volcando estructura para tabla planmaster.project_values
CREATE TABLE IF NOT EXISTS `project_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `value_text` varchar(255) NOT NULL,
  `value_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_project_values` (`project_id`,`value_order`),
  CONSTRAINT `project_values_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.project_values: ~19 rows (aproximadamente)
INSERT INTO `project_values` (`id`, `project_id`, `value_text`, `value_order`, `created_at`, `updated_at`) VALUES
	(1, 6, 'Integridad', 1, '2025-09-18 18:48:55', '2025-09-18 18:48:55'),
	(2, 6, 'Compromiso', 2, '2025-09-18 18:48:55', '2025-09-18 18:48:55'),
	(3, 6, 'Innovación', 3, '2025-09-18 18:48:55', '2025-09-18 18:48:55'),
	(10, 5, 'Integridad', 1, '2025-10-22 22:37:54', '2025-10-22 22:37:54'),
	(11, 5, 'Compromiso', 2, '2025-10-22 22:37:54', '2025-10-22 22:37:54'),
	(12, 5, 'Innovación', 3, '2025-10-22 22:37:54', '2025-10-22 22:37:54'),
	(13, 8, 'Accesibilidad Universal', 1, '2025-10-23 12:12:50', '2025-10-23 12:12:50'),
	(14, 8, 'Privacidad y Seguridad', 2, '2025-10-23 12:12:50', '2025-10-23 12:12:50'),
	(15, 8, 'Innovación Responsable', 3, '2025-10-23 12:12:50', '2025-10-23 12:12:50'),
	(16, 8, 'Educación y Empoderamiento', 4, '2025-10-23 12:12:50', '2025-10-23 12:12:50'),
	(17, 8, 'Impacto Social Positivo', 5, '2025-10-23 12:12:50', '2025-10-23 12:12:50'),
	(18, 8, 'Excelencia y Calidad', 6, '2025-10-23 12:12:50', '2025-10-23 12:12:50'),
	(19, 9, 'Integridad', 1, '2025-10-23 23:09:13', '2025-10-23 23:09:13'),
	(20, 9, 'Innovación', 2, '2025-10-23 23:09:13', '2025-10-23 23:09:13'),
	(21, 9, 'Compromiso', 3, '2025-10-23 23:09:13', '2025-10-23 23:09:13'),
	(22, 9, 'Excelencia', 4, '2025-10-23 23:09:13', '2025-10-23 23:09:13'),
	(23, 11, 'Integridad', 1, '2025-11-05 16:52:52', '2025-11-05 16:52:52'),
	(24, 11, 'Compromiso', 2, '2025-11-05 16:52:52', '2025-11-05 16:52:52'),
	(25, 11, 'Etica', 3, '2025-11-05 16:52:52', '2025-11-05 16:52:52');

-- Volcando estructura para tabla planmaster.project_value_chain
CREATE TABLE IF NOT EXISTS `project_value_chain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `question_number` int(11) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_question` (`project_id`,`question_number`),
  KEY `idx_project_id` (`project_id`),
  CONSTRAINT `project_value_chain_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_value_chain_chk_1` CHECK (`question_number` between 1 and 25),
  CONSTRAINT `project_value_chain_chk_2` CHECK (`rating` between 0 and 4)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.project_value_chain: ~125 rows (aproximadamente)
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
	(75, 5, 25, 2, '2025-10-22 22:27:55', '2025-10-22 22:27:55'),
	(76, 9, 1, 2, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(77, 9, 2, 3, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(78, 9, 3, 4, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(79, 9, 4, 1, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(80, 9, 5, 2, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(81, 9, 6, 3, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(82, 9, 7, 1, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(83, 9, 8, 2, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(84, 9, 9, 4, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(85, 9, 10, 1, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(86, 9, 11, 3, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(87, 9, 12, 0, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(88, 9, 13, 4, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(89, 9, 14, 4, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(90, 9, 15, 4, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(91, 9, 16, 4, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(92, 9, 17, 3, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(93, 9, 18, 3, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(94, 9, 19, 3, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(95, 9, 20, 2, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(96, 9, 21, 2, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(97, 9, 22, 2, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(98, 9, 23, 1, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(99, 9, 24, 1, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(100, 9, 25, 1, '2025-10-23 23:11:46', '2025-10-23 23:11:46'),
	(101, 11, 1, 3, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(102, 11, 2, 4, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(103, 11, 3, 2, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(104, 11, 4, 4, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(105, 11, 5, 4, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(106, 11, 6, 0, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(107, 11, 7, 0, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(108, 11, 8, 2, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(109, 11, 9, 4, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(110, 11, 10, 1, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(111, 11, 11, 3, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(112, 11, 12, 1, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(113, 11, 13, 1, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(114, 11, 14, 3, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(115, 11, 15, 4, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(116, 11, 16, 4, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(117, 11, 17, 4, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(118, 11, 18, 4, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(119, 11, 19, 4, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(120, 11, 20, 3, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(121, 11, 21, 4, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(122, 11, 22, 3, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(123, 11, 23, 4, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(124, 11, 24, 3, '2025-11-05 16:55:25', '2025-11-05 16:55:25'),
	(125, 11, 25, 4, '2025-11-05 16:55:25', '2025-11-05 16:55:25');

-- Volcando estructura para tabla planmaster.project_vision
CREATE TABLE IF NOT EXISTS `project_vision` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `vision_text` text NOT NULL,
  `is_completed` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_vision` (`project_id`),
  CONSTRAINT `project_vision_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.project_vision: ~5 rows (aproximadamente)
INSERT INTO `project_vision` (`id`, `project_id`, `vision_text`, `is_completed`, `created_at`, `updated_at`) VALUES
	(1, 6, 'Ser reconocidos en 2027, como la mejor salchipaperia de Tacna', 1, '2025-09-18 18:48:30', '2025-09-18 18:48:30'),
	(2, 8, 'afdddddddddddddddddddddddddddddddddddddddddddddd ssssssssssssssssssssssssssssssssssssssssssssss sssssssssssssssssssssssssssssssssssssssssssssssss', 1, '2025-10-01 22:24:15', '2025-10-01 22:24:15'),
	(3, 5, 'Imagina tu empresa en 2-3 años: ¿Dónde estará ubicada? ¿Qué productos o servicios ofrecerá? ¿Cómo será reconocida en el mercado? ¿Cuál será su posición competitiva?', 1, '2025-10-22 22:37:42', '2025-10-22 22:37:42'),
	(4, 9, 'ajsdkjabjdkbkjs ajbdkjakjdnkjand ajndkjadkjbajd ajsdkjbakjdbsa', 1, '2025-10-23 23:08:21', '2025-10-23 23:08:21'),
	(5, 11, 'Visión de la empresa es ser reconocida a nivel distrital de Tacna como una de las farmacias con mas relevancia.', 1, '2025-11-05 16:52:37', '2025-11-05 16:52:37');

-- Volcando estructura para tabla planmaster.strategic_projects
CREATE TABLE IF NOT EXISTS `strategic_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `status` enum('draft','in_progress','completed') DEFAULT 'draft',
  `progress_percentage` decimal(5,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `strategic_projects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.strategic_projects: ~11 rows (aproximadamente)
INSERT INTO `strategic_projects` (`id`, `user_id`, `project_name`, `company_name`, `created_at`, `updated_at`, `completed_at`, `status`, `progress_percentage`) VALUES
	(1, 7, 'dafafaffafaf', 'fafafafaf', '2025-09-17 23:55:51', '2025-09-17 23:55:51', NULL, 'in_progress', NULL),
	(2, 2, 'Plan de Superación de Caida de ventas', 'CAPICODEX', '2025-09-18 00:08:52', '2025-09-18 00:08:52', NULL, 'in_progress', NULL),
	(3, 6, 'proyecto 1', 'proyecto 1', '2025-09-18 18:38:21', '2025-09-18 18:38:21', NULL, 'in_progress', 0.00),
	(4, 2, 'PLAN PARA AUMENTO DE VENTAS DE LA SALCHIPAPERIA DE VICTOR', 'SALCHIPAPEANDO CON VICTOR', '2025-09-18 18:43:36', '2025-09-18 18:43:36', NULL, 'in_progress', NULL),
	(5, 6, 'lk;lk;lk;l', 'kkkkk', '2025-09-18 18:45:17', '2025-09-18 18:45:17', NULL, 'in_progress', NULL),
	(6, 2, 'PLAN ESTRATEGICO PARA AUMENTO DE VENTAS DE LA SALCHIPAPERIA DE VICTOR', 'SALCHIPAPEANDO CON VICTOR', '2025-09-18 18:45:41', '2025-09-18 18:45:41', NULL, 'in_progress', NULL),
	(7, 10, 'dadawdawdadwadaw', 'dwdawdawdawd', '2025-09-18 19:05:32', '2025-09-18 19:05:32', NULL, 'in_progress', NULL),
	(8, 3, 'Plan Estrategico Google 2025-2030: Tecnologia para Todos', 'Google', '2025-10-01 22:23:19', '2025-10-01 22:23:19', NULL, 'in_progress', 0.00),
	(9, 3, 'plan estrategico', 'gaby corp', '2025-10-23 23:06:33', '2025-10-23 23:06:33', NULL, 'in_progress', 0.00),
	(10, 1, 'BCG Test 2025-10-30 02:21:51', 'Empresa Demo BCG', '2025-10-30 01:21:51', '2025-10-30 01:21:51', NULL, 'draft', 0.00),
	(11, 10, 'Plan Estrategico para Farmacia Maria de los Angeles', 'Farmacia Maria de los Angeles', '2025-11-05 16:51:13', '2025-11-05 16:51:13', NULL, 'in_progress', 0.00);

-- Volcando estructura para tabla planmaster.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `avatar` varchar(500) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `google_id` (`google_id`),
  KEY `idx_users_email` (`email`),
  KEY `idx_users_google_id` (`google_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.users: ~13 rows (aproximadamente)
INSERT INTO `users` (`id`, `email`, `password`, `name`, `avatar`, `google_id`, `email_verified`, `verification_token`, `reset_token`, `reset_token_expires`, `created_at`, `updated_at`, `last_login`, `status`) VALUES
	(1, 'admin@planmaster.com', '$2y$10$rCgRXCL8EfE5IUvYwLBVN.6wxPoSCS9QZUTnULXwT2cH4SCcrJ9U.', 'Administrador PlanMaster', NULL, NULL, 1, NULL, NULL, NULL, '2025-09-11 19:04:33', '2025-09-11 23:02:26', NULL, 'active'),
	(2, 'fuentessebastiansa4s@gmail.com', NULL, 'Sebastian Fuentes', 'https://lh3.googleusercontent.com/a/ACg8ocLfEswYK_9p-rBZuBQE7S8VeDn8_qMdo6rVjf2vCrLvDkU9CxBg=s96-c', '118266572871877651902', 1, NULL, NULL, NULL, '2025-09-11 23:08:36', '2025-09-18 18:59:08', '2025-09-18 18:59:08', 'active'),
	(3, 'gg2022074263@virtual.upt.pe', NULL, 'GABRIELA LUZKALID GUTIERREZ MAMANI', 'https://lh3.googleusercontent.com/a/ACg8ocJjxREiRM1D_ZSObeuGt0bZFHXkv4mdBwUTp_BHwvPgg_IZxVpr=s96-c', '115944247263508584295', 1, NULL, NULL, NULL, '2025-09-11 23:10:45', '2025-10-23 23:05:32', '2025-10-23 23:05:32', 'active'),
	(4, 'chevichin2018@gmail.com', '$2y$10$TTSoSkzIGip9IATplJuHy.6Yd7WSb9vDIbU4Cu6B3Uniao05mJ3nC', 'Chebastian Ricolas', NULL, NULL, 1, NULL, NULL, NULL, '2025-09-11 23:58:53', '2025-09-11 23:59:29', '2025-09-11 23:59:29', 'active'),
	(5, 'victoraprendiendocon@gmail.com', NULL, 'Aprendiendo con Victor', 'https://lh3.googleusercontent.com/a/ACg8ocITzx8cXQonIajDFmHtppjavUgNFl2YqzWyXUmeGAps1M3WM7Q=s96-c', '115289334880461933766', 1, NULL, NULL, NULL, '2025-09-12 00:04:32', '2025-09-12 00:04:32', '2025-09-12 00:04:32', 'active'),
	(6, 'nkmelndz@gmail.com', '$2y$10$CZxOAbvuR47a/5rfZ/zqL.TTC4msAvG1WNF.CeLGXKGTPhPlOyQQ.', 'nikolas', NULL, NULL, 1, NULL, NULL, NULL, '2025-09-17 21:59:04', '2025-10-23 22:47:18', '2025-10-23 22:47:18', 'active'),
	(7, 'sf2022073902@virtual.upt.pe', NULL, 'SEBASTIAN NICOLAS FUENTES AVALOS', 'https://lh3.googleusercontent.com/a/ACg8ocIldVbBQckiP7rwOIKiNWrDyrMX8yoUr2wjceuxppk4ahCQpm0=s96-c', '118030351119923353936', 1, NULL, NULL, NULL, '2025-09-17 21:59:09', '2025-10-22 23:55:08', '2025-10-22 23:55:08', 'active'),
	(8, 'ferquatck@gmail.com', NULL, 'fer ,', 'https://lh3.googleusercontent.com/a/ACg8ocJwB9Y4ST5t74ag0w5PyB7qshajRj4NsO-1HvO7QsUIOizrBg=s96-c', '108307062242127529441', 1, NULL, NULL, NULL, '2025-09-17 22:01:35', '2025-09-17 22:01:35', '2025-09-17 22:01:35', 'active'),
	(9, 'cescamac@upt.pe', '$2y$10$KRSRaJ0qScKBdlIBKwpBwukDiVHkbC7FlEOcCdXF4QGBCjs6quv5e', 'cesar camac', NULL, NULL, 1, NULL, NULL, NULL, '2025-09-18 04:08:08', '2025-09-18 04:08:15', '2025-09-18 04:08:15', 'active'),
	(10, 'gagaga@email.com', '$2y$10$S58/gIoNoC9ruw9dia59sOpduIAYei2QBiNMEMwwuoyG33aV.UkdW', 'gagaga', NULL, NULL, 1, NULL, NULL, NULL, '2025-09-18 19:05:09', '2025-11-05 18:42:25', '2025-11-05 18:42:25', 'active'),
	(11, 'cc2022074262@virtual.upt.pe', NULL, 'CESAR NIKOLAS CAMAC MELENDEZ', 'https://lh3.googleusercontent.com/a/ACg8ocJ8aemfsa0JcyWht1g7g1wafmHFPaDnMqzk0JvbZnUWJ7-IDXmI=s96-c', '117081121404025596376', 1, NULL, NULL, NULL, '2025-10-22 22:55:19', '2025-10-29 22:03:30', '2025-10-29 22:03:30', 'active'),
	(12, 'nicolas@gmail.com', '$2y$10$B9D2c/HG4gBK5jm4ywZ78OZ6eL9Y/HLPp4KsMgf28KfaEAD/EipxS', 'nicolas', NULL, NULL, 1, NULL, NULL, NULL, '2025-10-29 22:27:19', '2025-10-29 22:27:19', NULL, 'active'),
	(13, 'nicolas@email.com', '$2y$10$gAzRBXs0em/rPVnOdZr2.OJKsKFidIaJrTqAICclH0v0TxAkDVInm', 'nicolas', NULL, NULL, 1, NULL, NULL, NULL, '2025-10-29 22:28:26', '2025-10-29 22:28:26', NULL, 'active');

-- Volcando estructura para tabla planmaster.user_sessions
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx_sessions_user_id` (`user_id`),
  KEY `idx_sessions_expires` (`expires_at`),
  CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla planmaster.user_sessions: ~0 rows (aproximadamente)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
