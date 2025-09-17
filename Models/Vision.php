<?php
require_once __DIR__ . '/../config/database.php';

class Vision {
    private $conn;
    private $table_name = "project_vision";
    
    public $id;
    public $project_id;
    public $vision_text;
    public $is_completed;
    public $created_at;
    public $updated_at;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Guardar o actualizar visión
    public function save() {
        // Verificar si ya existe una visión para este proyecto
        $existing = $this->getByProjectId($this->project_id);
        
        if ($existing) {
            return $this->update();
        } else {
            return $this->create();
        }
    }
    
    // Crear nueva visión
    private function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET project_id=?, vision_text=?, is_completed=?";
        
        $stmt = $this->conn->prepare($query);
        
        $this->is_completed = 1; // Al guardar, se considera completada
        
        $stmt->bind_param("isi", 
            $this->project_id,
            $this->vision_text,
            $this->is_completed
        );
        
        if($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        return false;
    }
    
    // Actualizar visión existente
    private function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET vision_text=?, is_completed=?, updated_at=NOW()
                 WHERE project_id=?";
        
        $stmt = $this->conn->prepare($query);
        
        $this->is_completed = 1; // Al guardar, se considera completada
        
        $stmt->bind_param("sii", 
            $this->vision_text,
            $this->is_completed,
            $this->project_id
        );
        
        return $stmt->execute();
    }
    
    // Obtener visión por ID de proyecto
    public function getByProjectId($project_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE project_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
        return false;
    }
    
    // Obtener visión por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
        return false;
    }
    
    // Eliminar visión
    public function delete($project_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE project_id=?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $project_id);
        
        return $stmt->execute();
    }
}
?>