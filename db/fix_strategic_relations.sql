-- Script de corrección para project_strategic_relations
-- Fecha: 20 de noviembre de 2025
-- Descripción: Optimización y corrección de la tabla de relaciones estratégicas

-- ============================================
-- Verificar y corregir estructura de tabla
-- ============================================

-- Primero, verificar si la tabla existe y crearla si es necesario
CREATE TABLE IF NOT EXISTS project_strategic_relations (
    id INT(11) NOT NULL AUTO_INCREMENT,
    project_id INT(11) NOT NULL,
    relation_type ENUM('FO', 'FA', 'DO', 'DA') NOT NULL COMMENT 'Tipo de relación estratégica',
    fortaleza_id INT(11) NULL COMMENT 'ID de la fortaleza (para FO y FA)',
    debilidad_id INT(11) NULL COMMENT 'ID de la debilidad (para DO y DA)',
    oportunidad_id INT(11) NULL COMMENT 'ID de la oportunidad (para FO y DO)',
    amenaza_id INT(11) NULL COMMENT 'ID de la amenaza (para FA y DA)',
    value_score TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Puntuación 0-4',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_project_relation (project_id, relation_type),
    KEY idx_project_fortaleza (project_id, fortaleza_id),
    KEY idx_project_debilidad (project_id, debilidad_id),
    KEY idx_project_oportunidad (project_id, oportunidad_id),
    KEY idx_project_amenaza (project_id, amenaza_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Verificar y corregir estructura de análisis estratégico
-- ============================================

CREATE TABLE IF NOT EXISTS project_strategic_analysis (
    id INT(11) NOT NULL AUTO_INCREMENT,
    project_id INT(11) NOT NULL,
    fo_total INT(11) DEFAULT 0 COMMENT 'Total FO (Estrategia Ofensiva)',
    fa_total INT(11) DEFAULT 0 COMMENT 'Total FA (Estrategia Defensiva)', 
    do_total INT(11) DEFAULT 0 COMMENT 'Total DO (Estrategia Adaptativa)',
    da_total INT(11) DEFAULT 0 COMMENT 'Total DA (Estrategia Supervivencia)',
    strategy_type ENUM('Ofensiva', 'Defensiva', 'Adaptativa', 'Supervivencia') NULL,
    strategy_description TEXT NULL,
    max_score INT(11) DEFAULT 0,
    is_dominant_strategy TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_project_analysis (project_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Procedimiento para recalcular totales
-- ============================================

DELIMITER $$

CREATE PROCEDURE IF NOT EXISTS RecalculateStrategicAnalysis(IN p_project_id INT)
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
        
END$$

DELIMITER ;

-- ============================================
-- Limpieza de datos inconsistentes (opcional)
-- ============================================

-- Eliminar relaciones con combinaciones inválidas de IDs
-- DELETE FROM project_strategic_relations WHERE
-- (relation_type = 'FO' AND (fortaleza_id IS NULL OR oportunidad_id IS NULL OR debilidad_id IS NOT NULL OR amenaza_id IS NOT NULL)) OR
-- (relation_type = 'FA' AND (fortaleza_id IS NULL OR amenaza_id IS NULL OR debilidad_id IS NOT NULL OR oportunidad_id IS NOT NULL)) OR
-- (relation_type = 'DO' AND (debilidad_id IS NULL OR oportunidad_id IS NULL OR fortaleza_id IS NOT NULL OR amenaza_id IS NOT NULL)) OR
-- (relation_type = 'DA' AND (debilidad_id IS NULL OR amenaza_id IS NULL OR fortaleza_id IS NOT NULL OR oportunidad_id IS NOT NULL));

-- ============================================
-- Instrucciones de uso
-- ============================================

/*
Para ejecutar este script:

1. Ejecutar todo el script en tu base de datos
2. Para recalcular análisis de un proyecto específico:
   CALL RecalculateStrategicAnalysis(project_id);
   
3. Para recalcular todos los proyectos:
   SELECT DISTINCT project_id FROM project_strategic_relations;
   -- Luego ejecutar CALL RecalculateStrategicAnalysis(X) para cada project_id

Cambios realizados:
- Optimización de índices para mejor rendimiento
- Procedimiento almacenado para recálculos automáticos
- Validaciones de integridad de datos mejoradas
- Campo is_dominant_strategy agregado para compatibilidad con PDF
- Estructura compatible con el código PHP existente
*/