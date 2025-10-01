<?php
// session_start(); // Removido para evitar conflicto - la sesión se maneja en AuthController
require_once __DIR__ . '/../Models/Project.php';
require_once __DIR__ . '/../Models/Mission.php';
require_once __DIR__ . '/../Models/Vision.php';
require_once __DIR__ . '/../Models/Values.php';
require_once __DIR__ . '/../Models/Objectives.php';
require_once __DIR__ . '/../Models/FodaAnalysis.php';
require_once __DIR__ . '/../Models/ValueChain.php';
require_once __DIR__ . '/../Models/BCGAnalysis.php';
require_once __DIR__ . '/../Controllers/AuthController.php';

class ProjectController {
    private $project;
    private $mission;
    private $vision;
    private $values;
    private $objectives;
    private $fodaAnalysis;
    private $valueChain;
    private $bcgAnalysis;
    
    public function __construct() {
        $this->project = new Project();
        $this->mission = new Mission();
        $this->vision = new Vision();
        $this->values = new Values();
        $this->objectives = new Objectives();
        $this->fodaAnalysis = new FodaAnalysis();
        $this->valueChain = new ValueChain();
        $this->bcgAnalysis = new BCGAnalysis();
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
            $this->project->progress_percentage = 0.00; // Asegurar que siempre sea 0.00 inicialmente
            
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
            'objectives' => count($this->objectives->getStrategicObjectivesByProjectId($project_id)) > 0 ? true : false,
            'foda_analysis' => $this->isFodaComplete($project_id) ? true : false,
            'value_chain' => $this->isValueChainComplete($project_id) ? true : false,
            'bcg_analysis' => $this->isBCGComplete($project_id) ? true : false
        ];
        
        $completed = array_sum($progress);
        $total = count($progress);
        $percentage = $total > 0 ? ($completed / $total) * 100 : 0;
        
        return [
            'progress' => $progress,
            'percentage' => (float)$percentage,
            'completed' => (int)$completed,
            'total' => (int)$total,
            'sections' => $progress // Para compatibilidad
        ];
    }
    
    // Guardar análisis FODA
    public function saveFodaAnalysis() {
        AuthController::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $project_id = intval($_POST['project_id']);
            $save_and_exit = isset($_POST['save_and_exit']);
            
            // Verificar que el proyecto pertenece al usuario
            $project = $this->getProject($project_id);
            
            try {
                // Limpiar análisis FODA anterior
                $this->fodaAnalysis->deleteByProject($project_id);
                
                // Procesar cada tipo de análisis FODA
                $types = ['oportunidad', 'amenaza', 'fortaleza', 'debilidad'];
                
                foreach ($types as $type) {
                    $plural_type = $type . 'es'; // oportunidades, amenazas, etc.
                    if ($type == 'oportunidad') $plural_type = 'oportunidades';
                    if ($type == 'amenaza') $plural_type = 'amenazas';
                    if ($type == 'fortaleza') $plural_type = 'fortalezas';
                    if ($type == 'debilidad') $plural_type = 'debilidades';
                    
                    if (isset($_POST[$plural_type]) && is_array($_POST[$plural_type])) {
                        $order = 1;
                        foreach ($_POST[$plural_type] as $item_text) {
                            $item_text = trim($item_text);
                            if (!empty($item_text)) {
                                $this->fodaAnalysis->project_id = $project_id;
                                $this->fodaAnalysis->type = $type;
                                $this->fodaAnalysis->item_text = $item_text;
                                $this->fodaAnalysis->item_order = $order++;
                                
                                if (!$this->fodaAnalysis->create()) {
                                    throw new Exception("Error al guardar el análisis FODA");
                                }
                            }
                        }
                    }
                }
                
                $_SESSION['success'] = "Análisis FODA guardado exitosamente";
                
                // Redirigir según la acción
                if ($save_and_exit) {
                    header("Location: ../Views/Users/projects.php");
                } else {
                    header("Location: ../Views/Projects/foda-analysis.php?project_id=" . $project_id);
                }
                exit();
                
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header("Location: ../Views/Projects/foda-analysis.php?project_id=" . $project_id);
                exit();
            }
        }
    }
    
    // Obtener análisis FODA de un proyecto
    public function getFodaAnalysis($project_id) {
        $allItems = $this->fodaAnalysis->getByProject($project_id);
        
        $foda = array(
            'oportunidades' => array(),
            'amenazas' => array(),
            'fortalezas' => array(),
            'debilidades' => array()
        );
        
        foreach ($allItems as $item) {
            switch ($item['type']) {
                case 'oportunidad':
                    $foda['oportunidades'][] = $item;
                    break;
                case 'amenaza':
                    $foda['amenazas'][] = $item;
                    break;
                case 'fortaleza':
                    $foda['fortalezas'][] = $item;
                    break;
                case 'debilidad':
                    $foda['debilidades'][] = $item;
                    break;
            }
        }
        
        return $foda;
    }
    
    // Verificar si el análisis FODA está completo
    public function isFodaComplete($project_id) {
        $status = $this->fodaAnalysis->getCompletionStatus($project_id);
        return $status['is_complete'];
    }
    
    // Guardar respuestas de Cadena de Valor
    public function saveValueChain() {
        try {
            // Verificar que el usuario esté logueado
            if (!AuthController::isLoggedIn()) {
                header("Location: " . getBaseUrl() . "/Views/Auth/login.php");
                exit();
            }
            
            // Validar datos
            if (!isset($_POST['project_id']) || !isset($_POST['responses'])) {
                throw new Exception("Datos incompletos");
            }
            
            $project_id = (int)$_POST['project_id'];
            $responses = $_POST['responses'];
            
            // Validar que el proyecto pertenezca al usuario
            $user_id = $_SESSION['user_id'];
            $project = $this->project->getById($project_id);
            
            if (!$project || $project['user_id'] != $user_id) {
                throw new Exception("Proyecto no encontrado o sin permisos");
            }
            
            // Validar que todas las respuestas están en el rango 0-4
            foreach ($responses as $question_number => $rating) {
                $rating = (int)$rating;
                if ($rating < 0 || $rating > 4) {
                    throw new Exception("Rating fuera del rango permitido (0-4)");
                }
            }
            
            // Guardar las respuestas
            if ($this->valueChain->saveResponses($project_id, $responses)) {
                // Redirigir con éxito
                header("Location: " . getBaseUrl() . "/Views/Projects/value-chain.php?project_id=" . $project_id . "&success=1");
                exit();
            } else {
                throw new Exception("Error al guardar las respuestas");
            }
            
        } catch (Exception $e) {
            // Redirigir con error
            $project_id = isset($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
            header("Location: " . getBaseUrl() . "/Views/Projects/value-chain.php?project_id=" . $project_id . "&error=" . urlencode($e->getMessage()));
            exit();
        }
    }
    
    // Obtener respuestas de Cadena de Valor
    public function getValueChain($project_id) {
        return $this->valueChain->getByProject($project_id);
    }
    
    // Obtener cálculo de mejora potencial
    public function getValueChainImprovement($project_id) {
        return $this->valueChain->calculatePotentialImprovement($project_id);
    }
    
    // Verificar si la Cadena de Valor está completa
    public function isValueChainComplete($project_id) {
        return $this->valueChain->isComplete($project_id);
    }
    
    // Obtener estadísticas por categorías
    public function getValueChainStats($project_id) {
        return $this->valueChain->getCategoryStats($project_id);
    }
    
    // ===== MÉTODOS BCG ANALYSIS =====
    
    // Guardar análisis BCG
    public function saveBCGAnalysis() {
        try {
            // Verificar que el usuario esté logueado
            if (!AuthController::isLoggedIn()) {
                header("Location: " . getBaseUrl() . "/Views/Auth/login.php");
                exit();
            }
            
            // Validar datos básicos
            if (!isset($_POST['project_id']) || !isset($_POST['products'])) {
                throw new Exception("Datos incompletos");
            }
            
            $project_id = (int)$_POST['project_id'];
            $products = $_POST['products'];
            
            // Validar que el proyecto pertenezca al usuario
            $user_id = $_SESSION['user_id'];
            $project = $this->project->getById($project_id);
            
            if (!$project || $project['user_id'] != $user_id) {
                throw new Exception("Proyecto no encontrado o sin permisos");
            }
            
            // Validar productos
            if (!is_array($products) || count($products) < 1) {
                throw new Exception("Debe incluir al menos un producto");
            }
            
            // Validar cada producto
            foreach ($products as $index => $product) {
                if (empty(trim($product['name'] ?? ''))) {
                    throw new Exception("El nombre del producto " . ($index + 1) . " es obligatorio");
                }
                
                if (!is_numeric($product['sales_forecast'] ?? 0) || floatval($product['sales_forecast']) <= 0) {
                    throw new Exception("El pronóstico de ventas del producto " . ($index + 1) . " debe ser mayor a 0");
                }
                
                if (!is_numeric($product['tcm_rate'] ?? '') || floatval($product['tcm_rate']) < 0) {
                    throw new Exception("La TCM del producto " . ($index + 1) . " debe ser mayor o igual a 0");
                }
            }
            
            // Guardar productos
            if ($this->bcgAnalysis->saveProducts($project_id, $products)) {
                // Procesar datos adicionales si existen (competidores, evolución mercado)
                if (isset($_POST['market_evolution'])) {
                    foreach ($_POST['market_evolution'] as $product_index => $market_data) {
                        if (is_array($market_data) && count($market_data) > 0) {
                            // Obtener el ID del producto recién guardado
                            $saved_products = $this->bcgAnalysis->getProductsByProject($project_id);
                            if (isset($saved_products[$product_index])) {
                                $product_id = $saved_products[$product_index]['id'];
                                $this->bcgAnalysis->saveMarketEvolution($product_id, $market_data);
                            }
                        }
                    }
                }
                
                if (isset($_POST['competitors'])) {
                    foreach ($_POST['competitors'] as $product_index => $competitors_data) {
                        if (is_array($competitors_data) && count($competitors_data) > 0) {
                            // Obtener el ID del producto recién guardado
                            $saved_products = $this->bcgAnalysis->getProductsByProject($project_id);
                            if (isset($saved_products[$product_index])) {
                                $product_id = $saved_products[$product_index]['id'];
                                $this->bcgAnalysis->saveCompetitors($product_id, $competitors_data);
                            }
                        }
                    }
                }
                
                // Redirigir con éxito
                $_SESSION['success'] = "Análisis BCG guardado correctamente";
                header("Location: " . getBaseUrl() . "/Views/Projects/bcg-analysis.php?id=" . $project_id);
                exit();
            } else {
                throw new Exception("Error al guardar el análisis BCG");
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $project_id = $_POST['project_id'] ?? '';
            if (!empty($project_id)) {
                header("Location: " . getBaseUrl() . "/Views/Projects/bcg-analysis.php?id=" . $project_id);
            } else {
                header("Location: " . getBaseUrl() . "/Views/Users/dashboard.php");
            }
            exit();
        }
    }
    
    // Obtener datos BCG por proyecto
    public function getBCGAnalysis($project_id) {
        try {
            return $this->bcgAnalysis->getProductsByProject($project_id);
        } catch (Exception $e) {
            error_log("BCG Analysis error: " . $e->getMessage());
            return []; // Retornar array vacío si hay error
        }
    }
    
    // Obtener matriz BCG calculada
    public function getBCGMatrix($project_id) {
        try {
            return $this->bcgAnalysis->calculateBCGMatrix($project_id);
        } catch (Exception $e) {
            error_log("BCG Matrix error: " . $e->getMessage());
            return []; // Retornar array vacío si hay error
        }
    }
    
    // Verificar si el análisis BCG está completo
    public function isBCGComplete($project_id) {
        try {
            return $this->bcgAnalysis->isComplete($project_id);
        } catch (Exception $e) {
            error_log("BCG Complete error: " . $e->getMessage());
            return false; // Retornar false si hay error
        }
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
        case 'save_foda':
            $controller->saveFodaAnalysis();
            break;
        case 'save_value_chain':
            $controller->saveValueChain();
            break;
        default:
            header("Location: ../Views/Users/dashboard.php");
            break;
    }
}

// Manejo de rutas
if (isset($_GET['action'])) {
    // Iniciar sesión si no está iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    $controller = new ProjectController();
    $action = $_GET['action'];
    
    switch ($action) {
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
        case 'save_foda':
            $controller->saveFodaAnalysis();
            break;
        case 'save_value_chain':
            $controller->saveValueChain();
            break;
        case 'save_bcg_analysis':
            $controller->saveBCGAnalysis();
            break;
        default:
            header("Location: ../Views/Users/dashboard.php");
            break;
    }
}
?>