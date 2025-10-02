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