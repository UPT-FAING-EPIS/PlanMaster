<?php
require_once __DIR__ . '/../config/database.php';

class BCGAnalysis {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * MÉTODO PRINCIPAL: Guardar análisis BCG completo
     * Recibe todos los datos del formulario y los guarda de manera simple
     */
    public function saveComplete($project_id, $data) {
        try {
            $this->conn->autocommit(FALSE);
            
            // 1. Limpiar datos existentes del proyecto
            $this->clearProjectData($project_id);
            
            // 2. Crear registro principal
            $this->createMainRecord($project_id);
            
            // 3. Guardar productos
            if (isset($data['products']) && is_array($data['products'])) {
                foreach ($data['products'] as $index => $product) {
                    $product_id = $this->saveProduct($project_id, $product, $index);
                    
                    // Guardar períodos TCM si existen
                    if (isset($data['periods']) && isset($data['periods'][$index])) {
                        $this->saveMarketEvolution($project_id, $product_id, $data['periods'][$index]);
                    }
                }
            }
            
            $this->conn->commit();
            $this->conn->autocommit(TRUE);
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->conn->autocommit(TRUE);
            error_log("BCG Save Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Limpiar todos los datos BCG de un proyecto
     */
    private function clearProjectData($project_id) {
        // Eliminar competidores
        $query = "DELETE c FROM project_bcg_competitors c 
                  INNER JOIN project_bcg_products p ON c.product_id = p.id 
                  WHERE p.project_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
        
        // Eliminar evolución de mercado
        $query = "DELETE FROM project_bcg_market_evolution WHERE project_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
        
        // Eliminar productos
        $query = "DELETE FROM project_bcg_products WHERE project_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
        
        // Eliminar registro principal
        $query = "DELETE FROM project_bcg_analysis WHERE project_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
    }
    
    /**
     * Crear registro principal del análisis BCG
     */
    private function createMainRecord($project_id) {
        $query = "INSERT INTO project_bcg_analysis (project_id) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
    }
    
    /**
     * Guardar un producto individual
     */
    private function saveProduct($project_id, $product, $order) {
        $name = trim($product['name'] ?? '');
        $sales = floatval($product['sales_forecast'] ?? 0);
        $tcm = floatval($product['tcm_rate'] ?? 0);
        
        if (empty($name) || $sales <= 0) {
            throw new Exception("Producto #" . ($order + 1) . ": nombre y ventas son obligatorios");
        }
        
        // Calcular porcentaje sobre total (se calculará después con todos los productos)
        $query = "INSERT INTO project_bcg_products 
                  (project_id, product_name, sales_forecast, tcm_calculated, product_order) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('isddi', $project_id, $name, $sales, $tcm, $order);
        $stmt->execute();
        
        $product_id = $this->conn->insert_id;
        
        // Guardar competidores si existen
        if (isset($product['competitors']) && is_array($product['competitors'])) {
            $this->saveProductCompetitors($project_id, $product_id, $product['competitors']);
        }
        
        return $product_id;
    }
    
    /**
     * Guardar competidores de un producto
     */
    private function saveProductCompetitors($project_id, $product_id, $competitors) {
        foreach ($competitors as $index => $competitor) {
            $name = trim($competitor['name'] ?? '');
            $sales = floatval($competitor['sales'] ?? 0);
            
            if (!empty($name) && $sales > 0) {
                $query = "INSERT INTO project_bcg_competitors 
                          (project_id, product_id, competitor_name, competitor_sales, competitor_order) 
                          VALUES (?, ?, ?, ?, ?)";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param('iisdi', $project_id, $product_id, $name, $sales, $index);
                $stmt->execute();
            }
        }
    }
    
    /**
     * Guardar períodos TCM para un producto
     */
    public function saveMarketEvolution($project_id, $product_id, $periods) {
        if (!is_array($periods)) return;
        
        // Eliminar períodos existentes del producto
        $query = "DELETE FROM project_bcg_market_evolution WHERE project_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $project_id, $product_id);
        $stmt->execute();
        
        // Insertar nuevos períodos
        foreach ($periods as $index => $period) {
            $start_year = intval($period['start_year'] ?? 0);
            $end_year = intval($period['end_year'] ?? 0);
            $tcm_percentage = floatval($period['tcm_percentage'] ?? 0);
            
            if ($start_year > 0 && $end_year > 0 && $end_year > $start_year) {
                $query = "INSERT INTO project_bcg_market_evolution 
                          (project_id, product_id, period_start_year, period_end_year, tcm_percentage, period_order) 
                          VALUES (?, ?, ?, ?, ?, ?)";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bind_param('iiiidi', $project_id, $product_id, $start_year, $end_year, $tcm_percentage, $index);
                $stmt->execute();
            }
        }
    }
    
    /**
     * Obtener períodos TCM de un producto
     */
    public function getMarketEvolution($product_id) {
        $query = "SELECT * FROM project_bcg_market_evolution 
                  WHERE product_id = ? 
                  ORDER BY period_order ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $periods = [];
        
        while ($row = $result->fetch_assoc()) {
            $periods[] = $row;
        }
        
        return $periods;
    }
    
    /**
     * Calcular TCM promedio para un producto basado en sus períodos
     */
    public function calculateProductTCM($product_id) {
        $periods = $this->getMarketEvolution($product_id);
        
        if (count($periods) === 0) {
            return 0;
        }
        
        $total_tcm = 0;
        foreach ($periods as $period) {
            $total_tcm += $period['tcm_percentage'];
        }
        
        return round($total_tcm / count($periods), 2);
    }
    
    /**
     * Obtener todos los productos de un proyecto con sus cálculos
     */
    public function getProjectAnalysis($project_id) {
        $query = "SELECT * FROM project_bcg_products 
                  WHERE project_id = ? 
                  ORDER BY product_order ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $products = [];
        $total_sales = 0;
        
        // Obtener productos y calcular total
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
            $total_sales += $row['sales_forecast'];
        }
        
        // Calcular porcentajes, TCM real y PRM
        foreach ($products as &$product) {
            // Calcular porcentaje sobre ventas totales
            $product['sales_percentage'] = $total_sales > 0 
                ? round(($product['sales_forecast'] / $total_sales) * 100, 2)
                : 0;
            
            // Calcular TCM real basado en períodos
            $product['tcm_calculated'] = $this->calculateProductTCM($product['id']);
            
            // Obtener períodos TCM
            $product['market_evolution'] = $this->getMarketEvolution($product['id']);
            
            // Obtener competidores y calcular PRM
            $competitors = $this->getProductCompetitors($product['id']);
            $max_competitor_sales = 0;
            
            foreach ($competitors as $comp) {
                if ($comp['competitor_sales'] > $max_competitor_sales) {
                    $max_competitor_sales = $comp['competitor_sales'];
                }
            }
            
            $product['prm_calculated'] = $max_competitor_sales > 0 
                ? round($product['sales_forecast'] / $max_competitor_sales, 2)
                : 0;
            
            $product['competitors'] = $competitors;
        }
        
        return $products;
    }
    
    /**
     * Obtener competidores de un producto
     */
    public function getProductCompetitors($product_id) {
        $query = "SELECT * FROM project_bcg_competitors 
                  WHERE product_id = ? 
                  ORDER BY competitor_order ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $competitors = [];
        
        while ($row = $result->fetch_assoc()) {
            $competitors[] = $row;
        }
        
        return $competitors;
    }
    
    /**
     * Verificar si el análisis está completo
     */
    public function isComplete($project_id) {
        $query = "SELECT COUNT(*) as count FROM project_bcg_products 
                  WHERE project_id = ? AND sales_forecast > 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
    
    /**
     * Calcular matriz BCG con posiciones
     */
    public function calculateMatrix($project_id) {
        $products = $this->getProjectAnalysis($project_id);
        $matrix = [];
        
        foreach ($products as $product) {
            $tcm = $product['tcm_calculated'];
            $prm = $product['prm_calculated'];
            
            // Determinar posición BCG
            // TCM alto >= 10%, PRM alto >= 1.0
            if ($tcm >= 10 && $prm >= 1.0) {
                $position = 'estrella';
            } elseif ($tcm >= 10 && $prm < 1.0) {
                $position = 'interrogante';
            } elseif ($tcm < 10 && $prm >= 1.0) {
                $position = 'vaca';
            } else {
                $position = 'perro';
            }
            
            $matrix[] = [
                'product_name' => $product['product_name'],
                'sales_forecast' => $product['sales_forecast'],
                'sales_percentage' => $product['sales_percentage'],
                'tcm_rate' => $tcm,
                'prm_rate' => $prm,
                'position' => $position
            ];
        }
        
        return $matrix;
    }
    
    /**
     * NUEVOS MÉTODOS PARA INTERFAZ MEJORADA BCG
     */
     
    /**
     * Guardar análisis BCG mejorado con 4 mini-steps
     */
    public function saveEnhancedBCG($project_id, $bcg_data) {
        try {
            $this->conn->autocommit(FALSE);
            
            // Limpiar datos existentes
            $this->clearProjectData($project_id);
            
            // Crear registro principal
            $this->createMainRecord($project_id);
            
            // Step 1: Guardar productos y ventas
            if (isset($bcg_data['products']) && is_array($bcg_data['products'])) {
                $product_ids = [];
                foreach ($bcg_data['products'] as $index => $product) {
                    $product_id = $this->saveEnhancedProduct($project_id, $product, $index);
                    $product_ids[] = $product_id;
                }
                
                // Step 2: Guardar evolución del mercado
                if (isset($bcg_data['market_evolution']) && is_array($bcg_data['market_evolution'])) {
                    $this->saveEnhancedMarketEvolution($project_id, $product_ids, $bcg_data['market_evolution']);
                }
                
                // Step 3: Guardar competidores
                if (isset($bcg_data['competitors']) && is_array($bcg_data['competitors'])) {
                    $this->saveEnhancedCompetitors($project_id, $product_ids, $bcg_data['competitors']);
                }
            }
            
            $this->conn->commit();
            $this->conn->autocommit(TRUE);
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->conn->autocommit(TRUE);
            error_log("Enhanced BCG Save Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Guardar producto mejorado con cálculos automáticos
     */
    private function saveEnhancedProduct($project_id, $product_data, $order) {
        $sql = "INSERT INTO project_bcg_products 
                (project_id, product_name, sales_forecast, sales_percentage, 
                 tcm_calculated, prm_calculated, bcg_position, product_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        $name = $product_data['name'] ?? '';
        $sales = floatval($product_data['sales'] ?? 0);
        $percentage = floatval($product_data['percentage'] ?? 0);
        $tcm = floatval($product_data['tcm'] ?? 0);
        $prm = floatval($product_data['prm'] ?? 0);
        $position = $product_data['bcg_position'] ?? null;
        
        $stmt->bind_param("isdddisi", 
            $project_id, $name, $sales, $percentage, 
            $tcm, $prm, $position, $order
        );
        
        $stmt->execute();
        return $this->conn->insert_id;
    }
    
    /**
     * Guardar evolución del mercado mejorada
     */
    private function saveEnhancedMarketEvolution($project_id, $product_ids, $market_data) {
        $sql = "INSERT INTO project_bcg_market_evolution 
                (project_id, product_id, period_start_year, period_end_year, 
                 tcm_percentage, period_order) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        foreach ($market_data as $period_index => $period) {
            $period_parts = explode('-', $period['period'] ?? '');
            $start_year = intval($period_parts[0] ?? date('Y'));
            $end_year = intval($period_parts[1] ?? date('Y') + 1);
            
            foreach ($period['rates'] as $product_index => $rate) {
                if (isset($product_ids[$product_index])) {
                    $tcm_percentage = floatval($rate ?? 0);
                    
                    $stmt->bind_param("iiidii", 
                        $project_id, 
                        $product_ids[$product_index], 
                        $start_year, 
                        $end_year, 
                        $tcm_percentage, 
                        $period_index
                    );
                    
                    $stmt->execute();
                }
            }
        }
    }
    
    /**
     * Guardar competidores mejorados
     */
    private function saveEnhancedCompetitors($project_id, $product_ids, $competitors_data) {
        $sql = "INSERT INTO project_bcg_competitors 
                (project_id, product_id, competitor_name, competitor_sales, 
                 is_max_competitor, competitor_order) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        foreach ($competitors_data as $product_name => $competitors) {
            // Encontrar product_id por nombre
            $product_index = array_search($product_name, array_column($competitors_data, 'product_name'));
            
            if ($product_index !== false && isset($product_ids[$product_index])) {
                $product_id = $product_ids[$product_index];
                
                foreach ($competitors as $comp_index => $competitor) {
                    $comp_name = $competitor['name'] ?? '';
                    $comp_sales = floatval($competitor['sales'] ?? 0);
                    $is_max = intval($competitor['isMax'] ?? 0);
                    
                    $stmt->bind_param("iisdii", 
                        $project_id, 
                        $product_id, 
                        $comp_name, 
                        $comp_sales, 
                        $is_max, 
                        $comp_index
                    );
                    
                    $stmt->execute();
                }
            }
        }
    }
    
    /**
     * Obtener análisis BCG completo mejorado
     */
    public function getEnhancedBCGAnalysis($project_id) {
        $analysis = [
            'products' => $this->getEnhancedProducts($project_id),
            'market_evolution' => $this->getEnhancedMarketEvolution($project_id),
            'competitors' => $this->getEnhancedCompetitors($project_id),
            'matrix_data' => []
        ];
        
        // Calcular datos de matriz
        $analysis['matrix_data'] = $this->calculateEnhancedMatrix($analysis['products']);
        
        return $analysis;
    }
    
    /**
     * Obtener productos mejorados
     */
    private function getEnhancedProducts($project_id) {
        $sql = "SELECT * FROM project_bcg_products 
                WHERE project_id = ? 
                ORDER BY product_order ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $products = [];
        
        while ($row = $result->fetch_assoc()) {
            $products[] = [
                'id' => $row['id'],
                'name' => $row['product_name'],
                'sales' => floatval($row['sales_forecast']),
                'percentage' => floatval($row['sales_percentage']),
                'tcm' => floatval($row['tcm_calculated']),
                'prm' => floatval($row['prm_calculated']),
                'bcg_position' => $row['bcg_position'],
                'order' => $row['product_order']
            ];
        }
        
        return $products;
    }
    
    /**
     * Obtener evolución del mercado mejorada
     */
    private function getEnhancedMarketEvolution($project_id) {
        $sql = "SELECT me.*, p.product_name 
                FROM project_bcg_market_evolution me
                JOIN project_bcg_products p ON me.product_id = p.id
                WHERE me.project_id = ? 
                ORDER BY me.period_order ASC, p.product_order ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $evolution = [];
        
        while ($row = $result->fetch_assoc()) {
            $period_key = $row['period_start_year'] . '-' . $row['period_end_year'];
            
            if (!isset($evolution[$period_key])) {
                $evolution[$period_key] = [
                    'period' => $period_key,
                    'rates' => []
                ];
            }
            
            $evolution[$period_key]['rates'][] = floatval($row['tcm_percentage']);
        }
        
        return array_values($evolution);
    }
    
    /**
     * Obtener competidores mejorados
     */
    private function getEnhancedCompetitors($project_id) {
        $sql = "SELECT c.*, p.product_name 
                FROM project_bcg_competitors c
                JOIN project_bcg_products p ON c.product_id = p.id
                WHERE c.project_id = ? 
                ORDER BY p.product_order ASC, c.competitor_order ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $competitors = [];
        
        while ($row = $result->fetch_assoc()) {
            $product_name = $row['product_name'];
            
            if (!isset($competitors[$product_name])) {
                $competitors[$product_name] = [];
            }
            
            $competitors[$product_name][] = [
                'name' => $row['competitor_name'],
                'sales' => floatval($row['competitor_sales']),
                'isMax' => boolval($row['is_max_competitor']),
                'order' => $row['competitor_order']
            ];
        }
        
        return $competitors;
    }
    
    /**
     * Calcular matriz BCG mejorada
     */
    private function calculateEnhancedMatrix($products) {
        $matrix_data = [];
        
        foreach ($products as $product) {
            $tcm = $product['tcm'];
            $prm = $product['prm'];
            
            // Determinar cuadrante BCG
            if ($tcm > 10 && $prm > 100) {
                $quadrant = 'Estrella';
                $color = '#4CAF50';
            } elseif ($tcm <= 10 && $prm > 100) {
                $quadrant = 'Vaca Lechera';
                $color = '#2196F3';
            } elseif ($tcm > 10 && $prm <= 100) {
                $quadrant = 'Interrogante';
                $color = '#FF9800';
            } else {
                $quadrant = 'Perro';
                $color = '#9E9E9E';
            }
            
            $matrix_data[] = [
                'product_name' => $product['name'],
                'sales' => $product['sales'],
                'percentage' => $product['percentage'],
                'tcm' => $tcm,
                'prm' => $prm,
                'quadrant' => $quadrant,
                'color' => $color,
                'x' => $prm,  // Posición X en la matriz
                'y' => $tcm,  // Posición Y en la matriz
                'size' => $product['percentage'] * 2  // Tamaño proporcional
            ];
        }
        
        return $matrix_data;
    }

    /**
     * Eliminar análisis completo de un proyecto
     */
    public function deleteByProject($project_id) {
        try {
            $this->conn->autocommit(FALSE);
            $this->clearProjectData($project_id);
            $this->conn->commit();
            $this->conn->autocommit(TRUE);
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->conn->autocommit(TRUE);
            throw $e;
        }
    }
}
?>