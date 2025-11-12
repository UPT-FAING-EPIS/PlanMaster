<?php
// Incluir configuraciones necesarias
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../Controllers/BCGTestController.php';
require_once __DIR__ . '/../../config/url_config.php';

// Verificar que el usuario esté logueado
if (!AuthController::isLoggedIn()) {
    header("Location: " . getBaseUrl() . "/Views/Auth/login.php");
    exit();
}

// Obtener ID del proyecto
$project_id = $_GET['id'] ?? null;
if (!$project_id) {
    header("Location: " . getBaseUrl() . "/Views/Users/projects.php");
    exit();
}

// Obtener datos del usuario y proyecto
$user = AuthController::getCurrentUser();
$projectController = new ProjectController();
$project = $projectController->getProject($project_id);

if (!$project) {
    $_SESSION['error'] = "Proyecto no encontrado";
    header("Location: " . getBaseUrl() . "/Views/Users/projects.php");
    exit();
}

// Obtener datos FODA existentes para Fortalezas y Debilidades
$fodaData = $projectController->getFodaAnalysis($project_id);
$fortalezas = isset($fodaData['fortalezas']) ? $fodaData['fortalezas'] : [];
$debilidades = isset($fodaData['debilidades']) ? $fodaData['debilidades'] : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 ANÁLISIS BCG - MATRIZ INTERACTIVA</title>
    
    <!-- CSS del sistema -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_bcg_test.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="main-content">
        <div class="container">
        
        <!-- Header del BCG Analysis -->
        <div class="bcg-header">
            <div class="bcg-info">
                <h1>📊 ANÁLISIS BCG - MATRIZ INTERACTIVA</h1>
                <p class="subtitle"><?php echo htmlspecialchars($project['project_name']); ?></p>
                <p class="subtitle" style="color: #475569; font-size: 1rem;"><?php echo htmlspecialchars($project['company_name']); ?></p>
            </div>
            
            <!-- Botón de continuar si está completo -->
            <?php if ($projectController->isBCGComplete($project_id)): ?>
            <div class="continue-button-bcg">
                <a href="<?php echo getBaseUrl(); ?>/Views/Projects/porter-matrix.php?id=<?php echo $project_id; ?>" 
                   class="btn-continue-porter">
                    🏛️ Continuar a Matriz de Porter
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- TABLA 1: PREVISIÓN DE VENTAS -->
        <div class="section">
            <h2>📈 1. PREVISIÓN DE VENTAS</h2>
            <p style="color: #6b7280; margin-bottom: 15px;">
                Ingrese los productos/servicios y sus previsiones de ventas. Los porcentajes se calcularán automáticamente.
            </p>
            <div id="sales-table-container">
                <!-- Tabla generada dinámicamente por JavaScript -->
            </div>
        </div>

        <!-- TABLA 2: TASAS DE CRECIMIENTO DEL MERCADO (TCM) -->
        <div class="section">
            <h2>📊 2. TASAS DE CRECIMIENTO DEL MERCADO (TCM)</h2>
            <p style="color: #6b7280; margin-bottom: 15px;">
                Defina los períodos y las tasas de crecimiento para cada producto. El TCM promedio se calcula automáticamente.
            </p>
            <div id="market-growth-table-container">
                <!-- Tabla generada dinámicamente por JavaScript -->
            </div>
        </div>

        <!-- TABLA 3: NIVELES DE VENTA DE COMPETIDORES (PRM) -->
        <div class="section">
            <h2>🏢 3. NIVELES DE VENTA DE COMPETIDORES</h2>
            <p style="color: #6b7280; margin-bottom: 15px;">
                Agregue competidores para cada producto y marque cuál tiene las mayores ventas. El PRM se calculará automáticamente.
            </p>
            <div id="competitors-table-container">
                <!-- Tablas generadas dinámicamente por JavaScript -->
            </div>
        </div>

        <!-- TABLA 4: EVOLUCIÓN DE LA DEMANDA GLOBAL DEL SECTOR -->
        <div class="section">
            <h2>🌍 4. EVOLUCIÓN DE LA DEMANDA GLOBAL DEL SECTOR</h2>
            <p style="color: #6b7280; margin-bottom: 15px;">
                Ingrese la demanda global del sector por año. Las tasas de crecimiento se calculan automáticamente.
            </p>
            <div id="sector-demand-table-container">
                <!-- Tabla generada dinámicamente por JavaScript -->
            </div>
        </div>

        <!-- SECCIÓN FORTALEZAS Y DEBILIDADES -->
        <div class="section">
            <h2>💪 5. FORTALEZAS Y DEBILIDADES</h2>
            <div style="background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #4299e1;">
                <h3 style="color: #2d3748; margin-bottom: 10px;">📝 Reflexión Estratégica</h3>
                <p style="color: #4a5568; line-height: 1.6;">
                    <strong>Realice una reflexión general sobre sus productos y servicios e identifique las fortalezas y amenazas más significativas de su empresa.</strong> 
                    La información aportada servirá para completar la matriz FODA y desarrollar estrategias competitivas basadas en el análisis BCG realizado.
                </p>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
                <!-- FORTALEZAS -->
                <div>
                    <h3 style="color: #059669; margin-bottom: 15px;">💚 FORTALEZAS</h3>
                    <div id="strengths-container">
                        <!-- Fortalezas generadas dinámicamente -->
                    </div>
                    <div style="margin-top: 15px; text-align: center;">
                        <button class="excel-btn success" onclick="addStrength()">
                            ➕ Agregar Fortaleza
                        </button>
                    </div>
                </div>
                
                <!-- DEBILIDADES -->
                <div>
                    <h3 style="color: #dc2626; margin-bottom: 15px;">💔 DEBILIDADES</h3>
                    <div id="weaknesses-container">
                        <!-- Debilidades generadas dinámicamente -->
                    </div>
                    <div style="margin-top: 15px; text-align: center;">
                        <button class="excel-btn danger" onclick="addWeakness()">
                            ➕ Agregar Debilidad
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MATRIZ BCG VISUAL -->
        <div class="section">
            <h2>🎯 6. MATRIZ BCG - VISUALIZACIÓN</h2>
            <p style="color: #6b7280; margin-bottom: 15px;">
                La matriz BCG se actualiza automáticamente basándose en los datos ingresados. 
                <strong>TCM ≥ 10%</strong> = Alto crecimiento | <strong>PRM ≥ 1.0</strong> = Alta participación relativa
            </p>
            <div id="bcg-matrix-container">
                <!-- Matriz generada dinámicamente por JavaScript -->
            </div>
        </div>
        
        <!-- SECCIÓN DE GUARDADO -->
        <div class="section" style="text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h2 style="color: white; border-bottom: 3px solid white;">💾 GUARDAR ANÁLISIS BCG</h2>
            <p style="margin-bottom: 20px; opacity: 0.9;">
                Una vez completado el análisis, guarde todos los datos para poder consultarlos posteriormente.
            </p>
            <div class="save-actions" style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
                <button class="excel-btn success" onclick="saveBCGData()" style="padding: 15px 30px; font-size: 16px; font-weight: bold;">
                    💾 GUARDAR ANÁLISIS
                </button>
                <button class="excel-btn" onclick="loadExampleData()" style="padding: 15px 30px; font-size: 16px; background: rgba(255,255,255,0.2);">
                    📄 Cargar Ejemplo
                </button>
            </div>
        </div>
        
        <!-- Mensaje de completitud -->
        <?php if ($projectController->isBCGComplete($project_id)): ?>
        <div class="section" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #22c55e; border-radius: 10px;">
            <h3 style="color: #16a34a; text-align: center;">✅ Análisis BCG Completado</h3>
            <p style="text-align: center; margin-bottom: 0; color: #15803d;">
                Has completado exitosamente el análisis BCG. Todos los datos han sido guardados correctamente.
            </p>
        </div>
        <?php else: ?>
        <div class="section" style="background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; border-radius: 10px;">
            <p style="text-align: center; margin: 0;">
                ⚠️ <strong>Complete todas las tablas y guarde el análisis</strong> para poder continuar con la Matriz de Porter.
            </p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Scripts del sistema -->
    <script>
        // Datos del proyecto para JavaScript
        const PROJECT_ID = <?php echo json_encode($project['id']); ?>;
        const PROJECT_DATA = <?php echo json_encode($project); ?>;
        
        // Cargar datos BCG existentes si existen
        <?php
        // Obtener datos BCG existentes
        $bcgController = new BCGTestController();
        $existingData = null;
        try {
            // Verificar si hay datos BCG para este proyecto
            require_once __DIR__ . '/../../config/database.php';
            $database = new Database();
            $conn = $database->getConnection();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM project_bcg_products WHERE project_id = ?");
            $stmt->bind_param('i', $project['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];
            
            if ($count > 0) {
                // Hay datos existentes, cargarlos
                $existingData = [
                    'products' => [],
                    'competitors' => [],
                    'market_growth' => [],
                    'sector_demand' => [],
                    'strengths' => [],
                    'weaknesses' => []
                ];
                
                // Cargar productos
                $stmt = $conn->prepare("SELECT * FROM project_bcg_products WHERE project_id = ? ORDER BY product_order");
                $stmt->bind_param('i', $project['id']);
                $stmt->execute();
                $products = $stmt->get_result();
                while ($row = $products->fetch_assoc()) {
                    $existingData['products'][] = [
                        'name' => $row['product_name'],
                        'sales' => (float)$row['sales_forecast'],
                        'percentage' => (float)$row['sales_percentage']
                    ];
                }
                
                // Cargar competidores
                $stmt = $conn->prepare("
                    SELECT c.*, p.product_name 
                    FROM project_bcg_competitors c 
                    JOIN project_bcg_products p ON c.product_id = p.id 
                    WHERE c.project_id = ? 
                    ORDER BY p.product_order, c.competitor_order
                ");
                $stmt->bind_param('i', $project['id']);
                $stmt->execute();
                $competitors = $stmt->get_result();
                while ($row = $competitors->fetch_assoc()) {
                    $productName = $row['product_name'];
                    if (!isset($existingData['competitors'][$productName])) {
                        $existingData['competitors'][$productName] = [];
                    }
                    $existingData['competitors'][$productName][] = [
                        'name' => $row['competitor_name'],
                        'sales' => (float)$row['competitor_sales'],
                        'isMax' => (bool)$row['is_max_sales']
                    ];
                }
                
                // Cargar períodos de mercado TCM CORRECTAMENTE
                $periodsMap = [];
                $stmt = $conn->prepare("
                    SELECT mg.period_name, mg.period_order, mg.tcm_percentage, p.product_order 
                    FROM project_bcg_market_growth mg
                    JOIN project_bcg_products p ON mg.product_id = p.id
                    WHERE mg.project_id = ? 
                    ORDER BY mg.period_order, p.product_order
                ");
                $stmt->bind_param('i', $project['id']);
                $stmt->execute();
                $tcmData = $stmt->get_result();
                
                while ($row = $tcmData->fetch_assoc()) {
                    $periodName = $row['period_name'];
                    $productIndex = $row['product_order'] - 1; // Convertir a índice base 0
                    $tcmValue = (float)$row['tcm_percentage'];
                    
                    // Inicializar período si no existe
                    if (!isset($periodsMap[$periodName])) {
                        $periodsMap[$periodName] = [
                            'period' => $periodName,
                            'rates' => []
                        ];
                    }
                    
                    // Asignar TCM al producto correspondiente
                    $periodsMap[$periodName]['rates'][$productIndex] = $tcmValue;
                }
                
                // Convertir map a array ordenado
                foreach ($periodsMap as $period) {
                    // Asegurar que todos los productos tengan valor (rellenar con 0 si falta)
                    $maxProducts = count($existingData['products']);
                    for ($i = 0; $i < $maxProducts; $i++) {
                        if (!isset($period['rates'][$i])) {
                            $period['rates'][$i] = 0;
                        }
                    }
                    $existingData['market_growth'][] = $period;
                }
                
                // Cargar demanda sectorial CORREGIDO - CON SOPORTE PARA MÚLTIPLES AÑOS
                $stmt = $conn->prepare("
                    SELECT sd.demand_year, sd.total_sector_demand, p.product_order
                    FROM project_bcg_sector_demand sd
                    JOIN project_bcg_products p ON sd.product_id = p.id
                    WHERE sd.project_id = ? 
                    ORDER BY sd.demand_year, p.product_order
                ");
                $stmt->bind_param('i', $project['id']);
                $stmt->execute();
                $demands = $stmt->get_result();
                
                // Organizar datos por año y producto
                $demandsByYear = [];
                while ($row = $demands->fetch_assoc()) {
                    $year = (int)$row['demand_year'];
                    $productIndex = $row['product_order'] - 1; // Convertir a índice base 0
                    $demand = (float)$row['total_sector_demand'];
                    
                    if (!isset($demandsByYear[$year])) {
                        $demandsByYear[$year] = [];
                    }
                    $demandsByYear[$year][$productIndex] = $demand;
                }
                
                // Convertir a estructura esperada por JavaScript
                foreach ($demandsByYear as $year => $productDemands) {
                    // Asegurar que todos los productos tengan valor (rellenar con 0 si falta)
                    $maxProducts = count($existingData['products']);
                    $completeDemands = [];
                    for ($i = 0; $i < $maxProducts; $i++) {
                        $completeDemands[$i] = isset($productDemands[$i]) ? $productDemands[$i] : 0;
                    }
                    
                    $existingData['sector_demand'][] = [
                        'year' => $year,
                        'productDemands' => array_values($completeDemands) // Reindexar array
                    ];
                }
                
                // Inicializar arrays de fortalezas y debilidades
                if (!isset($existingData['strengths'])) {
                    $existingData['strengths'] = [];
                }
                if (!isset($existingData['weaknesses'])) {
                    $existingData['weaknesses'] = [];
                }
                
                // Cargar fortalezas y debilidades
                $stmt = $conn->prepare("SELECT * FROM project_foda_analysis WHERE project_id = ? AND type IN ('fortaleza', 'debilidad') ORDER BY type, item_order");
                $stmt->bind_param('i', $project['id']);
                $stmt->execute();
                $fodas = $stmt->get_result();
                while ($row = $fodas->fetch_assoc()) {
                    if ($row['type'] === 'fortaleza') {
                        $existingData['strengths'][] = [
                            'id' => (int)$row['id'],
                            'text' => $row['item_text']
                        ];
                    } else {
                        $existingData['weaknesses'][] = [
                            'id' => (int)$row['id'],
                            'text' => $row['item_text']
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            // Error al cargar datos, continuar sin datos existentes
        }
        
        // Si no hay datos BCG pero sí hay datos FODA, cargar solo los FODA
        if (!$existingData) {
            try {
                $existingData = [
                    'strengths' => [],
                    'weaknesses' => []
                ];
                
                // Cargar solo fortalezas y debilidades si existen
                $stmt = $conn->prepare("SELECT * FROM project_foda_analysis WHERE project_id = ? AND type IN ('fortaleza', 'debilidad') ORDER BY type, item_order");
                $stmt->bind_param('i', $project['id']);
                $stmt->execute();
                $fodas = $stmt->get_result();
                while ($row = $fodas->fetch_assoc()) {
                    if ($row['type'] === 'fortaleza') {
                        $existingData['strengths'][] = [
                            'id' => (int)$row['id'],
                            'text' => $row['item_text']
                        ];
                    } else {
                        $existingData['weaknesses'][] = [
                            'id' => (int)$row['id'],
                            'text' => $row['item_text']
                        ];
                    }
                }
            } catch (Exception $e) {
                // Error al cargar FODA
            }
        }
        
        if ($existingData && (count($existingData) > 0 || count($existingData['strengths'] ?? []) > 0 || count($existingData['weaknesses'] ?? []) > 0)) {
            echo 'const EXISTING_BCG_DATA = ' . json_encode($existingData) . ';';
        } else {
            echo 'const EXISTING_BCG_DATA = null;';
        }
        ?>
    </script>
    <script src="<?php echo getBaseUrl(); ?>/Publics/js/dashboard.js"></script>
    <script src="<?php echo getBaseUrl(); ?>/Publics/js/bcg-test.js"></script>
    
        </div> <!-- Cerrar container -->
    </main> <!-- Cerrar main-content -->
    
    <?php include __DIR__ . '/../Users/footer.php'; ?>
    
</body>
</html>