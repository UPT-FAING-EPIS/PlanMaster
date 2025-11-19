<?php
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/ReportController.php';
require_once __DIR__ . '/../../config/url_config.php';

// Verificar que el usuario esté logueado
AuthController::requireLogin();

// Obtener datos del usuario
$user = AuthController::getCurrentUser();

// Obtener proyectos del usuario
$reportController = new ReportController();
$projects = $reportController->getUserProjects($user['id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plantillas - PlanMaster</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_templates.css">
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
            <div class="page-header">
                <h1 class="page-title">📋 Plantillas de Informe</h1>
                <p class="page-subtitle">Genera reportes ejecutivos en PDF de tus planes estratégicos</p>
            </div>
            
            <?php if (empty($projects)): ?>
            <div class="no-projects">
                <div class="no-projects-icon">📄</div>
                <h3>No tienes proyectos disponibles</h3>
                <p>Crea un proyecto estratégico primero para poder generar reportes.</p>
                <a href="dashboard.php" class="btn-primary">Ir al Dashboard</a>
            </div>
            <?php else: ?>
            
            <!-- Selector de Proyecto -->
            <div class="report-generator">
                <div class="generator-section">
                    <h3>🎯 Seleccionar Proyecto</h3>
                    <select id="project-select" class="form-control">
                        <option value="">Selecciona un proyecto...</option>
                        <?php foreach ($projects as $project): ?>
                        <option value="<?php echo $project['id']; ?>" 
                                data-name="<?php echo htmlspecialchars($project['project_name']); ?>"
                                data-company="<?php echo htmlspecialchars($project['company_name']); ?>">
                            <?php echo htmlspecialchars($project['project_name']); ?> - <?php echo htmlspecialchars($project['company_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Participantes -->
                <div class="generator-section">
                    <h3>👥 Emprendedores/Promotores</h3>
                    <textarea id="participants" class="form-control" rows="3" 
                              placeholder="Ingresa los nombres de los participantes del proyecto, separados por comas..."></textarea>
                </div>
                
                <!-- Plantillas de Carátula -->
                <div class="generator-section">
                    <h3>🎨 Plantillas de Carátula</h3>
                    <div class="templates-grid">
                        <div class="template-card" data-template="default">
                            <div class="template-preview template-default">
                                <div class="preview-content">
                                    <h4>Clásica</h4>
                                    <p>Gradiente azul elegante</p>
                                </div>
                            </div>
                            <div class="template-info">
                                <h5>🔷 Plantilla Clásica</h5>
                                <p>Diseño profesional con gradiente azul</p>
                            </div>
                        </div>
                        
                        <div class="template-card" data-template="corporate">
                            <div class="template-preview template-corporate">
                                <div class="preview-content">
                                    <h4>Corporativa</h4>
                                    <p>Estilo empresarial</p>
                                </div>
                            </div>
                            <div class="template-info">
                                <h5>🏢 Plantilla Corporativa</h5>
                                <p>Diseño sobrio para empresas</p>
                            </div>
                        </div>
                        
                        <div class="template-card" data-template="modern">
                            <div class="template-preview template-modern">
                                <div class="preview-content">
                                    <h4>Moderna</h4>
                                    <p>Diseño contemporáneo</p>
                                </div>
                            </div>
                            <div class="template-info">
                                <h5>✨ Plantilla Moderna</h5>
                                <p>Gradientes vibrantes y efectos</p>
                            </div>
                        </div>
                        
                        <div class="template-card" data-template="elegant">
                            <div class="template-preview template-elegant">
                                <div class="preview-content">
                                    <h4>Elegante</h4>
                                    <p>Tonos oscuros sofisticados</p>
                                </div>
                            </div>
                            <div class="template-info">
                                <h5>🌙 Plantilla Elegante</h5>
                                <p>Diseño minimalista y sofisticado</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Botón de Generar -->
                <div class="generator-section">
                    <button id="generate-report" class="btn-generate" disabled>
                        📄 Generar Reporte PDF
                    </button>
                </div>
            </div>
            
            <?php endif; ?>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include 'footer.php'; ?>
    
    <!-- JavaScript -->
    <script>
        const BASE_URL = '<?php echo getBaseUrl(); ?>';
        let selectedProject = null;
        let selectedTemplate = 'default';
        
        document.addEventListener('DOMContentLoaded', function() {
            // Event listeners
            document.getElementById('project-select').addEventListener('change', handleProjectSelect);
            document.getElementById('participants').addEventListener('input', validateForm);
            document.getElementById('generate-report').addEventListener('click', generateReport);
            
            // Template selection
            document.querySelectorAll('.template-card').forEach(card => {
                card.addEventListener('click', function() {
                    selectTemplate(this.dataset.template);
                });
            });
            
            // Select default template
            selectTemplate('default');
        });
        
        function handleProjectSelect(e) {
            const select = e.target;
            if (select.value) {
                selectedProject = {
                    id: select.value,
                    name: select.options[select.selectedIndex].dataset.name,
                    company: select.options[select.selectedIndex].dataset.company
                };
            } else {
                selectedProject = null;
            }
            validateForm();
        }
        
        function selectTemplate(template) {
            // Remove previous selection
            document.querySelectorAll('.template-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Select new template
            document.querySelector(`[data-template="${template}"]`).classList.add('selected');
            selectedTemplate = template;
        }
        
        function validateForm() {
            const generateBtn = document.getElementById('generate-report');
            const participants = document.getElementById('participants').value.trim();
            
            if (selectedProject && participants.length > 0) {
                generateBtn.disabled = false;
                generateBtn.classList.add('enabled');
            } else {
                generateBtn.disabled = true;
                generateBtn.classList.remove('enabled');
            }
        }
        
        function generateReport() {
            if (!selectedProject) {
                alert('Por favor selecciona un proyecto');
                return;
            }
            
            const participants = document.getElementById('participants').value.trim();
            if (!participants) {
                alert('Por favor ingresa los nombres de los participantes');
                return;
            }
            
            console.log('🚀 Iniciando generación de PDF...');
            console.log('📊 Proyecto:', selectedProject);
            console.log('👥 Participantes:', participants);
            console.log('🎨 Template:', selectedTemplate);
            
            // Show loading
            const btn = document.getElementById('generate-report');
            const originalText = btn.textContent;
            btn.textContent = '⏳ Generando PDF...';
            btn.disabled = true;
            
            // Create FormData for AJAX
            const formData = new FormData();
            formData.append('action', 'generate_pdf');
            formData.append('project_id', selectedProject.id);
            formData.append('participants', participants);
            formData.append('template', selectedTemplate);
            
            // Log form data
            console.log('📤 Datos enviados:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
            
            // Use the correct URL for PDF generation
            const pdfUrl = `${BASE_URL}/generate_pdf_direct.php`;
            console.log('🌐 URL completa:', pdfUrl);
            
            // Use direct PDF generator
            fetch(pdfUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('📥 Respuesta recibida:', response);
                console.log('Status:', response.status);
                console.log('Content-Type:', response.headers.get('content-type'));
                console.log('URL final:', response.url);
                
                if (!response.ok) {
                    if (response.status === 404) {
                        throw new Error(`El archivo generate_pdf_direct.php no se encuentra en: ${pdfUrl}`);
                    } else {
                        throw new Error(`Error del servidor: ${response.status} - ${response.statusText}`);
                    }
                }
                
                if (response.headers.get('content-type')?.includes('application/pdf')) {
                    console.log('✅ PDF detectado, iniciando descarga...');
                    return response.blob().then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        // Sanitizar nombre del archivo
                        const sanitizedName = selectedProject.name.replace(/[^a-zA-Z0-9]/g, '_');
                        a.download = `Reporte_${sanitizedName}_${new Date().toISOString().slice(0,10)}.pdf`;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                        
                        // Restore button
                        btn.textContent = originalText;
                        btn.disabled = false;
                        btn.classList.add('enabled');
                        
                        alert('✅ PDF generado y descargado correctamente');
                    });
                } else {
                    console.log('⚠️ No es PDF, mostrando debug...');
                    return response.text().then(text => {
                        console.log('Contenido de respuesta:', text);
                        
                        // Show error in alert instead of opening new window
                        alert(`Error en la generación del PDF:\n\nStatus: ${response.status}\nContenido: ${text.substring(0, 200)}...`);
                        
                        // Restore button
                        btn.textContent = originalText;
                        btn.disabled = false;
                        btn.classList.add('enabled');
                    });
                }
            })
            .catch(error => {
                console.error('❌ Error:', error);
                alert(`Error al generar el reporte:\n\n${error.message}\n\nVerifica que el archivo generate_pdf_direct.php esté en la raíz del proyecto en el servidor.`);
                
                // Restore button
                btn.textContent = originalText;
                btn.disabled = false;
                btn.classList.add('enabled');
            });
        }
    </script>
</body>
</html>

<style>
.page-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 40px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 0 10px 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.page-subtitle {
    font-size: 1.1rem;
    margin: 0;
    opacity: 0.9;
    font-weight: 300;
}

.coming-soon {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.coming-soon-icon {
    font-size: 4rem;
    margin-bottom: 20px;
}

.coming-soon h2 {
    color: #333;
    margin-bottom: 15px;
    font-size: 1.8rem;
    font-weight: 600;
}

.coming-soon p {
    color: #666;
    margin-bottom: 30px;
    font-size: 1.1rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}

.btn-back {
    background: linear-gradient(135deg, #42a5f5, #1e88e5);
    color: white;
    text-decoration: none;
    padding: 15px 30px;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    display: inline-block;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(30, 136, 229, 0.3);
}

.btn-back:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(30, 136, 229, 0.4);
}
</style>