<?php
// Incluir configuraciones necesarias
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/PorterController.php';
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
$porterController = new PorterController();
$projectController = new ProjectController();

// Verificar que el proyecto existe y pertenece al usuario usando SOLO PorterController
$project = $porterController->getProjectForView($project_id);
if (!$project) {
    header("Location: " . getBaseUrl() . "/Views/Users/dashboard.php");
    exit();
}

// Obtener datos del usuario
$user = AuthController::getCurrentUser();

// Obtener análisis Porter existente
$porterAnalysis = $porterController->getPorterAnalysisForView($project_id);

// Si no hay análisis, inicializar el proyecto
if (empty($porterAnalysis)) {
    require_once __DIR__ . '/../../Models/PorterAnalysis.php';
    $porterModel = new PorterAnalysis();
    $porterModel->initializeForProject($project_id);
    $porterAnalysis = $porterController->getPorterAnalysisForView($project_id);
}

$porterScore = $porterController->getPorterScoreForView($project_id);
// Obtener datos FODA usando ProjectController (project_foda_analysis)
$porterFoda = $projectController->getFodaAnalysis($project_id);

// Obtener modelo para factores estándar
require_once __DIR__ . '/../../Models/PorterAnalysis.php';
$porterModel = new PorterAnalysis();
$standardFactors = $porterModel->getStandardFactors();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏛️ Matriz de Porter - <?php echo htmlspecialchars($project['project_name']); ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_project.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_porter_matrix.css">
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
    <div class="porter-container">
        <div class="porter-content">
            
            <!-- Header de Porter -->
            <div class="porter-header">
                <h1>🏛️ Matriz de Porter</h1>
                <p class="subtitle">Análisis de las 5 Fuerzas Competitivas</p>
                <?php if ($porterController->isPorterCompleteForView($project_id)): ?>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="<?php echo getBaseUrl(); ?>/Views/Projects/pest-analysis.php?id=<?php echo $project_id; ?>" 
                       class="btn-porter primary" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block; font-weight: 600;">
                        📈 Continuar con el siguiente análisis
                    </a>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Resultados del análisis -->
            <?php if ($porterScore && $porterScore['total_score'] > 0): ?>
            <div class="porter-results">
                <h3>📊 Resultados del Análisis Porter</h3>
                <div class="results-grid">
                    <div class="result-card">
                        <div class="result-number" id="total-score"><?php echo $porterScore['total_score']; ?></div>
                        <div class="result-label">Puntuación Total</div>
                        <div class="result-sublabel">de <?php echo $porterScore['max_score']; ?> puntos máximos</div>
                    </div>
                    <div class="result-card">
                        <div class="result-number" id="average-score"><?php echo $porterScore['average_score']; ?></div>
                        <div class="result-label">Promedio por Factor</div>
                        <div class="result-sublabel">Escala 1-5</div>
                    </div>
                    <div class="result-card highlight">
                        <div class="result-number" id="percentage-score"><?php echo $porterScore['percentage']; ?>%</div>
                        <div class="result-label">Competitividad</div>
                        <div class="result-sublabel" id="competitiveness-level"><?php echo $porterScore['competitiveness']; ?></div>
                    </div>
                </div>
                
                <div class="porter-recommendation">
                    <p class="recommendation-text" id="porter-recommendation">
                        <?php echo htmlspecialchars($porterScore['recommendation']); ?>
                    </p>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Instrucciones -->
            <div class="porter-instructions">
                <h3>📋 Instrucciones para el Análisis</h3>
                <p>A continuación marque con un punto en las casillas que estime conveniente según el estado actual de su empresa. Valore su perfil competitivo en la escala <strong>Hostil-Favorable</strong>. Al finalizar lea la conclusión para su caso particular relativa al análisis del entorno próximo.</p>
                
                <div class="porter-scale">
                    <div class="scale-item">
                        <span class="scale-value">1</span>
                        <span class="scale-label">Nada</span>
                    </div>
                    <div class="scale-item">
                        <span class="scale-value">2</span>
                        <span class="scale-label">Poco</span>
                    </div>
                    <div class="scale-item">
                        <span class="scale-value">3</span>
                        <span class="scale-label">Medio</span>
                    </div>
                    <div class="scale-item">
                        <span class="scale-value">4</span>
                        <span class="scale-label">Alto</span>
                    </div>
                    <div class="scale-item">
                        <span class="scale-value">5</span>
                        <span class="scale-label">Muy Alto</span>
                    </div>
                </div>
            </div>

            <!-- Formulario del análisis Porter -->
            <form id="porter-form" action="<?php echo getBaseUrl(); ?>/Controllers/PorterController.php?action=save_porter" method="POST">
                <input type="hidden" name="action" value="save_porter">
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                
                <?php
                $sectionTitles = [
                    'rivalidad' => '⚔️ Rivalidad entre Empresas del Sector',
                    'barreras_entrada' => '🚧 Barreras de Entrada',
                    'poder_clientes' => '👥 Poder de los Clientes',
                    'productos_sustitutivos' => '🔄 Productos Sustitutivos'
                ];
                
                foreach ($standardFactors as $category => $factors):
                ?>
                
                <!-- Sección <?php echo $sectionTitles[$category]; ?> -->
                <div class="porter-section">
                    <div class="porter-section-header">
                        <h2 class="porter-section-title"><?php echo $sectionTitles[$category]; ?></h2>
                    </div>
                    <div class="porter-section-content">
                        <table class="porter-factors-table">
                            <thead>
                                <tr>
                                    <th>Factor</th>
                                    <th>Hostil</th>
                                    <th colspan="5">Nivel de Evaluación</th>
                                    <th>Favorable</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>1</th>
                                    <th>2</th>
                                    <th>3</th>
                                    <th>4</th>
                                    <th>5</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($factors as $factor): 
                                    // Buscar datos existentes de la BD - USAR ID REAL DE LA BD
                                    $selectedValue = null;
                                    $factorDbId = null;
                                    
                                    if (isset($porterAnalysis[$category])) {
                                        foreach ($porterAnalysis[$category] as $existingFactor) {
                                            if ($existingFactor['factor_name'] === $factor['name']) {
                                                $selectedValue = $existingFactor['selected_value'];
                                                $factorDbId = $existingFactor['id'];  // USAR ID DE LA BD
                                                break;
                                            }
                                        }
                                    }
                                ?>
                                <tr>
                                    <td class="factor-name"><?php echo htmlspecialchars($factor['name']); ?></td>
                                    <td class="hostil-label"><?php echo htmlspecialchars($factor['hostil_label']); ?></td>
                                    
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <td>
                                        <div class="porter-radio-group">
                                            <label class="porter-radio">
                                                <input type="radio" 
                                                       name="responses[<?php echo $factorDbId; ?>]" 
                                                       value="<?php echo $i; ?>"
                                                       <?php echo ($selectedValue == $i) ? 'checked' : ''; ?>
                                                       <?php echo $factorDbId ? '' : 'disabled'; ?>>
                                                <span class="porter-radio-custom"><?php echo $i; ?></span>
                                            </label>
                                        </div>
                                    </td>
                                    <?php endfor; ?>
                                    
                                    <td class="favorable-label"><?php echo htmlspecialchars($factor['favorable_label']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <?php endforeach; ?>
                
                <!-- Botón de guardar análisis Porter -->
                <div class="porter-save-section">
                    <button type="submit" class="btn-porter primary" id="save-porter-btn">
                        💾 Guardar Análisis Porter
                    </button>
                </div>
                
                </form>
                
                <!-- Sección FODA derivada de Porter -->
                <div class="porter-foda-section">
                <form id="porter-foda-form" action="<?php echo getBaseUrl(); ?>/Controllers/ProjectController.php?action=save_foda" method="POST">
                    <input type="hidden" name="action" value="save_foda">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                    <input type="hidden" name="source" value="porter-matrix">
                    <h3>🎯 Oportunidades y Amenazas del Entorno</h3>
                    <p style="text-align: center; margin-bottom: 25px; color: #6b7280;">
                        Una vez analizado el entorno próximo de su empresa (análisis externo del microentorno), identifique las <strong>oportunidades y amenazas</strong> más relevantes que desee que se reflejen en el análisis FODA de su Plan Estratégico.
                    </p>
                    
                    <div class="foda-grid">
                        <!-- Oportunidades -->
                        <div class="foda-column oportunidades">
                            <h4>🌟 OPORTUNIDADES</h4>
                            <div class="foda-items" id="oportunidades-container">
                                <?php if (!empty($porterFoda['oportunidades'])): ?>
                                    <?php foreach ($porterFoda['oportunidades'] as $oportunidad): ?>
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
                                <?php if (!empty($porterFoda['amenazas'])): ?>
                                    <?php foreach ($porterFoda['amenazas'] as $amenaza): ?>
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
                </div>
                
                <!-- Botón de guardar FODA -->
                <div class="porter-actions" style="margin-top: 30px;">
                    <button type="submit" class="btn-porter primary" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);">
                        💾 Guardar Oportunidades y Amenazas
                    </button>
                </div>
                </form>
            
            <!-- Botones de navegación -->
            <div class="porter-actions">
                <a href="project.php?id=<?php echo $project_id; ?>" class="btn-porter secondary">
                    ← Volver al Proyecto
                </a>
            </div>
            
            <!-- Mensaje de completitud -->
            <?php if ($porterController->isPorterCompleteForView($project_id)): ?>
            <div class="porter-results" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-color: #22c55e;">
                <h3 style="color: #16a34a;">✅ Análisis Porter Completado</h3>
                <p style="text-align: center; margin-bottom: 0;">
                    Has completado exitosamente el análisis de las 5 fuerzas competitivas de Porter.
                </p>
            </div>
            <?php else: ?>
            <div class="porter-alert" style="background: #fef3c7; border-color: #f59e0b; color: #92400e;">
                ⚠️ <strong>Complete todos los factores</strong> para obtener una evaluación precisa del entorno competitivo.
            </div>
            <?php endif; ?>
            
        </div>
    </div>
    
    <!-- Footer -->
    <?php include '../Users/footer.php'; ?>
    
    <!-- Mensajes de éxito/error -->
    <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
    <div class="porter-alert success" id="alertMessage">
        ✅ Análisis Porter guardado exitosamente
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['success_foda'])): ?>
    <div class="porter-alert success" id="alertMessage">
        ✅ Oportunidades y amenazas guardadas exitosamente
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="porter-alert error" id="alertMessage">
        ❌ Error: <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
    <?php endif; ?>

    <!-- Scripts del sistema -->
    <script>
        // Datos del proyecto para JavaScript
        const PROJECT_ID = <?php echo json_encode($project['id']); ?>;
        const PROJECT_DATA = <?php echo json_encode($project); ?>;
        const BASE_URL = <?php echo json_encode(getBaseUrl()); ?>;
        
        // Cargar datos Porter existentes si existen
        <?php
        $existingPorterData = null;
        if (!empty($porterAnalysis) || !empty($porterFoda)) {
            $existingPorterData = [
                'analysis' => $porterAnalysis,
                'foda' => $porterFoda,
                'score' => $porterScore
            ];
        }
        
        if ($existingPorterData) {
            echo 'const EXISTING_PORTER_DATA = ' . json_encode($existingPorterData) . ';';
        } else {
            echo 'const EXISTING_PORTER_DATA = null;';
        }
        ?>
    </script>
    <script src="<?php echo getBaseUrl(); ?>/Publics/js/porter-matrix.js"></script>
    
    <script>
        // Auto-ocultar alertas después de 5 segundos
        const alertMessage = document.getElementById('alertMessage');
        if (alertMessage) {
            setTimeout(() => {
                alertMessage.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>
