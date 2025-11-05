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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

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
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

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

-- La exportación de datos fue deseleccionada.

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

-- La exportación de datos fue deseleccionada.

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

-- La exportación de datos fue deseleccionada.

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

-- La exportación de datos fue deseleccionada.

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

-- La exportación de datos fue deseleccionada.

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

-- La exportación de datos fue deseleccionada.

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

-- La exportación de datos fue deseleccionada.

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

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

-- La exportación de datos fue deseleccionada.

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

-- La exportación de datos fue deseleccionada.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
