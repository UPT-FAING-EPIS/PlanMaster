// JavaScript para la vista individual del proyecto - PlanMaster
document.addEventListener('DOMContentLoaded', function() {
    console.log('Project.js cargado correctamente');
    console.log('Datos del proyecto:', projectData);
    console.log('Progreso:', progressData);
    
    // Inicializar funcionalidades
    initProjectPage();
    setupProgressCircle();
    setupEventListeners();
    animateSections();
});

function initProjectPage() {
    // Verificar datos del proyecto
    if (typeof projectData === 'undefined' || typeof progressData === 'undefined') {
        console.error('Datos del proyecto no disponibles');
        return;
    }
    
    // Actualizar el título de la página
    document.title = `${projectData.project_name} - PlanMaster`;
    
    // Configurar el círculo de progreso
    updateProgressCircle();
    
    // Marcar secciones completadas
    markCompletedSections();
}

function setupProgressCircle() {
    const progressCircle = document.querySelector('.circle-progress');
    if (progressCircle && typeof progressData !== 'undefined') {
        const percentage = Math.round(progressData.percentage);
        const degrees = (percentage / 100) * 360;
        
        // Establecer la variable CSS para el progreso
        progressCircle.style.setProperty('--progress-deg', `${degrees}deg`);
    }
}

function updateProgressCircle() {
    const progressValue = document.querySelector('.progress-value');
    const progressFill = document.querySelector('.progress-fill');
    
    if (progressValue && progressData) {
        // Animar el texto del porcentaje
        animateNumber(progressValue, Math.round(progressData.percentage));
        
        // Animar la barra de progreso
        if (progressFill) {
            setTimeout(() => {
                progressFill.style.width = `${progressData.percentage}%`;
            }, 500);
        }
    }
}

function animateNumber(element, target) {
    let current = 0;
    const increment = target / 50; // 50 frames de animación
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current) + '%';
    }, 30);
}

function markCompletedSections() {
    if (!progressData.sections) return;
    
    Object.keys(progressData.sections).forEach(sectionNum => {
        const isCompleted = progressData.sections[sectionNum];
        const sectionCard = document.querySelector(`[data-section="${sectionNum}"]`);
        
        if (sectionCard) {
            if (isCompleted) {
                sectionCard.classList.add('completed');
                sectionCard.classList.remove('pending');
            } else {
                sectionCard.classList.add('pending');
                sectionCard.classList.remove('completed');
            }
        }
    });
}

function setupEventListeners() {
    // Botón de salir y guardar
    const saveExitBtn = document.querySelector('.btn-save-exit');
    if (saveExitBtn) {
        saveExitBtn.addEventListener('click', saveAndExit);
    }
    
    // Botones de comenzar sección
    const startButtons = document.querySelectorAll('.btn-start-section');
    startButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const sectionNum = this.closest('.section-card').dataset.section;
            startSection(sectionNum);
        });
    });
    
    // Botones de editar sección
    const editButtons = document.querySelectorAll('.btn-edit-section');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const sectionNum = this.closest('.section-card').dataset.section;
            editSection(sectionNum);
        });
    });
}

function animateSections() {
    const sectionCards = document.querySelectorAll('.section-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    sectionCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });
}

// Función para guardar y salir
function saveAndExit() {
    console.log('Guardando progreso y saliendo...');
    
    // Mostrar indicador de guardado
    showNotification('Guardando progreso...', 'info');
    
    // Simular guardado
    setTimeout(() => {
        showNotification('Progreso guardado exitosamente', 'success');
        
        // Redirigir al dashboard después de un breve delay
        setTimeout(() => {
            window.location.href = '../Users/dashboard.php';
        }, 1500);
    }, 1000);
}

// Función para comenzar una sección
function startSection(sectionNum) {
    console.log('Iniciando sección:', sectionNum);
    
    const sectionNames = {
        '1': 'mission',
        '2': 'vision',
        '3': 'values',
        '4': 'objectives',
        '5': 'foda-analysis',
        '6': 'value-chain',
        '7': 'bcg-analysis',
        '8': 'porter-matrix',
        '9': 'pest-analysis',
        '10': 'strategies',
        '11': 'came-matrix'
    };
    
    const sectionName = sectionNames[sectionNum];
    if (sectionName) {
        // Mostrar indicador de carga
        showNotification('Cargando sección...', 'info');
        
        // Redirigir a la página de la sección
        setTimeout(() => {
            let targetUrl = '';
            
            // Para las secciones 1-4, usar directamente el nombre del archivo
            if (sectionNum <= 4) {
                targetUrl = `${sectionName}.php?id=${projectData.id}`;
            } else if (sectionName === 'foda-analysis' || sectionName === 'value-chain' || sectionName === 'bcg-analysis' || sectionName === 'strategies') {
                // Para FODA, Cadena de Valor, BCG y Estrategias, usar el nombre completo del archivo
                targetUrl = `${sectionName}.php?id=${projectData.id}`;
            } else {
                // Para las futuras secciones, usar la carpeta sections/
                targetUrl = `sections/${sectionName}.php?project_id=${projectData.id}`;
            }
            
            console.log('Redirigiendo a:', targetUrl);
            console.log('Sección:', sectionName, 'Número:', sectionNum);
            console.log('Project ID:', projectData.id);
            
            window.location.href = targetUrl;
        }, 1000);
    }
}

// Función para editar una sección
function editSection(sectionNum) {
    console.log('Editando sección:', sectionNum);
    
    // Misma lógica que startSection pero para editar
    startSection(sectionNum);
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 10px;
        color: white;
        font-weight: 500;
        z-index: 10001;
        animation: slideInRight 0.3s ease-out;
        max-width: 300px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        ${type === 'success' ? 'background: linear-gradient(135deg, #4caf50, #43a047);' : 
          type === 'error' ? 'background: linear-gradient(135deg, #f44336, #d32f2f);' : 
          'background: linear-gradient(135deg, #42a5f5, #1e88e5);'}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 3000);
}

// Modal de confirmación para salir
function showExitConfirmation() {
    const modal = document.createElement('div');
    modal.className = 'exit-modal-overlay';
    modal.innerHTML = `
        <div class="exit-modal">
            <div class="modal-header">
                <h2>💾 Guardar y Salir</h2>
            </div>
            
            <div class="modal-body">
                <p>¿Estás seguro de que quieres salir del proyecto?</p>
                <p><strong>Tu progreso actual será guardado automáticamente.</strong></p>
            </div>
            
            <div class="modal-footer">
                <button class="btn-modal-cancel" onclick="closeExitModal()">Cancelar</button>
                <button class="btn-modal-confirm" onclick="confirmExit()">Guardar y Salir</button>
            </div>
        </div>
    `;
    
    // Agregar estilos del modal
    const modalStyles = `
        <style>
        .exit-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease-out;
        }
        
        .exit-modal {
            background: white;
            border-radius: 15px;
            max-width: 400px;
            width: 90%;
            animation: slideInUp 0.4s ease-out;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .exit-modal .modal-header {
            padding: 25px 25px 15px;
            border-bottom: 1px solid #e0e0e0;
            text-align: center;
        }
        
        .exit-modal .modal-header h2 {
            color: #333;
            font-weight: 600;
            margin: 0;
        }
        
        .exit-modal .modal-body {
            padding: 25px;
            text-align: center;
        }
        
        .exit-modal .modal-body p {
            margin-bottom: 15px;
            color: #555;
            line-height: 1.6;
        }
        
        .exit-modal .modal-footer {
            padding: 15px 25px 25px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .exit-modal .btn-modal-cancel,
        .exit-modal .btn-modal-confirm {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .exit-modal .btn-modal-cancel {
            background: #f5f5f5;
            color: #666;
        }
        
        .exit-modal .btn-modal-cancel:hover {
            background: #e0e0e0;
        }
        
        .exit-modal .btn-modal-confirm {
            background: linear-gradient(135deg, #4caf50, #43a047);
            color: white;
        }
        
        .exit-modal .btn-modal-confirm:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
        }
        </style>
    `;
    
    document.head.insertAdjacentHTML('beforeend', modalStyles);
    document.body.appendChild(modal);
}

function closeExitModal() {
    const modal = document.querySelector('.exit-modal-overlay');
    if (modal) {
        modal.style.animation = 'fadeOut 0.3s ease-in';
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
}

function confirmExit() {
    closeExitModal();
    saveAndExit();
}

// Agregar estilos de animación
const animationStyles = `
    <style>
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes slideInUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    </style>
`;

document.head.insertAdjacentHTML('beforeend', animationStyles);