// JavaScript para Identificación de Estrategias - PlanMaster
document.addEventListener('DOMContentLoaded', function() {
    
    // Inicializar la aplicación
    initStrategiesApp();
    
    // Configurar eventos
    setupEventListeners();
    
    // Configurar validación en tiempo real
    setupFormValidation();
    
    // Configurar guardado automático
    setupAutoSave();
    
    // Actualizar contadores iniciales
    updateStrategyCounts();
    
    // Animaciones de entrada
    animateElements();
    
    console.log('✅ Módulo de Identificación de Estrategias inicializado correctamente');
});

// ======= INICIALIZACIÓN ======= 
function initStrategiesApp() {
    // Cargar preferencias del usuario desde localStorage
    loadUserPreferences();
    
    // Configurar tooltips y ayudas
    setupTooltips();
    
    // Verificar si hay datos guardados localmente
    checkLocalStorage();
    
    // Configurar atajos de teclado
    setupKeyboardShortcuts();
}

// ======= CONFIGURACIÓN DE EVENTOS =======
function setupEventListeners() {
    // Botones para agregar estrategias
    const addButtons = document.querySelectorAll('.btn-add-strategy');
    addButtons.forEach(button => {
        button.addEventListener('click', function() {
            const strategyType = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            addStrategy(strategyType);
        });
    });
    
    // Botón de guardado automático toggle
    const autoSaveBtn = document.getElementById('auto-save-btn');
    if (autoSaveBtn) {
        autoSaveBtn.addEventListener('click', toggleAutoSave);
    }
    
    // Formulario principal
    const strategiesForm = document.querySelector('.strategies-form');
    if (strategiesForm) {
        strategiesForm.addEventListener('submit', handleFormSubmit);
    }
    
    // Detectar cambios en los inputs para guardado automático
    setupChangeDetection();
    
    // Prevenir pérdida de datos al salir
    setupBeforeUnload();
}

// ======= GESTIÓN DE ESTRATEGIAS =======
function addStrategy(type) {
    const container = document.getElementById(`${type}-strategies-container`);
    if (!container) {
        console.error(`Container para ${type} no encontrado`);
        return;
    }
    
    // Crear el HTML del nuevo elemento
    const strategyItem = createStrategyElement(type);
    
    // Insertar con animación
    container.insertAdjacentHTML('beforeend', strategyItem);
    
    // Animar la entrada del nuevo elemento
    const newItem = container.lastElementChild;
    animateNewElement(newItem);
    
    // Enfocar el primer input
    const firstInput = newItem.querySelector('.strategy-name');
    if (firstInput) {
        firstInput.focus();
    }
    
    // Actualizar contadores
    updateStrategyCounts();
    
    // Configurar eventos para el nuevo elemento
    setupElementEvents(newItem);
    
    // Mostrar feedback visual
    showNotification(`Nueva estrategia de ${getTypeLabel(type)} agregada`, 'success');
}

function createStrategyElement(type) {
    const placeholders = getPlaceholders(type);
    
    return `
        <div class="strategy-item" style="opacity: 0; transform: translateY(20px);">
            <div class="strategy-input-group">
                <input type="text" 
                       name="${type}_name[]" 
                       class="strategy-name" 
                       placeholder="${placeholders.name}"
                       required>
                <textarea name="${type}_description[]" 
                          class="strategy-description" 
                          placeholder="${placeholders.description}"
                          rows="3"
                          required></textarea>
                <select name="${type}_priority[]" class="strategy-priority" required>
                    <option value="alta">Alta Prioridad</option>
                    <option value="media" selected>Media Prioridad</option>
                    <option value="baja">Baja Prioridad</option>
                </select>
            </div>
            <button type="button" class="btn-remove-strategy" onclick="removeStrategy(this)" title="Eliminar estrategia">
                &times;
            </button>
        </div>
    `;
}

function removeStrategy(button) {
    const strategyItem = button.closest('.strategy-item');
    const strategyName = strategyItem.querySelector('.strategy-name').value || 'esta estrategia';
    
    // Confirmar eliminación si hay contenido
    if (strategyName.trim() && strategyName !== 'esta estrategia') {
        if (!confirm(`¿Estás seguro de que deseas eliminar "${strategyName}"?`)) {
            return;
        }
    }
    
    // Animar salida
    strategyItem.style.animation = 'fadeOutRight 0.3s ease-out forwards';
    
    setTimeout(() => {
        strategyItem.remove();
        updateStrategyCounts();
        showNotification('Estrategia eliminada correctamente', 'info');
    }, 300);
}

function setupElementEvents(element) {
    // Configurar eventos de cambio para guardado automático
    const inputs = element.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('input', debounce(handleInputChange, 500));
        input.addEventListener('blur', handleInputBlur);
    });
    
    // Configurar drag and drop para reordenar (funcionalidad avanzada)
    setupDragAndDrop(element);
}

// ======= CONTADORES Y ESTADÍSTICAS =======
function updateStrategyCounts() {
    const categories = ['competitive', 'growth', 'innovation', 'differentiation'];
    
    categories.forEach(category => {
        const items = document.querySelectorAll(`#${category}-strategies-container .strategy-item`);
        const count = items.length;
        const countElement = document.getElementById(`${category}-count`);
        
        if (countElement) {
            countElement.textContent = `${count} ${count === 1 ? 'estrategia' : 'estrategias'}`;
            
            // Agregar clase de estado basada en el conteo
            const summaryCard = countElement.closest('.summary-card');
            if (summaryCard) {
                summaryCard.classList.toggle('has-strategies', count > 0);
                summaryCard.classList.toggle('many-strategies', count > 3);
            }
        }
    });
    
    // Actualizar estadísticas generales
    updateGeneralStats();
}

function updateGeneralStats() {
    const totalStrategies = document.querySelectorAll('.strategy-item').length;
    const completedStrategies = document.querySelectorAll('.strategy-item').length; // Por simplicidad
    
    // Mostrar en la interfaz si hay elementos para ello
    const statsElement = document.querySelector('.general-stats');
    if (statsElement) {
        statsElement.innerHTML = `
            <span>Total: ${totalStrategies}</span>
            <span>Completadas: ${completedStrategies}</span>
        `;
    }
}

// ======= VALIDACIÓN DE FORMULARIO =======
function setupFormValidation() {
    const form = document.querySelector('.strategies-form');
    if (!form) return;
    
    // Validación en tiempo real
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', validateInput);
        input.addEventListener('input', clearValidationErrors);
    });
}

function validateInput(event) {
    const input = event.target;
    const value = input.value.trim();
    
    // Remover errores previos
    clearInputError(input);
    
    // Validaciones específicas
    if (!value) {
        showInputError(input, 'Este campo es requerido');
        return false;
    }
    
    if (input.classList.contains('strategy-name') && value.length < 3) {
        showInputError(input, 'El nombre debe tener al menos 3 caracteres');
        return false;
    }
    
    if (input.classList.contains('strategy-description') && value.length < 10) {
        showInputError(input, 'La descripción debe tener al menos 10 caracteres');
        return false;
    }
    
    // Validación exitosa
    showInputSuccess(input);
    return true;
}

function showInputError(input, message) {
    input.classList.add('error');
    
    // Crear o actualizar mensaje de error
    let errorElement = input.parentNode.querySelector('.error-message');
    if (!errorElement) {
        errorElement = document.createElement('span');
        errorElement.className = 'error-message';
        input.parentNode.appendChild(errorElement);
    }
    errorElement.textContent = message;
}

function showInputSuccess(input) {
    input.classList.remove('error');
    input.classList.add('success');
    clearInputError(input);
}

function clearInputError(input) {
    const errorElement = input.parentNode.querySelector('.error-message');
    if (errorElement) {
        errorElement.remove();
    }
}

function clearValidationErrors(event) {
    const input = event.target;
    input.classList.remove('error', 'success');
    clearInputError(input);
}

// ======= GUARDADO AUTOMÁTICO =======
let autoSaveEnabled = true;
let autoSaveTimeout;
let hasUnsavedChanges = false;

function setupAutoSave() {
    // Configurar intervalo de guardado automático
    setInterval(function() {
        if (autoSaveEnabled && hasUnsavedChanges) {
            saveStrategiesAuto();
        }
    }, 30000); // Cada 30 segundos
}

function setupChangeDetection() {
    const form = document.querySelector('.strategies-form');
    if (!form) return;
    
    // Detectar cambios en todos los inputs
    form.addEventListener('input', function() {
        hasUnsavedChanges = true;
        
        // Reiniciar el timeout de guardado automático
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            if (autoSaveEnabled) {
                saveStrategiesAuto();
            }
        }, 5000); // 5 segundos después del último cambio
    });
}

function saveStrategiesAuto() {
    if (!autoSaveEnabled) return;
    
    const formData = new FormData(document.querySelector('.strategies-form'));
    
    // Agregar identificador de guardado automático
    formData.append('auto_save', '1');
    
    // Mostrar indicador de guardado
    showAutoSaveIndicator('Guardando...');
    
    fetch(window.location.href.replace('strategies.php', '') + '../../Controllers/ProjectController.php?action=save_strategies_auto', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hasUnsavedChanges = false;
            showAutoSaveIndicator('✓ Guardado automáticamente', 'success');
        } else {
            showAutoSaveIndicator('⚠ Error al guardar', 'error');
        }
    })
    .catch(error => {
        console.error('Error en guardado automático:', error);
        showAutoSaveIndicator('⚠ Error de conexión', 'error');
    });
}

function toggleAutoSave() {
    autoSaveEnabled = !autoSaveEnabled;
    const statusElement = document.getElementById('auto-save-status');
    const buttonElement = document.getElementById('auto-save-btn');
    
    if (statusElement) {
        statusElement.textContent = autoSaveEnabled ? 'Activado' : 'Desactivado';
    }
    
    if (buttonElement) {
        buttonElement.classList.toggle('disabled', !autoSaveEnabled);
    }
    
    const message = autoSaveEnabled ? 'Guardado automático activado' : 'Guardado automático desactivado';
    showNotification(message, autoSaveEnabled ? 'success' : 'warning');
    
    // Guardar preferencia en localStorage
    localStorage.setItem('autoSaveEnabled', autoSaveEnabled);
}

function showAutoSaveIndicator(message, type = 'info') {
    // Crear o actualizar indicador
    let indicator = document.getElementById('auto-save-indicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'auto-save-indicator';
        indicator.className = 'auto-save-indicator';
        document.body.appendChild(indicator);
    }
    
    indicator.textContent = message;
    indicator.className = `auto-save-indicator ${type} show`;
    
    // Ocultar después de 3 segundos
    setTimeout(() => {
        indicator.classList.remove('show');
    }, 3000);
}

// ======= ENVÍO DEL FORMULARIO =======
function handleFormSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    
    // Validar formulario completo
    if (!validateForm(form)) {
        showNotification('Por favor, corrige los errores antes de continuar', 'error');
        return;
    }
    
    // Mostrar indicador de carga
    showLoadingIndicator(true);
    
    // Enviar formulario
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        showLoadingIndicator(false);
        
        if (data.success) {
            hasUnsavedChanges = false;
            showNotification('✅ Estrategias guardadas correctamente', 'success');
            
            // Opcional: redireccionar o actualizar interfaz
            setTimeout(() => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            }, 2000);
        } else {
            showNotification('❌ Error al guardar: ' + (data.message || 'Error desconocido'), 'error');
        }
    })
    .catch(error => {
        showLoadingIndicator(false);
        console.error('Error:', error);
        showNotification('❌ Error de conexión al guardar', 'error');
    });
}

function validateForm(form) {
    let isValid = true;
    const requiredInputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    
    requiredInputs.forEach(input => {
        if (!validateInput({ target: input })) {
            isValid = false;
        }
    });
    
    // Validación específica: al menos una estrategia en cada categoría
    const categories = ['competitive', 'growth', 'innovation', 'differentiation'];
    categories.forEach(category => {
        const items = document.querySelectorAll(`#${category}-strategies-container .strategy-item`);
        if (items.length === 0) {
            showNotification(`Agrega al menos una estrategia de ${getTypeLabel(category)}`, 'warning');
            isValid = false;
        }
    });
    
    return isValid;
}

// ======= UTILIDADES Y HELPERS =======
function getPlaceholders(type) {
    const placeholders = {
        competitive: {
            name: 'Ej: Liderazgo en costos',
            description: 'Ej: Optimizar procesos para reducir costos operativos y ofrecer precios más competitivos que la competencia...'
        },
        growth: {
            name: 'Ej: Expansión a nuevos mercados',
            description: 'Ej: Identificar y penetrar en mercados geográficos adyacentes con alta demanda para nuestros productos...'
        },
        innovation: {
            name: 'Ej: Desarrollo de productos digitales',
            description: 'Ej: Crear una plataforma digital que complemente nuestros servicios tradicionales y mejore la experiencia del cliente...'
        },
        differentiation: {
            name: 'Ej: Servicio al cliente 24/7',
            description: 'Ej: Implementar un sistema de atención al cliente disponible las 24 horas con respuesta inmediata y soporte especializado...'
        }
    };
    
    return placeholders[type] || { name: 'Nombre de la estrategia', description: 'Descripción detallada...' };
}

function getTypeLabel(type) {
    const labels = {
        competitive: 'competitiva',
        growth: 'crecimiento',
        innovation: 'innovación',
        differentiation: 'diferenciación'
    };
    
    return labels[type] || type;
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showNotification(message, type = 'info') {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <span class="notification-message">${message}</span>
        <button class="notification-close" onclick="this.parentElement.remove()">&times;</button>
    `;
    
    // Agregar estilos si no existen
    if (!document.getElementById('notification-styles')) {
        const styles = document.createElement('style');
        styles.id = 'notification-styles';
        styles.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem 1.5rem;
                border-radius: 0.5rem;
                color: white;
                z-index: 10000;
                animation: slideInRight 0.3s ease-out;
                max-width: 400px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
            }
            .notification-success { background: #10b981; }
            .notification-error { background: #ef4444; }
            .notification-warning { background: #f59e0b; }
            .notification-info { background: #3b82f6; }
            .notification-close {
                background: none;
                border: none;
                color: white;
                font-size: 1.25rem;
                cursor: pointer;
                padding: 0;
                line-height: 1;
            }
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(styles);
    }
    
    // Agregar al DOM
    document.body.appendChild(notification);
    
    // Remover automáticamente después de 5 segundos
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideInRight 0.3s ease-out reverse';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

function showLoadingIndicator(show) {
    let indicator = document.getElementById('loading-indicator');
    
    if (show && !indicator) {
        indicator = document.createElement('div');
        indicator.id = 'loading-indicator';
        indicator.innerHTML = `
            <div class="loading-overlay">
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <p>Guardando estrategias...</p>
                </div>
            </div>
        `;
        
        // Agregar estilos
        const styles = document.createElement('style');
        styles.textContent = `
            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10001;
            }
            .loading-spinner {
                background: white;
                padding: 2rem;
                border-radius: 1rem;
                text-align: center;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            }
            .spinner {
                width: 40px;
                height: 40px;
                border: 4px solid #f3f3f3;
                border-top: 4px solid #6366f1;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 1rem;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(styles);
        document.body.appendChild(indicator);
    } else if (!show && indicator) {
        indicator.remove();
    }
}

// ======= FUNCIONALIDADES ADICIONALES =======
function animateElements() {
    // Animar elementos existentes al cargar
    const elements = document.querySelectorAll('.strategy-section, .strategies-summary');
    elements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            element.style.transition = 'all 0.6s ease-out';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

function animateNewElement(element) {
    // Animar nuevo elemento agregado
    element.style.opacity = '0';
    element.style.transform = 'translateY(20px)';
    
    requestAnimationFrame(() => {
        element.style.transition = 'all 0.4s ease-out';
        element.style.opacity = '1';
        element.style.transform = 'translateY(0)';
    });
}

function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(event) {
        // Ctrl + S para guardar
        if (event.ctrlKey && event.key === 's') {
            event.preventDefault();
            document.querySelector('.btn-save-strategies').click();
        }
        
        // Ctrl + Shift + A para agregar estrategia competitiva
        if (event.ctrlKey && event.shiftKey && event.key === 'A') {
            event.preventDefault();
            addStrategy('competitive');
        }
    });
}

function setupBeforeUnload() {
    window.addEventListener('beforeunload', function(event) {
        if (hasUnsavedChanges) {
            event.preventDefault();
            event.returnValue = '¿Estás seguro de que quieres salir? Tienes cambios sin guardar.';
            return event.returnValue;
        }
    });
}

function loadUserPreferences() {
    // Cargar configuración de guardado automático
    const autoSavePref = localStorage.getItem('autoSaveEnabled');
    if (autoSavePref !== null) {
        autoSaveEnabled = JSON.parse(autoSavePref);
        const statusElement = document.getElementById('auto-save-status');
        if (statusElement) {
            statusElement.textContent = autoSaveEnabled ? 'Activado' : 'Desactivado';
        }
    }
}

function checkLocalStorage() {
    // Verificar si hay borradores guardados localmente
    const draftKey = `strategies_draft_${window.location.pathname}`;
    const draft = localStorage.getItem(draftKey);
    
    if (draft) {
        // Mostrar opción para restaurar borrador
        const restoreOption = confirm('Se encontró un borrador guardado. ¿Deseas restaurarlo?');
        if (restoreOption) {
            restoreDraft(JSON.parse(draft));
        } else {
            localStorage.removeItem(draftKey);
        }
    }
}

function setupTooltips() {
    // Configurar tooltips para botones y elementos
    const elementsWithTooltips = document.querySelectorAll('[title]');
    elementsWithTooltips.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function setupDragAndDrop(element) {
    // Funcionalidad futura para reordenar estrategias
    element.setAttribute('draggable', 'true');
    element.addEventListener('dragstart', handleDragStart);
    element.addEventListener('dragover', handleDragOver);
    element.addEventListener('drop', handleDrop);
}

// Funciones auxiliares para drag and drop
function handleDragStart(event) {
    // Implementación futura
}

function handleDragOver(event) {
    event.preventDefault();
}

function handleDrop(event) {
    // Implementación futura
}

function handleInputChange(event) {
    // Guardar borrador en localStorage
    saveDraft();
}

function handleInputBlur(event) {
    // Validar input cuando pierde el foco
    validateInput(event);
}

function saveDraft() {
    const formData = new FormData(document.querySelector('.strategies-form'));
    const draftData = {};
    
    for (let [key, value] of formData.entries()) {
        if (!draftData[key]) {
            draftData[key] = [];
        }
        draftData[key].push(value);
    }
    
    const draftKey = `strategies_draft_${window.location.pathname}`;
    localStorage.setItem(draftKey, JSON.stringify(draftData));
}

function restoreDraft(draftData) {
    // Implementar restauración de borrador
    console.log('Restaurando borrador:', draftData);
    // Esta función se implementaría completamente según las necesidades específicas
}

// Funciones auxiliares para tooltips
function showTooltip(event) {
    // Implementación de tooltips personalizada
}

function hideTooltip(event) {
    // Ocultar tooltip
}

// ======= FUNCIONES GLOBALES (para uso en HTML) =======
window.addStrategy = addStrategy;
window.removeStrategy = removeStrategy;
window.updateStrategyCounts = updateStrategyCounts;
window.saveStrategiesAuto = saveStrategiesAuto;

// ======= FUNCIONES PARA CSS DINÁMICO =======
function addDynamicStyles() {
    const style = document.createElement('style');
    style.textContent = `
        .strategy-item.error {
            border-color: #ef4444 !important;
            background: #fef2f2;
        }
        .strategy-item.success {
            border-color: #10b981 !important;
            background: #f0fdf4;
        }
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }
        .summary-card.has-strategies {
            border-width: 3px;
            transform: scale(1.02);
        }
        .summary-card.many-strategies .strategy-count {
            font-weight: 600;
            color: var(--color-success);
        }
    `;
    document.head.appendChild(style);
}

// Agregar estilos dinámicos al cargar
document.addEventListener('DOMContentLoaded', addDynamicStyles);

console.log('📋 Módulo de Estrategias cargado exitosamente');