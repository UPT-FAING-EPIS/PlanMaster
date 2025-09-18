<?php
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../config/url_config.php';

// Si ya está logueado, redirigir al dashboard
if (AuthController::isLoggedIn()) {
    header("Location: " . getBaseUrl() . "/Views/Users/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - PlanMaster</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_login.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo getBaseUrl(); ?>/Resources/favicon.ico">
</head>
<body>
    <div class="login-container">
        <!-- Panel izquierdo - Bienvenida -->
        <div class="welcome-panel">
            <a href="<?php echo getBaseUrl(); ?>/index.php" class="back-button">← Volver al inicio</a>
            
            <div class="logo-welcome">PlanMaster</div>
            <p class="welcome-subtitle">Tu plan estratégico en un solo clic</p>
            
            <ul class="feature-list">
                <li>Guía estructurada paso a paso</li>
                <li>Ahorro de tiempo y accesibilidad</li>
                <li>Toma de decisiones más clara</li>
                <li>Reportes profesionales</li>
            </ul>
        </div>
        
        <!-- Panel derecho - Formulario -->
        <div class="form-panel">
            <div class="form-header">
                <h1 class="form-title">¡Bienvenido!</h1>
                <p class="form-subtitle">Accede a tu cuenta o crea una nueva</p>
            </div>
            
            <!-- Mostrar mensajes de error o éxito -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    echo htmlspecialchars($_SESSION['error']); 
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo htmlspecialchars($_SESSION['success']); 
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <!-- Pestañas -->
            <div class="tab-container">
                <button class="tab-button active" data-tab="login-content">Iniciar Sesión</button>
                <button class="tab-button" data-tab="register-content">Registrarse</button>
            </div>
            
            <!-- Google Login (Primero) -->
            <div id="g_id_onload"
                 data-client_id="123656077365-r7upne95qtnee2qqmjli12cgeb7jomjm.apps.googleusercontent.com"
                 data-callback="handleCredentialResponse">
            </div>
            <div class="g_id_signin" data-type="standard"></div>
            
            <!-- Separador -->
            <div class="text-center" style="margin: 30px 0; color: #ccc; position: relative;">
                <span style="background: white; padding: 0 20px; font-size: 0.9rem;">o usa tu cuenta</span>
                <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #e0e0e0; z-index: -1;"></div>
            </div>
            
            <!-- Contenido de Login -->
            <div id="login-content" class="form-content active">
                <form id="loginForm" method="POST" action="<?php echo getBaseUrl(); ?>/Controllers/AuthController.php?action=login">
                    <div class="form-group">
                        <label for="login-email" class="form-label">Email</label>
                        <input type="email" id="login-email" name="email" class="form-input" 
                               placeholder="tu@email.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="login-password" class="form-label">Contraseña</label>
                        <input type="password" id="login-password" name="password" class="form-input" 
                               placeholder="Tu contraseña" required>
                    </div>
                    
                    <div class="checkbox-group">
                        <label class="custom-checkbox">
                            <input type="checkbox" name="remember_me">
                            <span class="checkmark"></span>
                            Recordar mi sesión
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                </form>
                
                <div class="text-center text-small">
                    <a href="forgot-password.php" class="link">¿Olvidaste tu contraseña?</a>
                </div>
            </div>
            
            <!-- Contenido de Registro -->
            <div id="register-content" class="form-content">
                <form id="registerForm" method="POST" action="<?php echo getBaseUrl(); ?>/Controllers/AuthController.php?action=register">
                    <div class="form-group">
                        <label for="register-name" class="form-label">Nombre completo</label>
                        <input type="text" id="register-name" name="name" class="form-input" 
                               placeholder="Tu nombre completo" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-email" class="form-label">Email (falso)</label>
                        <input type="email" id="register-email" name="email" class="form-input" 
                               placeholder="tu@email.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-password" class="form-label">Contraseña</label>
                        <input type="password" id="register-password" name="password" class="form-input" 
                               placeholder="Mínimo 6 caracteres" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-confirm-password" class="form-label">Confirmar contraseña</label>
                        <input type="password" id="register-confirm-password" name="confirm_password" class="form-input" 
                               placeholder="Confirma tu contraseña" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Crear Cuenta</button>
                </form>
            </div>            <div class="text-center text-small">
                <p>Al registrarte, aceptas nuestros 
                   <a href="#" class="link">Términos de Servicio</a> y 
                   <a href="#" class="link">Política de Privacidad</a>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Google Identity Services -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    
    <!-- JavaScript -->
    <script src="<?php echo getBaseUrl(); ?>/Publics/js/login.js"></script>
    
    <script>
        // Función para manejar la respuesta de Google
        function handleCredentialResponse(response) {
            console.log('Token de Google recibido:', response.credential);
            
            // Enviar el token al servidor
            fetch('<?php echo getBaseUrl(); ?>/Controllers/AuthController.php?action=google_login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    credential: response.credential
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Login exitoso:', data);
                    // Redirigir al dashboard
                    window.location.href = '<?php echo getBaseUrl(); ?>/Views/Users/dashboard.php';
                } else {
                    console.error('Error en login:', data.message);
                    alert('Error al iniciar sesión con Google: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error de red:', error);
                alert('Error de conexión. Por favor intenta de nuevo.');
            });
        }
        
        // Inicializar Google Identity cuando la página se carga
        window.onload = function() {
            console.log('Inicializando Google Identity...');
            
            // Esperar a que Google se cargue
            function initGoogle() {
                if (window.google && window.google.accounts) {
                    try {
                        google.accounts.id.initialize({
                            client_id: "123656077365-r7upne95qtnee2qqmjli12cgeb7jomjm.apps.googleusercontent.com",
                            callback: handleCredentialResponse,
                            auto_select: false,
                            cancel_on_tap_outside: false
                        });
                        console.log('Google Identity inicializado correctamente');
                        
                        // Renderizar el botón automático también
                        google.accounts.id.renderButton(
                            document.querySelector('.g_id_signin'),
                            { 
                                theme: 'outline', 
                                size: 'large',
                                type: 'standard',
                                text: 'continue_with',
                                shape: 'rectangular',
                                width: '100%'
                            }
                        );
                        
                    } catch (e) {
                        console.error('Error al inicializar Google:', e);
                    }
                } else {
                    console.log('Google no disponible aún, reintentando...');
                    setTimeout(initGoogle, 500);
                }
            }
            
            initGoogle();
        };
    </script>
</body>
</html>