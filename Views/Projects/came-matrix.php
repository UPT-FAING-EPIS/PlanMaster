<?php
session_start();
require_once __DIR__ . '/../../Controllers/AuthController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../config/url_config.php';

// Verificar que el usuario esté logueado
AuthController::requireLogin();

// Obtener el ID del proyecto
$project_id = intval($_GET['project_id'] ?? 0);
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
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_value_chain.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="<?php echo getBaseUrl(); ?>/Resources/favicon.ico">
    <style>
        .came-matrix-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            padding: 32px 24px;
            margin-top: 24px;
        }
        .came-title {
            color: #2bb3c0;
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 12px;
        }
        .came-context {
            background: #eaf6fa;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 18px;
            font-size: 1rem;
        }
        .came-reflection {
            background: #e0e0e0;
            border-radius: 6px;
            padding: 10px 16px;
            font-size: 0.98rem;
            font-style: italic;
            color: #1e293b;
            margin-bottom: 18px;
            text-align: center;
        }
        .came-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .came-table th, .came-table td {
            border: 1px solid #b6e0ef;
            padding: 8px 10px;
            text-align: left;
        }
        .came-table th {
            background: #2bb3c0;
            color: #fff;
            font-weight: 600;
            text-align: center;
        }
        .came-section-label {
            background: #2bb3c0;
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
            text-align: center;
            vertical-align: middle;
            width: 48px;
        }
        .came-action-input {
            width: 100%;
            border: 1px solid #b6e0ef;
            border-radius: 5px;
            padding: 6px 8px;
            font-size: 1rem;
            background: #f8fafc;
            transition: border-color 0.2s;
        }
        .came-action-input:focus {
            border-color: #2bb3c0;
            outline: none;
        }
        .came-table tr.section-header td {
            background: #eaf6fa;
            font-weight: 600;
            color: #2bb3c0;
            text-align: left;
            font-size: 1.05rem;
        }
        @media (max-width: 700px) {
            .came-matrix-container { padding: 12px 2px; }
            .came-table th, .came-table td { font-size: 0.95rem; }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <main class="main-content">
        <div class="container">
            <div class="project-header">
                <div class="project-info">
                    <h1 class="came-title">11. MATRIZ CAME</h1>
                    <p class="project-name"><?php echo htmlspecialchars($project['project_name']); ?></p>
                    <p class="company-name"><?php echo htmlspecialchars($project['company_name']); ?></p>
                </div>
            </div>
            <div class="came-matrix-container">
                <div class="came-context">
                    <p>A continuación y para finalizar de elaborar un Plan Estratégico, además de tener identificada la estrategia es necesario determinar acciones que permitan corregir las debilidades, afrontar las amenazas, mantener las fortalezas y explotar las oportunidades.</p>
                </div>
                <div class="came-reflection">
                    <strong>Reflexione y anote acciones a llevar a cabo teniendo en cuenta que estas acciones deben favorecer la ejecución exitosa de la estrategia general identificada.</strong>
                </div>
                <form class="came-matrix-form">
                    <table class="came-table">
                        <tr>
                            <th style="width:48px;">Acciones</th>
                            <th>Corregir las debilidades</th>
                        </tr>
                        <tr>
                            <td class="came-section-label" rowspan="4">C</td>
                            <td><input type="text" class="came-action-input" name="c1" placeholder="Acción 1"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="came-action-input" name="c2" placeholder="Acción 2"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="came-action-input" name="c3" placeholder="Acción 3"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="came-action-input" name="c4" placeholder="Acción 4"></td>
                        </tr>
                        <tr class="section-header">
                            <td colspan="2">Afrontar las amenazas</td>
                        </tr>
                        <tr>
                            <td class="came-section-label" rowspan="4">A</td>
                            <td><input type="text" class="came-action-input" name="a5" placeholder="Acción 5"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="came-action-input" name="a6" placeholder="Acción 6"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="came-action-input" name="a7" placeholder="Acción 7"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="came-action-input" name="a8" placeholder="Acción 8"></td>
                        </tr>
                        <tr class="section-header">
                            <td colspan="2">Mantener las fortalezas</td>
                        </tr>
                        <tr>
                            <td class="came-section-label" rowspan="4">M</td>
                            <td><input type="text" class="came-action-input" name="m9" placeholder="Acción 9"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="came-action-input" name="m10" placeholder="Acción 10"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="came-action-input" name="m11" placeholder="Acción 11"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="came-action-input" name="m12" placeholder="Acción 12"></td>
                        </tr>
                        <tr class="section-header">
                            <td colspan="2">Explotar las oportunidades</td>
                        </tr>
                        <tr>
                            <td class="came-section-label" rowspan="4">E</td>
                            <td><input type="text" class="came-action-input" name="e13" placeholder="Acción 13"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="came-action-input" name="e14" placeholder="Acción 14"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="came-action-input" name="e15" placeholder="Acción 15"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="came-action-input" name="e16" placeholder="Acción 16"></td>
                        </tr>
                    </table>
                    <div class="form-actions" style="text-align:center;">
                        <button type="button" class="btn btn-save" disabled>
                            💾 Guardar Matriz CAME
                        </button>
                        <a href="project.php?id=<?php echo $project_id; ?>" class="btn btn-secondary">
                            ↩️ Volver al Proyecto
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <?php include '../Users/footer.php'; ?>
</body>
</html>
