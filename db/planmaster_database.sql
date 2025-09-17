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

-- Tabla de misión del proyecto
CREATE TABLE IF NOT EXISTS project_mission (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    mission_text TEXT NOT NULL,
    is_completed TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES strategic_projects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_mission (project_id)
);

-- Tabla de visión del proyecto
CREATE TABLE IF NOT EXISTS project_vision (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    vision_text TEXT NOT NULL,
    is_completed TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES strategic_projects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_vision (project_id)
);

-- Tabla de valores del proyecto
CREATE TABLE IF NOT EXISTS project_values (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    value_text VARCHAR(255) NOT NULL,
    value_order INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES strategic_projects(id) ON DELETE CASCADE,
    INDEX idx_project_values (project_id, value_order)
);

-- Tabla de objetivos estratégicos (generales)
CREATE TABLE IF NOT EXISTS project_strategic_objectives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    objective_title VARCHAR(255) NOT NULL,
    objective_description TEXT,
    objective_order INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES strategic_projects(id) ON DELETE CASCADE,
    INDEX idx_project_objectives (project_id, objective_order)
);

-- Tabla de objetivos específicos
CREATE TABLE IF NOT EXISTS project_specific_objectives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    strategic_objective_id INT NOT NULL,
    objective_title VARCHAR(255) NOT NULL,
    objective_description TEXT,
    objective_order INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (strategic_objective_id) REFERENCES project_strategic_objectives(id) ON DELETE CASCADE,
    INDEX idx_specific_objectives (strategic_objective_id, objective_order)
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
CREATE INDEX idx_sessions_user_id ON user_sessions(user_id);
CREATE INDEX idx_sessions_expires ON user_sessions(expires_at);
CREATE INDEX idx_mission_project ON project_mission(project_id);
CREATE INDEX idx_vision_project ON project_vision(project_id);
CREATE INDEX idx_values_project ON project_values(project_id);
CREATE INDEX idx_strategic_obj_project ON project_strategic_objectives(project_id);
CREATE INDEX idx_specific_obj_strategic ON project_specific_objectives(strategic_objective_id);

-- Usuario administrador por defecto (contraseña: admin)
INSERT INTO users (email, password, name, email_verified, status) 
VALUES ('admin@planmaster.com', '$2y$10$rCgRXCL8EfE5IUvYwLBVN.6wxPoSCS9QZUTnULXwT2cH4SCcrJ9U.', 'Administrador PlanMaster', 1, 'active')
ON DUPLICATE KEY UPDATE 
    password = '$2y$10$rCgRXCL8EfE5IUvYwLBVN.6wxPoSCS9QZUTnULXwT2cH4SCcrJ9U.',
    name = 'Administrador PlanMaster';

COMMIT;
