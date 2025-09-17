<?php

class Router
{
    public function handleRequest()
    {
        session_start();

        // Detectar ruta o asignar 'auth' como ruta por defecto
        $uri = isset($_GET['url']) ? trim($_GET['url'], '/') : 'auth/index';

        // Dividir la URI
        $segments = explode('/', $uri);
        $controllerName = $segments[0];
        $action         = $segments[1] ?? 'index';
        $subaction      = $segments[2] ?? null;


        // Manejadores especiales (ej. Google login)
        if ($this->handleSpecialRoutes()) {
            return;
        }
        
        // Ruta de API
        if ($segments[0] === 'api') {
            $this->handleApiRequest($segments);
            return;
        }
        

        // Verificar sesión y rol
        if ($controllerName != 'auth') {

            $rol = $_SESSION['usuario_rol'] ?? null;
            if (!$rol) {
                header('Location: /');
                exit;
            }

            $namespace = $rol;

            // Intentar primero en la raíz (por si hay controladores comunes)
            $rootControllerFile = "../app/controllers/" . ucfirst($controllerName) . "Controller.php";
            $controllerClass = ucfirst($controllerName) . "Controller";

            if (file_exists($rootControllerFile)) {
                require_once $rootControllerFile;
                $controller = new $controllerClass();

                // Verificar si el método de la acción existe
                if (method_exists($controller, $action)) {
                    $controller->$action();
                } else {
                    header("Location: /");
                    exit;
                }
            }

            // Construir ruta del controlador
            $controllerFile = "../app/controllers/$namespace/" . ucfirst($controllerName) . "Controller.php";
            $controllerClass = ucfirst($controllerName) . "Controller";

            if (file_exists($controllerFile)) {
                require_once $controllerFile;

                $controller = new $controllerClass();

                if (is_numeric($action) && $subaction && method_exists($controller, $subaction)) {
                    $controller->$subaction($action); // /clientes/3/editar
                } elseif (is_numeric($action) && !$subaction) {
                    $controller->ver($action); // /clientes/3
                } elseif (method_exists($controller, $action) && $subaction) {
                    $controller->$action($subaction);// /clientes/editar/3
                } elseif (method_exists($controller, $action)) {
                    $controller->$action(); // /clientes/crear
                } else {
                    http_response_code(404);
                    echo "Acción no encontrada.";
                }
                return;
            }
            header('Location: /');
        } else {
            require_once '../app/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->$action();
            return;
        }
    }


    private function handleSpecialRoutes()
    {
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
        switch ($requestPath) {
            case '/google-login':
                require '../app/handlers/google/login.php';
                return true;
            case '/google-login/callback':
                require '../app/handlers/google/callback.php';
                return true;
        }
    
        return false;
    }

    private function handleApiRequest($segments)
    {
        $apiControllerName = ucfirst($segments[1]) . 'ApiController';
        $apiAction = $segments[2] ?? 'index';
        $apiFile = "../app/api/$apiControllerName.php";
    
        if (file_exists($apiFile)) {
            require_once $apiFile;
            $controller = new $apiControllerName();
    
            if (method_exists($controller, $apiAction)) {
                $controller->$apiAction();
                return;
            }
        }
    
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Ruta de API no encontrada']);
    }

    private function handleExportRequest($segments)
    {
        // Verificar sesión y rol de admin
        $rol = $_SESSION['usuario_rol'] ?? null;
        if ($rol !== 'admin') {
            header('Location: /');
            exit;
        }
        
        $exportAction = $segments[1] ?? null; // clientes, inversiones, etc.
        $formato = $segments[2] ?? 'excel'; // excel o csv
        
        if ($exportAction) {
            require_once '../app/controllers/admin/ExportController.php';
            $controller = new ExportController();
            
            if (method_exists($controller, $exportAction)) {
                $controller->$exportAction($formato);
                return;
            }
        }
        
        http_response_code(404);
        echo "Ruta de exportación no encontrada.";
    }

    private function handleControllerRequest($segments) {}

}