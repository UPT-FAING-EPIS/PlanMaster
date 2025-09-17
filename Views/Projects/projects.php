<?php
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';

// Verificar que el usuario esté logueado
AuthController::requireLogin();

// Verificar que se proporcione el ID del proyecto
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID de proyecto inválido";
    header("Location: ../Users/dashboard.php");
    exit();
}

$project_id = intval($_GET['id']);
$projectController = new ProjectController();

// Obtener datos del proyecto y verificar permisos
$project = $projectController->getProject($project_id);
$user = AuthController::getCurrentUser();

// Obtener progreso del proyecto
$progress = $projectController->getProjectProgress($project_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto: <?php echo htmlspecialchars($project['project_name']); ?> - PlanMaster</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../../Publics/css/styles_projects.css">
    <link rel="stylesheet" href="../../Publics/css/styles_dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../Resources/favicon.ico">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <!-- Logo y navegación principal -->
            <div class="header-left">
                <div class="logo">
                    <a href="../Users/dashboard.php">
                        <span class="logo-text">PlanMaster</span>
                        <span class="logo-subtitle">Plan Estratégico</span>
                    </a>
                </div>
            </div>
            
            <!-- Usuario y acciones -->
            <div class="header-right">
                <!-- Información del proyecto actual -->
                <div class="current-project-info">
                    <div class="project-details">
                        <span class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></span>
                        <span class="company-name"><?php echo htmlspecialchars($project['company_name']); ?></span>
                    </div>
                    <div class="project-progress-header">
                        <div class="progress-circle">
                            <div class="progress-value"><?php echo round($progress['percentage']); ?>%</div>
                        </div>
                    </div>
                </div>
                
                <!-- Menu de usuario -->
                <div class="user-menu">
                    <div class="user-avatar" onclick="toggleUserDropdown()">
                        <?php if ($user['avatar']): ?>
                            <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar">
                        <?php else: ?>
                            <div class="avatar-placeholder">
                                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="user-info" onclick="toggleUserDropdown()">
                        <span class="user-name"><?php echo htmlspecialchars($user['name']); ?></span>
                        <span class="user-email"><?php echo htmlspecialchars($user['email']); ?></span>
                        <span class="dropdown-arrow">▼</span>
                    </div>
                    
                    <!-- Dropdown del usuario -->
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <div class="dropdown-avatar">
                                <?php if ($user['avatar']): ?>
                                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar">
                                <?php else: ?>
                                    <div class="avatar-placeholder">
                                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="dropdown-info">
                                <div class="dropdown-name"><?php echo htmlspecialchars($user['name']); ?></div>
                                <div class="dropdown-email"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>
                        </div>
                        
                        <div class="dropdown-divider"></div>
                        
                        <a href="../Users/dashboard.php" class="dropdown-item">
                            <span class="dropdown-icon">🏠</span>
                            Dashboard
                        </a>
                        
                        <a href="../Users/profile.php" class="dropdown-item">
                            <span class="dropdown-icon">👤</span>
                            Mi Perfil
                        </a>
                        
                        <div class="dropdown-divider"></div>
                        
                        <a href="../../Controllers/AuthController.php?action=logout" class="dropdown-item logout">
                            <span class="dropdown-icon">🚪</span>
                            Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Breadcrumb -->
        <div class="breadcrumb-container">
            <nav class="breadcrumb">
                <a href="../Users/dashboard.php" class="breadcrumb-item">Inicio</a>
                <span class="breadcrumb-separator">›</span>
                <span class="breadcrumb-current">Proyecto: <?php echo htmlspecialchars($project['project_name']); ?></span>
            </nav>
        </div>
    </header>
    
    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container">
            <!-- Mensajes -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo htmlspecialchars($_SESSION['success']); 
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    echo htmlspecialchars($_SESSION['error']); 
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <!-- Información del proyecto -->
            <div class="project-header">
                <div class="project-info">
                    <h1 class="project-title"><?php echo htmlspecialchars($project['project_name']); ?></h1>
                    <p class="project-company"><?php echo htmlspecialchars($project['company_name']); ?></p>
                    <div class="project-meta">
                        <span class="created-date">Creado: <?php echo date('d/m/Y', strtotime($project['created_at'])); ?></span>
                        <span class="status status-<?php echo $project['status']; ?>">
                            <?php 
                            $status_text = [
                                'draft' => 'Borrador',
                                'in_progress' => 'En Progreso',
                                'completed' => 'Completado'
                            ];
                            echo $status_text[$project['status']] ?? $project['status'];
                            ?>
                        </span>
                    </div>
                </div>
                
                <div class="project-progress">
                    <div class="progress-info">
                        <h3>Progreso General</h3>
                        <div class="progress-bar-container">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $progress['percentage']; ?>%"></div>
                            </div>
                            <span class="progress-text"><?php echo round($progress['percentage']); ?>% completado</span>
                        </div>
                        <div class="progress-details">
                            <span><?php echo $progress['completed']; ?> de <?php echo $progress['total']; ?> secciones completadas</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Secciones del plan estratégico -->
            <div class="strategic-plan-sections">
                <h2 class="sections-title">Plan Estratégico - Secciones</h2>
                
                <div class="sections-grid">
                    <!-- Sección 1: Misión -->
                    <div class="section-card <?php echo $progress['progress']['mission'] ? 'completed' : 'pending'; ?>" 
                         onclick="navigateToSection('mission', <?php echo $project_id; ?>)">
                        <div class="section-header">
                            <div class="section-number">1</div>
                            <div class="section-status">
                                <?php if ($progress['progress']['mission']): ?>
                                    <span class="status-icon completed">✓</span>
                                <?php else: ?>
                                    <span class="status-icon pending">●</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <h3>Misión</h3>
                        <p>Define el propósito fundamental de tu organización</p>
                        <div class="section-action">
                            <span class="action-text">
                                <?php echo $progress['progress']['mission'] ? 'Ver/Editar' : 'Comenzar'; ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Sección 2: Visión -->
                    <div class="section-card <?php echo $progress['progress']['vision'] ? 'completed' : ($progress['progress']['mission'] ? 'available' : 'locked'); ?>" 
                         onclick="navigateToSection('vision', <?php echo $project_id; ?>)">
                        <div class="section-header">
                            <div class="section-number">2</div>
                            <div class="section-status">
                                <?php if ($progress['progress']['vision']): ?>
                                    <span class="status-icon completed">✓</span>
                                <?php elseif ($progress['progress']['mission']): ?>
                                    <span class="status-icon pending">●</span>
                                <?php else: ?>
                                    <span class="status-icon locked">🔒</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <h3>Visión</h3>
                        <p>Establece hacia dónde quieres que vaya tu empresa</p>
                        <div class="section-action">
                            <span class="action-text">
                                <?php 
                                if ($progress['progress']['vision']) {
                                    echo 'Ver/Editar';
                                } elseif ($progress['progress']['mission']) {
                                    echo 'Comenzar';
                                } else {
                                    echo 'Bloqueado';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Sección 3: Valores -->
                    <div class="section-card <?php echo $progress['progress']['values'] ? 'completed' : ($progress['progress']['vision'] ? 'available' : 'locked'); ?>" 
                         onclick="navigateToSection('values', <?php echo $project_id; ?>)">
                        <div class="section-header">
                            <div class="section-number">3</div>
                            <div class="section-status">
                                <?php if ($progress['progress']['values']): ?>
                                    <span class="status-icon completed">✓</span>
                                <?php elseif ($progress['progress']['vision']): ?>
                                    <span class="status-icon pending">●</span>
                                <?php else: ?>
                                    <span class="status-icon locked">🔒</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <h3>Valores</h3>
                        <p>Los principios que guían tu organización</p>
                        <div class="section-action">
                            <span class="action-text">
                                <?php 
                                if ($progress['progress']['values']) {
                                    echo 'Ver/Editar';
                                } elseif ($progress['progress']['vision']) {
                                    echo 'Comenzar';
                                } else {
                                    echo 'Bloqueado';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Sección 4: Objetivos Estratégicos -->
                    <div class="section-card <?php echo $progress['progress']['objectives'] ? 'completed' : ($progress['progress']['values'] ? 'available' : 'locked'); ?>" 
                         onclick="navigateToSection('objectives', <?php echo $project_id; ?>)">
                        <div class="section-header">
                            <div class="section-number">4</div>
                            <div class="section-status">
                                <?php if ($progress['progress']['objectives']): ?>
                                    <span class="status-icon completed">✓</span>
                                <?php elseif ($progress['progress']['values']): ?>
                                    <span class="status-icon pending">●</span>
                                <?php else: ?>
                                    <span class="status-icon locked">🔒</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <h3>Objetivos Estratégicos</h3>
                        <p>Metas específicas y medibles a alcanzar</p>
                        <div class="section-action">
                            <span class="action-text">
                                <?php 
                                if ($progress['progress']['objectives']) {
                                    echo 'Ver/Editar';
                                } elseif ($progress['progress']['values']) {
                                    echo 'Comenzar';
                                } else {
                                    echo 'Bloqueado';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Secciones futuras (bloqueadas por ahora) -->
                    <div class="section-card locked">
                        <div class="section-header">
                            <div class="section-number">5</div>
                            <div class="section-status">
                                <span class="status-icon locked">🔒</span>
                            </div>
                        </div>
                        <h3>Análisis Interno y Externo</h3>
                        <p>Evaluación completa del entorno empresarial</p>
                        <div class="section-action">
                            <span class="action-text">Próximamente</span>
                        </div>
                    </div>
                    
                    <div class="section-card locked">
                        <div class="section-header">
                            <div class="section-number">6</div>
                            <div class="section-status">
                                <span class="status-icon locked">🔒</span>
                            </div>
                        </div>
                        <h3>Cadena de Valor</h3>
                        <p>Análisis de los procesos que agregan valor</p>
                        <div class="section-action">
                            <span class="action-text">Próximamente</span>
                        </div>
                    </div>
                    
                    <!-- Más secciones aquí... -->
                </div>
            </div>
            
            <!-- Acciones del proyecto -->
            <div class="project-actions">
                <div class="actions-container">
                    <a href="../Users/dashboard.php" class="btn-secondary">
                        <span class="btn-icon">←</span>
                        Volver al Dashboard
                    </a>
                    
                    <button class="btn-primary" onclick="exportProject()">
                        <span class="btn-icon">📄</span>
                        Exportar Proyecto
                    </button>
                    
                    <button class="btn-danger" onclick="deleteProject()">
                        <span class="btn-icon">🗑️</span>
                        Eliminar Proyecto
                    </button>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include '../Users/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="../../Publics/js/projects.js"></script>
    <script>
        // Variables PHP para JavaScript
        const projectId = <?php echo $project_id; ?>;
        const projectData = <?php echo json_encode($project); ?>;
        const progressData = <?php echo json_encode($progress); ?>;
    </script>
</body>
</html>