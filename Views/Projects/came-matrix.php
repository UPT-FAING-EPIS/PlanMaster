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
			<section class="came-description">
				<p>A continuación y para finalizar de elaborar un Plan Estratégico, además de tener identificada la estrategia es necesario determinar acciones que permitan corregir las debilidades, afrontar las amenazas, mantener las fortalezas y explotar las oportunidades.</p>
				<div class="came-reflection-box">
					Reflexione y anote acciones a llevar a cabo teniendo en cuenta que estas acciones deben favorecer la ejecución exitosa de la estrategia general identificada
				</div>
			</section>
			<section class="came-matrix-table">
				<form>
					<table class="came-table">
						<thead>
							<tr>
								<th style="width:60px;">Acciones</th>
								<th>Corregir las debilidades<span>C</span></th>
								<th>Afrontar las amenazas<span>A</span></th>
								<th>Mantener las fortalezas<span>M</span></th>
								<th>Explotar las oportunidades<span>E</span></th>
							</tr>
						</thead>
						<tbody>
							<?php for ($i = 1; $i <= 16; $i++): ?>
							<tr>
								<td><?php echo $i; ?></td>
								<td><?php if ($i <= 4): ?><textarea rows="2" placeholder="Describa la acción para corregir debilidades..." disabled></textarea><?php endif; ?></td>
								<td><?php if ($i > 4 && $i <= 8): ?><textarea rows="2" placeholder="Describa la acción para afrontar amenazas..." disabled></textarea><?php endif; ?></td>
								<td><?php if ($i > 8 && $i <= 12): ?><textarea rows="2" placeholder="Describa la acción para mantener fortalezas..." disabled></textarea><?php endif; ?></td>
								<td><?php if ($i > 12): ?><textarea rows="2" placeholder="Describa la acción para explotar oportunidades..." disabled></textarea><?php endif; ?></td>
							</tr>
							<?php endfor; ?>
						</tbody>
					</table>
				</form>
			</section>
			<div class="came-navigation">
				<a href="#" class="btn-main">&#8592; 10. IDENTIFICACIÓN DE ESTRATEGIAS</a>
				<a href="#" class="btn-main">RESUMEN ESTRATÉGICO &#8594;</a>
			</div>
		</div>
	</main>
	<?php include __DIR__ . '/../Users/footer.php'; ?>
</body>
</html>
