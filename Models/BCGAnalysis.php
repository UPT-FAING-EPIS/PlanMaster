<?php
require_once __DIR__ . '/../config/database.php';

class BCGAnalysis {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    // Crear análisis BCG para un proyecto
    public function createAnalysis($project_id) {
        try {
            $query = "INSERT INTO project_bcg_analysis (project_id) VALUES (?)
                     ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $project_id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error creating BCG analysis: " . $e->getMessage());
            return false;
        }
    }
    
    // Guardar productos del análisis BCG
    public function saveProducts($project_id, $products) {
        try {
            $this->conn->autocommit(FALSE);
            
            // Crear análisis si no existe
            $this->createAnalysis($project_id);
            
            // Eliminar productos existentes
            $this->deleteProductsByProject($project_id);
            
            // Insertar nuevos productos
            $query = "INSERT INTO project_bcg_products (project_id, product_name, sales_forecast, tcm_rate, prm_rate, product_order) 
                     VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            
            foreach ($products as $index => $product) {
                $product_name = $product['name'] ?? '';
                $sales_forecast = floatval($product['sales_forecast'] ?? 0);
                $tcm_rate = floatval($product['tcm_rate'] ?? 0);
                $prm_rate = floatval($product['prm_rate'] ?? 0);
                $order = $index + 1;
                
                $stmt->bind_param('isdddi', $project_id, $product_name, $sales_forecast, $tcm_rate, $prm_rate, $order);
                $stmt->execute();
            }
            
            $this->conn->commit();
            $this->conn->autocommit(TRUE);
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->conn->autocommit(TRUE);
            error_log("Error saving BCG products: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener productos por proyecto
    public function getProductsByProject($project_id) {
        try {
            $query = "SELECT * FROM project_bcg_products 
                     WHERE project_id = ? 
                     ORDER BY product_order ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $project_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $products = [];
            
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            
            return $products;
            
        } catch (Exception $e) {
            error_log("Error getting BCG products: " . $e->getMessage());
            return [];
        }
    }
    
    // Guardar evolución del mercado
    public function saveMarketEvolution($product_id, $market_data) {
        try {
            $this->conn->autocommit(FALSE);
            
            // Eliminar datos existentes del producto
            $delete_query = "DELETE FROM project_bcg_market_evolution WHERE product_id = ?";
            $delete_stmt = $this->conn->prepare($delete_query);
            $delete_stmt->bind_param('i', $product_id);
            $delete_stmt->execute();
            
            // Insertar nuevos datos
            $query = "INSERT INTO project_bcg_market_evolution (product_id, year, market_value) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            
            foreach ($market_data as $year => $value) {
                $market_value = floatval($value);
                $stmt->bind_param('iid', $product_id, $year, $market_value);
                $stmt->execute();
            }
            
            $this->conn->commit();
            $this->conn->autocommit(TRUE);
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->conn->autocommit(TRUE);
            error_log("Error saving market evolution: " . $e->getMessage());
            return false;
        }
    }
    
    // Guardar competidores
    public function saveCompetitors($product_id, $competitors) {
        try {
            $this->conn->autocommit(FALSE);
            
            // Eliminar competidores existentes del producto
            $delete_query = "DELETE FROM project_bcg_competitors WHERE product_id = ?";
            $delete_stmt = $this->conn->prepare($delete_query);
            $delete_stmt->bind_param('i', $product_id);
            $delete_stmt->execute();
            
            // Insertar nuevos competidores
            $query = "INSERT INTO project_bcg_competitors (product_id, competitor_name, sales_value, competitor_order) 
                     VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            
            foreach ($competitors as $index => $competitor) {
                $competitor_name = $competitor['name'] ?? '';
                $sales_value = floatval($competitor['sales'] ?? 0);
                $order = $index + 1;
                
                if (!empty(trim($competitor_name))) {
                    $stmt->bind_param('isdi', $product_id, $competitor_name, $sales_value, $order);
                    $stmt->execute();
                }
            }
            
            $this->conn->commit();
            $this->conn->autocommit(TRUE);
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->conn->autocommit(TRUE);
            error_log("Error saving competitors: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener competidores por producto
    public function getCompetitorsByProduct($product_id) {
        try {
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
            
        } catch (Exception $e) {
            error_log("Error getting competitors: " . $e->getMessage());
            return [];
        }
    }
    
    // Calcular datos de la matriz BCG
    public function calculateBCGMatrix($project_id) {
        $products = $this->getProductsByProject($project_id);
        $bcg_data = [];
        $total_sales = 0;
        
        // Calcular total de ventas
        foreach ($products as $product) {
            $total_sales += $product['sales_forecast'];
        }
        
        foreach ($products as $product) {
            $product_id = $product['id'];
            
            // Calcular porcentaje sobre ventas totales
            $sales_percentage = $total_sales > 0 ? ($product['sales_forecast'] / $total_sales) * 100 : 0;
            
            // Obtener competidores para calcular PRM
            $competitors = $this->getCompetitorsByProduct($product_id);
            $max_competitor_sales = 0;
            
            foreach ($competitors as $competitor) {
                if ($competitor['sales_value'] > $max_competitor_sales) {
                    $max_competitor_sales = $competitor['sales_value'];
                }
            }
            
            // Calcular PRM (Participación Relativa del Mercado)
            $prm = $max_competitor_sales > 0 ? $product['sales_forecast'] / $max_competitor_sales : 0;
            
            // Determinar posición en la matriz BCG
            $position = $this->determineBCGPosition($product['tcm_rate'], $prm);
            
            $bcg_data[] = [
                'product_name' => $product['product_name'],
                'sales_forecast' => $product['sales_forecast'],
                'sales_percentage' => round($sales_percentage, 2),
                'tcm_rate' => $product['tcm_rate'],
                'prm_rate' => round($prm, 2),
                'position' => $position,
                'x_coordinate' => $prm, // Para el gráfico
                'y_coordinate' => $product['tcm_rate'], // Para el gráfico
                'bubble_size' => $sales_percentage // Tamaño de la burbuja basado en ventas
            ];
        }
        
        return $bcg_data;
    }
    
    // Determinar posición en matriz BCG
    private function determineBCGPosition($tcm_rate, $prm_rate) {
        // TCM alto: >= 10%, TCM bajo: < 10%
        // PRM alto: >= 1.0, PRM bajo: < 1.0
        
        if ($tcm_rate >= 10 && $prm_rate >= 1.0) {
            return 'estrella';
        } elseif ($tcm_rate >= 10 && $prm_rate < 1.0) {
            return 'interrogante';
        } elseif ($tcm_rate < 10 && $prm_rate >= 1.0) {
            return 'vaca';
        } else {
            return 'perro';
        }
    }
    
    // Verificar si el análisis BCG está completo
    public function isComplete($project_id) {
        $products = $this->getProductsByProject($project_id);
        
        if (count($products) < 1) {
            return false;
        }
        
        // Verificar que todos los productos tengan datos básicos
        foreach ($products as $product) {
            if (empty($product['product_name']) || 
                $product['sales_forecast'] <= 0 || 
                $product['tcm_rate'] < 0) {
                return false;
            }
        }
        
        return true;
    }
    
    // Eliminar productos por proyecto
    private function deleteProductsByProject($project_id) {
        try {
            $query = "DELETE FROM project_bcg_products WHERE project_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $project_id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error deleting BCG products: " . $e->getMessage());
            return false;
        }
    }
    
    // Eliminar análisis completo por proyecto
    public function deleteByProject($project_id) {
        try {
            $this->conn->autocommit(FALSE);
            
            // Eliminar en orden correcto debido a foreign keys
            $this->deleteProductsByProject($project_id);
            
            $query = "DELETE FROM project_bcg_analysis WHERE project_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $project_id);
            $stmt->execute();
            
            $this->conn->commit();
            $this->conn->autocommit(TRUE);
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->conn->autocommit(TRUE);
            error_log("Error deleting BCG analysis: " . $e->getMessage());
            return false;
        }
    }
}
?>