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

// Obtener datos FODA existentes (oportunidades y amenazas del PEST)
$pestFoda = $projectController->getFodaAnalysis($project_id);
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
    
    <style>
        /* Estilos para el diagrama PEST mejorado */
        .pest-chart-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .chart-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .chart-header h3 {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .chart-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            margin: 0;
        }
        
        .pest-bars-grid {
            display: grid;
            gap: 20px;
        }
        
        .pest-bar-item {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .pest-bar-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        
        .bar-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            gap: 15px;
        }
        
        .bar-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }
        
        .bar-icon.social { background: linear-gradient(135deg, #60a5fa, #3b82f6); }
        .bar-icon.political { background: linear-gradient(135deg, #f87171, #ef4444); }
        .bar-icon.economic { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
        .bar-icon.tech { background: linear-gradient(135deg, #a78bfa, #8b5cf6); }
        .bar-icon.environmental { background: linear-gradient(135deg, #34d399, #10b981); }
        
        .bar-info {
            flex: 1;
        }
        
        .bar-title {
            color: white;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.2;
        }
        
        .bar-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            margin-top: 2px;
        }
        
        .bar-score {
            text-align: right;
        }
        
        .score-number {
            color: #fbbf24;
            font-size: 28px;
            font-weight: 700;
            line-height: 1;
        }
        
        .score-max {
            color: rgba(255, 255, 255, 0.8);
            font-size: 16px;
        }
        
        .bar-track {
            position: relative;
            height: 12px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 6px;
            overflow: hidden;
        }
        
        .bar-fill {
            height: 100%;
            width: 0%;
            border-radius: 6px;
            position: relative;
            overflow: hidden;
        }
        
        .social-bar { background: linear-gradient(90deg, #60a5fa, #3b82f6); }
        .political-bar { background: linear-gradient(90deg, #f87171, #ef4444); }
        .economic-bar { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
        .tech-bar { background: linear-gradient(90deg, #a78bfa, #8b5cf6); }
        .environmental-bar { background: linear-gradient(90deg, #34d399, #10b981); }
        
        .bar-markers {
            position: absolute;
            top: -20px;
            left: 0;
            right: 0;
            height: 16px;
        }
        
        .marker {
            position: absolute;
            color: rgba(255, 255, 255, 0.6);
            font-size: 11px;
            font-weight: 500;
            transform: translateX(-50%);
        }
        
        @media (min-width: 768px) {
            .pest-bars-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        @media (min-width: 1200px) {
            .pest-bars-grid {
                grid-template-columns: 1fr;
                max-width: 800px;
                margin: 0 auto;
            }
        }
    </style>
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
            <!-- Header PEST -->
            <div class="pest-header">
                <div class="pest-info">
                    <h3>🎯 Diagnóstico PEST</h3>
                </div>
                
                <!-- Botón de continuar si está completo -->
                <?php if ($projectController->isPestComplete($project_id)): ?>
                <div class="continue-button-pest">
                    <a href="<?php echo getBaseUrl(); ?>/Views/Projects/strategies.php?id=<?php echo $project_id; ?>" 
                       class="btn-continue-strategies">
                        📈 Continuar con el siguiente análisis 🧠 Estrategias
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Contexto PEST -->
            <div class="context-box">
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
            <form id="pest-form" action="<?php echo getBaseUrl(); ?>/Controllers/PestController.php?action=save_pest" method="POST" class="pest-form">
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                
                <!-- DIAGRAMA DE BARRAS PEST MEJORADO -->
                <div class="pest-chart-container">
                    <div class="chart-header">
                        <h3>📊 Análisis en Tiempo Real por Factores PEST</h3>
                        <p>Complete las preguntas para ver el impacto de cada factor en su entorno empresarial</p>
                    </div>
                    
                    <div class="pest-bars-grid">
                        <!-- Factor Social -->
                        <div class="pest-bar-item">
                            <div class="bar-header">
                                <div class="bar-icon social">👥</div>
                                <div class="bar-info">
                                    <div class="bar-title">SOCIAL Y DEMOGRÁFICO</div>
                                    <div class="bar-subtitle">Preguntas 1-5</div>
                                </div>
                                <div class="bar-score">
                                    <span id="score-social" class="score-number">0</span>
                                    <span class="score-max">/100</span>
                                </div>
                            </div>
                            <div class="bar-track">
                                <div id="bar-social" class="bar-fill social-bar"></div>
                                <div class="bar-markers">
                                    <span class="marker" style="left: 20%;">20</span>
                                    <span class="marker" style="left: 40%;">40</span>
                                    <span class="marker" style="left: 60%;">60</span>
                                    <span class="marker" style="left: 80%;">80</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Factor Político -->
                        <div class="pest-bar-item">
                            <div class="bar-header">
                                <div class="bar-icon political">🏛️</div>
                                <div class="bar-info">
                                    <div class="bar-title">POLÍTICO</div>
                                    <div class="bar-subtitle">Preguntas 6-10</div>
                                </div>
                                <div class="bar-score">
                                    <span id="score-politic" class="score-number">0</span>
                                    <span class="score-max">/100</span>
                                </div>
                            </div>
                            <div class="bar-track">
                                <div id="bar-politic" class="bar-fill political-bar"></div>
                                <div class="bar-markers">
                                    <span class="marker" style="left: 20%;">20</span>
                                    <span class="marker" style="left: 40%;">40</span>
                                    <span class="marker" style="left: 60%;">60</span>
                                    <span class="marker" style="left: 80%;">80</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Factor Económico -->
                        <div class="pest-bar-item">
                            <div class="bar-header">
                                <div class="bar-icon economic">💰</div>
                                <div class="bar-info">
                                    <div class="bar-title">ECONÓMICO</div>
                                    <div class="bar-subtitle">Preguntas 11-15</div>
                                </div>
                                <div class="bar-score">
                                    <span id="score-econ" class="score-number">0</span>
                                    <span class="score-max">/100</span>
                                </div>
                            </div>
                            <div class="bar-track">
                                <div id="bar-econ" class="bar-fill economic-bar"></div>
                                <div class="bar-markers">
                                    <span class="marker" style="left: 20%;">20</span>
                                    <span class="marker" style="left: 40%;">40</span>
                                    <span class="marker" style="left: 60%;">60</span>
                                    <span class="marker" style="left: 80%;">80</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Factor Tecnológico -->
                        <div class="pest-bar-item">
                            <div class="bar-header">
                                <div class="bar-icon tech">🔧</div>
                                <div class="bar-info">
                                    <div class="bar-title">TECNOLÓGICO</div>
                                    <div class="bar-subtitle">Preguntas 16-20</div>
                                </div>
                                <div class="bar-score">
                                    <span id="score-tech" class="score-number">0</span>
                                    <span class="score-max">/100</span>
                                </div>
                            </div>
                            <div class="bar-track">
                                <div id="bar-tech" class="bar-fill tech-bar"></div>
                                <div class="bar-markers">
                                    <span class="marker" style="left: 20%;">20</span>
                                    <span class="marker" style="left: 40%;">40</span>
                                    <span class="marker" style="left: 60%;">60</span>
                                    <span class="marker" style="left: 80%;">80</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Factor Medioambiental -->
                        <div class="pest-bar-item">
                            <div class="bar-header">
                                <div class="bar-icon environmental">🌱</div>
                                <div class="bar-info">
                                    <div class="bar-title">MEDIOAMBIENTAL</div>
                                    <div class="bar-subtitle">Preguntas 21-25</div>
                                </div>
                                <div class="bar-score">
                                    <span id="score-env" class="score-number">0</span>
                                    <span class="score-max">/100</span>
                                </div>
                            </div>
                            <div class="bar-track">
                                <div id="bar-env" class="bar-fill environmental-bar"></div>
                                <div class="bar-markers">
                                    <span class="marker" style="left: 20%;">20</span>
                                    <span class="marker" style="left: 40%;">40</span>
                                    <span class="marker" style="left: 60%;">60</span>
                                    <span class="marker" style="left: 80%;">80</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="questions-container">
                    <h3>📋 Autodiagnóstico Entorno Global P.E.S.T.</h3>
                    
                    <?php foreach ($questions as $index => $question): ?>
                    <div class="question-item">
                        <div class="question-text">
                            <span class="question-number"><?php echo $index; ?>.</span>
                            <span><?php echo htmlspecialchars($question); ?></span>
                        </div>
                        <div class="rating-options">
                            <?php for ($rating = 0; $rating <= 4; $rating++): ?>
                            <div class="rating-option">
                                <input type="radio" 
                                       id="q<?php echo $index; ?>_r<?php echo $rating; ?>" 
                                       name="responses[<?php echo $index; ?>]" 
                                       value="<?php echo $rating; ?>"
                                       <?php if (isset($pestData[$index]) && $pestData[$index]['rating'] == $rating): ?>checked<?php endif; ?>>
                                <label for="q<?php echo $index; ?>_r<?php echo $rating; ?>"><?php echo $rating; ?></label>
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
            
            <!-- Sección FODA derivada del PEST -->
            <div class="pest-foda-section">
                <h3>🎯 Oportunidades y Amenazas del Entorno</h3>
                <p style="text-align: center; margin-bottom: 25px; color: #6b7280;">
                    Una vez analizado el entorno externo PEST, identifique las <strong>oportunidades y amenazas</strong> más relevantes que desee que se reflejen en el análisis FODA de su Plan Estratégico.
                </p>
                
                <form id="pest-foda-form" action="<?php echo getBaseUrl(); ?>/Controllers/ProjectController.php?action=save_foda" method="POST">
                    <input type="hidden" name="action" value="save_foda">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                    <input type="hidden" name="source" value="pest-analysis">
                    
                    <div class="foda-grid">
                        <!-- Oportunidades -->
                        <div class="foda-column oportunidades">
                            <h4>🌟 OPORTUNIDADES</h4>
                            <div class="foda-items" id="oportunidades-container">
                                <?php if (!empty($pestFoda['oportunidades'])): ?>
                                    <?php foreach ($pestFoda['oportunidades'] as $oportunidad): ?>
                                        <div class="foda-item">
                                            <textarea name="oportunidades[]" placeholder="Escriba una oportunidad..." maxlength="500"><?php echo htmlspecialchars($oportunidad['item_text']); ?></textarea>
                                            <button type="button" class="btn-remove-foda" onclick="removeFodaItem(this, 'oportunidades')">❌</button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <!-- Campo vacío por defecto -->
                                <div class="foda-item">
                                    <textarea name="oportunidades[]" placeholder="Escriba una oportunidad..." maxlength="500"></textarea>
                                    <button type="button" class="btn-remove-foda" onclick="removeFodaItem(this, 'oportunidades')">❌</button>
                                </div>
                            </div>
                            <button type="button" class="btn-add-foda" onclick="addOportunidad()">
                                ➕ Agregar Oportunidad
                            </button>
                        </div>
                        
                        <!-- Amenazas -->
                        <div class="foda-column amenazas">
                            <h4>⚠️ AMENAZAS</h4>
                            <div class="foda-items" id="amenazas-container">
                                <?php if (!empty($pestFoda['amenazas'])): ?>
                                    <?php foreach ($pestFoda['amenazas'] as $amenaza): ?>
                                        <div class="foda-item">
                                            <textarea name="amenazas[]" placeholder="Escriba una amenaza..." maxlength="500"><?php echo htmlspecialchars($amenaza['item_text']); ?></textarea>
                                            <button type="button" class="btn-remove-foda" onclick="removeFodaItem(this, 'amenazas')">❌</button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <!-- Campo vacío por defecto -->
                                <div class="foda-item">
                                    <textarea name="amenazas[]" placeholder="Escriba una amenaza..." maxlength="500"></textarea>
                                    <button type="button" class="btn-remove-foda" onclick="removeFodaItem(this, 'amenazas')">❌</button>
                                </div>
                            </div>
                            <button type="button" class="btn-add-foda" onclick="addAmenaza()">
                                ➕ Agregar Amenaza
                            </button>
                        </div>
                    </div>
                    
                    <!-- Botón de guardar FODA -->
                    <div class="pest-actions" style="margin-top: 30px; text-align: center;">
                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; border: none; padding: 15px 30px; border-radius: 10px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(34, 197, 94, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(34, 197, 94, 0.3)'">
                            💾 Guardar Oportunidades y Amenazas
                        </button>
                    </div>
                </form>
            </div>
            
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

    <?php if (isset($_GET['success_foda'])): ?>
    <div class="alert alert-success" id="alertMessage">
        ✅ Oportunidades y amenazas guardadas exitosamente
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
                updatePestChart();
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
            updatePestChart();
        });

        // Actualizar diagrama de barras PEST
        function updatePestChart() {
            // Mapear preguntas por bloque (5 preguntas cada uno)
            const groups = {
                social: [1,2,3,4,5],
                env: [21,22,23,24,25],
                politic: [6,7,8,9,10],
                econ: [11,12,13,14,15],
                tech: [16,17,18,19,20]
            };

            function calcTotal(ids) {
                let sum = 0;
                ids.forEach(q => {
                    const sel = document.querySelector('input[name="responses['+q+']"]:checked');
                    if (sel) sum += parseInt(sel.value, 10);
                });
                // Cada pregunta 0-4 -> mapear a 0-20 puntos multiplicando por 5
                return sum * 5;
            }

            const socialVal = calcTotal(groups.social);
            const envVal = calcTotal(groups.env);
            const politicVal = calcTotal(groups.politic);
            const econVal = calcTotal(groups.econ);
            const techVal = calcTotal(groups.tech);

            function setBar(barId, scoreId, value) {
                const pct = Math.max(0, Math.min(100, value));
                const bar = document.getElementById(barId);
                const scoreElement = document.getElementById(scoreId);
                
                if (bar) {
                    bar.style.width = pct + '%';
                    // Añadir animación suave
                    bar.style.transition = 'width 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                }
                if (scoreElement) {
                    scoreElement.innerText = value;
                }
            }

            setBar('bar-social', 'score-social', socialVal);
            setBar('bar-env', 'score-env', envVal);
            setBar('bar-politic', 'score-politic', politicVal);
            setBar('bar-econ', 'score-econ', econVal);
            setBar('bar-tech', 'score-tech', techVal);
        }

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

        // ===============================================
        // FUNCIONES CRUD PARA OPORTUNIDADES Y AMENAZAS
        // ===============================================

        // Agregar elemento FODA
        function addFodaItem(type, text = '', index = null) {
            const container = document.getElementById(`${type}-container`);
            if (!container) return;

            const itemDiv = document.createElement('div');
            itemDiv.className = 'foda-item';
            itemDiv.innerHTML = `
                <textarea 
                    name="${type}[]" 
                    placeholder="Escriba una ${type.slice(0, -1)}..." 
                    maxlength="500"
                    data-index="${index !== null ? index : container.children.length}"
                >${text}</textarea>
                <button type="button" class="btn-remove-foda" onclick="removeFodaItem(this, '${type}')">
                    ❌
                </button>
            `;

            container.appendChild(itemDiv);

            // Focus en el nuevo textarea si está vacío
            if (!text) {
                const textarea = itemDiv.querySelector('textarea');
                textarea.focus();
            }
        }

        // Remover elemento FODA
        function removeFodaItem(button, type) {
            const item = button.closest('.foda-item');
            const container = document.getElementById(`${type}-container`);

            // No permitir eliminar si es el único elemento
            if (container.children.length <= 1) {
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
                updateFodaIndices(type);
            }, 300);
        }

        // Actualizar índices de elementos FODA
        function updateFodaIndices(type) {
            const container = document.getElementById(`${type}-container`);
            Array.from(container.children).forEach((item, index) => {
                const textarea = item.querySelector('textarea');
                textarea.setAttribute('data-index', index);
            });
        }

        // Funciones auxiliares para los botones
        function addOportunidad() {
            addFodaItem('oportunidades');
        }

        function addAmenaza() {
            addFodaItem('amenazas');
        }

        // Hacer funciones disponibles globalmente
        window.addOportunidad = addOportunidad;
        window.addAmenaza = addAmenaza;
        window.removeFodaItem = removeFodaItem;
    </script>
</body>
</html>