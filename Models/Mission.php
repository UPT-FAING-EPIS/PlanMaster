<?php
require_once __DIR__ . '/../config/database.php';

class Mission {
    private $conn;
    private $table_name = "project_mission";
    
    public $id;
    public $project_id;
    public $mission_text;
    public $is_completed;
    public $created_at;
    public $updated_at;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Guardar o actualizar misión
    public function save() {
        // Verificar si ya existe una misión para este proyecto
        $existing = $this->getByProjectId($this->project_id);
        
        if ($existing) {
            return $this->update();
        } else {
            return $this->create();
        }
    }
    
    // Crear nueva misión
    private function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET project_id=?, mission_text=?, is_completed=?";
        
        $stmt = $this->conn->prepare($query);
        
        $this->is_completed = 1; // Al guardar, se considera completada
        
        $stmt->bind_param("isi", 
            $this->project_id,
            $this->mission_text,
            $this->is_completed
        );
        
        if($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        return false;
    }
    
    // Actualizar misión existente
    private function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET mission_text=?, is_completed=?, updated_at=NOW()
                 WHERE project_id=?";
        
        $stmt = $this->conn->prepare($query);
        
        $this->is_completed = 1; // Al guardar, se considera completada
        
        $stmt->bind_param("sii", 
            $this->mission_text,
            $this->is_completed,
            $this->project_id
        );
        
        return $stmt->execute();
    }
    
    // Obtener misión por ID de proyecto
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
    
    // Obtener misión por ID
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
    
    // Eliminar misión
    public function delete($project_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE project_id=?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $project_id);
        
        return $stmt->execute();
    }
}
?>