<?php
require_once __DIR__ . '/../config/database.php';

class StrategicAnalysis {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Crear una nueva relación estratégica
    public function create($project_id, $relation_type, $fortaleza_id = null, $debilidad_id = null, $oportunidad_id = null, $amenaza_id = null, $value_score = 0) {
        try {
            $query = "INSERT INTO project_strategic_relations 
                     (project_id, relation_type, fortaleza_id, debilidad_id, oportunidad_id, amenaza_id, value_score) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("isiiiii", $project_id, $relation_type, $fortaleza_id, $debilidad_id, $oportunidad_id, $amenaza_id, $value_score);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error creando relación estratégica: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener relaciones por proyecto
    public function getByProject($project_id) {
        try {
            $query = "SELECT sr.*, 
                             f.description as fortaleza_desc,
                             d.description as debilidad_desc,
                             o.description as oportunidad_desc,
                             a.description as amenaza_desc
                      FROM project_strategic_relations sr
                      LEFT JOIN project_foda_analysis f ON sr.fortaleza_id = f.id
                      LEFT JOIN project_foda_analysis d ON sr.debilidad_id = d.id
                      LEFT JOIN project_foda_analysis o ON sr.oportunidad_id = o.id
                      LEFT JOIN project_foda_analysis a ON sr.amenaza_id = a.id
                      WHERE sr.project_id = ?
                      ORDER BY sr.relation_type, sr.created_at ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error obteniendo relaciones estratégicas: " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener relaciones por tipo
    public function getByProjectAndType($project_id, $relation_type) {
        try {
            $query = "SELECT sr.*, 
                             f.description as fortaleza_desc,
                             d.description as debilidad_desc,
                             o.description as oportunidad_desc,
                             a.description as amenaza_desc
                      FROM project_strategic_relations sr
                      LEFT JOIN project_foda_analysis f ON sr.fortaleza_id = f.id
                      LEFT JOIN project_foda_analysis d ON sr.debilidad_id = d.id
                      LEFT JOIN project_foda_analysis o ON sr.oportunidad_id = o.id
                      LEFT JOIN project_foda_analysis a ON sr.amenaza_id = a.id
                      WHERE sr.project_id = ? AND sr.relation_type = ?
                      ORDER BY sr.created_at ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("is", $project_id, $relation_type);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error obteniendo relaciones estratégicas por tipo: " . $e->getMessage());
            return [];
        }
    }
    
    // Actualizar una relación
    public function update($id, $value_score) {
        try {
            $query = "UPDATE project_strategic_relations 
                     SET value_score = ?, updated_at = CURRENT_TIMESTAMP 
                     WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $value_score, $id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error actualizando relación estratégica: " . $e->getMessage());
            return false;
        }
    }
    
    // Eliminar relaciones por proyecto
    public function deleteByProject($project_id) {
        try {
            $query = "DELETE FROM project_strategic_relations WHERE project_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $project_id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error eliminando relaciones estratégicas: " . $e->getMessage());
            return false;
        }
    }
    
    // Verificar si existe una relación específica
    public function existsRelation($project_id, $relation_type, $fortaleza_id = null, $debilidad_id = null, $oportunidad_id = null, $amenaza_id = null) {
        try {
            $query = "SELECT id FROM project_strategic_relations 
                     WHERE project_id = ? AND relation_type = ? 
                     AND fortaleza_id = ? AND debilidad_id = ? 
                     AND oportunidad_id = ? AND amenaza_id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("isiiii", $project_id, $relation_type, $fortaleza_id, $debilidad_id, $oportunidad_id, $amenaza_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error verificando existencia de relación: " . $e->getMessage());
            return false;
        }
    }
}
?>