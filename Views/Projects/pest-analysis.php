<?php
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../config/url_config.php';

// Asegurar que el usuario esté logueado
AuthController::requireLogin();

// Validar parámetro de proyecto
if (!isset($_GET['id'])) {
    header("Location: " . getBaseUrl() . "/Views/Users/dashboard.php");
    exit();
}

$project_id = (int)$_GET['id'];
$projectController = new ProjectController();

// Verificar que el proyecto existe y pertenece al usuario
$project = $projectController->getProject($project_id);
if (!$project || $project['user_id'] != $_SESSION['user_id']) {
    header("Location: " . getBaseUrl() . "/Views/Users/dashboard.php");
    exit();
}

// Obtener usuario actual
$user = AuthController::getCurrentUser();

// Lista de 25 preguntas PEST (en el orden proporcionado)
$questions = [
    "Los cambios en la composicón étnica de los consumidores de nuestro mercado está teniendo un notable impacto.",
    "El envejecimiento de la población tiene un importante impacto en la demanda.",
    "Los nuevos estilos de vida y tendencias originan cambios en la oferta de nuestro sector.",
    "El envejecimiento de la población tiene un importante impacto en la oferta del sector donde operamos.",
    "Las variaciones en el nivel de riqueza de la población impactan considerablemente en la demanda de los productos/servicios del sector donde operamos.",
    "La legislación fiscal afecta muy considerablemente a la economía de las empresas del sector donde operamos.",
    "La legislación laboral afecta muy considerablemente a la operativa del sector donde actuamos.",
    "Las subvenciones otorgadas por las Administraciones Públicas son claves en el desarrollo competitivo del mercado donde operamos.",
    "El impacto que tiene la legislación de protección al consumidor, en la manera de producir bienes y/o servicios es muy importante.",
    "La normativa autonómica tiene un impacto considerable en el funcionamiento del sector donde actuamos.",
    "Las expectativas de crecimiento económico generales afectan crucialmente al mercado donde operamos.",
    "La política de tipos de interés es fundamental en el desarrollo financiero del sector donde trabaja nuestra empresa.",
    "La globalización permite a nuestra industria gozar de importantes oportunidades en  nuevos mercados.",
    "La situación del empleo es fundamental para el desarrollo económico de nuestra empresa y nuestro sector.",
    "Las expectativas del ciclo económico de nuestro sector impactan en la situación económica de sus empresas.",
    "Las Administraciones Públicas están incentivando el esfuerzo tecnológico de las empresas de nuestro sector.",
    "Internet, el comercio electrónico, el wireless y otras NTIC están impactando en la demanda de nuestros productos/servicios y en los de la competencia.",
    "El empleo de NTIC´s es generalizado en el sector donde trabajamos.",
    "En nuestro sector, es de gran importancia ser pionero o referente en el empleo de aplicaciones tecnológicas.",
    "En el sector donde operamos, para ser competitivos, es condición \"sine qua non\" innovar constantemente.",
    "La legislación medioambiental afecta al desarrollo de nuestro sector.",
    "Los clientes de nuestro mercado exigen que se seamos socialmente responsables, en el plano medioambiental.",
    "En nuestro sector, la políticas medioambientales son una fuente de ventajas competitivas.",
    "La creciente preocupación social por el medio ambiente impacta notablemente en la demanda de productos/servicios ofertados en nuestro mercado.",
    "El factor ecológico es una fuente de diferenciación clara en el sector donde opera nuestra empresa."
];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis PEST - <?php echo htmlspecialchars($project['project_name']); ?></title>
    
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
                    <h1><?php echo htmlspecialchars($project['project_name']); ?></h1>
                    <p><?php echo htmlspecialchars($project['company_name']); ?></p>
                </div>
            </div>
            
            <!-- Contexto PEST -->
            <div class="context-box">
                <h3>9. ANÁLISIS EXTERNO MACROENTORNO: PEST</h3>
                <p>PEST es un acrónimo que representa el macro entorno de la empresa. Responda cada afirmación marcando del <strong>0</strong> al <strong>4</strong> según el siguiente criterio:</p>
                <div class="rating-scale">
                    <div><strong>0</strong> En total desacuerdo</div>
                    <div><strong>1</strong> No está de acuerdo</div>
                    <div><strong>2</strong> Está de acuerdo</div>
                    <div><strong>3</strong> Está bastante de acuerdo</div>
                    <div><strong>4</strong> En total acuerdo</div>
                </div>
            </div>

            <!-- Formulario PEST (frontend solamente) -->
            <form action="<?php echo getBaseUrl(); ?>/Controllers/ProjectController.php?action=save_pest_analysis" method="POST" class="pest-form">
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

                <div class="questions-container">
                    <h3>AUTODIAGNÓSTICO ENTORNO GLOBAL P.E.S.T. <span class="small">VALORACIÓN</span></h3>
                    <div class="questions-grid">
                        <?php foreach ($questions as $index => $text):
                            $qnum = $index + 1;
                        ?>
                        <div class="question-item">
                            <div class="question-text"><strong><?php echo $qnum; ?>.</strong> <?php echo $text; ?></div>
                            <div class="rating-options">
                                <?php for ($r = 0; $r <= 4; $r++): ?>
                                    <label class="rating-option">
                                        <input type="radio" name="responses[<?php echo $qnum; ?>]" value="<?php echo $r; ?>">
                                        <span class="rating-label"><?php echo $r; ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="form-actions">
                    <button type="button" class="btn-calculate" onclick="calculateSummary()">📊 Calcular</button>
                    <button type="submit" class="btn-save btn-save-pest" disabled>💾 Guardar</button>
                    <button type="submit" name="save_and_exit" value="1" class="btn-save-exit">💾 Salir y Guardar</button>
                </div>
            </form>

            <div class="incomplete-message">
                <p><strong>⚠️ Complete todas las preguntas</strong></p>
                <p>Una vez completado el diagnóstico, podrá guardar y continuar con otras secciones del Plan Estratégico.</p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../Users/footer.php'; ?>
    
    <!-- Mensajes de éxito/error -->
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success" id="alertMessage">
        ✅ Diagnóstico guardado exitosamente
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error" id="alertMessage">
        ❌ Error: <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
    <?php endif; ?>

    <script>
        // Auto-ocultar alertas después de 5 segundos
        const alertMessage = document.getElementById('alertMessage');
        if (alertMessage) {
            setTimeout(() => { alertMessage.style.display = 'none'; }, 5000);
        }

        // Interactividad para opciones
        document.querySelectorAll('.rating-option input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const questionItem = this.closest('.question-item');
                questionItem.querySelectorAll('.rating-option').forEach(option => option.classList.remove('selected'));
                this.closest('.rating-option').classList.add('selected');
                updateProgressCounter();
            });
        });

        function updateProgressCounter() {
            const totalQuestions = <?php echo count($questions); ?>;
            const answered = document.querySelectorAll('.rating-option input[type="radio"]:checked').length;

            // Mostrar indicador de progreso junto al título
            let progressIndicator = document.querySelector('.progress-indicator');
            if (!progressIndicator) {
                progressIndicator = document.createElement('div');
                progressIndicator.className = 'progress-indicator';
                document.querySelector('.questions-container h3').appendChild(progressIndicator);
            }

            const percentage = (answered / totalQuestions) * 100;
            progressIndicator.innerHTML = ` (${answered}/${totalQuestions} - ${Math.round(percentage)}%)`;

            const saveBtn = document.querySelector('.btn-save-pest');
            if (answered === totalQuestions) {
                saveBtn.disabled = false;
                saveBtn.style.opacity = '1';
            } else {
                saveBtn.disabled = true;
                saveBtn.style.opacity = '0.6';
            }
        }

        document.addEventListener('DOMContentLoaded', function() { updateProgressCounter(); });

        // Función de cálculo simple que resume valores (frontend only)
        function calculateSummary() {
            const totalQuestions = <?php echo count($questions); ?>;
            const checked = document.querySelectorAll('.rating-option input[type="radio"]:checked');
            if (checked.length !== totalQuestions) {
                alert('Por favor responda todas las preguntas antes de calcular.');
                return;
            }

            let sum = 0;
            checked.forEach(input => sum += parseInt(input.value, 10));
            const avg = (sum / totalQuestions).toFixed(2);
            alert('Resumen PEST:\nSuma: ' + sum + '\nMedia: ' + avg + '\n(este cálculo es solo informativo, el guardado requiere backend)');
        }
    </script>
</body>
</html>
