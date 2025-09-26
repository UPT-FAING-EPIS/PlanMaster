<?php
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../Models/Objectives.php';
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
$objectivesModel = new Objectives();
$missionModel = new Mission();

// Obtener datos del proyecto y verificar permisos
$project = $projectController->getProject($project_id);
$user = AuthController::getCurrentUser();

// Verificar que los valores estén completados
$progress = $projectController->getProjectProgress($project_id);
if (!$progress['progress']['values']) {
    $_SESSION['error'] = "Debe completar los Valores antes de continuar con los Objetivos";
    header("Location: values.php?id=" . $project_id);
    exit();
}

// Obtener misión para mostrar como referencia
$mission = $missionModel->getByProjectId($project_id);

// Obtener objetivos existentes si existen
$existing_objectives = $objectivesModel->getStrategicObjectivesByProjectId($project_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Objetivos Estratégicos - <?php echo htmlspecialchars($project['project_name']); ?> - PlanMaster</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../../Publics/css/styles_projects.css">
    <link rel="stylesheet" href="../../Publics/css/styles_dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../Resources/favicon.ico">
    
    <style>
        .objectives-container {
            max-width: 1000px;
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
        
        .objectives-description {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 40px;
            border-left: 5px solid #42a5f5;
        }
        
        .objectives-description h3 {
            color: #1e88e5;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .mission-reference {
            background: #e8f5e8;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 5px solid #4caf50;
        }
        
        .mission-reference h4 {
            color: #2e7d32;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .mission-text {
            color: #1b5e20;
            line-height: 1.6;
            font-style: italic;
            margin: 0;
        }
        
        .objectives-form {
            margin-bottom: 40px;
        }
        
        .strategic-objective {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        
        .strategic-objective:hover {
            border-color: #42a5f5;
        }
        
        .strategic-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .strategic-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #42a5f5, #1e88e5);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .strategic-title {
            flex: 1;
            color: #1e88e5;
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-input,
        .form-textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s ease;
        }
        
        .form-input:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #42a5f5;
            box-shadow: 0 0 0 3px rgba(66, 165, 245, 0.1);
        }
        
        .form-textarea {
            min-height: 80px;
            resize: vertical;
        }
        
        .specific-objectives {
            margin-top: 20px;
        }
        
        .specific-objective {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border: 2px solid #e8f5e8;
            position: relative;
        }
        
        .specific-objective::before {
            content: '';
            position: absolute;
            left: -2px;
            top: -2px;
            bottom: -2px;
            width: 4px;
            background: #4caf50;
            border-radius: 2px 0 0 2px;
        }
        
        .specific-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .specific-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #4caf50;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .specific-label {
            color: #2e7d32;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .char-counter {
            text-align: right;
            margin-top: 5px;
            font-size: 0.8rem;
            color: #666;
        }
        
        .objectives-counter {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(66, 165, 245, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(66, 165, 245, 0.2);
        }
        
        .objectives-counter h4 {
            color: #1e88e5;
            margin: 0 0 10px 0;
            font-size: 1.1rem;
        }
        
        .objectives-counter p {
            margin: 0;
            color: #1565c0;
            font-size: 0.9rem;
        }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 40px;
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
        
        .btn-continue {
            background: linear-gradient(135deg, #2196f3, #1976d2);
            color: white;
            margin-left: 10px;
        }
        
        .btn-continue:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(33, 150, 243, 0.4);
        }
        
        .actions-right {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .navigation-hint {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: rgba(76, 175, 80, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(76, 175, 80, 0.2);
        }
        
        .navigation-hint p {
            margin: 0;
            color: #2e7d32;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .objectives-container {
                padding: 25px;
                margin: 20px;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .strategic-objective {
                padding: 20px;
            }
            
            .specific-objective {
                padding: 15px;
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
                <span class="breadcrumb-current">Objetivos Estratégicos</span>
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
            
            <div class="objectives-container">
                <!-- Header de la sección -->
                <div class="section-header">
                    <div class="section-number-large">4</div>
                    <h1 class="section-title">Objetivos Estratégicos</h1>
                    <p class="section-subtitle">
                        Define metas específicas y medibles alineadas con tu misión y visión.
                    </p>
                </div>
                
                <!-- Descripción de los objetivos -->
                <div class="objectives-description">
                    <h3>🎯 Definición de Objetivos Estratégicos</h3>
                    <p>
                        A continuación reflexione sobre la misión, visión y valores definidos y establezca los objetivos estratégicos y específicos de su empresa. Le proponemos que comience con definir <strong>3 objetivos estratégicos</strong> y <strong>dos específicos</strong> para cada uno de ellos.
                    </p>
                </div>
                
                <!-- Referencia a la misión -->
                <?php if ($mission): ?>
                <div class="mission-reference">
                    <h4>
                        <span>📋</span>
                        Misión de tu empresa (como referencia):
                    </h4>
                    <p class="mission-text">
                        "<?php echo htmlspecialchars($mission['mission_text']); ?>"
                    </p>
                </div>
                <?php endif; ?>
                
                <!-- Contador de objetivos -->
                <div class="objectives-counter">
                    <h4>Estructura de Objetivos</h4>
                    <p>3 Objetivos Estratégicos × 2 Objetivos Específicos cada uno = 6 Objetivos Específicos totales</p>
                </div>
                
                <!-- Formulario -->
                <form method="POST" action="../../Controllers/ProjectController.php?action=save_objectives" class="objectives-form" id="objectives-form">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                    
                    <!-- Objetivos Estratégicos -->
                    <div id="strategic-objectives">
                        <?php for ($i = 1; $i <= 3; $i++): ?>
                            <?php 
                            $existing_strategic = isset($existing_objectives[$i-1]) ? $existing_objectives[$i-1] : null;
                            ?>
                            <div class="strategic-objective">
                                <div class="strategic-header">
                                    <div class="strategic-number"><?php echo $i; ?></div>
                                    <h3 class="strategic-title">Objetivo Estratégico <?php echo $i; ?></h3>
                                </div>
                                
                                <div class="form-group">
                                    <label for="strategic_title_<?php echo $i; ?>" class="form-label">
                                        Título del objetivo estratégico:
                                    </label>
                                    <input type="text" 
                                           name="strategic_objectives[<?php echo $i-1; ?>][title]" 
                                           id="strategic_title_<?php echo $i; ?>"
                                           class="form-input strategic-title-input"
                                           placeholder="Ej: Incrementar la participación en el mercado nacional"
                                           value="<?php echo $existing_strategic ? htmlspecialchars($existing_strategic['objective_title']) : ''; ?>"
                                           maxlength="150"
                                           required>
                                    <div class="char-counter">
                                        <span class="char-count">0</span> / 150 caracteres
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="strategic_desc_<?php echo $i; ?>" class="form-label">
                                        Descripción (opcional):
                                    </label>
                                    <textarea name="strategic_objectives[<?php echo $i-1; ?>][description]" 
                                              id="strategic_desc_<?php echo $i; ?>"
                                              class="form-textarea"
                                              placeholder="Describe más detalles sobre este objetivo estratégico..."
                                              maxlength="300"><?php echo $existing_strategic ? htmlspecialchars($existing_strategic['objective_description']) : ''; ?></textarea>
                                    <div class="char-counter">
                                        <span class="char-count">0</span> / 300 caracteres
                                    </div>
                                </div>
                                
                                <!-- Objetivos Específicos -->
                                <div class="specific-objectives">
                                    <?php for ($j = 1; $j <= 2; $j++): ?>
                                        <?php 
                                        $existing_specific = null;
                                        if ($existing_strategic && isset($existing_strategic['specific_objectives'][$j-1])) {
                                            $existing_specific = $existing_strategic['specific_objectives'][$j-1];
                                        }
                                        ?>
                                        <div class="specific-objective">
                                            <div class="specific-header">
                                                <div class="specific-number"><?php echo $j; ?></div>
                                                <span class="specific-label">Objetivo Específico <?php echo $j; ?></span>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="specific_title_<?php echo $i; ?>_<?php echo $j; ?>" class="form-label">
                                                    Título del objetivo específico:
                                                </label>
                                                <input type="text" 
                                                       name="strategic_objectives[<?php echo $i-1; ?>][specific_objectives][<?php echo $j-1; ?>][title]" 
                                                       id="specific_title_<?php echo $i; ?>_<?php echo $j; ?>"
                                                       class="form-input specific-title-input"
                                                       placeholder="Ej: Aumentar las ventas en un 15% en el primer semestre"
                                                       value="<?php echo $existing_specific ? htmlspecialchars($existing_specific['objective_title']) : ''; ?>"
                                                       maxlength="120"
                                                       required>
                                                <div class="char-counter">
                                                    <span class="char-count">0</span> / 120 caracteres
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="specific_desc_<?php echo $i; ?>_<?php echo $j; ?>" class="form-label">
                                                    Descripción (opcional):
                                                </label>
                                                <textarea name="strategic_objectives[<?php echo $i-1; ?>][specific_objectives][<?php echo $j-1; ?>][description]" 
                                                          id="specific_desc_<?php echo $i; ?>_<?php echo $j; ?>"
                                                          class="form-textarea"
                                                          placeholder="Detalles sobre cómo alcanzar este objetivo específico..."
                                                          maxlength="200"><?php echo $existing_specific ? htmlspecialchars($existing_specific['objective_description']) : ''; ?></textarea>
                                                <div class="char-counter">
                                                    <span class="char-count">0</span> / 200 caracteres
                                                </div>
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                    
                    <div class="form-actions">
                        <a href="values.php?id=<?php echo $project_id; ?>" class="btn btn-back">
                            <span class="btn-icon">←</span>
                            Volver a Valores
                        </a>
                        
                        <div class="actions-right">
                            <button type="submit" class="btn btn-save" id="save-btn">
                                <span class="btn-icon">💾</span>
                                <?php echo count($existing_objectives) > 0 ? 'Actualizar Objetivos' : 'Guardar Objetivos'; ?>
                            </button>
                            
                            <?php if (count($existing_objectives) > 0): ?>
                                <a href="foda-analysis.php?project_id=<?php echo $project_id; ?>" class="btn btn-continue">
                                    <span class="btn-icon">📊</span>
                                    Continuar a Análisis Interno y Externo
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
                
                <!-- Hint de navegación -->
                <div class="navigation-hint">
                    <p>
                        <strong>¡Excelente! 🎉</strong> Has completado los objetivos estratégicos. 
                        <?php if (count($existing_objectives) > 0): ?>
                            El siguiente paso es realizar un <strong>Análisis Interno y Externo</strong> para identificar las estrategias más adecuadas.
                        <?php else: ?>
                            Una vez guardados, podrás continuar con el Análisis Interno y Externo.
                        <?php endif; ?>
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
            const form = document.getElementById('objectives-form');
            const saveBtn = document.getElementById('save-btn');
            
            // Configurar contadores de caracteres
            function setupCharCounters() {
                const inputs = document.querySelectorAll('.form-input, .form-textarea');
                
                inputs.forEach(input => {
                    const counter = input.parentNode.querySelector('.char-count');
                    if (counter) {
                        const maxLength = parseInt(input.getAttribute('maxlength')) || 0;
                        
                        function updateCounter() {
                            const currentLength = input.value.length;
                            counter.textContent = currentLength;
                            
                            // Cambiar color según la proximidad al límite
                            const percentage = (currentLength / maxLength) * 100;
                            if (percentage >= 90) {
                                counter.style.color = '#f44336';
                            } else if (percentage >= 75) {
                                counter.style.color = '#ff9800';
                            } else {
                                counter.style.color = '#666';
                            }
                        }
                        
                        // Actualizar al cargar y al escribir
                        updateCounter();
                        input.addEventListener('input', updateCounter);
                    }
                });
            }
            
            // Validación del formulario
            function validateForm() {
                const strategicTitles = document.querySelectorAll('.strategic-title-input');
                const specificTitles = document.querySelectorAll('.specific-title-input');
                
                let isValid = true;
                
                // Validar títulos estratégicos
                strategicTitles.forEach(input => {
                    const value = input.value.trim();
                    if (value.length < 5) {
                        input.style.borderColor = value.length === 0 ? '#f44336' : '#ff9800';
                        isValid = false;
                    } else {
                        input.style.borderColor = '#4caf50';
                    }
                });
                
                // Validar títulos específicos
                specificTitles.forEach(input => {
                    const value = input.value.trim();
                    if (value.length < 5) {
                        input.style.borderColor = value.length === 0 ? '#f44336' : '#ff9800';
                        isValid = false;
                    } else {
                        input.style.borderColor = '#4caf50';
                    }
                });
                
                saveBtn.disabled = !isValid;
                return isValid;
            }
            
            // Event listeners para validación en tiempo real
            function setupValidation() {
                const allRequiredInputs = document.querySelectorAll('.strategic-title-input, .specific-title-input');
                
                allRequiredInputs.forEach(input => {
                    input.addEventListener('input', validateForm);
                    input.addEventListener('blur', validateForm);
                });
            }
            
            // Auto-resize para textareas
            function setupAutoResize() {
                const textareas = document.querySelectorAll('.form-textarea');
                
                textareas.forEach(textarea => {
                    textarea.addEventListener('input', function() {
                        this.style.height = 'auto';
                        this.style.height = Math.max(80, this.scrollHeight) + 'px';
                    });
                });
            }
            
            // Auto-save
            function setupAutoSave() {
                let autoSaveInterval = setInterval(function() {
                    if (validateForm()) {
                        const formData = new FormData(form);
                        const objectives = {};
                        
                        // Recopilar datos para el borrador
                        for (let [key, value] of formData.entries()) {
                            if (key.includes('strategic_objectives') && value.trim()) {
                                objectives[key] = value;
                            }
                        }
                        
                        if (Object.keys(objectives).length > 0) {
                            localStorage.setItem(`objectives_${<?php echo $project_id; ?>}`, JSON.stringify(objectives));
                            showNotification('Borrador guardado automáticamente', 'info');
                        }
                    }
                }, 45000); // Cada 45 segundos
                
                // Limpiar al enviar el formulario
                form.addEventListener('submit', function() {
                    localStorage.removeItem(`objectives_${<?php echo $project_id; ?>}`);
                    clearInterval(autoSaveInterval);
                });
            }
            
            // Recuperar borrador
            function loadDraft() {
                const draft = localStorage.getItem(`objectives_${<?php echo $project_id; ?>}`);
                if (draft) {
                    try {
                        const objectives = JSON.parse(draft);
                        let hasExistingData = false;
                        
                        // Verificar si ya hay datos existentes
                        const inputs = document.querySelectorAll('.strategic-title-input, .specific-title-input');
                        inputs.forEach(input => {
                            if (input.value.trim()) {
                                hasExistingData = true;
                            }
                        });
                        
                        if (!hasExistingData) {
                            // Cargar borrador solo si no hay datos existentes
                            Object.entries(objectives).forEach(([key, value]) => {
                                const input = document.querySelector(`[name="${key}"]`);
                                if (input && !input.value) {
                                    input.value = value;
                                }
                            });
                            showNotification('Borrador recuperado', 'info');
                        }
                    } catch (e) {
                        console.error('Error al cargar borrador:', e);
                    }
                }
            }
            
            // Atajos de teclado
            function setupKeyboardShortcuts() {
                document.addEventListener('keydown', function(e) {
                    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                        e.preventDefault();
                        if (validateForm()) {
                            saveBtn.click();
                        } else {
                            showNotification('Complete todos los campos requeridos antes de guardar', 'warning');
                        }
                    }
                });
            }
            
            // Validación antes del envío
            form.addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    showNotification('Por favor complete todos los campos requeridos', 'error');
                    
                    // Hacer scroll al primer campo inválido
                    const firstInvalid = document.querySelector('.form-input[style*="border-color: rgb(244, 67, 54)"], .form-input[style*="border-color: rgb(255, 152, 0)"]');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalid.focus();
                    }
                }
            });
            
            // Inicialización
            setupCharCounters();
            setupValidation();
            setupAutoResize();
            setupAutoSave();
            setupKeyboardShortcuts();
            loadDraft();
            validateForm(); // Validación inicial
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
            }, 4000);
        }
    </script>
</body>
</html>