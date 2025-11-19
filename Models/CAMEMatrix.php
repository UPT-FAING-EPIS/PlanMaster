<?php
require_once __DIR__ . '/../config/database.php';

class CAMEMatrix {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Obtener todas las acciones CAME de un proyecto
    public function getByProject($project_id) {
        try {
            $query = "SELECT * FROM project_came_matrix 
                     WHERE project_id = ? 
                     ORDER BY action_type, action_number ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $actions = [
                'C' => [],
                'A' => [],
                'M' => [],
                'E' => []
            ];
            
            while ($row = $result->fetch_assoc()) {
                $actions[$row['action_type']][] = $row;
            }
            
            return $actions;
        } catch (Exception $e) {
            error_log("Error obteniendo acciones CAME: " . $e->getMessage());
            return ['C' => [], 'A' => [], 'M' => [], 'E' => []];
        }
    }
    
    // Crear o actualizar una acción CAME
    public function saveAction($project_id, $action_type, $action_number, $action_text) {
        try {
            // Verificar si ya existe
            $query = "SELECT id FROM project_came_matrix 
                     WHERE project_id = ? AND action_type = ? AND action_number = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("isi", $project_id, $action_type, $action_number);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Actualizar existente
                $row = $result->fetch_assoc();
                $query = "UPDATE project_came_matrix 
                         SET action_text = ?, updated_at = CURRENT_TIMESTAMP 
                         WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("si", $action_text, $row['id']);
                return $stmt->execute();
            } else {
                // Crear nueva
                $query = "INSERT INTO project_came_matrix 
                         (project_id, action_type, action_number, action_text) 
                         VALUES (?, ?, ?, ?)";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("isis", $project_id, $action_type, $action_number, $action_text);
                return $stmt->execute();
            }
        } catch (Exception $e) {
            error_log("Error guardando acción CAME: " . $e->getMessage());
            return false;
        }
    }
    
    // Eliminar una acción específica
    public function deleteAction($project_id, $action_type, $action_number) {
        try {
            $query = "DELETE FROM project_came_matrix 
                     WHERE project_id = ? AND action_type = ? AND action_number = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("isi", $project_id, $action_type, $action_number);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error eliminando acción CAME: " . $e->getMessage());
            return false;
        }
    }
    
    // Reordenar acciones después de eliminar una
    public function reorderActions($project_id, $action_type, $deleted_number) {
        try {
            $query = "UPDATE project_came_matrix 
                     SET action_number = action_number - 1 
                     WHERE project_id = ? AND action_type = ? AND action_number > ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("isi", $project_id, $action_type, $deleted_number);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error reordenando acciones CAME: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener el siguiente número de acción para un tipo
    public function getNextActionNumber($project_id, $action_type) {
        try {
            $query = "SELECT COALESCE(MAX(action_number), 0) + 1 as next_number 
                     FROM project_came_matrix 
                     WHERE project_id = ? AND action_type = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("is", $project_id, $action_type);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row['next_number'];
        } catch (Exception $e) {
            error_log("Error obteniendo siguiente número: " . $e->getMessage());
            return 1;
        }
    }
    
    // Eliminar todas las acciones de un proyecto
    public function deleteByProject($project_id) {
        try {
            $query = "DELETE FROM project_came_matrix WHERE project_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $project_id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error eliminando acciones del proyecto: " . $e->getMessage());
            return false;
        }
    }
}
?>