-- Script SQL para el Sistema de Análisis Estratégico
-- Fecha: 19 de noviembre de 2025
-- Descripción: Tablas para almacenar relaciones estratégicas FODA y análisis de estrategias

-- ============================================
-- Tabla: project_strategic_relations
-- Propósito: Almacenar las relaciones entre factores FODA con puntuaciones
-- ============================================
CREATE TABLE IF NOT EXISTS project_strategic_relations (
    id INT(11) NOT NULL AUTO_INCREMENT,
    project_id INT(11) NOT NULL,
    relation_type ENUM('FO', 'FA', 'DO', 'DA') NOT NULL COMMENT 'Tipo de relación estratégica: FO=Fortaleza-Oportunidad, FA=Fortaleza-Amenaza, DO=Debilidad-Oportunidad, DA=Debilidad-Amenaza',
    fortaleza_id INT(11) NULL COMMENT 'ID de la fortaleza (solo para relaciones FO y FA)',
    debilidad_id INT(11) NULL COMMENT 'ID de la debilidad (solo para relaciones DO y DA)',
    oportunidad_id INT(11) NULL COMMENT 'ID de la oportunidad (solo para relaciones FO y DO)',
    amenaza_id INT(11) NULL COMMENT 'ID de la amenaza (solo para relaciones FA y DA)',
    value_score TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Puntuación de la relación (0-4): 0=Total desacuerdo, 1=No acuerdo, 2=Acuerdo, 3=Bastante acuerdo, 4=Total acuerdo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_project_relation (project_id, relation_type),
    INDEX idx_project_fortaleza (project_id, fortaleza_id),
    INDEX idx_project_debilidad (project_id, debilidad_id),
    INDEX idx_project_oportunidad (project_id, oportunidad_id),
    INDEX idx_project_amenaza (project_id, amenaza_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Relaciones estratégicas entre factores FODA con puntuaciones del 0 al 4';

-- ============================================
-- Tabla: project_strategic_analysis
-- Propósito: Almacenar los resultados calculados del análisis estratégico
-- ============================================
CREATE TABLE IF NOT EXISTS project_strategic_analysis (
    id INT(11) NOT NULL AUTO_INCREMENT,
    project_id INT(11) NOT NULL,
    fo_total INT(11) DEFAULT 0 COMMENT 'Total puntuación Fortalezas-Oportunidades (Estrategia Ofensiva)',
    fa_total INT(11) DEFAULT 0 COMMENT 'Total puntuación Fortalezas-Amenazas (Estrategia Defensiva)',
    do_total INT(11) DEFAULT 0 COMMENT 'Total puntuación Debilidades-Oportunidades (Estrategia Adaptativa)',
    da_total INT(11) DEFAULT 0 COMMENT 'Total puntuación Debilidades-Amenazas (Estrategia Supervivencia)',
    strategy_type ENUM('Ofensiva', 'Defensiva', 'Adaptativa', 'Supervivencia') NULL COMMENT 'Tipo de estrategia recomendada basada en la puntuación más alta',
    strategy_description TEXT NULL COMMENT 'Descripción detallada de la estrategia recomendada',
    max_score INT(11) DEFAULT 0 COMMENT 'Puntuación máxima obtenida entre todas las estrategias',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_project_analysis (project_id),
    INDEX idx_project_strategy (project_id, strategy_type),
    INDEX idx_strategy_scores (fo_total, fa_total, do_total, da_total)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Análisis estratégico calculado con totales y recomendaciones por proyecto';

-- ============================================
-- Datos de ejemplo (opcional)
-- ============================================
-- (No se insertan datos automáticamente para evitar conflictos)

-- ============================================
-- Comentarios finales
-- ============================================
/*
Este script crea la estructura completa para el sistema de análisis estratégico:

1. project_strategic_relations: Almacena cada relación individual entre factores FODA
2. project_strategic_analysis: Almacena los resultados calculados y recomendaciones

Características principales:
- Validaciones de integridad referencial
- Constraints para asegurar valores válidos (0-4)
- Índices optimizados para consultas frecuentes  
- Vistas para simplificar consultas comunes
- Procedimiento almacenado para recálculo automático

Para usar este sistema:
1. Ejecutar este script en la base de datos
2. Los datos FODA deben existir en project_foda_analysis
3. Usar StrategicAnalysisController.php para gestionar los datos
4. La vista strategies.php proporciona la interfaz de usuario

Última actualización: 19 de noviembre de 2025
*/