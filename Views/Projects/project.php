<?php
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../config/url_config.php';

// Verificar que el usuario esté logueado
AuthController::requireLogin();

// Obtener ID del proyecto
$project_id = $_GET['id'] ?? null;
if (!$project_id) {
    header("Location: ../Users/projects.php");
    exit();
}

// Obtener datos del usuario y proyecto
$user = AuthController::getCurrentUser();
$projectController = new ProjectController();
$project = $projectController->getProject($project_id);

if (!$project) {
    $_SESSION['error'] = "Proyecto no encontrado";
    header("Location: ../Users/projects.php");
    exit();
}

// Obtener progreso del proyecto
$progress = $projectController->getProjectProgress($project_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['project_name']); ?> - PlanMaster</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_project.css">
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
            <!-- Header del proyecto -->
            <div class="project-header-section">
                <div class="project-info">
                    <h1 class="project-title"><?php echo htmlspecialchars($project['project_name']); ?></h1>
                    <p class="company-title"><?php echo htmlspecialchars($project['company_name']); ?></p>
                </div>
                
                <div class="project-progress-circle">
                    <div class="circle-progress" data-progress="<?php echo round($progress['percentage']); ?>">
                        <div class="circle-inner">
                            <span class="progress-value"><?php echo round($progress['percentage']); ?>%</span>
                            <span class="progress-label">Completado</span>
                        </div>
                    </div>
                </div>
                
                <div class="project-actions-header">
                    <button class="btn-save-exit" onclick="saveAndExit()">
                        <span class="btn-icon">💾</span>
                        Salir y Guardar
                    </button>
                </div>
            </div>
            
            <!-- Progreso detallado -->
            <div class="progress-section">
                <h2>Progreso del Plan Estratégico</h2>
                <div class="progress-details">
                    <span><?php echo $progress['completed']; ?> de <?php echo $progress['total']; ?> secciones completadas</span>
                </div>
                <div class="progress-bar-full">
                    <div class="progress-fill" style="width: <?php echo $progress['percentage']; ?>%"></div>
                </div>
            </div>
            
            <!-- Los 11 apartados del plan estratégico -->
            <div class="strategic-sections">
                <h2 class="sections-title">Apartados del Plan Estratégico</h2>
                
                <div class="sections-grid">
                    <?php 
                    $sections = [
                        1 => ['title' => 'Misión', 'description' => 'Define el propósito fundamental de tu empresa', 'icon' => '🎯', 'key' => 'mission'],
                        2 => ['title' => 'Visión', 'description' => 'Establece hacia dónde quiere llegar tu empresa', 'icon' => '🔮', 'key' => 'vision'],
                        3 => ['title' => 'Valores', 'description' => 'Los principios que guían tu organización', 'icon' => '⭐', 'key' => 'values'],
                        4 => ['title' => 'Objetivos', 'description' => 'Metas específicas y medibles', 'icon' => '🎯', 'key' => 'objectives'],
                        5 => ['title' => 'Análisis FODA', 'description' => 'Fortalezas, Oportunidades, Debilidades, Amenazas', 'icon' => '�', 'key' => 'foda_analysis'],
                        6 => ['title' => 'Cadena de Valor', 'description' => 'Actividades que generan valor', 'icon' => '⛓️', 'key' => 'value_chain'],
                        7 => ['title' => 'Matriz BCG', 'description' => 'Análisis de cartera de productos', 'icon' => '📊', 'key' => 'bcg_analysis'],
                        8 => ['title' => 'Matriz de Porter', 'description' => 'Cinco fuerzas competitivas', 'icon' => '🏛️', 'key' => 'porter_matrix'],
                        9 => ['title' => 'Análisis PEST', 'description' => 'Factores políticos, económicos, sociales y tecnológicos', 'icon' => '🌍', 'key' => 'pest_analysis'],
                        10 => ['title' => 'Estrategias', 'description' => 'Identificación de estrategias clave', 'icon' => '🧠', 'key' => 'strategies'],
                        11 => ['title' => 'Matriz CAME', 'description' => 'Corregir, Afrontar, Mantener, Explotar', 'icon' => '�', 'key' => 'came_matrix']
                    ];
                    
                    foreach ($sections as $number => $section): 
                        $isCompleted = isset($progress['sections'][$section['key']]) && $progress['sections'][$section['key']];
                        $statusClass = $isCompleted ? 'completed' : 'pending';
                        $statusIcon = $isCompleted ? '✅' : '⏳';
                    ?>
                    <div class="section-card <?php echo $statusClass; ?>" data-section="<?php echo $number; ?>">
                        <div class="section-header">
                            <div class="section-number"><?php echo $number; ?></div>
                            <div class="section-status"><?php echo $statusIcon; ?></div>
                        </div>
                        
                        <div class="section-content">
                            <div class="section-icon"><?php echo $section['icon']; ?></div>
                            <h3 class="section-title"><?php echo $section['title']; ?></h3>
                            <p class="section-description"><?php echo $section['description']; ?></p>
                        </div>
                        
                        <div class="section-actions">
                            <?php if ($isCompleted): ?>
                                <button class="btn-edit-section" onclick="editSection(<?php echo $number; ?>)">
                                    <span class="btn-icon">✏️</span>
                                    Editar
                                </button>
                            <?php else: ?>
                                <button class="btn-start-section" onclick="startSection(<?php echo $number; ?>)">
                                    <span class="btn-icon">▶️</span>
                                    Comenzar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include '../Users/footer.php'; ?>
    
    <!-- JavaScript -->
    <script>
        // Datos del proyecto
        const projectData = <?php echo json_encode($project); ?>;
        const progressData = <?php echo json_encode($progress); ?>;
    </script>
    <script src="<?php echo getBaseUrl(); ?>/Publics/js/dashboard.js"></script>
    <script src="<?php echo getBaseUrl(); ?>/Publics/js/project.js"></script>
</body>
</html>