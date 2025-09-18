<?php
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../config/url_config.php';
require_once __DIR__ . '/../../Models/Mission.php';

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
$missionModel = new Mission();

// Obtener datos del proyecto y verificar permisos
$project = $projectController->getProject($project_id);
$user = AuthController::getCurrentUser();

// Obtener misión existente si existe
$existing_mission = $missionModel->getByProjectId($project_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Misión - <?php echo htmlspecialchars($project['project_name']); ?> - PlanMaster</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../../Publics/css/styles_projects.css">
    <link rel="stylesheet" href="../../Publics/css/styles_dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../Resources/favicon.ico">
    
    <style>
        .mission-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .section-number-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #42a5f5, #1e88e5);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            margin: 0 auto 20px;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }
        
        .section-subtitle {
            font-size: 1.1rem;
            color: #666;
            line-height: 1.6;
        }
        
        .mission-description {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 40px;
            border-left: 5px solid #42a5f5;
        }
        
        .mission-description h3 {
            color: #1e88e5;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .mission-points {
            list-style: none;
            padding: 0;
        }
        
        .mission-points li {
            margin-bottom: 12px;
            padding-left: 25px;
            position: relative;
            color: #555;
            line-height: 1.6;
        }
        
        .mission-points li::before {
            content: "•";
            color: #42a5f5;
            font-weight: bold;
            position: absolute;
            left: 0;
            font-size: 1.2rem;
        }
        
        .mission-form {
            margin-bottom: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 12px;
        }
        
        .form-textarea {
            width: 100%;
            min-height: 200px;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            resize: vertical;
            transition: border-color 0.3s ease;
        }
        
        .form-textarea:focus {
            outline: none;
            border-color: #42a5f5;
            box-shadow: 0 0 0 3px rgba(66, 165, 245, 0.1);
        }
        
        .form-textarea::placeholder {
            color: #999;
            font-style: italic;
        }
        
        .char-counter {
            text-align: right;
            margin-top: 8px;
            font-size: 0.9rem;
            color: #666;
        }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .btn-back {
            background: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .btn-back:hover {
            background: #e0e0e0;
        }
        
        .btn-save {
            background: linear-gradient(135deg, #4caf50, #45a049);
            color: white;
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4);
        }
        
        .btn-save:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .navigation-hint {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: rgba(66, 165, 245, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(66, 165, 245, 0.2);
        }
        
        .navigation-hint p {
            margin: 0;
            color: #1e88e5;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .mission-container {
                padding: 25px;
                margin: 20px;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .form-actions .btn {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    <!-- Header simplificado -->
    <header class="header">
        <div class="header-container">
            <div class="header-left">
                <div class="logo">
                    <a href="../Users/dashboard.php">
                        <span class="logo-text">PlanMaster</span>
                        <span class="logo-subtitle">Plan Estratégico</span>
                    </a>
                </div>
            </div>
            
            <div class="header-right">
                <div class="current-project-info">
                    <div class="project-details">
                        <span class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></span>
                        <span class="company-name"><?php echo htmlspecialchars($project['company_name']); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="breadcrumb-container">
            <nav class="breadcrumb">
                <a href="../Users/dashboard.php" class="breadcrumb-item">Inicio</a>
                <span class="breadcrumb-separator">›</span>
                <a href="project.php?id=<?php echo $project_id; ?>" class="breadcrumb-item">Proyecto</a>
                <span class="breadcrumb-separator">›</span>
                <span class="breadcrumb-current">Misión</span>
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
            
            <div class="mission-container">
                <!-- Header de la sección -->
                <div class="section-header">
                    <div class="section-number-large">1</div>
                    <h1 class="section-title">Misión</h1>
                    <p class="section-subtitle">
                        En este apartado describa la Misión de su empresa.
                    </p>
                </div>
                
                <!-- Descripción de la misión -->
                <div class="mission-description">
                    <h3>¿Qué es la MISIÓN?</h3>
                    <p style="margin-bottom: 20px;">
                        La <strong>MISIÓN</strong> es la razón de ser de la empresa/organización.
                    </p>
                    <ul class="mission-points">
                        <li>Debe ser clara, concisa y compartida.</li>
                        <li>Siempre orientada hacia el cliente no hacia el producto o servicio.</li>
                        <li>Refleja el propósito fundamental de la empresa en el mercado.</li>
                    </ul>
                </div>
                
                <!-- Formulario -->
                <form method="POST" action="../../Controllers/ProjectController.php?action=save_mission" class="mission-form">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                    
                    <div class="form-group">
                        <label for="mission_text" class="form-label">
                            Describe la misión de tu empresa:
                        </label>
                        <textarea 
                            name="mission_text" 
                            id="mission_text" 
                            class="form-textarea"
                            placeholder="Ejemplo: Nuestra misión es proporcionar soluciones tecnológicas innovadoras que mejoren la productividad de nuestros clientes, ofreciendo servicios de alta calidad y un soporte excepcional para impulsar su crecimiento y éxito empresarial."
                            maxlength="1000"
                            required
                        ><?php echo $existing_mission ? htmlspecialchars($existing_mission['mission_text']) : ''; ?></textarea>
                        <div class="char-counter">
                            <span id="char-count">0</span> / 1000 caracteres
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="project.php?id=<?php echo $project_id; ?>" class="btn btn-back">
                            <span class="btn-icon">←</span>
                            Volver al Proyecto
                        </a>
                        
                        <button type="submit" class="btn btn-save" id="save-btn">
                            <span class="btn-icon">💾</span>
                            <?php echo $existing_mission ? 'Actualizar Misión' : 'Guardar Misión'; ?>
                        </button>
                    </div>
                </form>
                
                <!-- Hint de navegación -->
                <div class="navigation-hint">
                    <p>
                        <strong>Siguiente paso:</strong> Una vez guardada la misión, podrás continuar con la definición de la Visión.
                    </p>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include '../Users/footer.php'; ?>
    
    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('mission_text');
            const charCount = document.getElementById('char-count');
            const saveBtn = document.getElementById('save-btn');
            
            // Contador de caracteres
            function updateCharCount() {
                const count = textarea.value.length;
                charCount.textContent = count;
                
                if (count > 950) {
                    charCount.style.color = '#f44336';
                } else if (count > 800) {
                    charCount.style.color = '#ff9800';
                } else {
                    charCount.style.color = '#666';
                }
            }
            
            // Validación en tiempo real
            function validateForm() {
                const text = textarea.value.trim();
                const isValid = text.length >= 50 && text.length <= 1000;
                
                saveBtn.disabled = !isValid;
                
                if (text.length > 0 && text.length < 50) {
                    textarea.style.borderColor = '#ff9800';
                } else if (text.length > 1000) {
                    textarea.style.borderColor = '#f44336';
                } else if (isValid) {
                    textarea.style.borderColor = '#4caf50';
                } else {
                    textarea.style.borderColor = '#e0e0e0';
                }
            }
            
            // Event listeners
            textarea.addEventListener('input', function() {
                updateCharCount();
                validateForm();
            });
            
            // Auto-resize del textarea
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.max(200, this.scrollHeight) + 'px';
            });
            
            // Guardar con Ctrl+S
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    if (!saveBtn.disabled) {
                        saveBtn.click();
                    }
                }
            });
            
            // Auto-save cada 30 segundos (opcional)
            let autoSaveInterval;
            function startAutoSave() {
                autoSaveInterval = setInterval(function() {
                    const text = textarea.value.trim();
                    if (text.length >= 50) {
                        localStorage.setItem(`mission_${<?php echo $project_id; ?>}`, text);
                        showNotification('Borrador guardado automáticamente', 'info');
                    }
                }, 30000);
            }
            
            // Recuperar borrador
            function loadDraft() {
                const draft = localStorage.getItem(`mission_${<?php echo $project_id; ?>}`);
                if (draft && !textarea.value) {
                    textarea.value = draft;
                    updateCharCount();
                    validateForm();
                    showNotification('Borrador recuperado', 'info');
                }
            }
            
            // Inicialización
            updateCharCount();
            validateForm();
            loadDraft();
            startAutoSave();
            
            // Limpiar borrador al guardar exitosamente
            const form = document.querySelector('.mission-form');
            form.addEventListener('submit', function() {
                localStorage.removeItem(`mission_${<?php echo $project_id; ?>}`);
                clearInterval(autoSaveInterval);
            });
        });
        
        // Función para mostrar notificaciones
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.textContent = message;
            
            const colors = {
                success: '#4caf50',
                error: '#f44336',
                info: '#2196f3',
                warning: '#ff9800'
            };
            
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                z-index: 10000;
                animation: slideInRight 0.3s ease-out;
                background: ${colors[type] || colors.info};
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                max-width: 300px;
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
    </script>
</body>
</html>