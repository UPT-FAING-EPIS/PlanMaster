<?php
session_start();
require_once __DIR__ . '/../../config/url_config.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../Controllers/AuthController.php';

// Verificar autenticación
AuthController::requireLogin();
$user = AuthController::getCurrentUser();

// Obtener ID del proyecto
$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($project_id <= 0) {
    echo "Error: ID de proyecto inválido";
    exit();
}

// Instanciar controlador
$controller = new ProjectController();

// Verificar que el proyecto pertenece al usuario
$project = $controller->getProject($project_id);
if (!$project || $project['user_id'] != $user['id']) {
    echo "Error: Proyecto no encontrado o sin permisos";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis Matriz BCG - PlanMaster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="h3 mb-0">
                            <i class="fas fa-chart-area me-3"></i>
                            Matriz BCG - <?php echo htmlspecialchars($project['project_name']); ?>
                        </h1>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h4>¡Análisis BCG funcionando!</h4>
                            <p><strong>Proyecto:</strong> <?php echo htmlspecialchars($project['project_name']); ?></p>
                            <p><strong>Empresa:</strong> <?php echo htmlspecialchars($project['company_name']); ?></p>
                            <p><strong>ID del Proyecto:</strong> <?php echo $project_id; ?></p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5>¿Qué es la Matriz BCG?</h5>
                                <p>La Matriz BCG (Boston Consulting Group) es una herramienta de análisis estratégico que clasifica los productos según:</p>
                                <ul>
                                    <li><strong>TCM:</strong> Tasa de Crecimiento del Mercado</li>
                                    <li><strong>PRM:</strong> Participación Relativa del Mercado</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>Los 4 Cuadrantes:</h5>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="p-2 border rounded mb-2" style="background: #fff3cd;">
                                            <strong>⭐ Estrellas</strong><br>
                                            <small>Alto TCM + Alto PRM</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 border rounded mb-2" style="background: #d1ecf1;">
                                            <strong>❓ Interrogantes</strong><br>
                                            <small>Alto TCM + Bajo PRM</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 border rounded mb-2" style="background: #d4edda;">
                                            <strong>🐄 Vacas Lecheras</strong><br>
                                            <small>Bajo TCM + Alto PRM</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 border rounded mb-2" style="background: #f8d7da;">
                                            <strong>🐕 Perros</strong><br>
                                            <small>Bajo TCM + Bajo PRM</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <form method="POST" action="../../Controllers/ProjectController.php?action=save_bcg_analysis" class="mt-4">
                            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                            
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Agregar Productos</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Nombre del Producto *</label>
                                                <input type="text" class="form-control" name="products[0][name]" placeholder="Ej: Producto A" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Pronóstico de Ventas *</label>
                                                <input type="number" class="form-control" name="products[0][sales_forecast]" step="0.01" min="0" placeholder="0.00" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">TCM - Crecimiento del Mercado (%) *</label>
                                                <input type="number" class="form-control" name="products[0][tcm_rate]" step="0.1" min="0" placeholder="10.5" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg me-3">
                                    <i class="fas fa-save"></i> Guardar Análisis BCG
                                </button>
                                <a href="project.php?id=<?php echo $project_id; ?>" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-times"></i> Volver al Proyecto
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>