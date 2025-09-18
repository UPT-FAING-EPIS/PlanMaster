<?php
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../config/url_config.php';

// Verificar que el usuario esté logueado
AuthController::requireLogin();

// Obtener datos del usuario
$user = AuthController::getCurrentUser();

// Obtener proyectos del usuario
$projectController = new ProjectController();
$projects = $projectController->getUserProjects();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Proyectos - PlanMaster</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_projects.css">
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
            <!-- Título de la página -->
            <div class="page-header">
                <h1 class="page-title">📊 Mis Proyectos</h1>
                <p class="page-subtitle">Administra y continúa tus planes estratégicos empresariales</p>
            </div>
            
            <!-- Acciones rápidas -->
            <div class="quick-actions">
                <button class="btn-new-project" onclick="startNewProject()">
                    <span class="btn-icon">🚀</span>
                    Nuevo Proyecto
                </button>
            </div>
            
            <!-- Lista de proyectos -->
            <div class="projects-container">
                <?php if (empty($projects)): ?>
                    <!-- Estado vacío -->
                    <div class="empty-state">
                        <div class="empty-icon">📁</div>
                        <h3>No tienes proyectos aún</h3>
                        <p>Crea tu primer plan estratégico empresarial y comienza a organizar el futuro de tu empresa.</p>
                        <button class="btn-start-first-project" onclick="startNewProject()">
                            <span class="btn-icon">🚀</span>
                            Crear Mi Primer Proyecto
                        </button>
                    </div>
                <?php else: ?>
                    <div class="projects-grid">
                        <?php foreach ($projects as $project): ?>
                            <?php 
                            $progress = $projectController->getProjectProgress($project['id']);
                            $statusClass = '';
                            $statusText = '';
                            
                            switch ($project['status']) {
                                case 'completed':
                                    $statusClass = 'status-completed';
                                    $statusText = 'Completado';
                                    break;
                                case 'in_progress':
                                    $statusClass = 'status-in-progress';
                                    $statusText = 'En Progreso';
                                    break;
                                default:
                                    $statusClass = 'status-draft';
                                    $statusText = 'Borrador';
                                    break;
                            }
                            ?>
                            <div class="project-card">
                                <div class="project-header">
                                    <div class="project-info">
                                        <h3 class="project-name"><?php echo htmlspecialchars($project['project_name'] ?? 'Sin nombre'); ?></h3>
                                        <p class="company-name"><?php echo htmlspecialchars($project['company_name'] ?? 'Sin empresa'); ?></p>
                                    </div>
                                    <span class="project-status <?php echo $statusClass; ?>">
                                        <?php echo $statusText; ?>
                                    </span>
                                </div>
                                
                                <div class="project-progress">
                                    <div class="progress-info">
                                        <span class="progress-text">Progreso</span>
                                        <span class="progress-percentage"><?php echo round($progress['percentage'] ?? 0); ?>%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo ($progress['percentage'] ?? 0); ?>%"></div>
                                    </div>
                                    <div class="progress-details">
                                        <small><?php echo ($progress['completed'] ?? 0); ?> de <?php echo ($progress['total'] ?? 4); ?> secciones completadas</small>
                                    </div>
                                </div>
                                
                                <div class="project-dates">
                                    <div class="date-item">
                                        <span class="date-label">Creado:</span>
                                        <span class="date-value"><?php echo date('d/m/Y', strtotime($project['created_at'])); ?></span>
                                    </div>
                                    <?php if ($project['updated_at']): ?>
                                    <div class="date-item">
                                        <span class="date-label">Actualizado:</span>
                                        <span class="date-value"><?php echo date('d/m/Y', strtotime($project['updated_at'])); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="project-actions">
                                    <button class="btn-continue" onclick="continueProject(<?php echo $project['id']; ?>)">
                                        <span class="btn-icon">▶️</span>
                                        Continuar
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include 'footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="<?php echo getBaseUrl(); ?>/Publics/js/dashboard.js"></script>
    <script src="<?php echo getBaseUrl(); ?>/Publics/js/projects.js"></script>
</body>
</html>