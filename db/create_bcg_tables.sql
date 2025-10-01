-- Tabla para almacenar análisis BCG
CREATE TABLE project_bcg_analysis (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES strategic_projects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_bcg (project_id)
);

-- Tabla para productos en análisis BCG
CREATE TABLE project_bcg_products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    sales_forecast DECIMAL(15,2) NOT NULL DEFAULT 0,
    tcm_rate DECIMAL(5,2) NOT NULL DEFAULT 0, -- Tasa de Crecimiento del Mercado
    prm_rate DECIMAL(5,2) NOT NULL DEFAULT 0, -- Participación Relativa del Mercado
    product_order INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES strategic_projects(id) ON DELETE CASCADE,
    INDEX idx_project_id (project_id)
);

-- Tabla para evolución de demanda por producto
CREATE TABLE project_bcg_market_evolution (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    year INT NOT NULL,
    market_value DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES project_bcg_products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_year (product_id, year)
);

-- Tabla para competidores por producto
CREATE TABLE project_bcg_competitors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    competitor_name VARCHAR(255) NOT NULL,
    sales_value DECIMAL(15,2) NOT NULL DEFAULT 0,
    competitor_order INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES project_bcg_products(id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id)
);