<?php
require_once __DIR__ . '/../config/database.php';

class FodaAnalysis {
    private $conn;
    private $table_name = "project_foda_analysis";
    
    public $id;
    public $project_id;
    public $type; // oportunidad, amenaza, fortaleza, debilidad
    public $item_text;
    public $item_order;
    public $created_at;
    public $updated_at;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Crear nuevo elemento FODA
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET project_id=?, type=?, item_text=?, item_order=?";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bind_param("issi", 
            $this->project_id,
            $this->type,
            $this->item_text,
            $this->item_order
        );
        
        if($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        return false;
    }
    
    // Obtener elementos FODA por proyecto
    public function getByProject($project_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE project_id = ? 
                 ORDER BY type, item_order ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $items = array();
        
        while($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
    
    // Obtener elementos FODA por proyecto y tipo
    public function getByProjectAndType($project_id, $type) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE project_id = ? AND type = ? 
                 ORDER BY item_order ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $project_id, $type);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $items = array();
        
        while($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
    
    // Actualizar elemento FODA
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET item_text=?, item_order=?, updated_at=NOW()
                 WHERE id=? AND project_id=?";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bind_param("siii", 
            $this->item_text,
            $this->item_order,
            $this->id,
            $this->project_id
        );
        
        return $stmt->execute();
    }
    
    // Eliminar elemento FODA
    public function delete($id, $project_id) {
        $query = "DELETE FROM " . $this->table_name . " 
                 WHERE id=? AND project_id=?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $id, $project_id);
        
        return $stmt->execute();
    }
    
    // Eliminar todos los elementos FODA de un proyecto
    public function deleteByProject($project_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE project_id=?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $project_id);
        
        return $stmt->execute();
    }
    
    // Eliminar todos los elementos de un tipo específico de un proyecto
    public function deleteByProjectAndType($project_id, $type) {
        $query = "DELETE FROM " . $this->table_name . " 
                 WHERE project_id=? AND type=?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $project_id, $type);
        
        return $stmt->execute();
    }
    
    // Verificar si existe análisis FODA para un proyecto
    public function existsByProject($project_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                 WHERE project_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
    
    // Obtener el máximo order para un tipo específico
    public function getMaxOrderByType($project_id, $type) {
        $query = "SELECT COALESCE(MAX(item_order), 0) as max_order 
                 FROM " . $this->table_name . " 
                 WHERE project_id = ? AND type = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $project_id, $type);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return intval($row['max_order']);
    }
    
    // Obtener resumen de completitud del análisis FODA
    public function getCompletionStatus($project_id) {
        $query = "SELECT type, COUNT(*) as count 
                 FROM " . $this->table_name . " 
                 WHERE project_id = ? 
                 GROUP BY type";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $status = array(
            'oportunidad' => 0,
            'amenaza' => 0,
            'fortaleza' => 0,
            'debilidad' => 0,
            'total' => 0,
            'is_complete' => false
        );
        
        while($row = $result->fetch_assoc()) {
            $status[$row['type']] = intval($row['count']);
            $status['total'] += intval($row['count']);
        }
        
        // Consideramos completo si tiene al menos 1 elemento de cada tipo
        $status['is_complete'] = ($status['oportunidad'] > 0 && 
                                 $status['amenaza'] > 0 && 
                                 $status['fortaleza'] > 0 && 
                                 $status['debilidad'] > 0);
        
        return $status;
    }
}
?>