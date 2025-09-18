<?php
session_start();
require_once __DIR__ . '/../../../Controllers/AuthController.php';
require_once __DIR__ . '/../../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../../Models/Mission.php';
require_once __DIR__ . '/../../../config/url_config.php';

// Verificar que el usuario esté logueado
AuthController::requireLogin();

// Obtener ID del proyecto
$project_id = $_GET['project_id'] ?? null;
if (!$project_id) {
    header("Location: ../../Users/projects.php");
    exit();
}

// Obtener datos del usuario y proyecto
$user = AuthController::getCurrentUser();
$projectController = new ProjectController();
$project = $projectController->getProject($project_id);

if (!$project) {
    $_SESSION['error'] = "Proyecto no encontrado";
    header("Location: ../../Users/projects.php");
    exit();
}

// Obtener misión existente si existe
$mission = new Mission();
$existingMission = $mission->getByProjectId($project_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Misión - <?php echo htmlspecialchars($project['project_name']); ?> - PlanMaster</title>
    
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
            <!-- Header de la sección -->
            <div class="section-header">
                <div class="section-info">
                    <h1 class="section-title">🎯 Misión</h1>
                    <p class="section-subtitle">Define el propósito fundamental de tu empresa</p>
                    <p class="project-context">Proyecto: <strong><?php echo htmlspecialchars($project['project_name']); ?></strong></p>
                </div>
                
                <div class="section-actions">
                    <button class="btn-save-exit" onclick="saveAndExit()">
                        <span class="btn-icon">💾</span>
                        Guardar y Salir
                    </button>
                </div>
            </div>
            
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
            
            <!-- Guía y formulario -->
            <div class="section-content">
                <div class="guide-section">
                    <h2>📋 Guía para crear tu Misión</h2>
                    <div class="guide-content">
                        <p>La misión define <strong>el propósito fundamental de tu empresa</strong>, explica por qué existe y qué busca lograr en el presente.</p>
                        
                        <div class="tips-grid">
                            <div class="tip-card">
                                <h3>¿Qué incluir?</h3>
                                <ul>
                                    <li>Actividad principal de la empresa</li>
                                    <li>Público objetivo</li>
                                    <li>Valor que proporcionas</li>
                                    <li>Cómo lo haces diferente</li>
                                </ul>
                            </div>
                            
                            <div class="tip-card">
                                <h3>Características</h3>
                                <ul>
                                    <li>Clara y concisa</li>
                                    <li>Inspiradora pero realista</li>
                                    <li>Orientada al presente</li>
                                    <li>Fácil de recordar</li>
                                </ul>
                            </div>
                            
                            <div class="tip-card">
                                <h3>Ejemplo</h3>
                                <p class="example">"Brindar soluciones tecnológicas innovadoras que simplifiquen la vida de nuestros clientes, mejorando su productividad y eficiencia a través de productos de calidad superior."</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <form id="missionForm" method="POST" action="<?php echo getBaseUrl(); ?>/Controllers/ProjectController.php?action=save_mission">
                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                        
                        <div class="form-group">
                            <label for="mission_text">Misión de <?php echo htmlspecialchars($project['company_name']); ?></label>
                            <textarea 
                                id="mission_text" 
                                name="mission_text" 
                                rows="6" 
                                placeholder="Escribe la misión de tu empresa..."
                                required
                            ><?php echo $existingMission ? htmlspecialchars($existingMission['mission_text']) : ''; ?></textarea>
                            <small class="form-help">Recomendación: Entre 50-150 palabras</small>
                        </div>
                        
                        <div class="form-actions">
                            <a href="../project.php?id=<?php echo $project_id; ?>" class="btn-secondary">
                                <span class="btn-icon">←</span>
                                Volver al Proyecto
                            </a>
                            
                            <button type="submit" class="btn-primary">
                                <span class="btn-icon">💾</span>
                                Guardar Misión
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include '../../Users/footer.php'; ?>
    
    <!-- JavaScript -->
    <script>
        // Datos del proyecto
        const projectData = <?php echo json_encode($project); ?>;
        const projectId = <?php echo $project_id; ?>;
        
        // Función para guardar y salir
        function saveAndExit() {
            const form = document.getElementById('missionForm');
            const missionText = document.getElementById('mission_text').value.trim();
            
            if (!missionText) {
                showNotification('Por favor escribe la misión antes de guardar', 'error');
                return;
            }
            
            // Agregar un campo hidden para indicar que se quiere salir
            const exitField = document.createElement('input');
            exitField.type = 'hidden';
            exitField.name = 'save_and_exit';
            exitField.value = '1';
            form.appendChild(exitField);
            
            // Enviar el formulario
            form.submit();
        }
        
        // Función para mostrar notificaciones
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 10px;
                color: white;
                font-weight: 500;
                z-index: 10001;
                animation: slideInRight 0.3s ease-out;
                max-width: 300px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                ${type === 'success' ? 'background: linear-gradient(135deg, #4caf50, #43a047);' : 
                  type === 'error' ? 'background: linear-gradient(135deg, #f44336, #d32f2f);' : 
                  'background: linear-gradient(135deg, #42a5f5, #1e88e5);'}
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }, 3000);
        }
        
        // Auto-save cada 30 segundos
        setInterval(() => {
            const missionText = document.getElementById('mission_text').value.trim();
            if (missionText && missionText.length > 10) {
                // Aquí podrías implementar auto-save via AJAX
                console.log('Auto-save triggered');
            }
        }, 30000);
    </script>
    
    <style>
    /* Estilos específicos para secciones */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 40px;
        padding: 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }
    
    .section-info h1 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0 0 10px 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .section-subtitle {
        font-size: 1.2rem;
        margin: 0 0 10px 0;
        opacity: 0.9;
        font-weight: 300;
    }
    
    .project-context {
        font-size: 1rem;
        margin: 0;
        opacity: 0.8;
    }
    
    .guide-section {
        background: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .guide-section h2 {
        color: #333;
        margin-bottom: 20px;
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .tips-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    
    .tip-card {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        border-left: 4px solid #42a5f5;
    }
    
    .tip-card h3 {
        color: #333;
        margin-bottom: 10px;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    .tip-card ul {
        margin: 0;
        padding-left: 20px;
    }
    
    .tip-card li {
        margin-bottom: 5px;
        color: #666;
    }
    
    .example {
        font-style: italic;
        color: #555;
        background: white;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
    }
    
    .form-section {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .form-group textarea {
        width: 100%;
        padding: 15px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 1rem;
        font-family: inherit;
        resize: vertical;
        min-height: 150px;
        transition: border-color 0.3s ease;
        box-sizing: border-box;
    }
    
    .form-group textarea:focus {
        outline: none;
        border-color: #42a5f5;
    }
    
    .form-help {
        display: block;
        margin-top: 5px;
        color: #666;
        font-size: 0.9rem;
    }
    
    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: space-between;
        align-items: center;
        margin-top: 30px;
    }
    
    .btn-secondary,
    .btn-primary {
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-secondary {
        background: #f5f5f5;
        color: #666;
    }
    
    .btn-secondary:hover {
        background: #e0e0e0;
        transform: translateY(-1px);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #42a5f5, #1e88e5);
        color: white;
        box-shadow: 0 5px 15px rgba(30, 136, 229, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(30, 136, 229, 0.4);
    }
    
    .alert {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 500;
    }
    
    .alert-success {
        background: #e8f5e8;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }
    
    .alert-error {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @media (max-width: 768px) {
        .section-header {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }
        
        .tips-grid {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column;
            gap: 15px;
        }
        
        .btn-secondary,
        .btn-primary {
            width: 100%;
            justify-content: center;
        }
    }
    </style>
</body>
</html>