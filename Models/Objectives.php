<?php
require_once __DIR__ . '/../config/database.php';

class Objectives {
    private $conn;
    private $strategic_table = "project_strategic_objectives";
    private $specific_table = "project_specific_objectives";
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Guardar objetivos del proyecto (estratégicos y específicos)
    public function saveProjectObjectives($project_id, $strategic_objectives) {
        // Iniciar transacción
        $this->conn->autocommit(false);
        
        try {
            // Eliminar objetivos existentes
            $this->deleteByProjectId($project_id);
            
            foreach ($strategic_objectives as $index => $strategic) {
                // Insertar objetivo estratégico
                $strategic_id = $this->createStrategicObjective(
                    $project_id,
                    trim($strategic['title']),
                    trim($strategic['description'] ?? ''),
                    $index + 1
                );
                
                if (!$strategic_id) {
                    throw new Exception("Error al crear objetivo estratégico");
                }
                
                // Insertar objetivos específicos
                $specific_objectives = $strategic['specific_objectives'] ?? [];
                foreach ($specific_objectives as $spec_index => $specific) {
                    if (!$this->createSpecificObjective(
                        $strategic_id,
                        trim($specific['title']),
                        trim($specific['description'] ?? ''),
                        $spec_index + 1
                    )) {
                        throw new Exception("Error al crear objetivo específico");
                    }
                }
            }
            
            // Confirmar transacción
            $this->conn->commit();
            $this->conn->autocommit(true);
            
            return true;
            
        } catch (Exception $e) {
            // Revertir transacción
            $this->conn->rollback();
            $this->conn->autocommit(true);
            
            error_log("Error en saveProjectObjectives: " . $e->getMessage());
            return false;
        }
    }
    
    // Crear objetivo estratégico
    private function createStrategicObjective($project_id, $title, $description, $order) {
        $query = "INSERT INTO " . $this->strategic_table . " 
                 SET project_id=?, objective_title=?, objective_description=?, objective_order=?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("issi", $project_id, $title, $description, $order);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    // Crear objetivo específico
    private function createSpecificObjective($strategic_objective_id, $title, $description, $order) {
        $query = "INSERT INTO " . $this->specific_table . " 
                 SET strategic_objective_id=?, objective_title=?, objective_description=?, objective_order=?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("issi", $strategic_objective_id, $title, $description, $order);
        
        return $stmt->execute();
    }
    
    // Obtener objetivos estratégicos por ID de proyecto
    public function getStrategicObjectivesByProjectId($project_id) {
        $query = "SELECT * FROM " . $this->strategic_table . " 
                 WHERE project_id = ? 
                 ORDER BY objective_order ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $objectives = array();
        
        while($row = $result->fetch_assoc()) {
            // Obtener objetivos específicos para cada estratégico
            $row['specific_objectives'] = $this->getSpecificObjectivesByStrategicId($row['id']);
            $objectives[] = $row;
        }
        
        return $objectives;
    }
    
    // Obtener objetivos específicos por ID de objetivo estratégico
    public function getSpecificObjectivesByStrategicId($strategic_objective_id) {
        $query = "SELECT * FROM " . $this->specific_table . " 
                 WHERE strategic_objective_id = ? 
                 ORDER BY objective_order ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $strategic_objective_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $objectives = array();
        
        while($row = $result->fetch_assoc()) {
            $objectives[] = $row;
        }
        
        return $objectives;
    }
    
    // Obtener objetivo estratégico por ID
    public function getStrategicObjectiveById($id) {
        $query = "SELECT * FROM " . $this->strategic_table . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if($result->num_rows == 1) {
            $objective = $result->fetch_assoc();
            $objective['specific_objectives'] = $this->getSpecificObjectivesByStrategicId($id);
            return $objective;
        }
        return false;
    }
    
    // Obtener objetivo específico por ID
    public function getSpecificObjectiveById($id) {
        $query = "SELECT * FROM " . $this->specific_table . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
        return false;
    }
    
    // Eliminar todos los objetivos de un proyecto
    public function deleteByProjectId($project_id) {
        // Primero eliminar objetivos específicos
        $query1 = "DELETE ps FROM " . $this->specific_table . " ps 
                  INNER JOIN " . $this->strategic_table . " st ON ps.strategic_objective_id = st.id 
                  WHERE st.project_id = ?";
        
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->bind_param("i", $project_id);
        $stmt1->execute();
        
        // Luego eliminar objetivos estratégicos
        $query2 = "DELETE FROM " . $this->strategic_table . " WHERE project_id = ?";
        
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bind_param("i", $project_id);
        
        return $stmt2->execute();
    }
    
    // Contar objetivos estratégicos de un proyecto
    public function countStrategicObjectivesByProjectId($project_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->strategic_table . " WHERE project_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }
    
    // Contar objetivos específicos de un objetivo estratégico
    public function countSpecificObjectivesByStrategicId($strategic_objective_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->specific_table . " WHERE strategic_objective_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $strategic_objective_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }
}
?>