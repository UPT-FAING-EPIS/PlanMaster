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
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>
    
    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container">
            <!-- Información del proyecto -->
            <div class="project-header">
                <div class="project-info">
                    <h1>⛓️ Cadena de Valor</h1>
                    <p class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></p>
                    <p class="company-name"><?php echo htmlspecialchars($project['company_name']); ?></p>
                </div>
            </div>
            
            <!-- Contexto de la Cadena de Valor -->
            <div class="context-box">
                <h3>🎯 Diagnóstico de Cadena de Valor</h3>
                <p>Evalúe cada aspecto de la gestión comercial de su empresa calificando del <strong>0 al 4</strong> según el siguiente criterio:</p>
                <div class="rating-scale">
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
            
            <!-- Navegación a siguiente sección -->
            <?php if ($projectController->isValueChainComplete($project_id)): ?>
            <div class="next-section">
                <div class="completion-message">
                    <h3>✅ Cadena de Valor Completada</h3>
                    <p>Has completado exitosamente el diagnóstico de la Cadena de Valor.</p>
                    
                    <div class="next-section-info">
                        <h4>Siguiente paso: Matriz BCG</h4>
                        <p>El siguiente paso es realizar la <strong>Matriz BCG</strong> para analizar la cartera de productos.</p>
                        <div class="action-buttons">
                            <a href="bcg-matrix.php?project_id=<?php echo $project_id; ?>" class="btn btn-continue">
                                📊 Continuar a Matriz BCG
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="incomplete-message">
                <p><strong>⚠️ Complete todas las preguntas</strong></p>
                <p>Una vez completado el diagnóstico, podrás ver tu potencial de mejora y continuar con la Matriz BCG.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../Users/footer.php'; ?>
    
    <!-- Mensajes de éxito/error -->
    <?php if (isset($_GET['success'])): ?>
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
    </script>
</body>
</html>