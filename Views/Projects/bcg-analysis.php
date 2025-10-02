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

// Obtener datos del usuario para el header
$user = AuthController::getCurrentUser();

// Obtener datos BCG existentes
$bcg_products = $projectController->getBCGAnalysis($project_id);
$bcg_matrix = $projectController->getBCGMatrix($project_id);

// Preparar datos para JavaScript
$bcg_data_json = json_encode($bcg_products);
$bcg_matrix_json = json_encode($bcg_matrix);
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
                        <i class="fas fa-info-circle me-2"></i>
                        ¿Qué es la Matriz BCG?
                    </h2>
                </div>
                <div class="section-content">
                    <p class="text-muted mb-4">
                        Toda empresa debe analizar de forma periódica su cartera de productos y servicios.
                        La matriz BCG es una herramienta de análisis estratégico que evalúa los productos según su participación relativa en el mercado y la tasa de crecimiento del mercado.
                    </p>
                </div>
            </div>

            <!-- Mensajes -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

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
                        <button type="button" class="btn-add-mini" onclick="updateDemandEvolution()">
                            <i class="icon-refresh"></i> Actualizar
                        </button>
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
                        <button type="button" class="btn-add-mini" onclick="updateCompetitorsSales()">
                            <i class="icon-refresh"></i> Actualizar
                        </button>
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

            <!-- Matriz BCG Visual (solo mostrar si hay datos) -->
            <?php if (count($bcg_matrix) > 0): ?>
            <div class="section-card mt-5">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-chart-area me-2"></i>
                        Matriz BCG Calculada
                    </h2>
                </div>
                <div class="section-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Ventas</th>
                                    <th>% Ventas</th>
                                    <th>TCM</th>
                                    <th>PRM</th>
                                    <th>Posición</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bcg_matrix as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td>$<?php echo number_format($item['sales_forecast'], 2); ?></td>
                                    <td><?php echo $item['sales_percentage']; ?>%</td>
                                    <td><?php echo $item['tcm_rate']; ?>%</td>
                                    <td><?php echo $item['prm_rate']; ?></td>
                                    <td><strong><?php echo ucfirst($item['position']); ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div> <!-- container -->
    </main> <!-- main-content -->

    <!-- JavaScript -->
    <script src="<?php echo getBaseUrl(); ?>/Publics/js/dashboard.js"></script>
    <script>
        let productCount = 0;
        let periodCount = 0;
        let products = [];
        
        // Datos existentes del servidor
        const existingBCGData = <?php echo $bcg_data_json; ?>;
        const existingBCGMatrix = <?php echo $bcg_matrix_json; ?>;
        
        console.log('Datos BCG existentes:', existingBCGData);

        // Inicializar la aplicación
        document.addEventListener('DOMContentLoaded', function() {
            initializeBCG();
        });

        function initializeBCG() {
            // Si hay datos existentes, cargarlos
            if (existingBCGData && existingBCGData.length > 0) {
                loadExistingData();
            } else {
                // Si no hay datos, inicializar con un producto vacío
                addProduct();
            }
            
            addPeriod();
            updateBCGSummary();
            
            // Inicializar mini pasos 3 y 4 solo al cargar la página
            setTimeout(() => {
                initializeDemandEvolution();
                initializeCompetitorsSales();
            }, 100);
        }
        
        function loadExistingData() {
            console.log('Cargando datos existentes...');
            
            existingBCGData.forEach((product, index) => {
                products.push({
                    id: product.id,
                    name: product.product_name,
                    sales: parseFloat(product.sales_forecast),
                    tcm: parseFloat(product.tcm_calculated || 0),
                    percentage: parseFloat(product.sales_percentage || 0),
                    competitors: product.competitors || []
                });
            });
            
            // Usar la función rebuildProductsList para crear los elementos DOM
            rebuildProductsList();
            
            // Cargar períodos TCM si existen
            loadExistingMarketEvolution();
            
            // Cargar competidores si existen
            loadExistingCompetitors();
            
            // Forzar recálculo de porcentajes después de un pequeño delay
            setTimeout(() => {
                updateSalesPercentages();
                updateTCMPeriods();
                updateDemandEvolution();
                updateCompetitorsSales();
                updateBCGSummary();
            }, 100);
        }

        function loadExistingMarketEvolution() {
            existingBCGData.forEach((product, productIndex) => {
                if (product.market_evolution && product.market_evolution.length > 0) {
                    product.market_evolution.forEach(evolution => {
                        // Cargar datos de evolución del mercado
                        // Esta funcionalidad se implementaría para cargar períodos específicos
                    });
                }
            });
        }

        function loadExistingCompetitors() {
            existingBCGData.forEach((product, productIndex) => {
                if (product.competitors && product.competitors.length > 0) {
                    // Los competidores ya están en el array products, 
                    // se cargarán automáticamente cuando se llame updateCompetitorsSales
                    console.log(`Producto ${productIndex} tiene ${product.competitors.length} competidores`);
                }
            });
        }

        // Función para inicializar solo al cargar (no regenera si ya existe contenido)
        function initializeDemandEvolution() {
            const container = document.getElementById('demand-evolution');
            if (container && (container.innerHTML.trim() === '' || container.innerHTML.includes('Primero configure'))) {
                updateDemandEvolution();
            }
        }

        // Función para inicializar solo al cargar (no regenera si ya existe contenido)
        function initializeCompetitorsSales() {
            const container = document.getElementById('competitors-sales');
            if (container && (container.innerHTML.trim() === '' || container.innerHTML.includes('Tabla de ventas de competidores por implementar'))) {
                updateCompetitorsSales();
            }
        }

        function addProduct() {
            const productIndex = products.length; // Usar el tamaño actual del array
            const productName = `Producto ${productIndex + 1}`;
            
            products.push({
                name: productName,
                sales: 0,
                percentage: 0
            });

            const container = document.getElementById('products-container');
            const productRow = document.createElement('div');
            productRow.className = 'product-row';
            productRow.setAttribute('data-product-index', productIndex);
            
            productRow.innerHTML = `
                <div>
                    <input type="text" 
                           name="products[${productIndex}][name]"
                           class="product-input" 
                           placeholder="Nombre del producto" 
                           value="${productName}" 
                           onchange="updateProductName(${productIndex}, this.value)">
                </div>
                <div>
                    <input type="number" 
                           name="products[${productIndex}][sales_forecast]"
                           class="product-input" 
                           placeholder="0.00" 
                           step="0.01" 
                           min="0"
                           onchange="updateProductSales(${productIndex}, this.value)"
                           oninput="updateProductSales(${productIndex}, this.value)">
                    <input type="hidden" 
                           name="products[${productIndex}][tcm_rate]"
                           class="tcm-hidden-input"
                           value="0">
                </div>
                <div class="percentage-display">0%</div>
                <div>
                    <button type="button" class="btn-remove" onclick="removeProduct(${productIndex})">
                        Eliminar
                    </button>
                </div>
            `;
            
            container.appendChild(productRow);
            updateTCMTable();
        }

        function removeProduct(productIndex) {
            if (products.length <= 1) {
                alert('Debe mantener al menos un producto');
                return;
            }
            
            // Eliminar del array
            products.splice(productIndex, 1);
            
            // Recrear toda la lista para mantener índices correctos
            rebuildProductsList();
            updateSalesPercentages();
            updateTCMTable();
            updateBCGSummary();
        }
        
        function rebuildProductsList() {
            const container = document.getElementById('products-container');
            if (!container) return;
            
            container.innerHTML = '';
            
            products.forEach((product, index) => {
                const productRow = document.createElement('div');
                productRow.className = 'product-row';
                productRow.setAttribute('data-product-index', index);
                
                productRow.innerHTML = `
                    <div>
                        <input type="text" 
                               name="products[${index}][name]"
                               class="product-input" 
                               placeholder="Nombre del producto" 
                               value="${product.name}" 
                               onchange="updateProductName(${index}, this.value)">
                    </div>
                    <div>
                        <input type="number" 
                               name="products[${index}][sales_forecast]"
                               class="product-input" 
                               placeholder="0.00" 
                               step="0.01" 
                               min="0"
                               value="${product.sales || ''}"
                               onchange="updateProductSales(${index}, this.value)"
                               oninput="updateProductSales(${index}, this.value)">
                        <input type="hidden" 
                               name="products[${index}][tcm_rate]"
                               class="tcm-hidden-input"
                               value="0">
                    </div>
                    <div class="percentage-display">${product.percentage || 0}%</div>
                    <div>
                        <button type="button" class="btn-remove" onclick="removeProduct(${index})">
                            Eliminar
                        </button>
                    </div>
                `;
                
                container.appendChild(productRow);
            });
        }

        function updateProductName(productIndex, name) {
            if (products[productIndex]) {
                products[productIndex].name = name;
                updateTCMTable();
                updateBCGSummary();
            }
        }

        function updateProductSales(productIndex, sales) {
            console.log(`Actualizando ventas del producto ${productIndex}: ${sales}`);
            if (products[productIndex]) {
                products[productIndex].sales = parseFloat(sales) || 0;
            }
            // Llamar inmediatamente a la función de actualización de porcentajes
            updateSalesPercentages();
        }

        function updateSalesPercentages() {
            console.log('Actualizando porcentajes de ventas...');
            
            // Obtener valores reales de los inputs de ventas
            const salesInputs = document.querySelectorAll('input[name*="[sales_forecast]"]');
            let totalSales = 0;
            const salesValues = [];
            
            salesInputs.forEach((input, index) => {
                const sales = parseFloat(input.value) || 0;
                salesValues[index] = sales;
                totalSales += sales;
                
                // Actualizar también el array products
                if (products[index]) {
                    products[index].sales = sales;
                }
            });
            
            console.log('Total de ventas:', totalSales);
            console.log('Valores de ventas:', salesValues);
            
            // Calcular y actualizar porcentajes
            salesInputs.forEach((input, index) => {
                const sales = salesValues[index] || 0;
                const percentage = totalSales > 0 ? ((sales / totalSales) * 100) : 0;
                
                // Actualizar el array products
                if (products[index]) {
                    products[index].percentage = percentage.toFixed(1);
                }
                
                // Buscar y actualizar la visualización del porcentaje
                const productRow = document.querySelector(`[data-product-index="${index}"]`);
                if (productRow) {
                    const percentageDisplay = productRow.querySelector('.percentage-display');
                    if (percentageDisplay) {
                        percentageDisplay.textContent = percentage.toFixed(1) + '%';
                        console.log(`Producto ${index + 1}: ${sales} (${percentage.toFixed(1)}%)`);
                    }
                }
            });

            // Actualizar el total
            const totalElement = document.getElementById('total-sales');
            if (totalElement) {
                totalElement.textContent = totalSales.toFixed(2);
            }

            updateBCGSummary();
        }

        // Mini Paso 2: Funciones de períodos TCM
        function addPeriod() {
            periodCount++;
            const container = document.getElementById('periods-container');
            
            if (!container) return;
            
            const periodRow = document.createElement('div');
            periodRow.className = 'period-row';
            periodRow.setAttribute('data-period-id', periodCount);
            
            // Crear grid de productos con inputs TCM
            let productsGrid = '';
            products.forEach((product, index) => {
                productsGrid += `
                    <div class="product-tcm-cell">
                        <label class="product-label">${product.name}</label>
                        <div class="percentage-input-wrapper">
                            <input type="number" 
                                   name="periods[${index}][${periodCount}][tcm_percentage]"
                                   placeholder="0.0" 
                                   step="0.1" 
                                   min="0" 
                                   max="100"
                                   class="product-input percentage-input" 
                                   data-product="${index}"
                                   data-period="${periodCount}"
                                   onchange="calculateTCM(${periodCount}, ${index}, this.value)">
                            <span class="percentage-symbol">%</span>
                        </div>
                    </div>
                `;
            });
            
            periodRow.innerHTML = `
                <div class="period-input">
                    <div class="years-row">
                        <div class="year-group">
                            <label>Año inicial:</label>
                            <input type="number" 
                                   name="periods[${periodCount}][start_year]"
                                   placeholder="2020" 
                                   min="2000" 
                                   max="2030" 
                                   class="year-input start-year" 
                                   onchange="autoFillEndYear(this, ${periodCount})">
                        </div>
                        <span class="year-separator">-</span>
                        <div class="year-group">
                            <label>Año final:</label>
                            <input type="number" 
                                   name="periods[${periodCount}][end_year]"
                                   placeholder="2021" 
                                   min="2000" 
                                   max="2030" 
                                   class="year-input end-year" 
                                   readonly>
                        </div>
                        <button type="button" class="btn-remove-period" onclick="removePeriod(${periodCount})">
                            Eliminar Período
                        </button>
                    </div>
                </div>
                <div class="products-grid" style="grid-template-columns: repeat(${Math.max(1, products.length)}, 1fr);">
                    ${productsGrid}
                </div>
            `;
            
            container.appendChild(periodRow);
            updateBCGSummary();
        }

        function removePeriod(periodId) {
            const periodRow = document.querySelector(`[data-period-id="${periodId}"]`);
            if (periodRow) {
                periodRow.remove();
                updateBCGSummary();
            }
        }

        // Auto completar año final
        function autoFillEndYear(startYearInput, periodId) {
            const startYear = parseInt(startYearInput.value);
            if (startYear && startYear >= 2000) {
                const periodRow = document.querySelector(`[data-period-id="${periodId}"]`);
                const endYearInput = periodRow.querySelector('.end-year');
                if (endYearInput) {
                    endYearInput.value = startYear + 1;
                }
            }
        }

        // Calcular TCM basado en los datos ingresados
        function calculateTCM(periodId, productIndex, percentage) {
            const value = parseFloat(percentage) || 0;
            if (value < 0 || value > 100) {
                alert('El porcentaje debe estar entre 0 y 100');
                return;
            }
            
            // Actualizar el resumen automáticamente
            setTimeout(() => {
                updateBCGSummary();
            }, 100);
        }

        function updateTCMTable() {
            console.log('Actualizando tabla TCM...');
            
            // Regenerar productos en todos los períodos existentes
            const periods = document.querySelectorAll('.period-row');
            periods.forEach(period => {
                const periodId = period.getAttribute('data-period-id');
                const productsGrid = period.querySelector('.products-grid');
                
                if (productsGrid) {
                    let productsHTML = '';
                    products.forEach((product, index) => {
                        // Buscar valor existente si hay
                        const existingInput = period.querySelector(`input[data-product="${index}"]`);
                        const existingValue = existingInput ? existingInput.value : '';
                        
                        productsHTML += `
                            <div class="product-tcm-cell">
                                <label class="product-label">${product.name}</label>
                                <div class="percentage-input-wrapper">
                                    <input type="number" 
                                           name="periods[${index}][${periodId}][tcm_percentage]"
                                           value="${existingValue}"
                                           placeholder="0.0" 
                                           step="0.1" 
                                           min="0" 
                                           max="100"
                                           class="product-input percentage-input" 
                                           data-product="${index}"
                                           data-period="${periodId}"
                                           onchange="calculateTCM(${periodId}, ${index}, this.value)">
                                    <span class="percentage-symbol">%</span>
                                </div>
                            </div>
                        `;
                    });
                    
                    productsGrid.innerHTML = productsHTML;
                    productsGrid.style.gridTemplateColumns = `repeat(${Math.max(1, products.length)}, 1fr)`;
                }
            });
        }

        function updateBCGSummary() {
            const container = document.getElementById('bcg-summary');
            if (!container) return;
            
            if (products.length === 0) {
                container.innerHTML = '<p class="text-muted">Agregue productos para ver el resumen BCG</p>';
                return;
            }
            
            // Calcular TCM por producto basado en los períodos
            const tcmValues = products.map((product, productIndex) => {
                const periodsContainer = document.getElementById('periods-container');
                const periods = periodsContainer ? periodsContainer.querySelectorAll('.period-row') : [];
                
                let tcmSum = 0;
                let totalPeriods = 0;
                
                periods.forEach(period => {
                    const productInput = period.querySelector(`input[data-product="${productIndex}"]`);
                    if (productInput && productInput.value) {
                        const value = parseFloat(productInput.value) || 0;
                        if (value > 0) {
                            tcmSum += value;
                            totalPeriods++;
                        }
                    }
                });
                
                const averageTCM = totalPeriods > 0 ? tcmSum / totalPeriods : 0;
                
                // Actualizar el campo oculto con el valor TCM
                const hiddenInput = document.querySelector(`input[name="products[${productIndex}][tcm_rate]"]`);
                if (hiddenInput) {
                    hiddenInput.value = averageTCM.toFixed(2);
                }
                
                return averageTCM.toFixed(2);
            });
            
            let headerColumns = 'auto';
            for (let i = 0; i < products.length; i++) {
                headerColumns += ' 1fr';
            }
            
            let summaryHTML = `
                <div class="bcg-row header" style="grid-template-columns: ${headerColumns};">
                    <div><strong>MÉTRICA</strong></div>
                    ${products.map(product => `<div><strong>${product.name}</strong></div>`).join('')}
                </div>
                <div class="bcg-row" style="grid-template-columns: ${headerColumns};">
                    <div>TCM</div>
                    ${tcmValues.map(tcm => `<div>${tcm}%</div>`).join('')}
                </div>
                <div class="bcg-row" style="grid-template-columns: ${headerColumns};">
                    <div>PRM</div>
                    ${products.map((product, index) => {
                        // Calcular PRM basado en competidores
                        const prm = calculateProductPRM(index);
                        const hiddenInput = document.querySelector(`input[name="products[${index}][prm_rate]"]`);
                        if (hiddenInput) {
                            hiddenInput.value = prm.toFixed(2);
                        }
                        return `<div>${prm.toFixed(2)}</div>`;
                    }).join('')}
                </div>
                <div class="bcg-row" style="grid-template-columns: ${headerColumns};">
                    <div>% S/VTAS</div>
                    ${products.map(product => `<div>${(product.percentage || 0)}%</div>`).join('')}
                </div>
            `;
            
            container.innerHTML = summaryHTML;
        }

        // Calcular PRM (Participación Relativa del Mercado) para un producto
        function calculateProductPRM(productIndex) {
            // PRM = Ventas de nuestro producto / Ventas del mayor competidor
            
            // Obtener las ventas de nuestro producto del primer paso
            const productSales = parseFloat(products[productIndex].sales) || 0;
            
            // Obtener las ventas del mayor competidor
            const maxCompetitorInput = document.querySelector(`input[data-product="${productIndex}"].max-sales-input`);
            const maxCompetitorSales = parseFloat(maxCompetitorInput?.value) || 0;
            
            // Si no hay datos de competidores o ventas, retornar 0
            if (maxCompetitorSales === 0 || productSales === 0) {
                return 0;
            }
            
            // Calcular PRM
            const prm = productSales / maxCompetitorSales;
            return prm;
        }

        // Mini Paso 3: Evolución de la Demanda Global del Sector
        function updateDemandEvolution() {
            const container = document.getElementById('demand-evolution');
            if (!container) return;
            
            if (products.length === 0) {
                container.innerHTML = `<p class="text-muted">Primero agregue productos en la sección "Previsión de Ventas"</p>`;
                return;
            }

            // Obtener todos los años únicos de los períodos TCM
            const periodsContainer = document.getElementById('periods-container');
            const periods = periodsContainer ? periodsContainer.querySelectorAll('.period-row') : [];
            const years = new Set();
            
            periods.forEach(period => {
                const startYearInput = period.querySelector('.start-year');
                const endYearInput = period.querySelector('.end-year');
                
                if (startYearInput && startYearInput.value) {
                    years.add(parseInt(startYearInput.value));
                }
                if (endYearInput && endYearInput.value) {
                    years.add(parseInt(endYearInput.value));
                }
            });
            
            // Si no hay períodos definidos, usar años por defecto
            if (years.size === 0) {
                const currentYear = new Date().getFullYear();
                for (let i = currentYear - 2; i <= currentYear + 2; i++) {
                    years.add(i);
                }
            }
            
            const sortedYears = Array.from(years).sort((a, b) => a - b);
            
            // Crear tabla de evolución de demanda
            let tableHTML = `
                <div class="demand-evolution-table">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>PRODUCTOS / AÑOS</th>
                                    ${sortedYears.map(year => `<th>${year}</th>`).join('')}
                                    <th>TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
            `;
            
            // Agregar fila para cada producto
            products.forEach((product, productIndex) => {
                tableHTML += `
                    <tr>
                        <td><strong>${product.name}</strong></td>
                        ${sortedYears.map(year => `
                            <td>
                                <input type="number" 
                                       name="demand_evolution[${productIndex}][${year}]"
                                       class="form-control demand-input" 
                                       placeholder="0" 
                                       step="0.01" 
                                       min="0"
                                       data-product="${productIndex}"
                                       data-year="${year}"
                                       onchange="calculateDemandTotal(${productIndex})">
                            </td>
                        `).join('')}
                        <td>
                            <span class="demand-total" data-product="${productIndex}">0.00</span>
                        </td>
                    </tr>
                `;
            });
            
            // Fila de totales por año
            tableHTML += `
                    <tr class="table-info">
                        <td><strong>TOTAL POR AÑO</strong></td>
                        ${sortedYears.map(year => `
                            <td>
                                <span class="year-total" data-year="${year}">0.00</span>
                            </td>
                        `).join('')}
                        <td>
                            <span class="grand-total">0.00</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i>
                Los años se generan automáticamente basándose en los períodos TCM definidos. 
                Ingrese la demanda global del sector para cada producto por año (en miles de soles).
            </small>
        </div>
    </div>
            `;
            
            container.innerHTML = tableHTML;
        }

        function calculateDemandTotal(productIndex) {
            // Calcular total por producto
            const productInputs = document.querySelectorAll(`input[data-product="${productIndex}"]`);
            let productTotal = 0;
            
            productInputs.forEach(input => {
                const value = parseFloat(input.value) || 0;
                productTotal += value;
            });
            
            const productTotalSpan = document.querySelector(`span[data-product="${productIndex}"]`);
            if (productTotalSpan) {
                productTotalSpan.textContent = productTotal.toFixed(2);
            }
            
            // Recalcular totales por año
            calculateYearTotals();
        }

        function calculateYearTotals() {
            const yearTotalSpans = document.querySelectorAll('.year-total');
            let grandTotal = 0;
            
            yearTotalSpans.forEach(span => {
                const year = span.getAttribute('data-year');
                const yearInputs = document.querySelectorAll(`input[data-year="${year}"]`);
                let yearTotal = 0;
                
                yearInputs.forEach(input => {
                    const value = parseFloat(input.value) || 0;
                    yearTotal += value;
                });
                
                span.textContent = yearTotal.toFixed(2);
                grandTotal += yearTotal;
            });
            
            const grandTotalSpan = document.querySelector('.grand-total');
            if (grandTotalSpan) {
                grandTotalSpan.textContent = grandTotal.toFixed(2);
            }
        }

        // Mini Paso 4: Niveles de Venta de los Competidores
        function updateCompetitorsSales() {
            const container = document.getElementById('competitors-sales');
            if (!container) return;
            
            if (products.length === 0) {
                container.innerHTML = '<p class="text-muted">Primero agregue productos en la sección "Previsión de Ventas"</p>';
                return;
            }

            let tableHTML = `
                <div class="competitors-sales-container">
            `;

            products.forEach((product, productIndex) => {
                // Obtener competidores existentes o crear por defecto
                const existingCompetitors = product.competitors || [];
                
                tableHTML += `
                    <div class="product-competitors-section">
                        <h5 class="product-title">${product.name}</h5>
                        <div class="competitors-table">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>COMPETIDORES</th>
                                        <th>VENTAS (miles de soles)</th>
                                    </tr>
                                </thead>
                                <tbody data-product="${productIndex}">`;
                
                // Crear filas para competidores existentes o mínimo 2 por defecto
                const minCompetitors = Math.max(2, existingCompetitors.length);
                for (let i = 0; i < minCompetitors; i++) {
                    const competitor = existingCompetitors[i] || {};
                    const competitorName = competitor.competitor_name || '';
                    const competitorSales = competitor.competitor_sales || '';
                    
                    tableHTML += `
                        <tr class="competitor-row">
                            <td>
                                <input type="text" 
                                       name="competitors[${productIndex}][${i}][name]" 
                                       class="form-control competitor-name" 
                                       placeholder="Competidor ${i + 1}"
                                       value="${competitorName}"
                                       data-product="${productIndex}"
                                       data-competitor="${i}">
                            </td>
                            <td>
                                <input type="number" 
                                       name="competitors[${productIndex}][${i}][sales]" 
                                       class="form-control competitor-sales" 
                                       placeholder="0.00" 
                                       step="0.01" 
                                       min="0"
                                       value="${competitorSales}"
                                       data-product="${productIndex}"
                                       data-competitor="${i}"
                                       onchange="updateMaxCompetitorSales(${productIndex})">
                                ${i >= 2 ? `
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger ms-2 remove-competitor-btn" 
                                        onclick="removeCompetitor(this, ${productIndex})" 
                                        title="Eliminar competidor">
                                    <i class="fas fa-times"></i>
                                </button>` : ''}
                            </td>
                        </tr>`;
                }
                
                tableHTML += `
                                    <tr class="mayor-row table-info">
                                        <td><strong>MAYOR</strong></td>
                                        <td>
                                            <span class="max-competitor-sales" data-product="${productIndex}">0</span>
                                            <input type="hidden" 
                                                   name="competitors[${productIndex}][max_sales]" 
                                                   class="max-sales-input" 
                                                   data-product="${productIndex}"
                                                   value="0">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="add-competitor-btn-container mt-2">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary add-competitor-btn" 
                                        onclick="addCompetitor(${productIndex})">
                                    <i class="fas fa-plus"></i> Agregar Competidor
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            tableHTML += `
                </div>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Ingrese los nombres de los competidores y sus ventas para cada producto. 
                        El valor "MAYOR" se calculará automáticamente con el competidor de mayores ventas.
                        Esta información es opcional pero ayuda a calcular el PRM (Participación Relativa del Mercado).
                    </small>
                </div>`;

            container.innerHTML = tableHTML;
            
            // Calcular los máximos iniciales para cada producto después de cargar datos existentes
            setTimeout(() => {
                products.forEach((product, productIndex) => {
                    updateMaxCompetitorSales(productIndex);
                });
            }, 100);
        }

        // Función para agregar competidor
        function addCompetitor(productIndex) {
            const competitorsBody = document.querySelector(`tbody[data-product="${productIndex}"]`);
            const mayorRow = competitorsBody.querySelector('.mayor-row');
            
            // Contar competidores existentes
            const existingRows = competitorsBody.querySelectorAll('.competitor-row');
            const competitorNumber = existingRows.length;
            
            // Crear nueva fila de competidor
            const newRow = document.createElement('tr');
            newRow.className = 'competitor-row';
            newRow.innerHTML = `
                <td>
                    <input type="text" 
                           name="competitors[${productIndex}][${competitorNumber}][name]" 
                           class="form-control competitor-name" 
                           placeholder="Competidor ${competitorNumber + 1}"
                           data-product="${productIndex}"
                           data-competitor="${competitorNumber}">
                </td>
                <td>
                    <input type="number" 
                           name="competitors[${productIndex}][${competitorNumber}][sales]" 
                           class="form-control competitor-sales" 
                           placeholder="0.00" 
                           step="0.01" 
                           min="0"
                           data-product="${productIndex}"
                           data-competitor="${competitorNumber}"
                           onchange="updateMaxCompetitorSales(${productIndex})">
                    <button type="button" 
                            class="btn btn-sm btn-outline-danger ms-2 remove-competitor-btn" 
                            onclick="removeCompetitor(this, ${productIndex})" 
                            title="Eliminar competidor">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;
            
            // Insertar antes de la fila MAYOR
            competitorsBody.insertBefore(newRow, mayorRow);
        }

        // Función para eliminar competidor
        function removeCompetitor(button, productIndex) {
            const row = button.closest('.competitor-row');
            const competitorsBody = row.closest('tbody');
            const remainingRows = competitorsBody.querySelectorAll('.competitor-row');
            
            if (remainingRows.length <= 2) {
                alert('Debe mantener al menos 2 competidores por producto');
                return;
            }
            
            row.remove();
            updateMaxCompetitorSales(productIndex);
        }

        // Función para actualizar el mayor competidor por producto
        function updateMaxCompetitorSales(productIndex) {
            const competitorInputs = document.querySelectorAll(`input[data-product="${productIndex}"].competitor-sales`);
            let maxSales = 0;
            
            competitorInputs.forEach(input => {
                const sales = parseFloat(input.value) || 0;
                if (sales > maxSales) {
                    maxSales = sales;
                }
            });
            
            // Actualizar la visualización del mayor
            const maxDisplay = document.querySelector(`span[data-product="${productIndex}"].max-competitor-sales`);
            const maxInput = document.querySelector(`input[data-product="${productIndex}"].max-sales-input`);
            
            if (maxDisplay) {
                maxDisplay.textContent = maxSales.toLocaleString('es-PE');
            }
            if (maxInput) {
                maxInput.value = maxSales;
            }
            
            // Actualizar PRM en el resumen BCG
            updateBCGSummary();
        }

        // Validación del formulario
        document.getElementById('bcgForm').addEventListener('submit', function(e) {
            const productsContainer = document.getElementById('products-container');
            const productRows = productsContainer ? productsContainer.querySelectorAll('.product-row') : [];
            let valid = true;
            
            if (productRows.length === 0) {
                valid = false;
                alert('Debe agregar al menos un producto');
            } else {
                productRows.forEach((row, index) => {
                    const nameInput = row.querySelector('input[type="text"]');
                    const salesInput = row.querySelector('input[type="number"]');
                    
                    if (!nameInput.value.trim() || !salesInput.value || salesInput.value <= 0) {
                        valid = false;
                        alert(`Por favor complete correctamente los datos del producto ${index + 1}`);
                    }
                });
            }
            
            if (!valid) {
                e.preventDefault();
                return;
            }
            
            // Preparar datos antes del envío
            console.log('Enviando formulario BCG...');
            
            // Asegurar que los datos de productos estén actualizados
            updateSalesPercentages();
            
            // Los competidores son opcionales - no validar
            console.log('Formulario válido, enviando datos...');
        });
    </script>

    <!-- Footer -->
    <?php include __DIR__ . '/../Users/footer.php'; ?>
</body>
</html>