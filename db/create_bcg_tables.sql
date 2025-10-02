-- Tabla principal para análisis BCG (Mini Paso 1: PREVISIÓN DE VENTAS)
CREATE TABLE project_bcg_analysis (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES strategic_projects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_bcg (project_id)
);

-- Tabla para productos y ventas (Mini Paso 1: PREVISIÓN DE VENTAS)
CREATE TABLE project_bcg_products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    sales_forecast DECIMAL(15,2) NOT NULL DEFAULT 0,
    sales_percentage DECIMAL(5,2) NOT NULL DEFAULT 0, -- % sobre total calculado automáticamente
    tcm_calculated DECIMAL(5,2) NOT NULL DEFAULT 0, -- TCM calculado con fórmula Excel
    prm_calculated DECIMAL(5,2) NOT NULL DEFAULT 0, -- PRM calculado con fórmula Excel
    bcg_position VARCHAR(20) DEFAULT NULL, -- estrella, interrogante, vaca, perro
    product_order INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES strategic_projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id)
);

-- Tabla para períodos TCM (Mini Paso 2: TASAS DE CRECIMIENTO DEL MERCADO)
CREATE TABLE project_bcg_market_evolution (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    product_id INT NOT NULL,
    period_start_year INT NOT NULL,
    period_end_year INT NOT NULL,
    tcm_percentage DECIMAL(5,2) NOT NULL DEFAULT 0, -- Porcentaje TCM ingresado por usuario
    period_order INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES strategic_projects(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES project_bcg_products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_period (product_id, period_start_year, period_end_year),
    INDEX idx_project_id (project_id),
    INDEX idx_product_id (product_id)
);

-- Tabla para competidores por producto (Mini Paso 4: NIVELES DE VENTA DE LOS COMPETIDORES)
CREATE TABLE project_bcg_competitors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    product_id INT NOT NULL,
    competitor_name VARCHAR(255) NOT NULL,
    competitor_sales DECIMAL(15,2) NOT NULL DEFAULT 0,
    is_max_competitor TINYINT(1) DEFAULT 0, -- 1 si es el competidor con mayores ventas
    competitor_order INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES strategic_projects(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES project_bcg_products(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id),
    INDEX idx_product_id (product_id)
);