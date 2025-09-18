<?php
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../config/url_config.php';

// Verificar que el usuario esté logueado
AuthController::requireLogin();

// Obtener datos del usuario
$user = AuthController::getCurrentUser();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PlanMaster</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo getBaseUrl(); ?>/Resources/favicon.ico">
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>
    
    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container">
            <!-- Mensaje de bienvenida -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo htmlspecialchars($_SESSION['success']); 
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <!-- Título del dashboard -->
            <div class="dashboard-header">
                <h1 class="dashboard-title">¡Hola, <?php echo htmlspecialchars($user['name']); ?>! 👋</h1>
                <p class="dashboard-subtitle">Bienvenido a tu espacio de planificación estratégica</p>
            </div>
            
            <!-- Botón para iniciar proyecto -->
            <div class="start-project-section">
                <div class="start-project-card">
                    <h2>¿Listo para comenzar?</h2>
                    <p>Inicia tu primer proyecto estratégico y completa los 11 apartados con nuestra guía paso a paso.</p>
                    <button class="btn-start-project">
                        <span class="btn-icon">🚀</span>
                        Iniciar Nuevo Proyecto
                    </button>
                </div>
            </div>

            <!-- Resumen de la aplicación -->
            <div class="app-summary">
                <div class="summary-card">
                    <div class="summary-content">
                        <h2 class="summary-title">¿Qué es PlanMaster?</h2>
                        <p class="summary-description">
                            PlanMaster es tu asistente digital para crear <strong>planes estratégicos empresariales</strong> 
                            basados en PETI (Plan Estratégico de Tecnologías de Información). Te guiamos paso a paso 
                            para completar los 11 apartados esenciales de tu estrategia empresarial.
                        </p>
                        
                        <div class="features-grid">
                            <div class="feature-item">
                                <div class="feature-icon">📋</div>
                                <div class="feature-content">
                                    <h3>11 Apartados Completos</h3>
                                    <p>Desde misión y visión hasta matrices estratégicas</p>
                                </div>
                            </div>
                            
                            <div class="feature-item">
                                <div class="feature-icon">🎯</div>
                                <div class="feature-content">
                                    <h3>Guía Paso a Paso</h3>
                                    <p>Formularios intuitivos y estructurados</p>
                                </div>
                            </div>
                            
                            <div class="feature-item">
                                <div class="feature-icon">📊</div>
                                <div class="feature-content">
                                    <h3>Análisis Profundo</h3>
                                    <p>Matrices BCG, Porter, PEST y CAME</p>
                                </div>
                            </div>
                            
                            <div class="feature-item">
                                <div class="feature-icon">💾</div>
                                <div class="feature-content">
                                    <h3>Guardado Automático</h3>
                                    <p>Tu progreso se guarda en tiempo real</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Los 11 apartados del plan estratégico -->
            <div class="strategic-sections">
                <h2 class="sections-title">Los 11 Apartados de tu Plan Estratégico</h2>
                
                <div class="sections-grid">
                    <div class="section-card">
                        <div class="section-number">1</div>
                        <h3>Misión</h3>
                        <p>Define el propósito fundamental de tu organización</p>
                    </div>
                    
                    <div class="section-card">
                        <div class="section-number">2</div>
                        <h3>Visión</h3>
                        <p>Establece hacia dónde quieres que vaya tu empresa</p>
                    </div>
                    
                    <div class="section-card">
                        <div class="section-number">3</div>
                        <h3>Valores</h3>
                        <p>Los principios que guían tu organización</p>
                    </div>
                    
                    <div class="section-card">
                        <div class="section-number">4</div>
                        <h3>Objetivos</h3>
                        <p>Metas específicas y medibles a alcanzar</p>
                    </div>
                    
                    <div class="section-card">
                        <div class="section-number">5</div>
                        <h3>Análisis Interno y Externo</h3>
                        <p>Evaluación completa del entorno empresarial</p>
                    </div>
                    
                    <div class="section-card">
                        <div class="section-number">6</div>
                        <h3>Cadena de Valor</h3>
                        <p>Análisis de los procesos que agregan valor</p>
                    </div>
                    
                    <div class="section-card">
                        <div class="section-number">7</div>
                        <h3>Matriz BCG</h3>
                        <p>Matriz de crecimiento y participación</p>
                    </div>
                    
                    <div class="section-card">
                        <div class="section-number">8</div>
                        <h3>Matriz de Porter</h3>
                        <p>Análisis del microentorno competitivo</p>
                    </div>
                    
                    <div class="section-card">
                        <div class="section-number">9</div>
                        <h3>Análisis PEST</h3>
                        <p>Factores políticos, económicos, sociales y tecnológicos</p>
                    </div>
                    
                    <div class="section-card">
                        <div class="section-number">10</div>
                        <h3>Identificación de Estrategias</h3>
                        <p>Desarrollo de estrategias competitivas</p>
                    </div>
                    
                    <div class="section-card">
                        <div class="section-number">11</div>
                        <h3>Matriz CAME</h3>
                        <p>Corregir, Afrontar, Mantener, Explotar</p>
                    </div>
                </div>
            </div>
            
            <!-- Proyectos existentes (placeholder) -->
            <div class="existing-projects">
                <h2>Tus Proyectos</h2>
                <div class="projects-container">
                    <div class="empty-projects">
                        <div class="empty-icon">📁</div>
                        <p>Aún no tienes proyectos creados</p>
                        <small>Inicia tu primer proyecto estratégico usando el botón de arriba</small>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include 'footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="<?php echo getBaseUrl(); ?>/Publics/js/dashboard.js"></script>
</body>
</html>
