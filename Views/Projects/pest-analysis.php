<?php
// Incluir configuraciones necesarias
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../config/url_config.php';

// Verificar que el usuario esté logueado
if (!AuthController::isLoggedIn()) {
    header("Location: " . getBaseUrl() . "/Views/Auth/login.php");
    exit();
}

// Validar parámetros
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

// Obtener datos del usuario
$user = AuthController::getCurrentUser();
$baseUrl = getBaseUrl();

// Obtener las preguntas desde el modelo PEST
require_once __DIR__ . '/../../Models/PestAnalysis.php';
$pestAnalysisModel = new PestAnalysis();
$questions = $pestAnalysisModel->getStandardQuestions();

// Obtener respuestas existentes de PEST (si las hay)
$pestData = $projectController->getPestAnalysis($project_id);
$pestSummary = $projectController->getPestSummary($project_id);





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
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_pest_analysis.css">

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
                    <h2>🏢 <?php echo htmlspecialchars($project['project_name']); ?></h2>
                    <p class="project-description"><?php echo htmlspecialchars($project['project_description']); ?></p>
                    <p><strong>Paso 9:</strong> Análisis Externo Macroentorno (PEST)</p>
                </div>

            </div>

                "Las expectativas del ciclo económico de nuestro sector impactan en la situación económica de sus empresas.",    "Las expectativas del ciclo económico de nuestro sector impactan en la situación económica de sus empresas.",

            <!-- Contexto PEST -->

            <div class="context-box">    "Las Administraciones Públicas están incentivando el esfuerzo tecnológico de las empresas de nuestro sector.",    "Las Administraciones Públicas están incentivando el esfuerzo tecnológico de las empresas de nuestro sector.",

                <h3>🎯 Diagnóstico PEST</h3>

                <p><strong>PEST</strong> es un acrónimo que representa el macroentorno de la empresa:</p>    "Internet, el comercio electrónico, el wireless y otras NTIC están impactando en la demanda de nuestros productos/servicios y en los de la competencia.",    "Internet, el comercio electrónico, el wireless y otras NTIC están impactando en la demanda de nuestros productos/servicios y en los de la competencia.",

                <ul style="margin: 15px 0; padding-left: 20px;">

                    <li><strong>Políticos:</strong> Factores que determinan la actividad empresarial (legislación, normas, tratados comerciales)</li>    "El empleo de NTIC's es generalizado en el sector donde trabajamos.",    "El empleo de NTIC´s es generalizado en el sector donde trabajamos.",

                    <li><strong>Económicos:</strong> Comportamiento económico general (tasas, empleo, índices de precios)</li>

                    <li><strong>Sociales:</strong> Fuerzas sociales que afectan actitudes e intereses (demografía, estilos de vida)</li>    "En nuestro sector, es de gran importancia ser pionero o referente en el empleo de aplicaciones tecnológicas.",    "En nuestro sector, es de gran importancia ser pionero o referente en el empleo de aplicaciones tecnológicas.",

                    <li><strong>Tecnológicos:</strong> Avances tecnológicos que impulsan o transforman los negocios</li>

                </ul>    "En el sector donde operamos, para ser competitivos, es condición \"sine qua non\" innovar constantemente.",    "En el sector donde operamos, para ser competitivos, es condición \"sine qua non\" innovar constantemente.",

                <p>Evalúe cada aspecto calificando del <strong>0 al 4</strong> según el siguiente criterio:</p>

                <div class="rating-scale">    "La legislación medioambiental afecta al desarrollo de nuestro sector.",    "La legislación medioambiental afecta al desarrollo de nuestro sector.",

                    <div class="scale-item">

                        <span class="scale-number">0</span>    "Los clientes de nuestro mercado exigen que seamos socialmente responsables, en el plano medioambiental.",    "Los clientes de nuestro mercado exigen que se seamos socialmente responsables, en el plano medioambiental.",

                        <span class="scale-text">En total desacuerdo</span>

                    </div>    "En nuestro sector, las políticas medioambientales son una fuente de ventajas competitivas.",    "En nuestro sector, la políticas medioambientales son una fuente de ventajas competitivas.",

                    <div class="scale-item">

                        <span class="scale-number">1</span>    "La creciente preocupación social por el medio ambiente impacta notablemente en la demanda de productos/servicios ofertados en nuestro mercado.",    "La creciente preocupación social por el medio ambiente impacta notablemente en la demanda de productos/servicios ofertados en nuestro mercado.",

                        <span class="scale-text">No está de acuerdo</span>

                    </div>    "El factor ecológico es una fuente de diferenciación clara en el sector donde opera nuestra empresa."    "El factor ecológico es una fuente de diferenciación clara en el sector donde opera nuestra empresa."

                    <div class="scale-item">

                        <span class="scale-number">2</span>];];

                        <span class="scale-text">Está de acuerdo</span>

                    </div>

                    <div class="scale-item">

                        <span class="scale-number">3</span>?>?>

                        <span class="scale-text">Está bastante de acuerdo</span>

                    </div><!DOCTYPE html><!DOCTYPE html>

                    <div class="scale-item">

                        <span class="scale-number">4</span><html lang="es"><html lang="es">

                        <span class="scale-text">En total acuerdo</span>

                    </div><head><head>

                </div>

            </div>    <meta charset="UTF-8">    <meta charset="UTF-8">

            

            <!-- Formulario de diagnóstico -->    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <form action="<?php echo getBaseUrl(); ?>/Controllers/ProjectController.php?action=save_pest_analysis" method="POST" class="value-chain-form">

                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">    <title>Análisis PEST - <?php echo htmlspecialchars($project['project_name']); ?></title>    <title>Análisis PEST - <?php echo htmlspecialchars($project['project_name']); ?></title>

                

                <div class="questions-container">        

                    <h3>📋 Autodiagnóstico Entorno Global P.E.S.T.</h3>

                        <!-- CSS -->    <!-- CSS -->

                    <?php foreach ($questions as $index => $question): ?>

                    <div class="question-item">    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">

                        <div class="question-text">

                            <span class="question-number"><?php echo ($index + 1); ?>.</span>    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_project.css">    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_project.css">

                            <?php echo htmlspecialchars($question); ?>

                        </div>    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_value_chain.css">    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_foda.css">

                        <div class="rating-options">

                            <?php for ($rating = 0; $rating <= 4; $rating++): ?>    <link rel="preconnect" href="https://fonts.googleapis.com">    <link rel="preconnect" href="https://fonts.googleapis.com">

                            <div class="rating-option">

                                <input type="radio"     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

                                       id="q<?php echo ($index + 1); ?>_r<?php echo $rating; ?>" 

                                       name="responses[<?php echo ($index + 1); ?>]"     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

                                       value="<?php echo $rating; ?>">

                                <label for="q<?php echo ($index + 1); ?>_r<?php echo $rating; ?>"><?php echo $rating; ?></label>        

                            </div>

                            <?php endfor; ?>    <!-- Favicon -->    <!-- Favicon -->

                        </div>

                    </div>    <link rel="icon" type="image/x-icon" href="<?php echo getBaseUrl(); ?>/Resources/favicon.ico">    <link rel="icon" type="image/x-icon" href="<?php echo getBaseUrl(); ?>/Resources/favicon.ico">

                    <?php endforeach; ?>

                </div></head></head>

                

                <!-- Botones de acción --><body><body>

                <div class="form-actions">

                    <button type="button" onclick="calculateSummary()" class="btn btn-secondary">📊 Calcular</button>    <!-- Header -->    <!-- Header -->

                    <button type="submit" class="btn btn-primary btn-save-pest" disabled>💾 Guardar</button>

                    <a href="<?php echo getBaseUrl(); ?>/Views/Users/projects.php" class="btn btn-outline">🔙 Salir y Guardar</a>    <?php include 'header.php'; ?>    <?php include 'header.php'; ?>

                </div>

            </form>        

            

            <!-- Navegación a siguiente sección -->    <!-- Contenido principal -->    <!-- Contenido principal -->

            <div class="incomplete-message">

                <p><strong>⚠️ Complete todas las preguntas</strong></p>    <main class="main-content">    <main class="main-content">

                <p>Una vez completado el diagnóstico PEST, podrá continuar con el siguiente análisis estratégico.</p>

            </div>        <div class="container">        <div class="container">

        </div>

    </main>            <!-- Información del proyecto -->            <!-- Información del proyecto -->



    <!-- Footer -->            <div class="project-header">            <div class="project-header">

    <?php include '../Users/footer.php'; ?>

                    <div class="project-info">                <div class="project-info">

    <!-- Mensajes de éxito/error -->

    <?php if (isset($_GET['success'])): ?>                    <h2>🏢 <?php echo htmlspecialchars($project['project_name']); ?></h2>                    <h1><?php echo htmlspecialchars($project['project_name']); ?></h1>

    <div class="alert alert-success" id="alertMessage">

        ✅ Diagnóstico PEST guardado exitosamente                    <p class="project-description"><?php echo htmlspecialchars($project['project_description']); ?></p>                    <p><?php echo htmlspecialchars($project['company_name']); ?></p>

    </div>

    <?php endif; ?>                    <p><strong>Paso 9:</strong> Análisis Externo Macroentorno (PEST)</p>                </div>



    <?php if (isset($_GET['error'])): ?>                </div>            </div>

    <div class="alert alert-error" id="alertMessage">

        ❌ Error: <?php echo htmlspecialchars($_GET['error']); ?>            </div>            

    </div>

    <?php endif; ?>                        <!-- Contexto PEST -->



    <script>            <!-- Contexto PEST -->            <div class="context-box">

        // Auto-ocultar alertas después de 5 segundos

        const alertMessage = document.getElementById('alertMessage');            <div class="context-box">                <h3>9. ANÁLISIS EXTERNO MACROENTORNO: PEST</h3>

        if (alertMessage) {

            setTimeout(() => {                <h3>🎯 Diagnóstico PEST</h3>                <p>PEST es un acrónimo que representa el macro entorno de la empresa. Responda cada afirmación marcando del <strong>0</strong> al <strong>4</strong> según el siguiente criterio:</p>

                alertMessage.style.display = 'none';

            }, 5000);                <p><strong>PEST</strong> es un acrónimo que representa el macroentorno de la empresa:</p>                <div class="rating-scale">

        }

                        <ul style="margin: 15px 0; padding-left: 20px;">                    <div><strong>0</strong> En total desacuerdo</div>

        // Mejorar interactividad de las opciones de rating

        document.querySelectorAll('.rating-option input[type="radio"]').forEach(radio => {                    <li><strong>Políticos:</strong> Factores que determinan la actividad empresarial (legislación, normas, tratados comerciales)</li>                    <div><strong>1</strong> No está de acuerdo</div>

            radio.addEventListener('change', function() {

                // Limpiar selección previa en la misma pregunta                    <li><strong>Económicos:</strong> Comportamiento económico general (tasas, empleo, índices de precios)</li>                    <div><strong>2</strong> Está de acuerdo</div>

                const questionName = this.name;

                document.querySelectorAll(`input[name="${questionName}"]`).forEach(r => {                    <li><strong>Sociales:</strong> Fuerzas sociales que afectan actitudes e intereses (demografía, estilos de vida)</li>                    <div><strong>3</strong> Está bastante de acuerdo</div>

                    r.closest('.rating-option').classList.remove('selected');

                });                    <li><strong>Tecnológicos:</strong> Avances tecnológicos que impulsan o transforman los negocios</li>                    <div><strong>4</strong> En total acuerdo</div>

                

                // Marcar opción actual como seleccionada                </ul>                </div>

                this.closest('.rating-option').classList.add('selected');

                                <p>Evalúe cada aspecto calificando del <strong>0 al 4</strong> según el siguiente criterio:</p>            </div>

                updateProgressCounter();

            });                <div class="rating-scale">

        });

                            <div class="scale-item">            <!-- Formulario PEST (frontend solamente) -->

        // Contador de progreso

        function updateProgressCounter() {                        <span class="scale-number">0</span>            <form action="<?php echo getBaseUrl(); ?>/Controllers/ProjectController.php?action=save_pest_analysis" method="POST" class="pest-form">

            const totalQuestions = <?php echo count($questions); ?>;

            const answeredQuestions = document.querySelectorAll('.rating-option input[type="radio"]:checked').length;                        <span class="scale-text">En total desacuerdo</span>                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

            

            // Crear o actualizar indicador de progreso si no existe                    </div>

            let progressIndicator = document.querySelector('.progress-indicator');

            if (!progressIndicator) {                    <div class="scale-item">                <div class="questions-container">

                progressIndicator = document.createElement('span');

                progressIndicator.className = 'progress-indicator';                        <span class="scale-number">1</span>                    <h3>AUTODIAGNÓSTICO ENTORNO GLOBAL P.E.S.T. <span class="small">VALORACIÓN</span></h3>

                document.querySelector('.questions-container h3').appendChild(progressIndicator);

            }                        <span class="scale-text">No está de acuerdo</span>                    <div class="questions-grid">

            

            const percentage = (answeredQuestions / totalQuestions) * 100;                    </div>                        <?php foreach ($questions as $index => $text):

            progressIndicator.innerHTML = ` (${answeredQuestions}/${totalQuestions} - ${Math.round(percentage)}%)`;

                                <div class="scale-item">                            $qnum = $index + 1;

            // Habilitar botón de guardar solo si todas las preguntas están respondidas

            const saveButton = document.querySelector('.btn-save-pest');                        <span class="scale-number">2</span>                        ?>

            if (answeredQuestions === totalQuestions) {

                saveButton.disabled = false;                        <span class="scale-text">Está de acuerdo</span>                        <div class="question-item">

                saveButton.style.opacity = '1';

            } else {                    </div>                            <div class="question-text"><strong><?php echo $qnum; ?>.</strong> <?php echo $text; ?></div>

                saveButton.disabled = true;

                saveButton.style.opacity = '0.6';                    <div class="scale-item">                            <div class="rating-options">

            }

        }                        <span class="scale-number">3</span>                                <?php for ($r = 0; $r <= 4; $r++): ?>

        

        // Inicializar contador de progreso                        <span class="scale-text">Está bastante de acuerdo</span>                                    <label class="rating-option">

        document.addEventListener('DOMContentLoaded', function() {

            updateProgressCounter();                    </div>                                        <input type="radio" name="responses[<?php echo $qnum; ?>]" value="<?php echo $r; ?>">

        });

                    <div class="scale-item">                                        <span class="rating-label"><?php echo $r; ?></span>

        // Función de cálculo simple que resume valores (frontend only)

        function calculateSummary() {                        <span class="scale-number">4</span>                                    </label>

            const totalQuestions = <?php echo count($questions); ?>;

            const checked = document.querySelectorAll('.rating-option input[type="radio"]:checked');                        <span class="scale-text">En total acuerdo</span>                                <?php endfor; ?>

            

            if (checked.length !== totalQuestions) {                    </div>                            </div>

                alert('Por favor responda todas las preguntas antes de calcular.');

                return;                </div>                        </div>

            }

            </div>                        <?php endforeach; ?>

            let sum = 0;

            checked.forEach(input => sum += parseInt(input.value, 10));                                </div>

            const avg = (sum / totalQuestions).toFixed(2);

                        <!-- Formulario de diagnóstico -->                </div>

            let interpretation = '';

            if (avg >= 3.5) {            <form action="<?php echo getBaseUrl(); ?>/Controllers/ProjectController.php?action=save_pest_analysis" method="POST" class="value-chain-form">

                interpretation = 'Entorno muy favorable para su empresa';

            } else if (avg >= 2.5) {                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">                <!-- Botones de acción -->

                interpretation = 'Entorno moderadamente favorable';

            } else if (avg >= 1.5) {                                <div class="form-actions">

                interpretation = 'Entorno con desafíos significativos';

            } else {                <div class="questions-container">                    <button type="button" class="btn-calculate" onclick="calculateSummary()">📊 Calcular</button>

                interpretation = 'Entorno muy desafiante, requiere estrategias especiales';

            }                    <h3>📋 Autodiagnóstico Entorno Global P.E.S.T.</h3>                    <button type="submit" class="btn-save btn-save-pest" disabled>💾 Guardar</button>

            

            alert(`Resumen Análisis PEST:\n\nPuntuación total: ${sum}/${totalQuestions * 4}\nMedia: ${avg}/4\n\nInterpretación: ${interpretation}\n\n(Este cálculo es informativo. Use "Guardar" para almacenar las respuestas)`);                                        <button type="submit" name="save_and_exit" value="1" class="btn-save-exit">💾 Salir y Guardar</button>

        }

    </script>                    <?php foreach ($questions as $index => $question): ?>                </div>

</body>

</html>                    <div class="question-item">            </form>

                        <div class="question-text">

                            <span class="question-number"><?php echo ($index + 1); ?>.</span>            <div class="incomplete-message">

                            <?php echo htmlspecialchars($question); ?>                <p><strong>⚠️ Complete todas las preguntas</strong></p>

                        </div>                <p>Una vez completado el diagnóstico, podrá guardar y continuar con otras secciones del Plan Estratégico.</p>

                        <div class="rating-options">            </div>

                            <?php for ($rating = 0; $rating <= 4; $rating++): ?>        </div>

                            <div class="rating-option">    </main>

                                <input type="radio" 

                                       id="q<?php echo ($index + 1); ?>_r<?php echo $rating; ?>"     <!-- Footer -->

                                       name="responses[<?php echo ($index + 1); ?>]"     <?php include '../Users/footer.php'; ?>

                                       value="<?php echo $rating; ?>">    

                                <label for="q<?php echo ($index + 1); ?>_r<?php echo $rating; ?>"><?php echo $rating; ?></label>    <!-- Mensajes de éxito/error -->

                            </div>    <?php if (isset($_GET['success'])): ?>

                            <?php endfor; ?>    <div class="alert alert-success" id="alertMessage">

                        </div>        ✅ Diagnóstico guardado exitosamente

                    </div>    </div>

                    <?php endforeach; ?>    <?php endif; ?>

                </div>

                    <?php if (isset($_GET['error'])): ?>

                <!-- Botones de acción -->    <div class="alert alert-error" id="alertMessage">

                <div class="form-actions">        ❌ Error: <?php echo htmlspecialchars($_GET['error']); ?>

                    <button type="button" onclick="calculateSummary()" class="btn btn-secondary">📊 Calcular</button>    </div>

                    <button type="submit" class="btn btn-primary btn-save-pest" disabled>💾 Guardar</button>    <?php endif; ?>

                    <a href="<?php echo getBaseUrl(); ?>/Views/Users/projects.php" class="btn btn-outline">🔙 Salir y Guardar</a>

                </div>    <script>

            </form>        // Auto-ocultar alertas después de 5 segundos

                    const alertMessage = document.getElementById('alertMessage');

            <!-- Navegación a siguiente sección -->        if (alertMessage) {

            <div class="incomplete-message">            setTimeout(() => { alertMessage.style.display = 'none'; }, 5000);

                <p><strong>⚠️ Complete todas las preguntas</strong></p>        }

                <p>Una vez completado el diagnóstico PEST, podrá continuar con el siguiente análisis estratégico.</p>

            </div>        // Interactividad para opciones

        </div>        document.querySelectorAll('.rating-option input[type="radio"]').forEach(radio => {

    </main>            radio.addEventListener('change', function() {

                const questionItem = this.closest('.question-item');

    <!-- Footer -->                questionItem.querySelectorAll('.rating-option').forEach(option => option.classList.remove('selected'));

    <?php include '../Users/footer.php'; ?>                this.closest('.rating-option').classList.add('selected');

                    updateProgressCounter();

    <!-- Mensajes de éxito/error -->            });

    <?php if (isset($_GET['success'])): ?>        });

    <div class="alert alert-success" id="alertMessage">

        ✅ Diagnóstico PEST guardado exitosamente        function updateProgressCounter() {

    </div>            const totalQuestions = <?php echo count($questions); ?>;

    <?php endif; ?>            const answered = document.querySelectorAll('.rating-option input[type="radio"]:checked').length;



    <?php if (isset($_GET['error'])): ?>            // Mostrar indicador de progreso junto al título

    <div class="alert alert-error" id="alertMessage">            let progressIndicator = document.querySelector('.progress-indicator');

        ❌ Error: <?php echo htmlspecialchars($_GET['error']); ?>            if (!progressIndicator) {

    </div>                progressIndicator = document.createElement('div');

    <?php endif; ?>                progressIndicator.className = 'progress-indicator';

                document.querySelector('.questions-container h3').appendChild(progressIndicator);

    <script>            }

        // Auto-ocultar alertas después de 5 segundos

        const alertMessage = document.getElementById('alertMessage');            const percentage = (answered / totalQuestions) * 100;

        if (alertMessage) {            progressIndicator.innerHTML = ` (${answered}/${totalQuestions} - ${Math.round(percentage)}%)`;

            setTimeout(() => {

                alertMessage.style.display = 'none';            const saveBtn = document.querySelector('.btn-save-pest');

            }, 5000);            if (answered === totalQuestions) {

        }                saveBtn.disabled = false;

                        saveBtn.style.opacity = '1';

        // Mejorar interactividad de las opciones de rating            } else {

        document.querySelectorAll('.rating-option input[type="radio"]').forEach(radio => {                saveBtn.disabled = true;

            radio.addEventListener('change', function() {                saveBtn.style.opacity = '0.6';

                // Limpiar selección previa en la misma pregunta            }

                const questionName = this.name;        }

                document.querySelectorAll(`input[name="${questionName}"]`).forEach(r => {

                    r.closest('.rating-option').classList.remove('selected');        document.addEventListener('DOMContentLoaded', function() { updateProgressCounter(); });

                });

                        // Función de cálculo simple que resume valores (frontend only)

                // Marcar opción actual como seleccionada        function calculateSummary() {

                this.closest('.rating-option').classList.add('selected');            const totalQuestions = <?php echo count($questions); ?>;

                            const checked = document.querySelectorAll('.rating-option input[type="radio"]:checked');

                updateProgressCounter();            if (checked.length !== totalQuestions) {

            });                alert('Por favor responda todas las preguntas antes de calcular.');

        });                return;

                    }

        // Contador de progreso

        function updateProgressCounter() {            let sum = 0;

            const totalQuestions = <?php echo count($questions); ?>;            checked.forEach(input => sum += parseInt(input.value, 10));

            const answeredQuestions = document.querySelectorAll('.rating-option input[type="radio"]:checked').length;            const avg = (sum / totalQuestions).toFixed(2);

                        alert('Resumen PEST:\nSuma: ' + sum + '\nMedia: ' + avg + '\n(este cálculo es solo informativo, el guardado requiere backend)');

            // Crear o actualizar indicador de progreso si no existe        }

            let progressIndicator = document.querySelector('.progress-indicator');    </script>

            if (!progressIndicator) {</body>

                progressIndicator = document.createElement('span');</html>

                progressIndicator.className = 'progress-indicator';
                document.querySelector('.questions-container h3').appendChild(progressIndicator);
            }
            
            const percentage = (answeredQuestions / totalQuestions) * 100;
            progressIndicator.innerHTML = ` (${answeredQuestions}/${totalQuestions} - ${Math.round(percentage)}%)`;
            
            // Habilitar botón de guardar solo si todas las preguntas están respondidas
            const saveButton = document.querySelector('.btn-save-pest');
            if (answeredQuestions === totalQuestions) {
                saveButton.disabled = false;
                saveButton.style.opacity = '1';
            } else {
                saveButton.disabled = true;
                saveButton.style.opacity = '0.6';
            }
        }
        
        // Inicializar contador de progreso
        document.addEventListener('DOMContentLoaded', function() {
            updateProgressCounter();
        });

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
            
            let interpretation = '';
            if (avg >= 3.5) {
                interpretation = 'Entorno muy favorable para su empresa';
            } else if (avg >= 2.5) {
                interpretation = 'Entorno moderadamente favorable';
            } else if (avg >= 1.5) {
                interpretation = 'Entorno con desafíos significativos';
            } else {
                interpretation = 'Entorno muy desafiante, requiere estrategias especiales';
            }
            
            alert(`Resumen Análisis PEST:\n\nPuntuación total: ${sum}/${totalQuestions * 4}\nMedia: ${avg}/4\n\nInterpretación: ${interpretation}\n\n(Este cálculo es informativo. Use "Guardar" para almacenar las respuestas)`);
        }
    </script>
</body>
</html>