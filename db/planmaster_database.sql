-- Base de datos para PlanMaster
-- Creado: 11 de septiembre de 2025

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS planmaster CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE planmaster;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NULL, -- NULL para usuarios de Google
    name VARCHAR(255) NOT NULL,
    avatar VARCHAR(500) NULL,
    google_id VARCHAR(255) NULL UNIQUE,
    email_verified TINYINT(1) DEFAULT 0,
    verification_token VARCHAR(255) NULL,
    reset_token VARCHAR(255) NULL,
    reset_token_expires DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active'
);

-- Tabla de proyectos estratégicos
CREATE TABLE IF NOT EXISTS strategic_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    project_name VARCHAR(255) NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    status ENUM('draft', 'in_progress', 'completed') DEFAULT 'draft',
    progress_percentage DECIMAL(5,2) DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabla de secciones del plan estratégico
CREATE TABLE IF NOT EXISTS project_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    section_type ENUM('mision', 'vision', 'valores', 'objetivos', 'analisis_interno_externo', 
                     'cadena_valor', 'matriz_bcg', 'matriz_porter', 'analisis_pest', 
                     'estrategias', 'matriz_came') NOT NULL,
    section_title VARCHAR(255) NOT NULL,
    section_content TEXT,
    is_completed TINYINT(1) DEFAULT 0,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES strategic_projects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_section (project_id, section_type)
);

-- Tabla de sesiones (opcional para manejo de sesiones)
CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Índices para optimización
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_google_id ON users(google_id);
CREATE INDEX idx_projects_user_id ON strategic_projects(user_id);
CREATE INDEX idx_projects_status ON strategic_projects(status);
CREATE INDEX idx_sections_project_id ON project_sections(project_id);
CREATE INDEX idx_sections_type ON project_sections(section_type);
CREATE INDEX idx_sessions_user_id ON user_sessions(user_id);
CREATE INDEX idx_sessions_expires ON user_sessions(expires_at);

-- Usuario administrador por defecto (contraseña: admin)
INSERT INTO users (email, password, name, email_verified, status) 
VALUES ('admin@planmaster.com', '$2y$10$rCgRXCL8EfE5IUvYwLBVN.6wxPoSCS9QZUTnULXwT2cH4SCcrJ9U.', 'Administrador PlanMaster', 1, 'active')
ON DUPLICATE KEY UPDATE 
    password = '$2y$10$rCgRXCL8EfE5IUvYwLBVN.6wxPoSCS9QZUTnULXwT2cH4SCcrJ9U.',
    name = 'Administrador PlanMaster';

-- Datos de ejemplo para las secciones del plan estratégico
INSERT INTO project_sections (project_id, section_type, section_title, section_content, is_completed) 
SELECT 1, 'mision', 'Misión de la Empresa', 'Ejemplo de misión empresarial...', 0
FROM DUAL 
WHERE NOT EXISTS (SELECT 1 FROM project_sections WHERE project_id = 1 AND section_type = 'mision');

COMMIT;
