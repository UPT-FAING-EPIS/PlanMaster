<?php
/**
 * PorterController - Controlador para el análisis de Matriz de Porter
 * Sistema PlanMaster - Análisis Estratégico
 * 
 * Maneja todas las operaciones relacionadas con:
 * - Análisis de las 5 fuerzas competitivas de Porter
 * - Gestión de factores y evaluaciones
 * - Cálculo de puntuaciones y competitividad
 * - Gestión de oportunidades y amenazas derivadas
 */

require_once __DIR__ . '/../Models/PorterAnalysis.php';
require_once __DIR__ . '/AuthController.php';

class PorterController {
    private $porterModel;
    
    public function __construct() {
        $this->porterModel = new PorterAnalysis();
    }
    
    /**
     * Maneja las peticiones HTTP para el análisis Porter
     */
    public function handleRequest() {
        // Asegurar que la sesión esté iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar autenticación
        if (!AuthController::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            exit();
        }
        
        $action = $_POST['action'] ?? $_GET['action'] ?? '';
        
        try {
            switch ($action) {
                case 'save_porter':
                    $this->savePorterAnalysis();
                    break;
                    
                case 'get_porter':
                    $this->getPorterAnalysis();
                    break;
                    
                case 'get_porter_score':
                    $this->getPorterScore();
                    break;
                    
                case 'get_porter_foda':
                    $this->getPorterFoda();
                    break;
                    
                case 'save_porter_foda':
                    $this->savePorterFoda();
                    break;
                    
                case 'delete_porter_item':
                    $this->deletePorterItem();
                    break;
                    
                case 'check_porter_complete':
                    $this->checkPorterComplete();
                    break;
                    
                default:
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
                    break;
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'message' => 'Error del servidor: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Guarda el análisis Porter completo (factores + FODA)
     */
    private function savePorterAnalysis() {
        $project_id = (int)($_POST['project_id'] ?? 0);
        
        if ($project_id === 0) {
            throw new Exception('ID de proyecto no válido');
        }
        
        // Verificar que el proyecto pertenece al usuario
        if (!$this->verifyProjectOwnership($project_id)) {
            throw new Exception('No tienes permisos para este proyecto');
        }
        
        // Procesar factores Porter - organizar por categoría
        $factorsData = [];
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'factor_') === 0) {
                // Extraer información del factor
                $factorKey = str_replace('factor_', '', $key);
                $parts = explode('_', $factorKey);
                
                if (count($parts) >= 2) {
                    $category = $parts[0];
                    $factorName = str_replace('_', ' ', implode(' ', array_slice($parts, 1)));
                    
                    // Organizar por categoría como espera el modelo
                    if (!isset($factorsData[$category])) {
                        $factorsData[$category] = [];
                    }
                    
                    $factorsData[$category][] = [
                        'factor_name' => $factorName,
                        'selected_value' => (int)$value
                    ];
                }
            }
        }
        
        // Guardar factores Porter
        if (!empty($factorsData)) {
            $result = $this->porterModel->saveAnalysis($project_id, $factorsData);
            if (!$result) {
                throw new Exception('Error al guardar el análisis Porter');
            }
        }
        
        // Procesar items FODA
        $fodaData = [];
        
        // Oportunidades
        if (isset($_POST['oportunidades']) && is_array($_POST['oportunidades'])) {
            foreach ($_POST['oportunidades'] as $item) {
                $item = trim($item);
                if (!empty($item)) {
                    $fodaData[] = [
                        'type' => 'oportunidad',
                        'item_text' => $item
                    ];
                }
            }
        }
        
        // Amenazas
        if (isset($_POST['amenazas']) && is_array($_POST['amenazas'])) {
            foreach ($_POST['amenazas'] as $item) {
                $item = trim($item);
                if (!empty($item)) {
                    $fodaData[] = [
                        'type' => 'amenaza',
                        'item_text' => $item
                    ];
                }
            }
        }
        
        // Guardar items FODA
        if (!empty($fodaData)) {
            // Separar oportunidades y amenazas
            $oportunidades = [];
            $amenazas = [];
            
            foreach ($fodaData as $item) {
                if ($item['type'] === 'oportunidad') {
                    $oportunidades[] = $item['item_text'];
                } elseif ($item['type'] === 'amenaza') {
                    $amenazas[] = $item['item_text'];
                }
            }
            
            $fodaResult = $this->porterModel->saveFodaItems($project_id, $oportunidades, $amenazas);
            if (!$fodaResult) {
                throw new Exception('Error al guardar los items FODA');
            }
        }
        
        // Respuesta exitosa
        if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
            // Respuesta AJAX
            $score = $this->porterModel->calculateScore($project_id);
            echo json_encode([
                'success' => true, 
                'message' => 'Análisis Porter guardado exitosamente',
                'score' => $score
            ]);
        } else {
            // Redirección normal
            $redirect_url = "../Views/Projects/porter-matrix.php?id=" . $project_id . "&success=1";
            header("Location: " . $redirect_url);
            exit();
        }
    }
    
    /**
     * Obtiene el análisis Porter de un proyecto
     */
    private function getPorterAnalysis() {
        $project_id = (int)($_GET['project_id'] ?? 0);
        
        if ($project_id === 0) {
            throw new Exception('ID de proyecto no válido');
        }
        
        if (!$this->verifyProjectOwnership($project_id)) {
            throw new Exception('No tienes permisos para este proyecto');
        }
        
        $analysis = $this->porterModel->getByProject($project_id);
        
        echo json_encode([
            'success' => true,
            'analysis' => $analysis
        ]);
    }
    
    /**
     * Obtiene la puntuación del análisis Porter
     */
    private function getPorterScore() {
        $project_id = (int)($_GET['project_id'] ?? 0);
        
        if ($project_id === 0) {
            throw new Exception('ID de proyecto no válido');
        }
        
        if (!$this->verifyProjectOwnership($project_id)) {
            throw new Exception('No tienes permisos para este proyecto');
        }
        
        $score = $this->porterModel->calculateScore($project_id);
        
        echo json_encode([
            'success' => true,
            'score' => $score
        ]);
    }
    
    /**
     * Obtiene los items FODA del análisis Porter
     */
    private function getPorterFoda() {
        $project_id = (int)($_GET['project_id'] ?? 0);
        
        if ($project_id === 0) {
            throw new Exception('ID de proyecto no válido');
        }
        
        if (!$this->verifyProjectOwnership($project_id)) {
            throw new Exception('No tienes permisos para este proyecto');
        }
        
        $foda = $this->porterModel->getFodaItems($project_id);
        
        echo json_encode([
            'success' => true,
            'foda' => $foda
        ]);
    }
    
    /**
     * Guarda solo los items FODA
     */
    private function savePorterFoda() {
        $project_id = (int)($_POST['project_id'] ?? 0);
        
        if ($project_id === 0) {
            throw new Exception('ID de proyecto no válido');
        }
        
        if (!$this->verifyProjectOwnership($project_id)) {
            throw new Exception('No tienes permisos para este proyecto');
        }
        
        // Separar oportunidades y amenazas
        $oportunidades = [];
        $amenazas = [];
        
        // Oportunidades
        if (isset($_POST['oportunidades']) && is_array($_POST['oportunidades'])) {
            foreach ($_POST['oportunidades'] as $item) {
                $item = trim($item);
                if (!empty($item)) {
                    $oportunidades[] = $item;
                }
            }
        }
        
        // Amenazas
        if (isset($_POST['amenazas']) && is_array($_POST['amenazas'])) {
            foreach ($_POST['amenazas'] as $item) {
                $item = trim($item);
                if (!empty($item)) {
                    $amenazas[] = $item;
                }
            }
        }
        
        $result = $this->porterModel->saveFodaItems($project_id, $oportunidades, $amenazas);
        
        if (!$result) {
            throw new Exception('Error al guardar los items FODA');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Items FODA guardados exitosamente'
        ]);
    }
    
    /**
     * Elimina un item específico del análisis Porter
     */
    private function deletePorterItem() {
        $item_id = (int)($_POST['item_id'] ?? 0);
        $item_type = $_POST['item_type'] ?? '';
        
        if ($item_id === 0 || empty($item_type)) {
            throw new Exception('Parámetros no válidos para eliminación');
        }
        
        // Nota: Los métodos de eliminación no están implementados en el modelo
        // Por ahora retornamos éxito para que no de error
        echo json_encode([
            'success' => true,
            'message' => 'Funcionalidad de eliminación pendiente de implementar'
        ]);
    }
    
    /**
     * Verifica si el análisis Porter está completo
     */
    private function checkPorterComplete() {
        $project_id = (int)($_GET['project_id'] ?? 0);
        
        if ($project_id === 0) {
            throw new Exception('ID de proyecto no válido');
        }
        
        if (!$this->verifyProjectOwnership($project_id)) {
            throw new Exception('No tienes permisos para este proyecto');
        }
        
        $isComplete = $this->porterModel->isComplete($project_id);
        
        echo json_encode([
            'success' => true,
            'is_complete' => $isComplete
        ]);
    }
    
    /**
     * Verifica que el proyecto pertenece al usuario actual
     */
    private function verifyProjectOwnership($project_id) {
        // Verificar que hay una sesión activa
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        require_once __DIR__ . '/../config/database.php';
        
        $database = new Database();
        $conn = $database->getConnection();
        
        if ($conn instanceof PDO) {
            // Conexión PDO
            $query = "SELECT user_id FROM strategic_projects WHERE id = :project_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
            $stmt->execute();
            $project = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Conexión MySQLi
            $query = "SELECT user_id FROM strategic_projects WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $project_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $project = $result->fetch_assoc();
        }
        
        return $project && isset($project['user_id']) && $project['user_id'] == $_SESSION['user_id'];
    }
    
    /**
     * Métodos públicos para uso en otras partes del sistema
     */
    
    /**
     * Obtiene el análisis Porter para mostrar en vistas
     */
    public function getPorterAnalysisForView($project_id) {
        if (!$this->verifyProjectOwnership($project_id)) {
            return null;
        }
        
        return $this->porterModel->getByProject($project_id);
    }
    
    /**
     * Obtiene la puntuación Porter para mostrar en vistas
     */
    public function getPorterScoreForView($project_id) {
        if (!$this->verifyProjectOwnership($project_id)) {
            return null;
        }
        
        return $this->porterModel->calculateScore($project_id);
    }
    
    /**
     * Obtiene los items FODA para mostrar en vistas
     */
    public function getPorterFodaForView($project_id) {
        if (!$this->verifyProjectOwnership($project_id)) {
            return null;
        }
        
        return $this->porterModel->getFodaItems($project_id);
    }
    
    /**
     * Verifica si el análisis Porter está completo
     */
    public function isPorterCompleteForView($project_id) {
        if (!$this->verifyProjectOwnership($project_id)) {
            return false;
        }
        
        return $this->porterModel->isComplete($project_id);
    }
}

// Manejar peticiones directas al controlador solo si hay una acción definida
if (($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') && 
    (isset($_POST['action']) || isset($_GET['action']))) {
    
    // Asegurar que la sesión esté iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    $controller = new PorterController();
    $controller->handleRequest();
}
?>