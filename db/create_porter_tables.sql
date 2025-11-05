-- ===============================================
-- TABLA PARA MATRIZ DE PORTER
-- Sistema de Plan Estratégico - PlanMaster
-- ===============================================

-- Tabla para almacenar análisis de Matriz de Porter
CREATE TABLE IF NOT EXISTS `project_porter_analysis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `factor_category` enum('rivalidad', 'barreras_entrada', 'poder_clientes', 'productos_sustitutivos') NOT NULL,
  `factor_name` varchar(255) NOT NULL,
  `factor_description` text DEFAULT NULL,
  `hostil_label` varchar(100) NOT NULL,
  `favorable_label` varchar(100) NOT NULL,
  `selected_value` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Nada, 2=Poco, 3=Medio, 4=Alto, 5=Muy Alto',
  `factor_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_factor` (`project_id`, `factor_category`, `factor_name`),
  KEY `idx_porter_project` (`project_id`),
  KEY `idx_porter_category` (`factor_category`),
  CONSTRAINT `project_porter_analysis_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla para almacenar oportunidades y amenazas derivadas del análisis Porter
CREATE TABLE IF NOT EXISTS `project_porter_foda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `type` enum('oportunidad', 'amenaza') NOT NULL,
  `item_text` text NOT NULL,
  `item_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_porter_foda_project` (`project_id`),
  KEY `idx_porter_foda_type` (`type`),
  CONSTRAINT `project_porter_foda_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertar datos estándar para todos los proyectos (factores predefinidos)
-- NOTA: Estos se insertarán dinámicamente cuando se cree un análisis Porter para un proyecto

-- ===============================================
-- ESTRUCTURA LISTA PARA MATRIZ DE PORTER
-- ===============================================