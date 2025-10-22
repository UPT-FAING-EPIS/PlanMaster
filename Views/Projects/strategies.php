<?php
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../config/url_config.php';

// Verificar que el usuario esté logueado
AuthController::requireLogin();

// Obtener el ID del proyecto
$project_id = intval($_GET['id'] ?? 0);
if ($project_id === 0) {
    header("Location: " . getBaseUrl() . "/Views/Users/projects.php");
    exit();
}

// Obtener datos del proyecto y del usuario
$projectController = new ProjectController();
$project = $projectController->getProject($project_id);
$user = AuthController::getCurrentUser();

if (!$project) {
    $_SESSION['error'] = "Proyecto no encontrado";
    header("Location: " . getBaseUrl() . "/Views/Users/projects.php");
    exit();
}

// Obtener estrategias existentes
$strategiesData = $projectController->getStrategiesAnalysis($project_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identificación de Estrategias - <?php echo htmlspecialchars($project['project_name']); ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_project.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_strategies.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo getBaseUrl(); ?>/Resources/favicon.ico">
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section strategies-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="hero-content">
                        <div class="breadcrumb-nav">
                            <a href="<?php echo getBaseUrl(); ?>/Views/Users/dashboard.php" class="breadcrumb-link">Dashboard</a>
                            <span class="breadcrumb-separator">></span>
                            <a href="project.php?id=<?php echo $project_id; ?>" class="breadcrumb-link">Proyecto</a>
                            <span class="breadcrumb-separator">></span>
                            <span class="breadcrumb-current">Identificación de Estrategias</span>
                        </div>
                        
                        <h1 class="hero-title">
                            <span class="step-number">10.</span>
                            Identificación de Estrategias
                        </h1>
                        
                        <div class="project-info">
                            <div class="project-badge">
                                <i class="icon-briefcase"></i>
                                <span><?php echo htmlspecialchars($project['project_name']); ?></span>
                            </div>
                            <div class="company-badge">
                                <i class="icon-building"></i>
                                <span><?php echo htmlspecialchars($project['company_name']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="hero-icon">
                        🧠
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container">
            <!-- Contexto de identificación de estrategias -->
            <div class="context-box">
                <h3>🎯 Identificación de Estrategias</h3>
                <p>Basándose en los análisis previos (FODA, BCG, Porter y PEST), identifiquemos las estrategias más efectivas para su organización.</p>
                <p><strong>En esta sección definiremos:</strong></p>
                <ul>
                    <li><strong>Estrategias Competitivas:</strong> Cómo competir en el mercado</li>
                    <li><strong>Estrategias de Crecimiento:</strong> Planes de expansión y desarrollo</li>
                    <li><strong>Estrategias de Innovación:</strong> Mejoras en productos/servicios</li>
                    <li><strong>Estrategias de Diferenciación:</strong> Ventajas competitivas únicas</li>
                </ul>
            </div>

            <!-- Formulario de identificación de estrategias -->
            <form action="<?php echo getBaseUrl(); ?>/Controllers/ProjectController.php?action=save_strategies" method="POST" class="strategies-form">
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                
                <div class="strategies-container">
                    <!-- ESTRATEGIAS COMPETITIVAS -->
                    <div class="strategy-section competitive">
                        <h3>⚔️ Estrategias Competitivas</h3>
                        <p class="section-description">
                            Define cómo tu organización competirá en el mercado y se posicionará frente a la competencia.
                        </p>
                        <div id="competitive-strategies-container">
                            <?php if (!empty($strategiesData['competitive'])): ?>
                                <?php foreach ($strategiesData['competitive'] as $index => $strategy): ?>
                                    <div class="strategy-item">
                                        <div class="strategy-input-group">
                                            <input type="text" name="competitive_name[]" class="strategy-name" 
                                                   placeholder="Nombre de la estrategia"
                                                   value="<?php echo htmlspecialchars($strategy['name']); ?>">
                                            <textarea name="competitive_description[]" class="strategy-description" 
                                                      placeholder="Descripción detallada de la estrategia..."
                                                      rows="3"><?php echo htmlspecialchars($strategy['description']); ?></textarea>
                                            <select name="competitive_priority[]" class="strategy-priority">
                                                <option value="alta" <?php echo $strategy['priority'] == 'alta' ? 'selected' : ''; ?>>Alta Prioridad</option>
                                                <option value="media" <?php echo $strategy['priority'] == 'media' ? 'selected' : ''; ?>>Media Prioridad</option>
                                                <option value="baja" <?php echo $strategy['priority'] == 'baja' ? 'selected' : ''; ?>>Baja Prioridad</option>
                                            </select>
                                        </div>
                                        <button type="button" class="btn-remove-strategy" onclick="removeStrategy(this)">&times;</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="strategy-item">
                                    <div class="strategy-input-group">
                                        <input type="text" name="competitive_name[]" class="strategy-name" 
                                               placeholder="Ej: Liderazgo en costos">
                                        <textarea name="competitive_description[]" class="strategy-description" 
                                                  placeholder="Ej: Optimizar procesos para reducir costos operativos y ofrecer precios más competitivos..."
                                                  rows="3"></textarea>
                                        <select name="competitive_priority[]" class="strategy-priority">
                                            <option value="alta">Alta Prioridad</option>
                                            <option value="media">Media Prioridad</option>
                                            <option value="baja">Baja Prioridad</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn-remove-strategy" onclick="removeStrategy(this)">&times;</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn-add-strategy" onclick="addStrategy('competitive')">
                            + Agregar Estrategia Competitiva
                        </button>
                    </div>

                    <!-- ESTRATEGIAS DE CRECIMIENTO -->
                    <div class="strategy-section growth">
                        <h3>📈 Estrategias de Crecimiento</h3>
                        <p class="section-description">
                            Planes para expandir la organización, aumentar ingresos y captar nuevos mercados.
                        </p>
                        <div id="growth-strategies-container">
                            <?php if (!empty($strategiesData['growth'])): ?>
                                <?php foreach ($strategiesData['growth'] as $index => $strategy): ?>
                                    <div class="strategy-item">
                                        <div class="strategy-input-group">
                                            <input type="text" name="growth_name[]" class="strategy-name" 
                                                   placeholder="Nombre de la estrategia"
                                                   value="<?php echo htmlspecialchars($strategy['name']); ?>">
                                            <textarea name="growth_description[]" class="strategy-description" 
                                                      placeholder="Descripción detallada de la estrategia..."
                                                      rows="3"><?php echo htmlspecialchars($strategy['description']); ?></textarea>
                                            <select name="growth_priority[]" class="strategy-priority">
                                                <option value="alta" <?php echo $strategy['priority'] == 'alta' ? 'selected' : ''; ?>>Alta Prioridad</option>
                                                <option value="media" <?php echo $strategy['priority'] == 'media' ? 'selected' : ''; ?>>Media Prioridad</option>
                                                <option value="baja" <?php echo $strategy['priority'] == 'baja' ? 'selected' : ''; ?>>Baja Prioridad</option>
                                            </select>
                                        </div>
                                        <button type="button" class="btn-remove-strategy" onclick="removeStrategy(this)">&times;</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="strategy-item">
                                    <div class="strategy-input-group">
                                        <input type="text" name="growth_name[]" class="strategy-name" 
                                               placeholder="Ej: Expansión a nuevos mercados">
                                        <textarea name="growth_description[]" class="strategy-description" 
                                                  placeholder="Ej: Identificar y penetrar en mercados geográficos adyacentes con alta demanda..."
                                                  rows="3"></textarea>
                                        <select name="growth_priority[]" class="strategy-priority">
                                            <option value="alta">Alta Prioridad</option>
                                            <option value="media">Media Prioridad</option>
                                            <option value="baja">Baja Prioridad</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn-remove-strategy" onclick="removeStrategy(this)">&times;</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn-add-strategy" onclick="addStrategy('growth')">
                            + Agregar Estrategia de Crecimiento
                        </button>
                    </div>

                    <!-- ESTRATEGIAS DE INNOVACIÓN -->
                    <div class="strategy-section innovation">
                        <h3>💡 Estrategias de Innovación</h3>
                        <p class="section-description">
                            Iniciativas para mejorar productos, servicios o procesos mediante la innovación.
                        </p>
                        <div id="innovation-strategies-container">
                            <?php if (!empty($strategiesData['innovation'])): ?>
                                <?php foreach ($strategiesData['innovation'] as $index => $strategy): ?>
                                    <div class="strategy-item">
                                        <div class="strategy-input-group">
                                            <input type="text" name="innovation_name[]" class="strategy-name" 
                                                   placeholder="Nombre de la estrategia"
                                                   value="<?php echo htmlspecialchars($strategy['name']); ?>">
                                            <textarea name="innovation_description[]" class="strategy-description" 
                                                      placeholder="Descripción detallada de la estrategia..."
                                                      rows="3"><?php echo htmlspecialchars($strategy['description']); ?></textarea>
                                            <select name="innovation_priority[]" class="strategy-priority">
                                                <option value="alta" <?php echo $strategy['priority'] == 'alta' ? 'selected' : ''; ?>>Alta Prioridad</option>
                                                <option value="media" <?php echo $strategy['priority'] == 'media' ? 'selected' : ''; ?>>Media Prioridad</option>
                                                <option value="baja" <?php echo $strategy['priority'] == 'baja' ? 'selected' : ''; ?>>Baja Prioridad</option>
                                            </select>
                                        </div>
                                        <button type="button" class="btn-remove-strategy" onclick="removeStrategy(this)">&times;</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="strategy-item">
                                    <div class="strategy-input-group">
                                        <input type="text" name="innovation_name[]" class="strategy-name" 
                                               placeholder="Ej: Desarrollo de productos digitales">
                                        <textarea name="innovation_description[]" class="strategy-description" 
                                                  placeholder="Ej: Crear una plataforma digital que complemente nuestros servicios tradicionales..."
                                                  rows="3"></textarea>
                                        <select name="innovation_priority[]" class="strategy-priority">
                                            <option value="alta">Alta Prioridad</option>
                                            <option value="media">Media Prioridad</option>
                                            <option value="baja">Baja Prioridad</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn-remove-strategy" onclick="removeStrategy(this)">&times;</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn-add-strategy" onclick="addStrategy('innovation')">
                            + Agregar Estrategia de Innovación
                        </button>
                    </div>

                    <!-- ESTRATEGIAS DE DIFERENCIACIÓN -->
                    <div class="strategy-section differentiation">
                        <h3>🎨 Estrategias de Diferenciación</h3>
                        <p class="section-description">
                            Elementos únicos que distinguen a tu organización de la competencia.
                        </p>
                        <div id="differentiation-strategies-container">
                            <?php if (!empty($strategiesData['differentiation'])): ?>
                                <?php foreach ($strategiesData['differentiation'] as $index => $strategy): ?>
                                    <div class="strategy-item">
                                        <div class="strategy-input-group">
                                            <input type="text" name="differentiation_name[]" class="strategy-name" 
                                                   placeholder="Nombre de la estrategia"
                                                   value="<?php echo htmlspecialchars($strategy['name']); ?>">
                                            <textarea name="differentiation_description[]" class="strategy-description" 
                                                      placeholder="Descripción detallada de la estrategia..."
                                                      rows="3"><?php echo htmlspecialchars($strategy['description']); ?></textarea>
                                            <select name="differentiation_priority[]" class="strategy-priority">
                                                <option value="alta" <?php echo $strategy['priority'] == 'alta' ? 'selected' : ''; ?>>Alta Prioridad</option>
                                                <option value="media" <?php echo $strategy['priority'] == 'media' ? 'selected' : ''; ?>>Media Prioridad</option>
                                                <option value="baja" <?php echo $strategy['priority'] == 'baja' ? 'selected' : ''; ?>>Baja Prioridad</option>
                                            </select>
                                        </div>
                                        <button type="button" class="btn-remove-strategy" onclick="removeStrategy(this)">&times;</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="strategy-item">
                                    <div class="strategy-input-group">
                                        <input type="text" name="differentiation_name[]" class="strategy-name" 
                                               placeholder="Ej: Servicio al cliente 24/7">
                                        <textarea name="differentiation_description[]" class="strategy-description" 
                                                  placeholder="Ej: Implementar un sistema de atención al cliente disponible las 24 horas con respuesta inmediata..."
                                                  rows="3"></textarea>
                                        <select name="differentiation_priority[]" class="strategy-priority">
                                            <option value="alta">Alta Prioridad</option>
                                            <option value="media">Media Prioridad</option>
                                            <option value="baja">Baja Prioridad</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn-remove-strategy" onclick="removeStrategy(this)">&times;</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn-add-strategy" onclick="addStrategy('differentiation')">
                            + Agregar Estrategia de Diferenciación
                        </button>
                    </div>
                </div>

                <!-- Resumen de estrategias -->
                <div class="strategies-summary">
                    <h3>📋 Resumen de Estrategias</h3>
                    <div class="summary-grid">
                        <div class="summary-card competitive-summary">
                            <h4>⚔️ Competitivas</h4>
                            <p class="strategy-count" id="competitive-count">0 estrategias</p>
                        </div>
                        <div class="summary-card growth-summary">
                            <h4>📈 Crecimiento</h4>
                            <p class="strategy-count" id="growth-count">0 estrategias</p>
                        </div>
                        <div class="summary-card innovation-summary">
                            <h4>💡 Innovación</h4>
                            <p class="strategy-count" id="innovation-count">0 estrategias</p>
                        </div>
                        <div class="summary-card differentiation-summary">
                            <h4>🎨 Diferenciación</h4>
                            <p class="strategy-count" id="differentiation-count">0 estrategias</p>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="form-actions">
                    <button type="submit" class="btn-save-strategies">
                        <span class="btn-icon">💾</span>
                        Guardar Estrategias
                    </button>
                    
                    <button type="button" class="btn-auto-save" id="auto-save-btn">
                        <span class="btn-icon">⚡</span>
                        Guardado Automático: <span id="auto-save-status">Activado</span>
                    </button>
                    
                    <a href="project.php?id=<?php echo $project_id; ?>" class="btn-back">
                        <span class="btn-icon">←</span>
                        Volver al Proyecto
                    </a>
                </div>
            </form>

            <!-- Mensaje de ayuda -->
            <div class="help-box">
                <h4>💡 Consejos para identificar estrategias efectivas:</h4>
                <ul>
                    <li><strong>Base tus estrategias en los análisis previos:</strong> Utiliza la información del FODA, BCG y Porter</li>
                    <li><strong>Sé específico y medible:</strong> Define acciones concretas con resultados cuantificables</li>
                    <li><strong>Considera los recursos disponibles:</strong> Asegúrate de que las estrategias sean viables</li>
                    <li><strong>Establece prioridades:</strong> No todas las estrategias tienen la misma urgencia</li>
                    <li><strong>Piensa a largo plazo:</strong> Considera el impacto futuro de cada estrategia</li>
                </ul>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include '../Users/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="<?php echo getBaseUrl(); ?>/Publics/js/strategies.js"></script>
    
    <!-- Guardado automático -->
    <script>
        // Configurar guardado automático cada 30 segundos
        let autoSaveInterval;
        let isAutoSaveEnabled = true;
        
        document.addEventListener('DOMContentLoaded', function() {
            updateStrategyCounts();
            setupAutoSave();
        });
        
        function setupAutoSave() {
            if (isAutoSaveEnabled) {
                autoSaveInterval = setInterval(function() {
                    saveStrategiesAuto();
                }, 30000); // 30 segundos
            }
        }
        
        function updateStrategyCounts() {
            const categories = ['competitive', 'growth', 'innovation', 'differentiation'];
            categories.forEach(category => {
                const items = document.querySelectorAll(`#${category}-strategies-container .strategy-item`);
                const count = items.length;
                const countElement = document.getElementById(`${category}-count`);
                if (countElement) {
                    countElement.textContent = `${count} ${count === 1 ? 'estrategia' : 'estrategias'}`;
                }
            });
        }
    </script>
</body>
</html>