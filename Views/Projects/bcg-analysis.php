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
            <form id="bcgForm" method="POST" action="<?php echo getBaseUrl(); ?>/Controllers/ProjectController.php?action=save_enhanced_bcg" class="step-form">
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

                <!-- Mini Paso 1: PREVISIÓN DE VENTAS -->
                <div class="mini-step">
                    <div class="mini-step-header">
                        <div class="step-number">1</div>
                        <h3 class="step-title">TABLA 1: PREVISIÓN DE VENTAS</h3>
                        <button type="button" class="btn-add-mini" onclick="addProduct()">
                            <i class="icon-plus"></i> Agregar Producto
                        </button>
                        <button type="button" class="btn-example" onclick="loadExampleData()">
                            <i class="icon-lightbulb"></i> Cargar Ejemplo
                        </button>
                    </div>
                    
                    <div class="mini-step-content">
                        <div class="info-box">
                            <strong>Propósito:</strong> Establecer el tamaño de cada producto en tu cartera<br>
                            <strong>Cálculo:</strong> % = (Venta del producto / Total ventas) × 100<br>
                            <strong>Uso en gráfico:</strong> Este porcentaje determina el <strong>TAMAÑO DE LA BOLA</strong> en la matriz BCG
                        </div>
                        
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
                                <strong>TOTAL: <span id="total-sales">0</span></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mini Paso 2: EVOLUCIÓN HISTÓRICA DEL MERCADO -->
                <div class="mini-step">
                    <div class="mini-step-header">
                        <div class="step-number">2</div>
                        <h3 class="step-title">TABLA 2: EVOLUCIÓN DE LA DEMANDA GLOBAL DEL SECTOR</h3>
                        <button type="button" class="btn-add-mini" onclick="addHistoryYear()">
                            <i class="icon-plus"></i> Agregar Año
                        </button>
                    </div>
                    
                    <div class="mini-step-content">
                        <div class="info-box">
                            <strong>Propósito:</strong> Medir cómo crece cada mercado año tras año<br>
                            <strong>Dato importante:</strong> Son tasas de crecimiento del MERCADO TOTAL, no solo de tu empresa<br>
                            <strong>Resultado:</strong> Se calcula el TCM (promedio de los años) para posicionar en el <strong>EJE Y</strong>
                        </div>
                        
                        <div class="market-evolution-table">
                            <div id="market-history-container">
                                <div class="history-header">
                                    <div class="col-year">AÑOS</div>
                                    <div id="products-header-history">
                                        <!-- Se genera dinámicamente según productos -->
                                    </div>
                                </div>
                                <div id="history-rows">
                                    <!-- Filas de años se agregan dinámicamente -->
                                </div>
                            </div>
                        </div>

                        <!-- Tabla TCM Calculado -->
                        <div class="tcm-calculated-table">
                            <h4 class="table-title">TABLA 3: TCM (TASA DE CRECIMIENTO DEL MERCADO) - CALCULADO</h4>
                            <div class="info-box">
                                <strong>Cálculo:</strong> TCM = PROMEDIO(tasas de los años)<br>
                                <strong>Ejemplo:</strong> dominios: TCM = (10% + 50% + 3% + 5% + 1%) / 5 = 13.80%
                            </div>
                            <div id="tcm-summary" class="tcm-results">
                                <!-- Se calcula automáticamente -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mini Paso 3: COMPETIDORES POR PRODUCTO -->
                <div class="mini-step">
                    <div class="mini-step-header">
                        <div class="step-number">3</div>
                        <h3 class="step-title">TABLA 4: NIVELES DE VENTA DE COMPETIDORES</h3>
                        <button type="button" class="btn-add-mini" onclick="generateCompetitorTables()">
                            <i class="icon-refresh"></i> Generar Tablas
                        </button>
                    </div>
                    
                    <div class="mini-step-content">
                        <div class="info-box">
                            <strong>Propósito:</strong> Identificar al líder del mercado en cada categoría<br>
                            <strong>Dato clave:</strong> El competidor con <strong>MAYORES VENTAS</strong> se usará para calcular PRM<br>
                            <strong>Ejemplo:</strong> Si tu empresa vende 100 y el mayor competidor 150, entonces estás en desventaja
                        </div>
                        
                        <div id="competitors-container">
                            <!-- Se genera una tabla por cada producto -->
                        </div>
                    </div>
                </div>

                <!-- Mini Paso 4: PRM Y MATRIZ BCG -->
                <div class="mini-step">
                    <div class="mini-step-header">
                        <div class="step-number">4</div>
                        <h3 class="step-title">TABLA 5: PRM Y POSICIONAMIENTO EN MATRIZ BCG</h3>
                        <button type="button" class="btn-add-mini" onclick="calculateBCGMatrix()">
                            <i class="icon-chart"></i> Calcular Matriz
                        </button>
                    </div>
                    
                    <div class="mini-step-content">
                        <div class="info-box">
                            <strong>Cálculo CRÍTICO:</strong> PRM = Ventas de MI empresa / Ventas del MAYOR competidor<br>
                            <strong>Interpretación:</strong> PRM > 1.0 = Somos líderes | PRM < 1.0 = No somos líderes<br>
                            <strong>Uso:</strong> Posiciona la bola en el <strong>EJE X (horizontal)</strong> de la matriz
                        </div>
                        
                        <!-- Tabla PRM Calculado -->
                        <div class="prm-calculated-table">
                            <h4 class="table-title">PRM (PARTICIPACIÓN RELATIVA EN EL MERCADO) - CALCULADO</h4>
                            <div id="prm-summary" class="prm-results">
                                <!-- Se calcula automáticamente -->
                            </div>
                        </div>

                        <!-- Resumen Final BCG -->
                        <div class="bcg-final-summary">
                            <h4 class="table-title">🎯 POSICIONAMIENTO EN LA MATRIZ BCG</h4>
                            <div id="bcg-positioning-table" class="positioning-table">
                                <!-- Tabla resumen con todos los cálculos -->
                            </div>
                        </div>

                        <!-- Matriz BCG Visual Interactiva -->
                        <div class="bcg-matrix-visual">
                            <h4 class="table-title">📊 MATRIZ BCG INTERACTIVA</h4>
                            <div class="matrix-container">
                                <div id="bcg-chart" class="chart-container">
                                    <!-- Gráfico interactivo con Canvas/SVG -->
                                </div>
                                <div class="matrix-legend">
                                    <div class="legend-item estrella">
                                        <span class="legend-color"></span> ESTRELLA (Alto crec, Alta part)
                                    </div>
                                    <div class="legend-item incognita">
                                        <span class="legend-color"></span> INCÓGNITA (Alto crec, Baja part)
                                    </div>
                                    <div class="legend-item vaca">
                                        <span class="legend-color"></span> VACA (Bajo crec, Alta part)
                                    </div>
                                    <div class="legend-item perro">
                                        <span class="legend-color"></span> PERRO (Bajo crec, Baja part)
                                </div>
                            </div>
                        </div>

                        <!-- Contenedores adicionales para resultados -->
                        <div id="market-evolution-container" class="market-evolution-section">
                            <!-- Contenido dinámico de evolución del mercado -->
                        </div>

                        <div id="tcm-results" class="tcm-results-section">
                            <!-- Resultados TCM calculados -->
                        </div>

                        <div id="prm-results" class="prm-results-section">
                            <!-- Resultados PRM calculados -->
                        </div>

                        <div id="bcg-summary" class="bcg-summary-section">
                            <!-- Resumen final BCG -->
                        </div>
                    </div>
                </div>
            </div>                <!-- Botones de navegación -->
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

        // ===== NUEVAS FUNCIONES BCG MEJORADAS =====

        // Cargar datos de ejemplo reales
        function loadExampleData() {
            console.log('Cargando datos de ejemplo BCG...');
            
            // Datos de ejemplo específicos basados en tu solicitud
            const exampleDataReal = {
                products: [
                    { name: 'Dominios', sales: 2500000, tcm: 8.5, prm: 45.2 },
                    { name: 'Software', sales: 4200000, tcm: 12.3, prm: 32.7 },
                    { name: 'Desarrollo', sales: 3800000, tcm: 15.7, prm: 28.4 },
                    { name: 'TI', sales: 5100000, tcm: 6.2, prm: 52.8 },
                    { name: 'Máquinas', sales: 1900000, tcm: 3.8, prm: 18.6 }
                ],
                marketHistory: [
                    { period: '2020-2021', rates: [8.2, 12.1, 15.2, 6.8, 4.1] },
                    { period: '2021-2022', rates: [8.5, 12.3, 15.8, 6.2, 3.9] },
                    { period: '2022-2023', rates: [8.8, 12.5, 16.1, 5.6, 3.4] }
                ],
                competitors: [
                    { 
                        product: 'Dominios', 
                        competitors: [
                            { name: 'GoDaddy', sales: 5500000, isMax: true },
                            { name: 'Namecheap', sales: 3200000, isMax: false },
                            { name: 'Google Domains', sales: 4100000, isMax: false }
                        ]
                    },
                    { 
                        product: 'Software', 
                        competitors: [
                            { name: 'Microsoft', sales: 12800000, isMax: true },
                            { name: 'Adobe', sales: 8900000, isMax: false },
                            { name: 'Oracle', sales: 9500000, isMax: false }
                        ]
                    },
                    { 
                        product: 'Desarrollo', 
                        competitors: [
                            { name: 'Accenture', sales: 13400000, isMax: true },
                            { name: 'IBM', sales: 11200000, isMax: false },
                            { name: 'TCS', sales: 10800000, isMax: false }
                        ]
                    },
                    { 
                        product: 'TI', 
                        competitors: [
                            { name: 'Amazon AWS', sales: 9650000, isMax: true },
                            { name: 'Microsoft Azure', sales: 8200000, isMax: false },
                            { name: 'Google Cloud', sales: 7100000, isMax: false }
                        ]
                    },
                    { 
                        product: 'Máquinas', 
                        competitors: [
                            { name: 'Dell Technologies', sales: 10200000, isMax: true },
                            { name: 'HP Inc.', sales: 8900000, isMax: false },
                            { name: 'Lenovo', sales: 9100000, isMax: false }
                        ]
                    }
                ]
            };

            // Limpiar datos actuales
            products = [];
            marketEvolution = [];
            competitorData = {};

            // Cargar productos con cálculos
            const totalSales = exampleDataReal.products.reduce((sum, p) => sum + p.sales, 0);
            
            exampleDataReal.products.forEach((product, index) => {
                const percentage = ((product.sales / totalSales) * 100);
                products.push({
                    id: index,
                    name: product.name,
                    sales: product.sales,
                    percentage: percentage,
                    tcm: product.tcm,
                    prm: product.prm
                });
            });

            // Cargar evolución del mercado
            marketEvolution = exampleDataReal.marketHistory.map(period => ({
                period: period.period,
                rates: [...period.rates]
            }));

            // Cargar competidores
            exampleDataReal.competitors.forEach(comp => {
                competitorData[comp.product] = comp.competitors.map(c => ({
                    name: c.name,
                    sales: c.sales,
                    isMax: c.isMax
                }));
            });

            // Actualizar UI step por step
            updateStep1UI();
            updateStep2UI();
            updateStep3UI();
            updateStep4UI();
            
            // Mostrar mensaje de confirmación
            showAlert('Datos de ejemplo cargados exitosamente', 'success');
            
            console.log('Datos cargados:', {
                products: products,
                marketEvolution: marketEvolution,
                competitorData: competitorData
            });
        }

        // Función para actualizar la UI del Step 1 (Productos y Ventas)
        function updateStep1UI() {
            const tableBody = document.querySelector('#products-table tbody');
            if (!tableBody) return;
            
            tableBody.innerHTML = '';
            
            products.forEach((product, index) => {
                const row = document.createElement('tr');
                row.setAttribute('data-product-index', index);
                row.innerHTML = `
                    <td>
                        <input type="text" 
                               class="enhanced-input" 
                               name="products[${index}][name]" 
                               value="${product.name}"
                               onchange="updateProductName(${index}, this.value)"
                               placeholder="Nombre del producto">
                    </td>
                    <td>
                        <input type="number" 
                               class="enhanced-input" 
                               name="products[${index}][sales_forecast]" 
                               value="${product.sales}"
                               onchange="updateProductSales(${index}, this.value)"
                               placeholder="0" min="0" step="0.01">
                    </td>
                    <td class="percentage-display calculated-field">
                        ${product.percentage.toFixed(1)}%
                    </td>
                    <td>
                        <button type="button" 
                                class="enhanced-btn danger" 
                                onclick="removeProduct(${index})"
                                title="Eliminar producto">
                            🗑️
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Función para actualizar la UI del Step 2 (Evolución del Mercado)
        function updateStep2UI() {
            const container = document.getElementById('market-evolution-container');
            if (!container) return;
            
            container.innerHTML = '<h4>Evolución Histórica del Mercado (TCM por período)</h4>';
            
            marketEvolution.forEach((period, periodIndex) => {
                const periodDiv = document.createElement('div');
                periodDiv.className = 'market-period';
                periodDiv.innerHTML = `
                    <h5>Período: ${period.period}</h5>
                    <table class="enhanced-products-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>TCM (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${products.map((product, productIndex) => `
                                <tr>
                                    <td>${product.name}</td>
                                    <td>
                                        <input type="number" 
                                               class="enhanced-input" 
                                               value="${period.rates[productIndex] || 0}"
                                               onchange="updateMarketRate(${periodIndex}, ${productIndex}, this.value)"
                                               placeholder="0" step="0.1">
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
                container.appendChild(periodDiv);
            });
            
            // Calcular y mostrar TCM promedio
            updateTCMCalculations();
        }

        // Función para actualizar la UI del Step 3 (Competidores)
        function updateStep3UI() {
            const container = document.getElementById('competitors-container');
            if (!container) return;
            
            container.innerHTML = '<h4>Análisis de Competidores por Producto</h4>';
            
            products.forEach((product, productIndex) => {
                const productDiv = document.createElement('div');
                productDiv.className = 'competitor-section';
                productDiv.innerHTML = `
                    <h5>Competidores de: ${product.name}</h5>
                    <table class="enhanced-products-table">
                        <thead>
                            <tr>
                                <th>Competidor</th>
                                <th>Ventas</th>
                                <th>Mayor Competidor</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="competitors-${productIndex}">
                            <!-- Se llenará dinámicamente -->
                        </tbody>
                    </table>
                    <button type="button" 
                            class="enhanced-btn secondary" 
                            onclick="addCompetitor('${product.name}')">
                        ➕ Agregar Competidor
                    </button>
                `;
                container.appendChild(productDiv);
                
                // Llenar competidores existentes
                const competitors = competitorData[product.name] || [];
                competitors.forEach((comp, compIndex) => {
                    addCompetitorToTable(product.name, comp, compIndex);
                });
            });
        }

        // Función para actualizar la UI del Step 4 (Matriz BCG)
        function updateStep4UI() {
            calculatePRMValues();
            calculateFinalBCGPositions();
            drawBCGMatrix();
            updateBCGSummary();
        }

        // Renderizar tabla de productos
        function renderProducts() {
            const container = document.getElementById('products-container');
            container.innerHTML = '';

            products.forEach((product, index) => {
                const totalSales = products.reduce((sum, p) => sum + p.sales, 0);
                const percentage = totalSales > 0 ? ((product.sales / totalSales) * 100).toFixed(2) : 0;

                const productRow = document.createElement('div');
                productRow.className = 'product-row';
                productRow.innerHTML = `
                    <div class="col-product">
                        <input type="text" name="products[${index}][name]" value="${product.name}" 
                               onchange="updateProduct(${index}, 'name', this.value)" placeholder="Nombre del producto">
                    </div>
                    <div class="col-sales">
                        <input type="number" name="products[${index}][sales]" value="${product.sales}" 
                               onchange="updateProduct(${index}, 'sales', parseFloat(this.value) || 0)" placeholder="0" min="0" step="0.01">
                    </div>
                    <div class="col-percentage">
                        <span class="percentage-display">${percentage}%</span>
                    </div>
                    <div class="col-actions">
                        <button type="button" onclick="removeProduct(${index})" class="btn-remove">
                            <i class="icon-trash"></i>
                        </button>
                    </div>
                `;
                container.appendChild(productRow);
            });

            // Actualizar total
            const totalSales = products.reduce((sum, p) => sum + p.sales, 0);
            document.getElementById('total-sales').textContent = totalSales;

            // Actualizar headers de historia
            updateProductsHeaderHistory();
        }

        // Actualizar producto
        function updateProduct(index, field, value) {
            if (products[index]) {
                products[index][field] = value;
                renderProducts();
                updateAllCalculations();
            }
        }

        // Agregar producto
        function addProduct() {
            const newId = products.length;
            products.push({
                id: newId,
                name: '',
                sales: 0
            });
            renderProducts();
        }

        // Remover producto
        function removeProduct(index) {
            if (products.length > 1) {
                products.splice(index, 1);
                renderProducts();
                updateAllCalculations();
            } else {
                alert('Debe mantener al menos un producto');
            }
        }

        // Actualizar headers de productos en historia
        function updateProductsHeaderHistory() {
            const headerContainer = document.getElementById('products-header-history');
            headerContainer.innerHTML = '';

            products.forEach(product => {
                const col = document.createElement('div');
                col.className = 'col-product-history';
                col.textContent = product.name || 'Producto';
                headerContainer.appendChild(col);
            });
        }

        // Renderizar historia del mercado
        function renderMarketHistory() {
            const container = document.getElementById('history-rows');
            container.innerHTML = '';

            marketHistory.forEach((history, yearIndex) => {
                const row = document.createElement('div');
                row.className = 'history-row';
                
                let rowHTML = `<div class="col-year">
                    <input type="text" name="history[${yearIndex}][year]" value="${history.year}" 
                           onchange="updateHistoryYear(${yearIndex}, this.value)" placeholder="2023-2024">
                </div>`;

                products.forEach((product, productIndex) => {
                    const rate = history.rates[productIndex] || 0;
                    rowHTML += `<div class="col-product-history">
                        <input type="number" name="history[${yearIndex}][rates][${productIndex}]" 
                               value="${rate}" onchange="updateHistoryRate(${yearIndex}, ${productIndex}, this.value)"
                               placeholder="0%" min="0" max="100" step="0.1">
                        <span class="percentage-symbol">%</span>
                    </div>`;
                });

                rowHTML += `<div class="col-actions-history">
                    <button type="button" onclick="removeHistoryYear(${yearIndex})" class="btn-remove">
                        <i class="icon-trash"></i>
                    </button>
                </div>`;

                row.innerHTML = rowHTML;
                container.appendChild(row);
            });
        }

        // Actualizar año en historia
        function updateHistoryYear(yearIndex, value) {
            if (marketHistory[yearIndex]) {
                marketHistory[yearIndex].year = value;
                updateAllCalculations();
            }
        }

        // Actualizar tasa en historia
        function updateHistoryRate(yearIndex, productIndex, value) {
            if (!marketHistory[yearIndex]) return;
            if (!marketHistory[yearIndex].rates) marketHistory[yearIndex].rates = [];
            
            marketHistory[yearIndex].rates[productIndex] = parseFloat(value) || 0;
            updateAllCalculations();
        }

        // Agregar año a historia
        function addHistoryYear() {
            const newYear = {
                year: '',
                rates: new Array(products.length).fill(0)
            };
            marketHistory.push(newYear);
            renderMarketHistory();
        }

        // Remover año de historia
        function removeHistoryYear(yearIndex) {
            if (marketHistory.length > 1) {
                marketHistory.splice(yearIndex, 1);
                renderMarketHistory();
                updateAllCalculations();
            }
        }

        // Generar tablas de competidores
        function generateCompetitorTables() {
            const container = document.getElementById('competitors-container');
            container.innerHTML = '';

            products.forEach((product, productIndex) => {
                const productCompetitors = competitors[product.name] || [];
                
                const table = document.createElement('div');
                table.className = 'competitor-table';
                table.innerHTML = `
                    <h5 class="product-title">${product.name.toUpperCase()} (Mi empresa: ${product.sales} ventas)</h5>
                    <div class="competitor-rows" id="competitors-${productIndex}">
                        ${productCompetitors.map((comp, compIndex) => `
                            <div class="competitor-row">
                                <input type="text" value="${comp.name}" placeholder="Nombre competidor">
                                <input type="number" value="${comp.sales}" placeholder="Ventas" min="0">
                                <button type="button" onclick="removeCompetitor(${productIndex}, ${compIndex})" class="btn-remove-small">❌</button>
                            </div>
                        `).join('')}
                        <div class="competitor-row">
                            <input type="text" placeholder="Nombre competidor">
                            <input type="number" placeholder="Ventas" min="0">
                            <button type="button" onclick="addCompetitor(${productIndex})" class="btn-add-small">➕</button>
                        </div>
                    </div>
                    <div class="max-competitor">
                        <strong>Mayor competidor: <span id="max-comp-${productIndex}">-</span></strong>
                    </div>
                `;
                container.appendChild(table);
            });

            updateAllCalculations();
        }

        // ===== FUNCIONES AUXILIARES PARA DATOS DE EJEMPLO =====
        
        // Función para mostrar alertas
        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `enhanced-alert ${type}`;
            alertDiv.innerHTML = `
                <strong>${type === 'success' ? '✅' : type === 'warning' ? '⚠️' : 'ℹ️'}</strong>
                ${message}
            `;
            
            // Insertar al inicio del contenido
            const content = document.querySelector('.step-content.active') || document.querySelector('.section-content');
            if (content) {
                content.insertBefore(alertDiv, content.firstChild);
                
                // Remover después de 5 segundos
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            }
        }

        // Actualizar nombre de producto
        function updateProductName(index, name) {
            if (products[index]) {
                products[index].name = name;
            }
        }

        // Remover producto
        function removeProduct(index) {
            if (products.length <= 1) {
                showAlert('Debe mantener al menos un producto', 'warning');
                return;
            }
            
            products.splice(index, 1);
            updateStep1UI();
            recalculatePercentages();
        }

        // Recalcular porcentajes después de cambios
        function recalculatePercentages() {
            const totalSales = products.reduce((sum, p) => sum + p.sales, 0);
            
            products.forEach(product => {
                product.percentage = totalSales > 0 ? ((product.sales / totalSales) * 100) : 0;
            });
            
            updateStep1UI();
        }

        // Actualizar tasa de mercado
        function updateMarketRate(periodIndex, productIndex, rate) {
            if (marketEvolution[periodIndex] && marketEvolution[periodIndex].rates[productIndex] !== undefined) {
                marketEvolution[periodIndex].rates[productIndex] = parseFloat(rate) || 0;
                updateTCMCalculations();
            }
        }

        // Calcular TCM promedio
        function updateTCMCalculations() {
            products.forEach((product, index) => {
                const rates = marketEvolution.map(period => period.rates[index] || 0);
                const avgTCM = rates.length > 0 ? (rates.reduce((sum, rate) => sum + rate, 0) / rates.length) : 0;
                product.tcm = avgTCM;
            });
            
            // Mostrar TCM calculado
            const tcmContainer = document.getElementById('tcm-results');
            if (tcmContainer) {
                tcmContainer.innerHTML = `
                    <h4>TCM Promedio Calculado</h4>
                    <table class="enhanced-products-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>TCM Promedio (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${products.map(p => `
                                <tr>
                                    <td>${p.name}</td>
                                    <td class="calculated-field">${p.tcm.toFixed(2)}%</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
            }
        }

        // Agregar competidor a tabla específica
        function addCompetitorToTable(productName, competitor, index) {
            const productIndex = products.findIndex(p => p.name === productName);
            const tbody = document.getElementById(`competitors-${productIndex}`);
            
            if (!tbody) return;
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <input type="text" 
                           class="enhanced-input" 
                           value="${competitor.name}"
                           onchange="updateCompetitorName('${productName}', ${index}, this.value)"
                           placeholder="Nombre del competidor">
                </td>
                <td>
                    <input type="number" 
                           class="enhanced-input" 
                           value="${competitor.sales}"
                           onchange="updateCompetitorSales('${productName}', ${index}, this.value)"
                           placeholder="0" min="0" step="0.01">
                </td>
                <td>
                    <input type="radio" 
                           name="max_competitor_${productName}" 
                           ${competitor.isMax ? 'checked' : ''}
                           onchange="setMaxCompetitor('${productName}', ${index})">
                </td>
                <td>
                    <button type="button" 
                            class="enhanced-btn danger" 
                            onclick="removeCompetitor('${productName}', ${index})"
                            title="Eliminar competidor">
                        🗑️
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        }

        // Agregar nuevo competidor
        function addCompetitor(productName) {
            if (!competitorData[productName]) {
                competitorData[productName] = [];
            }
            
            competitorData[productName].push({
                name: '',
                sales: 0,
                isMax: false
            });
            
            updateStep3UI();
        }

        // Actualizar nombre de competidor
        function updateCompetitorName(productName, index, name) {
            if (competitorData[productName] && competitorData[productName][index]) {
                competitorData[productName][index].name = name;
            }
        }

        // Actualizar ventas de competidor
        function updateCompetitorSales(productName, index, sales) {
            if (competitorData[productName] && competitorData[productName][index]) {
                competitorData[productName][index].sales = parseFloat(sales) || 0;
                calculatePRMValues();
            }
        }

        // Establecer competidor máximo
        function setMaxCompetitor(productName, index) {
            if (competitorData[productName]) {
                competitorData[productName].forEach((comp, i) => {
                    comp.isMax = (i === index);
                });
                calculatePRMValues();
            }
        }

        // Remover competidor
        function removeCompetitor(productName, index) {
            if (competitorData[productName] && competitorData[productName].length > 1) {
                competitorData[productName].splice(index, 1);
                updateStep3UI();
                calculatePRMValues();
            } else {
                showAlert('Debe mantener al menos un competidor', 'warning');
            }
        }

        // Calcular valores PRM
        function calculatePRMValues() {
            products.forEach(product => {
                const competitors = competitorData[product.name] || [];
                const maxCompetitor = competitors.find(c => c.isMax);
                
                if (maxCompetitor && maxCompetitor.sales > 0) {
                    product.prm = (product.sales / maxCompetitor.sales) * 100;
                } else {
                    product.prm = 0;
                }
            });
            
            // Mostrar PRM calculado
            updatePRMDisplay();
        }

        // Actualizar visualización PRM
        function updatePRMDisplay() {
            const prmContainer = document.getElementById('prm-results');
            if (prmContainer) {
                prmContainer.innerHTML = `
                    <h4>PRM Calculado (Participación Relativa en el Mercado)</h4>
                    <table class="enhanced-products-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Ventas Empresa</th>
                                <th>Mayor Competidor</th>
                                <th>PRM (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${products.map(p => {
                                const competitors = competitorData[p.name] || [];
                                const maxComp = competitors.find(c => c.isMax);
                                return `
                                    <tr>
                                        <td>${p.name}</td>
                                        <td>$${p.sales.toLocaleString()}</td>
                                        <td>${maxComp ? maxComp.name + ': $' + maxComp.sales.toLocaleString() : 'No definido'}</td>
                                        <td class="calculated-field">${p.prm.toFixed(2)}%</td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                `;
            }
        }

        // Calcular posiciones finales BCG
        function calculateFinalBCGPositions() {
            products.forEach(product => {
                // Definir cuadrantes basados en TCM y PRM
                // TCM > 10% = Alto crecimiento, TCM <= 10% = Bajo crecimiento
                // PRM > 100% = Alta participación, PRM <= 100% = Baja participación
                
                if (product.tcm > 10 && product.prm > 100) {
                    product.bcgPosition = 'Estrella';
                } else if (product.tcm <= 10 && product.prm > 100) {
                    product.bcgPosition = 'Vaca Lechera';
                } else if (product.tcm > 10 && product.prm <= 100) {
                    product.bcgPosition = 'Interrogante';
                } else {
                    product.bcgPosition = 'Perro';
                }
            });
        }

        // Actualizar resumen BCG
        function updateBCGSummary() {
            const summary = {
                'Estrella': 0,
                'Vaca Lechera': 0,
                'Interrogante': 0,
                'Perro': 0
            };
            
            products.forEach(p => {
                summary[p.bcgPosition]++;
            });
            
            const summaryContainer = document.getElementById('bcg-summary');
            if (summaryContainer) {
                summaryContainer.innerHTML = `
                    <div class="bcg-summary">
                        ${Object.entries(summary).map(([position, count]) => `
                            <div class="summary-card">
                                <h4>${position}</h4>
                                <div class="value">${count}</div>
                                <div class="label">productos</div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
        }

        // Calcular todos los valores BCG
        function updateAllCalculations() {
            updateTCMCalculations();
            calculatePRMValues();
            calculateFinalBCGPositions();
            updateBCGSummary();
        }
            calculatePRM();
            generateBCGSummary();
            drawBCGMatrix();
        }

        // Calcular TCM (Tasa de Crecimiento del Mercado)
        function calculateTCM() {
            const tcmContainer = document.getElementById('tcm-summary');
            tcmContainer.innerHTML = '';

            const tcmRow = document.createElement('div');
            tcmRow.className = 'tcm-row';
            tcmRow.innerHTML = '<div class="tcm-header-calc"><strong>TCM Calculado:</strong></div>';

            products.forEach((product, productIndex) => {
                // Calcular promedio de todas las tasas históricas para este producto
                let totalRate = 0;
                let validRates = 0;

                marketHistory.forEach(history => {
                    if (history.rates && history.rates[productIndex] !== undefined) {
                        totalRate += history.rates[productIndex];
                        validRates++;
                    }
                });

                const tcm = validRates > 0 ? (totalRate / validRates).toFixed(2) : 0;
                
                const tcmCell = document.createElement('div');
                tcmCell.className = 'tcm-cell';
                tcmCell.innerHTML = `<strong>${product.name}:</strong> ${tcm}%`;
                tcmRow.appendChild(tcmCell);

                // Guardar resultado
                if (!bcgResults[product.name]) bcgResults[product.name] = {};
                bcgResults[product.name].tcm = parseFloat(tcm);
            });

            tcmContainer.appendChild(tcmRow);
        }

        // Calcular PRM (Participación Relativa en el Mercado)
        function calculatePRM() {
            const prmContainer = document.getElementById('prm-summary');
            prmContainer.innerHTML = '';

            const prmRow = document.createElement('div');
            prmRow.className = 'prm-row';
            prmRow.innerHTML = '<div class="prm-header-calc"><strong>PRM Calculado:</strong></div>';

            products.forEach((product, productIndex) => {
                const productCompetitors = competitors[product.name] || [];
                let maxCompetitorSales = 0;

                productCompetitors.forEach(comp => {
                    if (comp.sales > maxCompetitorSales) {
                        maxCompetitorSales = comp.sales;
                    }
                });

                const prm = maxCompetitorSales > 0 ? (product.sales / maxCompetitorSales).toFixed(2) : 0;
                
                const prmCell = document.createElement('div');
                prmCell.className = 'prm-cell';
                prmCell.innerHTML = `<strong>${product.name}:</strong> ${prm} (${product.sales}/${maxCompetitorSales})`;
                prmRow.appendChild(prmCell);

                // Actualizar display del mayor competidor
                const maxCompDisplay = document.getElementById(`max-comp-${productIndex}`);
                if (maxCompDisplay) {
                    maxCompDisplay.textContent = maxCompetitorSales;
                }

                // Guardar resultado
                if (!bcgResults[product.name]) bcgResults[product.name] = {};
                bcgResults[product.name].prm = parseFloat(prm);
                bcgResults[product.name].sales = product.sales;
            });

            prmContainer.appendChild(prmRow);
        }

        // Generar tabla resumen BCG
        function generateBCGSummary() {
            const container = document.getElementById('bcg-positioning-table');
            container.innerHTML = '';

            const totalSales = products.reduce((sum, p) => sum + p.sales, 0);

            const table = document.createElement('table');
            table.className = 'bcg-summary-table';
            table.innerHTML = `
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>TCM (%)</th>
                        <th>PRM</th>
                        <th>% Ventas</th>
                        <th>Cuadrante</th>
                    </tr>
                </thead>
                <tbody>
                    ${products.map(product => {
                        const result = bcgResults[product.name] || {};
                        const percentage = totalSales > 0 ? ((product.sales / totalSales) * 100).toFixed(2) : 0;
                        const cuadrante = getCuadrante(result.tcm, result.prm);
                        
                        return `
                            <tr class="${cuadrante.class}">
                                <td><strong>${product.name}</strong></td>
                                <td>${result.tcm || 0}%</td>
                                <td>${result.prm || 0}</td>
                                <td>${percentage}%</td>
                                <td><strong>${cuadrante.name}</strong></td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            `;
            container.appendChild(table);
        }

        // Determinar cuadrante BCG
        function getCuadrante(tcm, prm) {
            tcm = tcm || 0;
            prm = prm || 0;

            if (tcm > 10 && prm > 1) return {name: 'ESTRELLA', class: 'estrella'};
            if (tcm > 10 && prm <= 1) return {name: 'INCÓGNITA', class: 'incognita'};
            if (tcm <= 10 && prm > 1) return {name: 'VACA', class: 'vaca'};
            return {name: 'PERRO', class: 'perro'};
        }

        // Dibujar matriz BCG interactiva
        function drawBCGMatrix() {
            const container = document.getElementById('bcg-chart');
            container.innerHTML = `
                <svg width="500" height="400" class="bcg-svg">
                    <!-- Cuadrantes de fondo -->
                    <rect x="0" y="0" width="250" height="200" fill="#FFE4E1" opacity="0.3"/>
                    <rect x="250" y="0" width="250" height="200" fill="#E6FFE6" opacity="0.3"/>
                    <rect x="0" y="200" width="250" height="200" fill="#FFE4B5" opacity="0.3"/>
                    <rect x="250" y="200" width="250" height="200" fill="#E0E6FF" opacity="0.3"/>
                    
                    <!-- Líneas divisorias -->
                    <line x1="250" y1="0" x2="250" y2="400" stroke="#333" stroke-width="2"/>
                    <line x1="0" y1="200" x2="500" y2="400" stroke="#333" stroke-width="2"/>
                    
                    <!-- Etiquetas de ejes -->
                    <text x="125" y="15" text-anchor="middle" class="axis-label">INCÓGNITA</text>
                    <text x="375" y="15" text-anchor="middle" class="axis-label">ESTRELLA</text>
                    <text x="125" y="395" text-anchor="middle" class="axis-label">PERRO</text>
                    <text x="375" y="395" text-anchor="middle" class="axis-label">VACA</text>
                    
                    <!-- Eje Y (TCM) -->
                    <text x="10" y="20" class="axis-text">20%</text>
                    <text x="10" y="110" class="axis-text">10%</text>
                    <text x="10" y="200" class="axis-text">0%</text>
                    
                    <!-- Eje X (PRM) -->
                    <text x="0" y="420" class="axis-text">0</text>
                    <text x="125" y="420" class="axis-text">0.5</text>
                    <text x="250" y="420" class="axis-text">1.0</text>
                    <text x="375" y="420" class="axis-text">1.5</text>
                    <text x="500" y="420" class="axis-text">2.0+</text>
                    
                    ${products.map(product => {
                        const result = bcgResults[product.name] || {};
                        const tcm = result.tcm || 0;
                        const prm = result.prm || 0;
                        
                        // Convertir TCM (0-20%) a coordenada Y (400-0)
                        const y = 400 - (tcm / 20) * 400;
                        
                        // Convertir PRM (0-2) a coordenada X (0-500)
                        const x = Math.min(prm / 2 * 500, 480);
                        
                        // Tamaño basado en % de ventas
                        const totalSales = products.reduce((sum, p) => sum + p.sales, 0);
                        const percentage = totalSales > 0 ? (product.sales / totalSales) * 100 : 0;
                        const radius = Math.max(8, Math.min(25, percentage));
                        
                        const cuadrante = getCuadrante(tcm, prm);
                        const color = cuadrante.class === 'estrella' ? '#4CAF50' :
                                     cuadrante.class === 'incognita' ? '#FF9800' :
                                     cuadrante.class === 'vaca' ? '#2196F3' : '#F44336';
                        
                        return `
                            <circle cx="${x}" cy="${y}" r="${radius}" fill="${color}" opacity="0.8" stroke="#333" stroke-width="1">
                                <title>${product.name}: TCM=${tcm}%, PRM=${prm}, Ventas=${percentage.toFixed(1)}%</title>
                            </circle>
                            <text x="${x}" y="${y+4}" text-anchor="middle" class="product-label" fill="white" font-size="10" font-weight="bold">
                                ${product.name}
                            </text>
                        `;
                    }).join('')}
                </svg>
            `;
        }

        // Función para calcular matriz (botón)
        function calculateBCGMatrix() {
            updateAllCalculations();
            
            // Scroll hasta la matriz
            document.getElementById('bcg-chart').scrollIntoView({behavior: 'smooth'});
        }

        // ===== FUNCIONES DE NAVEGACIÓN DE STEPS =====
        
        // Cambiar mini-step activo  
        function setActiveStep(stepNumber) {
            console.log(`Activando step ${stepNumber}`);
            
            // Remover active de todos los mini-steps
            document.querySelectorAll('.bcg-mini-steps .mini-step').forEach(step => {
                step.classList.remove('active');
                step.classList.remove('completed');
            });
            
            // Remover active de contenidos
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Marcar steps anteriores como completados
            for (let i = 1; i < stepNumber; i++) {
                const prevStep = document.querySelector(`.bcg-mini-steps .mini-step:nth-child(${i})`);
                if (prevStep) prevStep.classList.add('completed');
            }
            
            // Activar step actual
            const activeNavStep = document.querySelector(`.bcg-mini-steps .mini-step:nth-child(${stepNumber})`);
            if (activeNavStep) activeNavStep.classList.add('active');
            
            const activeContent = document.getElementById(`step${stepNumber}-content`);
            if (activeContent) activeContent.classList.add('active');
            
            currentStep = stepNumber;
        }

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            // Eventos click en mini-steps
            document.querySelectorAll('.bcg-mini-steps .mini-step').forEach((step, index) => {
                step.addEventListener('click', () => setActiveStep(index + 1));
            });
            
            setActiveStep(1); // Activar step 1 por defecto
            
            // Cargar datos de ejemplo automáticamente
            loadExampleData();
        });

    </script>

    <!-- Footer -->
    <?php include __DIR__ . '/../Users/footer.php'; ?>
</body>
</html>