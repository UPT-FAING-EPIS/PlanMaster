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

// Obtener análisis Porter existente (si lo implementas en el controlador)
$porterData = $projectController->getPorterAnalysis($project_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Matriz de Porter - <?php echo htmlspecialchars($project['project_name']); ?></title>
	<!-- CSS -->
	<link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">
	<link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_project.css">
	<link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_value_chain.css">
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
					<h1>🔷 Análisis Externo Microentorno: Matriz de Porter</h1>
					<p class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></p>
				</div>
			</div>
			<section class="porter-description">
				<h2>¿Qué es la Matriz de Porter?</h2>
				<p>El Modelo de las 5 Fuerzas de Porter estudia la competencia de un sector en función de la amenaza de nuevos competidores, productos sustitutos, poder de negociación de proveedores y clientes, y la rivalidad entre empresas. Permite analizar la rentabilidad y estabilidad del sector.</p>
			</section>
			<section class="porter-matrix-graphic">
				<img src="<?php echo getBaseUrl(); ?>/Resources/porter-matrix.png" alt="Matriz de Porter" style="max-width: 100%; margin: 0 auto; display: block;">
			</section>
			<form method="post" action="<?php echo getBaseUrl(); ?>/Controllers/ProjectController.php">
				<input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
				<input type="hidden" name="action" value="savePorterAnalysis">
				<div class="porter-forces">
					<h2>Analiza cada fuerza competitiva</h2>
					<div class="force-block">
						<label for="force1">1. Amenaza de entrada de nuevos competidores</label>
						<textarea id="force1" name="force1" rows="4" placeholder="Describe la amenaza de nuevos competidores..." required><?php echo htmlspecialchars($porterData['force1'] ?? ''); ?></textarea>
					</div>
					<div class="force-block">
						<label for="force2">2. Rivalidad entre las empresas del sector</label>
						<textarea id="force2" name="force2" rows="4" placeholder="Describe la rivalidad en el sector..." required><?php echo htmlspecialchars($porterData['force2'] ?? ''); ?></textarea>
					</div>
					<div class="force-block">
						<label for="force3">3. Amenaza de productos sustitutos</label>
						<textarea id="force3" name="force3" rows="4" placeholder="Describe la amenaza de productos sustitutos..." required><?php echo htmlspecialchars($porterData['force3'] ?? ''); ?></textarea>
					</div>
					<div class="force-block">
						<label for="force4">4. Poder de negociación de los clientes</label>
						<textarea id="force4" name="force4" rows="4" placeholder="Describe el poder de negociación de los clientes..." required><?php echo htmlspecialchars($porterData['force4'] ?? ''); ?></textarea>
					</div>
					<div class="force-block">
						<label for="force5">5. Poder de negociación de los proveedores</label>
						<textarea id="force5" name="force5" rows="4" placeholder="Describe el poder de negociación de los proveedores..." required><?php echo htmlspecialchars($porterData['force5'] ?? ''); ?></textarea>
					</div>
				</div>
				<div class="form-actions" style="text-align:center; margin-top:2rem;">
					<button type="submit" class="btn-main">Guardar análisis</button>
				</div>
			</form>
		</div>
	</main>
	<?php include __DIR__ . '/../Users/footer.php'; ?>
</body>
</html>
