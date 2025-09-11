t<?php
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';

// Si ya está logueado, redirigir al dashboard
if (AuthController::isLoggedIn()) {
    header("Location: ../Users/dashboard.php");
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
    <link rel="stylesheet" href="../../Publics/css/styles_login.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../Resources/favicon.ico">
</head>
<body>
    <div class="login-container">
        <!-- Panel izquierdo - Bienvenida -->
        <div class="welcome-panel">
            <a href="../../index.php" class="back-button">← Volver al inicio</a>
            
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
            
            <!-- Contenido de Login -->
            <div id="login-content" class="form-content active">
                <form id="loginForm" method="POST" action="../../Controllers/AuthController.php?action=login">
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
                <form id="registerForm" method="POST" action="../../Controllers/AuthController.php?action=register">
                    <div class="form-group">
                        <label for="register-name" class="form-label">Nombre completo</label>
                        <input type="text" id="register-name" name="name" class="form-input" 
                               placeholder="Tu nombre completo" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-email" class="form-label">Email</label>
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
            </div>
            
            <!-- Separador -->
            <div class="text-center" style="margin: 30px 0; color: #ccc; position: relative;">
                <span style="background: white; padding: 0 20px; font-size: 0.9rem;">o continúa con</span>
                <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #e0e0e0; z-index: -1;"></div>
            </div>
            
    <!-- Google Login (Real) -->
    <div id="g_id_onload"
         data-client_id="123656077365-r7upne95qtnee2qqmjli12cgeb7jomjm.apps.googleusercontent.com"
         data-callback="handleCredentialResponse">
    </div>
    <div class="g_id_signin" data-type="standard"></div>            <div class="text-center text-small">
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
    <script src="../../Publics/js/login.js"></script>
    
    <script>
        // Función para manejar la respuesta de Google
        function handleCredentialResponse(response) {
            console.log('Token de Google recibido:', response.credential);
            
            // Mostrar loading
            const googleBtn = document.getElementById('googleLogin');
            if (googleBtn) {
                googleBtn.innerHTML = '⏳ Iniciando sesión con Google...';
                googleBtn.disabled = true;
            }
            
            // Enviar el token al servidor
            fetch('../../Controllers/AuthController.php?action=google_login', {
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
                    window.location.href = '../Users/dashboard.php';
                } else {
                    console.error('Error en login:', data.message);
                    alert('Error al iniciar sesión con Google: ' + data.message);
                    
                    // Restaurar botón
                    if (googleBtn) {
                        googleBtn.innerHTML = `
                            <svg class="google-icon" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Continuar con Google
                        `;
                        googleBtn.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error('Error de red:', error);
                alert('Error de conexión. Por favor intenta de nuevo.');
                
                // Restaurar botón
                if (googleBtn) {
                    googleBtn.innerHTML = `
                        <svg class="google-icon" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Continuar con Google
                    `;
                    googleBtn.disabled = false;
                }
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