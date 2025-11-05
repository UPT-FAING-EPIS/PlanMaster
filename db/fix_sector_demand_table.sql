-- ===============================================
-- CORRECCIÓN TABLA DEMANDA SECTORIAL - BCG SYSTEM
-- Agregar campo para año y permitir múltiples registros por producto/año
-- ===============================================

-- 1. Agregar campo demand_year a la tabla existente
-- NOTA: DEFAULT 2023 es solo para datos existentes, el sistema guardará cualquier año dinámicamente
ALTER TABLE project_bcg_sector_demand 
ADD COLUMN demand_year INT NOT NULL DEFAULT 2023 AFTER product_id;

-- 2. Eliminar la restricción única actual (solo product_id)
ALTER TABLE project_bcg_sector_demand 
DROP INDEX unique_product_sector;

-- 3. Crear nueva restricción única compuesta (product_id + demand_year)
ALTER TABLE project_bcg_sector_demand 
ADD UNIQUE KEY unique_product_year_sector (product_id, demand_year);

-- 4. Agregar índice para consultas por año
ALTER TABLE project_bcg_sector_demand 
ADD KEY idx_sector_demand_year (project_id, demand_year);

-- 5. Actualizar registros existentes para tener el año actual (solo una vez)
-- Los nuevos registros se guardarán con el año que el usuario especifique dinámicamente
UPDATE project_bcg_sector_demand 
SET demand_year = 2025 
WHERE demand_year = 2023;

-- ===============================================
-- SCRIPT LISTO - Ejecutar en HeidiSQL o phpMyAdmin
-- ===============================================