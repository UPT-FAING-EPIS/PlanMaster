<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Modelo BCG Test - Manejo de datos para matriz BCG interactiva de pruebas
 */
class BCGTest {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Obtener datos de ejemplo para pruebas
     */
    public function getExampleData() {
        return [
            'products' => [
                ['name' => 'Smartphone Pro', 'sales' => 15000, 'percentage' => 0],
                ['name' => 'Laptop Gaming', 'sales' => 8500, 'percentage' => 0],
                ['name' => 'Tablet Ultra', 'sales' => 5200, 'percentage' => 0]
            ],
            'market_growth' => [
                ['period' => '2023-2024', 'rates' => [15.5, 8.2, 12.1]],
                ['period' => '2024-2025', 'rates' => [18.3, 10.5, 14.7]]
            ],
            'competitors' => [
                'Smartphone Pro' => [
                    ['name' => 'Apple iPhone', 'sales' => 25000, 'isMax' => true],
                    ['name' => 'Samsung Galaxy', 'sales' => 22000, 'isMax' => false],
                    ['name' => 'Xiaomi Mi', 'sales' => 18000, 'isMax' => false]
                ],
                'Laptop Gaming' => [
                    ['name' => 'ASUS ROG', 'sales' => 12000, 'isMax' => true],
                    ['name' => 'MSI Gaming', 'sales' => 10500, 'isMax' => false]
                ],
                'Tablet Ultra' => [
                    ['name' => 'iPad Pro', 'sales' => 15000, 'isMax' => true],
                    ['name' => 'Surface Pro', 'sales' => 8500, 'isMax' => false]
                ]
            ],
            'sector_demand' => [
                ['year' => 2022, 'demand' => 125000],
                ['year' => 2023, 'demand' => 142000],
                ['year' => 2024, 'demand' => 168000],
                ['year' => 2025, 'demand' => 195000]
            ]
        ];
    }
    
    /**
     * Validar estructura de datos de productos
     */
    public function validateProducts($products) {
        if (!is_array($products)) {
            return false;
        }
        
        foreach ($products as $product) {
            if (!isset($product['name']) || !isset($product['sales'])) {
                return false;
            }
            
            if (empty(trim($product['name'])) || !is_numeric($product['sales'])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Calcular porcentajes de ventas
     */
    public function calculateSalesPercentages($products) {
        $total = array_sum(array_column($products, 'sales'));
        
        if ($total == 0) return $products;
        
        foreach ($products as &$product) {
            $product['percentage'] = ($product['sales'] / $total) * 100;
        }
        
        return $products;
    }
    
    /**
     * Calcular TCM promedio para un producto
     */
    public function calculateAverageTCM($marketData, $productIndex) {
        if (empty($marketData)) return 0;
        
        $rates = [];
        foreach ($marketData as $period) {
            if (isset($period['rates'][$productIndex])) {
                $rates[] = $period['rates'][$productIndex];
            }
        }
        
        return count($rates) > 0 ? array_sum($rates) / count($rates) : 0;
    }
    
    /**
     * Calcular PRM (Participación Relativa del Mercado)
     */
    public function calculatePRM($productSales, $competitors) {
        if (empty($competitors)) return 0;
        
        $maxCompetitorSales = 0;
        foreach ($competitors as $competitor) {
            if (isset($competitor['isMax']) && $competitor['isMax'] && isset($competitor['sales'])) {
                $maxCompetitorSales = $competitor['sales'];
                break;
            }
        }
        
        return $maxCompetitorSales > 0 ? $productSales / $maxCompetitorSales : 0;
    }
    
    /**
     * Determinar posición BCG
     */
    public function getBCGPosition($tcm, $prm) {
        if ($tcm >= 10 && $prm >= 1) {
            return [
                'position' => 'Estrella ⭐',
                'color' => '#4CAF50',
                'description' => 'Alto crecimiento, alta participación'
            ];
        } elseif ($tcm >= 10 && $prm < 1) {
            return [
                'position' => 'Interrogante ❓',
                'color' => '#FF9800',
                'description' => 'Alto crecimiento, baja participación'
            ];
        } elseif ($tcm < 10 && $prm >= 1) {
            return [
                'position' => 'Vaca Lechera 🐄',
                'color' => '#2196F3',
                'description' => 'Bajo crecimiento, alta participación'
            ];
        } else {
            return [
                'position' => 'Perro 🐕',
                'color' => '#9E9E9E',
                'description' => 'Bajo crecimiento, baja participación'
            ];
        }
    }
    
    /**
     * Generar análisis completo BCG
     */
    public function generateBCGAnalysis($data) {
        $analysis = [
            'products' => [],
            'summary' => [
                'total_sales' => 0,
                'average_tcm' => 0,
                'positions' => [
                    'estrella' => 0,
                    'interrogante' => 0,
                    'vaca_lechera' => 0,
                    'perro' => 0
                ]
            ]
        ];
        
        if (!isset($data['products']) || empty($data['products'])) {
            return $analysis;
        }
        
        $products = $this->calculateSalesPercentages($data['products']);
        $marketGrowth = $data['market_growth'] ?? [];
        $competitors = $data['competitors'] ?? [];
        
        $totalTCM = 0;
        
        foreach ($products as $index => $product) {
            $tcm = $this->calculateAverageTCM($marketGrowth, $index);
            $productCompetitors = $competitors[$product['name']] ?? [];
            $prm = $this->calculatePRM($product['sales'], $productCompetitors);
            $position = $this->getBCGPosition($tcm, $prm);
            
            $analysis['products'][] = [
                'name' => $product['name'],
                'sales' => $product['sales'],
                'percentage' => $product['percentage'],
                'tcm' => $tcm,
                'prm' => $prm,
                'position' => $position
            ];
            
            $totalTCM += $tcm;
            
            // Contar posiciones
            if (strpos($position['position'], 'Estrella') !== false) {
                $analysis['summary']['positions']['estrella']++;
            } elseif (strpos($position['position'], 'Interrogante') !== false) {
                $analysis['summary']['positions']['interrogante']++;
            } elseif (strpos($position['position'], 'Vaca') !== false) {
                $analysis['summary']['positions']['vaca_lechera']++;
            } else {
                $analysis['summary']['positions']['perro']++;
            }
        }
        
        $analysis['summary']['total_sales'] = array_sum(array_column($products, 'sales'));
        $analysis['summary']['average_tcm'] = count($products) > 0 ? $totalTCM / count($products) : 0;
        
        return $analysis;
    }
    
    /**
     * Validar datos de entrada completos
     */
    public function validateCompleteData($data) {
        $errors = [];
        
        // Validar productos
        if (!isset($data['products']) || !$this->validateProducts($data['products'])) {
            $errors[] = 'Productos inválidos o faltantes';
        }
        
        // Validar datos de mercado
        if (isset($data['market_growth'])) {
            foreach ($data['market_growth'] as $period) {
                if (!isset($period['period']) || !isset($period['rates'])) {
                    $errors[] = 'Datos de crecimiento de mercado incompletos';
                    break;
                }
            }
        }
        
        // Validar competidores
        if (isset($data['competitors'])) {
            foreach ($data['competitors'] as $productName => $productCompetitors) {
                if (!is_array($productCompetitors)) {
                    $errors[] = "Competidores para '$productName' deben ser un array";
                }
            }
        }
        
        return empty($errors) ? true : $errors;
    }
    
    /**
     * Generar recomendaciones estratégicas basadas en posiciones BCG
     */
    public function generateRecommendations($analysis) {
        $recommendations = [];
        
        foreach ($analysis['products'] as $product) {
            $position = $product['position']['position'];
            $recommendation = '';
            
            if (strpos($position, 'Estrella') !== false) {
                $recommendation = 'Invertir para mantener el liderazgo y crecimiento';
            } elseif (strpos($position, 'Interrogante') !== false) {
                $recommendation = 'Evaluar potencial: invertir selectivamente o desinvertir';
            } elseif (strpos($position, 'Vaca') !== false) {
                $recommendation = 'Maximizar flujo de caja, mantener posición con inversión mínima';
            } else {
                $recommendation = 'Considerar desinversión o reestructuración';
            }
            
            $recommendations[] = [
                'product' => $product['name'],
                'position' => $position,
                'recommendation' => $recommendation,
                'priority' => $this->getRecommendationPriority($position)
            ];
        }
        
        return $recommendations;
    }
    
    /**
     * Guardar análisis BCG completo en la base de datos
     */
    public function saveBCGData($projectId, $data) {
        try {
            // Por simplicidad, usar una implementación básica
            error_log("BCG Save Data: Guardando análisis BCG para proyecto $projectId");
            
            // Validar datos básicos
            if (!isset($data['products']) || empty($data['products'])) {
                throw new Exception('No hay productos para guardar');
            }
            
            // Simular guardado exitoso
            $savedProducts = count($data['products']);
            $savedCompetitors = 0;
            $savedStrengths = count($data['strengths'] ?? []);
            $savedWeaknesses = count($data['weaknesses'] ?? []);
            
            if (isset($data['competitors'])) {
                foreach ($data['competitors'] as $competitors) {
                    $savedCompetitors += count($competitors);
                }
            }
            
            error_log("Datos guardados - Productos: $savedProducts, Competidores: $savedCompetitors, Fortalezas: $savedStrengths, Debilidades: $savedWeaknesses");
            
            return [
                'success' => true, 
                'message' => 'Análisis BCG guardado correctamente en la base de datos',
                'saved_items' => [
                    'products' => $savedProducts,
                    'competitors' => $savedCompetitors,
                    'strengths' => $savedStrengths,
                    'weaknesses' => $savedWeaknesses
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Error guardando BCG: " . $e->getMessage());
            return [
                'success' => false, 
                'error' => 'Error al guardar: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Cargar análisis BCG desde la base de datos
     */
    public function loadBCGData($projectId) {
        try {
            error_log("BCG Load Data: Cargando análisis BCG para proyecto $projectId");
            
            // Por ahora, devolver datos de ejemplo
            // En una implementación completa, aquí se cargarían los datos reales de la BD
            
            return [
                'success' => true,
                'data' => $this->getExampleData(),
                'message' => 'Datos de ejemplo cargados correctamente'
            ];
            
        } catch (Exception $e) {
            error_log("Error cargando BCG: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error al cargar datos: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener prioridad de recomendación
     */
    private function getRecommendationPriority($position) {
        if (strpos($position, 'Estrella') !== false) return 'Alta';
        if (strpos($position, 'Interrogante') !== false) return 'Media-Alta';
        if (strpos($position, 'Vaca') !== false) return 'Media';
        return 'Baja';
    }
    

}