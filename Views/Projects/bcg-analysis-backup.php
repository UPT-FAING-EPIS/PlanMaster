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

// Obtener datos BCG existentes
$bcg_products = $projectController->getBCGAnalysis($project_id);
$bcg_matrix = $projectController->getBCGMatrix($project_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matriz BCG - <?php echo htmlspecialchars($project['project_name']); ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_bcg_analysis.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo getBaseUrl(); ?>/Resources/favicon.ico">
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Hero Section con estilo de los otros pasos -->
    <section class="hero-section bcg-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="hero-content">
                        <div class="breadcrumb-nav">
                            <a href="<?php echo getBaseUrl(); ?>/Views/Users/dashboard.php" class="breadcrumb-link">Dashboard</a>
                            <span class="breadcrumb-separator">></span>
                            <a href="project.php?id=<?php echo $project_id; ?>" class="breadcrumb-link">Proyecto</a>
                            <span class="breadcrumb-separator">></span>
                            <span class="breadcrumb-current">Matriz BCG</span>
                        </div>
                        
                        <h1 class="hero-title">
                            <span class="step-number">7.</span>
                            Análisis Interno: Matriz de Crecimiento - Participación BCG
                        </h1>
                        
                        <div class="project-info">
                            <div class="project-badge">
                                <i class="icon-briefcase"></i>
                                <span><?php echo htmlspecialchars($project['project_name']); ?></span>
                            </div>
                            <div class="company-badge">
                                <i class="icon-building"></i>
                                <span><?php echo htmlspecialchars($project['company_name']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="hero-icon">
                        📊
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">

    <!-- Introducción -->
    <div class="content-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="section-icon">📖</i>
                ¿Qué es la Matriz BCG?
            </h2>
        </div>
        <div class="section-content">
                <p class="text-muted mb-4">
                    Toda empresa debe analizar de forma periódica su cartera de productos y servicios.
                </p>
                <div class="alert alert-info mb-4">
                    <p class="mb-2">
                        <strong>La Matriz de crecimiento - participación (Matriz BCG)</strong> es un método gráfico de análisis de cartera de negocios desarrollado por <em>The Boston Consulting Group</em> en la década de 1970.
                    </p>
                    <p class="mb-2">
                        Su finalidad es ayudar a <strong>priorizar recursos</strong> entre distintas áreas de negocios o Unidades Estratégicas de Análisis (UEA), determinando en qué negocios invertir, desinvertir o incluso abandonar.
                    </p>
                    <p class="mb-0">
                        Se trata de una matriz con <strong>cuatro cuadrantes</strong>, cada uno propone una estrategia diferente. El <strong>eje vertical</strong> define el crecimiento en el mercado, y el <strong>horizontal</strong> la cuota de mercado.
                    </p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box mb-3">
                            <h5><i class="fas fa-trending-up text-success me-2"></i>Tasa de Crecimiento del Mercado (TCM)</h5>
                            <p class="small">Mide qué tan rápido está creciendo el mercado del producto. Se calcula como el porcentaje de crecimiento anual del mercado.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box mb-3">
                            <h5><i class="fas fa-chart-pie text-info me-2"></i>Participación Relativa del Mercado (PRM)</h5>
                            <p class="small">Compara las ventas del producto con las del competidor más fuerte. PRM = Ventas del producto / Ventas del mayor competidor.</p>
                        </div>
                    </div>
                </div>

                <div class="bcg-matrix-info">
                    <h4 class="matrix-title">Los Cuatro Cuadrantes de la Matriz BCG</h4>
                    <div class="quadrants-grid">
                        <div class="quadrant-item estrella">
                            <div class="quadrant-header">
                                <span class="quadrant-icon">⭐</span>
                                <h5>Estrellas</h5>
                            </div>
                            <div class="quadrant-criteria">
                                <span class="criteria-high">Alto TCM</span> + <span class="criteria-high">Alto PRM</span>
                            </div>
                            <p class="quadrant-strategy">Invertir para mantener liderazgo</p>
                        </div>

                        <div class="quadrant-item interrogante">
                            <div class="quadrant-header">
                                <span class="quadrant-icon">❓</span>
                                <h5>Interrogantes</h5>
                            </div>
                            <div class="quadrant-criteria">
                                <span class="criteria-high">Alto TCM</span> + <span class="criteria-low">Bajo PRM</span>
                            </div>
                            <p class="quadrant-strategy">Análisis cuidadoso - Invertir o Desinvertir</p>
                        </div>

                        <div class="quadrant-item vaca">
                            <div class="quadrant-header">
                                <span class="quadrant-icon">🐄</span>
                                <h5>Vacas Lecheras</h5>
                            </div>
                            <div class="quadrant-criteria">
                                <span class="criteria-low">Bajo TCM</span> + <span class="criteria-high">Alto PRM</span>
                            </div>
                            <p class="quadrant-strategy">Maximizar generación de efectivo</p>
                        </div>

                        <div class="quadrant-item perro">
                            <div class="quadrant-header">
                                <span class="quadrant-icon">🐕</span>
                                <h5>Perros</h5>
                            </div>
                            <div class="quadrant-criteria">
                                <span class="criteria-low">Bajo TCM</span> + <span class="criteria-low">Bajo PRM</span>
                            </div>
                            <p class="quadrant-strategy">Desinversión o eliminación</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Formulario Principal con 4 Mini Pasos -->
    <form id="bcgForm" method="POST" action="<?php echo getBaseUrl(); ?>/Controllers/ProjectController.php?action=save_bcg_analysis" class="step-form">
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

        <!-- Mini Paso 1: PREVISIÓN DE VENTAS -->
        <div class="mini-step">
            <div class="mini-step-header">
                <div class="step-number">1</div>
                <h3 class="step-title">PREVISIÓN DE VENTAS</h3>
                <button type="button" class="btn-add-mini" onclick="addProduct()">
                    <i class="icon-plus"></i> Agregar Producto
                </button>
            </div>
            
            <div class="mini-step-content">
                <div class="sales-forecast-table">
                    <div class="table-header">
                        <div class="col-product">PRODUCTOS</div>
                        <div class="col-sales">VENTAS</div>
                        <div class="col-percentage">% S/ TOTAL</div>
                        <div class="col-actions">ACCIONES</div>
                    </div>
                    <div id="products-container">
                        <!-- Productos se agregan dinámicamente aquí -->
                    </div>
                    <div class="sales-total">
                        <strong>TOTAL: <span id="total-sales">0.00</span></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mini Paso 2: TASAS DE CRECIMIENTO DEL MERCADO (TCM) -->
        <div class="mini-step">
            <div class="mini-step-header">
                <div class="step-number">2</div>
                <h3 class="step-title">TASAS DE CRECIMIENTO DEL MERCADO (TCM)</h3>
                <button type="button" class="btn-add-mini" onclick="addPeriod()">
                    <i class="icon-plus"></i> Agregar Período
                </button>
            </div>
            
            <div class="mini-step-content">
                <div class="tcm-table">
                    <div class="tcm-header">
                        <div class="col-periods">PERÍODOS</div>
                        <div class="col-markets">MERCADOS</div>
                    </div>
                    <div class="tcm-periods" id="periods-container">
                        <!-- Períodos se agregan dinámicamente -->
                    </div>
                </div>

                <!-- Tabla Resumen BCG -->
                <div class="bcg-summary-table">
                    <h4 class="table-title">Resumen BCG</h4>
                    <div class="bcg-table" id="bcg-summary">
                        <!-- Se genera automáticamente -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Mini Paso 3: EVOLUCIÓN DE LA DEMANDA GLOBAL SECTOR -->
        <div class="mini-step">
            <div class="mini-step-header">
                <div class="step-number">3</div>
                <h3 class="step-title">EVOLUCIÓN DE LA DEMANDA GLOBAL SECTOR (en miles de soles)</h3>
            </div>
            
            <div class="mini-step-content">
                <div class="demand-evolution-table" id="demand-evolution">
                    <!-- Tabla de evolución de demanda -->
                </div>
            </div>
        </div>

        <!-- Mini Paso 4: NIVELES DE VENTA DE LOS COMPETIDORES -->
        <div class="mini-step">
            <div class="mini-step-header">
                <div class="step-number">4</div>
                <h3 class="step-title">NIVELES DE VENTA DE LOS COMPETIDORES DE CADA PRODUCTO</h3>
            </div>
            
            <div class="mini-step-content">
                <div class="competitors-sales-table" id="competitors-sales">
                    <!-- Tabla de ventas de competidores por producto -->
                </div>
            </div>
        </div>

            <!-- Botones de navegación -->
            <div class="form-navigation">
                <div class="nav-buttons">
                    <a href="project.php?id=<?php echo $project_id; ?>" class="btn-secondary">
                        <i class="icon-arrow-left"></i>
                        Volver al Proyecto
                    </a>
                    
                    <div class="nav-buttons-right">
                        <button type="submit" name="save_and_exit" class="btn-outline">
                            <i class="icon-save"></i>
                            Guardar y Salir
                        </button>
                        
                        <button type="submit" class="btn-primary">
                            <i class="icon-arrow-right"></i>
                            Guardar y Continuar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

        <!-- Matriz BCG Visual (solo mostrar si hay datos) -->
        <?php if (count($bcg_matrix) > 0): ?>
        <div class="section-card mt-5">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-chart-area me-2"></i>
                    Matriz BCG Visual
                </h2>
            </div>
            <div class="section-content">
                <div class="bcg-matrix-container">
                    <canvas id="bcgMatrix" width="600" height="600"></canvas>
                </div>
                
                <div class="matrix-legend mt-4">
                    <h5>Resumen de Productos:</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Ventas Pronosticadas</th>
                                    <th>% sobre Total</th>
                                    <th>TCM (%)</th>
                                    <th>PRM</th>
                                    <th>Posición</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bcg_matrix as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td>$<?php echo number_format($product['sales_forecast'], 2); ?></td>
                                    <td><?php echo $product['sales_percentage']; ?>%</td>
                                    <td><?php echo $product['tcm_rate']; ?>%</td>
                                    <td><?php echo $product['prm_rate']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $product['position'] == 'estrella' ? 'warning' : 
                                                ($product['position'] == 'interrogante' ? 'info' : 
                                                ($product['position'] == 'vaca' ? 'success' : 'danger')); 
                                        ?>">
                                            <?php 
                                            $positions = [
                                                'estrella' => 'Estrella',
                                                'interrogante' => 'Interrogante', 
                                                'vaca' => 'Vaca Lechera',
                                                'perro' => 'Perro'
                                            ];
                                            echo $positions[$product['position']];
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        </div> <!-- container -->
    </main> <!-- main-content -->

    <!-- Footer -->
    <?php include __DIR__ . '/../Users/footer.php'; ?>

    <!-- JavaScript -->
    <script src="<?php echo getBaseUrl(); ?>/Publics/js/dashboard.js"></script>
    <script>
        let productCount = 0;
        let periodCount = 0;
        let products = [];

        // Inicializar la aplicación
        document.addEventListener('DOMContentLoaded', function() {
            initializeBCG();
        });

        function initializeBCG() {
            // Agregar producto inicial
            addProduct();
            addPeriod();
            updateBCGSummary();
        }

        // Función para agregar producto
        function addProduct() {
            productCount++;
            const productName = `Producto ${productCount}`;
            products.push({
                name: productName,
                sales: 0,
                percentage: 0
            });

            const container = document.getElementById('products-container');
            const productRow = document.createElement('div');
            productRow.className = 'product-row';
            productRow.setAttribute('data-product-id', productCount);
            
            productRow.innerHTML = `
                <div>
                    <input type="text" class="product-input" placeholder="Nombre del producto" 
                           value="${productName}" onchange="updateProductName(${productCount}, this.value)">
                </div>
                <div>
                    <input type="number" class="product-input" placeholder="0.00" step="0.01" 
                           onchange="updateProductSales(${productCount}, this.value)">
                </div>
                <div class="percentage-display">0%</div>
                <div>
                    <button type="button" class="btn-remove" onclick="removeProduct(${productCount})">
                        Eliminar
                    </button>
                </div>
            `;
            
            container.appendChild(productRow);
            updateTCMTable();
            updateDemandEvolution();
            updateCompetitorsSales();
        }

        function removeProduct(productId) {
            if (products.length <= 1) {
                alert('Debe mantener al menos un producto');
                return;
            }

            const productRow = document.querySelector(`[data-product-id="${productId}"]`);
            if (productRow) {
                productRow.remove();
            }

            products = products.filter((_, index) => index + 1 !== productId);
            updateSalesPercentages();
            updateTCMTable();
            updateBCGSummary();
        }

        function updateProductName(productId, name) {
            if (products[productId - 1]) {
                products[productId - 1].name = name;
                updateTCMTable();
                updateBCGSummary();
            }
        }

        function updateProductSales(productId, sales) {
            if (products[productId - 1]) {
                products[productId - 1].sales = parseFloat(sales) || 0;
                updateSalesPercentages();
            }
        }

        function updateSalesPercentages() {
            const totalSales = products.reduce((sum, product) => sum + product.sales, 0);
            
            products.forEach((product, index) => {
                const percentage = totalSales > 0 ? ((product.sales / totalSales) * 100).toFixed(1) : 0;
                product.percentage = percentage;
                
                const productRow = document.querySelector(`[data-product-id="${index + 1}"]`);
                if (productRow) {
                    const percentageDisplay = productRow.querySelector('.percentage-display');
                    if (percentageDisplay) {
                        percentageDisplay.textContent = percentage + '%';
                    }
                }
            });

            const totalElement = document.getElementById('total-sales');
            if (totalElement) {
                totalElement.textContent = totalSales.toFixed(2);
            }

            updateBCGSummary();
        }

        function addPeriod() {
            periodCount++;
            const container = document.getElementById('periods-container');
            
            const periodRow = document.createElement('div');
            periodRow.className = 'period-row';
            periodRow.setAttribute('data-period-id', periodCount);
            
            let productsGrid = '';
            for (let i = 0; i < products.length; i++) {
                productsGrid += `<input type="number" placeholder="0.0%" step="0.1" class="product-input">`;
            }
            
            periodRow.innerHTML = `
                <div class="period-input">
                    <input type="number" placeholder="Año inicial" min="2000" max="2030">
                    <input type="number" placeholder="Año final" min="2000" max="2030">
                </div>
                <div class="products-grid" style="grid-template-columns: repeat(${products.length}, 1fr);">
                    ${productsGrid}
                </div>
            `;
            
            container.appendChild(periodRow);
        }

        function updateTCMTable() {
            const periodsContainer = document.getElementById('periods-container');
            const periods = periodsContainer.querySelectorAll('.period-row');
            
            periods.forEach(period => {
                const productsGrid = period.querySelector('.products-grid');
                productsGrid.style.gridTemplateColumns = `repeat(${products.length}, 1fr)`;
                
                let productsHTML = '';
                for (let i = 0; i < products.length; i++) {
                    productsHTML += `<input type="number" placeholder="0.0%" step="0.1" class="product-input">`;
                }
                productsGrid.innerHTML = productsHTML;
            });
        }

        function updateBCGSummary() {
            const container = document.getElementById('bcg-summary');
            
            let headerColumns = 'auto';
            for (let i = 0; i < products.length; i++) {
                headerColumns += ' 1fr';
            }
            
            let summaryHTML = `
                <div class="bcg-row header" style="grid-template-columns: ${headerColumns};">
                    <div>BCG</div>
                    ${products.map(product => `<div>${product.name}</div>`).join('')}
                </div>
                <div class="bcg-row" style="grid-template-columns: ${headerColumns};">
                    <div>TCM</div>
                    ${products.map(() => `<div>0.00%</div>`).join('')}
                </div>
                <div class="bcg-row" style="grid-template-columns: ${headerColumns};">
                    <div>PRM</div>
                    ${products.map(() => `<div>0.00</div>`).join('')}
                </div>
                <div class="bcg-row" style="grid-template-columns: ${headerColumns};">
                    <div>% S/VTAS</div>
                    ${products.map(product => `<div>${product.percentage}%</div>`).join('')}
                </div>
            `;
            
            container.innerHTML = summaryHTML;
        }

        function updateDemandEvolution() {
            const container = document.getElementById('demand-evolution');
            container.innerHTML = `<p class="text-muted">Tabla de evolución de demanda por implementar</p>`;
        }

        function updateCompetitorsSales() {
            const container = document.getElementById('competitors-sales');
            container.innerHTML = `<p class="text-muted">Tabla de ventas de competidores por implementar</p>`;
        }
    </script>

    <!-- Script de validación del formulario -->
    <script>
        // Validación del formulario
        document.getElementById('bcgForm').addEventListener('submit', function(e) {
            const productsContainer = document.getElementById('products-container');
            const productRows = productsContainer.querySelectorAll('.product-row');
            let valid = true;
            
            if (productRows.length === 0) {
                valid = false;
                alert('Debe agregar al menos un producto');
            } else {
                productRows.forEach(row => {
                    const nameInput = row.querySelector('input[type="text"]');
                    const salesInput = row.querySelector('input[type="number"]');
                    
                    if (!nameInput.value.trim() || !salesInput.value || salesInput.value <= 0) {
                        valid = false;
                    }
                });
            }
            
            if (!valid) {
                e.preventDefault();
                alert('Por favor complete todos los campos obligatorios correctamente');
            }
        });
            productDiv.setAttribute('data-index', productCount);
            
            productDiv.innerHTML = `
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-box-open me-2"></i>
                            Producto ${productCount + 1}
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeProduct(this)">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-tag me-1"></i>Nombre del Producto *
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="products[${productCount}][name]" 
                                           placeholder="Ej: Producto ${productCount + 1}"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-dollar-sign me-1"></i>Pronóstico de Ventas *
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           name="products[${productCount}][sales_forecast]" 
                                           step="0.01" 
                                           min="0"
                                           placeholder="0.00"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-percentage me-1"></i>TCM - Crecimiento del Mercado (%) *
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           name="products[${productCount}][tcm_rate]" 
                                           step="0.1" 
                                           min="0"
                                           placeholder="10.5"
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="competitors-section mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6>
                                    <i class="fas fa-users me-2"></i>
                                    Competidores Principales
                                </h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCompetitor(${productCount})">
                                    <i class="fas fa-plus"></i> Agregar Competidor
                                </button>
                            </div>
                            <div class="competitors-container" id="competitors-${productCount}">
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(productDiv);
            competitorCounts[productCount] = 0;
            productCount++;
            
            // Actualizar índices
            updateProductIndexes();
        }

        // Función para remover producto
        function removeProduct(button) {
            const container = document.getElementById('products-container');
            if (container.children.length > 1) {
                button.closest('.product-item').remove();
                updateProductIndexes();
            } else {
                alert('Debe mantener al menos un producto');
            }
        }

        // Función para agregar competidor
        function addCompetitor(productIndex) {
            const container = document.getElementById(`competitors-${productIndex}`);
            const competitorCount = competitorCounts[productIndex] || 0;
            
            const competitorDiv = document.createElement('div');
            competitorDiv.className = 'competitor-item mb-2';
            competitorDiv.innerHTML = `
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <input type="text" 
                               class="form-control" 
                               name="competitors[${productIndex}][${competitorCount}][name]" 
                               placeholder="Nombre del competidor">
                    </div>
                    <div class="col-md-5">
                        <input type="number" 
                               class="form-control" 
                               name="competitors[${productIndex}][${competitorCount}][sales]" 
                               step="0.01" 
                               min="0"
                               placeholder="Ventas del competidor">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCompetitor(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            
            container.appendChild(competitorDiv);
            competitorCounts[productIndex]++;
        }

        // Función para remover competidor
        function removeCompetitor(button) {
            button.closest('.competitor-item').remove();
        }

        // Actualizar índices de productos
        function updateProductIndexes() {
            const products = document.querySelectorAll('.product-item');
            products.forEach((product, index) => {
                product.setAttribute('data-index', index);
                
                // Actualizar nombre del producto en el header
                const header = product.querySelector('.card-header h5');
                header.innerHTML = `<i class="fas fa-box-open me-2"></i>Producto ${index + 1}`;
                
                // Actualizar nombres de inputs
                const inputs = product.querySelectorAll('input[name^="products["]');
                inputs.forEach(input => {
                    const name = input.name;
                    const newName = name.replace(/products\[\d+\]/, `products[${index}]`);
                    input.name = newName;
                });
                
                // Actualizar ID de contenedor de competidores
                const competitorsContainer = product.querySelector('.competitors-container');
                if (competitorsContainer) {
                    competitorsContainer.id = `competitors-${index}`;
                }
                
                // Actualizar botón de agregar competidor
                const addCompetitorBtn = product.querySelector('button[onclick*="addCompetitor"]');
                if (addCompetitorBtn) {
                    addCompetitorBtn.setAttribute('onclick', `addCompetitor(${index})`);
                }
            });
            
            productCount = products.length;
        }

        // Dibujar matriz BCG si hay datos
        <?php if (count($bcg_matrix) > 0): ?>
        function drawBCGMatrix() {
            const canvas = document.getElementById('bcgMatrix');
            const ctx = canvas.getContext('2d');
            
            // Limpiar canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Configuración
            const margin = 80;
            const width = canvas.width - 2 * margin;
            const height = canvas.height - 2 * margin;
            
            // Dibujar ejes
            ctx.strokeStyle = '#333';
            ctx.lineWidth = 2;
            
            // Eje X (PRM)
            ctx.beginPath();
            ctx.moveTo(margin, margin + height);
            ctx.lineTo(margin + width, margin + height);
            ctx.stroke();
            
            // Eje Y (TCM)
            ctx.beginPath();
            ctx.moveTo(margin, margin);
            ctx.lineTo(margin, margin + height);
            ctx.stroke();
            
            // Líneas divisorias (TCM = 10%, PRM = 1.0)
            ctx.strokeStyle = '#666';
            ctx.lineWidth = 1;
            ctx.setLineDash([5, 5]);
            
            // Línea horizontal en TCM = 10%
            const tcm10Y = margin + height * 0.6; // 10% está en el 60% desde arriba
            ctx.beginPath();
            ctx.moveTo(margin, tcm10Y);
            ctx.lineTo(margin + width, tcm10Y);
            ctx.stroke();
            
            // Línea vertical en PRM = 1.0
            const prm1X = margin + width * 0.5; // PRM 1.0 está en el centro
            ctx.beginPath();
            ctx.moveTo(prm1X, margin);
            ctx.lineTo(prm1X, margin + height);
            ctx.stroke();
            
            ctx.setLineDash([]);
            
            // Etiquetas de cuadrantes
            ctx.font = '16px Arial';
            ctx.fillStyle = '#666';
            ctx.textAlign = 'center';
            
            // Estrella (arriba izquierda)
            ctx.fillText('⭐ Estrellas', margin + width * 0.25, margin + height * 0.15);
            
            // Interrogante (arriba derecha)
            ctx.fillText('❓ Interrogantes', margin + width * 0.75, margin + height * 0.15);
            
            // Vaca (abajo izquierda)
            ctx.fillText('🐄 Vacas Lecheras', margin + width * 0.25, margin + height * 0.85);
            
            // Perro (abajo derecha)
            ctx.fillText('🐕 Perros', margin + width * 0.75, margin + height * 0.85);
            
            // Dibujar productos
            const products = <?php echo json_encode($bcg_matrix); ?>;
            
            products.forEach(product => {
                // Calcular posición
                const prm = Math.max(0, Math.min(3, product.prm_rate)); // Limitar PRM entre 0 y 3
                const tcm = Math.max(0, Math.min(30, product.tcm_rate)); // Limitar TCM entre 0 y 30%
                
                const x = margin + (prm / 3) * width;
                const y = margin + height - (tcm / 30) * height;
                
                // Tamaño de burbuja basado en ventas (min 10, max 40)
                const radius = Math.max(10, Math.min(40, product.bubble_size * 2));
                
                // Color según posición
                let color;
                switch (product.position) {
                    case 'estrella': color = '#ffc107'; break;
                    case 'interrogante': color = '#17a2b8'; break;
                    case 'vaca': color = '#28a745'; break;
                    case 'perro': color = '#dc3545'; break;
                    default: color = '#6c757d';
                }
                
                // Dibujar burbuja
                ctx.fillStyle = color + '80'; // Añadir transparencia
                ctx.beginPath();
                ctx.arc(x, y, radius, 0, 2 * Math.PI);
                ctx.fill();
                
                // Borde
                ctx.strokeStyle = color;
                ctx.lineWidth = 2;
                ctx.stroke();
                
                // Etiqueta del producto
                ctx.fillStyle = '#000';
                ctx.font = '12px Arial';
                ctx.textAlign = 'center';
                ctx.fillText(product.product_name, x, y - radius - 10);
            });
            
            // Etiquetas de ejes
            ctx.fillStyle = '#333';
            ctx.font = '14px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('Participación Relativa del Mercado (PRM)', margin + width / 2, canvas.height - 20);
            
            ctx.save();
            ctx.translate(20, margin + height / 2);
            ctx.rotate(-Math.PI / 2);
            ctx.fillText('Tasa de Crecimiento del Mercado (%)', 0, 0);
            ctx.restore();
        }
        
        // Dibujar matriz cuando cargue la página
        document.addEventListener('DOMContentLoaded', drawBCGMatrix);
        <?php endif; ?>

        // Validación del formulario
        document.getElementById('bcgForm').addEventListener('submit', function(e) {
            const products = document.querySelectorAll('.product-item');
            
            if (products.length === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un producto');
                return;
            }
            
            // Validar cada producto
            let valid = true;
            products.forEach((product, index) => {
                const name = product.querySelector(`input[name="products[${index}][name]"]`).value.trim();
                const sales = product.querySelector(`input[name="products[${index}][sales_forecast]"]`).value;
                const tcm = product.querySelector(`input[name="products[${index}][tcm_rate]"]`).value;
                
                if (!name || !sales || sales <= 0 || !tcm || tcm < 0) {
                    valid = false;
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Por favor complete todos los campos obligatorios correctamente');
            }
        });
    </script>
</body>
</html>