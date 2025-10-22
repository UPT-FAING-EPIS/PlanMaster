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
                            IDENTIFICACIÓN DE ESTRATEGIAS
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
            <!-- Contexto del análisis DAFO -->
            <div class="context-box">
                <h3>🎯 Análisis DAFO para Identificación de Estrategias</h3>
                <p>A partir del análisis FODA previo, ahora evaluaremos las relaciones entre factores internos y externos para identificar las estrategias más apropiadas para su organización.</p>
                <p><strong>Este análisis le permitirá determinar:</strong></p>
                <ul>
                    <li><strong>Estrategias Ofensivas (FO):</strong> Aprovechar fortalezas para capitalizar oportunidades</li>
                    <li><strong>Estrategias Defensivas (FA):</strong> Usar fortalezas para enfrentar amenazas</li>
                    <li><strong>Estrategias de Reorientación (DO):</strong> Superar debilidades aprovechando oportunidades</li>
                    <li><strong>Estrategias de Supervivencia (DA):</strong> Minimizar debilidades y evitar amenazas</li>
                </ul>
            </div>

            <!-- Matriz DAFO Visual -->
            <div class="dafo-matrix-container">
                <h2 class="section-title">📊 Matriz DAFO Estratégica</h2>
                
                <div class="matrix-2x2">
                    <div class="matrix-quadrant quadrant-fo">
                        <h3>🚀 Estrategias Ofensivas</h3>
                        <p class="quadrant-subtitle">(Fortalezas + Oportunidades)</p>
                        <div class="quadrant-content">
                            <p>Aprovechar las fortalezas internas para capitalizar las oportunidades externas</p>
                        </div>
                    </div>
                    
                    <div class="matrix-quadrant quadrant-fa">
                        <h3>🛡️ Estrategias Defensivas</h3>
                        <p class="quadrant-subtitle">(Fortalezas + Amenazas)</p>
                        <div class="quadrant-content">
                            <p>Utilizar las fortalezas para enfrentar y neutralizar las amenazas</p>
                        </div>
                    </div>
                    
                    <div class="matrix-quadrant quadrant-do">
                        <h3>🔄 Estrategias de Reorientación</h3>
                        <p class="quadrant-subtitle">(Debilidades + Oportunidades)</p>
                        <div class="quadrant-content">
                            <p>Superar las debilidades aprovechando las oportunidades del entorno</p>
                        </div>
                    </div>
                    
                    <div class="matrix-quadrant quadrant-da">
                        <h3>⚠️ Estrategias de Supervivencia</h3>
                        <p class="quadrant-subtitle">(Debilidades + Amenazas)</p>
                        <div class="quadrant-content">
                            <p>Minimizar debilidades y evitar amenazas para asegurar la supervivencia</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de evaluación DAFO -->
            <form action="<?php echo getBaseUrl(); ?>/Controllers/ProjectController.php?action=save_strategies" method="POST" class="dafo-evaluation-form">
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                
                <!-- Leyenda de escala -->
                <div class="scale-legend">
                    <h3>📏 Escala de Evaluación</h3>
                    <div class="scale-items">
                        <span class="scale-item"><strong>0</strong> = En total desacuerdo</span>
                        <span class="scale-item"><strong>1</strong> = No está de acuerdo</span>
                        <span class="scale-item"><strong>2</strong> = Está de acuerdo</span>
                        <span class="scale-item"><strong>3</strong> = Bastante de acuerdo</span>
                        <span class="scale-item"><strong>4</strong> = En total acuerdo</span>
                    </div>
                </div>

                <!-- Tablas de evaluación -->
                <div class="evaluation-tables">
                    <!-- Tabla FO (Fortalezas-Oportunidades) -->
                    <div class="evaluation-table-container">
                        <h3 class="table-title fo-title">🚀 Tabla FO (Fortalezas-Oportunidades)</h3>
                        <div class="table-wrapper">
                            <table class="evaluation-table fo-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>O1</th>
                                        <th>O2</th>
                                        <th>O3</th>
                                        <th>O4</th>
                                        <th class="total-column">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>F1</th>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fo[f1][o1]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fo[f1][o2]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fo[f1][o3]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fo[f1][o4]" value="0"></td>
                                        <td class="total-cell" id="fo-f1-total">0</td>
                                    </tr>
                                    <tr>
                                        <th>F2</th>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fo[f2][o1]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fo[f2][o2]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fo[f2][o3]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fo[f2][o4]" value="0"></td>
                                        <td class="total-cell" id="fo-f2-total">0</td>
                                    </tr>
                                    <tr>
                                        <th>F3</th>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fo[f3][o1]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fo[f3][o2]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fo[f3][o3]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fo[f3][o4]" value="0"></td>
                                        <td class="total-cell" id="fo-f3-total">0</td>
                                    </tr>
                                    <tr>
                                        <th>F4</th>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fo[f4][o1]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fo[f4][o2]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fo[f4][o3]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fo[f4][o4]" value="0"></td>
                                        <td class="total-cell" id="fo-f4-total">0</td>
                                    </tr>
                                    <tr class="total-row">
                                        <th>Total</th>
                                        <td class="total-cell" id="fo-o1-total">0</td>
                                        <td class="total-cell" id="fo-o2-total">0</td>
                                        <td class="total-cell" id="fo-o3-total">0</td>
                                        <td class="total-cell" id="fo-o4-total">0</td>
                                        <td class="grand-total" id="fo-grand-total">0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabla FA (Fortalezas-Amenazas) -->
                    <div class="evaluation-table-container">
                        <h3 class="table-title fa-title">🛡️ Tabla FA (Fortalezas-Amenazas)</h3>
                        <div class="table-wrapper">
                            <table class="evaluation-table fa-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>A1</th>
                                        <th>A2</th>
                                        <th>A3</th>
                                        <th>A4</th>
                                        <th class="total-column">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>F1</th>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fa[f1][a1]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fa[f1][a2]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fa[f1][a3]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fa[f1][a4]" value="0"></td>
                                        <td class="total-cell" id="fa-f1-total">0</td>
                                    </tr>
                                    <tr>
                                        <th>F2</th>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fa[f2][a1]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fa[f2][a2]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fa[f2][a3]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fa[f2][a4]" value="0"></td>
                                        <td class="total-cell" id="fa-f2-total">0</td>
                                    </tr>
                                    <tr>
                                        <th>F3</th>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fa[f3][a1]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fa[f3][a2]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fa[f3][a3]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fa[f3][a4]" value="0"></td>
                                        <td class="total-cell" id="fa-f3-total">0</td>
                                    </tr>
                                    <tr>
                                        <th>F4</th>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fa[f4][a1]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fa[f4][a2]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fa[f4][a3]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="fa[f4][a4]" value="0"></td>
                                        <td class="total-cell" id="fa-f4-total">0</td>
                                    </tr>
                                    <tr class="total-row">
                                        <th>Total</th>
                                        <td class="total-cell" id="fa-a1-total">0</td>
                                        <td class="total-cell" id="fa-a2-total">0</td>
                                        <td class="total-cell" id="fa-a3-total">0</td>
                                        <td class="total-cell" id="fa-a4-total">0</td>
                                        <td class="grand-total" id="fa-grand-total">0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabla DO (Debilidades-Oportunidades) -->
                    <div class="evaluation-table-container">
                        <h3 class="table-title do-title">🔄 Tabla DO (Debilidades-Oportunidades)</h3>
                        <div class="table-wrapper">
                            <table class="evaluation-table do-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>O1</th>
                                        <th>O2</th>
                                        <th>O3</th>
                                        <th>O4</th>
                                        <th class="total-column">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>D1</th>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="do[d1][o1]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="do[d1][o2]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="do[d1][o3]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="do[d1][o4]" value="0"></td>
                                        <td class="total-cell" id="do-d1-total">0</td>
                                    </tr>
                                    <tr>
                                        <th>D2</th>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="do[d2][o1]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="do[d2][o2]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="do[d2][o3]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="do[d2][o4]" value="0"></td>
                                        <td class="total-cell" id="do-d2-total">0</td>
                                    </tr>
                                    <tr>
                                        <th>D3</th>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="do[d3][o1]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="do[d3][o2]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="do[d3][o3]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="do[d3][o4]" value="0"></td>
                                        <td class="total-cell" id="do-d3-total">0</td>
                                    </tr>
                                    <tr>
                                        <th>D4</th>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="do[d4][o1]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="do[d4][o2]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="do[d4][o3]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="do[d4][o4]" value="0"></td>
                                        <td class="total-cell" id="do-d4-total">0</td>
                                    </tr>
                                    <tr class="total-row">
                                        <th>Total</th>
                                        <td class="total-cell" id="do-o1-total">0</td>
                                        <td class="total-cell" id="do-o2-total">0</td>
                                        <td class="total-cell" id="do-o3-total">0</td>
                                        <td class="total-cell" id="do-o4-total">0</td>
                                        <td class="grand-total" id="do-grand-total">0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabla DA (Debilidades-Amenazas) -->
                    <div class="evaluation-table-container">
                        <h3 class="table-title da-title">⚠️ Tabla DA (Debilidades-Amenazas)</h3>
                        <div class="table-wrapper">
                            <table class="evaluation-table da-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>A1</th>
                                        <th>A2</th>
                                        <th>A3</th>
                                        <th>A4</th>
                                        <th class="total-column">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>D1</th>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="da[d1][a1]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="da[d1][a2]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="da[d1][a3]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="da[d1][a4]" value="0"></td>
                                        <td class="total-cell" id="da-d1-total">0</td>
                                    </tr>
                                    <tr>
                                        <th>D2</th>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="da[d2][a1]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="da[d2][a2]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="da[d2][a3]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="da[d2][a4]" value="0"></td>
                                        <td class="total-cell" id="da-d2-total">0</td>
                                    </tr>
                                    <tr>
                                        <th>D3</th>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="da[d3][a1]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="da[d3][a2]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="da[d3][a3]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="da[d3][a4]" value="0"></td>
                                        <td class="total-cell" id="da-d3-total">0</td>
                                    </tr>
                                    <tr>
                                        <th>D4</th>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="da[d4][a1]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="da[d4][a2]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="da[d4][a3]" value="0"></td>
                                        <td><input type="number" min="0" max="4" class="evaluation-input" name="da[d4][a4]" value="0"></td>
                                        <td class="total-cell" id="da-d4-total">0</td>
                                    </tr>
                                    <tr class="total-row">
                                        <th>Total</th>
                                        <td class="total-cell" id="da-a1-total">0</td>
                                        <td class="total-cell" id="da-a2-total">0</td>
                                        <td class="total-cell" id="da-a3-total">0</td>
                                        <td class="total-cell" id="da-a4-total">0</td>
                                        <td class="grand-total" id="da-grand-total">0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sección de Síntesis de Resultados -->
                <div class="synthesis-section">
                    <h2 class="section-title">📈 Síntesis de Resultados</h2>
                    
                    <div class="synthesis-table-container">
                        <table class="synthesis-table">
                            <thead>
                                <tr>
                                    <th>Relaciones</th>
                                    <th>Puntuación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="relation-name">FO (Fortalezas-Oportunidades)</td>
                                    <td class="synthesis-score" id="synthesis-fo">0</td>
                                </tr>
                                <tr>
                                    <td class="relation-name">FA (Fortalezas-Amenazas)</td>
                                    <td class="synthesis-score" id="synthesis-fa">0</td>
                                </tr>
                                <tr>
                                    <td class="relation-name">DO (Debilidades-Oportunidades)</td>
                                    <td class="synthesis-score" id="synthesis-do">0</td>
                                </tr>
                                <tr>
                                    <td class="relation-name">DA (Debilidades-Amenazas)</td>
                                    <td class="synthesis-score" id="synthesis-da">0</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="synthesis-note">
                            <p><strong>Nota:</strong> La puntuación mayor le indica la estrategia que deberá llevar a cabo</p>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Tipología de Estrategias -->
                <div class="typology-section">
                    <h2 class="section-title">🎯 Tipología de Estrategias</h2>
                    
                    <div class="typology-table-container">
                        <table class="typology-table">
                            <tbody>
                                <tr class="strategy-offensive">
                                    <td class="strategy-type">
                                        <div class="strategy-icon">🚀</div>
                                        <strong>Estrategia Ofensiva</strong>
                                    </td>
                                    <td class="strategy-description">
                                        Deberá adoptar estrategias de crecimiento
                                    </td>
                                </tr>
                                <tr class="strategy-defensive">
                                    <td class="strategy-type">
                                        <div class="strategy-icon">�️</div>
                                        <strong>Estrategia Defensiva</strong>
                                    </td>
                                    <td class="strategy-description">
                                        La empresa está preparada para enfrentarse a las amenazas
                                    </td>
                                </tr>
                                <tr class="strategy-reorientation">
                                    <td class="strategy-type">
                                        <div class="strategy-icon">🔄</div>
                                        <strong>Estrategia de Reorientación</strong>
                                    </td>
                                    <td class="strategy-description">
                                        La empresa no puede aprovechar las oportunidades
                                    </td>
                                </tr>
                                <tr class="strategy-survival">
                                    <td class="strategy-type">
                                        <div class="strategy-icon">⚠️</div>
                                        <strong>Estrategia de Supervivencia</strong>
                                    </td>
                                    <td class="strategy-description">
                                        Se enfrenta a amenazas externas sin las fortalezas necesarias
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recomendación estratégica -->
                <div class="strategic-recommendation">
                    <h3>🎯 Recomendación Estratégica</h3>
                    <div class="recommendation-content" id="strategy-recommendation">
                        <p>Complete la evaluación para obtener su recomendación estratégica personalizada.</p>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="form-actions">
                    <button type="submit" class="btn-save-evaluation">
                        <span class="btn-icon">💾</span>
                        Guardar Evaluación
                    </button>
                    
                    <button type="button" class="btn-calculate" id="calculate-btn">
                        <span class="btn-icon">🧮</span>
                        Calcular Estrategias
                    </button>
                    
                    <button type="button" class="btn-reset" id="reset-btn">
                        <span class="btn-icon">🔄</span>
                        Limpiar Evaluación
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