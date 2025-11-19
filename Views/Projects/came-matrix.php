<?php
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../Controllers/CAMEMatrixController.php';
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

// Obtener acciones CAME existentes
$cameController = new CAMEMatrixController();
$cameActions = $cameController->getActionsByProject($project_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Matriz CAME - <?php echo htmlspecialchars($project['project_name']); ?></title>
	<!-- CSS -->
	<link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">
	<link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_project.css">
	<link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_came.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<!-- Favicon -->
	<link rel="icon" type="image/x-icon" href="<?php echo getBaseUrl(); ?>/Resources/favicon.ico">
</head>
<body>
	<?php include 'header.php'; ?>
	<main class="main-content">
		<div class="container">
			<div class="project-header">
				<div class="project-info">
					<h1>🟦 11. MATRIZ CAME</h1>
					<p class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></p>
				</div>
			</div>
			<!-- Descripción de la Matriz CAME -->
			<section class="came-description">
				<p>A continuación y para finalizar de elaborar un Plan Estratégico, además de tener identificada la estrategia es necesario determinar acciones que permitan corregir las debilidades, afrontar las amenazas, mantener las fortalezas y explotar las oportunidades.</p>
				<div class="came-reflection-box">
					Reflexione y anote acciones a llevar a cabo teniendo en cuenta que estas acciones deben favorecer la ejecución exitosa de la estrategia general identificada
				</div>
			</section>

			<!-- Matriz CAME Dinámica -->
			<div class="came-actions-container">
				<?php
				// Las acciones ya vienen organizadas por tipo desde el modelo
				$actionsByType = $cameActions ? $cameActions : [
					'C' => [],
					'A' => [],
					'M' => [],
					'E' => []
				];

				// Definir configuración para cada tipo de acción
				$actionTypes = [
					'C' => [
						'title' => 'Corregir las debilidades',
						'description' => 'Acciones para mejorar las áreas débiles de la organización',
						'icon' => '🔧'
					],
					'A' => [
						'title' => 'Afrontar las amenazas',
						'description' => 'Estrategias para contrarrestar amenazas externas',
						'icon' => '🛡️'
					],
					'M' => [
						'title' => 'Mantener las fortalezas',
						'description' => 'Acciones para preservar y potenciar las ventajas competitivas',
						'icon' => '💪'
					],
					'E' => [
						'title' => 'Explotar las oportunidades',
						'description' => 'Estrategias para aprovechar oportunidades del entorno',
						'icon' => '🚀'
					]
				];

				foreach ($actionTypes as $type => $config):
				?>
				<div class="came-section type-<?php echo strtolower($type); ?>">
					<div class="came-section-header">
						<div class="came-section-title">
							<div class="came-letter"><?php echo $type; ?></div>
							<div>
								<h3><?php echo $config['title']; ?></h3>
								<small><?php echo $config['description']; ?></small>
							</div>
						</div>
						<button type="button" class="add-action-btn" onclick="addNewAction('<?php echo $type; ?>')">
							<?php echo $config['icon']; ?> Agregar Acción
						</button>
					</div>
					
					<ul class="came-actions-list" id="actions-<?php echo $type; ?>">
						<?php if (empty($actionsByType[$type])): ?>
							<li class="empty-state" id="empty-<?php echo $type; ?>">
								<div class="empty-state-icon">📝</div>
								<div class="empty-state-text">
									No hay acciones definidas para <?php echo strtolower($config['title']); ?>.<br>
									Haga clic en "Agregar Acción" para comenzar.
								</div>
							</li>
						<?php else: ?>
							<?php foreach ($actionsByType[$type] as $action): ?>
							<li class="came-action-item" id="action-<?php echo $action['id']; ?>">
								<div class="action-number"><?php echo $action['action_number']; ?></div>
								<div class="action-content">
									<textarea 
										class="action-textarea" 
										data-action-id="<?php echo $action['id']; ?>"
										data-project-id="<?php echo $project_id; ?>"
										data-action-type="<?php echo $type; ?>"
										data-action-number="<?php echo $action['action_number']; ?>"
										placeholder="<?php echo $cameController->getPlaceholderText($type); ?>"
										onblur="saveAction(this)"><?php echo htmlspecialchars($action['action_text']); ?></textarea>
									<div class="action-controls">
										<button type="button" class="delete-action-btn" onclick="deleteAction(<?php echo $action['id']; ?>, '<?php echo $type; ?>', <?php echo $action['action_number']; ?>)">
											🗑️ Eliminar
										</button>
									</div>
								</div>
							</li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
				</div>
				<?php endforeach; ?>
			</div>

			<!-- Navegación -->
			<div class="came-navigation">
				<a href="strategies.php?id=<?php echo $project_id; ?>" class="came-nav-btn">
					← 10. IDENTIFICACIÓN DE ESTRATEGIAS
				</a>
				<a href="<?php echo getBaseUrl(); ?>/Views/Users/projects.php" class="came-nav-btn">
					🏠 Regresar a Proyectos
				</a>
			</div>

			<!-- Mensajes de estado -->
			<div id="message-container"></div>
		</div>
	</main>

	<!-- JavaScript -->
	<script src="<?php echo getBaseUrl(); ?>/Publics/js/came-matrix.js"></script>
	<script>
		// Configuración global
		window.projectId = <?php echo $project_id; ?>;
		window.baseUrl = '<?php echo getBaseUrl(); ?>';
		
		// Inicializar la matriz CAME
		document.addEventListener('DOMContentLoaded', function() {
			console.log('Matriz CAME cargada para proyecto:', window.projectId);
		});
	</script>
	
	<?php include __DIR__ . '/../Users/footer.php'; ?>
</body>
</html>
