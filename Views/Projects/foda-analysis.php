<?php
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../config/url_config.php';

// Verificar que el usuario esté logueado
AuthController::requireLogin();

// Obtener el ID del proyecto
$project_id = intval($_GET['id'] ?? 0);
if ($project_id === 0) {
    header("Location: " . getBaseUrl() . "/Views/Users/projects.php");
    exit();
}

// Obtener datos del proyecto y del usuario
$projectController = new ProjectController();
$project = $projectController->getProject($project_id);
$user = AuthController::getCurrentUser();

if (!$project) {
    $_SESSION['error'] = "Proyecto no encontrado";
    header("Location: " . getBaseUrl() . "/Views/Users/projects.php");
    exit();
}

// Obtener análisis FODA existente
$fodaData = $projectController->getFodaAnalysis($project_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis Interno y Externo - <?php echo htmlspecialchars($project['project_name']); ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_project.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_foda.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo getBaseUrl(); ?>/Resources/favicon.ico">
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>
    
    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container">
            <!-- Información del proyecto -->
            <div class="project-header">
                <div class="project-info">
                    <h1>📊 Análisis Interno y Externo</h1>
                    <p class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></p>
                    <p class="company-name"><?php echo htmlspecialchars($project['company_name']); ?></p>
                </div>
            </div>
            
            <!-- Contexto del análisis FODA -->
            <div class="context-box">
                <h3>🎯 Análisis Interno y Externo</h3>
                <p>Para determinar la estrategia más conveniente, realizaremos un análisis interno y externo de su empresa para obtener una matriz FODA e identificar los factores clave.</p>
                <p><strong>Este análisis le permitirá detectar:</strong></p>
                <ul>
                    <li><strong>Factores de éxito:</strong> Fortalezas y Oportunidades</li>
                    <li><strong>Aspectos a gestionar:</strong> Debilidades y Amenazas</li>
                </ul>
            </div>
            
            <!-- Formulario de análisis FODA -->
            <form action="<?php echo getBaseUrl(); ?>/Controllers/ProjectController.php?action=save_foda" method="POST" class="project-form">
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                
                <div class="foda-container">
                    <!-- ANÁLISIS EXTERNO -->
                    <!-- Oportunidades -->
                    <div class="foda-section oportunidades">
                        <h3>🌟 Oportunidades</h3>
                        <p>
                            Aspectos que pueden mejorar la rentabilidad, aumentar ventas y fortalecer la ventaja competitiva.
                        </p>
                        <div id="oportunidades-container">
                            <?php if (!empty($fodaData['oportunidades'])): ?>
                                <?php foreach ($fodaData['oportunidades'] as $index => $oportunidad): ?>
                                    <div class="foda-item">
                                        <input type="text" name="oportunidades[]" class="foda-input" 
                                               placeholder="Ejemplo: Crecimiento del mercado digital"
                                               value="<?php echo htmlspecialchars($oportunidad['item_text']); ?>">
                                        <button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="foda-item">
                                    <input type="text" name="oportunidades[]" class="foda-input" 
                                           placeholder="Ejemplo: Crecimiento del mercado digital">
                                    <button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button>
                                </div>
                                <div class="foda-item">
                                    <input type="text" name="oportunidades[]" class="foda-input" 
                                           placeholder="Ejemplo: Nuevas tecnologías disponibles">
                                    <button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button>
                                </div>
                                <div class="foda-item">
                                    <input type="text" name="oportunidades[]" class="foda-input" 
                                           placeholder="Ejemplo: Cambios en regulaciones favorables">
                                    <button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn-add" onclick="addItem('oportunidades')">
                            <span>+</span> Agregar Oportunidad
                        </button>
                    </div>
                    
                    <!-- Amenazas -->
                    <div class="foda-section amenazas">
                        <h3>⚠️ Amenazas</h3>
                        <p>
                            Fuerzas del mercado que pueden impedir el crecimiento, reducir eficacia o incrementar riesgos.
                        </p>
                        <div id="amenazas-container">
                            <?php if (!empty($fodaData['amenazas'])): ?>
                                <?php foreach ($fodaData['amenazas'] as $index => $amenaza): ?>
                                    <div class="foda-item">
                                        <input type="text" name="amenazas[]" class="foda-input" 
                                               placeholder="Ejemplo: Entrada de nuevos competidores"
                                               value="<?php echo htmlspecialchars($amenaza['item_text']); ?>">
                                        <button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="foda-item">
                                    <input type="text" name="amenazas[]" class="foda-input" 
                                           placeholder="Ejemplo: Entrada de nuevos competidores">
                                    <button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button>
                                </div>
                                <div class="foda-item">
                                    <input type="text" name="amenazas[]" class="foda-input" 
                                           placeholder="Ejemplo: Crisis económica">
                                    <button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button>
                                </div>
                                <div class="foda-item">
                                    <input type="text" name="amenazas[]" class="foda-input" 
                                           placeholder="Ejemplo: Cambios tecnológicos disruptivos">
                                    <button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn-add" onclick="addItem('amenazas')">
                            <span>+</span> Agregar Amenaza
                        </button>
                    </div>
                    
                    <!-- ANÁLISIS INTERNO -->
                    <!-- Fortalezas -->
                    <div class="foda-section fortalezas">
                        <h3>💪 Fortalezas</h3>
                        <p>
                            Capacidades, recursos y ventajas competitivas que posee la empresa.
                        </p>
                        <div id="fortalezas-container">
                            <?php if (!empty($fodaData['fortalezas'])): ?>
                                <?php foreach ($fodaData['fortalezas'] as $index => $fortaleza): ?>
                                    <div class="foda-item">
                                        <input type="text" name="fortalezas[]" class="foda-input" 
                                               placeholder="Ejemplo: Equipo altamente capacitado"
                                               value="<?php echo htmlspecialchars($fortaleza['item_text']); ?>">
                                        <button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="foda-item">
                                    <input type="text" name="fortalezas[]" class="foda-input" 
                                           placeholder="Ejemplo: Equipo altamente capacitado">
                                    <button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button>
                                </div>
                                <div class="foda-item">
                                    <input type="text" name="fortalezas[]" class="foda-input" 
                                           placeholder="Ejemplo: Buena reputación en el mercado">
                                    <button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button>
                                </div>
                                <div class="foda-item">
                                    <input type="text" name="fortalezas[]" class="foda-input" 
                                           placeholder="Ejemplo: Tecnología innovadora">
                                    <button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn-add" onclick="addItem('fortalezas')">
                            <span>+</span> Agregar Fortaleza
                        </button>
                    </div>
                    
                    <!-- Debilidades -->
                    <div class="foda-section debilidades">
                        <h3>⚡ Debilidades</h3>
                        <p>
                            Aspectos que limitan o reducen la capacidad de desarrollo de la empresa.
                        </p>
                        <div id="debilidades-container">
                            <?php if (!empty($fodaData['debilidades'])): ?>
                                <?php foreach ($fodaData['debilidades'] as $index => $debilidad): ?>
                                    <div class="foda-item">
                                        <input type="text" name="debilidades[]" class="foda-input" 
                                               placeholder="Ejemplo: Recursos financieros limitados"
                                               value="<?php echo htmlspecialchars($debilidad['item_text']); ?>">
                                        <button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="foda-item">
                                    <input type="text" name="debilidades[]" class="foda-input" 
                                           placeholder="Ejemplo: Recursos financieros limitados">
                                    <button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button>
                                </div>
                                <div class="foda-item">
                                    <input type="text" name="debilidades[]" class="foda-input" 
                                           placeholder="Ejemplo: Falta de presencia digital">
                                    <button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button>
                                </div>
                                <div class="foda-item">
                                    <input type="text" name="debilidades[]" class="foda-input" 
                                           placeholder="Ejemplo: Procesos internos ineficientes">
                                    <button type="button" class="btn-remove" onclick="removeItem(this)">&times;</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn-add" onclick="addItem('debilidades')">
                            <span>+</span> Agregar Debilidad
                        </button>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="form-actions">
                    <div class="actions-left">
                        <a href="<?php echo getBaseUrl(); ?>/Views/Projects/objectives.php?project_id=<?php echo $project_id; ?>" 
                           class="btn-back">
                            ← Objetivos
                        </a>
                    </div>
                    <div class="actions-right">
                        <button type="submit" class="btn-save">
                            💾 Guardar y Continuar
                        </button>
                        <button type="submit" name="save_and_exit" class="btn-save-exit">
                            💾 Guardar y Salir
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Navegación a siguiente sección -->
            <?php if ($projectController->isFodaComplete($project_id)): ?>
            <div class="next-section">
                <div class="completion-message">
                    <h3>✅ Análisis Interno y Externo Completado</h3>
                    <p>Has completado exitosamente el análisis FODA de tu empresa.</p>
                    
                    <div class="next-section-info">
                        <h4>Siguiente paso: Cadena de Valor</h4>
                        <p>El siguiente paso es realizar el <strong>Diagnóstico de Cadena de Valor</strong> para evaluar tus procesos comerciales.</p>
                        <div class="action-buttons">
                            <a href="value-chain.php?project_id=<?php echo $project_id; ?>" class="btn btn-continue">
                                ⛓️ Continuar a Cadena de Valor
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="incomplete-message">
                <p><strong>⚠️ Complete el análisis</strong></p>
                <p>Una vez completadas todas las secciones, podrás continuar con la Cadena de Valor.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- JavaScript -->
    <script>
        // Debug: Confirmar que el script se cargó
        console.log('FODA Analysis JavaScript cargado');
        
        function addItem(type) {
            console.log('Agregando item de tipo:', type);
            
            const container = document.getElementById(type + '-container');
            if (!container) {
                console.error('No se encontró el contenedor:', type + '-container');
                return;
            }
            
            const newItem = document.createElement('div');
            newItem.className = 'foda-item';
            
            newItem.innerHTML = `
                <input type="text" name="${type}[]" class="foda-input" 
                       placeholder="Ingrese nuevo elemento..."
                       autocomplete="off">
                <button type="button" class="btn-remove" onclick="removeItem(this)" title="Eliminar">&times;</button>
            `;
            
            container.appendChild(newItem);
            
            // Enfocar el nuevo input
            const newInput = newItem.querySelector('input');
            newInput.focus();
            
            // Animación de entrada
            newItem.style.opacity = '0';
            newItem.style.transform = 'translateY(-10px)';
            
            setTimeout(() => {
                newItem.style.transition = 'all 0.3s ease';
                newItem.style.opacity = '1';
                newItem.style.transform = 'translateY(0)';
            }, 10);
            
            console.log('Item agregado exitosamente');
        }
        
        function removeItem(button) {
            console.log('Eliminando item');
            
            const item = button.parentElement;
            const container = item.parentElement;
            
            // No permitir eliminar si solo hay un elemento
            if (container.children.length <= 1) {
                alert('Debe mantener al menos un elemento en cada sección');
                return;
            }
            
            // Animación de salida
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '0';
            item.style.transform = 'translateX(100px)';
            
            setTimeout(() => {
                if (item.parentNode) {
                    container.removeChild(item);
                }
            }, 300);
            
            console.log('Item eliminado');
        }
        
        // Validación del formulario
        document.querySelector('.project-form').addEventListener('submit', function(e) {
            const containers = ['oportunidades', 'amenazas', 'fortalezas', 'debilidades'];
            let hasError = false;
            
            containers.forEach(type => {
                const inputs = document.querySelectorAll(`input[name="${type}[]"]`);
                let hasContent = false;
                
                inputs.forEach(input => {
                    if (input.value.trim() !== '') {
                        hasContent = true;
                    }
                });
                
                if (!hasContent) {
                    hasError = true;
                }
            });
            
            if (hasError) {
                e.preventDefault();
                alert('Por favor, complete al menos un elemento en cada sección del análisis FODA.');
            }
        });
        
        // Inicialización cuando la página se carga
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM cargado - Inicializando eventos FODA');
            
            // Verificar que todos los botones existen
            const addButtons = document.querySelectorAll('.btn-add');
            const removeButtons = document.querySelectorAll('.btn-remove');
            
            console.log('Botones "Agregar" encontrados:', addButtons.length);
            console.log('Botones "Eliminar" encontrados:', removeButtons.length);
            
            // Agregar eventos click adicionales por si acaso
            addButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const onclick = button.getAttribute('onclick');
                    if (onclick) {
                        console.log('Ejecutando:', onclick);
                    }
                });
            });
        });
        
        // Auto-resize de inputs y validación en tiempo real
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('foda-input')) {
                // Validar longitud mínima
                if (e.target.value.length > 3) {
                    e.target.style.borderColor = '#10b981';
                } else if (e.target.value.length > 0) {
                    e.target.style.borderColor = '#f59e0b';
                } else {
                    e.target.style.borderColor = '#e5e7eb';
                }
            }
        });
    </script>
    
    <!-- Footer -->
    <?php include '../Users/footer.php'; ?>
</body>
</html>