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
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=282 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=269 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla planmaster.project_bcg_sector_demand
CREATE TABLE IF NOT EXISTS `project_bcg_sector_demand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `demand_year` int(11) NOT NULL DEFAULT 2023,
  `total_sector_demand` decimal(15,2) NOT NULL DEFAULT 0.00,
  `company_participation` decimal(15,2) NOT NULL DEFAULT 0.00,
  `participation_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `market_share_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_year_sector` (`product_id`,`demand_year`),
  KEY `idx_sector_demand_project` (`project_id`),
  KEY `idx_sector_demand_product` (`product_id`),
  KEY `idx_sector_demand_year` (`project_id`,`demand_year`)
) ENGINE=InnoDB AUTO_INCREMENT=188 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla planmaster.project_came_matrix
CREATE TABLE IF NOT EXISTS `project_came_matrix` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `action_type` enum('C','A','M','E') NOT NULL COMMENT 'C=Corregir debilidades, A=Afrontar amenazas, M=Mantener fortalezas, E=Explotar oportunidades',
  `action_number` int(11) NOT NULL COMMENT 'Número secuencial de la acción dentro del tipo',
  `action_text` text NOT NULL COMMENT 'Descripción de la acción a realizar',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_action` (`project_id`,`action_type`,`action_number`),
  KEY `idx_project_type` (`project_id`,`action_type`),
  KEY `idx_project_id` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Matriz CAME - Acciones estratégicas por proyecto';

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
) ENGINE=InnoDB AUTO_INCREMENT=455 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla planmaster.project_porter_analysis
CREATE TABLE IF NOT EXISTS `project_porter_analysis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `factor_category` enum('rivalidad','barreras_entrada','poder_clientes','productos_sustitutivos') NOT NULL,
  `factor_name` varchar(255) NOT NULL,
  `factor_description` text DEFAULT NULL,
  `hostil_label` varchar(100) NOT NULL,
  `favorable_label` varchar(100) NOT NULL,
  `selected_value` tinyint(4) DEFAULT NULL,
  `factor_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_factor` (`project_id`,`factor_category`,`factor_name`),
  KEY `idx_porter_project` (`project_id`),
  KEY `idx_porter_category` (`factor_category`),
  CONSTRAINT `project_porter_analysis_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=187 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla planmaster.project_porter_foda
CREATE TABLE IF NOT EXISTS `project_porter_foda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `type` enum('oportunidad','amenaza') NOT NULL,
  `item_text` text NOT NULL,
  `item_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_porter_foda_project` (`project_id`),
  KEY `idx_porter_foda_type` (`type`),
  CONSTRAINT `project_porter_foda_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla planmaster.project_strategic_analysis
CREATE TABLE IF NOT EXISTS `project_strategic_analysis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `fo_total` int(11) DEFAULT 0 COMMENT 'Total FO (Estrategia Ofensiva)',
  `fa_total` int(11) DEFAULT 0 COMMENT 'Total FA (Estrategia Defensiva)',
  `do_total` int(11) DEFAULT 0 COMMENT 'Total DO (Estrategia Adaptativa)',
  `da_total` int(11) DEFAULT 0 COMMENT 'Total DA (Estrategia Supervivencia)',
  `strategy_type` enum('Ofensiva','Defensiva','Adaptativa','Supervivencia') DEFAULT NULL,
  `strategy_description` text DEFAULT NULL,
  `max_score` int(11) DEFAULT 0,
  `is_dominant_strategy` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_analysis` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla planmaster.project_strategic_relations
CREATE TABLE IF NOT EXISTS `project_strategic_relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `relation_type` enum('FO','FA','DO','DA') NOT NULL COMMENT 'Tipo de relación estratégica',
  `fortaleza_id` int(11) DEFAULT NULL COMMENT 'ID de la fortaleza (para FO y FA)',
  `debilidad_id` int(11) DEFAULT NULL COMMENT 'ID de la debilidad (para DO y DA)',
  `oportunidad_id` int(11) DEFAULT NULL COMMENT 'ID de la oportunidad (para FO y DO)',
  `amenaza_id` int(11) DEFAULT NULL COMMENT 'ID de la amenaza (para FA y DA)',
  `value_score` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Puntuación 0-4',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_project_relation` (`project_id`,`relation_type`),
  KEY `idx_project_fortaleza` (`project_id`,`fortaleza_id`),
  KEY `idx_project_debilidad` (`project_id`,`debilidad_id`),
  KEY `idx_project_oportunidad` (`project_id`,`oportunidad_id`),
  KEY `idx_project_amenaza` (`project_id`,`amenaza_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para procedimiento planmaster.RecalculateStrategicAnalysis
DELIMITER //
CREATE PROCEDURE `RecalculateStrategicAnalysis`(IN p_project_id INT)
BEGIN
    DECLARE fo_sum, fa_sum, do_sum, da_sum INT DEFAULT 0;
    DECLARE max_total INT DEFAULT 0;
    DECLARE strategy_name VARCHAR(20);
    DECLARE strategy_desc TEXT;
    
    -- Calcular totales por tipo de estrategia
    SELECT IFNULL(SUM(value_score), 0) INTO fo_sum 
    FROM project_strategic_relations 
    WHERE project_id = p_project_id AND relation_type = 'FO';
    
    SELECT IFNULL(SUM(value_score), 0) INTO fa_sum 
    FROM project_strategic_relations 
    WHERE project_id = p_project_id AND relation_type = 'FA';
    
    SELECT IFNULL(SUM(value_score), 0) INTO do_sum 
    FROM project_strategic_relations 
    WHERE project_id = p_project_id AND relation_type = 'DO';
    
    SELECT IFNULL(SUM(value_score), 0) INTO da_sum 
    FROM project_strategic_relations 
    WHERE project_id = p_project_id AND relation_type = 'DA';
    
    -- Determinar estrategia dominante
    SET max_total = GREATEST(fo_sum, fa_sum, do_sum, da_sum);
    
    IF max_total = fo_sum THEN
        SET strategy_name = 'Ofensiva';
        SET strategy_desc = 'Deberá adoptar estrategias de crecimiento. Las fortalezas de la organización pueden aprovecharse para capitalizar las oportunidades del entorno.';
    ELSEIF max_total = fa_sum THEN
        SET strategy_name = 'Defensiva';
        SET strategy_desc = 'Deberá adoptar estrategias defensivas. Use las fortalezas para evitar o reducir el impacto de las amenazas externas.';
    ELSEIF max_total = do_sum THEN
        SET strategy_name = 'Adaptativa';
        SET strategy_desc = 'Deberá adoptar estrategias de reorientación. Supere las debilidades internas aprovechando las oportunidades externas.';
    ELSE
        SET strategy_name = 'Supervivencia';
        SET strategy_desc = 'Deberá adoptar estrategias de supervivencia. Minimice las debilidades y evite las amenazas para mantener la competitividad.';
    END IF;
    
    -- Insertar o actualizar análisis
    INSERT INTO project_strategic_analysis (
        project_id, fo_total, fa_total, do_total, da_total, 
        strategy_type, strategy_description, max_score
    ) VALUES (
        p_project_id, fo_sum, fa_sum, do_sum, da_sum,
        strategy_name, strategy_desc, max_total
    )
    ON DUPLICATE KEY UPDATE
        fo_total = fo_sum,
        fa_total = fa_sum,
        do_total = do_sum,
        da_total = da_sum,
        strategy_type = strategy_name,
        strategy_description = strategy_desc,
        max_score = max_total,
        updated_at = CURRENT_TIMESTAMP;
        
END//
DELIMITER ;

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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
