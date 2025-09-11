<?php
session_start();
require_once __DIR__ . '/../Models/User.php';

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
                header("Location: ../../Views/Users/dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Email o contraseña incorrectos";
                header("Location: ../../Views/Auth/login.php");
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
    
    // Procesar login con Google
    public function googleLogin() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $google_data = json_decode($_POST['google_data'], true);
            
            if (isset($google_data['sub']) && isset($google_data['email']) && isset($google_data['name'])) {
                $google_id = $google_data['sub'];
                $email = $google_data['email'];
                $name = $google_data['name'];
                $avatar = $google_data['picture'] ?? null;
                
                if ($this->user->loginWithGoogle($google_id, $email, $name, $avatar)) {
                    $_SESSION['user_id'] = $this->user->id;
                    $_SESSION['user_name'] = $this->user->name;
                    $_SESSION['user_email'] = $this->user->email;
                    $_SESSION['user_avatar'] = $this->user->avatar;
                    $_SESSION['logged_in'] = true;
                    
                    echo json_encode(['success' => true, 'redirect' => '../../Views/Users/dashboard.php']);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Error al procesar el login con Google']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Datos de Google incompletos']);
            }
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
            header("Location: ../../Views/Auth/login.php");
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
    $auth = new AuthController();
    
    switch ($_GET['action']) {
        case 'login':
            $auth->login();
            break;
        case 'register':
            $auth->register();
            break;
        case 'google-login':
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
