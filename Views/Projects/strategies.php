<?php
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/StrategicAnalysisController.php';
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
$strategicController = new StrategicAnalysisController();
$user = AuthController::getCurrentUser();

// Obtener datos FODA del proyecto
$fodaData = $strategicController->getFodaData($project_id);
$strategicAnalysis = $strategicController->getStrategicAnalysis($project_id);
$relations = $strategicController->getStrategicRelations($project_id);

// Definir variables globales para usar en las matrices
$fortalezas = isset($fodaData['fortalezas']) ? $fodaData['fortalezas'] : [];
$debilidades = isset($fodaData['debilidades']) ? $fodaData['debilidades'] : [];
$oportunidades = isset($fodaData['oportunidades']) ? $fodaData['oportunidades'] : [];
$amenazas = isset($fodaData['amenazas']) ? $fodaData['amenazas'] : [];

// Debug: Información sobre los datos obtenidos (comentado para producción)
// echo "DEBUG: " . count($fodaData['fortalezas']) . " fortalezas, " . count($fodaData['debilidades']) . " debilidades";

// Verificar si hay datos FODA
$hasFodaData = !empty($fodaData['fortalezas']) || !empty($fodaData['debilidades']) || 
               !empty($fodaData['oportunidades']) || !empty($fodaData['amenazas']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis Estratégico - PlanMaster</title>
    
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

    <div class="strategic-container" style="background-image: url('<?php echo getBaseUrl(); ?>/Resources/fondo11.jpg'); background-size: cover; background-position: center; background-attachment: fixed;">
        <div class="container">
            <!-- Header del análisis estratégico -->
            <div class="strategic-header">
                <div class="breadcrumb-nav" style="color: rgba(255, 255, 255, 0.8); margin-bottom: 1rem;">
                    <a href="<?php echo getBaseUrl(); ?>/Views/Users/dashboard.php" style="color: rgba(255, 255, 255, 0.8);">Dashboard</a>
                    <span> > </span>
                    <a href="project.php?id=<?php echo $project_id; ?>" style="color: rgba(255, 255, 255, 0.8);">Proyecto</a>
                    <span> > </span>
                    <span style="color: white;">Análisis Estratégico</span>
                </div>
                
                <h1 class="strategic-title">Análisis Estratégico FODA</h1>
                <p class="strategic-subtitle">
                    Identifique las estrategias más apropiadas relacionando fortalezas, debilidades, 
                    oportunidades y amenazas de su organización.
                </p>
            </div>

            <?php 
                // Debug temporal
                echo "<!-- DEBUG: ";
                echo "Fortalezas: " . count($fodaData['fortalezas']) . " ";
                echo "Debilidades: " . count($fodaData['debilidades']) . " ";
                echo "Oportunidades: " . count($fodaData['oportunidades']) . " ";
                echo "Amenazas: " . count($fodaData['amenazas']) . " ";
                echo "-->";
                ?>
                
                <?php if (!$hasFodaData): ?>
                <div class="alert alert-warning" style="background: rgba(255, 193, 7, 0.9); color: #856404; padding: 2rem; border-radius: 15px; text-align: center;">
                    <h4>⚠️ Datos FODA Requeridos</h4>
                    <p>Para realizar el análisis estratégico, primero debe completar el análisis FODA en las siguientes secciones:</p>
                    <div style="margin: 1rem 0;">
                        <a href="porter-matrix.php?id=<?php echo $project_id; ?>" class="btn btn-primary" style="margin: 0.5rem;">Porter Matrix (Fortalezas/Oportunidades)</a>
                        <a href="pest-analysis.php?id=<?php echo $project_id; ?>" class="btn btn-primary" style="margin: 0.5rem;">PEST Analysis (Amenazas)</a>
                        <a href="value-chain.php?id=<?php echo $project_id; ?>" class="btn btn-primary" style="margin: 0.5rem;">Cadena de Valor (Fortalezas/Debilidades)</a>
                    </div>
                </div>
            <?php else: ?>
                
                <!-- Leyenda de puntuación -->
                <div class="legend-section">
                    <h4 class="legend-title">Escala de Puntuación</h4>
                    <div class="legend-content">
                        <strong>0</strong> = En total desacuerdo | 
                        <strong>1</strong> = No está de acuerdo | 
                        <strong>2</strong> = Está de acuerdo | 
                        <strong>3</strong> = Bastante de acuerdo | 
                        <strong>4</strong> = En total acuerdo
                    </div>
                </div>

                <div class="strategic-content">
                    <!-- 1. Matriz FO (Fortalezas - Oportunidades) -->
                    <div class="matrix-section">
                        <h3 class="matrix-title">Fortalezas x Oportunidades (Estrategia Ofensiva)</h3>
                        <p class="matrix-description">Las fortalezas se usan para tomar ventaja en cada una de las oportunidades.</p>
                        
                        <?php if (empty($fodaData['fortalezas']) || empty($fodaData['oportunidades'])): ?>
                            <div class="alert alert-info" style="background: rgba(52, 152, 219, 0.1); color: #2980b9; padding: 1.5rem; border-radius: 10px; text-align: center;">
                                <p><strong>⚠️ Datos insuficientes</strong></p>
                                <p>Se requieren fortalezas y oportunidades para esta matriz.</p>
                            </div>
                        <?php else: ?>
                            <table class="strategic-matrix" id="fo-matrix">
                                <thead>
                                    <tr>
                                        <th>FORTALEZAS</th>
                                        <?php 
                                        $oportunidades = isset($fodaData['oportunidades']) ? $fodaData['oportunidades'] : [];
                                        foreach ($oportunidades as $index => $oportunidad): 
                                        ?>
                                            <th class="popup-trigger" 
                                                data-popup-title="<?php echo htmlspecialchars($oportunidad['item_text'] ?? 'Oportunidad'); ?>"
                                                data-popup-content="<?php echo htmlspecialchars($oportunidad['item_text'] ?? 'Sin descripción'); ?>">
                                                O<?php echo $index + 1; ?>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $fortalezas = isset($fodaData['fortalezas']) ? $fodaData['fortalezas'] : [];
                                    foreach ($fortalezas as $fIndex => $fortaleza): 
                                    ?>
                                        <tr>
                                            <td class="row-header">
                                                <?php echo htmlspecialchars($fortaleza['item_text'] ?? 'Sin descripción'); ?>
                                            </td>
                                            <?php foreach ($oportunidades as $oIndex => $oportunidad): ?>
                                                <td>
                                                    <input type="number" 
                                                           class="score-input" 
                                                           min="0" 
                                                           max="4" 
                                                           value="0"
                                                           data-relation-type="FO"
                                                           data-fortaleza-id="<?php echo $fortaleza['id'] ?? 0; ?>"
                                                           data-oportunidad-id="<?php echo $oportunidad['id'] ?? 0; ?>">
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="total-row">
                                        <td><strong>Total</strong></td>
                                        <?php foreach ($oportunidades as $oportunidad): ?>
                                            <td class="total-cell">0</td>
                                        <?php endforeach; ?>
                                    </tr>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>

                    <!-- 2. Matriz FA (Fortalezas - Amenazas) -->
                    <div class="matrix-section">
                        <h3 class="matrix-title">Fortalezas x Amenazas (Estrategia Defensiva)</h3>
                        <p class="matrix-description">Las fortalezas evaden el efecto negativo de las amenazas.</p>
                        
                        <?php if (empty($fodaData['fortalezas']) || empty($fodaData['amenazas'])): ?>
                            <div class="alert alert-info" style="background: rgba(52, 152, 219, 0.1); color: #2980b9; padding: 1.5rem; border-radius: 10px; text-align: center;">
                                <p><strong>⚠️ Datos insuficientes</strong></p>
                                <p>Se requieren fortalezas y amenazas para esta matriz.</p>
                            </div>
                        <?php else: ?>
                            <table class="strategic-matrix" id="fa-matrix">
                                <thead>
                                    <tr>
                                        <th>FORTALEZAS</th>
                                        <?php 
                                        $amenazas = isset($fodaData['amenazas']) ? $fodaData['amenazas'] : [];
                                        foreach ($amenazas as $index => $amenaza): 
                                        ?>
                                            <th class="popup-trigger" 
                                                data-popup-title="<?php echo htmlspecialchars($amenaza['item_text'] ?? 'Amenaza'); ?>"
                                                data-popup-content="<?php echo htmlspecialchars($amenaza['item_text'] ?? 'Sin descripción'); ?>">
                                                A<?php echo $index + 1; ?>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($fortalezas as $fIndex => $fortaleza): ?>
                                        <tr>
                                            <td class="row-header">
                                                <?php echo htmlspecialchars($fortaleza['item_text'] ?? 'Sin descripción'); ?>
                                            </td>
                                            <?php foreach ($amenazas as $aIndex => $amenaza): ?>
                                                <td>
                                                    <input type="number" 
                                                           class="score-input" 
                                                           min="0" 
                                                           max="4" 
                                                           value="0"
                                                           data-relation-type="FA"
                                                           data-fortaleza-id="<?php echo $fortaleza['id']; ?>"
                                                           data-amenaza-id="<?php echo $amenaza['id']; ?>">
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="total-row">
                                        <td><strong>Total</strong></td>
                                        <?php foreach ($amenazas as $amenaza): ?>
                                            <td class="total-cell">0</td>
                                        <?php endforeach; ?>
                                    </tr>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>

                    <!-- 3. Matriz DO (Debilidades - Oportunidades) -->
                    <div class="matrix-section">
                        <h3 class="matrix-title">Debilidades x Oportunidades (Estrategia Adaptativa)</h3>
                        <p class="matrix-description">Superamos las debilidades tomando ventaja de las oportunidades.</p>
                        
                        <?php if (empty($fodaData['debilidades']) || empty($fodaData['oportunidades'])): ?>
                            <div class="alert alert-info" style="background: rgba(52, 152, 219, 0.1); color: #2980b9; padding: 1.5rem; border-radius: 10px; text-align: center;">
                                <p><strong>⚠️ Datos insuficientes</strong></p>
                                <p>Se requieren debilidades y oportunidades para esta matriz.</p>
                            </div>
                        <?php else: ?>
                            <table class="strategic-matrix" id="do-matrix">
                                <thead>
                                    <tr>
                                        <th>DEBILIDADES</th>
                                        <?php foreach ($oportunidades as $index => $oportunidad): ?>
                                            <th class="popup-trigger" 
                                                data-popup-title="<?php echo htmlspecialchars($oportunidad['item_text'] ?? 'Oportunidad'); ?>"
                                                data-popup-content="<?php echo htmlspecialchars($oportunidad['item_text'] ?? 'Sin descripción'); ?>">
                                                O<?php echo $index + 1; ?>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $debilidades = isset($fodaData['debilidades']) ? $fodaData['debilidades'] : [];
                                    foreach ($debilidades as $dIndex => $debilidad): 
                                    ?>
                                        <tr>
                                            <td class="row-header">
                                                <?php echo htmlspecialchars($debilidad['item_text'] ?? 'Sin descripción'); ?>
                                            </td>
                                            <?php foreach ($oportunidades as $oIndex => $oportunidad): ?>
                                                <td>
                                                    <input type="number" 
                                                           class="score-input" 
                                                           min="0" 
                                                           max="4" 
                                                           value="0"
                                                           data-relation-type="DO"
                                                           data-debilidad-id="<?php echo $debilidad['id'] ?? 0; ?>"
                                                           data-oportunidad-id="<?php echo $oportunidad['id'] ?? 0; ?>">
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="total-row">
                                        <td><strong>Total</strong></td>
                                        <?php foreach ($oportunidades as $oportunidad): ?>
                                            <td class="total-cell">0</td>
                                        <?php endforeach; ?>
                                    </tr>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>

                    <!-- 4. Matriz DA (Debilidades - Amenazas) -->
                    <div class="matrix-section">
                        <h3 class="matrix-title">Debilidades x Amenazas (Estrategia de Supervivencia)</h3>
                        <p class="matrix-description">Las debilidades intensifican notablemente el efecto negativo de las amenazas.</p>
                        
                        <?php if (empty($fodaData['debilidades']) || empty($fodaData['amenazas'])): ?>
                            <div class="alert alert-info" style="background: rgba(52, 152, 219, 0.1); color: #2980b9; padding: 1.5rem; border-radius: 10px; text-align: center;">
                                <p><strong>⚠️ Datos insuficientes</strong></p>
                                <p>Se requieren debilidades y amenazas para esta matriz.</p>
                            </div>
                        <?php else: ?>
                            <table class="strategic-matrix" id="da-matrix">
                                <thead>
                                    <tr>
                                        <th>DEBILIDADES</th>
                                        <?php foreach ($amenazas as $index => $amenaza): ?>
                                            <th class="popup-trigger" 
                                                data-popup-title="<?php echo htmlspecialchars($amenaza['item_text'] ?? 'Amenaza'); ?>"
                                                data-popup-content="<?php echo htmlspecialchars($amenaza['item_text'] ?? 'Sin descripción'); ?>">
                                                A<?php echo $index + 1; ?>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($debilidades as $dIndex => $debilidad): ?>
                                        <tr>
                                            <td class="row-header">
                                                <?php echo htmlspecialchars($debilidad['item_text'] ?? 'Sin descripción'); ?>
                                            </td>
                                            <?php foreach ($amenazas as $aIndex => $amenaza): ?>
                                                <td>
                                                    <input type="number" 
                                                           class="score-input" 
                                                           min="0" 
                                                           max="4" 
                                                           value="0"
                                                           data-relation-type="DA"
                                                           data-debilidad-id="<?php echo $debilidad['id'] ?? 0; ?>"
                                                           data-amenaza-id="<?php echo $amenaza['id'] ?? 0; ?>">
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="total-row">
                                        <td><strong>Total</strong></td>
                                        <?php foreach ($amenazas as $amenaza): ?>
                                            <td class="total-cell">0</td>
                                        <?php endforeach; ?>
                                    </tr>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sección de Resultados -->
                <div class="results-section">
                    <h2 class="results-title">Síntesis de Resultados</h2>
                    
                    <div class="results-grid">
                        <div class="result-card" id="fo-result">
                            <div class="result-label">Estrategia Ofensiva (FO)</div>
                            <div class="result-value">0</div>
                            <div class="result-type">Fortalezas + Oportunidades</div>
                        </div>
                        
                        <div class="result-card" id="fa-result">
                            <div class="result-label">Estrategia Defensiva (FA)</div>
                            <div class="result-value">0</div>
                            <div class="result-type">Fortalezas + Amenazas</div>
                        </div>
                        
                        <div class="result-card" id="do-result">
                            <div class="result-label">Estrategia Adaptativa (DO)</div>
                            <div class="result-value">0</div>
                            <div class="result-type">Debilidades + Oportunidades</div>
                        </div>
                        
                        <div class="result-card" id="da-result">
                            <div class="result-label">Estrategia de Supervivencia (DA)</div>
                            <div class="result-value">0</div>
                            <div class="result-type">Debilidades + Amenazas</div>
                        </div>
                    </div>

                    <!-- Recomendación estratégica -->
                    <div class="strategy-recommendation">
                        <div class="strategy-type" id="strategy-type">Determine su estrategia</div>
                        <div class="strategy-description" id="strategy-description">Complete las evaluaciones para obtener la recomendación estratégica.</div>
                    </div>
                </div>

                <!-- Botón de guardado -->
                <button type="button" id="saveStrategicAnalysis" class="save-button">
                    Guardar Análisis Estratégico
                </button>

            <?php endif; ?>
        </div>
    </div>

    <!-- Popup para mostrar descripciones -->
    <div id="foda-popup" class="foda-popup" style="display: none;">
        <div class="popup-content">
            <div class="popup-header">
                <h4 id="popup-title"></h4>
                <button class="popup-close">&times;</button>
            </div>
            <div class="popup-body">
                <p id="popup-text"></p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?php echo getBaseUrl(); ?>/Publics/js/strategies.js"></script>
    
    <script>
    // Cargar datos guardados de las relaciones estratégicas
    const savedRelations = <?php echo json_encode($relations); ?>;
    
    // Función para cargar datos guardados en los inputs
    function loadSavedData() {
        console.log('Cargando datos guardados:', savedRelations);
        
        // Iterar por cada tipo de relación
        Object.keys(savedRelations).forEach(relationType => {
            savedRelations[relationType].forEach(relation => {
                // Encontrar el input correspondiente basado en los data attributes
                let input = null;
                
                if (relationType === 'FO') {
                    input = document.querySelector(`input[data-relation-type="FO"][data-fortaleza-id="${relation.fortaleza_id}"][data-oportunidad-id="${relation.oportunidad_id}"]`);
                } else if (relationType === 'FA') {
                    input = document.querySelector(`input[data-relation-type="FA"][data-fortaleza-id="${relation.fortaleza_id}"][data-amenaza-id="${relation.amenaza_id}"]`);
                } else if (relationType === 'DO') {
                    input = document.querySelector(`input[data-relation-type="DO"][data-debilidad-id="${relation.debilidad_id}"][data-oportunidad-id="${relation.oportunidad_id}"]`);
                } else if (relationType === 'DA') {
                    input = document.querySelector(`input[data-relation-type="DA"][data-debilidad-id="${relation.debilidad_id}"][data-amenaza-id="${relation.amenaza_id}"]`);
                }
                
                if (input && relation.value_score) {
                    input.value = relation.value_score;
                }
            });
        });
        
        // Calcular totales después de cargar los datos
        setTimeout(() => {
            calculateAllTotals();
        }, 100);
    }
    
    // Función para calcular totales de todas las matrices
    function calculateAllTotals() {
        calculateMatrixTotals('fo-matrix');
        calculateMatrixTotals('fa-matrix');
        calculateMatrixTotals('do-matrix');
        calculateMatrixTotals('da-matrix');
    }
    
    // Función para calcular totales de una matriz específica
    function calculateMatrixTotals(matrixId) {
        const matrix = document.getElementById(matrixId);
        if (!matrix) return;
        
        const totalCells = matrix.querySelectorAll('.total-cell');
        let matrixGrandTotal = 0;
        
        // Obtener todas las filas de datos (excluir total-row)
        const dataRows = matrix.querySelectorAll('tbody tr:not(.total-row)');
        const numCols = totalCells.length;
        
        // Calcular totales por columna
        for (let col = 0; col < numCols; col++) {
            let columnTotal = 0;
            
            // Sumar valores de cada fila en esta columna
            dataRows.forEach(row => {
                const input = row.querySelector(`td:nth-child(${col + 2}) input.score-input`);
                if (input) {
                    const value = parseInt(input.value) || 0;
                    columnTotal += value;
                }
            });
            
            // Actualizar celda total
            if (totalCells[col]) {
                totalCells[col].textContent = columnTotal;
                matrixGrandTotal += columnTotal;
            }
        }
        
        // Actualizar el total general de esta matriz en la síntesis
        updateSynthesisTotal(matrixId, matrixGrandTotal);
        
        return matrixGrandTotal;
    }
    
    // Función para actualizar totales en la síntesis de resultados
    function updateSynthesisTotal(matrixId, total) {
        let resultCardId = '';
        
        switch(matrixId) {
            case 'fo-matrix':
                resultCardId = 'fo-result';
                break;
            case 'fa-matrix':
                resultCardId = 'fa-result';
                break;
            case 'do-matrix':
                resultCardId = 'do-result';
                break;
            case 'da-matrix':
                resultCardId = 'da-result';
                break;
        }
        
        // Actualizar el valor en la tarjeta de resultados
        const resultValue = document.querySelector(`#${resultCardId} .result-value`);
        if (resultValue) {
            resultValue.textContent = total;
        }
        
        // Actualizar recomendación estratégica
        updateStrategyRecommendation();
    }
    
    // Función para determinar la estrategia recomendada
    function updateStrategyRecommendation() {
        const fo = parseInt(document.querySelector('#fo-result .result-value')?.textContent) || 0;
        const fa = parseInt(document.querySelector('#fa-result .result-value')?.textContent) || 0;
        const do_total = parseInt(document.querySelector('#do-result .result-value')?.textContent) || 0;
        const da = parseInt(document.querySelector('#da-result .result-value')?.textContent) || 0;
        
        const max = Math.max(fo, fa, do_total, da);
        let strategy = 'Determine su estrategia';
        let description = 'Complete las evaluaciones para obtener la recomendación estratégica.';
        
        if (max > 0) {
            if (max === fo) {
                strategy = 'Estrategia Ofensiva';
                description = 'Deberá adoptar estrategias de crecimiento. Las fortalezas de la organización pueden aprovecharse para capitalizar las oportunidades del entorno.';
            } else if (max === fa) {
                strategy = 'Estrategia Defensiva';
                description = 'Deberá adoptar estrategias defensivas. Use las fortalezas para evitar o reducir el impacto de las amenazas externas.';
            } else if (max === do_total) {
                strategy = 'Estrategia Adaptativa';
                description = 'Deberá adoptar estrategias de reorientación. Supere las debilidades internas aprovechando las oportunidades externas.';
            } else if (max === da) {
                strategy = 'Estrategia de Supervivencia';
                description = 'Deberá adoptar estrategias de supervivencia. Minimice las debilidades y evite las amenazas para mantener la competitividad.';
            }
        }
        
        const strategyType = document.getElementById('strategy-type');
        const strategyDescription = document.getElementById('strategy-description');
        
        if (strategyType) strategyType.textContent = strategy;
        if (strategyDescription) strategyDescription.textContent = description;
    }
    
    // Sistema de popups para FODA
    document.addEventListener('DOMContentLoaded', function() {
        const popup = document.getElementById('foda-popup');
        const popupTitle = document.getElementById('popup-title');
        const popupText = document.getElementById('popup-text');
        const closeBtn = document.querySelector('.popup-close');
        
        // Agregar event listeners a todos los elementos con popup
        document.querySelectorAll('.popup-trigger').forEach(element => {
            element.addEventListener('click', function() {
                const title = this.getAttribute('data-popup-title');
                const content = this.getAttribute('data-popup-content');
                
                popupTitle.textContent = title;
                popupText.textContent = content;
                popup.style.display = 'flex';
            });
        });
        
        // Cerrar popup
        closeBtn.addEventListener('click', function() {
            popup.style.display = 'none';
        });
        
        // Cerrar popup al hacer clic fuera
        popup.addEventListener('click', function(e) {
            if (e.target === popup) {
                popup.style.display = 'none';
            }
        });
        
        // Cerrar popup con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && popup.style.display === 'flex') {
                popup.style.display = 'none';
            }
        });
        
        // Agregar event listeners a todos los inputs para actualizar totales y guardar
        document.querySelectorAll('.score-input').forEach(input => {
            input.addEventListener('input', function() {
                // Encontrar la matriz padre y recalcular totales
                const matrix = this.closest('.strategic-matrix');
                if (matrix) {
                    calculateMatrixTotals(matrix.id);
                }
                
                // Guardar automáticamente el valor
                saveRelation(this);
            });
        });
        
        // Función para guardar relación estratégica
        function saveRelation(input) {
            const relationType = input.dataset.relationType;
            const value = parseInt(input.value) || 0;
            
            // Preparar datos según el tipo de relación
            let data = {
                project_id: <?php echo $project_id; ?>,
                relation_type: relationType,
                value_score: value
            };
            
            // Agregar IDs específicos según el tipo
            if (relationType === 'FO') {
                data.fortaleza_id = input.dataset.fortalezaId;
                data.oportunidad_id = input.dataset.oportunidadId;
            } else if (relationType === 'FA') {
                data.fortaleza_id = input.dataset.fortalezaId;
                data.amenaza_id = input.dataset.amenazaId;
            } else if (relationType === 'DO') {
                data.debilidad_id = input.dataset.debilidadId;
                data.oportunidad_id = input.dataset.oportunidadId;
            } else if (relationType === 'DA') {
                data.debilidad_id = input.dataset.debilidadId;
                data.amenaza_id = input.dataset.amenazaId;
            }
            
            // Enviar datos al servidor usando FormData
            const formData = new FormData();
            formData.append('action', 'save_relation');
            formData.append('project_id', data.project_id);
            formData.append('relation_type', data.relation_type);
            formData.append('value_score', data.value_score);
            
            // Agregar IDs específicos
            if (data.fortaleza_id) formData.append('fortaleza_id', data.fortaleza_id);
            if (data.debilidad_id) formData.append('debilidad_id', data.debilidad_id);
            if (data.oportunidad_id) formData.append('oportunidad_id', data.oportunidad_id);
            if (data.amenaza_id) formData.append('amenaza_id', data.amenaza_id);
            
            fetch('../../Controllers/StrategicAnalysisController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Relación guardada exitosamente:', data);
                    if (data.debug) {
                        console.log('Debug info:', data.debug);
                    }
                } else {
                    console.error('Error al guardar relación:', data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        // Cargar datos guardados al inicializar
        loadSavedData();
    });
    </script>

    <style>
    /* Estilos para el popup */
    .foda-popup {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
    }
    
    .popup-content {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        max-width: 500px;
        width: 90%;
        max-height: 70vh;
        overflow: hidden;
    }
    
    .popup-header {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .popup-header h4 {
        margin: 0;
        font-weight: 600;
    }
    
    .popup-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s ease;
    }
    
    .popup-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    
    .popup-body {
        padding: 1.5rem;
        overflow-y: auto;
        max-height: 50vh;
    }
    
    .popup-body p {
        margin: 0;
        line-height: 1.6;
        color: #333;
    }
    
    /* Estilos para elementos clickeables */
    .popup-trigger {
        cursor: pointer;
        transition: all 0.3s ease;
        user-select: none;
    }
    
    .popup-trigger:hover {
        background: rgba(102, 126, 234, 0.1) !important;
        transform: scale(1.05);
    }
    
    .strategic-matrix .popup-trigger {
        font-weight: 600;
        text-align: center;
    }
    </style>
</body>
</html>