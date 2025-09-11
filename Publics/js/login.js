// JavaScript simplificado para PlanMaster Login
console.log('Iniciando JavaScript del login...');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado - Iniciando funciones del login');
    
    // Función para cambiar entre tabs
    function handleTabs() {
        const loginTab = document.getElementById('login-tab');
        const registerTab = document.getElementById('register-tab');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        
        console.log('Elementos encontrados:', {
            loginTab: !!loginTab,
            registerTab: !!registerTab, 
            loginForm: !!loginForm,
            registerForm: !!registerForm
        });
        
        if (loginTab && registerTab && loginForm && registerForm) {
            loginTab.onclick = function() {
                console.log('Cambiando a tab de login');
                loginTab.classList.add('active');
                registerTab.classList.remove('active');
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
            };
            
            registerTab.onclick = function() {
                console.log('Cambiando a tab de registro');
                registerTab.classList.add('active');
                loginTab.classList.remove('active');
                registerForm.style.display = 'block';
                loginForm.style.display = 'none';
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
    
    // Función para verificar botones
    function testButtons() {
        const buttons = document.querySelectorAll('button, .btn');
        console.log('Total de botones encontrados:', buttons.length);
        
        buttons.forEach(function(button, index) {
            console.log('Botón ' + index + ':', button.textContent.trim());
            
            button.addEventListener('click', function(e) {
                console.log('CLICK en botón:', this.textContent.trim());
                
                // Si es botón de Google, mostrar mensaje
                if (this.textContent.includes('Google')) {
                    e.preventDefault();
                    alert('Login con Google no configurado aún.\n\nUsa las credenciales de prueba:\nEmail: admin@planmaster.com\nContraseña: admin');
                }
            });
        });
    }
    
    // Inicializar todas las funciones
    handleTabs();
    testInputs();
    testButtons();
    
    console.log('JavaScript del login inicializado correctamente');
});
