-- Script SQL para agregar tabla de Estrategias a PlanMaster
-- Creado: Octubre 2025

USE planmaster;

-- Tabla de estrategias del proyecto
CREATE TABLE IF NOT EXISTS project_strategies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    category ENUM('competitive', 'growth', 'innovation', 'differentiation') NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('alta', 'media', 'baja') NOT NULL DEFAULT 'media',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES strategic_projects(id) ON DELETE CASCADE,
    INDEX idx_project_strategies_project (project_id),
    INDEX idx_project_strategies_category (project_id, category),
    INDEX idx_project_strategies_priority (project_id, priority)
);

-- Comentarios sobre las categorías:
-- competitive: Estrategias competitivas (liderazgo en costos, diferenciación, enfoque)
-- growth: Estrategias de crecimiento (expansión, nuevos mercados, nuevos productos)
-- innovation: Estrategias de innovación (I+D, tecnología, procesos)
-- differentiation: Estrategias de diferenciación (servicio, calidad, marca)

COMMIT;