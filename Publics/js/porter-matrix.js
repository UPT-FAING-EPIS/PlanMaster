// ===============================================
// MATRIZ DE PORTER - JAVASCRIPT
// Sistema de Plan Estratégico - PlanMaster  
// ===============================================

// Variables globales
let porterData = {
    rivalidad: [],
    barreras_entrada: [], 
    poder_clientes: [],
    productos_sustitutivos: []
};

let fodaItems = {
    oportunidades: [],
    amenazas: []
};

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    initializePorterMatrix();
    setupEventListeners();
    loadExistingData();
    
    // Calcular resultados iniciales
    setTimeout(() => {
        calculateResults();
    }, 100);
});

// Configurar event listeners
function setupEventListeners() {
    // Event listener para cambios en radio buttons
    document.addEventListener('change', function(e) {
        if (e.target.type === 'radio' && e.target.name.startsWith('factor_')) {
            updateFactorValue(e.target);
            calculateResults();
        }
    });

    // Auto-guardar deshabilitado por ahora
    // setInterval(() => {
    //     if (hasUnsavedChanges()) {
    //         autoSavePorter();
    //     }
    // }, 30000);
}

// Inicializar matriz de Porter
function initializePorterMatrix() {
    // Inicialización silenciosa
}

// Cargar datos existentes
function loadExistingData() {
    
    if (typeof EXISTING_PORTER_DATA !== 'undefined' && EXISTING_PORTER_DATA) {
        // Cargar análisis de factores si existen
        if (EXISTING_PORTER_DATA.analysis) {
            for (const [category, factors] of Object.entries(EXISTING_PORTER_DATA.analysis)) {
                if (Array.isArray(factors)) {
                    factors.forEach(factor => {
                        const factorId = category + '_' + factor.factor_name.replace(/ /g, '_');
                        const radioButton = document.querySelector(`input[name="factor_${factorId}"][value="${factor.selected_value}"]`);
                        if (radioButton) {
                            radioButton.checked = true;
                        }
                    });
                }
            }
        }
        
    }
}

// Cargar datos existentes
function loadPorterData(data) {
    if (data.analysis) {
        porterData = data.analysis;
    }
    
    if (data.foda) {
        fodaItems = data.foda;
    }
    
    // Actualizar los radio buttons con los valores cargados
    setTimeout(() => {
        updateRadioButtons();
    }, 100);
}

// Inicializar datos por defecto
function initializeDefaultData() {
    // Los datos por defecto se manejan desde el servidor/modelo
    // Aquí solo inicializamos las estructuras vacías si es necesario
}

// Actualizar radio buttons con datos cargados
function updateRadioButtons() {
    Object.keys(porterData).forEach(category => {
        porterData[category].forEach(factor => {
            const radioName = `factor_${category}_${factor.id || factor.factor_name.replace(/\s+/g, '_')}`;
            const selectedRadio = document.querySelector(`input[name="${radioName}"][value="${factor.selected_value}"]`);
            if (selectedRadio) {
                selectedRadio.checked = true;
            }
        });
    });
}

// Actualizar valor de factor cuando cambia radio button
function updateFactorValue(radioElement) {
    const factorName = radioElement.name;
    const selectedValue = parseInt(radioElement.value);
    
    // Efecto visual
    animateFactorUpdate(radioElement);
}

// Animación cuando se actualiza un factor
function animateFactorUpdate(radioElement) {
    const row = radioElement.closest('tr');
    if (row) {
        row.style.backgroundColor = '#dbeafe';
        row.style.transform = 'scale(1.02)';
        
        setTimeout(() => {
            row.style.backgroundColor = '';
            row.style.transform = '';
        }, 300);
    }
}

// Calcular resultados del análisis Porter
function calculateResults() {
    let totalScore = 0;
    let totalFactors = 0;
    let maxPossibleScore = 0;
    
    // Obtener todos los radio buttons seleccionados
    const selectedRadios = document.querySelectorAll('input[type="radio"]:checked');
    
    selectedRadios.forEach(radio => {
        if (radio.name.startsWith('responses[')) {
            const value = parseInt(radio.value) || 3;
            totalScore += value;
            totalFactors++;
            maxPossibleScore += 5; // Máximo valor posible por factor
        }
    });
    
    // Si no hay factores, obtener total de factores disponibles con valor por defecto
    if (totalFactors === 0) {
        const allRadios = document.querySelectorAll('input[type="radio"][name^="responses["]');
        const factorNames = new Set();
        
        allRadios.forEach(radio => {
            factorNames.add(radio.name);
        });
        
        totalFactors = factorNames.size;
        totalScore = totalFactors * 3; // Valor por defecto
        maxPossibleScore = totalFactors * 5;
    }
    
    if (totalFactors === 0) return;
    
    // Calcular métricas
    const averageScore = totalScore / totalFactors;
    const percentage = (totalScore / maxPossibleScore) * 100;
    
    // Determinar nivel competitivo y recomendación
    let competitiveness, recommendation, recommendationClass;
    
    if (percentage >= 80) {
        competitiveness = "Excelente";
        recommendation = "Estamos en una situación excelente para la empresa.";
        recommendationClass = "excellent";
    } else if (percentage >= 60) {
        competitiveness = "Favorable";
        recommendation = "La situación actual del mercado es favorable a la empresa.";
        recommendationClass = "favorable";
    } else if (percentage >= 40) {
        competitiveness = "Medio";
        recommendation = "Estamos en un mercado de competitividad relativamente alta, pero con ciertas modificaciones en el producto y la política comercial de la empresa, podría encontrarse un nicho de mercado.";
        recommendationClass = "medium";
    } else {
        competitiveness = "Hostil";
        recommendation = "Estamos en un mercado altamente competitivo, en el que es muy difícil hacerse un hueco en el mercado.";
        recommendationClass = "hostile";
    }
    
    // Actualizar resultados en la UI
    updateResultsDisplay({
        totalScore: totalScore.toFixed(1),
        averageScore: averageScore.toFixed(2),
        maxScore: maxPossibleScore,
        percentage: percentage.toFixed(1),
        competitiveness,
        recommendation,
        recommendationClass
    });
}

// Actualizar display de resultados
function updateResultsDisplay(results) {
    // Actualizar cards de resultados
    const totalScoreElement = document.getElementById('total-score');
    const averageScoreElement = document.getElementById('average-score');
    const percentageElement = document.getElementById('percentage-score');
    const competitivenessElement = document.getElementById('competitiveness-level');
    
    if (totalScoreElement) totalScoreElement.textContent = results.totalScore;
    if (averageScoreElement) averageScoreElement.textContent = results.averageScore;
    if (percentageElement) percentageElement.textContent = results.percentage + '%';
    if (competitivenessElement) competitivenessElement.textContent = results.competitiveness;
    
    // Actualizar recomendación
    const recommendationElement = document.getElementById('porter-recommendation');
    if (recommendationElement) {
        recommendationElement.textContent = results.recommendation;
        recommendationElement.className = `recommendation-text ${results.recommendationClass}`;
    }
    
    // Actualizar color de cards según el nivel
    updateResultCardsColor(results.recommendationClass);
}

// Actualizar colores de cards según el nivel
function updateResultCardsColor(level) {
    const cards = document.querySelectorAll('.result-card');
    cards.forEach(card => {
        card.classList.remove('excellent', 'favorable', 'medium', 'hostile');
        card.classList.add(level);
    });
}

// Renderizar matriz de Porter
function renderPorterMatrix() {
    // Esta función se ejecuta después de que el HTML base ya está renderizado por PHP
    // Aquí solo agregamos interactividad adicional si es necesario
}

// Renderizar sección FODA
function renderFodaSection() {
    renderOportunidades();
    renderAmenazas();
}

// Renderizar oportunidades
function renderOportunidades() {
    const container = document.getElementById('oportunidades-container');
    if (!container) return;
    
    container.innerHTML = '';
    
    // Agregar oportunidades existentes
    if (fodaItems.oportunidades && fodaItems.oportunidades.length > 0) {
        fodaItems.oportunidades.forEach((oportunidad, index) => {
            addFodaItem('oportunidades', oportunidad.item_text || oportunidad.text || '', index);
        });
    }
    
    // Agregar al menos un campo vacío
    if (!fodaItems.oportunidades || fodaItems.oportunidades.length === 0) {
        addFodaItem('oportunidades', '', 0);
    }
}

// Renderizar amenazas
function renderAmenazas() {
    const container = document.getElementById('amenazas-container');
    if (!container) return;
    
    container.innerHTML = '';
    
    // Agregar amenazas existentes
    if (fodaItems.amenazas && fodaItems.amenazas.length > 0) {
        fodaItems.amenazas.forEach((amenaza, index) => {
            addFodaItem('amenazas', amenaza.item_text || amenaza.text || '', index);
        });
    }
    
    // Agregar al menos un campo vacío
    if (!fodaItems.amenazas || fodaItems.amenazas.length === 0) {
        addFodaItem('amenazas', '', 0);
    }
}

// Agregar elemento FODA
function addFodaItem(type, text = '', index = null) {
    const container = document.getElementById(`${type}-container`);
    if (!container) return;
    
    const itemDiv = document.createElement('div');
    itemDiv.className = 'foda-item';
    itemDiv.innerHTML = `
        <textarea 
            name="${type}[]" 
            placeholder="Escriba una ${type.slice(0, -1)}..." 
            maxlength="500"
            data-index="${index !== null ? index : container.children.length}"
        >${text}</textarea>
        <button type="button" class="btn-remove-foda" onclick="removeFodaItem(this, '${type}')">
            ❌
        </button>
    `;
    
    container.appendChild(itemDiv);
    
    // Focus en el nuevo textarea si está vacío
    if (!text) {
        const textarea = itemDiv.querySelector('textarea');
        textarea.focus();
    }
}

// Remover elemento FODA
function removeFodaItem(button, type) {
    const item = button.closest('.foda-item');
    const container = document.getElementById(`${type}-container`);
    
    // No permitir eliminar si es el único elemento
    if (container.children.length <= 1) {
        const textarea = item.querySelector('textarea');
        textarea.value = '';
        textarea.focus();
        return;
    }
    
    // Animación de salida
    item.style.transform = 'translateX(-100%)';
    item.style.opacity = '0';
    
    setTimeout(() => {
        item.remove();
        updateFodaIndices(type);
    }, 300);
}

// Actualizar índices de elementos FODA
function updateFodaIndices(type) {
    const container = document.getElementById(`${type}-container`);
    Array.from(container.children).forEach((item, index) => {
        const textarea = item.querySelector('textarea');
        textarea.setAttribute('data-index', index);
    });
}

// Guardar análisis Porter - SIMPLE COMO VALUE CHAIN
function savePorterAnalysis() {
    // El formulario se envía normalmente, no necesita AJAX
    // Esta función se mantiene por compatibilidad pero no se usa
    const form = document.getElementById('porter-form');
    if (form) {
        form.submit();
    }
}

// Auto-guardar
function autoSavePorter() {
    savePorterAnalysis();
}

// Verificar si hay cambios sin guardar
function hasUnsavedChanges() {
    // Implementar lógica para detectar cambios
    return true; // Por simplicidad, siempre retorna true
}

// Resetear estado de cambios sin guardar
function resetUnsavedChanges() {
    // Implementar reset del estado
}

// Mostrar indicador de guardado
function showSaveIndicator(message) {
    let indicator = document.getElementById('save-indicator');
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'save-indicator';
        indicator.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #3b82f6;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            z-index: 1000;
            font-weight: 600;
        `;
        document.body.appendChild(indicator);
    }
    indicator.textContent = message;
    indicator.style.display = 'block';
}

// Ocultar indicador de guardado
function hideSaveIndicator() {
    const indicator = document.getElementById('save-indicator');
    if (indicator) {
        indicator.style.display = 'none';
    }
}

// Mostrar alerta
function showAlert(message, type = 'info') {
    // Remover alertas existentes
    const existingAlerts = document.querySelectorAll('.porter-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Crear nueva alerta
    const alert = document.createElement('div');
    alert.className = `porter-alert ${type}`;
    alert.textContent = message;
    
    // Insertar después del header
    const header = document.querySelector('.porter-header');
    if (header && header.nextSibling) {
        header.parentNode.insertBefore(alert, header.nextSibling);
    } else {
        document.querySelector('.porter-content').prepend(alert);
    }
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
    
    // Scroll a la alerta
    alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Funciones auxiliares para los botones
function addOportunidad() {
    addFodaItem('oportunidades');
}

function addAmenaza() {
    addFodaItem('amenazas');
}

// Agregar item FODA a un container específico
function addFodaItemToContainer(type, text = '') {
    const container = document.getElementById(`${type}-container`);
    if (!container) return;
    
    const fodaItem = document.createElement('div');
    fodaItem.className = 'foda-item';
    
    fodaItem.innerHTML = `
        <textarea name="${type}[]" placeholder="Escriba una ${type.slice(0, -1)}..." maxlength="500">${text}</textarea>
        <button type="button" class="btn-remove-foda" onclick="removeFodaItem(this, '${type}')">❌</button>
    `;
    
    container.appendChild(fodaItem);
}

// Exportar funciones globales
window.savePorterAnalysis = savePorterAnalysis;
window.addOportunidad = addOportunidad;
window.addAmenaza = addAmenaza;
window.removeFodaItem = removeFodaItem;