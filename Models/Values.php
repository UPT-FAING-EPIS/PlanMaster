<?php
require_once __DIR__ . '/../config/database.php';

class Values {
    private $conn;
    private $table_name = "project_values";
    
    public $id;
    public $project_id;
    public $value_text;
    public $value_order;
    public $created_at;
    public $updated_at;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Guardar valores del proyecto (reemplaza todos los valores existentes)
    public function saveProjectValues($project_id, $values) {
        // Iniciar transacción
        $this->conn->autocommit(false);
        
        try {
            // Eliminar valores existentes
            $this->deleteByProjectId($project_id);
            
            // Insertar nuevos valores
            $query = "INSERT INTO " . $this->table_name . " 
                     SET project_id=?, value_text=?, value_order=?";
            
            $stmt = $this->conn->prepare($query);
            
            foreach ($values as $index => $value) {
                $value_text = trim($value);
                $value_order = $index + 1;
                
                if (!empty($value_text)) {
                    $stmt->bind_param("isi", $project_id, $value_text, $value_order);
                    
                    if (!$stmt->execute()) {
                        throw new Exception("Error al insertar valor: " . $value_text);
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
            
            error_log("Error en saveProjectValues: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener valores por ID de proyecto
    public function getByProjectId($project_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE project_id = ? 
                 ORDER BY value_order ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $values = array();
        
        while($row = $result->fetch_assoc()) {
            $values[] = $row;
        }
        
        return $values;
    }
    
    // Obtener solo los textos de los valores (para mostrar)
    public function getValueTexts($project_id) {
        $values = $this->getByProjectId($project_id);
        $texts = array();
        
        foreach ($values as $value) {
            $texts[] = $value['value_text'];
        }
        
        return $texts;
    }
    
    // Crear nuevo valor
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET project_id=?, value_text=?, value_order=?";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bind_param("isi", 
            $this->project_id,
            $this->value_text,
            $this->value_order
        );
        
        if($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        return false;
    }
    
    // Obtener valor por ID
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
    
    // Eliminar valores por ID de proyecto
    public function deleteByProjectId($project_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE project_id=?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $project_id);
        
        return $stmt->execute();
    }
    
    // Eliminar valor por ID
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id=?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
    
    // Contar valores de un proyecto
    public function countByProjectId($project_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE project_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }
}
?>