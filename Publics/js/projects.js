// JavaScript para la página de proyectos - PlanMaster
document.addEventListener('DOMContentLoaded', function() {
    console.log('Projects.js cargado correctamente');
    
    // Inicializar funcionalidades
    initProjectsPage();
    setupEventListeners();
    animateProjectCards();
});

function initProjectsPage() {
    // Verificar si hay proyectos
    const projectCards = document.querySelectorAll('.project-card');
    console.log('Proyectos encontrados:', projectCards.length);
    
    // Configurar animaciones de entrada
    projectCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

function setupEventListeners() {
    // Botones de nuevo proyecto
    const newProjectBtns = document.querySelectorAll('.btn-new-project, .btn-start-first-project');
    newProjectBtns.forEach(btn => {
        btn.addEventListener('click', startNewProject);
    });
}

function animateProjectCards() {
    const cards = document.querySelectorAll('.project-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'slideInUp 0.6s ease-out';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    cards.forEach(card => observer.observe(card));
}

// Función para continuar un proyecto
function continueProject(projectId) {
    console.log('Continuando proyecto ID:', projectId);
    
    // Mostrar indicador de carga
    showNotification('Cargando proyecto...', 'info');
    
    // Simular carga y redirigir
    setTimeout(() => {
        // Redirigir a la página del proyecto
        window.location.href = `../Projects/project.php?id=${projectId}`;
    }, 1000);
}

// Función para editar un proyecto
function editProject(projectId) {
    console.log('Editando proyecto ID:', projectId);
    
    // Crear modal de edición
    showEditProjectModal(projectId);
}

// Función para duplicar un proyecto
function duplicateProject(projectId) {
    console.log('Duplicando proyecto ID:', projectId);
    
    // Mostrar confirmación
    if (confirm('¿Estás seguro de que quieres duplicar este proyecto?')) {
        // Simular duplicación
        showNotification('Duplicando proyecto...', 'info');
        
        setTimeout(() => {
            showNotification('Proyecto duplicado exitosamente', 'success');
            // Recargar la página para mostrar el proyecto duplicado
            location.reload();
        }, 2000);
    }
}

function updateProgressCircle() {
    const progressValue = document.querySelector('.progress-value');
    const progressFill = document.querySelector('.progress-fill');
    
    if (progressValue && progressData) {
        const percentage = Math.round(progressData.percentage);
        progressValue.textContent = `${percentage}%`;
        
        if (progressFill) {
            progressFill.style.width = `${percentage}%`;
        }
    }
}

function updateSectionStates() {
    // Actualizar el estado visual de las secciones según el progreso
    const sections = document.querySelectorAll('.section-card');
    
    sections.forEach((section, index) => {
        const sectionNumber = section.querySelector('.section-number');
        if (sectionNumber) {
            const number = parseInt(sectionNumber.textContent);
            
            // Lógica para determinar el estado de cada sección
            if (number <= 4) { // Solo las primeras 4 secciones están implementadas
                if (progressData.progress.mission && number === 1) {
                    section.classList.add('completed');
                } else if (progressData.progress.vision && number === 2) {
                    section.classList.add('completed');
                } else if (progressData.progress.values && number === 3) {
                    section.classList.add('completed');
                } else if (progressData.progress.objectives && number === 4) {
                    section.classList.add('completed');
                } else {
                    // Determinar si está disponible o bloqueada
                    if (number === 1 || 
                        (number === 2 && progressData.progress.mission) ||
                        (number === 3 && progressData.progress.vision) ||
                        (number === 4 && progressData.progress.values)) {
                        section.classList.add('available');
                    } else {
                        section.classList.add('locked');
                    }
                }
            } else {
                section.classList.add('locked');
            }
        }
    });
}

function setupEventListeners() {
    // Event listeners para las secciones
    const sectionCards = document.querySelectorAll('.section-card:not(.locked)');
    sectionCards.forEach(card => {
        card.addEventListener('click', handleSectionClick);
        card.addEventListener('mouseenter', handleSectionHover);
        card.addEventListener('mouseleave', handleSectionLeave);
    });
    
    // Event listeners para el dropdown de usuario
    const userMenu = document.querySelector('.user-menu');
    if (userMenu) {
        userMenu.addEventListener('click', toggleUserDropdown);
    }
    
    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function(event) {
        const userMenu = document.querySelector('.user-menu');
        const dropdown = document.getElementById('userDropdown');
        
        if (userMenu && dropdown && !userMenu.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });
    
    // Event listeners para botones de acción
    setupActionButtons();
}

function handleSectionClick(event) {
    const card = event.currentTarget;
    const sectionNumber = card.querySelector('.section-number').textContent;
    
    if (card.classList.contains('locked')) {
        showNotification('Esta sección está bloqueada. Completa las secciones anteriores primero.', 'warning');
        return;
    }
    
    navigateToSection(getSectionName(sectionNumber), projectId);
}

function handleSectionHover(event) {
    const card = event.currentTarget;
    if (!card.classList.contains('locked')) {
        card.style.transform = 'translateY(-5px) scale(1.02)';
    }
}

function handleSectionLeave(event) {
    const card = event.currentTarget;
    if (!card.classList.contains('locked')) {
        card.style.transform = 'translateY(0) scale(1)';
    }
}

function getSectionName(sectionNumber) {
    const sectionMap = {
        '1': 'mission',
        '2': 'vision',
        '3': 'values',
        '4': 'objectives'
    };
    return sectionMap[sectionNumber] || null;
}

function navigateToSection(sectionName, projectId) {
    if (!sectionName) {
        showNotification('Sección no disponible', 'error');
        return;
    }
    
    // Mostrar loading
    showNotification('Cargando sección...', 'info');
    
    // Redirigir a la sección específica
    setTimeout(() => {
        window.location.href = `${sectionName}.php?id=${projectId}`;
    }, 500);
}

function setupActionButtons() {
    // Botón de exportar proyecto
    const exportBtn = document.querySelector('button[onclick="exportProject()"]');
    if (exportBtn) {
        exportBtn.removeAttribute('onclick');
        exportBtn.addEventListener('click', exportProject);
    }
    
    // Botón de eliminar proyecto
    const deleteBtn = document.querySelector('button[onclick="deleteProject()"]');
    if (deleteBtn) {
        deleteBtn.removeAttribute('onclick');
        deleteBtn.addEventListener('click', deleteProject);
    }
}

function animateElements() {
    // Animar elementos al cargar la página
    const elementsToAnimate = document.querySelectorAll('.section-card, .project-header, .project-actions');
    
    elementsToAnimate.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            element.style.transition = 'all 0.6s ease';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}

function exportProject() {
    // Mostrar modal de confirmación
    const confirmExport = confirm('¿Deseas exportar este proyecto a PDF?');
    
    if (confirmExport) {
        showNotification('Preparando exportación...', 'info');
        
        // Simular proceso de exportación
        setTimeout(() => {
            // Aquí iría la lógica real de exportación
            showNotification('Proyecto exportado exitosamente', 'success');
            
            // Simular descarga
            const link = document.createElement('a');
            link.href = '#'; // Aquí iría la URL del PDF generado
            link.download = `${projectData.project_name.replace(/\s+/g, '_')}.pdf`;
            // link.click(); // Descomentar cuando esté implementada la exportación real
        }, 2000);
    }
}

function deleteProject() {
    // Mostrar modal de confirmación más elaborado
    const confirmDelete = confirm(`¿Estás seguro de que quieres eliminar el proyecto "${projectData.project_name}"?\n\nEsta acción no se puede deshacer y se perderán todos los datos del proyecto.`);
    
    if (confirmDelete) {
        // Segunda confirmación
        const finalConfirm = confirm('Esta es tu última oportunidad. ¿Realmente quieres eliminar este proyecto?');
        
        if (finalConfirm) {
            showNotification('Eliminando proyecto...', 'info');
            
            // Crear formulario para eliminar proyecto
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../../Controllers/ProjectController.php?action=delete';
            form.style.display = 'none';
            
            const projectIdInput = document.createElement('input');
            projectIdInput.type = 'hidden';
            projectIdInput.name = 'project_id';
            projectIdInput.value = projectId;
            form.appendChild(projectIdInput);
            
            document.body.appendChild(form);
            
            setTimeout(() => {
                form.submit();
            }, 1000);
        }
    }
}

// Función para mostrar notificaciones (reutilizada del dashboard)
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

// Funciones de monitoreo del progreso
function updateProjectProgress() {
    // Actualizar el progreso del proyecto en tiempo real
    fetch(`../../Controllers/ProjectController.php?action=get_progress&id=${projectId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                progressData = data.progress;
                updateProgressCircle();
                updateSectionStates();
            }
        })
        .catch(error => {
            console.error('Error al actualizar progreso:', error);
        });
}

// Función para auto-guardar (si se implementa)
function autoSave() {
    // Función para auto-guardar cambios en las secciones
    console.log('Auto-guardado activado');
}

// Event listeners para el teclado
document.addEventListener('keydown', function(e) {
    // Atajos de teclado útiles
    if (e.ctrlKey || e.metaKey) {
        switch(e.key) {
            case 's':
                e.preventDefault();
                autoSave();
                break;
            case 'e':
                e.preventDefault();
                exportProject();
                break;
            case 'Escape':
                // Cerrar cualquier modal abierto
                const dropdown = document.getElementById('userDropdown');
                if (dropdown && dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
                break;
        }
    }
    
    // Navegación con flechas entre secciones
    if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
        navigateWithArrows(e.key);
    }
});

function navigateWithArrows(direction) {
    const availableSections = document.querySelectorAll('.section-card.available, .section-card.completed');
    const currentSection = document.activeElement.closest('.section-card');
    
    if (currentSection && availableSections.length > 1) {
        const currentIndex = Array.from(availableSections).indexOf(currentSection);
        let newIndex;
        
        if (direction === 'ArrowRight') {
            newIndex = (currentIndex + 1) % availableSections.length;
        } else {
            newIndex = (currentIndex - 1 + availableSections.length) % availableSections.length;
        }
        
        availableSections[newIndex].focus();
    }
}

// Inicialización de tooltips para elementos específicos
function initProjectTooltips() {
    const tooltipElements = [
        {
            selector: '.progress-circle',
            text: 'Progreso general del proyecto'
        },
        {
            selector: '.section-card.locked',
            text: 'Esta sección se desbloqueará al completar las anteriores'
        }
    ];
    
    tooltipElements.forEach(item => {
        const elements = document.querySelectorAll(item.selector);
        elements.forEach(element => {
            element.setAttribute('title', item.text);
        });
    });
}

// Llamar a la inicialización de tooltips
document.addEventListener('DOMContentLoaded', initProjectTooltips);

// Funciones de utilidad
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function calculateTimeSpent() {
    const createdAt = new Date(projectData.created_at);
    const now = new Date();
    const diffTime = Math.abs(now - createdAt);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    return diffDays;
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

.section-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.section-card:focus {
    outline: 2px solid var(--secondary-color);
    outline-offset: 2px;
}
</style>
`;

document.head.insertAdjacentHTML('beforeend', animationStyles);