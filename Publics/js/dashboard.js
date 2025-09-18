// Dashboard JavaScript - PlanMaster
document.addEventListener('DOMContentLoaded', function() {
    
    // Inicializar dashboard
    initDashboard();
    
    // Animaciones de entrada
    animateElements();
    
    // Event listeners
    setupEventListeners();
});

function initDashboard() {
    // Verificar si hay proyectos guardados en localStorage
    loadUserProjects();
    
    // Configurar theme según preferencias
    loadUserPreferences();
    
    // Inicializar tooltips
    initTooltips();
}

function animateElements() {
    // Animar elementos con intersection observer
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.8s ease-out forwards';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observar elementos que necesitan animación
    const elementsToAnimate = document.querySelectorAll('.section-card, .feature-item, .summary-card');
    elementsToAnimate.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        observer.observe(el);
    });
}

function setupEventListeners() {
    // Botón de iniciar proyecto
    const startProjectBtn = document.querySelector('.btn-start-project');
    if (startProjectBtn) {
        startProjectBtn.addEventListener('click', startNewProject);
    }
    
    // Cards de secciones - preview
    const sectionCards = document.querySelectorAll('.section-card');
    sectionCards.forEach(card => {
        card.addEventListener('click', () => {
            const sectionNumber = card.querySelector('.section-number').textContent;
            showSectionPreview(sectionNumber);
        });
    });
    
    // Features hover effect
    const featureItems = document.querySelectorAll('.feature-item');
    featureItems.forEach(item => {
        item.addEventListener('mouseenter', () => {
            item.style.transform = 'scale(1.05)';
            item.style.transition = 'transform 0.3s ease';
        });
        
        item.addEventListener('mouseleave', () => {
            item.style.transform = 'scale(1)';
        });
    });
    
    // Easter egg: Konami code para modo desarrollador
    setupKonamiCode();
}

function startNewProject() {
    // Mostrar modal de confirmación bonito
    showProjectModal();
}

function showProjectModal() {
    // Verificar si ya existe un modal y cerrarlo
    const existingModals = document.querySelectorAll('.project-modal-overlay');
    if (existingModals.length > 0) {
        existingModals.forEach(modal => modal.remove());
    }
    
    // Crear modal dinámicamente
    const modal = document.createElement('div');
    modal.className = 'project-modal-overlay';
    modal.innerHTML = `
        <div class="project-modal">
            <div class="modal-header">
                <h2>🚀 Iniciar Nuevo Proyecto</h2>
                <button class="modal-close" onclick="closeProjectModal()">&times;</button>
            </div>
            
            <div class="modal-body">
                <p>Estás a punto de crear un nuevo plan estratégico empresarial.</p>
                <p>Te guiaremos paso a paso por los <strong>11 apartados</strong> esenciales:</p>
                
                <div class="modal-sections">
                    <div class="modal-section-list">
                        <div class="modal-section-item">✓ Misión</div>
                        <div class="modal-section-item">✓ Visión</div>
                        <div class="modal-section-item">✓ Valores</div>
                        <div class="modal-section-item">✓ Objetivos</div>
                        <div class="modal-section-item">✓ Análisis Interno/Externo</div>
                        <div class="modal-section-item">✓ Cadena de Valor</div>
                        <div class="modal-section-item">✓ Matriz BCG</div>
                        <div class="modal-section-item">✓ Matriz de Porter</div>
                        <div class="modal-section-item">✓ Análisis PEST</div>
                        <div class="modal-section-item">✓ Estrategias</div>
                        <div class="modal-section-item">✓ Matriz CAME</div>
                    </div>
                </div>
                
                <div class="modal-form">
                    <div class="form-group">
                        <label for="projectName">Nombre del Proyecto:</label>
                        <input type="text" id="projectName" placeholder="Ej: Plan Estratégico 2024-2027" class="modal-input">
                    </div>
                    
                    <div class="form-group">
                        <label for="companyName">Nombre de la Empresa:</label>
                        <input type="text" id="companyName" placeholder="Ej: Mi Empresa S.A." class="modal-input">
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button class="btn-modal-cancel" onclick="closeProjectModal()">Cancelar</button>
                <button class="btn-modal-confirm" onclick="createProject()">¡Empezar Ahora!</button>
            </div>
        </div>
    `;
    
    // Estilos del modal
    const modalStyles = `
        <style>
        .project-modal-overlay {
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
            backdrop-filter: blur(5px);
        }
        
        .project-modal {
            background: white;
            border-radius: 20px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            animation: slideInUp 0.4s ease-out;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            padding: 30px 30px 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h2 {
            color: #1e88e5;
            font-weight: 600;
            margin: 0;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 2rem;
            color: #666;
            cursor: pointer;
            padding: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .modal-close:hover {
            background: #f5f5f5;
            color: #333;
        }
        
        .modal-body {
            padding: 30px;
        }
        
        .modal-body p {
            margin-bottom: 15px;
            color: #555;
            line-height: 1.6;
        }
        
        .modal-sections {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .modal-section-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }
        
        .modal-section-item {
            color: #4caf50;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .modal-form {
            margin-top: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .modal-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .modal-input:focus {
            outline: none;
            border-color: #42a5f5;
        }
        
        .modal-footer {
            padding: 20px 30px 30px;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }
        
        .btn-modal-cancel,
        .btn-modal-confirm {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-modal-cancel {
            background: #f5f5f5;
            color: #666;
        }
        
        .btn-modal-cancel:hover {
            background: #e0e0e0;
        }
        
        .btn-modal-confirm {
            background: linear-gradient(135deg, #42a5f5, #1e88e5);
            color: white;
        }
        
        .btn-modal-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 136, 229, 0.4);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        </style>
    `;
    
    // Agregar estilos y modal al DOM
    document.head.insertAdjacentHTML('beforeend', modalStyles);
    document.body.appendChild(modal);
    
    // Focus en el primer input
    setTimeout(() => {
        document.getElementById('projectName').focus();
    }, 400);
}

function closeProjectModal() {
    const modals = document.querySelectorAll('.project-modal-overlay');
    modals.forEach(modal => {
        modal.style.animation = 'fadeOut 0.3s ease-in';
        setTimeout(() => {
            if (modal.parentNode) {
                modal.remove();
            }
        }, 300);
    });
}

function createProject() {
    const projectName = document.getElementById('projectName').value.trim();
    const companyName = document.getElementById('companyName').value.trim();
    
    if (!projectName || !companyName) {
        showNotification('Por favor completa todos los campos', 'error');
        return;
    }
    
    // Verificar si ya se está procesando una solicitud
    const confirmBtn = document.querySelector('.btn-modal-confirm');
    if (confirmBtn.disabled) {
        return; // Ya se está procesando
    }
    
    // Deshabilitar botón para evitar doble envío
    confirmBtn.disabled = true;
    confirmBtn.textContent = 'Creando...';
    
    // Crear formulario para enviar datos
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '../../Controllers/ProjectController.php?action=create';
    form.style.display = 'none';
    
    // Agregar campos
    const projectNameInput = document.createElement('input');
    projectNameInput.type = 'hidden';
    projectNameInput.name = 'project_name';
    projectNameInput.value = projectName;
    form.appendChild(projectNameInput);
    
    const companyNameInput = document.createElement('input');
    companyNameInput.type = 'hidden';
    companyNameInput.name = 'company_name';
    companyNameInput.value = companyName;
    form.appendChild(companyNameInput);
    
    // Agregar formulario al DOM y enviarlo
    document.body.appendChild(form);
    form.submit();
}

function showSectionPreview(sectionNumber) {
    const sectionTitles = {
        '1': 'Misión',
        '2': 'Visión', 
        '3': 'Valores',
        '4': 'Objetivos',
        '5': 'Análisis Interno y Externo',
        '6': 'Cadena de Valor',
        '7': 'Matriz BCG',
        '8': 'Matriz de Porter',
        '9': 'Análisis PEST',
        '10': 'Identificación de Estrategias',
        '11': 'Matriz CAME'
    };
    
    showNotification(`Vista previa: ${sectionTitles[sectionNumber]} - Disponible en el editor del proyecto`, 'info');
}

function loadUserProjects() {
    const projects = JSON.parse(localStorage.getItem('userProjects') || '[]');
    
    if (projects.length > 0) {
        // Actualizar la sección de proyectos existentes
        updateProjectsSection(projects);
    }
}

function updateProjectsSection(projects) {
    const projectsContainer = document.querySelector('.projects-container');
    
    if (projects.length === 0) {
        return; // Mantener el estado vacío
    }
    
    projectsContainer.innerHTML = `
        <div class="projects-grid">
            ${projects.map(project => `
                <div class="project-card" onclick="openProject(${project.id})">
                    <div class="project-header">
                        <h3>${project.projectName}</h3>
                        <span class="project-status status-${project.status}">${getStatusText(project.status)}</span>
                    </div>
                    <p class="project-company">${project.companyName}</p>
                    <div class="project-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${project.progress}%"></div>
                        </div>
                        <span class="progress-text">${project.progress}% completado</span>
                    </div>
                    <div class="project-date">
                        Creado: ${new Date(project.createdAt).toLocaleDateString()}
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

function getStatusText(status) {
    const statusMap = {
        'draft': 'Borrador',
        'in_progress': 'En Progreso',
        'completed': 'Completado'
    };
    return statusMap[status] || status;
}

function openProject(projectId) {
    showNotification('Abriendo proyecto...', 'info');
    setTimeout(() => {
        window.location.href = `project-editor.php?id=${projectId}`;
    }, 1000);
}

function loadUserPreferences() {
    // Cargar preferencias del usuario (theme, idioma, etc.)
    const preferences = JSON.parse(localStorage.getItem('userPreferences') || '{}');
    
    if (preferences.theme === 'dark') {
        // Aplicar tema oscuro si está configurado
        document.body.classList.add('dark-theme');
    }
}

function initTooltips() {
    // Agregar tooltips informativos a elementos
    const elementsWithTooltips = document.querySelectorAll('[data-tooltip]');
    
    elementsWithTooltips.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(event) {
    const text = event.target.getAttribute('data-tooltip');
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = text;
    tooltip.style.cssText = `
        position: absolute;
        background: #333;
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        z-index: 10000;
        pointer-events: none;
        animation: fadeIn 0.3s ease-out;
    `;
    
    document.body.appendChild(tooltip);
    
    const rect = event.target.getBoundingClientRect();
    tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
    tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
    
    event.target._tooltip = tooltip;
}

function hideTooltip(event) {
    if (event.target._tooltip) {
        event.target._tooltip.remove();
        delete event.target._tooltip;
    }
}

function setupKonamiCode() {
    let konamiCode = [];
    const konamiSequence = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65]; // ↑↑↓↓←→←→BA
    
    document.addEventListener('keydown', function(e) {
        konamiCode.push(e.keyCode);
        
        if (konamiCode.length > konamiSequence.length) {
            konamiCode.shift();
        }
        
        if (konamiCode.length === konamiSequence.length && 
            konamiCode.every((code, index) => code === konamiSequence[index])) {
            
            activateDeveloperMode();
            konamiCode = [];
        }
    });
}

function activateDeveloperMode() {
    // Easter egg: Modo desarrollador
    document.body.style.filter = 'hue-rotate(180deg) saturate(1.5)';
    showNotification('🚀 ¡Modo desarrollador activado! Funciones especiales desbloqueadas', 'success');
    
    // Agregar funciones especiales
    const devPanel = document.createElement('div');
    devPanel.innerHTML = `
        <div style="position: fixed; bottom: 20px; left: 20px; background: rgba(0,0,0,0.8); color: white; padding: 15px; border-radius: 8px; z-index: 10000;">
            <h4>🔧 Panel de Desarrollador</h4>
            <button onclick="exportUserData()" style="background: #42a5f5; color: white; border: none; padding: 5px 10px; margin: 5px; border-radius: 4px; cursor: pointer;">Exportar Datos</button>
            <button onclick="clearAllData()" style="background: #f44336; color: white; border: none; padding: 5px 10px; margin: 5px; border-radius: 4px; cursor: pointer;">Limpiar Todo</button>
            <button onclick="deactivateDeveloperMode()" style="background: #666; color: white; border: none; padding: 5px 10px; margin: 5px; border-radius: 4px; cursor: pointer;">Desactivar</button>
        </div>
    `;
    document.body.appendChild(devPanel);
    devPanel.id = 'devPanel';
}

function deactivateDeveloperMode() {
    document.body.style.filter = 'none';
    const devPanel = document.getElementById('devPanel');
    if (devPanel) {
        devPanel.remove();
    }
    showNotification('Modo desarrollador desactivado', 'info');
}

function exportUserData() {
    const userData = {
        projects: JSON.parse(localStorage.getItem('userProjects') || '[]'),
        preferences: JSON.parse(localStorage.getItem('userPreferences') || '{}'),
        timestamp: new Date().toISOString()
    };
    
    const dataStr = JSON.stringify(userData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    
    const link = document.createElement('a');
    link.href = URL.createObjectURL(dataBlob);
    link.download = 'planmaster-backup.json';
    link.click();
    
    showNotification('Datos exportados exitosamente', 'success');
}

function clearAllData() {
    if (confirm('¿Estás seguro de que quieres eliminar todos los datos? Esta acción no se puede deshacer.')) {
        localStorage.clear();
        showNotification('Todos los datos han sido eliminados', 'success');
        setTimeout(() => {
            location.reload();
        }, 2000);
    }
}

// Función global para mostrar notificaciones
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    const colors = {
        success: '#4caf50',
        error: '#f44336',
        info: '#2196f3',
        warning: '#ff9800'
    };
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        animation: slideInRight 0.3s ease-out;
        background: ${colors[type] || colors.info};
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        max-width: 300px;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Agregar estilos de animación para notificaciones
const notificationStyles = `
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
    
    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
    </style>
`;

document.head.insertAdjacentHTML('beforeend', notificationStyles);
