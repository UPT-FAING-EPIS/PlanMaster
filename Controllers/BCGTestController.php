<?php
// Inicializar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Models/BCGTest.php';
require_once __DIR__ . '/../Controllers/AuthController.php';

/**
 * Controlador BCG Test - Manejo de la matriz BCG interactiva de pruebas
 */
class BCGTestController {
    private $bcgTest;
    
    public function __construct() {
        $this->bcgTest = new BCGTest();
    }
    
    /**
     * Obtener datos de ejemplo para la matriz BCG
     */
    public function getExampleData() {
        try {
            AuthController::requireLogin();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $this->bcgTest->getExampleData()
            ]);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Procesar y analizar datos BCG enviados por AJAX
     */
    public function analyzeData() {
        try {
            AuthController::requireLogin();
            
            // Obtener datos JSON del cuerpo de la petición
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                throw new Exception('Datos inválidos o faltantes');
            }
            
            // Validar datos
            $validation = $this->bcgTest->validateCompleteData($data);
            if ($validation !== true) {
                throw new Exception('Datos inválidos: ' . implode(', ', $validation));
            }
            
            // Generar análisis
            $analysis = $this->bcgTest->generateBCGAnalysis($data);
            
            // Generar recomendaciones
            $recommendations = $this->bcgTest->generateRecommendations($analysis);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'analysis' => $analysis,
                'recommendations' => $recommendations,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Calcular métricas específicas
     */
    public function calculateMetrics() {
        try {
            AuthController::requireLogin();
            
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data || !isset($data['products'])) {
                throw new Exception('Productos requeridos');
            }
            
            $metrics = [];
            $products = $data['products'];
            $marketGrowth = $data['market_growth'] ?? [];
            $competitors = $data['competitors'] ?? [];
            
            foreach ($products as $index => $product) {
                $tcm = $this->bcgTest->calculateAverageTCM($marketGrowth, $index);
                $productCompetitors = $competitors[$product['name']] ?? [];
                $prm = $this->bcgTest->calculatePRM($product['sales'], $productCompetitors);
                $position = $this->bcgTest->getBCGPosition($tcm, $prm);
                
                $metrics[] = [
                    'product_name' => $product['name'],
                    'tcm' => round($tcm, 2),
                    'prm' => round($prm, 3),
                    'position' => $position
                ];
            }
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'metrics' => $metrics
            ]);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Exportar análisis BCG a diferentes formatos
     */
    public function exportAnalysis() {
        try {
            AuthController::requireLogin();
            
            $format = $_GET['format'] ?? 'json';
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                throw new Exception('Datos requeridos para exportar');
            }
            
            $analysis = $this->bcgTest->generateBCGAnalysis($data);
            $recommendations = $this->bcgTest->generateRecommendations($analysis);
            
            switch ($format) {
                case 'csv':
                    $this->exportAsCSV($analysis, $recommendations);
                    break;
                    
                case 'pdf':
                    $this->exportAsPDF($analysis, $recommendations);
                    break;
                    
                default:
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'analysis' => $analysis,
                        'recommendations' => $recommendations,
                        'export_date' => date('Y-m-d H:i:s')
                    ]);
                    break;
            }
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Exportar como CSV
     */
    private function exportAsCSV($analysis, $recommendations) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="bcg_analysis_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Encabezados
        fputcsv($output, ['Producto', 'Ventas', 'Porcentaje', 'TCM', 'PRM', 'Posición BCG', 'Recomendación']);
        
        // Datos
        foreach ($analysis['products'] as $index => $product) {
            $recommendation = isset($recommendations[$index]) ? $recommendations[$index]['recommendation'] : '';
            
            fputcsv($output, [
                $product['name'],
                $product['sales'],
                round($product['percentage'], 2) . '%',
                round($product['tcm'], 2) . '%',
                round($product['prm'], 3),
                $product['position']['position'],
                $recommendation
            ]);
        }
        
        fclose($output);
    }
    
    /**
     * Exportar como PDF (básico)
     */
    private function exportAsPDF($analysis, $recommendations) {
        // Para una implementación completa de PDF, se necesitaría una librería como TCPDF o FPDF
        // Por ahora, devolvemos HTML que puede ser convertido a PDF
        
        header('Content-Type: text/html');
        header('Content-Disposition: inline; filename="bcg_analysis_' . date('Y-m-d') . '.html"');
        
        echo "<!DOCTYPE html>
<html>
<head>
    <title>Análisis BCG - " . date('Y-m-d') . "</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary { background: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Análisis BCG - " . date('Y-m-d H:i:s') . "</h1>
    
    <div class='summary'>
        <h3>Resumen</h3>
        <p>Total de Ventas: " . number_format($analysis['summary']['total_sales']) . "</p>
        <p>TCM Promedio: " . round($analysis['summary']['average_tcm'], 2) . "%</p>
    </div>
    
    <h3>Análisis por Producto</h3>
    <table>
        <tr>
            <th>Producto</th>
            <th>Ventas</th>
            <th>% Ventas</th>
            <th>TCM</th>
            <th>PRM</th>
            <th>Posición BCG</th>
            <th>Recomendación</th>
        </tr>";
        
        foreach ($analysis['products'] as $index => $product) {
            $recommendation = isset($recommendations[$index]) ? $recommendations[$index]['recommendation'] : '';
            
            echo "<tr>
                <td>" . htmlspecialchars($product['name']) . "</td>
                <td>" . number_format($product['sales']) . "</td>
                <td>" . round($product['percentage'], 2) . "%</td>
                <td>" . round($product['tcm'], 2) . "%</td>
                <td>" . round($product['prm'], 3) . "</td>
                <td>" . htmlspecialchars($product['position']['position']) . "</td>
                <td>" . htmlspecialchars($recommendation) . "</td>
            </tr>";
        }
        
        echo "</table>
</body>
</html>";
    }
    
    /**
     * Guardar análisis BCG en la base de datos
     */
    public function saveBCGData() {
        try {
            // Debug: registrar la petición
            error_log("BCGTestController::saveBCGData() - Petición recibida");
            
            // Obtener datos JSON del cuerpo de la petición
            $input = file_get_contents('php://input');
            error_log("Datos recibidos: " . substr($input, 0, 200) . "...");
            
            $data = json_decode($input, true);
            
            if (!$data) {
                throw new Exception('Datos inválidos o faltantes. Input: ' . substr($input, 0, 100));
            }
            
            // Usar project_id por defecto para testing
            $projectId = 1; // ID fijo para debugging
            
            $result = $this->bcgTest->saveBCGData($projectId, $data);
            
            header('Content-Type: application/json');
            echo json_encode($result);
            
        } catch (Exception $e) {
            error_log("Error en saveBCGData: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Cargar análisis BCG desde la base de datos
     */
    public function loadBCGData() {
        try {
            // Temporalmente sin verificación de login para debugging
            // AuthController::requireLogin();
            
            $projectId = $_GET['project_id'] ?? ($_SESSION['current_project_id'] ?? 1);
            $result = $this->bcgTest->loadBCGData($projectId);
            
            header('Content-Type: application/json');
            echo json_encode($result);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Manejar peticiones AJAX del frontend
     */
    public function handleRequest() {
        $action = $_GET['action'] ?? $_POST['action'] ?? '';
        
        switch ($action) {
            case 'get_example':
                $this->getExampleData();
                break;
                
            case 'analyze':
                $this->analyzeData();
                break;
                
            case 'calculate_metrics':
                $this->calculateMetrics();
                break;
                
            case 'export':
                $this->exportAnalysis();
                break;
                
            case 'save':
                $this->saveBCGData();
                break;
                
            case 'load':
                $this->loadBCGData();
                break;
                
            case 'test':
                $this->testConnection();
                break;
                
            default:
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Acción no válida'
                ]);
                break;
        }
    }
    
    /**
     * Test de conexión para debugging
     */
    public function testConnection() {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Controlador BCG funcionando correctamente',
            'session_active' => session_status() === PHP_SESSION_ACTIVE,
            'logged_in' => AuthController::isLoggedIn(),
            'session_data' => [
                'user_id' => $_SESSION['user_id'] ?? 'No definido',
                'logged_in' => $_SESSION['logged_in'] ?? 'No definido'
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}

// Manejar peticiones si este archivo es llamado directamente
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) || isset($_POST['action'])) {
        $controller = new BCGTestController();
        $controller->handleRequest();
        exit();
    }
}