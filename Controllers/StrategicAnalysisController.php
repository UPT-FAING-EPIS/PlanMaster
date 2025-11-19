<?php
require_once __DIR__ . '/../config/database.php';

class StrategicAnalysisController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Obtener todos los datos FODA del proyecto
    public function getFodaData($project_id) {
        try {
            $query = "SELECT * FROM project_foda_analysis WHERE project_id = ? ORDER BY type, created_at ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $results = $result->fetch_all(MYSQLI_ASSOC);
            
            $foda = [
                'fortalezas' => [],
                'debilidades' => [],
                'oportunidades' => [],
                'amenazas' => []
            ];
            
            foreach ($results as $item) {
                switch ($item['type']) {
                    case 'fortaleza':
                        $foda['fortalezas'][] = $item;
                        break;
                    case 'debilidad':
                        $foda['debilidades'][] = $item;
                        break;
                    case 'oportunidad':
                        $foda['oportunidades'][] = $item;
                        break;
                    case 'amenaza':
                        $foda['amenazas'][] = $item;
                        break;
                }
            }
            
            return $foda;
        } catch (Exception $e) {
            error_log("Error obteniendo datos FODA: " . $e->getMessage());
            return ['fortalezas' => [], 'debilidades' => [], 'oportunidades' => [], 'amenazas' => []];
        }
    }
    
    // Obtener relaciones estratégicas existentes
    public function getStrategicRelations($project_id) {
        try {
            $query = "SELECT * FROM project_strategic_relations WHERE project_id = ? ORDER BY relation_type, created_at ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $results = $result->fetch_all(MYSQLI_ASSOC);
            
            $relations = [
                'FO' => [],
                'FA' => [],
                'DO' => [],
                'DA' => []
            ];
            
            foreach ($results as $relation) {
                $relations[$relation['relation_type']][] = $relation;
            }
            
            return $relations;
        } catch (Exception $e) {
            error_log("Error obteniendo relaciones estratégicas: " . $e->getMessage());
            return ['FO' => [], 'FA' => [], 'DO' => [], 'DA' => []];
        }
    }
    
    // Guardar una relación estratégica
    public function saveStrategicRelation($project_id, $relation_type, $fortaleza_id = null, $debilidad_id = null, $oportunidad_id = null, $amenaza_id = null, $value_score = 0) {
        try {
            // Verificar si ya existe la relación
            $checkQuery = "SELECT id FROM project_strategic_relations 
                          WHERE project_id = ? AND relation_type = ? 
                          AND fortaleza_id = ? AND debilidad_id = ? 
                          AND oportunidad_id = ? AND amenaza_id = ?";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bind_param("isiiii", $project_id, $relation_type, $fortaleza_id, $debilidad_id, $oportunidad_id, $amenaza_id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->fetch_assoc()) {
                // Actualizar relación existente
                $updateQuery = "UPDATE project_strategic_relations 
                               SET value_score = ?, updated_at = CURRENT_TIMESTAMP 
                               WHERE project_id = ? AND relation_type = ? 
                               AND fortaleza_id = ? AND debilidad_id = ? 
                               AND oportunidad_id = ? AND amenaza_id = ?";
                $updateStmt = $this->db->prepare($updateQuery);
                $updateStmt->bind_param("iisiiii", $value_score, $project_id, $relation_type, $fortaleza_id, $debilidad_id, $oportunidad_id, $amenaza_id);
                return $updateStmt->execute();
            } else {
                // Crear nueva relación
                $insertQuery = "INSERT INTO project_strategic_relations 
                               (project_id, relation_type, fortaleza_id, debilidad_id, oportunidad_id, amenaza_id, value_score) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)";
                $insertStmt = $this->db->prepare($insertQuery);
                $insertStmt->bind_param("isiiiii", $project_id, $relation_type, $fortaleza_id, $debilidad_id, $oportunidad_id, $amenaza_id, $value_score);
                return $insertStmt->execute();
            }
        } catch (Exception $e) {
            error_log("Error guardando relación estratégica: " . $e->getMessage());
            return false;
        }
    }
    
    // Calcular y guardar análisis estratégico
    public function calculateStrategicAnalysis($project_id) {
        try {
            // Obtener todas las relaciones
            $relations = $this->getStrategicRelations($project_id);
            
            // Calcular totales
            $fo_total = 0;
            $fa_total = 0;
            $do_total = 0;
            $da_total = 0;
            
            foreach ($relations['FO'] as $relation) {
                $fo_total += $relation['value_score'];
            }
            
            foreach ($relations['FA'] as $relation) {
                $fa_total += $relation['value_score'];
            }
            
            foreach ($relations['DO'] as $relation) {
                $do_total += $relation['value_score'];
            }
            
            foreach ($relations['DA'] as $relation) {
                $da_total += $relation['value_score'];
            }
            
            // Determinar tipo de estrategia
            $max_score = max($fo_total, $fa_total, $do_total, $da_total);
            $strategy_type = '';
            $strategy_description = '';
            
            if ($max_score == $fo_total) {
                $strategy_type = 'Ofensiva';
                $strategy_description = 'Deberá adoptar estrategias de crecimiento';
            } elseif ($max_score == $fa_total) {
                $strategy_type = 'Defensiva';
                $strategy_description = 'Deberá adoptar estrategias defensivas';
            } elseif ($max_score == $do_total) {
                $strategy_type = 'Adaptativa';
                $strategy_description = 'Deberá adoptar estrategias de reorientación';
            } else {
                $strategy_type = 'Supervivencia';
                $strategy_description = 'Deberá adoptar estrategias de supervivencia';
            }
            
            // Guardar o actualizar análisis
            $checkQuery = "SELECT id FROM project_strategic_analysis WHERE project_id = ?";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bind_param("i", $project_id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->fetch_assoc()) {
                // Actualizar análisis existente
                $updateQuery = "UPDATE project_strategic_analysis 
                               SET fo_total = ?, fa_total = ?, do_total = ?, da_total = ?, 
                                   strategy_type = ?, strategy_description = ?, updated_at = CURRENT_TIMESTAMP 
                               WHERE project_id = ?";
                $updateStmt = $this->db->prepare($updateQuery);
                $updateStmt->bind_param("iiiissi", $fo_total, $fa_total, $do_total, $da_total, $strategy_type, $strategy_description, $project_id);
                return $updateStmt->execute();
            } else {
                // Crear nuevo análisis
                $insertQuery = "INSERT INTO project_strategic_analysis 
                               (project_id, fo_total, fa_total, do_total, da_total, strategy_type, strategy_description) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)";
                $insertStmt = $this->db->prepare($insertQuery);
                $insertStmt->bind_param("iiiiss", $project_id, $fo_total, $fa_total, $do_total, $da_total, $strategy_type, $strategy_description);
                return $insertStmt->execute();
            }
        } catch (Exception $e) {
            error_log("Error calculando análisis estratégico: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener análisis estratégico
    public function getStrategicAnalysis($project_id) {
        try {
            $query = "SELECT * FROM project_strategic_analysis WHERE project_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error obteniendo análisis estratégico: " . $e->getMessage());
            return null;
        }
    }
    
    // Procesar datos del formulario
    public function processStrategicData($project_id, $data) {
        try {
            $this->db->autocommit(FALSE);
            
            // Eliminar relaciones existentes del proyecto
            $deleteQuery = "DELETE FROM project_strategic_relations WHERE project_id = ?";
            $deleteStmt = $this->db->prepare($deleteQuery);
            $deleteStmt->bind_param("i", $project_id);
            $deleteStmt->execute();
            
            // Procesar relaciones FO (Fortalezas-Oportunidades)
            if (isset($data['fo_relations'])) {
                foreach ($data['fo_relations'] as $relation) {
                    $this->saveStrategicRelation(
                        $project_id,
                        'FO',
                        $relation['fortaleza_id'],
                        null,
                        $relation['oportunidad_id'],
                        null,
                        $relation['value']
                    );
                }
            }
            
            // Procesar relaciones FA (Fortalezas-Amenazas)
            if (isset($data['fa_relations'])) {
                foreach ($data['fa_relations'] as $relation) {
                    $this->saveStrategicRelation(
                        $project_id,
                        'FA',
                        $relation['fortaleza_id'],
                        null,
                        null,
                        $relation['amenaza_id'],
                        $relation['value']
                    );
                }
            }
            
            // Procesar relaciones DO (Debilidades-Oportunidades)
            if (isset($data['do_relations'])) {
                foreach ($data['do_relations'] as $relation) {
                    $this->saveStrategicRelation(
                        $project_id,
                        'DO',
                        null,
                        $relation['debilidad_id'],
                        $relation['oportunidad_id'],
                        null,
                        $relation['value']
                    );
                }
            }
            
            // Procesar relaciones DA (Debilidades-Amenazas)
            if (isset($data['da_relations'])) {
                foreach ($data['da_relations'] as $relation) {
                    $this->saveStrategicRelation(
                        $project_id,
                        'DA',
                        null,
                        $relation['debilidad_id'],
                        null,
                        $relation['amenaza_id'],
                        $relation['value']
                    );
                }
            }
            
            // Calcular análisis
            $this->calculateStrategicAnalysis($project_id);
            
            $this->db->commit();
            $this->db->autocommit(TRUE);
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            $this->db->autocommit(TRUE);
            error_log("Error procesando datos estratégicos: " . $e->getMessage());
            return false;
        }
    }
}

// Procesar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $controller = new StrategicAnalysisController();
    
    switch ($_POST['action']) {
        case 'save_relation':
            $project_id = intval($_POST['project_id']);
            $relation_type = $_POST['relation_type'];
            $fortaleza_id = $_POST['fortaleza_id'] ?? null;
            $debilidad_id = $_POST['debilidad_id'] ?? null;
            $oportunidad_id = $_POST['oportunidad_id'] ?? null;
            $amenaza_id = $_POST['amenaza_id'] ?? null;
            $value_score = intval($_POST['value_score']);
            
            $result = $controller->saveStrategicRelation(
                $project_id, 
                $relation_type, 
                $fortaleza_id, 
                $debilidad_id, 
                $oportunidad_id, 
                $amenaza_id, 
                $value_score
            );
            
            if ($result) {
                $controller->calculateStrategicAnalysis($project_id);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al guardar relación']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Acción no válida']);
            break;
    }
    exit();
}
?>