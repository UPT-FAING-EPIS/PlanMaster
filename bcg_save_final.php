<?php
// BCG Save Final - Sistema completo y funcional
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'config/database.php';

// Función para calcular TCM y PRM correctamente
function calculateAndUpdateMetrics($conn, $projectId) {
    try {
        // 1. CALCULAR TCM (Tasa de Crecimiento del Mercado)
        // Obtener todos los productos del proyecto
        $productsQuery = $conn->prepare("SELECT id, product_name FROM project_bcg_products WHERE project_id = ?");
        $productsQuery->bind_param('i', $projectId);
        $productsQuery->execute();
        $products = $productsQuery->get_result();
        
        while ($product = $products->fetch_assoc()) {
            $productId = $product['id'];
            
            // Obtener períodos de crecimiento para este producto
            $periodsQuery = $conn->prepare("
                SELECT tcm_percentage, period_order 
                FROM project_bcg_market_growth 
                WHERE project_id = ? AND product_id = ? 
                ORDER BY period_order
            ");
            $periodsQuery->bind_param('ii', $projectId, $productId);
            $periodsQuery->execute();
            $periods = $periodsQuery->get_result();
            
            $tcmValues = [];
            while ($period = $periods->fetch_assoc()) {
                $tcmValues[] = (float)$period['tcm_percentage'];
            }
            
            // Calcular TCM promedio
            $avgTcm = count($tcmValues) > 0 ? array_sum($tcmValues) / count($tcmValues) : 0;
            
            // 2. CALCULAR PRM (Participación Relativa en el Mercado)
            // Obtener ventas de la empresa para este producto
            $salesQuery = $conn->prepare("SELECT sales_forecast FROM project_bcg_products WHERE id = ?");
            $salesQuery->bind_param('i', $productId);
            $salesQuery->execute();
            $salesResult = $salesQuery->get_result();
            $companySales = 0;
            if ($salesData = $salesResult->fetch_assoc()) {
                $companySales = (float)$salesData['sales_forecast'];
            }
            
            // Obtener ventas del competidor más fuerte
            $maxCompetitorQuery = $conn->prepare("
                SELECT MAX(competitor_sales) as max_sales 
                FROM project_bcg_competitors 
                WHERE project_id = ? AND product_id = ?
            ");
            $maxCompetitorQuery->bind_param('ii', $projectId, $productId);
            $maxCompetitorQuery->execute();
            $maxCompResult = $maxCompetitorQuery->get_result();
            $maxCompetitorSales = 0;
            if ($maxCompData = $maxCompResult->fetch_assoc()) {
                $maxCompetitorSales = (float)$maxCompData['max_sales'];
            }
            
            // Calcular PRM = Ventas empresa / Ventas competidor más fuerte
            $prm = ($maxCompetitorSales > 0) ? ($companySales / $maxCompetitorSales) : 0;
            
            // Determinar cuadrante BCG
            $quadrant = null;
            $tcmThreshold = 10; // 10% como umbral de crecimiento
            $prmThreshold = 1.0; // PRM = 1 como umbral de participación
            
            if ($avgTcm >= $tcmThreshold && $prm >= $prmThreshold) {
                $quadrant = 'estrella';
            } elseif ($avgTcm >= $tcmThreshold && $prm < $prmThreshold) {
                $quadrant = 'interrogante';
            } elseif ($avgTcm < $tcmThreshold && $prm >= $prmThreshold) {
                $quadrant = 'vaca_lechera';
            } else {
                $quadrant = 'perro';
            }
            
            // 3. ACTUALIZAR PRODUCTO CON MÉTRICAS CALCULADAS
            $updateQuery = $conn->prepare("
                UPDATE project_bcg_products 
                SET tcm_calculated = ?, prm_calculated = ?, bcg_quadrant = ?
                WHERE id = ?
            ");
            $updateQuery->bind_param('ddsi', $avgTcm, $prm, $quadrant, $productId);
            $updateQuery->execute();
            
            // 4. GUARDAR EN TABLA DE RESULTADOS MATRIZ
            $matrixQuery = $conn->prepare("
                INSERT INTO project_bcg_matrix_results 
                (project_id, product_id, prm_relative_position, tcm_market_growth, bcg_quadrant, matrix_position_x, matrix_position_y, bubble_size)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                prm_relative_position = VALUES(prm_relative_position),
                tcm_market_growth = VALUES(tcm_market_growth),
                bcg_quadrant = VALUES(bcg_quadrant),
                matrix_position_x = VALUES(matrix_position_x),
                matrix_position_y = VALUES(matrix_position_y),
                bubble_size = VALUES(bubble_size)
            ");
            
            // Posición en matriz (normalizada entre 0-100)
            $posX = min(max($prm * 50, 0), 100);  // PRM en eje X
            $posY = min(max($avgTcm * 5, 0), 100); // TCM en eje Y (multiplicado por 5 para escalar)
            $bubbleSize = max(($companySales / 10000), 0.5); // Tamaño proporcional a ventas
            
            $matrixQuery->bind_param('iiddsddd', $projectId, $productId, $prm, $avgTcm, $quadrant, $posX, $posY, $bubbleSize);
            $matrixQuery->execute();
        }
        
        // 5. ACTUALIZAR ANÁLISIS GENERAL CON PROMEDIOS
        $avgMetricsQuery = $conn->query("
            SELECT 
                AVG(tcm_calculated) as avg_tcm,
                AVG(prm_calculated) as avg_prm,
                SUM(sales_forecast) as total_sales
            FROM project_bcg_products 
            WHERE project_id = $projectId AND is_active = 1
        ");
        
        if ($avgMetricsQuery && $avgData = $avgMetricsQuery->fetch_assoc()) {
            $updateAnalysisQuery = $conn->prepare("
                UPDATE project_bcg_analysis 
                SET total_sales_forecast = ?, average_tcm = ?, average_prm = ?
                WHERE project_id = ?
            ");
            $totalSales = (float)$avgData['total_sales'];
            $avgTcm = (float)$avgData['avg_tcm'];
            $avgPrm = (float)$avgData['avg_prm'];
            
            $updateAnalysisQuery->bind_param('dddi', $totalSales, $avgTcm, $avgPrm, $projectId);
            $updateAnalysisQuery->execute();
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Error calculando métricas BCG: " . $e->getMessage());
        return false;
    }
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'GET') {
        echo json_encode([
            'success' => true,
            'message' => 'BCG Save Final - Endpoint funcional',
            'method' => 'GET',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    if ($method !== 'POST') {
        throw new Exception("Método no soportado: $method");
    }
    
    // Leer datos JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // DEBUG: Guardar datos recibidos en archivo de log
    error_log("BCG DEBUG - Datos recibidos: " . $input);
    error_log("BCG DEBUG - JSON decodificado: " . print_r($data, true));
    
    if (!$data) {
        throw new Exception('Datos JSON inválidos o vacíos');
    }
    
    // Conexión a la base de datos
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('Error de conexión a la base de datos');
    }
    
    // Obtener project_id del request
    $projectId = null;
    
    // Verificar si viene project_id en los datos
    if (isset($data['project_id']) && !empty($data['project_id'])) {
        $projectId = (int)$data['project_id'];
    } else {
        throw new Exception('project_id es requerido en los datos');
    }
    
    // Verificar que el proyecto existe
    $stmt = $conn->prepare("SELECT id FROM strategic_projects WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $projectId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Proyecto no encontrado con ID: ' . $projectId);
        }
        $stmt->close();
    } else {
        throw new Exception('Error verificando proyecto existente');
    }
    
    // Iniciar transacción
    $conn->autocommit(false);
    
    $savedData = [
        'project_id' => $projectId,
        'products' => 0,
        'tcm_periods' => 0,
        'competitors' => 0,
        'sector_demand' => 0,
        'strengths' => 0,
        'weaknesses' => 0
    ];
    
    // 1. LIMPIAR DATOS EXISTENTES
    $cleanupQueries = [
        "DELETE FROM project_bcg_products WHERE project_id = ?",
        "DELETE FROM project_bcg_competitors WHERE project_id = ?",
        "DELETE FROM project_bcg_market_growth WHERE project_id = ?",
        "DELETE FROM project_bcg_sector_demand WHERE project_id = ?",
        "DELETE FROM project_foda_analysis WHERE project_id = ? AND type IN ('fortaleza', 'debilidad')"
    ];
    
    foreach ($cleanupQueries as $query) {
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $projectId);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    // 2. GUARDAR PRODUCTOS
    if (isset($data['products']) && is_array($data['products'])) {
        $stmt = $conn->prepare("
            INSERT INTO project_bcg_products 
            (project_id, product_name, sales_forecast, sales_percentage, product_order, is_active) 
            VALUES (?, ?, ?, ?, ?, 1)
        ");
        
        foreach ($data['products'] as $index => $product) {
            if (isset($product['name']) && isset($product['sales'])) {
                $name = $product['name'];
                $sales = (float)$product['sales'];
                $percentage = isset($product['percentage']) ? (float)$product['percentage'] : 0;
                $order = $index + 1;
                
                if ($stmt) {
                    $stmt->bind_param('isdii', $projectId, $name, $sales, $percentage, $order);
                    if ($stmt->execute()) {
                        $savedData['products']++;
                    }
                }
            }
        }
        if ($stmt) $stmt->close();
    }
    
    // 3. GUARDAR PERÍODOS TCM CORRECTAMENTE
    if (isset($data['market_growth']) && is_array($data['market_growth'])) {
        // Obtener todos los productos del proyecto
        $productQuery = $conn->query("SELECT id, product_order FROM project_bcg_products WHERE project_id = $projectId ORDER BY product_order");
        $productIds = [];
        if ($productQuery) {
            while ($row = $productQuery->fetch_assoc()) {
                $productIds[] = $row['id'];
            }
        }
        
        if (!empty($productIds)) {
            $stmt = $conn->prepare("
                INSERT INTO project_bcg_market_growth 
                (project_id, product_id, period_name, period_start_year, period_end_year, tcm_percentage, period_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($data['market_growth'] as $periodIndex => $period) {
                if (isset($period['period'])) {
                    $periodName = $period['period'];
                    
                    // Extraer años del nombre del período si es posible
                    if (preg_match('/(\d{4})-(\d{4})/', $periodName, $matches)) {
                        $startYear = (int)$matches[1];
                        $endYear = (int)$matches[2];
                    } else {
                        $startYear = 2023 + $periodIndex;
                        $endYear = $startYear + 1;
                    }
                    
                    $order = $periodIndex + 1;
                    
                    // Guardar TCM para cada producto en este período
                    foreach ($productIds as $productIndex => $productId) {
                        $tcm = 0; // Valor por defecto
                        
                        // Obtener el valor TCM específico para este producto y período
                        if (isset($period['rates']) && is_array($period['rates']) && isset($period['rates'][$productIndex])) {
                            $tcm = (float)$period['rates'][$productIndex];
                        }
                        
                        if ($stmt) {
                            $stmt->bind_param('iisiidi', $projectId, $productId, $periodName, $startYear, $endYear, $tcm, $order);
                            if ($stmt->execute()) {
                                $savedData['tcm_periods']++;
                            }
                        }
                    }
                }
            }
            if ($stmt) $stmt->close();
        }
    }
    
    // 4. GUARDAR COMPETIDORES CORRECTAMENTE
    if (isset($data['competitors']) && is_array($data['competitors'])) {
        // Obtener mapeo de productos: nombre -> id
        $productNameToId = [];
        $productQuery = $conn->query("SELECT id, product_name FROM project_bcg_products WHERE project_id = $projectId ORDER BY product_order");
        if ($productQuery) {
            while ($row = $productQuery->fetch_assoc()) {
                $productNameToId[$row['product_name']] = $row['id'];
            }
        }
        
        $stmt = $conn->prepare("
            INSERT INTO project_bcg_competitors 
            (project_id, product_id, competitor_name, competitor_sales, is_max_sales, competitor_order) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($data['competitors'] as $productName => $competitors) {
            if (is_array($competitors) && isset($productNameToId[$productName])) {
                $productId = $productNameToId[$productName];
                
                // Encontrar ventas máximas para marcar el competidor principal
                $maxSales = 0;
                foreach ($competitors as $competitor) {
                    if (isset($competitor['sales'])) {
                        $maxSales = max($maxSales, (float)$competitor['sales']);
                    }
                }
                
                $compOrder = 1;
                foreach ($competitors as $competitor) {
                    if (isset($competitor['name']) && isset($competitor['sales'])) {
                        $name = $competitor['name'];
                        $sales = (float)$competitor['sales'];
                        $isMax = ($sales === $maxSales && $maxSales > 0) ? 1 : 0;
                        
                        if ($stmt) {
                            $stmt->bind_param('iisdii', $projectId, $productId, $name, $sales, $isMax, $compOrder);
                            if ($stmt->execute()) {
                                $savedData['competitors']++;
                            }
                            $compOrder++;
                        }
                    }
                }
            }
        }
        if ($stmt) $stmt->close();
    }
    
    // 5. GUARDAR DEMANDA SECTORIAL (CORREGIDO - CON SOPORTE PARA MÚLTIPLES AÑOS)
    if (isset($data['sector_demand']) && is_array($data['sector_demand'])) {
        // Obtener mapeo de productos
        $productMapping = [];
        $productQuery = $conn->query("SELECT id, product_name, product_order FROM project_bcg_products WHERE project_id = $projectId ORDER BY product_order");
        if ($productQuery) {
            while ($row = $productQuery->fetch_assoc()) {
                $productMapping[$row['product_order'] - 1] = $row['id']; // índice basado en 0
            }
        }
        
        // CORREGIDO: Guardar TODOS los años, no solo el primero
        $stmt = $conn->prepare("
            INSERT INTO project_bcg_sector_demand 
            (project_id, product_id, demand_year, total_sector_demand, company_participation, participation_percentage) 
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            total_sector_demand = VALUES(total_sector_demand),
            company_participation = VALUES(company_participation),
            participation_percentage = VALUES(participation_percentage)
        ");
        
        // Iterar por cada año de demanda sectorial
        foreach ($data['sector_demand'] as $yearData) {
            if (isset($yearData['year']) && isset($yearData['productDemands']) && is_array($yearData['productDemands'])) {
                $year = (int)$yearData['year'];
                
                // Iterar por cada producto en este año
                foreach ($yearData['productDemands'] as $productIndex => $demand) {
                    if (isset($productMapping[$productIndex])) {
                        $productId = $productMapping[$productIndex];
                        $totalDemand = (float)$demand;
                        $companyParticipation = $totalDemand * 0.1; // Estimación del 10%
                        $participationPercentage = 10.0; // 10%
                        
                        if ($stmt) {
                            $stmt->bind_param('iiiddd', $projectId, $productId, $year, $totalDemand, $companyParticipation, $participationPercentage);
                            if ($stmt->execute()) {
                                $savedData['sector_demand']++;
                            }
                        }
                    }
                }
            }
        }
        if ($stmt) $stmt->close();
    }
    
    // 6. GUARDAR FORTALEZAS
    if (isset($data['strengths']) && is_array($data['strengths'])) {
        $stmt = $conn->prepare("
            INSERT INTO project_foda_analysis (project_id, type, item_text, item_order) 
            VALUES (?, 'fortaleza', ?, ?)
        ");
        
        foreach ($data['strengths'] as $index => $strength) {
            if (isset($strength['text']) && !empty(trim($strength['text']))) {
                $text = trim($strength['text']);
                $order = $index + 1;
                
                if ($stmt) {
                    $stmt->bind_param('isi', $projectId, $text, $order);
                    if ($stmt->execute()) {
                        $savedData['strengths']++;
                    }
                }
            }
        }
        if ($stmt) $stmt->close();
    }
    
    // 7. GUARDAR DEBILIDADES
    if (isset($data['weaknesses']) && is_array($data['weaknesses'])) {
        $stmt = $conn->prepare("
            INSERT INTO project_foda_analysis (project_id, type, item_text, item_order) 
            VALUES (?, 'debilidad', ?, ?)
        ");
        
        foreach ($data['weaknesses'] as $index => $weakness) {
            if (isset($weakness['text']) && !empty(trim($weakness['text']))) {
                $text = trim($weakness['text']);
                $order = $index + 1;
                
                if ($stmt) {
                    $stmt->bind_param('isi', $projectId, $text, $order);
                    if ($stmt->execute()) {
                        $savedData['weaknesses']++;
                    }
                }
            }
        }
        if ($stmt) $stmt->close();
    }
    
    // 8. CALCULAR TCM Y PRM REALES
    calculateAndUpdateMetrics($conn, $projectId);
    
    // 9. CREAR O ACTUALIZAR ANÁLISIS PRINCIPAL
    $stmt = $conn->prepare("
        INSERT INTO project_bcg_analysis (project_id, analysis_status, updated_at) 
        VALUES (?, 'completed', CURRENT_TIMESTAMP) 
        ON DUPLICATE KEY UPDATE analysis_status = 'completed', updated_at = CURRENT_TIMESTAMP
    ");
    
    if ($stmt) {
        $stmt->bind_param('i', $projectId);
        $stmt->execute();
        $stmt->close();
    }
    
    // Confirmar transacción
    $conn->commit();
    $conn->autocommit(true);
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => '✅ Análisis BCG guardado completamente',
        'project_id' => $projectId,
        'saved_data' => $savedData,
        'total_items' => array_sum(array_values($savedData)) - 1, // -1 porque project_id no es item
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    // Rollback en caso de error
    if (isset($conn)) {
        $conn->rollback();
        $conn->autocommit(true);
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>