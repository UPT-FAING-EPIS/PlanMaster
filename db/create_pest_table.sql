-- ============================================================
-- TABLA PARA ANÁLISIS PEST
-- ============================================================

-- Estructura para tabla project_pest_analysis
CREATE TABLE IF NOT EXISTS `project_pest_analysis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `question_number` int NOT NULL,
  `rating` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_pest_question` (`project_id`,`question_number`),
  KEY `idx_project_pest_id` (`project_id`),
  KEY `idx_question_number` (`question_number`),
  CONSTRAINT `project_pest_analysis_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `strategic_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_pest_analysis_chk_1` CHECK ((`question_number` between 1 and 25)),
  CONSTRAINT `project_pest_analysis_chk_2` CHECK ((`rating` between 0 and 4))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comentarios sobre la estructura:
-- - id: Clave primaria auto-incremental
-- - project_id: Referencia al proyecto (FK a strategic_projects)
-- - question_number: Número de pregunta (1-25)
-- - rating: Calificación del 0 al 4
-- - created_at: Fecha de creación
-- - updated_at: Fecha de última actualización
-- - unique_project_pest_question: Evita duplicados de pregunta por proyecto
-- - Constraints: Validan rango de preguntas (1-25) y ratings (0-4)

-- Índices para optimización de consultas:
-- - PRIMARY KEY en id
-- - UNIQUE KEY para evitar duplicados proyecto-pregunta
-- - INDEX en project_id para consultas rápidas por proyecto
-- - INDEX en question_number para análisis por pregunta

-- Ejemplo de inserción de datos:
-- INSERT INTO project_pest_analysis (project_id, question_number, rating) 
-- VALUES (1, 1, 3), (1, 2, 4), (1, 3, 2);

-- Consulta para obtener resumen por proyecto:
-- SELECT 
--     COUNT(*) as total_questions,
--     SUM(rating) as total_rating,
--     AVG(rating) as average_rating,
--     MAX(rating) as max_rating,
--     MIN(rating) as min_rating
-- FROM project_pest_analysis 
-- WHERE project_id = ?;

-- Consulta para obtener análisis por categorías PEST:
-- Político (P): preguntas 6-10
-- Económico (E): preguntas 11-15  
-- Social (S): preguntas 1-5, 21-25
-- Tecnológico (T): preguntas 16-20