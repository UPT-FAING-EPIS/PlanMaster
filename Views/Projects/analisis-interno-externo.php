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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis Interno y Externo - <?php echo htmlspecialchars($project['project_name']); ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_project.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_analisis.css">
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
                    <h1>📊 Análisis Interno y Externo</h1>
                    <p class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></p>
                    <p class="company-name"><?php echo htmlspecialchars($project['company_name']); ?></p>
                </div>
                
                <!-- Botón de continuar -->
                <div class="continue-button-header">
                    <a href="<?php echo getBaseUrl(); ?>/Views/Projects/value-chain.php?id=<?php echo $project_id; ?>" 
                       class="btn-continue-analysis">
                        ⛓️ Continuar a Cadena de Valor →
                    </a>
                </div>
            </div>
            
            <!-- Introducción -->
            <div class="intro-section">
                <div class="intro-content">
                    <h2>🎯 Determinación de la Estrategia</h2>
                    <p>Fijados los objetivos estratégicos se debe analizar las distintas estrategias para lograrlos. De esta forma, las estrategias son los caminos, vías, o enfoques para alcanzar los objetivos. <strong>Responden a la pregunta ¿cómo?</strong></p>
                    
                    <p>Para determinar la estrategia, podríamos basarnos en el conjunto de estrategias genéricas y específicas que diferentes profesionales proponen al respecto. Esta guía, lejos de rozar la teoría, propone llevar a cabo un <strong>análisis interno y externo</strong> de su empresa para obtener una matriz cruzada e identificar la estrategia más adecuada.</p>
                    
                    <p>Este análisis le permitirá detectar por un lado <span class="highlight-success">los factores de éxito</span> (fortalezas y oportunidades), y por otro lado, <span class="highlight-warning">las debilidades y amenazas</span> que una empresa debe gestionar.</p>
                </div>
            </div>

            <!-- Diagrama de análisis -->
            <div class="analysis-diagram">
                <h3>🔄 Marco de Análisis Estratégico</h3>
                <div class="diagram-container">
                    <div class="analysis-matrix">
                        <!-- Análisis Externo -->
                        <div class="external-analysis">
                            <h4 class="analysis-title external">📡 ANÁLISIS EXTERNO</h4>
                            <div class="external-factors">
                                <div class="factor-box opportunities">
                                    <h5>🌟 OPORTUNIDADES</h5>
                                </div>
                                <div class="factor-box threats">
                                    <h5>⚠️ AMENAZAS</h5>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Análisis Interno -->
                        <div class="internal-analysis">
                            <h4 class="analysis-title internal">🏢 ANÁLISIS INTERNO</h4>
                            <div class="internal-factors">
                                <div class="factor-box strengths">
                                    <h5>💪 FORTALEZAS</h5>
                                </div>
                                <div class="factor-box weaknesses">
                                    <h5>⚡ DEBILIDADES</h5>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Centro - Análisis de Recursos -->
                        <div class="resources-analysis">
                            <div class="resources-box">
                                <h5>🔍 ANÁLISIS DE RECURSOS Y CAPACIDADES DE LA EMPRESA</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Definiciones detalladas -->
            <div class="definitions-section">
                <div class="definitions-grid">
                    <!-- Oportunidades -->
                    <div class="definition-card opportunities-card">
                        <div class="card-header">
                            <h3>🌟 Oportunidades</h3>
                        </div>
                        <div class="card-content">
                            <p><strong>Definición:</strong> Aquellos aspectos que pueden presentar una posibilidad para mejorar la rentabilidad de la empresa, aumentar la cifra de negocio y fortalecer la ventaja competitiva.</p>
                            
                            <div class="examples-section">
                                <h4>📋 Ejemplos:</h4>
                                <ul>
                                    <li>Fuerte crecimiento del mercado</li>
                                    <li>Desarrollo de la externalización</li>
                                    <li>Nuevas tecnologías disponibles</li>
                                    <li>Seguridad de la distribución</li>
                                    <li>Atender a grupos adicionales de clientes</li>
                                    <li>Crecimiento rápido del mercado</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Amenazas -->
                    <div class="definition-card threats-card">
                        <div class="card-header">
                            <h3>⚠️ Amenazas</h3>
                        </div>
                        <div class="card-content">
                            <p><strong>Definición:</strong> Son fuerzas y presiones del mercado-entorno que pueden impedir y dificultar el crecimiento de la empresa, la ejecución de la estrategia, reducir su eficacia o incrementar los riesgos en relación con el entorno y sector de actividad.</p>
                            
                            <div class="examples-section">
                                <h4>📋 Ejemplos:</h4>
                                <ul>
                                    <li>Competencia intensa en el mercado</li>
                                    <li>Aparición de nuevos competidores</li>
                                    <li>Reglamentación desfavorable</li>
                                    <li>Monopolio en materias primas</li>
                                    <li>Cambio en las necesidades de los consumidores</li>
                                    <li>Creciente poder de negociación de clientes y/o proveedores</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Fortalezas -->
                    <div class="definition-card strengths-card">
                        <div class="card-header">
                            <h3>💪 Fortalezas</h3>
                        </div>
                        <div class="card-content">
                            <p><strong>Definición:</strong> Son capacidades, recursos, posiciones alcanzadas, ventajas competitivas que posee la empresa y que le ayudarán a aprovechar las oportunidades del mercado.</p>
                            
                            <div class="examples-section">
                                <h4>📋 Ejemplos:</h4>
                                <ul>
                                    <li>Buena implantación en el territorio</li>
                                    <li>Notoriedad de la marca</li>
                                    <li>Capacidad de innovación</li>
                                    <li>Recursos financieros adecuados</li>
                                    <li>Ventajas en costes</li>
                                    <li>Líder en el mercado</li>
                                    <li>Buena imagen ante los consumidores</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Debilidades -->
                    <div class="definition-card weaknesses-card">
                        <div class="card-header">
                            <h3>⚡ Debilidades</h3>
                        </div>
                        <div class="card-content">
                            <p><strong>Definición:</strong> Son todos aquellos aspectos que limitan o reducen la capacidad de desarrollo de la empresa. Constituyen dificultades para la organización y deben, por tanto, ser controladas y superadas.</p>
                            
                            <div class="examples-section">
                                <h4>📋 Ejemplos:</h4>
                                <ul>
                                    <li>Precios elevados</li>
                                    <li>Productos en el final de su ciclo de vida</li>
                                    <li>Deficiente control de los riesgos</li>
                                    <li>Recursos humanos poco cualificados</li>
                                    <li>Débil imagen en el mercado</li>
                                    <li>Red de distribución débil</li>
                                    <li>No hay dirección estratégica clara</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Próximos pasos -->
            <div class="next-steps-section">
                <div class="next-steps-content">
                    <h3>🚀 Próximos Pasos</h3>
                    <p>Para elaborar el análisis FODA de su empresa, le proponemos que utilice distintos instrumentos para el análisis tanto interno como externo.</p>
                    
                    <div class="steps-info">
                        <div class="step-item">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4>Análisis de la Cadena de Valor</h4>
                                <p>Identifique las actividades que generan valor en su empresa</p>
                            </div>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4>Matrices de Análisis Estratégico</h4>
                                <p>Utilice herramientas como BCG, Porter y PEST para un análisis profundo</p>
                            </div>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4>Formulación de Estrategias</h4>
                                <p>Desarrolle estrategias basadas en el análisis cruzado FODA</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navegación -->
            <div class="form-actions">
                <div class="actions-center">
                    <a href="<?php echo getBaseUrl(); ?>/Views/Projects/objectives.php?id=<?php echo $project_id; ?>" 
                       class="btn-back">
                        ← Regresar a Objetivos
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../Users/footer.php'; ?>
</body>
</html>