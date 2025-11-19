-- Script SQL para la Matriz CAME
-- Fecha: 19 de noviembre de 2025
-- Descripción: Tabla para almacenar acciones de la matriz CAME

-- ============================================
-- Tabla: project_came_matrix
-- Propósito: Almacenar las acciones CAME por proyecto
-- ============================================
CREATE TABLE IF NOT EXISTS project_came_matrix (
    id INT(11) NOT NULL AUTO_INCREMENT,
    project_id INT(11) NOT NULL,
    action_type ENUM('C', 'A', 'M', 'E') NOT NULL COMMENT 'C=Corregir debilidades, A=Afrontar amenazas, M=Mantener fortalezas, E=Explotar oportunidades',
    action_number INT(11) NOT NULL COMMENT 'Número secuencial de la acción dentro del tipo',
    action_text TEXT NOT NULL COMMENT 'Descripción de la acción a realizar',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_project_action (project_id, action_type, action_number),
    INDEX idx_project_type (project_id, action_type),
    INDEX idx_project_id (project_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Matriz CAME - Acciones estratégicas por proyecto';