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

            <!-- BCG Analysis Interactivo Completo -->
            <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
        
        <!-- Controles superiores -->
        <div class="nav-buttons" style="text-align: center; margin-bottom: 30px;">
            <button class="excel-btn success" onclick="loadExampleData()" style="margin: 0 10px; padding: 12px 24px; font-size: 16px;">
                📊 CARGAR DATOS DE EJEMPLO
            </button>
            <button class="excel-btn primary" onclick="calculateAllMetrics()" style="margin: 0 10px; padding: 12px 24px; font-size: 16px;">
                🧮 CALCULAR TODO
            </button>
            <button class="excel-btn info" onclick="generateBCGMatrix()" style="margin: 0 10px; padding: 12px 24px; font-size: 16px;">
                🎯 GENERAR MATRIZ BCG
            </button>
        </div>

        <!-- TABLA 1: PREVISIÓN DE VENTAS -->
        <div class="section">
            <h2>📊 TABLA 1: PREVISIÓN DE VENTAS POR PRODUCTO</h2>
            <div class="nav-buttons">
                <button class="excel-btn success" onclick="addProduct()">➕ Agregar Producto</button>
            </div>
            <div class="dynamic-table-container">
                <table class="excel-table dynamic-table" id="sales-forecast-table">
                    <thead>
                        <tr>
                            <th style="width: 30%;">PRODUCTOS</th>
                            <th style="width: 25%;">VENTAS (miles S/)</th>
                            <th style="width: 20%;">% S/ TOTAL</th>
                            <th style="width: 15%;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody id="sales-forecast-body">
                        <!-- Productos se agregan dinámicamente -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><strong>TOTAL VENTAS</strong></td>
                            <td><strong id="total-sales">0</strong></td>
                            <td>100%</td>
                            <td>-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- TABLA 2: TCM (TASAS DE CRECIMIENTO DEL MERCADO) -->
        <div class="section">
            <h2>📈 TABLA 2: TCM - TASAS DE CRECIMIENTO DEL MERCADO</h2>
            <div class="nav-buttons">
                <button class="excel-btn success" onclick="addMarketPeriod()">➕ Agregar Período</button>
            </div>
            <div class="dynamic-table-container">
                <table class="excel-table dynamic-table" id="tcm-table">
                    <thead id="tcm-header">
                        <!-- Encabezado dinámico generado por JavaScript -->
                    </thead>
                    <tbody id="tcm-body">
                        <!-- Filas de períodos generadas dinámicamente -->
                    </tbody>
                    <tfoot id="tcm-footer">
                        <!-- TCM promedio calculado -->
                    </tfoot>
                </table>
            </div>
        </div>>

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

        <!-- TABLA 3: NIVELES DE VENTA DE COMPETIDORES (PRM) -->
        <div class="section">
            <h2>🏆 TABLA 3: NIVELES DE VENTA DE COMPETIDORES (PRM)</h2>
            <div id="competitors-container">
                <!-- Sub-tablas por producto generadas dinámicamente -->
            </div>
            <div class="summary-box">
                <h3>📊 RESULTADOS PRM CALCULADOS</h3>
                <div id="prm-summary">
                    <!-- Resumen PRM por producto -->
                </div>
            </div>
        </div>

        <!-- TABLA 4: EVOLUCIÓN DE LA DEMANDA GLOBAL DEL SECTOR -->
        <div class="section">
            <h2>🌍 TABLA 4: EVOLUCIÓN DE LA DEMANDA GLOBAL DEL SECTOR</h2>
            <div class="nav-buttons">
                <button class="excel-btn success" onclick="addDemandPeriod()">➕ Agregar Año</button>
            </div>
            <div class="dynamic-table-container">
                <table class="excel-table dynamic-table" id="demand-table">
                    <thead id="demand-header">
                        <!-- Encabezado dinámico generado por JavaScript -->
                    </thead>
                    <tbody id="demand-body">
                        <!-- Filas de años generadas dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MATRIZ BCG VISUAL -->
        <div class="section">
            <h2>🎯 MATRIZ BCG - VISUALIZACIÓN</h2>
            
            <div class="nav-buttons">
                <button class="excel-btn success" onclick="generateBCGMatrix()" style="font-size: 18px; padding: 12px 24px;">
                    🧮 GENERAR MATRIZ BCG
                </button>
            </div>
            
            <!-- Tabla resumen de posicionamiento -->
            <div id="bcg-positioning-summary">
                <!-- Resumen de posicionamiento generado dinámicamente -->
            </div>
            
            <!-- Gráfico matriz BCG -->
            <div class="bcg-matrix-container">
                <div id="bcg-visual-matrix">
                    <!-- Matriz visual generada dinámicamente -->
                </div>
            </div>
        </div>
    </div>
    </main>

    <!-- JavaScript -->
    <script src="<?php echo getBaseUrl(); ?>/Publics/js/dashboard.js"></script>
    <script>
        // ===== VARIABLES GLOBALES =====
        let products = [];                    // TABLA 1: Lista de productos con ventas
        let marketGrowthData = [];           // TABLA 2: TCM por período y producto  
        let competitorsByProduct = {};       // TABLA 3: Competidores por producto
        let sectorDemandData = [];          // TABLA 4: Demanda global del sector
        
        // Colores distintivos para productos (máximo 6)
        const productColors = [
            '#fef3c7', '#d1fae5', '#dbeafe', '#f3e8ff', '#fed7d7', '#e6fffa'
        ];
        
        // Métricas calculadas
        let calculatedMetrics = {
            tcm: {},      // TCM promedio por producto
            prm: {},      // PRM por producto  
            positioning: {} // Posición BCG por producto
        };

        // ===== FUNCIONES PRINCIPALES (DEFINIDAS PRIMERO) =====
        
        function addProduct() {
            console.log('🔥 Agregando nuevo producto...');
            
            const productIndex = products.length;
            const productName = `Producto ${productIndex + 1}`;
            
            // Agregar al array de productos
            products.push({
                id: productIndex,
                name: productName,
                sales: 0,
                percentage: 0,
                tcm: 0,
                prm: 0
            });

            // Actualizar la tabla visual
            renderProductsTable();
            
            console.log(`✅ Producto agregado: ${productName} (index: ${productIndex})`);
        }

        function addHistoryYear() {
            console.log('📅 Agregando nuevo año histórico...');
            
            if (products.length === 0) {
                showAlert('Primero debe agregar productos', 'warning');
                return;
            }
            
            const currentYear = new Date().getFullYear();
            const yearIndex = marketEvolution.length;
            const startYear = currentYear - yearIndex - 1;
            const endYear = startYear + 1;
            
            // Agregar período al array
            const newPeriod = {
                period: `${startYear}-${endYear}`,
                rates: new Array(products.length).fill(0)
            };
            
            marketEvolution.push(newPeriod);
            
            // Actualizar tabla visual
            renderMarketHistoryTable();
            
            console.log(`✅ Año agregado: ${newPeriod.period}`);
        }

        function addMarketPeriod() {
            console.log('📅 Agregando nuevo período de mercado...');
            
            if (products.length === 0) {
                showAlert('Primero debe agregar productos', 'warning');
                return;
            }
            
            const currentYear = new Date().getFullYear();
            const yearIndex = marketEvolution.length;
            const startYear = currentYear - yearIndex - 1;
            const endYear = startYear + 1;
            
            const newPeriod = {
                period: `${startYear}-${endYear}`,
                rates: new Array(products.length).fill(0)
            };
            
            marketEvolution.push(newPeriod);
            renderMarketHistoryTable();
            
            showAlert(`Período ${newPeriod.period} agregado exitosamente`, 'success');
            console.log(`✅ Período TCM agregado: ${newPeriod.period}`);
        }

        function addDemandPeriod() {
            console.log('📊 Agregando nuevo año de demanda...');
            
            if (products.length === 0) {
                showAlert('Primero debe agregar productos', 'warning');
                return;
            }
            
            const currentYear = new Date().getFullYear();
            const newYear = {
                year: currentYear.toString(),
                values: new Array(products.length).fill(0)
            };
            
            // Agregar a sectorDemandData si existe, si no crear array
            if (typeof sectorDemandData === 'undefined') {
                window.sectorDemandData = [];
            }
            sectorDemandData.push(newYear);
            
            // Renderizar tabla si existe la función
            if (typeof renderSectorDemandTable === 'function') {
                renderSectorDemandTable();
            }
            
            showAlert(`Año de demanda ${currentYear} agregado exitosamente`, 'success');
            console.log(`✅ Año de demanda agregado: ${currentYear}`);
        }

        function generateBCGMatrix() {
            console.log('🎯 Generando matriz BCG completa...');
            
            if (products.length === 0) {
                showAlert('Primero debe agregar productos', 'warning');
                return;
            }
            
            // Calcular todas las métricas necesarias
            if (typeof calculateAllMetrics === 'function') {
                calculateAllMetrics();
            }
            
            // Generar resumen de posicionamiento
            if (typeof updateBCGSummary === 'function') {
                updateBCGSummary();
            }
            
            // Generar matriz visual
            if (typeof generateBCGVisual === 'function') {
                generateBCGVisual();
            }
            
            // Mostrar resultados finales
            displayBCGResults();
            
            showAlert('Matriz BCG generada exitosamente', 'success');
            console.log('✅ Matriz BCG generada completamente');
        }

        function displayBCGResults() {
            console.log('📊 Mostrando resultados BCG...');
            
            // Mostrar resumen en consola
            products.forEach(product => {
                console.log(`📦 ${product.name}:`);
                console.log(`   - Ventas: ${product.sales}`);
                console.log(`   - TCM: ${product.tcm || 'No calculado'}%`);
                console.log(`   - PRM: ${product.prm || 'No calculado'}`);
                console.log(`   - Porcentaje: ${product.percentage || 0}%`);
            });
        }

        function generateCompetitorTables() {
            console.log('🏢 Generando tablas de competidores...');
            
            if (products.length === 0) {
                showAlert('Primero debe agregar productos', 'warning');
                return;
            }
            
            const container = document.getElementById('competitors-container');
            if (!container) {
                console.error('❌ Container competitors-container no encontrado');
                return;
            }
            
            container.innerHTML = '<h4>Análisis de Competidores por Producto</h4>';
            
            products.forEach((product, productIndex) => {
                // Inicializar competidores si no existen
                if (!competitorData[product.name]) {
                    competitorData[product.name] = [
                        { name: 'Competidor 1', sales: 0, isMax: false },
                        { name: 'Competidor 2', sales: 0, isMax: false }
                    ];
                }
                
                const productSection = document.createElement('div');
                productSection.className = 'competitor-section';
                productSection.innerHTML = `
                    <h5>Competidores de: ${product.name}</h5>
                    <div class="competitor-table">
                        <div class="table-header">
                            <div class="col-competitor">COMPETIDOR</div>
                            <div class="col-sales">VENTAS</div>
                            <div class="col-max">MAYOR</div>
                            <div class="col-actions">ACCIONES</div>
                        </div>
                        <div id="competitors-${productIndex}" class="competitors-rows">
                            ${renderCompetitorRows(product.name, productIndex)}
                        </div>
                        <button type="button" 
                                class="enhanced-btn secondary" 
                                onclick="addCompetitor('${product.name}', ${productIndex})">
                            ➕ Agregar Competidor
                        </button>
                    </div>
                `;
                
                container.appendChild(productSection);
            });
            
            console.log('✅ Tablas de competidores generadas');
        }

        function calculateBCGMatrix() {
            console.log('🎯 Calculando matriz BCG...');
            
            if (products.length === 0) {
                showAlert('Primero debe agregar productos', 'warning');
                return;
            }
            
            // Calcular TCM promedio para cada producto
            updateTCMCalculations();
            
            // Calcular PRM para cada producto
            products.forEach(product => {
                const competitors = competitorData[product.name] || [];
                const maxCompetitor = competitors.find(c => c.isMax && c.sales > 0);
                
                if (maxCompetitor) {
                    product.prm = (product.sales / maxCompetitor.sales) * 100;
                } else {
                    product.prm = 0;
                }
                
                // Determinar posición BCG
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
            
            // Mostrar resultados
            displayPRMResults();
            displayBCGPositioning();
            drawBCGMatrix();
            
            console.log('✅ Matriz BCG calculada y mostrada');
        }

        // ===== FUNCIONES AUXILIARES =====

        function renderProductsTable() {
            const container = document.getElementById('products-container');
            if (!container) {
                console.error('❌ Container products-container no encontrado');
                return;
            }
            
            container.innerHTML = '';
            
            products.forEach((product, index) => {
                const productRow = document.createElement('div');
                productRow.className = 'product-row';
                productRow.setAttribute('data-product-index', index);
                
                productRow.innerHTML = `
                    <div class="col-product">
                        <input type="text" 
                               name="products[${index}][name]"
                               class="enhanced-input" 
                               placeholder="Nombre del producto" 
                               value="${product.name}" 
                               onchange="updateProductName(${index}, this.value)">
                    </div>
                    <div class="col-sales">
                        <input type="number" 
                               name="products[${index}][sales_forecast]"
                               class="enhanced-input" 
                               placeholder="0" 
                               value="${product.sales}" 
                               min="0" 
                               step="0.01"
                               onchange="updateProductSales(${index}, this.value)">
                    </div>
                    <div class="col-percentage">
                        <span class="percentage-display calculated-field">${product.percentage.toFixed(1)}%</span>
                    </div>
                    <div class="col-actions">
                        <button type="button" 
                                class="enhanced-btn danger" 
                                onclick="removeProduct(${index})"
                                title="Eliminar producto">
                            🗑️
                        </button>
                    </div>
                `;
                
                container.appendChild(productRow);
            });
            
            updateTotalSales();
        }

        function renderMarketHistoryTable() {
            const container = document.getElementById('market-history-container');
            if (!container) {
                console.error('❌ Container market-history-container no encontrado');
                return;
            }
            
            // Crear cabecera
            let headerHTML = `
                <div class="history-header">
                    <div class="col-year">AÑOS</div>
                    <div class="products-header-grid">
                        ${products.map(p => `<div class="col-product-header">${p.name}</div>`).join('')}
                    </div>
                    <div class="col-actions">ACCIONES</div>
                </div>
            `;
            
            // Crear filas de datos
            let rowsHTML = marketEvolution.map((period, periodIndex) => `
                <div class="history-row" data-period-index="${periodIndex}">
                    <div class="col-year">
                        <input type="text" 
                               class="enhanced-input" 
                               value="${period.period}" 
                               placeholder="2023-2024"
                               onchange="updatePeriodName(${periodIndex}, this.value)">
                    </div>
                    <div class="products-data-grid">
                        ${products.map((product, productIndex) => `
                            <div class="col-product-data">
                                <input type="number" 
                                       class="enhanced-input" 
                                       value="${period.rates[productIndex] || 0}" 
                                       placeholder="0" 
                                       step="0.1"
                                       onchange="updateMarketRate(${periodIndex}, ${productIndex}, this.value)">
                            </div>
                        `).join('')}
                    </div>
                    <div class="col-actions">
                        <button type="button" 
                                class="enhanced-btn danger" 
                                onclick="removePeriod(${periodIndex})"
                                title="Eliminar período">
                            🗑️
                        </button>
                    </div>
                </div>
            `).join('');
            
            container.innerHTML = headerHTML + rowsHTML;
            
            // Actualizar cálculos TCM
            updateTCMCalculations();
        }

        function renderCompetitorRows(productName, productIndex) {
            const competitors = competitorData[productName] || [];
            
            return competitors.map((competitor, compIndex) => `
                <div class="competitor-row" data-comp-index="${compIndex}">
                    <div class="col-competitor">
                        <input type="text" 
                               class="enhanced-input" 
                               value="${competitor.name}" 
                               placeholder="Nombre del competidor"
                               onchange="updateCompetitorName('${productName}', ${compIndex}, this.value)">
                    </div>
                    <div class="col-sales">
                        <input type="number" 
                               class="enhanced-input" 
                               value="${competitor.sales}" 
                               placeholder="0" 
                               min="0" 
                               step="0.01"
                               onchange="updateCompetitorSales('${productName}', ${compIndex}, this.value)">
                    </div>
                    <div class="col-max">
                        <input type="radio" 
                               name="max_competitor_${productName}" 
                               ${competitor.isMax ? 'checked' : ''}
                               onchange="setMaxCompetitor('${productName}', ${compIndex})">
                    </div>
                    <div class="col-actions">
                        <button type="button" 
                                class="enhanced-btn danger" 
                                onclick="removeCompetitor('${productName}', ${compIndex})"
                                title="Eliminar competidor">
                            🗑️
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function updateProductName(index, name) {
            console.log(`Actualizando nombre producto ${index}: ${name}`);
            if (products[index]) {
                products[index].name = name;
                markAsChanged();
            }
        }

        function updateProductSales(index, sales) {
            console.log(`Actualizando ventas producto ${index}: ${sales}`);
            if (products[index]) {
                products[index].sales = parseFloat(sales) || 0;
                updateSalesPercentages();
                markAsChanged();
            }
        }

        function updateSalesPercentages() {
            const totalSales = products.reduce((sum, p) => sum + p.sales, 0);
            
            products.forEach((product, index) => {
                product.percentage = totalSales > 0 ? ((product.sales / totalSales) * 100) : 0;
                
                // Actualizar visualización
                const row = document.querySelector(`[data-product-index="${index}"]`);
                if (row) {
                    const percentageDisplay = row.querySelector('.percentage-display');
                    if (percentageDisplay) {
                        percentageDisplay.textContent = product.percentage.toFixed(1) + '%';
                    }
                }
            });
            
            updateTotalSales();
        }

        function updateTotalSales() {
            const totalSales = products.reduce((sum, p) => sum + p.sales, 0);
            const totalElement = document.getElementById('total-sales');
            if (totalElement) {
                totalElement.textContent = totalSales.toLocaleString();
            }
        }

        function removeProduct(index) {
            if (products.length <= 1) {
                showAlert('Debe mantener al menos un producto', 'warning');
                return;
            }
            
            console.log(`Eliminando producto ${index}`);
            products.splice(index, 1);
            
            // Reindexar productos
            products.forEach((product, newIndex) => {
                product.id = newIndex;
            });
            
            renderProductsTable();
            updateSalesPercentages();
            markAsChanged();
        }

        function updatePeriodName(periodIndex, newPeriod) {
            if (marketEvolution[periodIndex]) {
                marketEvolution[periodIndex].period = newPeriod;
                markAsChanged();
            }
        }

        function updateMarketRate(periodIndex, productIndex, rate) {
            console.log(`Actualizando rate: período ${periodIndex}, producto ${productIndex}, valor ${rate}`);
            
            if (marketEvolution[periodIndex] && marketEvolution[periodIndex].rates[productIndex] !== undefined) {
                marketEvolution[periodIndex].rates[productIndex] = parseFloat(rate) || 0;
                updateTCMCalculations();
                markAsChanged();
            }
        }

        function removePeriod(periodIndex) {
            if (marketEvolution.length <= 1) {
                showAlert('Debe mantener al menos un período', 'warning');
                return;
            }
            
            marketEvolution.splice(periodIndex, 1);
            renderMarketHistoryTable();
            markAsChanged();
        }

        function updateTCMCalculations() {
            // Calcular TCM promedio para cada producto
            products.forEach((product, productIndex) => {
                const rates = marketEvolution.map(period => period.rates[productIndex] || 0);
                const avgTCM = rates.length > 0 ? (rates.reduce((sum, rate) => sum + rate, 0) / rates.length) : 0;
                product.tcm = avgTCM;
            });
            
            // Mostrar tabla de TCM calculado
            const tcmContainer = document.getElementById('tcm-results');
            if (tcmContainer) {
                tcmContainer.innerHTML = `
                    <h4>TCM PROMEDIO CALCULADO</h4>
                    <div class="tcm-summary-table">
                        <div class="table-header">
                            <div class="col-product">PRODUCTO</div>
                            <div class="col-tcm">TCM PROMEDIO (%)</div>
                        </div>
                        ${products.map(p => `
                            <div class="table-row">
                                <div class="col-product">${p.name}</div>
                                <div class="col-tcm calculated-field">${p.tcm.toFixed(2)}%</div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
        }

        function addCompetitor(productName, productIndex) {
            if (!competitorData[productName]) {
                competitorData[productName] = [];
            }
            
            const compIndex = competitorData[productName].length;
            competitorData[productName].push({
                name: `Competidor ${compIndex + 1}`,
                sales: 0,
                isMax: false
            });
            
            // Actualizar solo esa sección
            const competitorsContainer = document.getElementById(`competitors-${productIndex}`);
            if (competitorsContainer) {
                competitorsContainer.innerHTML = renderCompetitorRows(productName, productIndex);
            }
            
            markAsChanged();
        }

        function updateCompetitorName(productName, compIndex, name) {
            if (competitorData[productName] && competitorData[productName][compIndex]) {
                competitorData[productName][compIndex].name = name;
                markAsChanged();
            }
        }

        function updateCompetitorSales(productName, compIndex, sales) {
            if (competitorData[productName] && competitorData[productName][compIndex]) {
                competitorData[productName][compIndex].sales = parseFloat(sales) || 0;
                markAsChanged();
            }
        }

        function setMaxCompetitor(productName, compIndex) {
            if (competitorData[productName]) {
                // Desmarcar todos los competidores
                competitorData[productName].forEach((comp, i) => {
                    comp.isMax = (i === compIndex);
                });
                markAsChanged();
            }
        }

        function removeCompetitor(productName, compIndex) {
            if (competitorData[productName] && competitorData[productName].length > 1) {
                competitorData[productName].splice(compIndex, 1);
                
                // Actualizar la tabla visual
                const productIndex = products.findIndex(p => p.name === productName);
                if (productIndex >= 0) {
                    const competitorsContainer = document.getElementById(`competitors-${productIndex}`);
                    if (competitorsContainer) {
                        competitorsContainer.innerHTML = renderCompetitorRows(productName, productIndex);
                    }
                }
                markAsChanged();
            } else {
                showAlert('Debe mantener al menos un competidor', 'warning');
            }
        }

        function displayPRMResults() {
            const prmContainer = document.getElementById('prm-results');
            if (prmContainer) {
                prmContainer.innerHTML = `
                    <h4>PRM CALCULADO (PARTICIPACIÓN RELATIVA EN EL MERCADO)</h4>
                    <div class="prm-summary-table">
                        <div class="table-header">
                            <div class="col-product">PRODUCTO</div>
                            <div class="col-our-sales">NUESTRAS VENTAS</div>
                            <div class="col-competitor">MAYOR COMPETIDOR</div>
                            <div class="col-prm">PRM (%)</div>
                        </div>
                        ${products.map(product => {
                            const competitors = competitorData[product.name] || [];
                            const maxComp = competitors.find(c => c.isMax);
                            return `
                                <div class="table-row">
                                    <div class="col-product">${product.name}</div>
                                    <div class="col-our-sales">$${product.sales.toLocaleString()}</div>
                                    <div class="col-competitor">${maxComp ? maxComp.name + ': $' + maxComp.sales.toLocaleString() : 'No definido'}</div>
                                    <div class="col-prm calculated-field">${product.prm.toFixed(2)}%</div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                `;
            }
        }

        function displayBCGPositioning() {
            const positioningContainer = document.getElementById('bcg-positioning-table');
            if (positioningContainer) {
                positioningContainer.innerHTML = `
                    <div class="positioning-summary-table">
                        <div class="table-header">
                            <div class="col-product">PRODUCTO</div>
                            <div class="col-tcm">TCM (%)</div>
                            <div class="col-prm">PRM (%)</div>
                            <div class="col-position">POSICIÓN BCG</div>
                        </div>
                        ${products.map(product => `
                            <div class="table-row">
                                <div class="col-product">${product.name}</div>
                                <div class="col-tcm">${product.tcm.toFixed(2)}%</div>
                                <div class="col-prm">${product.prm.toFixed(2)}%</div>
                                <div class="col-position ${product.bcgPosition.toLowerCase().replace(' ', '-')}">${product.bcgPosition}</div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
        }

        function drawBCGMatrix() {
            const chartContainer = document.getElementById('bcg-chart');
            if (!chartContainer) {
                console.error('❌ Container bcg-chart no encontrado');
                return;
            }
            
            const width = 500;
            const height = 400;
            const margin = 50;
            
            // Crear SVG con productos posicionados
            const svg = `
                <svg width="${width}" height="${height}" class="bcg-matrix-svg">
                    <!-- Fondo y cuadrantes -->
                    <defs>
                        <pattern id="grid" width="50" height="50" patternUnits="userSpaceOnUse">
                            <path d="M 50 0 L 0 0 0 50" fill="none" stroke="#e0e0e0" stroke-width="1"/>
                        </pattern>
                    </defs>
                    
                    <!-- Fondo con cuadrículas -->
                    <rect width="100%" height="100%" fill="url(#grid)"/>
                    
                    <!-- Cuadrantes de colores -->
                    <rect x="${margin}" y="${margin}" width="${(width-2*margin)/2}" height="${(height-2*margin)/2}" 
                          fill="#4CAF50" opacity="0.1" stroke="#4CAF50" stroke-width="2"/>
                    <rect x="${margin + (width-2*margin)/2}" y="${margin}" width="${(width-2*margin)/2}" height="${(height-2*margin)/2}" 
                          fill="#FF9800" opacity="0.1" stroke="#FF9800" stroke-width="2"/>
                    <rect x="${margin}" y="${margin + (height-2*margin)/2}" width="${(width-2*margin)/2}" height="${(height-2*margin)/2}" 
                          fill="#2196F3" opacity="0.1" stroke="#2196F3" stroke-width="2"/>
                    <rect x="${margin + (width-2*margin)/2}" y="${margin + (height-2*margin)/2}" width="${(width-2*margin)/2}" height="${(height-2*margin)/2}" 
                          fill="#9E9E9E" opacity="0.1" stroke="#9E9E9E" stroke-width="2"/>
                    
                    <!-- Etiquetas de cuadrantes -->
                    <text x="${margin + (width-2*margin)/4}" y="${margin + 20}" text-anchor="middle" class="quadrant-label" fill="#4CAF50" font-weight="bold">ESTRELLA</text>
                    <text x="${margin + 3*(width-2*margin)/4}" y="${margin + 20}" text-anchor="middle" class="quadrant-label" fill="#FF9800" font-weight="bold">INTERROGANTE</text>
                    <text x="${margin + (width-2*margin)/4}" y="${height - margin - 10}" text-anchor="middle" class="quadrant-label" fill="#2196F3" font-weight="bold">VACA LECHERA</text>
                    <text x="${margin + 3*(width-2*margin)/4}" y="${height - margin - 10}" text-anchor="middle" class="quadrant-label" fill="#9E9E9E" font-weight="bold">PERRO</text>
                    
                    <!-- Ejes -->
                    <line x1="${margin}" y1="${margin}" x2="${margin}" y2="${height-margin}" stroke="#333" stroke-width="2"/>
                    <line x1="${margin}" y1="${height-margin}" x2="${width-margin}" y2="${height-margin}" stroke="#333" stroke-width="2"/>
                    
                    <!-- Líneas divisorias -->
                    <line x1="${margin + (width-2*margin)/2}" y1="${margin}" x2="${margin + (width-2*margin)/2}" y2="${height-margin}" stroke="#666" stroke-width="1" stroke-dasharray="5,5"/>
                    <line x1="${margin}" y1="${margin + (height-2*margin)/2}" x2="${width-margin}" y2="${margin + (height-2*margin)/2}" stroke="#666" stroke-width="1" stroke-dasharray="5,5"/>
                    
                    <!-- Etiquetas de ejes -->
                    <text x="${width/2}" y="${height - 10}" text-anchor="middle" class="axis-label">PRM (Participación Relativa del Mercado)</text>
                    <text x="20" y="${height/2}" text-anchor="middle" class="axis-label" transform="rotate(-90, 20, ${height/2})">TCM (Tasa de Crecimiento del Mercado)</text>
                    
                    <!-- Productos como círculos -->
                    ${products.map(product => {
                        // Normalizar posiciones (PRM en X, TCM en Y)
                        const maxPRM = Math.max(200, ...products.map(p => p.prm)); 
                        const maxTCM = Math.max(20, ...products.map(p => p.tcm)); 
                        
                        const x = margin + ((product.prm / maxPRM) * (width - 2*margin));
                        const y = height - margin - ((product.tcm / maxTCM) * (height - 2*margin));
                        
                        // Tamaño proporcional al % de ventas
                        const radius = Math.max(8, Math.min(25, product.percentage * 1.5));
                        
                        // Color según posición BCG
                        const color = product.bcgPosition === 'Estrella' ? '#4CAF50' :
                                     product.bcgPosition === 'Interrogante' ? '#FF9800' :
                                     product.bcgPosition === 'Vaca Lechera' ? '#2196F3' : '#9E9E9E';
                        
                        return `
                            <circle cx="${x}" cy="${y}" r="${radius}" fill="${color}" opacity="0.8" stroke="#333" stroke-width="2">
                                <title>${product.name}\nTCM: ${product.tcm.toFixed(1)}%\nPRM: ${product.prm.toFixed(1)}%\nVentas: ${product.percentage.toFixed(1)}%\nPosición: ${product.bcgPosition}</title>
                            </circle>
                            <text x="${x}" y="${y+4}" text-anchor="middle" class="product-label" fill="white" font-size="10" font-weight="bold">
                                ${product.name.substring(0, 8)}
                            </text>
                        `;
                    }).join('')}
                </svg>
            `;
            
            chartContainer.innerHTML = svg;
        }

        function showAlert(message, type = 'info') {
            // Crear elemento de alerta
            const alertDiv = document.createElement('div');
            alertDiv.className = `enhanced-alert ${type}`;
            alertDiv.innerHTML = `
                <strong>${type === 'success' ? '✅' : type === 'warning' ? '⚠️' : type === 'error' ? '❌' : 'ℹ️'}</strong>
                ${message}
                <button type="button" onclick="this.parentElement.remove()" style="float: right; background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
            `;
            
            // Agregar al contenedor principal
            const container = document.querySelector('.section-content') || document.body;
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto-remover después de 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Variables globales para cambios
        let hasChanges = false;
        
        function markAsChanged() {
            hasChanges = true;
        }
        
        // Datos existentes del servidor
        const existingBCGData = <?php echo $bcg_data_json; ?>;
        const existingBCGMatrix = <?php echo $bcg_matrix_json; ?>;
        
        console.log('Datos BCG existentes:', existingBCGData);

        // Inicialización antigua comentada - se usa la nueva más abajo
        /*
        document.addEventListener('DOMContentLoaded', function() {
            initializeBCG();
        });
        */

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

        // ===== INICIALIZACIÓN =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Inicializando BCG Analysis...');
            
            // Inicializar con datos de ejemplo automáticamente
            loadExampleData();
            
            console.log('✅ BCG Analysis inicializado correctamente');
        });

    </script>

    <!-- Footer -->
    <?php include __DIR__ . '/../Users/footer.php'; ?>
</body>
</html>
            console.log('Agregando nuevo producto...');
            
            const productIndex = products.length;
            const productName = `Producto ${productIndex + 1}`;
            
            // Agregar al array de productos
            products.push({
                id: productIndex,
                name: productName,
                sales: 0,
                percentage: 0,
                tcm: 0,
                prm: 0
            });

            // Actualizar la tabla visual
            renderProductsTable();
            
            console.log(`Producto agregado: ${productName} (index: ${productIndex})`);
        }

        function renderProductsTable() {
            const container = document.getElementById('products-container');
            if (!container) {
                console.error('Container products-container no encontrado');
                return;
            }
            
            container.innerHTML = '';
            
            products.forEach((product, index) => {
                const productRow = document.createElement('div');
                productRow.className = 'product-row';
                productRow.setAttribute('data-product-index', index);
                
                productRow.innerHTML = `
                    <div class="col-product">
                        <input type="text" 
                               name="products[${index}][name]"
                               class="enhanced-input" 
                               placeholder="Nombre del producto" 
                               value="${product.name}" 
                               onchange="updateProductName(${index}, this.value)">
                    </div>
                    <div class="col-sales">
                        <input type="number" 
                               name="products[${index}][sales_forecast]"
                               class="enhanced-input" 
                               placeholder="0" 
                               value="${product.sales}" 
                               min="0" 
                               step="0.01"
                               onchange="updateProductSales(${index}, this.value)">
                    </div>
                    <div class="col-percentage">
                        <span class="percentage-display calculated-field">${product.percentage.toFixed(1)}%</span>
                    </div>
                    <div class="col-actions">
                        <button type="button" 
                                class="enhanced-btn danger" 
                                onclick="removeProduct(${index})"
                                title="Eliminar producto">
                            🗑️
                        </button>
                    </div>
                ;
                
                container.appendChild(productRow);
            });
            
            updateTotalSales();
        }

        function updateProductName(index, name) {
            console.log(`Actualizando nombre producto ${index}: ${name}`);
            if (products[index]) {
                products[index].name = name;
                markAsChanged();
            }
        }

        function updateProductSales(index, sales) {
            console.log(`Actualizando ventas producto ${index}: ${sales}`);
            if (products[index]) {
                products[index].sales = parseFloat(sales) || 0;
                updateSalesPercentages();
                markAsChanged();
            }
        }

        function updateSalesPercentages() {
            const totalSales = products.reduce((sum, p) => sum + p.sales, 0);
            
            products.forEach((product, index) => {
                product.percentage = totalSales > 0 ? ((product.sales / totalSales) * 100) : 0;
                
                // Actualizar visualización
                const row = document.querySelector(`[data-product-index="${index}"]`);
                if (row) {
                    const percentageDisplay = row.querySelector('.percentage-display');
                    if (percentageDisplay) {
                        percentageDisplay.textContent = product.percentage.toFixed(1) + '%';
                    }
                }
            });
            
            updateTotalSales();
        }

        function updateTotalSales() {
            const totalSales = products.reduce((sum, p) => sum + p.sales, 0);
            const totalElement = document.getElementById('total-sales');
            if (totalElement) {
                totalElement.textContent = totalSales.toLocaleString();
            }
        }

        function removeProduct(index) {
            if (products.length <= 1) {
                showAlert('Debe mantener al menos un producto', 'warning');
                return;
            }
            
            console.log(`Eliminando producto ${index}`);
            products.splice(index, 1);
            
            // Reindexar productos
            products.forEach((product, newIndex) => {
                product.id = newIndex;
            });
            
            renderProductsTable();
            updateSalesPercentages();
            markAsChanged();
        }

        // Función para agregar años históricos
        function addHistoryYear() {
            console.log('Agregando nuevo año histórico...');
            
            if (products.length === 0) {
                showAlert('Primero debe agregar productos', 'warning');
                return;
            }
            
            const currentYear = new Date().getFullYear();
            const yearIndex = marketEvolution.length;
            const startYear = currentYear - yearIndex - 1;
            const endYear = startYear + 1;
            
            // Agregar período al array
            const newPeriod = {
                period: `${startYear}-${endYear}`,
                rates: new Array(products.length).fill(0)
            };
            
            marketEvolution.push(newPeriod);
            
            // Actualizar tabla visual
            renderMarketHistoryTable();
            
            console.log(`Año agregado: ${newPeriod.period}`);
        }

        function renderMarketHistoryTable() {
            const container = document.getElementById('market-history-container');
            if (!container) {
                console.error('Container market-history-container no encontrado');
                return;
            }
            
            // Crear cabecera
            let headerHTML = `
                <div class="history-header">
                    <div class="col-year">AÑOS</div>
                    <div class="products-header-grid">
                        ${products.map(p => `<div class="col-product-header">${p.name}</div>`).join('')}
                    </div>
                    <div class="col-actions">ACCIONES</div>
                </div>
            `;
            
            // Crear filas de datos
            let rowsHTML = marketEvolution.map((period, periodIndex) => `
                <div class="history-row" data-period-index="${periodIndex}">
                    <div class="col-year">
                        <input type="text" 
                               class="enhanced-input" 
                               value="${period.period}" 
                               placeholder="2023-2024"
                               onchange="updatePeriodName(${periodIndex}, this.value)">
                    </div>
                    <div class="products-data-grid">
                        ${products.map((product, productIndex) => `
                            <div class="col-product-data">
                                <input type="number" 
                                       class="enhanced-input" 
                                       value="${period.rates[productIndex] || 0}" 
                                       placeholder="0" 
                                       step="0.1"
                                       onchange="updateMarketRate(${periodIndex}, ${productIndex}, this.value)">
                            </div>
                        `).join('')}
                    </div>
                    <div class="col-actions">
                        <button type="button" 
                                class="enhanced-btn danger" 
                                onclick="removePeriod(${periodIndex})"
                                title="Eliminar período">
                            🗑️
                        </button>
                    </div>
                </div>
            `).join('');
            
            container.innerHTML = headerHTML + rowsHTML;
            
            // Actualizar cálculos TCM
            updateTCMCalculations();
        }

        function updatePeriodName(periodIndex, newPeriod) {
            if (marketEvolution[periodIndex]) {
                marketEvolution[periodIndex].period = newPeriod;
                markAsChanged();
            }
        }

        function updateMarketRate(periodIndex, productIndex, rate) {
            console.log(`Actualizando rate: período ${periodIndex}, producto ${productIndex}, valor ${rate}`);
            
            if (marketEvolution[periodIndex] && marketEvolution[periodIndex].rates[productIndex] !== undefined) {
                marketEvolution[periodIndex].rates[productIndex] = parseFloat(rate) || 0;
                updateTCMCalculations();
                markAsChanged();
            }
        }

        function removePeriod(periodIndex) {
            if (marketEvolution.length <= 1) {
                showAlert('Debe mantener al menos un período', 'warning');
                return;
            }
            
            marketEvolution.splice(periodIndex, 1);
            renderMarketHistoryTable();
            markAsChanged();
        }

        function updateTCMCalculations() {
            // Calcular TCM promedio para cada producto
            products.forEach((product, productIndex) => {
                const rates = marketEvolution.map(period => period.rates[productIndex] || 0);
                const avgTCM = rates.length > 0 ? (rates.reduce((sum, rate) => sum + rate, 0) / rates.length) : 0;
                product.tcm = avgTCM;
            });
            
            // Mostrar tabla de TCM calculado
            const tcmContainer = document.getElementById('tcm-results');
            if (tcmContainer) {
                tcmContainer.innerHTML = `
                    <h4>TCM PROMEDIO CALCULADO</h4>
                    <div class="tcm-summary-table">
                        <div class="table-header">
                            <div class="col-product">PRODUCTO</div>
                            <div class="col-tcm">TCM PROMEDIO (%)</div>
                        </div>
                        ${products.map(p => `
                            <div class="table-row">
                                <div class="col-product">${p.name}</div>
                                <div class="col-tcm calculated-field">${p.tcm.toFixed(2)}%</div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
        }

        // Función para generar tablas de competidores
        function generateCompetitorTables() {
            console.log('Generando tablas de competidores...');
            
            if (products.length === 0) {
                showAlert('Primero debe agregar productos', 'warning');
                return;
            }
            
            const container = document.getElementById('competitors-container');
            if (!container) {
                console.error('Container competitors-container no encontrado');
                return;
            }
            
            container.innerHTML = '<h4>Análisis de Competidores por Producto</h4>';
            
            products.forEach((product, productIndex) => {
                // Inicializar competidores si no existen
                if (!competitorData[product.name]) {
                    competitorData[product.name] = [
                        { name: 'Competidor 1', sales: 0, isMax: false },
                        { name: 'Competidor 2', sales: 0, isMax: false }
                    ];
                }
                
                const productSection = document.createElement('div');
                productSection.className = 'competitor-section';
                productSection.innerHTML = `
                    <h5>Competidores de: ${product.name}</h5>
                    <div class="competitor-table">
                        <div class="table-header">
                            <div class="col-competitor">COMPETIDOR</div>
                            <div class="col-sales">VENTAS</div>
                            <div class="col-max">MAYOR</div>
                            <div class="col-actions">ACCIONES</div>
                        </div>
                        <div id="competitors-${productIndex}" class="competitors-rows">
                            ${renderCompetitorRows(product.name, productIndex)}
                        </div>
                        <button type="button" 
                                class="enhanced-btn secondary" 
                                onclick="addCompetitor('${product.name}', ${productIndex})">
                            ➕ Agregar Competidor
                        </button>
                    </div>
                `;
                
                container.appendChild(productSection);
            });
            
            console.log('Tablas de competidores generadas');
        }

        function renderCompetitorRows(productName, productIndex) {
            const competitors = competitorData[productName] || [];
            
            return competitors.map((competitor, compIndex) => `
                <div class="competitor-row" data-comp-index="${compIndex}">
                    <div class="col-competitor">
                        <input type="text" 
                               class="enhanced-input" 
                               value="${competitor.name}" 
                               placeholder="Nombre del competidor"
                               onchange="updateCompetitorName('${productName}', ${compIndex}, this.value)">
                    </div>
                    <div class="col-sales">
                        <input type="number" 
                               class="enhanced-input" 
                               value="${competitor.sales}" 
                               placeholder="0" 
                               min="0" 
                               step="0.01"
                               onchange="updateCompetitorSales('${productName}', ${compIndex}, this.value)">
                    </div>
                    <div class="col-max">
                        <input type="radio" 
                               name="max_competitor_${productName}" 
                               ${competitor.isMax ? 'checked' : ''}
                               onchange="setMaxCompetitor('${productName}', ${compIndex})">
                    </div>
                    <div class="col-actions">
                        <button type="button" 
                                class="enhanced-btn danger" 
                                onclick="removeCompetitor('${productName}', ${compIndex})"
                                title="Eliminar competidor">
                            🗑️
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function addCompetitor(productName, productIndex) {
            if (!competitorData[productName]) {
                competitorData[productName] = [];
            }
            
            const compIndex = competitorData[productName].length;
            competitorData[productName].push({
                name: `Competidor ${compIndex + 1}`,
                sales: 0,
                isMax: false
            });
            
            // Actualizar solo esa sección
            const competitorsContainer = document.getElementById(`competitors-${productIndex}`);
            if (competitorsContainer) {
                competitorsContainer.innerHTML = renderCompetitorRows(productName, productIndex);
            }
            
            markAsChanged();
        }

        // Función para calcular la matriz BCG completa
        function calculateBCGMatrix() {
            console.log('Calculando matriz BCG...');
            
            if (products.length === 0) {
                showAlert('Primero debe agregar productos', 'warning');
                return;
            }
            
            // Calcular PRM para cada producto
            products.forEach(product => {
                const competitors = competitorData[product.name] || [];
                const maxCompetitor = competitors.find(c => c.isMax && c.sales > 0);
                
                if (maxCompetitor) {
                    product.prm = (product.sales / maxCompetitor.sales) * 100;
                } else {
                    product.prm = 0;
                }
                
                // Determinar posición BCG
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
            
            // Mostrar tabla PRM
            displayPRMResults();
            
            // Mostrar tabla de posicionamiento final
            displayBCGPositioning();
            
            // Dibujar matriz visual
            drawBCGMatrix();
            
            console.log('Matriz BCG calculada y mostrada');
        }

        function displayPRMResults() {
            const prmContainer = document.getElementById('prm-results');
            if (prmContainer) {
                prmContainer.innerHTML = `
                    <h4>PRM CALCULADO (PARTICIPACIÓN RELATIVA EN EL MERCADO)</h4>
                    <div class="prm-summary-table">
                        <div class="table-header">
                            <div class="col-product">PRODUCTO</div>
                            <div class="col-our-sales">NUESTRAS VENTAS</div>
                            <div class="col-competitor">MAYOR COMPETIDOR</div>
                            <div class="col-prm">PRM (%)</div>
                        </div>
                        ${products.map(product => {
                            const competitors = competitorData[product.name] || [];
                            const maxComp = competitors.find(c => c.isMax);
                            return `
                                <div class="table-row">
                                    <div class="col-product">${product.name}</div>
                                    <div class="col-our-sales">$${product.sales.toLocaleString()}</div>
                                    <div class="col-competitor">${maxComp ? maxComp.name + ': $' + maxComp.sales.toLocaleString() : 'No definido'}</div>
                                    <div class="col-prm calculated-field">${product.prm.toFixed(2)}%</div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                `;
            }
        }

        function displayBCGPositioning() {
            const positioningContainer = document.getElementById('bcg-positioning-table');
            if (positioningContainer) {
                positioningContainer.innerHTML = `
                    <div class="positioning-summary-table">
                        <div class="table-header">
                            <div class="col-product">PRODUCTO</div>
                            <div class="col-tcm">TCM (%)</div>
                            <div class="col-prm">PRM (%)</div>
                            <div class="col-position">POSICIÓN BCG</div>
                        </div>
                        ${products.map(product => `
                            <div class="table-row">
                                <div class="col-product">${product.name}</div>
                                <div class="col-tcm">${product.tcm.toFixed(2)}%</div>
                                <div class="col-prm">${product.prm.toFixed(2)}%</div>
                                <div class="col-position ${product.bcgPosition.toLowerCase().replace(' ', '-')}">${product.bcgPosition}</div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
        }

        // ===== FUNCIONES AUXILIARES =====
        
        function updateCompetitorName(productName, compIndex, name) {
            if (competitorData[productName] && competitorData[productName][compIndex]) {
                competitorData[productName][compIndex].name = name;
                markAsChanged();
            }
        }

        function updateCompetitorSales(productName, compIndex, sales) {
            if (competitorData[productName] && competitorData[productName][compIndex]) {
                competitorData[productName][compIndex].sales = parseFloat(sales) || 0;
                markAsChanged();
            }
        }

        function setMaxCompetitor(productName, compIndex) {
            if (competitorData[productName]) {
                // Desmarcar todos los competidores
                competitorData[productName].forEach((comp, i) => {
                    comp.isMax = (i === compIndex);
                });
                markAsChanged();
            }
        }

        function removeCompetitor(productName, compIndex) {
            if (competitorData[productName] && competitorData[productName].length > 1) {
                competitorData[productName].splice(compIndex, 1);
                
                // Actualizar la tabla visual
                const productIndex = products.findIndex(p => p.name === productName);
                if (productIndex >= 0) {
                    const competitorsContainer = document.getElementById(`competitors-${productIndex}`);
                    if (competitorsContainer) {
                        competitorsContainer.innerHTML = renderCompetitorRows(productName, productIndex);
                    }
                }
                markAsChanged();
            } else {
                showAlert('Debe mantener al menos un competidor', 'warning');
            }
        }

        function showAlert(message, type = 'info') {
            // Crear elemento de alerta
            const alertDiv = document.createElement('div');
            alertDiv.className = `enhanced-alert ${type}`;
            alertDiv.innerHTML = `
                <strong>${type === 'success' ? '✅' : type === 'warning' ? '⚠️' : type === 'error' ? '❌' : 'ℹ️'}</strong>
                ${message}
                <button type="button" onclick="this.parentElement.remove()" style="float: right; background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
            `;
            
            // Agregar al contenedor principal
            const container = document.querySelector('.section-content') || document.body;
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto-remover después de 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        function drawBCGMatrix() {
            const chartContainer = document.getElementById('bcg-chart');
            if (!chartContainer) {
                console.error('Container bcg-chart no encontrado');
                return;
            }
            
            const width = 500;
            const height = 400;
            const margin = 50;
            
            // Crear SVG
            const svg = `
                <svg width="${width}" height="${height}" class="bcg-matrix-svg">
                    <!-- Fondo y cuadrantes -->
                    <defs>
                        <pattern id="grid" width="50" height="50" patternUnits="userSpaceOnUse">
                            <path d="M 50 0 L 0 0 0 50" fill="none" stroke="#e0e0e0" stroke-width="1"/>
                        </pattern>
                    </defs>
                    
                    <!-- Fondo con cuadrículas -->
                    <rect width="100%" height="100%" fill="url(#grid)"/>
                    
                    <!-- Cuadrantes de colores -->
                    <rect x="${margin}" y="${margin}" width="${(width-2*margin)/2}" height="${(height-2*margin)/2}" 
                          fill="#4CAF50" opacity="0.1" stroke="#4CAF50" stroke-width="2"/>
                    <rect x="${margin + (width-2*margin)/2}" y="${margin}" width="${(width-2*margin)/2}" height="${(height-2*margin)/2}" 
                          fill="#FF9800" opacity="0.1" stroke="#FF9800" stroke-width="2"/>
                    <rect x="${margin}" y="${margin + (height-2*margin)/2}" width="${(width-2*margin)/2}" height="${(height-2*margin)/2}" 
                          fill="#2196F3" opacity="0.1" stroke="#2196F3" stroke-width="2"/>
                    <rect x="${margin + (width-2*margin)/2}" y="${margin + (height-2*margin)/2}" width="${(width-2*margin)/2}" height="${(height-2*margin)/2}" 
                          fill="#9E9E9E" opacity="0.1" stroke="#9E9E9E" stroke-width="2"/>
                    
                    <!-- Etiquetas de cuadrantes -->
                    <text x="${margin + (width-2*margin)/4}" y="${margin + 20}" text-anchor="middle" class="quadrant-label" fill="#4CAF50" font-weight="bold">ESTRELLA</text>
                    <text x="${margin + 3*(width-2*margin)/4}" y="${margin + 20}" text-anchor="middle" class="quadrant-label" fill="#FF9800" font-weight="bold">INTERROGANTE</text>
                    <text x="${margin + (width-2*margin)/4}" y="${height - margin - 10}" text-anchor="middle" class="quadrant-label" fill="#2196F3" font-weight="bold">VACA LECHERA</text>
                    <text x="${margin + 3*(width-2*margin)/4}" y="${height - margin - 10}" text-anchor="middle" class="quadrant-label" fill="#9E9E9E" font-weight="bold">PERRO</text>
                    
                    <!-- Ejes -->
                    <line x1="${margin}" y1="${margin}" x2="${margin}" y2="${height-margin}" stroke="#333" stroke-width="2"/>
                    <line x1="${margin}" y1="${height-margin}" x2="${width-margin}" y2="${height-margin}" stroke="#333" stroke-width="2"/>
                    
                    <!-- Líneas divisorias -->
                    <line x1="${margin + (width-2*margin)/2}" y1="${margin}" x2="${margin + (width-2*margin)/2}" y2="${height-margin}" stroke="#666" stroke-width="1" stroke-dasharray="5,5"/>
                    <line x1="${margin}" y1="${margin + (height-2*margin)/2}" x2="${width-margin}" y2="${margin + (height-2*margin)/2}" stroke="#666" stroke-width="1" stroke-dasharray="5,5"/>
                    
                    <!-- Etiquetas de ejes -->
                    <text x="${width/2}" y="${height - 10}" text-anchor="middle" class="axis-label">PRM (Participación Relativa del Mercado)</text>
                    <text x="20" y="${height/2}" text-anchor="middle" class="axis-label" transform="rotate(-90, 20, ${height/2})">TCM (Tasa de Crecimiento del Mercado)</text>
                    
                    <!-- Productos como círculos -->
                    ${products.map(product => {
                        // Normalizar posiciones (PRM en X, TCM en Y)
                        const maxPRM = Math.max(200, ...products.map(p => p.prm)); // Escala mínima de 200%
                        const maxTCM = Math.max(20, ...products.map(p => p.tcm)); // Escala mínima de 20%
                        
                        const x = margin + ((product.prm / maxPRM) * (width - 2*margin));
                        const y = height - margin - ((product.tcm / maxTCM) * (height - 2*margin));
                        
                        // Tamaño proporcional al % de ventas
                        const radius = Math.max(8, Math.min(25, product.percentage * 1.5));
                        
                        // Color según posición BCG
                        const color = product.bcgPosition === 'Estrella' ? '#4CAF50' :
                                     product.bcgPosition === 'Interrogante' ? '#FF9800' :
                                     product.bcgPosition === 'Vaca Lechera' ? '#2196F3' : '#9E9E9E';
                        
                        return `
                            <circle cx="${x}" cy="${y}" r="${radius}" fill="${color}" opacity="0.8" stroke="#333" stroke-width="2">
                                <title>${product.name}\nTCM: ${product.tcm.toFixed(1)}%\nPRM: ${product.prm.toFixed(1)}%\nVentas: ${product.percentage.toFixed(1)}%\nPosición: ${product.bcgPosition}</title>
                            </circle>
                            <text x="${x}" y="${y+4}" text-anchor="middle" class="product-label" fill="white" font-size="10" font-weight="bold">
                                ${product.name.substring(0, 8)}
                            </text>
                        `;
                    }).join('')}
                    
                    <!-- Escalas de referencia -->
                    <text x="${margin + (width-2*margin)/2}" y="${height - margin + 15}" text-anchor="middle" class="scale-label">100% PRM</text>
                    <text x="${margin - 10}" y="${margin + (height-2*margin)/2}" text-anchor="end" class="scale-label">10% TCM</text>
                </svg>
            `;
            
            chartContainer.innerHTML = svg;
        }

        function markAsChanged() {
            hasChanges = true;
        }
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

        // Las funciones addProduct y removeProduct ya están definidas arriba

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

        // ===== FUNCIÓN DE DEPURACIÓN =====
        function debugBCG() {
            console.log('=== DEBUG BCG ===');
            console.log('Products:', products);
            console.log('Market Evolution:', marketEvolution);
            console.log('Competitor Data:', competitorData);
            console.log('Current Step:', currentStep);
            
            // Verificar contenedores
            console.log('Products Container:', document.getElementById('products-container'));
            console.log('Market History Container:', document.getElementById('market-history-container'));
            console.log('Competitors Container:', document.getElementById('competitors-container'));
            console.log('BCG Chart:', document.getElementById('bcg-chart'));
        }

        // ===== INICIALIZACIÓN MEJORADA =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Inicializando BCG Analysis...');
            
            // Verificar que todos los contenedores existan
            const requiredContainers = [
                'products-container',
                'market-history-container', 
                'competitors-container',
                'tcm-results',
                'prm-results',
                'bcg-chart'
            ];
            
            let allContainersPresent = true;
            requiredContainers.forEach(containerId => {
                const container = document.getElementById(containerId);
                if (!container) {
                    console.error(`❌ Container faltante: ${containerId}`);
                    allContainersPresent = false;
                } else {
                    console.log(`✅ Container encontrado: ${containerId}`);
                }
            });
            
            if (!allContainersPresent) {
                console.error('❌ Faltan contenedores necesarios');
                return;
            }
            
            // Inicializar variables globales
            window.products = products;
            window.marketEvolution = marketEvolution; 
            window.competitorData = competitorData;
            
            // Eventos click en mini-steps
            document.querySelectorAll('.bcg-mini-steps .mini-step').forEach((step, index) => {
                step.addEventListener('click', () => {
                    console.log(`🖱️ Click en step ${index + 1}`);
                    setActiveStep(index + 1);
                });
            });
            
            // Activar step 1 por defecto  
            setActiveStep(1);
            
            // Cargar datos de ejemplo automáticamente
            console.log('📊 Cargando datos de ejemplo...');
            loadExampleData();
            
            // Función de depuración disponible globalmente
            window.debugBCG = debugBCG;
            
            console.log('✅ BCG Analysis inicializado correctamente');
            console.log('💡 Usa debugBCG() en la consola para depurar');
        });

    </script>

    <!-- Footer -->
    <?php include __DIR__ . '/../Users/footer.php'; ?>
</body>
</html>