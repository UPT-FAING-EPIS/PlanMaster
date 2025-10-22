// JavaScript para Análisis DAFO - Identificación de Estrategias - PlanMaster
document.addEventListener('DOMContentLoaded', function() {
    
    // Inicializar la aplicación DAFO
    initDAFOApp();
    
    // Configurar eventos
    setupEventListeners();
    
    // Configurar cálculo automático
    setupAutoCalculation();
    
    // Animaciones de entrada
    animateElements();
    
    console.log('✅ Módulo de Análisis DAFO inicializado correctamente');
});

// ======= INICIALIZACIÓN ======= 
function initDAFOApp() {
    // Cargar datos guardados si existen
    loadSavedData();
    
    // Calcular totales iniciales
    calculateAllTotals();
    
    // Configurar tooltips
    setupTooltips();
    
    // Configurar atajos de teclado
    setupKeyboardShortcuts();
    
    console.log('Aplicación DAFO inicializada');
}

// ======= CONFIGURACIÓN DE EVENTOS =======
function setupEventListeners() {
    // Inputs de evaluación
    const evaluationInputs = document.querySelectorAll('.evaluation-input');
    evaluationInputs.forEach(input => {
        input.addEventListener('input', handleInputChange);
        input.addEventListener('blur', validateInput);
    });
    
    // Botón calcular
    const calculateBtn = document.getElementById('calculate-btn');
    if (calculateBtn) {
        calculateBtn.addEventListener('click', calculateStrategies);
    }
    
    // Botón reset
    const resetBtn = document.getElementById('reset-btn');
    if (resetBtn) {
        resetBtn.addEventListener('click', resetEvaluation);
    }
    
    // Formulario principal
    const evaluationForm = document.querySelector('.dafo-evaluation-form');
    if (evaluationForm) {
        evaluationForm.addEventListener('submit', handleFormSubmit);
    }
    
    // Prevenir pérdida de datos
    setupBeforeUnload();
}

// ======= GESTIÓN DE EVALUACIÓN DAFO =======
function handleInputChange(event) {
    const input = event.target;
    const value = parseInt(input.value) || 0;
    
    // Validar rango
    if (value < 0) input.value = 0;
    if (value > 4) input.value = 4;
    
    // Calcular totales
    calculateAllTotals();
    
    // Guardar datos localmente
    saveToLocalStorage();
    
    // Añadir efecto visual
    input.style.background = value > 0 ? '#f0f9ff' : 'white';
}

// ======= CÁLCULOS PRINCIPALES =======
function calculateAllTotals() {
    calculateTableTotals('fo');
    calculateTableTotals('fa');
    calculateTableTotals('do');
    calculateTableTotals('da');
    
    updateSynthesis();
}

function calculateTableTotals(tablePrefix) {
    // Calcular totales por fila
    for (let i = 1; i <= 4; i++) {
        const rowId = tablePrefix === 'fo' || tablePrefix === 'fa' ? `f${i}` : `d${i}`;
        const colPrefix = tablePrefix === 'fo' || tablePrefix === 'do' ? 'o' : 'a';
        
        let rowTotal = 0;
        for (let j = 1; j <= 4; j++) {
            const input = document.querySelector(`input[name="${tablePrefix}[${rowId}][${colPrefix}${j}]"]`);
            if (input) {
                rowTotal += parseInt(input.value) || 0;
            }
        }
        
        const totalCell = document.getElementById(`${tablePrefix}-${rowId}-total`);
        if (totalCell) {
            totalCell.textContent = rowTotal;
            totalCell.style.background = rowTotal > 0 ? 'linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%)' : '';
        }
    }
    
    // Calcular totales por columna
    const colPrefix = tablePrefix === 'fo' || tablePrefix === 'do' ? 'o' : 'a';
    for (let j = 1; j <= 4; j++) {
        let colTotal = 0;
        for (let i = 1; i <= 4; i++) {
            const rowId = tablePrefix === 'fo' || tablePrefix === 'fa' ? `f${i}` : `d${i}`;
            const input = document.querySelector(`input[name="${tablePrefix}[${rowId}][${colPrefix}${j}]"]`);
            if (input) {
                colTotal += parseInt(input.value) || 0;
            }
        }
        
        const totalCell = document.getElementById(`${tablePrefix}-${colPrefix}${j}-total`);
        if (totalCell) {
            totalCell.textContent = colTotal;
            totalCell.style.background = colTotal > 0 ? 'linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%)' : '';
        }
    }
    
    // Calcular gran total
    let grandTotal = 0;
    const rowPrefix = tablePrefix === 'fo' || tablePrefix === 'fa' ? 'f' : 'd';
    const colPrefix2 = tablePrefix === 'fo' || tablePrefix === 'do' ? 'o' : 'a';
    
    for (let i = 1; i <= 4; i++) {
        for (let j = 1; j <= 4; j++) {
            const input = document.querySelector(`input[name="${tablePrefix}[${rowPrefix}${i}][${colPrefix2}${j}]"]`);
            if (input) {
                grandTotal += parseInt(input.value) || 0;
            }
        }
    }
    
    const grandTotalCell = document.getElementById(`${tablePrefix}-grand-total`);
    if (grandTotalCell) {
        grandTotalCell.textContent = grandTotal;
        grandTotalCell.style.background = grandTotal > 0 ? 'linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%)' : '';
    }
}

function updateSynthesis() {
    const foTotal = parseInt(document.getElementById('fo-grand-total')?.textContent) || 0;
    const faTotal = parseInt(document.getElementById('fa-grand-total')?.textContent) || 0;
    const doTotal = parseInt(document.getElementById('do-grand-total')?.textContent) || 0;
    const daTotal = parseInt(document.getElementById('da-grand-total')?.textContent) || 0;
    
    // Actualizar tabla de síntesis
    document.getElementById('synthesis-fo').textContent = foTotal;
    document.getElementById('synthesis-fa').textContent = faTotal;
    document.getElementById('synthesis-do').textContent = doTotal;
    document.getElementById('synthesis-da').textContent = daTotal;
    
    // Determinar estrategia recomendada
    const maxValue = Math.max(foTotal, faTotal, doTotal, daTotal);
    let recommendedStrategy = '';
    let strategyDescription = '';
    
    if (maxValue === 0) {
        recommendedStrategy = 'Complete la evaluación para obtener recomendaciones';
        strategyDescription = '';
    } else if (foTotal === maxValue) {
        recommendedStrategy = '🚀 Estrategia Ofensiva Recomendada';
        strategyDescription = 'Su empresa debe adoptar estrategias de crecimiento aprovechando sus fortalezas para capitalizar las oportunidades del mercado.';
    } else if (faTotal === maxValue) {
        recommendedStrategy = '🛡️ Estrategia Defensiva Recomendada';
        strategyDescription = 'Su empresa está preparada para enfrentarse a las amenazas utilizando sus fortalezas internas.';
    } else if (doTotal === maxValue) {
        recommendedStrategy = '🔄 Estrategia de Reorientación Recomendada';
        strategyDescription = 'Su empresa debe trabajar en superar sus debilidades para poder aprovechar las oportunidades disponibles.';
    } else if (daTotal === maxValue) {
        recommendedStrategy = '⚠️ Estrategia de Supervivencia Recomendada';
        strategyDescription = 'Su empresa se encuentra en una situación crítica y debe minimizar debilidades mientras evita amenazas.';
    }
    
    // Actualizar recomendación
    const recommendationElement = document.getElementById('strategy-recommendation');
    if (recommendationElement) {
        if (maxValue > 0) {
            recommendationElement.innerHTML = `
                <h4 style="color: var(--color-primary); margin-bottom: 0.75rem;">${recommendedStrategy}</h4>
                <p style="line-height: 1.6; font-size: 1rem;">${strategyDescription}</p>
                <div style="margin-top: 1rem; padding: 1rem; background: rgba(99, 102, 241, 0.1); border-radius: 0.5rem; border-left: 4px solid var(--color-primary);">
                    <strong>Puntuación más alta:</strong> ${maxValue} puntos
                </div>
            `;
            recommendationElement.classList.add('highlight');
        } else {
            recommendationElement.innerHTML = '<p>Complete la evaluación para obtener su recomendación estratégica personalizada.</p>';
            recommendationElement.classList.remove('highlight');
        }
    }
    
    // Resaltar filas en tabla de síntesis
    highlightMaxScore(foTotal, faTotal, doTotal, daTotal, maxValue);
}

function highlightMaxScore(fo, fa, do_val, da, maxValue) {
    // Resetear estilos
    const scoreElements = document.querySelectorAll('.synthesis-score');
    scoreElements.forEach(el => {
        el.style.background = '#f8fafc';
        el.style.color = 'var(--color-primary)';
        el.style.fontWeight = '700';
    });
    
    // Resaltar el máximo
    if (maxValue > 0) {
        if (fo === maxValue) {
            document.getElementById('synthesis-fo').style.background = 'linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%)';
            document.getElementById('synthesis-fo').style.color = '#059669';
        }
        if (fa === maxValue) {
            document.getElementById('synthesis-fa').style.background = 'linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%)';
            document.getElementById('synthesis-fa').style.color = '#1d4ed8';
        }
        if (do_val === maxValue) {
            document.getElementById('synthesis-do').style.background = 'linear-gradient(135deg, #fef3c7 0%, #fde68a 100%)';
            document.getElementById('synthesis-do').style.color = '#d97706';
        }
        if (da === maxValue) {
            document.getElementById('synthesis-da').style.background = 'linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)';
            document.getElementById('synthesis-da').style.color = '#dc2626';
        }
    }
}

// ======= FUNCIONES PRINCIPALES =======
function calculateStrategies() {
    calculateAllTotals();
    showNotification('✅ Cálculos actualizados correctamente', 'success');
    
    // Añadir efecto visual a las tablas
    const tables = document.querySelectorAll('.evaluation-table');
    tables.forEach(table => {
        table.style.animation = 'pulse 0.6s ease-out';
        setTimeout(() => {
            table.style.animation = '';
        }, 600);
    });
}

function resetEvaluation() {
    if (confirm('¿Está seguro de que desea limpiar toda la evaluación? Esta acción no se puede deshacer.')) {
        // Limpiar todos los inputs
        const inputs = document.querySelectorAll('.evaluation-input');
        inputs.forEach(input => {
            input.value = 0;
            input.style.background = 'white';
        });
        
        // Recalcular totales
        calculateAllTotals();
        
        // Limpiar localStorage
        localStorage.removeItem('dafo_evaluation_data');
        
        showNotification('🔄 Evaluación limpiada correctamente', 'info');
    }
}

function setupAutoCalculation() {
    // Calcular automáticamente cuando hay cambios
    let calculationTimeout;
    
    const inputs = document.querySelectorAll('.evaluation-input');
    inputs.forEach(input => {
        input.addEventListener('input', () => {
            clearTimeout(calculationTimeout);
            calculationTimeout = setTimeout(() => {
                calculateAllTotals();
            }, 300);
        });
    });
}

// ======= GESTIÓN DE DATOS =======
function saveToLocalStorage() {
    const data = {};
    const inputs = document.querySelectorAll('.evaluation-input');
    
    inputs.forEach(input => {
        data[input.name] = input.value;
    });
    
    localStorage.setItem('dafo_evaluation_data', JSON.stringify(data));
}

function loadSavedData() {
    const savedData = localStorage.getItem('dafo_evaluation_data');
    if (savedData) {
        try {
            const data = JSON.parse(savedData);
            
            Object.keys(data).forEach(name => {
                const input = document.querySelector(`input[name="${name}"]`);
                if (input) {
                    input.value = data[name] || 0;
                    input.style.background = data[name] > 0 ? '#f0f9ff' : 'white';
                }
            });
            
            showNotification('📂 Datos previos restaurados', 'info');
        } catch (error) {
            console.error('Error loading saved data:', error);
        }
    }
}

function validateInput(event) {
    const input = event.target;
    const value = parseInt(input.value);
    
    // Validar rango para inputs de evaluación
    if (input.classList.contains('evaluation-input')) {
        if (isNaN(value) || value < 0 || value > 4) {
            input.value = Math.max(0, Math.min(4, value || 0));
            showInputError(input, 'Valor debe estar entre 0 y 4');
            return false;
        }
    }
    
    clearInputError(input);
    return true;
}

function showInputError(input, message) {
    input.style.borderColor = '#ef4444';
    showNotification(message, 'error');
}

function clearInputError(input) {
    input.style.borderColor = '#e2e8f0';
}

function handleFormSubmit(event) {
    event.preventDefault();
    
    // Validar que hay datos
    const inputs = document.querySelectorAll('.evaluation-input');
    let hasData = false;
    
    inputs.forEach(input => {
        if (parseInt(input.value) > 0) {
            hasData = true;
        }
    });
    
    if (!hasData) {
        showNotification('⚠️ Por favor, complete al menos algunos valores antes de guardar', 'warning');
        return;
    }
    
    // Simular envío (aquí iría la lógica de guardado real)
    showLoadingIndicator(true);
    
    setTimeout(() => {
        showLoadingIndicator(false);
        showNotification('✅ Evaluación DAFO guardada correctamente', 'success');
        
        // Limpiar datos locales tras guardado exitoso
        localStorage.removeItem('dafo_evaluation_data');
    }, 2000);
}

// ======= UTILIDADES =======
function setupBeforeUnload() {
    window.addEventListener('beforeunload', function(event) {
        const inputs = document.querySelectorAll('.evaluation-input');
        let hasData = false;
        
        inputs.forEach(input => {
            if (parseInt(input.value) > 0) {
                hasData = true;
            }
        });
        
        if (hasData) {
            event.preventDefault();
            event.returnValue = '¿Estás seguro de que quieres salir? Tienes datos sin guardar.';
            return event.returnValue;
        }
    });
}

function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(event) {
        // Ctrl + S para guardar
        if (event.ctrlKey && event.key === 's') {
            event.preventDefault();
            document.querySelector('.btn-save-evaluation').click();
        }
        
        // Ctrl + R para calcular
        if (event.ctrlKey && event.key === 'r') {
            event.preventDefault();
            calculateStrategies();
        }
    });
}

function setupTooltips() {
    const elementsWithTooltips = document.querySelectorAll('[title]');
    elementsWithTooltips.forEach(element => {
        element.addEventListener('mouseenter', function() {
            // Implementación simple de tooltip
            const title = this.getAttribute('title');
            if (title) {
                this.setAttribute('data-original-title', title);
                this.removeAttribute('title');
            }
        });
    });
}

// ======= ANIMACIONES =======
function animateElements() {
    const elements = document.querySelectorAll('.matrix-quadrant, .evaluation-table-container');
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

// ======= EFECTOS CSS DINÁMICOS =======
function addDynamicStyles() {
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        
        .evaluation-input:focus {
            transform: scale(1.05);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        
        .matrix-quadrant:hover {
            transform: translateY(-5px) scale(1.02);
        }
        
        .synthesis-score.highlight {
            animation: pulse 1s ease-in-out;
            font-size: 1.2rem;
        }
    `;
    document.head.appendChild(style);
}

// ======= NOTIFICACIONES Y UI =======
function showNotification(message, type = 'info') {
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
                border-radius: 0.75rem;
                color: white;
                z-index: 10000;
                animation: slideInRight 0.3s ease-out;
                max-width: 400px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                font-family: 'Poppins', sans-serif;
            }
            .notification-success { background: linear-gradient(135deg, #10b981, #059669); }
            .notification-error { background: linear-gradient(135deg, #ef4444, #dc2626); }
            .notification-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
            .notification-info { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
            .notification-close {
                background: none;
                border: none;
                color: white;
                font-size: 1.25rem;
                cursor: pointer;
                padding: 0;
                line-height: 1;
                opacity: 0.8;
                transition: opacity 0.3s;
            }
            .notification-close:hover { opacity: 1; }
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(styles);
    }
    
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
                    <p>Procesando evaluación DAFO...</p>
                </div>
            </div>
        `;
        
        const styles = document.createElement('style');
        styles.textContent = `
            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.7);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10001;
                backdrop-filter: blur(5px);
            }
            .loading-spinner {
                background: white;
                padding: 2rem;
                border-radius: 1rem;
                text-align: center;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            }
            .spinner {
                width: 50px;
                height: 50px;
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

// ======= INICIALIZACIÓN FINAL =======
document.addEventListener('DOMContentLoaded', addDynamicStyles);

console.log('📊 Módulo de Análisis DAFO cargado exitosamente');

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