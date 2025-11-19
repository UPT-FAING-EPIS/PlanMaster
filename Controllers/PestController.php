<?php
require_once __DIR__ . '/../Models/PestAnalysis.php';
require_once __DIR__ . '/../Models/Project.php';
require_once __DIR__ . '/AuthController.php';

class PestController {
    private $pestModel;

    public function __construct() {
        $this->pestModel = new PestAnalysis();
    }

    // Manejar peticiones simples
    public function handleRequest() {
        if (session_status() == PHP_SESSION_NONE) session_start();

        $action = $_POST['action'] ?? $_GET['action'] ?? '';
        try {
            switch ($action) {
                case 'save_pest':
                    $this->savePestAnalysis();
                    break;
                case 'save_pest_foda':
                    $this->savePestFoda();
                    break;
                default:
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Guardar respuestas PEST
    private function savePestAnalysis() {
        try {
            if (!AuthController::isLoggedIn()) {
                header("Location: ../Views/Auth/login.php");
                exit();
            }

            if (!isset($_POST['project_id']) || !isset($_POST['responses'])) {
                throw new Exception('Datos incompletos');
            }

            $project_id = (int)$_POST['project_id'];
            $responses = $_POST['responses'];

            // verificar propiedad del proyecto
            $projectModel = new Project();
            $project = $projectModel->getById($project_id);
            $currentUser = AuthController::getCurrentUser();
            if (!$project || !$currentUser || $project['user_id'] != $currentUser['id']) {
                throw new Exception('Proyecto no encontrado o sin permisos');
            }

            // validar rangos 0-4
            foreach ($responses as $q => $r) {
                $r = (int)$r;
                if ($r < 0 || $r > 4) {
                    throw new Exception('Valor de respuesta fuera de rango (0-4)');
                }
            }

            if ($this->pestModel->saveResponses($project_id, $responses)) {
                header("Location: ../Views/Projects/pest-analysis.php?id=" . $project_id . "&success=1");
                exit();
            } else {
                throw new Exception('Error al guardar respuestas');
            }

        } catch (Exception $e) {
            $pid = isset($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
            header("Location: ../Views/Projects/pest-analysis.php?id=" . $pid . "&error=" . urlencode($e->getMessage()));
            exit();
        }
    }

    // Guardar oportunidades y amenazas del PEST
    private function savePestFoda() {
        try {
            if (!AuthController::isLoggedIn()) {
                header("Location: ../Views/Auth/login.php");
                exit();
            }

            // Usar el ProjectController para mantener consistencia
            require_once __DIR__ . '/ProjectController.php';
            $projectController = new ProjectController();
            $projectController->saveFodaAnalysis();

        } catch (Exception $e) {
            $pid = isset($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
            header("Location: ../Views/Projects/pest-analysis.php?id=" . $pid . "&error=" . urlencode($e->getMessage()));
            exit();
        }
    }
}

// Manejar llamadas directas
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    if (!empty($action)) {
        $ctrl = new PestController();
        $ctrl->handleRequest();
    }
}

?>
