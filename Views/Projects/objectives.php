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
        
        .btn-remove-strategic,
        .btn-remove-specific {
            background: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            padding: 5px 10px;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn-remove-strategic:hover,
        .btn-remove-specific:hover {
            background: #d32f2f;
        }
        
        .btn-add-specific {
            background: #2196f3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            padding: 8px 15px;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn-add-specific:hover {
            background: #1976d2;
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
            justify-content: space-between;
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
                        A continuación reflexione sobre la misión, visión y valores definidos y establezca los objetivos estratégicos y específicos de su empresa. Puede añadir tantos objetivos como considere necesarios para su organización.
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
                
                <!-- Contador dinámico de objetivos -->
                <div class="objectives-counter">
                    <h4>Estructura de Objetivos</h4>
                    <p id="objectives-summary">Agrega objetivos estratégicos y específicos según las necesidades de tu empresa</p>
                </div>
                
                <!-- Formulario -->
                <form method="POST" action="../../Controllers/ProjectController.php?action=save_objectives" class="objectives-form" id="objectives-form">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                    
                    <!-- Objetivos Estratégicos -->
                    <div id="strategic-objectives">
                        <!-- Los objetivos se cargarán dinámicamente -->
                    </div>
                    
                    <!-- Botón para añadir nuevo objetivo estratégico -->
                    <div style="text-align: center; margin: 20px 0;">
                        <button type="button" id="add-strategic-btn" class="btn btn-save" style="background: #4caf50;">
                            <span class="btn-icon">+</span>
                            Añadir Objetivo Estratégico
                        </button>
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
                                <a href="analisis-interno-externo.php?id=<?php echo $project_id; ?>" class="btn btn-continue">
                                    <span class="btn-icon">�</span>
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
            const strategicContainer = document.getElementById('strategic-objectives');
            const addStrategicBtn = document.getElementById('add-strategic-btn');
            const summary = document.getElementById('objectives-summary');
            
            let strategicCount = 0;
            const existingObjectives = <?php echo json_encode($existing_objectives ?: []); ?>;
            
            // Plantillas HTML
            function getStrategicObjectiveTemplate(index, title = '', specificObjectives = []) {
                // Si no hay objetivos específicos, crear uno por defecto
                if (specificObjectives.length === 0) {
                    specificObjectives = [{ objective_title: '' }];
                }
                
                return `
                    <div class="strategic-objective" data-strategic="${index}">
                        <div class="strategic-header">
                            <div class="strategic-number">${index + 1}</div>
                            <h3 class="strategic-title">Objetivo Estratégico ${index + 1}</h3>
                            <button type="button" class="btn-remove-strategic" onclick="removeStrategicObjective(${index})" style="margin-left: auto; padding: 5px 10px; background: #f44336; color: white; border: none; border-radius: 5px; cursor: pointer;">×</button>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Título del objetivo estratégico:</label>
                            <input type="text" 
                                   name="strategic_objectives[${index}][title]" 
                                   class="form-input strategic-title-input"
                                   placeholder="Ej: Incrementar la participación en el mercado nacional"
                                   value="${title}"
                                   maxlength="150"
                                   required>
                            <div class="char-counter">
                                <span class="char-count">0</span> / 150 caracteres
                            </div>
                        </div>
                        
                        <div class="specific-objectives" data-strategic="${index}">
                            ${specificObjectives.map((spec, specIndex) => getSpecificObjectiveTemplate(index, specIndex, spec.objective_title || '')).join('')}
                        </div>
                        
                        <div style="text-align: center; margin: 15px 0;">
                            <button type="button" class="btn-add-specific" onclick="addSpecificObjective(${index})" style="padding: 8px 15px; background: #2196f3; color: white; border: none; border-radius: 5px; cursor: pointer;">
                                + Añadir Objetivo Específico
                            </button>
                        </div>
                    </div>
                `;
            }
            
            function getSpecificObjectiveTemplate(strategicIndex, specificIndex, title = '', showRemoveBtn = true) {
                return `
                    <div class="specific-objective" data-strategic="${strategicIndex}" data-specific="${specificIndex}">
                        <div class="specific-header">
                            <div class="specific-number">${specificIndex + 1}</div>
                            <span class="specific-label">Objetivo Específico ${specificIndex + 1}</span>
                            ${showRemoveBtn ? `<button type="button" class="btn-remove-specific" onclick="removeSpecificObjective(${strategicIndex}, ${specificIndex})" style="margin-left: auto; padding: 2px 8px; background: #f44336; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;">×</button>` : ''}
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Título del objetivo específico:</label>
                            <input type="text" 
                                   name="strategic_objectives[${strategicIndex}][specific_objectives][${specificIndex}][title]" 
                                   class="form-input specific-title-input"
                                   placeholder="Ej: Aumentar las ventas en un 15% en el primer semestre"
                                   value="${title}"
                                   maxlength="120"
                                   required>
                            <div class="char-counter">
                                <span class="char-count">0</span> / 120 caracteres
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Funciones globales
            window.addSpecificObjective = function(strategicIndex) {
                const specificContainer = document.querySelector(`.specific-objectives[data-strategic="${strategicIndex}"]`);
                const existingSpecifics = specificContainer.querySelectorAll('.specific-objective').length;
                const newSpecificHtml = getSpecificObjectiveTemplate(strategicIndex, existingSpecifics, '', true);
                specificContainer.insertAdjacentHTML('beforeend', newSpecificHtml);
                
                // Actualizar botones de eliminar (mostrar si hay más de uno)
                updateRemoveButtons(strategicIndex);
                
                updateSummary();
                setupCharCounters();
                setupValidation();
            };
            
            window.removeSpecificObjective = function(strategicIndex, specificIndex) {
                const strategicContainer = document.querySelector(`.specific-objectives[data-strategic="${strategicIndex}"]`);
                const specificCount = strategicContainer.querySelectorAll('.specific-objective').length;
                
                // No permitir eliminar si solo hay uno
                if (specificCount <= 1) {
                    showNotification('Cada objetivo estratégico debe tener al menos un objetivo específico', 'warning');
                    return;
                }
                
                const specificObj = document.querySelector(`.specific-objective[data-strategic="${strategicIndex}"][data-specific="${specificIndex}"]`);
                if (specificObj) {
                    specificObj.remove();
                    reindexSpecificObjectives(strategicIndex);
                    updateRemoveButtons(strategicIndex);
                    updateSummary();
                    validateForm();
                }
            };
            
            window.removeStrategicObjective = function(strategicIndex) {
                if (confirm('¿Está seguro de eliminar este objetivo estratégico y todos sus objetivos específicos?')) {
                    const strategicObj = document.querySelector(`.strategic-objective[data-strategic="${strategicIndex}"]`);
                    if (strategicObj) {
                        strategicObj.remove();
                        reindexStrategicObjectives();
                        updateSummary();
                        validateForm();
                    }
                }
            };
            
            // Añadir nuevo objetivo estratégico
            addStrategicBtn.addEventListener('click', function() {
                const newStrategicHtml = getStrategicObjectiveTemplate(strategicCount, '', []);
                strategicContainer.insertAdjacentHTML('beforeend', newStrategicHtml);
                strategicCount++;
                
                updateSummary();
                setupCharCounters();
                setupValidation();
            });
            
            // Reindexar objetivos
            function reindexStrategicObjectives() {
                const strategics = document.querySelectorAll('.strategic-objective');
                strategicCount = 0;
                
                strategics.forEach((strategic, index) => {
                    strategic.setAttribute('data-strategic', index);
                    strategic.querySelector('.strategic-number').textContent = index + 1;
                    strategic.querySelector('.strategic-title').textContent = `Objetivo Estratégico ${index + 1}`;
                    
                    // Actualizar nombres de inputs
                    const titleInput = strategic.querySelector('.strategic-title-input');
                    titleInput.name = `strategic_objectives[${index}][title]`;
                    
                    // Actualizar botones
                    strategic.querySelector('.btn-remove-strategic').setAttribute('onclick', `removeStrategicObjective(${index})`);
                    strategic.querySelector('.btn-add-specific').setAttribute('onclick', `addSpecificObjective(${index})`);
                    
                    // Reindexar objetivos específicos
                    reindexSpecificObjectives(index);
                    strategicCount++;
                });
            }
            
            function reindexSpecificObjectives(strategicIndex) {
                const strategicObj = document.querySelector(`.strategic-objective[data-strategic="${strategicIndex}"]`);
                if (!strategicObj) return;
                
                const specifics = strategicObj.querySelectorAll('.specific-objective');
                specifics.forEach((specific, index) => {
                    specific.setAttribute('data-specific', index);
                    specific.querySelector('.specific-number').textContent = index + 1;
                    specific.querySelector('.specific-label').textContent = `Objetivo Específico ${index + 1}`;
                    
                    // Actualizar nombres de inputs
                    const titleInput = specific.querySelector('.specific-title-input');
                    titleInput.name = `strategic_objectives[${strategicIndex}][specific_objectives][${index}][title]`;
                    
                    // Actualizar botón de eliminar
                    specific.querySelector('.btn-remove-specific').setAttribute('onclick', `removeSpecificObjective(${strategicIndex}, ${index})`);
                });
            }
            
            // Actualizar botones de eliminar según la cantidad
            function updateRemoveButtons(strategicIndex) {
                const strategicContainer = document.querySelector(`.specific-objectives[data-strategic="${strategicIndex}"]`);
                const specifics = strategicContainer.querySelectorAll('.specific-objective');
                const showRemove = specifics.length > 1;
                
                specifics.forEach(specific => {
                    const removeBtn = specific.querySelector('.btn-remove-specific');
                    if (showRemove) {
                        if (!removeBtn) {
                            // Añadir botón si no existe
                            const header = specific.querySelector('.specific-header');
                            const specificIndex = specific.getAttribute('data-specific');
                            header.insertAdjacentHTML('beforeend', 
                                `<button type="button" class="btn-remove-specific" onclick="removeSpecificObjective(${strategicIndex}, ${specificIndex})" style="margin-left: auto; padding: 2px 8px; background: #f44336; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;">×</button>`
                            );
                        }
                    } else {
                        // Ocultar/remover botón si solo hay uno
                        if (removeBtn) {
                            removeBtn.remove();
                        }
                    }
                });
            }
            
            // Actualizar resumen
            function updateSummary() {
                const strategics = document.querySelectorAll('.strategic-objective').length;
                const specifics = document.querySelectorAll('.specific-objective').length;
                
                if (strategics === 0) {
                    summary.textContent = 'Agrega objetivos estratégicos y específicos según las necesidades de tu empresa';
                } else {
                    summary.textContent = `${strategics} Objetivo${strategics !== 1 ? 's' : ''} Estratégico${strategics !== 1 ? 's' : ''} con ${specifics} Objetivo${specifics !== 1 ? 's' : ''} Específico${specifics !== 1 ? 's' : ''} en total`;
                }
            }
            
            // Cargar objetivos existentes
            function loadExistingObjectives() {
                existingObjectives.forEach((objective, index) => {
                    const specificObjectives = objective.specific_objectives || [];
                    const newStrategicHtml = getStrategicObjectiveTemplate(index, objective.objective_title, specificObjectives);
                    strategicContainer.insertAdjacentHTML('beforeend', newStrategicHtml);
                    strategicCount++;
                    
                    // Actualizar botones para este objetivo estratégico
                    updateRemoveButtons(index);
                });
                
                // Si no hay objetivos existentes, crear uno por defecto
                if (existingObjectives.length === 0) {
                    addStrategicBtn.click();
                }
                
                updateSummary();
            }
            
            // Configurar contadores de caracteres
            function setupCharCounters() {
                const inputs = document.querySelectorAll('.form-input');
                
                inputs.forEach(input => {
                    const counter = input.parentNode.querySelector('.char-count');
                    if (counter) {
                        const maxLength = parseInt(input.getAttribute('maxlength')) || 0;
                        
                        function updateCounter() {
                            const currentLength = input.value.length;
                            counter.textContent = currentLength;
                            
                            const percentage = (currentLength / maxLength) * 100;
                            if (percentage >= 90) {
                                counter.style.color = '#f44336';
                            } else if (percentage >= 75) {
                                counter.style.color = '#ff9800';
                            } else {
                                counter.style.color = '#666';
                            }
                        }
                        
                        updateCounter();
                        input.addEventListener('input', updateCounter);
                    }
                });
            }
            
            // Validación del formulario
            function validateForm() {
                const strategicTitles = document.querySelectorAll('.strategic-title-input');
                const specificTitles = document.querySelectorAll('.specific-title-input');
                
                let isValid = strategicTitles.length > 0; // Al menos un objetivo estratégico
                
                strategicTitles.forEach(input => {
                    const value = input.value.trim();
                    if (value.length < 5) {
                        input.style.borderColor = value.length === 0 ? '#f44336' : '#ff9800';
                        isValid = false;
                    } else {
                        input.style.borderColor = '#4caf50';
                    }
                });
                
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
            
            // Validación antes del envío
            form.addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    showNotification('Por favor complete todos los campos requeridos', 'error');
                    
                    const firstInvalid = document.querySelector('.form-input[style*="border-color: rgb(244, 67, 54)"], .form-input[style*="border-color: rgb(255, 152, 0)"]');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalid.focus();
                    }
                }
            });
            
            // Inicialización
            loadExistingObjectives();
            setupCharCounters();
            setupValidation();
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