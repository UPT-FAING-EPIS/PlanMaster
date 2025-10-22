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
$baseUrl = getBaseUrl();

// Obtener las preguntas desde el modelo PEST
require_once __DIR__ . '/../../Models/PestAnalysis.php';
$pestAnalysisModel = new PestAnalysis();
$questions = $pestAnalysisModel->getStandardQuestions();

// Obtener respuestas existentes de PEST (si las hay)
$pestData = $projectController->getPestAnalysis($project_id);
$pestSummary = $projectController->getPestSummary($project_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis PEST - <?php echo htmlspecialchars($project['project_name']); ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_project.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_pest_analysis.css">
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
                    <h2>🏢 <?php echo htmlspecialchars($project['project_name']); ?></h2>
                    <p class="project-description"><?php echo htmlspecialchars($project['project_description']); ?></p>
                    <p><strong>Paso 9:</strong> Análisis Externo Macroentorno (PEST)</p>
                </div>
            </div>
            
            <!-- Contexto PEST -->
            <div class="context-box">
                <h3>🎯 Diagnóstico PEST</h3>
                <p><strong>PEST</strong> es un acrónimo que representa el macroentorno de la empresa:</p>
                <ul style="margin: 15px 0; padding-left: 20px;">
                    <li><strong>Políticos:</strong> Factores que determinan la actividad empresarial (legislación, normas, tratados comerciales)</li>
                    <li><strong>Económicos:</strong> Comportamiento económico general (tasas, empleo, índices de precios)</li>
                    <li><strong>Sociales:</strong> Fuerzas sociales que afectan actitudes e intereses (demografía, estilos de vida)</li>
                    <li><strong>Tecnológicos:</strong> Avances tecnológicos que impulsan o transforman los negocios</li>
                </ul>
                <p>Evalúe cada aspecto calificando del <strong>0 al 4</strong> según el siguiente criterio:</p>
                <div class="rating-scale">
                    <div class="scale-item">
                        <span class="scale-number">0</span>
                        <span class="scale-text">En total desacuerdo</span>
                    </div>
                    <div class="scale-item">
                        <span class="scale-number">1</span>
                        <span class="scale-text">No está de acuerdo</span>
                    </div>
                    <div class="scale-item">
                        <span class="scale-number">2</span>
                        <span class="scale-text">Está de acuerdo</span>
                    </div>
                    <div class="scale-item">
                        <span class="scale-number">3</span>
                        <span class="scale-text">Está bastante de acuerdo</span>
                    </div>
                    <div class="scale-item">
                        <span class="scale-number">4</span>
                        <span class="scale-text">En total acuerdo</span>
                    </div>
                </div>
            </div>
            
            <!-- Mostrar resultados si existe cálculo -->
            <?php if ($pestSummary): ?>
            <div class="pest-results">
                <h3>📊 Resultados del Análisis PEST</h3>
                <div class="results-grid">
                    <div class="result-card">
                        <div class="result-number"><?php echo $pestSummary['total_rating']; ?></div>
                        <div class="result-label">Puntuación Total</div>
                        <div class="result-sublabel">de <?php echo $pestSummary['max_possible']; ?> puntos máximos</div>
                    </div>
                    <div class="result-card highlight">
                        <div class="result-number"><?php echo $pestSummary['average_rating']; ?></div>
                        <div class="result-label">Promedio</div>
                        <div class="result-sublabel">Valoración media</div>
                    </div>
                    <div class="result-card">
                        <div class="result-number"><?php echo $pestSummary['percentage']; ?>%</div>
                        <div class="result-label">Porcentaje</div>
                        <div class="result-sublabel">Del total posible</div>
                    </div>
                </div>
                
                <div class="pest-interpretation">
                    <div class="interpretation <?php echo $pestSummary['interpretation']['color']; ?>">
                        <strong>Interpretación: <?php echo $pestSummary['interpretation']['level']; ?></strong><br>
                        <?php echo $pestSummary['interpretation']['description']; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Formulario de diagnóstico -->
            <form action="<?php echo getBaseUrl(); ?>/Controllers/ProjectController.php?action=save_pest_analysis" method="POST" class="pest-form">
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                
                <div class="questions-container">
                    <h3>📋 Autodiagnóstico Entorno Global P.E.S.T.</h3>
                    
                    <?php foreach ($questions as $index => $question): ?>
                    <div class="question-item">
                        <div class="question-text">
                            <span class="question-number"><?php echo ($index + 1); ?>.</span>
                            <span><?php echo htmlspecialchars($question); ?></span>
                        </div>
                        <div class="rating-options">
                            <?php for ($rating = 0; $rating <= 4; $rating++): ?>
                            <div class="rating-option">
                                <input type="radio" 
                                       id="q<?php echo ($index + 1); ?>_r<?php echo $rating; ?>" 
                                       name="responses[<?php echo ($index + 1); ?>]" 
                                       value="<?php echo $rating; ?>"
                                       <?php if (isset($pestData[$index + 1]) && $pestData[$index + 1]['rating'] == $rating): ?>checked<?php endif; ?>>
                                <label for="q<?php echo ($index + 1); ?>_r<?php echo $rating; ?>"><?php echo $rating; ?></label>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Botones de acción -->
                <div class="form-actions">
                    <button type="button" onclick="calculateSummary()" class="btn btn-secondary">📊 Calcular</button>
                    <button type="submit" class="btn btn-primary btn-save-pest" disabled>💾 Guardar</button>
                    <a href="<?php echo getBaseUrl(); ?>/Views/Users/projects.php" class="btn btn-outline">🔙 Salir y Guardar</a>
                </div>
            </form>
            
            <!-- Navegación a siguiente sección -->
            <?php if ($projectController->isPestComplete($project_id)): ?>
            <div class="next-section">
                <h3>✅ Análisis PEST Completado</h3>
                <p>Has completado exitosamente el diagnóstico del entorno externo PEST.</p>
                <p>Puedes continuar con la siguiente sección del plan estratégico.</p>
            </div>
            <?php else: ?>
            <div class="incomplete-message">
                <p><strong>⚠️ Complete todas las preguntas</strong></p>
                <p>Una vez completado el diagnóstico, podrá guardar y continuar con otras secciones del Plan Estratégico.</p>
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
            setTimeout(() => { alertMessage.style.display = 'none'; }, 5000);
        }

        // Interactividad para opciones de rating
        document.querySelectorAll('.rating-option input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const questionItem = this.closest('.question-item');
                questionItem.querySelectorAll('.rating-option').forEach(option => option.classList.remove('selected'));
                this.closest('.rating-option').classList.add('selected');
                updateProgressCounter();
            });
        });

        // Actualizar contador de progreso
        function updateProgressCounter() {
            const totalQuestions = <?php echo count($questions); ?>;
            const answered = document.querySelectorAll('.rating-option input[type="radio"]:checked').length;

            let progressIndicator = document.querySelector('.progress-indicator');
            if (!progressIndicator) {
                progressIndicator = document.createElement('span');
                progressIndicator.className = 'progress-indicator';
                document.querySelector('.questions-container h3').appendChild(progressIndicator);
            }

            const percentage = (answered / totalQuestions) * 100;
            progressIndicator.innerHTML = ` (${answered}/${totalQuestions} - ${Math.round(percentage)}%)`;

            const saveBtn = document.querySelector('.btn-save-pest');
            if (answered === totalQuestions) {
                saveBtn.disabled = false;
                saveBtn.style.opacity = '1';
            } else {
                saveBtn.disabled = true;
                saveBtn.style.opacity = '0.6';
            }
        }

        // Inicializar al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            updateProgressCounter();
        });

        // Función de cálculo
        function calculateSummary() {
            const totalQuestions = <?php echo count($questions); ?>;
            const checked = document.querySelectorAll('.rating-option input[type="radio"]:checked');
            
            if (checked.length !== totalQuestions) {
                alert('Por favor responda todas las preguntas antes de calcular.');
                return;
            }

            let sum = 0;
            checked.forEach(input => sum += parseInt(input.value, 10));
            const avg = (sum / totalQuestions).toFixed(2);
            
            let interpretation = '';
            if (avg >= 3.5) {
                interpretation = 'Entorno muy favorable para su empresa';
            } else if (avg >= 2.5) {
                interpretation = 'Entorno moderadamente favorable';
            } else if (avg >= 1.5) {
                interpretation = 'Entorno con desafíos significativos';
            } else {
                interpretation = 'Entorno muy desafiante, requiere estrategias especiales';
            }
            
            alert(`Resumen Análisis PEST:\n\nPuntuación total: ${sum}/${totalQuestions * 4}\nMedia: ${avg}/4\n\nInterpretación: ${interpretation}\n\n(Este cálculo es informativo. Use "Guardar" para almacenar las respuestas)`);
        }
    </script>
</body>
</html>