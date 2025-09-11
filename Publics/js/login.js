// JavaScript simplificado para PlanMaster Login
console.log('Iniciando JavaScript del login...');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado - Iniciando funciones del login');
    
    // Función para cambiar entre tabs
    function handleTabs() {
        const loginTab = document.querySelector('[data-tab="login-content"]');
        const registerTab = document.querySelector('[data-tab="register-content"]');
        const loginContent = document.getElementById('login-content');
        const registerContent = document.getElementById('register-content');
        
        console.log('Elementos encontrados:', {
            loginTab: !!loginTab,
            registerTab: !!registerTab, 
            loginContent: !!loginContent,
            registerContent: !!registerContent
        });
        
        if (loginTab && registerTab && loginContent && registerContent) {
            loginTab.onclick = function() {
                console.log('Cambiando a tab de login');
                loginTab.classList.add('active');
                registerTab.classList.remove('active');
                loginContent.classList.add('active');
                registerContent.classList.remove('active');
            };
            
            registerTab.onclick = function() {
                console.log('Cambiando a tab de registro');
                registerTab.classList.add('active');
                loginTab.classList.remove('active');
                registerContent.classList.add('active');
                loginContent.classList.remove('active');
            };
        }
    }
    
    // Función para verificar inputs
    function testInputs() {
        const inputs = document.querySelectorAll('input');
        console.log('Total de inputs encontrados:', inputs.length);
        
        inputs.forEach(function(input, index) {
            console.log('Input ' + index + ':', input.name, input.type);
            
            input.addEventListener('focus', function() {
                console.log('FOCUS en input:', this.name);
                this.style.borderColor = '#42a5f5';
            });
            
            input.addEventListener('input', function() {
                console.log('ESCRIBIENDO en input:', this.name, 'Valor:', this.value);
            });
            
            input.addEventListener('blur', function() {
                console.log('BLUR en input:', this.name);
                this.style.borderColor = '#e0e0e0';
            });
        });
    }
    
    // Función para manejar envío de formularios
    function handleForms() {
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                console.log('Enviando formulario de login');
                // Dejar que se envíe normalmente
            });
        }
        
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                console.log('Enviando formulario de registro');
                
                // Validaciones básicas del lado cliente
                const name = this.querySelector('[name="name"]').value.trim();
                const email = this.querySelector('[name="email"]').value.trim();
                const password = this.querySelector('[name="password"]').value;
                const confirmPassword = this.querySelector('[name="confirm_password"]').value;
                
                if (!name || !email || !password || !confirmPassword) {
                    e.preventDefault();
                    alert('Por favor completa todos los campos');
                    return;
                }
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden');
                    return;
                }
                
                if (password.length < 6) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 6 caracteres');
                    return;
                }
                
                console.log('Formulario de registro válido, enviando...');
                // Dejar que se envíe normalmente
            });
        }
    }
    
    // Función para verificar botones
    function testButtons() {
        const buttons = document.querySelectorAll('button, .btn');
        console.log('Total de botones encontrados:', buttons.length);
        
        buttons.forEach(function(button, index) {
            console.log('Botón ' + index + ':', button.textContent.trim());
            
            button.addEventListener('click', function(e) {
                console.log('CLICK en botón:', this.textContent.trim());
                
                // NO interferir con ningún botón - dejar que funcionen normalmente
            });
        });
    }
    
    // Inicializar todas las funciones
    handleTabs();
    testInputs();
    handleForms();
    testButtons();
    
    console.log('JavaScript del login inicializado correctamente');
});
