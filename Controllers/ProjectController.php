<?php
// session_start(); // Removido para evitar conflicto - la sesión se maneja en AuthController
require_once __DIR__ . '/../Models/Project.php';
require_once __DIR__ . '/../Models/Mission.php';
require_once __DIR__ . '/../Models/Vision.php';
require_once __DIR__ . '/../Models/Values.php';
require_once __DIR__ . '/../Models/Objectives.php';
require_once __DIR__ . '/../Controllers/AuthController.php';

class ProjectController {
    private $project;
    private $mission;
    private $vision;
    private $values;
    private $objectives;
    
    public function __construct() {
        $this->project = new Project();
        $this->mission = new Mission();
        $this->vision = new Vision();
        $this->values = new Values();
        $this->objectives = new Objectives();
    }
    
    // Crear nuevo proyecto
    public function createProject() {
        // Verificar que el usuario esté logueado
        AuthController::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $project_name = trim($_POST['project_name']);
            $company_name = trim($_POST['company_name']);
            $user_id = $_SESSION['user_id'];
            
            // Validaciones
            if (empty($project_name) || empty($company_name)) {
                $_SESSION['error'] = "Por favor completa todos los campos";
                header("Location: ../Views/Users/dashboard.php");
                exit();
            }
            
            // Crear proyecto
            $this->project->user_id = $user_id;
            $this->project->project_name = $project_name;
            $this->project->company_name = $company_name;
            $this->project->status = 'in_progress';
            
            if ($this->project->create()) {
                $_SESSION['success'] = "Proyecto creado exitosamente";
                $_SESSION['current_project_id'] = $this->project->id;
                header("Location: ../Views/Projects/project.php?id=" . $this->project->id);
                exit();
            } else {
                $_SESSION['error'] = "Error al crear el proyecto";
                header("Location: ../Views/Users/dashboard.php");
                exit();
            }
        }
    }
    
    // Obtener proyecto por ID
    public function getProject($project_id) {
        AuthController::requireLogin();
        
        $project = $this->project->getById($project_id);
        
        if (!$project || $project['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = "Proyecto no encontrado o no tienes permisos para verlo";
            header("Location: ../Views/Users/dashboard.php");
            exit();
        }
        
        return $project;
    }
    
    // Obtener todos los proyectos del usuario
    public function getUserProjects() {
        AuthController::requireLogin();
        
        return $this->project->getByUserId($_SESSION['user_id']);
    }
    
    // Guardar misión
    public function saveMission() {
        AuthController::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $project_id = intval($_POST['project_id']);
            $mission_text = trim($_POST['mission_text']);
            $save_and_exit = isset($_POST['save_and_exit']);
            
            // Verificar que el proyecto pertenece al usuario
            $project = $this->getProject($project_id);
            
            if (empty($mission_text)) {
                $_SESSION['error'] = "La misión no puede estar vacía";
                $redirect_url = $save_and_exit ? 
                    "../Views/Projects/sections/mission.php?project_id=" . $project_id :
                    "../Views/Projects/mission.php?id=" . $project_id;
                header("Location: " . $redirect_url);
                exit();
            }
            
            $this->mission->project_id = $project_id;
            $this->mission->mission_text = $mission_text;
            
            if ($this->mission->save()) {
                $_SESSION['success'] = "Misión guardada exitosamente";
                
                if ($save_and_exit) {
                    header("Location: ../Views/Users/dashboard.php");
                } else {
                    header("Location: ../Views/Projects/vision.php?id=" . $project_id);
                }
                exit();
            } else {
                $_SESSION['error'] = "Error al guardar la misión";
                $redirect_url = $save_and_exit ? 
                    "../Views/Projects/sections/mission.php?project_id=" . $project_id :
                    "../Views/Projects/mission.php?id=" . $project_id;
                header("Location: " . $redirect_url);
                exit();
            }
        }
    }
    
    // Guardar visión
    public function saveVision() {
        AuthController::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $project_id = intval($_POST['project_id']);
            $vision_text = trim($_POST['vision_text']);
            
            // Verificar que el proyecto pertenece al usuario
            $project = $this->getProject($project_id);
            
            if (empty($vision_text)) {
                $_SESSION['error'] = "La visión no puede estar vacía";
                header("Location: ../Views/Projects/vision.php?id=" . $project_id);
                exit();
            }
            
            $this->vision->project_id = $project_id;
            $this->vision->vision_text = $vision_text;
            
            if ($this->vision->save()) {
                $_SESSION['success'] = "Visión guardada exitosamente";
                header("Location: ../Views/Projects/values.php?id=" . $project_id);
                exit();
            } else {
                $_SESSION['error'] = "Error al guardar la visión";
                header("Location: ../Views/Projects/vision.php?id=" . $project_id);
                exit();
            }
        }
    }
    
    // Guardar valores
    public function saveValues() {
        AuthController::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $project_id = intval($_POST['project_id']);
            $values = $_POST['values'] ?? [];
            
            // Verificar que el proyecto pertenece al usuario
            $project = $this->getProject($project_id);
            
            // Filtrar valores vacíos
            $values = array_filter($values, function($value) {
                return !empty(trim($value));
            });
            
            if (empty($values)) {
                $_SESSION['error'] = "Debe ingresar al menos un valor";
                header("Location: ../Views/Projects/values.php?id=" . $project_id);
                exit();
            }
            
            if (count($values) > 10) {
                $_SESSION['error'] = "No puede ingresar más de 10 valores";
                header("Location: ../Views/Projects/values.php?id=" . $project_id);
                exit();
            }
            
            if ($this->values->saveProjectValues($project_id, $values)) {
                $_SESSION['success'] = "Valores guardados exitosamente";
                header("Location: ../Views/Projects/objectives.php?id=" . $project_id);
                exit();
            } else {
                $_SESSION['error'] = "Error al guardar los valores";
                header("Location: ../Views/Projects/values.php?id=" . $project_id);
                exit();
            }
        }
    }
    
    // Guardar objetivos
    public function saveObjectives() {
        AuthController::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $project_id = intval($_POST['project_id']);
            $strategic_objectives = $_POST['strategic_objectives'] ?? [];
            
            // Verificar que el proyecto pertenece al usuario
            $project = $this->getProject($project_id);
            
            // Validar objetivos estratégicos
            if (empty($strategic_objectives) || count($strategic_objectives) > 3) {
                $_SESSION['error'] = "Debe ingresar entre 1 y 3 objetivos estratégicos";
                header("Location: ../Views/Projects/objectives.php?id=" . $project_id);
                exit();
            }
            
            // Validar que cada objetivo tenga título y exactamente 2 objetivos específicos
            foreach ($strategic_objectives as $index => $strategic) {
                if (empty(trim($strategic['title']))) {
                    $_SESSION['error'] = "Todos los objetivos estratégicos deben tener título";
                    header("Location: ../Views/Projects/objectives.php?id=" . $project_id);
                    exit();
                }
                
                $specific_objectives = $strategic['specific_objectives'] ?? [];
                if (count($specific_objectives) != 2) {
                    $_SESSION['error'] = "Cada objetivo estratégico debe tener exactamente 2 objetivos específicos";
                    header("Location: ../Views/Projects/objectives.php?id=" . $project_id);
                    exit();
                }
                
                foreach ($specific_objectives as $specific) {
                    if (empty(trim($specific['title']))) {
                        $_SESSION['error'] = "Todos los objetivos específicos deben tener título";
                        header("Location: ../Views/Projects/objectives.php?id=" . $project_id);
                        exit();
                    }
                }
            }
            
            if ($this->objectives->saveProjectObjectives($project_id, $strategic_objectives)) {
                $_SESSION['success'] = "Objetivos guardados exitosamente";
                header("Location: ../Views/Projects/project.php?id=" . $project_id);
                exit();
            } else {
                $_SESSION['error'] = "Error al guardar los objetivos";
                header("Location: ../Views/Projects/objectives.php?id=" . $project_id);
                exit();
            }
        }
    }
    
    // Obtener progreso del proyecto
    public function getProjectProgress($project_id) {
        $progress = [
            'mission' => $this->mission->getByProjectId($project_id) ? true : false,
            'vision' => $this->vision->getByProjectId($project_id) ? true : false,
            'values' => count($this->values->getByProjectId($project_id)) > 0 ? true : false,
            'objectives' => count($this->objectives->getStrategicObjectivesByProjectId($project_id)) > 0 ? true : false
        ];
        
        $completed = array_sum($progress);
        $total = count($progress);
        $percentage = ($completed / $total) * 100;
        
        return [
            'progress' => $progress,
            'percentage' => $percentage,
            'completed' => $completed,
            'total' => $total
        ];
    }
}

// Manejo de rutas
if (isset($_GET['action'])) {
    // Iniciar sesión si no está iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    $controller = new ProjectController();
    
    switch ($_GET['action']) {
        case 'create':
            $controller->createProject();
            break;
        case 'save_mission':
            $controller->saveMission();
            break;
        case 'save_vision':
            $controller->saveVision();
            break;
        case 'save_values':
            $controller->saveValues();
            break;
        case 'save_objectives':
            $controller->saveObjectives();
            break;
        default:
            header("Location: ../Views/Users/dashboard.php");
            break;
    }
}
?>