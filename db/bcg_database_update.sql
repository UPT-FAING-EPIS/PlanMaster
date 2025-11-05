-- ================================================
-- SCRIPT BCG DATABASE UPDATE - PlanMaster
-- ================================================
-- Fecha: 2024-11-29
-- Propósito: Actualizar la estructura de la BD para el nuevo sistema BCG interactivo
-- Cambios: NO se necesitan cambios - todas las tablas ya existen y están bien estructuradas

-- ================================================
-- VERIFICACIÓN DE TABLAS EXISTENTES
-- ================================================

-- Las siguientes tablas YA EXISTEN y están correctamente estructuradas:

-- ✅ project_bcg_analysis (tabla principal del análisis)
-- ✅ project_bcg_products (productos con ventas y métricas)  
-- ✅ project_bcg_market_growth (períodos TCM por producto)
-- ✅ project_bcg_competitors (competidores por producto)
-- ✅ project_bcg_sector_demand (demanda sectorial por producto)
-- ✅ project_bcg_matrix_results (resultados y posicionamiento BCG)
-- ✅ project_bcg_settings (configuración del análisis)
-- ✅ project_foda_analysis (para fortalezas y debilidades)

-- ================================================
-- VERIFICAR ESTRUCTURA DE TABLAS
-- ================================================

-- Verificar si todas las tablas BCG existen
SELECT 
    TABLE_NAME,
    TABLE_COMMENT,
    CREATE_TIME
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME LIKE 'project_bcg_%'
ORDER BY TABLE_NAME;

-- ================================================
-- LIMPIEZA DE DATOS DE PRUEBA (OPCIONAL)
-- ================================================

-- Descomentar las siguientes líneas solo si necesitas limpiar datos de prueba:

-- DELETE FROM project_bcg_matrix_results WHERE project_id = 1;
-- DELETE FROM project_bcg_sector_demand WHERE project_id = 1;  
-- DELETE FROM project_bcg_market_growth WHERE project_id = 1;
-- DELETE FROM project_bcg_competitors WHERE project_id = 1;
-- DELETE FROM project_bcg_products WHERE project_id = 1;
-- DELETE FROM project_bcg_analysis WHERE project_id = 1;
-- DELETE FROM project_foda_analysis WHERE project_id = 1 AND type IN ('fortaleza', 'debilidad');

-- ================================================
-- INSERCIÓN DE DATOS DE EJEMPLO (PARA TESTING)
-- ================================================

-- Crear un análisis BCG de ejemplo para project_id = 1
INSERT INTO project_bcg_analysis (
    project_id, 
    analysis_name, 
    analysis_status,
    total_sales_forecast,
    average_tcm,
    average_prm
) VALUES (
    1,
    'Análisis BCG Interactivo',
    'in_progress',
    0.00,
    0.00,
    0.00
) ON DUPLICATE KEY UPDATE
    analysis_name = VALUES(analysis_name),
    updated_at = CURRENT_TIMESTAMP;

-- ================================================
-- VERIFICACIÓN FINAL
-- ================================================

-- Mostrar estructura de tablas principales
DESCRIBE project_bcg_products;
DESCRIBE project_bcg_market_growth;
DESCRIBE project_bcg_competitors;

-- Verificar datos insertados
SELECT 
    p.id as project_id,
    p.project_name,
    b.analysis_name,
    b.analysis_status,
    b.created_at
FROM strategic_projects p
LEFT JOIN project_bcg_analysis b ON p.id = b.project_id
WHERE p.id = 1;

-- ================================================
-- RESUMEN DE CAMBIOS
-- ================================================

/*
🔹 TABLAS REFORMULADAS: NINGUNA - Todas las tablas ya están correctamente estructuradas
🔹 TABLAS ELIMINADAS: NINGUNA - No se requiere eliminar tablas
🔹 TABLAS AUMENTADAS: NINGUNA - La estructura actual es completa y funcional

📋 ESTRUCTURA FINAL:
   - project_bcg_analysis: Análisis principal
   - project_bcg_products: Productos y ventas
   - project_bcg_market_growth: Períodos TCM
   - project_bcg_competitors: Competidores por producto
   - project_bcg_sector_demand: Demanda sectorial 
   - project_bcg_matrix_results: Resultados BCG
   - project_bcg_settings: Configuración
   - project_foda_analysis: Fortalezas/Debilidades

✅ La base de datos está lista para el sistema BCG interactivo
✅ No se necesitan modificaciones estructurales
✅ Solo se requiere implementar las funciones save/load en el modelo PHP
*/