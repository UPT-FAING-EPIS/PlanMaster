/**
 * JavaScript para la Matriz CAME - PlanMaster
 * Funcionalidad dinámica para agregar, editar y eliminar acciones
 */

class CAMEMatrix {
    constructor() {
        this.projectId = window.projectId;
        this.baseUrl = window.baseUrl;
        this.isLoading = false;
        this.init();
    }

    init() {
        console.log('Inicializando Matriz CAME...');
        this.setupEventListeners();
        this.updateActionNumbers();
    }

    setupEventListeners() {
        // Listener para cambios en textareas (auto-save)
        document.addEventListener('blur', (e) => {
            if (e.target.classList.contains('action-textarea')) {
                this.saveAction(e.target);
            }
        }, true);

        // Listener para tecla Enter en textareas
        document.addEventListener('keydown', (e) => {
            if (e.target.classList.contains('action-textarea') && e.key === 'Enter' && e.ctrlKey) {
                e.target.blur(); // Trigger save
            }
        });
    }

    /**
     * Agregar nueva acción
     */
    addNewAction(actionType) {
        if (this.isLoading) return;

        this.isLoading = true;
        this.showLoading(`Agregando nueva acción para ${this.getActionTypeDescription(actionType)}...`);

        const formData = new FormData();
        formData.append('action', 'add_action');
        formData.append('project_id', this.projectId);
        formData.append('action_type', actionType);
        formData.append('action_text', '');

        console.log('Enviando solicitud para agregar acción:', {
            project_id: this.projectId,
            action_type: actionType
        });

        fetch(`${this.baseUrl}/Controllers/CAMEMatrixController.php`, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Respuesta recibida:', response);
            return response.text().then(text => {
                console.log('Texto de respuesta:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Error parseando JSON:', e);
                    throw new Error('Respuesta inválida del servidor: ' + text);
                }
            });
        })
        .then(data => {
            console.log('Datos procesados:', data);
            if (data.success) {
                this.hideMessage();
                this.reloadActionSection(actionType);
                this.showMessage('Acción agregada correctamente. Puede editarla ahora.', 'success');
            } else {
                this.showMessage(data.error || 'Error al agregar la acción', 'error');
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            this.showMessage('Error: ' + error.message, 'error');
        })
        .finally(() => {
            this.isLoading = false;
        });
    }

    /**
     * Guardar acción (auto-save)
     */
    saveAction(textarea) {
        if (this.isLoading) return;

        const actionId = textarea.dataset.actionId;
        const projectId = textarea.dataset.projectId;
        const actionType = textarea.dataset.actionType;
        const actionNumber = textarea.dataset.actionNumber;
        const actionText = textarea.value.trim();

        // No guardar si está vacío
        if (!actionText) {
            return;
        }

        this.isLoading = true;
        
        // Visual feedback
        textarea.style.borderColor = '#ed8936';
        textarea.style.backgroundColor = '#fef5e7';

        const formData = new FormData();
        formData.append('action', 'save_action');
        formData.append('project_id', projectId);
        formData.append('action_type', actionType);
        formData.append('action_number', actionNumber);
        formData.append('action_text', actionText);

        if (actionId) {
            formData.append('action_id', actionId);
        }

        fetch(`${this.baseUrl}/Controllers/CAMEMatrixController.php`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success feedback
                textarea.style.borderColor = '#48bb78';
                textarea.style.backgroundColor = '#f0fff4';
                
                setTimeout(() => {
                    textarea.style.borderColor = '';
                    textarea.style.backgroundColor = '';
                }, 1500);
            } else {
                // Error feedback
                textarea.style.borderColor = '#f56565';
                textarea.style.backgroundColor = '#fed7d7';
                this.showMessage(data.error || 'Error al guardar la acción', 'error');
                
                setTimeout(() => {
                    textarea.style.borderColor = '';
                    textarea.style.backgroundColor = '';
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            textarea.style.borderColor = '#f56565';
            textarea.style.backgroundColor = '#fed7d7';
            this.showMessage('Error de conexión al guardar', 'error');
            
            setTimeout(() => {
                textarea.style.borderColor = '';
                textarea.style.backgroundColor = '';
            }, 3000);
        })
        .finally(() => {
            this.isLoading = false;
        });
    }

    /**
     * Eliminar acción
     */
    deleteAction(actionId, actionType, actionNumber) {
        if (this.isLoading) return;

        const actionDescription = this.getActionTypeDescription(actionType);
        
        if (!confirm(`¿Está seguro de eliminar la Acción ${actionNumber} de "${actionDescription}"?\n\nEsta acción no se puede deshacer.`)) {
            return;
        }

        this.isLoading = true;
        this.showLoading('Eliminando acción...');

        const formData = new FormData();
        formData.append('action', 'delete_action');
        formData.append('project_id', this.projectId);
        formData.append('action_type', actionType);
        formData.append('action_number', actionNumber);

        fetch(`${this.baseUrl}/Controllers/CAMEMatrixController.php`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.hideMessage();
                this.reloadActionSection(actionType);
                this.showMessage('Acción eliminada correctamente', 'success');
            } else {
                this.showMessage(data.error || 'Error al eliminar la acción', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showMessage('Error de conexión al eliminar la acción', 'error');
        })
        .finally(() => {
            this.isLoading = false;
        });
    }

    /**
     * Recargar una sección de acciones
     */
    reloadActionSection(actionType) {
        // Recargar la página para mostrar los cambios
        // En una implementación más avanzada, esto se haría con AJAX
        window.location.reload();
    }

    /**
     * Actualizar numeración de acciones
     */
    updateActionNumbers() {
        const actionTypes = ['C', 'A', 'M', 'E'];
        
        actionTypes.forEach(type => {
            const actions = document.querySelectorAll(`#actions-${type} .came-action-item:not(.empty-state)`);
            actions.forEach((action, index) => {
                const numberElement = action.querySelector('.action-number');
                if (numberElement) {
                    numberElement.textContent = index + 1;
                }
                
                const textarea = action.querySelector('.action-textarea');
                if (textarea) {
                    textarea.dataset.actionNumber = index + 1;
                }
            });
        });
    }

    /**
     * Mostrar mensaje de estado
     */
    showMessage(message, type = 'info') {
        this.hideMessage();
        
        const messageContainer = document.getElementById('message-container');
        const messageDiv = document.createElement('div');
        messageDiv.className = `came-message ${type}`;
        
        const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';
        messageDiv.innerHTML = `${icon} ${message}`;
        
        messageContainer.appendChild(messageDiv);
        
        // Auto-hide after 5 seconds for success messages
        if (type === 'success') {
            setTimeout(() => this.hideMessage(), 5000);
        }
    }

    /**
     * Mostrar estado de carga
     */
    showLoading(message) {
        this.showMessage(`⏳ ${message}`, 'info');
    }

    /**
     * Ocultar mensaje
     */
    hideMessage() {
        const messageContainer = document.getElementById('message-container');
        messageContainer.innerHTML = '';
    }

    /**
     * Obtener descripción del tipo de acción
     */
    getActionTypeDescription(type) {
        const descriptions = {
            'C': 'Corregir las debilidades',
            'A': 'Afrontar las amenazas',
            'M': 'Mantener las fortalezas',
            'E': 'Explotar las oportunidades'
        };
        return descriptions[type] || 'Acción desconocida';
    }
}

// Funciones globales para los eventos del HTML
let cameMatrix;

function addNewAction(actionType) {
    if (cameMatrix) {
        cameMatrix.addNewAction(actionType);
    }
}

function saveAction(textarea) {
    if (cameMatrix) {
        cameMatrix.saveAction(textarea);
    }
}

function deleteAction(actionId, actionType, actionNumber) {
    if (cameMatrix) {
        cameMatrix.deleteAction(actionId, actionType, actionNumber);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    cameMatrix = new CAMEMatrix();
});

// Manejar cambios de visibilidad para re-enumerar acciones
document.addEventListener('visibilitychange', function() {
    if (!document.hidden && cameMatrix) {
        setTimeout(() => cameMatrix.updateActionNumbers(), 100);
    }
});