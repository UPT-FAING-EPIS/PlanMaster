<?php
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../Models/Values.php';
require_once __DIR__ . '/../../config/url_config.php';

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
$valuesModel = new Values();

// Obtener datos del proyecto y verificar permisos
$project = $projectController->getProject($project_id);
$user = AuthController::getCurrentUser();

// Verificar que la visión esté completada
$progress = $projectController->getProjectProgress($project_id);
if (!$progress['progress']['vision']) {
    $_SESSION['error'] = "Debe completar la Visión antes de continuar con los Valores";
    header("Location: vision.php?id=" . $project_id);
    exit();
}

// Obtener valores existentes si existen
$existing_values = $valuesModel->getValueTexts($project_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valores - <?php echo htmlspecialchars($project['project_name']); ?> - PlanMaster</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../../Publics/css/styles_projects.css">
    <link rel="stylesheet" href="../../Publics/css/styles_dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../Resources/favicon.ico">
    
    <style>
        .values-container {
            max-width: 900px;
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
        
        .values-description {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 40px;
            border-left: 5px solid #42a5f5;
        }
        
        .values-description h3 {
            color: #1e88e5;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .values-points {
            list-style: none;
            padding: 0;
            margin-bottom: 20px;
        }
        
        .values-points li {
            margin-bottom: 12px;
            padding-left: 25px;
            position: relative;
            color: #555;
            line-height: 1.6;
        }
        
        .values-points li::before {
            content: "•";
            color: #42a5f5;
            font-weight: bold;
            position: absolute;
            left: 0;
            font-size: 1.2rem;
        }
        
        .values-examples {
            background: #fff3e0;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
            border-left: 5px solid #ff9800;
        }
        
        .values-examples h4 {
            color: #e65100;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .example-values {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        
        .example-values li {
            color: #bf360c;
            padding-left: 20px;
            position: relative;
            font-size: 0.9rem;
        }
        
        .example-values li::before {
            content: "•";
            color: #ff9800;
            position: absolute;
            left: 0;
        }
        
        .values-form {
            margin-bottom: 40px;
        }
        
        .values-list {
            margin-bottom: 30px;
        }
        
        .value-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .value-item:hover {
            border-color: #42a5f5;
        }
        
        .value-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #42a5f5;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        
        .value-input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s ease;
        }
        
        .value-input:focus {
            outline: none;
            border-color: #42a5f5;
        }
        
        .value-input::placeholder {
            color: #999;
            font-style: italic;
        }
        
        .remove-value {
            background: #f44336;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        
        .remove-value:hover {
            background: #d32f2f;
            transform: scale(1.1);
        }
        
        .add-value-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #ffeb3b, #ffc107);
            color: #333;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .add-value-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.4);
        }
        
        .add-value-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .values-counter {
            text-align: center;
            margin-bottom: 20px;
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
            .values-container {
                padding: 25px;
                margin: 20px;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .value-item {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }
            
            .value-number {
                align-self: flex-start;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .form-actions .btn {
                width: 100%;
                max-width: 300px;
            }
            
            .example-values {
                grid-template-columns: 1fr;
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
                <span class="breadcrumb-current">Valores</span>
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
            
            <div class="values-container">
                <!-- Header de la sección -->
                <div class="section-header">
                    <div class="section-number-large">3</div>
                    <h1 class="section-title">Valores</h1>
                    <p class="section-subtitle">
                        Los principios que guían tu organización y definen su cultura.
                    </p>
                </div>
                
                <!-- Descripción de los valores -->
                <div class="values-description">
                    <h3>¿Qué son los VALORES?</h3>
                    <p style="margin-bottom: 20px;">
                        Los <strong>VALORES</strong> de una empresa son el conjunto de principios, reglas y aspectos culturales con los que se rige la organización.
                    </p>
                    <ul class="values-points">
                        <li>Son las pautas de comportamiento de la empresa y generalmente son pocos, entre 3 y 6.</li>
                        <li>Son tan fundamentales y tan arraigados que casi nunca cambian.</li>
                        <li>Definen la personalidad y cultura organizacional.</li>
                    </ul>
                    
                    <div class="values-examples">
                        <h4>📋 Ejemplo de valores:</h4>
                        <ul class="example-values">
                            <li>Integridad</li>
                            <li>Compromiso con el desarrollo humano</li>
                            <li>Ética profesional</li>
                            <li>Responsabilidad social</li>
                            <li>Innovación</li>
                            <li>Excelencia</li>
                            <li>Trabajo en equipo</li>
                            <li>Transparencia</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Formulario -->
                <form method="POST" action="../../Controllers/ProjectController.php?action=save_values" class="values-form" id="values-form">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                    
                    <div class="values-counter">
                        <span id="values-count">0</span> de 10 valores máximo | Mínimo 3 valores requeridos
                    </div>
                    
                    <div class="values-list" id="values-list">
                        <!-- Los valores se cargarán dinámicamente aquí -->
                    </div>
                    
                    <button type="button" class="add-value-btn" id="add-value-btn">
                        <span>➕</span>
                        Agregar Valor
                    </button>
                    
                    <div class="form-actions">
                        <a href="vision.php?id=<?php echo $project_id; ?>" class="btn btn-back">
                            <span class="btn-icon">←</span>
                            Volver a Visión
                        </a>
                        
                        <button type="submit" class="btn btn-save" id="save-btn" disabled>
                            <span class="btn-icon">💾</span>
                            Guardar Valores
                        </button>
                    </div>
                </form>
                
                <!-- Hint de navegación -->
                <div class="navigation-hint">
                    <p>
                        <strong>Siguiente paso:</strong> Una vez guardados los valores, podrás continuar con la definición de los Objetivos Estratégicos.
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
            const valuesList = document.getElementById('values-list');
            const addValueBtn = document.getElementById('add-value-btn');
            const saveBtn = document.getElementById('save-btn');
            const valuesCount = document.getElementById('values-count');
            const form = document.getElementById('values-form');
            
            let values = [];
            let valueCounter = 0;
            
            // Cargar valores existentes
            const existingValues = <?php echo json_encode($existing_values); ?>;
            if (existingValues && existingValues.length > 0) {
                existingValues.forEach(value => {
                    addValue(value);
                });
            } else {
                // Agregar 3 valores vacíos por defecto
                for (let i = 0; i < 3; i++) {
                    addValue('');
                }
            }
            
            function addValue(text = '') {
                if (values.length >= 10) {
                    showNotification('No puedes agregar más de 10 valores', 'warning');
                    return;
                }
                
                valueCounter++;
                const valueId = `value-${valueCounter}`;
                
                const valueItem = document.createElement('div');
                valueItem.className = 'value-item';
                valueItem.innerHTML = `
                    <div class="value-number">${values.length + 1}</div>
                    <input type="text" 
                           class="value-input" 
                           name="values[]" 
                           id="${valueId}"
                           placeholder="Ej: Integridad, Compromiso, Innovación..."
                           value="${text}"
                           maxlength="50">
                    <button type="button" class="remove-value" onclick="removeValue(this)">×</button>
                `;
                
                valuesList.appendChild(valueItem);
                values.push(valueItem);
                
                updateUI();
                
                // Focus en el nuevo input
                const input = valueItem.querySelector('.value-input');
                input.focus();
                
                // Event listeners para el input
                input.addEventListener('input', validateForm);
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        addValueBtn.click();
                    }
                });
            }
            
            window.removeValue = function(button) {
                if (values.length <= 1) {
                    showNotification('Debe mantener al menos un valor', 'warning');
                    return;
                }
                
                const valueItem = button.parentNode;
                const index = values.indexOf(valueItem);
                
                if (index > -1) {
                    values.splice(index, 1);
                    valueItem.remove();
                    updateValueNumbers();
                    updateUI();
                    validateForm();
                }
            };
            
            function updateValueNumbers() {
                values.forEach((valueItem, index) => {
                    const numberElement = valueItem.querySelector('.value-number');
                    numberElement.textContent = index + 1;
                });
            }
            
            function updateUI() {
                const count = values.length;
                valuesCount.textContent = count;
                
                // Habilitar/deshabilitar botón de agregar
                addValueBtn.disabled = count >= 10;
                
                if (count >= 10) {
                    addValueBtn.textContent = 'Máximo de valores alcanzado';
                    addValueBtn.style.background = '#ccc';
                } else {
                    addValueBtn.innerHTML = '<span>➕</span> Agregar Valor';
                    addValueBtn.style.background = 'linear-gradient(135deg, #ffeb3b, #ffc107)';
                }
                
                validateForm();
            }
            
            function validateForm() {
                const inputs = valuesList.querySelectorAll('.value-input');
                const filledValues = Array.from(inputs).filter(input => input.value.trim().length > 0);
                const validValues = filledValues.filter(input => input.value.trim().length >= 2);
                
                const isValid = validValues.length >= 3 && validValues.length <= 10;
                
                saveBtn.disabled = !isValid;
                
                // Actualizar color del contador
                if (validValues.length < 3) {
                    valuesCount.style.color = '#f44336';
                } else if (validValues.length >= 3) {
                    valuesCount.style.color = '#4caf50';
                } else {
                    valuesCount.style.color = '#666';
                }
                
                // Validar inputs individuales
                inputs.forEach(input => {
                    const value = input.value.trim();
                    if (value.length === 0) {
                        input.style.borderColor = '#e0e0e0';
                    } else if (value.length < 2) {
                        input.style.borderColor = '#ff9800';
                    } else if (value.length >= 2) {
                        input.style.borderColor = '#4caf50';
                    }
                });
            }
            
            // Event listeners
            addValueBtn.addEventListener('click', () => addValue());
            
            // Prevenir envío si no es válido
            form.addEventListener('submit', function(e) {
                const inputs = valuesList.querySelectorAll('.value-input');
                const validValues = Array.from(inputs)
                    .map(input => input.value.trim())
                    .filter(value => value.length >= 2);
                
                if (validValues.length < 3) {
                    e.preventDefault();
                    showNotification('Debe ingresar al menos 3 valores válidos', 'error');
                    return;
                }
                
                if (validValues.length > 10) {
                    e.preventDefault();
                    showNotification('No puede ingresar más de 10 valores', 'error');
                    return;
                }
                
                // Verificar valores duplicados
                const uniqueValues = [...new Set(validValues.map(v => v.toLowerCase()))];
                if (uniqueValues.length !== validValues.length) {
                    e.preventDefault();
                    showNotification('No puede repetir valores', 'error');
                    return;
                }
            });
            
            // Auto-save cada 30 segundos
            let autoSaveInterval = setInterval(function() {
                const inputs = valuesList.querySelectorAll('.value-input');
                const currentValues = Array.from(inputs)
                    .map(input => input.value.trim())
                    .filter(value => value.length > 0);
                
                if (currentValues.length >= 3) {
                    localStorage.setItem(`values_${<?php echo $project_id; ?>}`, JSON.stringify(currentValues));
                    showNotification('Borrador guardado automáticamente', 'info');
                }
            }, 30000);
            
            // Recuperar borrador
            function loadDraft() {
                const draft = localStorage.getItem(`values_${<?php echo $project_id; ?>}`);
                if (draft && existingValues.length === 0) {
                    const draftValues = JSON.parse(draft);
                    // Limpiar valores actuales
                    values = [];
                    valuesList.innerHTML = '';
                    
                    // Cargar valores del borrador
                    draftValues.forEach(value => addValue(value));
                    showNotification('Borrador recuperado', 'info');
                }
            }
            
            // Inicialización
            loadDraft();
            
            // Limpiar borrador al guardar exitosamente
            form.addEventListener('submit', function() {
                localStorage.removeItem(`values_${<?php echo $project_id; ?>}`);
                clearInterval(autoSaveInterval);
            });
            
            // Atajos de teclado
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    if (!saveBtn.disabled) {
                        saveBtn.click();
                    }
                }
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