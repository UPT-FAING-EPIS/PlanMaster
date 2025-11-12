<?php
// Incluir configuraciones necesarias
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../config/url_config.php';

// Verificar que el usuario esté logueado
if (!AuthController::isLoggedIn()) {
    header("Location: " . getBaseUrl() . "/Views/Auth/login.php");
    exit();
}

// Validar parámetros
if (!isset($_GET['id'])) {
    header("Location: " . getBaseUrl() . "/Views/Users/dashboard.php");
    exit();
}

$project_id = (int)$_GET['id'];
$projectController = new ProjectController();

// Verificar que el proyecto existe y pertenece al usuario
$project = $projectController->getProject($project_id);
if (!$project || $project['user_id'] != $_SESSION['user_id']) {
    header("Location: " . getBaseUrl() . "/Views/Users/dashboard.php");
    exit();
}

// Obtener datos del usuario
$user = AuthController::getCurrentUser();

// Obtener respuestas existentes de Cadena de Valor
$valueChainData = $projectController->getValueChain($project_id);
$improvement = $projectController->getValueChainImprovement($project_id);

// Obtener las preguntas desde el modelo a través del controlador
require_once __DIR__ . '/../../Models/ValueChain.php';
$valueChainModel = new ValueChain();
$questions = $valueChainModel->getStandardQuestions();

// Obtener datos FODA existentes para Fortalezas y Debilidades
$fodaData = $projectController->getFodaAnalysis($project_id);
$fortalezas = isset($fodaData['fortalezas']) ? $fodaData['fortalezas'] : [];  // CORREGIDO: usar plural
$debilidades = isset($fodaData['debilidades']) ? $fodaData['debilidades'] : [];  // CORREGIDO: usar plural

// Debug temporal - descomentar para verificar datos
// echo "<!-- Debug FODA Data: " . print_r($fodaData, true) . " -->";
// echo "<!-- Debug Fortalezas: " . print_r($fortalezas, true) . " -->";
// echo "<!-- Debug Debilidades: " . print_r($debilidades, true) . " -->";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadena de Valor - <?php echo htmlspecialchars($project['project_name']); ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_project.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_value_chain.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo getBaseUrl(); ?>/Resources/favicon.ico">
    
    <style>
    .continue-button-header .btn-continue:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(34, 197, 94, 0.6);
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
    }
    
    .project-header {
        animation: slideIn 0.6s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @media (max-width: 768px) {
        .project-header {
            flex-direction: column;
            text-align: center;
            gap: 20px;
        }
        
        .continue-button-header {
            width: 100%;
        }
        
        .continue-button-header .btn-continue {
            width: 100%;
            padding: 18px 20px;
        }
    }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>
    
    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container">
            <!-- Información del proyecto -->
            <div class="project-header" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 15px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.2); padding: 25px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                <div class="project-info">
                    <h1 style="color: white; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8), 0 0 8px rgba(255, 255, 255, 0.3); font-size: 2.5rem; font-weight: 700; margin: 0;">⛓️ Cadena de Valor</h1>
                    <p class="project-name" style="color: white; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8), 0 0 6px rgba(255, 255, 255, 0.2); font-size: 1.3rem; font-weight: 600; margin: 10px 0 5px 0;"><?php echo htmlspecialchars($project['project_name']); ?></p>
                    <p class="company-name" style="color: white; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8), 0 0 6px rgba(255, 255, 255, 0.2); font-size: 1.1rem; font-weight: 500; margin: 0;"><?php echo htmlspecialchars($project['company_name']); ?></p>
                </div>
                
                <!-- Botón de continuar si está completo -->
                <?php if ($projectController->isValueChainComplete($project_id)): ?>
                <div class="continue-button-header">
                    <a href="<?php echo getBaseUrl(); ?>/Views/Projects/bcg-test.php?id=<?php echo $project_id; ?>" 
                       class="btn btn-continue" 
                       style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; padding: 15px 25px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 1.1rem; box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4); transition: all 0.3s ease; border: none; display: inline-block;">
                        📊 Continuar a Matriz BCG
                    </a>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Contexto de la Cadena de Valor -->
            <div class="context-box">
                <div class="rating-scale">
                    <h3>🎯 Diagnóstico de Cadena de Valor</h3>
                    <p>Evalúe cada aspecto de la gestión comercial de su empresa calificando del <strong>0 al 4</strong> según el siguiente criterio:</p>
                    <div class="rating-item">
                        <span class="rating-number">0</span>
                        <span class="rating-desc">No aplica / No se hace</span>
                    </div>
                    <div class="rating-item">
                        <span class="rating-number">1</span>
                        <span class="rating-desc">Se hace muy poco</span>
                    </div>
                    <div class="rating-item">
                        <span class="rating-number">2</span>
                        <span class="rating-desc">Se hace de manera básica</span>
                    </div>
                    <div class="rating-item">
                        <span class="rating-number">3</span>
                        <span class="rating-desc">Se hace de manera adecuada</span>
                    </div>
                    <div class="rating-item">
                        <span class="rating-number">4</span>
                        <span class="rating-desc">Se hace de manera excelente</span>
                    </div>
                </div>
            </div>
            <br>
            <!-- Mostrar resultados si existe cálculo -->
            <?php if ($improvement): ?>
            <div class="improvement-results">
                <h3>📊 Resultados del Diagnóstico</h3>
                <div class="results-grid">
                    <div class="result-card">
                        <div class="result-number"><?php echo $improvement['total_rating']; ?></div>
                        <div class="result-label">Puntuación Total</div>
                        <div class="result-sublabel">de 100 puntos máximos</div>
                    </div>
                    <div class="result-card highlight">
                        <div class="result-number"><?php echo $improvement['percentage']; ?>%</div>
                        <div class="result-label">Potencial de Mejora</div>
                        <div class="result-sublabel">Oportunidad de crecimiento</div>
                    </div>
                </div>
                
                <div class="improvement-interpretation">
                    <?php 
                    $percentage = $improvement['percentage'];
                    if ($percentage >= 70): ?>
                        <div class="interpretation high-potential">
                            <strong>🚀 Alto Potencial de Mejora:</strong> Su empresa tiene excelentes oportunidades para optimizar la gestión comercial. Priorice las áreas con menor puntuación.
                        </div>
                    <?php elseif ($percentage >= 40): ?>
                        <div class="interpretation medium-potential">
                            <strong>📈 Potencial Moderado:</strong> Hay áreas importantes para mejorar. Enfóquese en fortalecer los procesos comerciales clave.
                        </div>
                    <?php elseif ($percentage >= 20): ?>
                        <div class="interpretation low-potential">
                            <strong>✅ Gestión Sólida:</strong> Su empresa maneja bien la mayoría de aspectos comerciales. Identifique áreas específicas para la excelencia.
                        </div>
                    <?php else: ?>
                        <div class="interpretation excellent">
                            <strong>🏆 Excelente Gestión:</strong> Su empresa tiene una gestión comercial excepcional. Mantenga estos estándares y busque innovación continua.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Formulario de diagnóstico -->
            <form action="<?php echo getBaseUrl(); ?>/Controllers/ProjectController.php?action=save_value_chain" method="POST" class="value-chain-form">
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                
                <div class="questions-container">
                    <h3>📋 Cuestionario de Diagnóstico</h3>
                    
                    <?php foreach ($questions as $question_number => $question_text): ?>
                    <div class="question-item">
                        <div class="question-header">
                            <span class="question-number"><?php echo $question_number; ?></span>
                            <span class="question-text"><?php echo htmlspecialchars($question_text); ?></span>
                        </div>
                        
                        <div class="rating-options">
                            <?php for ($rating = 0; $rating <= 4; $rating++): ?>
                            <?php 
                            $is_selected = isset($valueChainData[$question_number]) && $valueChainData[$question_number]['rating'] == $rating;
                            ?>
                            <label class="rating-option <?php echo $is_selected ? 'selected' : ''; ?>">
                                <input type="radio" 
                                       name="responses[<?php echo $question_number; ?>]" 
                                       value="<?php echo $rating; ?>"
                                       <?php echo $is_selected ? 'checked' : ''; ?>
                                       required>
                                <span class="radio-custom"><?php echo $rating; ?></span>
                            </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Botones de acción -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-save">
                        💾 Guardar Diagnóstico
                    </button>
                    <a href="project.php?id=<?php echo $project_id; ?>" class="btn btn-secondary">
                        ↩️ Volver al Proyecto
                    </a>
                </div>
            </form>

            <!-- Sección FODA: Fortalezas y Debilidades -->
            <div class="foda-section" style="margin-top: 40px;">
                <div class="section-header">
                    <h2>🔍 Análisis FODA - Factores Internos</h2>
                    <p>Complete las fortalezas y debilidades de su organización para complementar el análisis de la cadena de valor.</p>
                </div>

                <form action="<?php echo getBaseUrl(); ?>/Controllers/ProjectController.php" method="POST" id="fodaForm">
                    <input type="hidden" name="action" value="save_foda">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                    <input type="hidden" name="source" value="value-chain">

                    <div class="foda-container">
                        <!-- Fortalezas -->
                        <div class="foda-column">
                            <h3 class="foda-title fortaleza">💪 Fortalezas</h3>
                            <p class="foda-description">Características internas positivas que dan ventaja competitiva</p>
                            <div class="foda-items" id="fortalezas">
                                <?php if (!empty($fortalezas)): ?>
                                    <?php foreach ($fortalezas as $index => $fortaleza): ?>
                                        <div class="foda-item">
                                            <textarea name="fortalezas[]" placeholder="Escriba una fortaleza..." maxlength="500"><?php echo htmlspecialchars($fortaleza['item_text']); ?></textarea>
                                            <button type="button" class="btn-remove" onclick="removeFodaItem(this)">❌</button>
                                        </div>
                                    <?php endforeach; ?>
                                    <!-- Campo vacío adicional para agregar más -->
                                    <div class="foda-item">
                                        <textarea name="fortalezas[]" placeholder="Escriba una fortaleza..." maxlength="500"></textarea>
                                        <button type="button" class="btn-remove" onclick="removeFodaItem(this)">❌</button>
                                    </div>
                                <?php else: ?>
                                    <div class="foda-item">
                                        <textarea name="fortalezas[]" placeholder="Escriba una fortaleza..." maxlength="500"></textarea>
                                        <button type="button" class="btn-remove" onclick="removeFodaItem(this)">❌</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn-add" onclick="addFodaItem('fortalezas', 'fortalezas')">➕ Agregar Fortaleza</button>
                        </div>

                        <!-- Debilidades -->
                        <div class="foda-column">
                            <h3 class="foda-title debilidad">⚠️ Debilidades</h3>
                            <p class="foda-description">Características internas que representan desventajas</p>
                            <div class="foda-items" id="debilidades">
                                <?php if (!empty($debilidades)): ?>
                                    <?php foreach ($debilidades as $index => $debilidad): ?>
                                        <div class="foda-item">
                                            <textarea name="debilidades[]" placeholder="Escriba una debilidad..." maxlength="500"><?php echo htmlspecialchars($debilidad['item_text']); ?></textarea>
                                            <button type="button" class="btn-remove" onclick="removeFodaItem(this)">❌</button>
                                        </div>
                                    <?php endforeach; ?>
                                    <!-- Campo vacío adicional para agregar más -->
                                    <div class="foda-item">
                                        <textarea name="debilidades[]" placeholder="Escriba una debilidad..." maxlength="500"></textarea>
                                        <button type="button" class="btn-remove" onclick="removeFodaItem(this)">❌</button>
                                    </div>
                                <?php else: ?>
                                    <div class="foda-item">
                                        <textarea name="debilidades[]" placeholder="Escriba una debilidad..." maxlength="500"></textarea>
                                        <button type="button" class="btn-remove" onclick="removeFodaItem(this)">❌</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn-add" onclick="addFodaItem('debilidades', 'debilidades')">➕ Agregar Debilidad</button>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 30px;">
                        <button type="submit" class="btn btn-save">
                            💾 Guardar Fortalezas y Debilidades
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Mensaje de completitud -->
            <?php if ($projectController->isValueChainComplete($project_id)): ?>
            <div class="next-section">
                <div class="completion-message">
                    <h3 style="color: #16a34a;">✅ Cadena de Valor Completada</h3>
                    <p>Has completado exitosamente el diagnóstico de la Cadena de Valor y el análisis FODA.</p>
                </div>
            </div>
            <?php else: ?>
            <div class="incomplete-message">
                <p><strong>⚠️ Complete todas las preguntas y el análisis FODA</strong></p>
                <p>Una vez completado el diagnóstico y definidas las fortalezas/debilidades, podrás continuar con la Matriz BCG.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../Users/footer.php'; ?>
    
    <!-- Mensajes de éxito/error -->
    <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
    <div class="alert alert-success" id="alertMessage">
        ✅ Fortalezas y Debilidades guardadas exitosamente
    </div>
    <?php elseif (isset($_GET['success'])): ?>
    <div class="alert alert-success" id="alertMessage">
        ✅ Diagnóstico guardado exitosamente
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error" id="alertMessage">
        ❌ Error: <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
    <?php endif; ?>

    <script>
        // Auto-ocultar alertas después de 5 segundos
        const alertMessage = document.getElementById('alertMessage');
        if (alertMessage) {
            setTimeout(() => {
                alertMessage.style.display = 'none';
            }, 5000);
        }
        
        // Mejorar interactividad de las opciones de rating
        document.querySelectorAll('.rating-option input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Remover selección previa en esta pregunta
                const questionItem = this.closest('.question-item');
                questionItem.querySelectorAll('.rating-option').forEach(option => {
                    option.classList.remove('selected');
                });
                
                // Agregar selección actual
                this.closest('.rating-option').classList.add('selected');
                
                // Actualizar contador de progreso
                updateProgressCounter();
            });
        });
        
        // Contador de progreso
        function updateProgressCounter() {
            const totalQuestions = <?php echo count($questions); ?>;
            const answeredQuestions = document.querySelectorAll('.rating-option input[type="radio"]:checked').length;
            
            // Crear o actualizar indicador de progreso si no existe
            let progressIndicator = document.querySelector('.progress-indicator');
            if (!progressIndicator) {
                progressIndicator = document.createElement('div');
                progressIndicator.className = 'progress-indicator';
                document.querySelector('.questions-container h3').appendChild(progressIndicator);
            }
            
            const percentage = (answeredQuestions / totalQuestions) * 100;
            progressIndicator.innerHTML = ` (${answeredQuestions}/${totalQuestions} - ${Math.round(percentage)}%)`;
            
            // Habilitar botón de guardar solo si todas las preguntas están respondidas
            const saveButton = document.querySelector('.btn-save');
            if (answeredQuestions === totalQuestions) {
                saveButton.disabled = false;
                saveButton.style.opacity = '1';
            } else {
                saveButton.disabled = true;
                saveButton.style.opacity = '0.6';
            }
        }
        
        // Inicializar contador de progreso
        document.addEventListener('DOMContentLoaded', function() {
            updateProgressCounter();
        });

        // Funciones FODA
        function addFodaItem(containerId, type) {
            const container = document.getElementById(containerId);
            const newItem = document.createElement('div');
            newItem.className = 'foda-item';
            newItem.innerHTML = `
                <textarea name="${type}[]" placeholder="Escriba una ${type}..." maxlength="500"></textarea>
                <button type="button" class="btn-remove" onclick="removeFodaItem(this)">❌</button>
            `;
            container.appendChild(newItem);
            
            // Focus en el nuevo textarea
            const textarea = newItem.querySelector('textarea');
            textarea.focus();
        }

        function removeFodaItem(button) {
            const item = button.closest('.foda-item');
            const container = item.parentNode;
            
            // No permitir eliminar si es el último elemento
            if (container.children.length <= 1) {
                // En lugar de eliminar, solo limpiar el contenido
                const textarea = item.querySelector('textarea');
                textarea.value = '';
                textarea.focus();
                return;
            }
            
            // Animación de salida
            item.style.transform = 'translateX(-100%)';
            item.style.opacity = '0';
            
            setTimeout(() => {
                item.remove();
            }, 300);
        }

        // Validación del formulario FODA
        document.addEventListener('DOMContentLoaded', function() {
            const fodaForm = document.getElementById('fodaForm');
            if (fodaForm) {
                fodaForm.addEventListener('submit', function(e) {
                    const fortalezas = document.querySelectorAll('textarea[name="fortalezas[]"]');
                    const debilidades = document.querySelectorAll('textarea[name="debilidades[]"]');
                    
                    let hasFortaleza = false;
                    let hasDebilidad = false;
                    
                    // Verificar si hay al menos una fortaleza
                    fortalezas.forEach(textarea => {
                        if (textarea.value.trim()) {
                            hasFortaleza = true;
                        }
                    });
                    
                    // Verificar si hay al menos una debilidad
                    debilidades.forEach(textarea => {
                        if (textarea.value.trim()) {
                            hasDebilidad = true;
                        }
                    });
                    
                    if (!hasFortaleza || !hasDebilidad) {
                        e.preventDefault();
                        let mensaje = 'Por favor, complete:';
                        if (!hasFortaleza) mensaje += '\n- Al menos una fortaleza';
                        if (!hasDebilidad) mensaje += '\n- Al menos una debilidad';
                        alert(mensaje);
                        return false;
                    }
                    
                    // Mostrar mensaje de guardando
                    const submitBtn = e.target.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '💾 Guardando...';
                    }
                });
            }
        });
    </script>
</body>
</html>