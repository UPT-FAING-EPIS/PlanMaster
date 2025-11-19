<?php
require_once __DIR__ . '/../Models/CAMEMatrix.php';

class CAMEMatrixController {
    private $cameMatrix;
    
    public function __construct() {
        $this->cameMatrix = new CAMEMatrix();
    }
    
    // Obtener todas las acciones CAME de un proyecto
    public function getCAMEActions($project_id) {
        return $this->cameMatrix->getByProject($project_id);
    }
    
    // Guardar una acción CAME
    public function saveAction($project_id, $action_type, $action_number, $action_text) {
        return $this->cameMatrix->saveAction($project_id, $action_type, $action_number, $action_text);
    }
    
    // Agregar nueva acción
    public function addAction($project_id, $action_type, $action_text) {
        $next_number = $this->cameMatrix->getNextActionNumber($project_id, $action_type);
        return $this->cameMatrix->saveAction($project_id, $action_type, $next_number, $action_text);
    }
    
    // Eliminar una acción
    public function deleteAction($project_id, $action_type, $action_number) {
        $result = $this->cameMatrix->deleteAction($project_id, $action_type, $action_number);
        if ($result) {
            // Reordenar las acciones restantes
            $this->cameMatrix->reorderActions($project_id, $action_type, $action_number);
        }
        return $result;
    }
    
    // Obtener descripción del tipo de acción
    public function getActionTypeDescription($type) {
        $descriptions = [
            'C' => 'Corregir las debilidades',
            'A' => 'Afrontar las amenazas', 
            'M' => 'Mantener las fortalezas',
            'E' => 'Explotar las oportunidades'
        ];
        return $descriptions[$type] ?? '';
    }
    
    // Obtener placeholder para cada tipo
    public function getPlaceholderText($type) {
        $placeholders = [
            'C' => 'Describa una acción para corregir debilidades...',
            'A' => 'Describa una acción para afrontar amenazas...',
            'M' => 'Describa una acción para mantener fortalezas...',
            'E' => 'Describa una acción para explotar oportunidades...'
        ];
        return $placeholders[$type] ?? '';
    }
    
    /**
     * Obtener todas las acciones de un proyecto agrupadas por tipo
     */
    public function getActionsByProject($project_id) {
        return $this->cameMatrix->getByProject($project_id);
    }
}

// Procesar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $controller = new CAMEMatrixController();
    
    switch ($_POST['action']) {
        case 'save_action':
            $project_id = intval($_POST['project_id']);
            $action_type = $_POST['action_type'];
            $action_number = intval($_POST['action_number']);
            $action_text = trim($_POST['action_text']);
            
            $result = $controller->saveAction($project_id, $action_type, $action_number, $action_text);
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al guardar acción']);
            }
            break;
            
        case 'add_action':
            $project_id = intval($_POST['project_id']);
            $action_type = $_POST['action_type'];
            $action_text = trim($_POST['action_text']);
            
            $result = $controller->addAction($project_id, $action_type, $action_text);
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al agregar acción']);
            }
            break;
            
        case 'delete_action':
            $project_id = intval($_POST['project_id']);
            $action_type = $_POST['action_type'];
            $action_number = intval($_POST['action_number']);
            
            $result = $controller->deleteAction($project_id, $action_type, $action_number);
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al eliminar acción']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Acción no válida']);
            break;
    }
    exit();
}
?>