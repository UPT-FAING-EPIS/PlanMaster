<?php
// session_start(); // Removido para evitar error de sesión duplicada
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../config/url_config.php';

class AuthController {
    private $user;
    
    public function __construct() {
        $this->user = new User();
    }
    
    // Procesar login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $remember_me = isset($_POST['remember_me']);
            
            // Validaciones básicas
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = "Por favor completa todos los campos";
                header("Location: ../../Views/Auth/login.php");
                exit();
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Por favor ingresa un email válido";
                header("Location: ../../Views/Auth/login.php");
                exit();
            }
            
            // Intentar login
            if ($this->user->login($email, $password)) {
                // Login exitoso
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['user_name'] = $this->user->name;
                $_SESSION['user_email'] = $this->user->email;
                $_SESSION['user_avatar'] = $this->user->avatar;
                $_SESSION['logged_in'] = true;
                
                // Recordar usuario si está marcado
                if ($remember_me) {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true); // 30 días
                    // Aquí podrías guardar el token en la base de datos para mayor seguridad
                }
                
                $_SESSION['success'] = "¡Bienvenido de vuelta, " . $this->user->name . "!";
                header("Location: " . getBaseUrl() . "/Views/Users/dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Email o contraseña incorrectos";
                header("Location: " . getBaseUrl() . "/Views/Auth/login.php");
                exit();
            }
        }
    }
    
    // Procesar registro
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            
            // Validaciones
            if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
                $_SESSION['error'] = "Por favor completa todos los campos";
                header("Location: ../../Views/Auth/login.php");
                exit();
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Por favor ingresa un email válido";
                header("Location: ../../Views/Auth/login.php");
                exit();
            }
            
            if (strlen($password) < 6) {
                $_SESSION['error'] = "La contraseña debe tener al menos 6 caracteres";
                header("Location: ../../Views/Auth/login.php");
                exit();
            }
            
            if ($password !== $confirm_password) {
                $_SESSION['error'] = "Las contraseñas no coinciden";
                header("Location: ../../Views/Auth/login.php");
                exit();
            }
            
            // Verificar si el email ya existe
            if ($this->user->emailExists($email)) {
                $_SESSION['error'] = "Este email ya está registrado";
                header("Location: ../../Views/Auth/login.php");
                exit();
            }
            
            // Crear usuario
            $this->user->name = $name;
            $this->user->email = $email;
            $this->user->password = $password;
            $this->user->email_verified = 1; // Por simplicidad, lo marcamos como verificado
            $this->user->verification_token = null;
            
            if ($this->user->register()) {
                $_SESSION['success'] = "¡Registro exitoso! Ahora puedes iniciar sesión";
                header("Location: ../../Views/Auth/login.php");
                exit();
            } else {
                $_SESSION['error'] = "Error al registrar usuario. Intenta nuevamente";
                header("Location: ../../Views/Auth/login.php");
                exit();
            }
        }
    }
    
    // Procesar login con Google (JWT Token)
    public function googleLogin() {
        // Configurar headers para JSON
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $credential = $input['credential'] ?? null;
            
            // Log para debug
            error_log('Google login attempt: ' . json_encode($input));
            
            if (!$credential) {
                echo json_encode(['success' => false, 'message' => 'Token de Google no recibido']);
                exit();
            }
            
            // Decodificar el JWT token de Google (sin verificación por simplicidad)
            $parts = explode('.', $credential);
            if (count($parts) !== 3) {
                echo json_encode(['success' => false, 'message' => 'Token de Google inválido']);
                exit();
            }
            
            // Decodificar el payload
            $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
            
            // Log del payload para debug
            error_log('Google payload: ' . json_encode($payload));
            
            if (!$payload || !isset($payload['sub']) || !isset($payload['email']) || !isset($payload['name'])) {
                echo json_encode(['success' => false, 'message' => 'Datos de Google incompletos', 'payload' => $payload]);
                exit();
            }
            
            $google_id = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'];
            $avatar = $payload['picture'] ?? null;
            
            // Verificar que el email esté verificado por Google
            if (!isset($payload['email_verified']) || !$payload['email_verified']) {
                echo json_encode(['success' => false, 'message' => 'Email no verificado por Google']);
                exit();
            }
            
            try {
                if ($this->user->loginWithGoogle($google_id, $email, $name, $avatar)) {
                    $_SESSION['user_id'] = $this->user->id;
                    $_SESSION['user_name'] = $this->user->name;
                    $_SESSION['user_email'] = $this->user->email;
                    $_SESSION['user_avatar'] = $this->user->avatar;
                    $_SESSION['logged_in'] = true;
                    
                    echo json_encode(['success' => true, 'message' => 'Login exitoso con Google']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al procesar el login con Google']);
                }
            } catch (Exception $e) {
                error_log('Google login error: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
            }
            
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit();
        }
    }
    
    // Logout
    public function logout() {
        session_start();
        
        // Limpiar cookies de recordar usuario
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        }
        
        // Destruir sesión
        session_unset();
        session_destroy();
        
        header("Location: ../../index.php");
        exit();
    }
    
    // Verificar si el usuario está logueado
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    // Requerir login (middleware)
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header("Location: " . getBaseUrl() . "/Views/Auth/login.php");
            exit();
        }
    }
    
    // Obtener datos del usuario actual
    public static function getCurrentUser() {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email'],
                'avatar' => $_SESSION['user_avatar']
            ];
        }
        return null;
    }
}

// Manejo de rutas
if (isset($_GET['action'])) {
    session_start(); // Iniciar sesión aquí en lugar del inicio del archivo
    $auth = new AuthController();
    
    switch ($_GET['action']) {
        case 'login':
            $auth->login();
            break;
        case 'register':
            $auth->register();
            break;
        case 'google_login':
            $auth->googleLogin();
            break;
        case 'logout':
            $auth->logout();
            break;
        default:
            header("Location: ../../Views/Auth/login.php");
            break;
    }
}
?>
