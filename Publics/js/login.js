// Login JavaScript - PlanMaster
document.addEventListener('DOMContentLoaded', function() {
    
    // Elementos del DOM
    const tabButtons = document.querySelectorAll('.tab-button');
    const formContents = document.querySelectorAll('.form-content');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const googleLoginBtn = document.getElementById('googleLogin');
    
    // Manejo de pestañas
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.getAttribute('data-tab');
            
            // Remover clase active de todos los botones y contenidos
            tabButtons.forEach(btn => btn.classList.remove('active'));
            formContents.forEach(content => content.classList.remove('active'));
            
            // Agregar clase active al botón clickeado y su contenido
            button.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
    
    // Validación en tiempo real
    const inputs = document.querySelectorAll('.form-input');
    inputs.forEach(input => {
        input.addEventListener('blur', validateField);
        input.addEventListener('input', clearError);
    });
    
    function validateField(e) {
        const input = e.target;
        const value = input.value.trim();
        
        // Limpiar errores previos
        clearError(e);
        
        switch(input.type) {
            case 'email':
                if (value && !isValidEmail(value)) {
                    showFieldError(input, 'Por favor ingresa un email válido');
                }
                break;
            case 'password':
                if (input.name === 'password' && value && value.length < 6) {
                    showFieldError(input, 'La contraseña debe tener al menos 6 caracteres');
                }
                if (input.name === 'confirm_password') {
                    const password = document.querySelector('input[name="password"]').value;
                    if (value && value !== password) {
                        showFieldError(input, 'Las contraseñas no coinciden');
                    }
                }
                break;
            case 'text':
                if (input.name === 'name' && value && value.length < 2) {
                    showFieldError(input, 'El nombre debe tener al menos 2 caracteres');
                }
                break;
        }
    }
    
    function clearError(e) {
        const input = e.target;
        input.classList.remove('error');
        const errorMsg = input.parentNode.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.remove();
        }
    }
    
    function showFieldError(input, message) {
        input.classList.add('error');
        
        // Remover mensaje de error anterior si existe
        const existingError = input.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Crear nuevo mensaje de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = '#f44336';
        errorDiv.style.fontSize = '0.8rem';
        errorDiv.style.marginTop = '5px';
        errorDiv.textContent = message;
        
        input.parentNode.appendChild(errorDiv);
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Manejo del formulario de login
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = this.querySelector('input[name="email"]').value.trim();
            const password = this.querySelector('input[name="password"]').value;
            
            if (!email || !password) {
                showAlert('Por favor completa todos los campos', 'error');
                return;
            }
            
            if (!isValidEmail(email)) {
                showAlert('Por favor ingresa un email válido', 'error');
                return;
            }
            
            // Mostrar loading
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner"></span> Iniciando sesión...';
            submitBtn.classList.add('loading');
            
            // Enviar formulario
            setTimeout(() => {
                this.submit();
            }, 500);
        });
    }
    
    // Manejo del formulario de registro
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = this.querySelector('input[name="name"]').value.trim();
            const email = this.querySelector('input[name="email"]').value.trim();
            const password = this.querySelector('input[name="password"]').value;
            const confirmPassword = this.querySelector('input[name="confirm_password"]').value;
            
            // Validaciones
            if (!name || !email || !password || !confirmPassword) {
                showAlert('Por favor completa todos los campos', 'error');
                return;
            }
            
            if (name.length < 2) {
                showAlert('El nombre debe tener al menos 2 caracteres', 'error');
                return;
            }
            
            if (!isValidEmail(email)) {
                showAlert('Por favor ingresa un email válido', 'error');
                return;
            }
            
            if (password.length < 6) {
                showAlert('La contraseña debe tener al menos 6 caracteres', 'error');
                return;
            }
            
            if (password !== confirmPassword) {
                showAlert('Las contraseñas no coinciden', 'error');
                return;
            }
            
            // Mostrar loading
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner"></span> Creando cuenta...';
            submitBtn.classList.add('loading');
            
            // Enviar formulario
            setTimeout(() => {
                this.submit();
            }, 500);
        });
    }
    
    // Google Login
    if (googleLoginBtn) {
        googleLoginBtn.addEventListener('click', function() {
            if (typeof google !== 'undefined' && google.accounts) {
                google.accounts.id.prompt();
            } else {
                showAlert('Error al cargar Google Sign-In. Verifica tu conexión a internet.', 'error');
            }
        });
    }
    
    // Función para mostrar alertas
    function showAlert(message, type = 'info') {
        // Remover alertas existentes
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Crear nueva alerta
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.innerHTML = message;
        
        // Insertar al inicio del form-panel
        const formPanel = document.querySelector('.form-panel');
        const formHeader = formPanel.querySelector('.form-header');
        formPanel.insertBefore(alertDiv, formHeader.nextSibling);
        
        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    // Animaciones de entrada
    const container = document.querySelector('.login-container');
    if (container) {
        container.style.transform = 'translateY(50px)';
        container.style.opacity = '0';
        
        setTimeout(() => {
            container.style.transition = 'all 0.8s ease-out';
            container.style.transform = 'translateY(0)';
            container.style.opacity = '1';
        }, 100);
    }
    
    // Easter egg: Konami code
    let konamiCode = [];
    const konamiSequence = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65]; // ↑↑↓↓←→←→BA
    
    document.addEventListener('keydown', function(e) {
        konamiCode.push(e.keyCode);
        
        if (konamiCode.length > konamiSequence.length) {
            konamiCode.shift();
        }
        
        if (konamiCode.length === konamiSequence.length && 
            konamiCode.every((code, index) => code === konamiSequence[index])) {
            
            // Activar modo desarrollador
            document.body.style.filter = 'hue-rotate(180deg)';
            showAlert('¡Modo desarrollador activado! 🚀', 'success');
            
            setTimeout(() => {
                document.body.style.filter = 'none';
            }, 3000);
            
            konamiCode = [];
        }
    });
});

// Callback para Google Sign-In
function handleCredentialResponse(response) {
    // Decodificar el JWT token de Google
    const responsePayload = decodeJwtResponse(response.credential);
    
    // Preparar datos para enviar al servidor
    const googleData = {
        sub: responsePayload.sub,
        email: responsePayload.email,
        name: responsePayload.name,
        picture: responsePayload.picture
    };
    
    // Mostrar loading en el botón de Google
    const googleBtn = document.getElementById('googleLogin');
    const originalText = googleBtn.innerHTML;
    googleBtn.innerHTML = '<span class="spinner"></span> Iniciando con Google...';
    googleBtn.classList.add('loading');
    
    // Enviar datos al servidor
    fetch('../../Controllers/AuthController.php?action=google-login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'google_data=' + encodeURIComponent(JSON.stringify(googleData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            showAlert(data.error || 'Error al iniciar sesión con Google', 'error');
            googleBtn.innerHTML = originalText;
            googleBtn.classList.remove('loading');
        }
    })
    .catch(error => {
        showAlert('Error de conexión. Intenta nuevamente.', 'error');
        googleBtn.innerHTML = originalText;
        googleBtn.classList.remove('loading');
    });
}

// Función para decodificar JWT token de Google
function decodeJwtResponse(token) {
    var base64Url = token.split('.')[1];
    var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    var jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
    }).join(''));
    
    return JSON.parse(jsonPayload);
}

// Función para mostrar alertas (global)
function showAlert(message, type = 'info') {
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = message;
    
    const formPanel = document.querySelector('.form-panel');
    const formHeader = formPanel.querySelector('.form-header');
    formPanel.insertBefore(alertDiv, formHeader.nextSibling);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
