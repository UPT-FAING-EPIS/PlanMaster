<?php
require_once __DIR__ . '/../config/database.php';

class Project {
    private $conn;
    private $table_name = "strategic_projects";
    
    public $id;
    public $user_id;
    public $project_name;
    public $company_name;
    public $created_at;
    public $updated_at;
    public $completed_at;
    public $status;
    public $progress_percentage;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Crear nuevo proyecto
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET user_id=?, project_name=?, company_name=?, status=?, progress_percentage=?";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bind_param("isssd", 
            $this->user_id,
            $this->project_name,
            $this->company_name,
            $this->status,
            $this->progress_percentage
        );
        
        if($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        return false;
    }
    
    // Obtener proyecto por ID
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
    
    // Obtener proyectos por usuario
    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE user_id = ? 
                 ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $projects = array();
        
        while($row = $result->fetch_assoc()) {
            $projects[] = $row;
        }
        
        return $projects;
    }
    
    // Actualizar proyecto
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                 SET project_name=?, company_name=?, status=?, progress_percentage=?, updated_at=NOW()
                 WHERE id=? AND user_id=?";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bind_param("sssdii", 
            $this->project_name,
            $this->company_name,
            $this->status,
            $this->progress_percentage,
            $this->id,
            $this->user_id
        );
        
        return $stmt->execute();
    }
    
    // Actualizar progreso del proyecto
    public function updateProgress($project_id, $percentage) {
        $status = $percentage >= 100 ? 'completed' : 'in_progress';
        $completed_at = $percentage >= 100 ? 'NOW()' : 'NULL';
        
        $query = "UPDATE " . $this->table_name . " 
                 SET progress_percentage=?, status=?, updated_at=NOW()";
        
        if ($percentage >= 100) {
            $query .= ", completed_at=NOW()";
        }
        
        $query .= " WHERE id=?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("dsi", $percentage, $status, $project_id);
        
        return $stmt->execute();
    }
    
    // Eliminar proyecto
    public function delete($id, $user_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id=? AND user_id=?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $id, $user_id);
        
        return $stmt->execute();
    }
    
    // Obtener estadísticas del usuario
    public function getUserStats($user_id) {
        $query = "SELECT 
                    COUNT(*) as total_projects,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_projects,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_projects,
                    AVG(progress_percentage) as avg_progress
                 FROM " . $this->table_name . " 
                 WHERE user_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
        
        return [
            'total_projects' => 0,
            'completed_projects' => 0,
            'in_progress_projects' => 0,
            'avg_progress' => 0
        ];
    }
}
?>