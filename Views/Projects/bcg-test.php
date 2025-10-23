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

// Obtener datos del usuario para el header
$user = AuthController::getCurrentUser();

// Para propósitos de test, usamos datos de proyecto ficticio
$project = [
    'project_name' => 'Análisis BCG ',
    'id' => 'test'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 ANÁLISIS BCG - MATRIZ INTERACTIVA</title>
    
    <!-- CSS del sistema -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_dashboard.css">
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/Publics/css/styles_bcg_analysis.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        .section { background: white; margin: 20px 0; padding: 25px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
        .section h2 { color: #2d3748; margin-bottom: 20px; border-bottom: 3px solid #4299e1; padding-bottom: 12px; font-size: 22px; }
        
        /* ESTILO TIPO EXCEL */
        .excel-table { width: 100%; border-collapse: collapse; margin: 15px 0; border: 2px solid #d1d5db; }
        .excel-table th { background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%); color: white; font-weight: bold; padding: 12px; text-align: center; border: 1px solid #9ca3af; }
        .excel-table td { padding: 10px; text-align: center; border: 1px solid #d1d5db; }
        .excel-table tr:nth-child(even) { background: #f8fafc; }
        .excel-table tr:hover { background: #e2e8f0; }
        
        /* Colores distintivos por producto */
        .product-color-0 { background: #fef3c7 !important; } /* Amarillo claro */
        .product-color-1 { background: #d1fae5 !important; } /* Verde claro */
        .product-color-2 { background: #dbeafe !important; } /* Azul claro */
        .product-color-3 { background: #f3e8ff !important; } /* Morado claro */
        .product-color-4 { background: #fed7d7 !important; } /* Rojo claro */
        .product-color-5 { background: #e6fffa !important; } /* Turquesa claro */
        
        /* Inputs y botones estilo Excel */
        .excel-input { width: 100%; padding: 6px 10px; border: 1px solid #9ca3af; font-size: 14px; text-align: center; }
        .excel-input:focus { border-color: #4338ca; outline: none; background: #fff; }
        .excel-input.readonly { background: #f3f4f6; color: #6b7280; }
        
        .excel-btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; margin: 2px; transition: all 0.2s; }
        .excel-btn.primary { background: #4338ca; color: white; }
        .excel-btn.success { background: #059669; color: white; }
        .excel-btn.danger { background: #dc2626; color: white; }
        .excel-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        
        /* Tablas dinámicas con columnas por producto */
        .dynamic-table-container { overflow-x: auto; margin: 15px 0; }
        .dynamic-table { min-width: 600px; }
        
        /* Celdas calculadas automáticamente */
        .calculated-cell { background: #ecfdf5 !important; color: #065f46; font-weight: bold; }
        .total-cell { background: #1e40af !important; color: white; font-weight: bold; }
        
        /* Sub-tablas de competidores - Layout Grid Horizontal */
        .competitors-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; margin: 20px 0; }
        .competitor-mini-table { border: 3px solid #d1d5db; border-radius: 12px; overflow: hidden; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .mini-table-header { background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%); color: white; padding: 12px; text-align: center; font-weight: bold; font-size: 16px; }
        .mini-table-empresa { background: #f3f4f6; padding: 8px 12px; border-bottom: 2px solid #d1d5db; display: flex; justify-content: space-between; align-items: center; font-weight: bold; }
        .mini-table-body { padding: 0; }
        .mini-competitor-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 12px; border-bottom: 1px solid #e5e7eb; }
        .mini-competitor-row:hover { background: #f8fafc; }
        .mini-competitor-name { flex: 1; }
        .mini-competitor-sales { width: 80px; text-align: center; }
        .mini-competitor-actions { width: 60px; text-align: center; }
        .mini-table-mayor { background: #ecfdf5; padding: 8px 12px; border-top: 2px solid #10b981; font-weight: bold; color: #047857; text-align: center; }
        .mini-table-controls { padding: 10px; background: #f9fafb; border-top: 1px solid #e5e7eb; text-align: center; }
        
        /* Matriz BCG mejorada */
        .bcg-matrix-container { background: white; padding: 20px; border-radius: 12px; margin: 20px 0; }
        .bcg-quadrant { position: relative; }
        .bcg-quadrant-estrella { background: rgba(34, 197, 94, 0.15); }
        .bcg-quadrant-incognita { background: rgba(251, 146, 60, 0.15); }
        .bcg-quadrant-vaca { background: rgba(59, 130, 246, 0.15); }
        .bcg-quadrant-perro { background: rgba(156, 163, 175, 0.15); }
        
        /* Totales y resúmenes */
        .summary-box { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 8px; margin: 15px 0; text-align: center; }
        .summary-value { font-size: 24px; font-weight: bold; margin: 5px 0; }
        
        /* Matriz BCG */
        .bcg-chart { width: 100%; height: 400px; border: 2px solid #ddd; border-radius: 8px; background: #f9f9f9; }
        .bcg-matrix-svg { width: 100%; height: 100%; }
        .quadrant-label { font-size: 14px; font-weight: bold; }
        .axis-label { font-size: 12px; fill: #666; }
        .product-label { font-size: 10px; font-weight: bold; }
        
        /* Navegación */
        .nav-buttons { text-align: center; margin: 20px 0; }
        .nav-buttons button { padding: 12px 24px; margin: 0 10px; font-size: 16px; }
        
        /* Estados de posición BCG */
        .estrella { background: #4CAF50; color: white; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
        .interrogante { background: #FF9800; color: white; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
        .vaca-lechera { background: #2196F3; color: white; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
        .perro { background: #9E9E9E; color: white; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <!-- Notificación modo test -->
        
        
        <h1 style="text-align: center; color: #1e293b; margin-bottom: 40px; font-size: 32px;">� ANÁLISIS BCG - MATRIZ INTERACTIVA</h1>
        
        <div class="nav-buttons" style="text-align: center; margin-bottom: 30px;">
            <a href="<?php echo getBaseUrl(); ?>/Views/Users/projects.php" class="excel-btn" style="background: #6b7280; color: white; text-decoration: none;">⬅️ Volver a Proyectos</a>
            <button class="excel-btn success" onclick="addProduct()">➕ Agregar Producto</button>
            <button class="excel-btn primary" onclick="loadExampleData()">📝 Cargar Datos de Ejemplo</button>
            <button class="excel-btn primary" onclick="calculateAllMetrics()">🧮 Calcular Todo</button>
        </div>
        
        <!-- TABLA 1: PREVISIÓN DE VENTAS -->
        <div class="section">
            <h2>� TABLA 1: PREVISIÓN DE VENTAS</h2>
            <div class="dynamic-table-container">
                <table class="excel-table" id="sales-forecast-table">
                    <thead>
                        <tr>
                            <th style="width: 40%;">PRODUCTO</th>
                            <th style="width: 30%;">VENTAS (Miles S/.)</th>
                            <th style="width: 20%;">% S/TOTAL</th>
                        </tr>
                    </thead>
                    <tbody id="sales-forecast-body">
                        <!-- Filas de productos generadas dinámicamente -->
                    </tbody>
                    <tfoot>
                        <tr class="total-cell">
                            <td><strong>TOTAL</strong></td>
                            <td><strong id="total-sales">0</strong></td>
                            <td><strong>100.0%</strong></td>
                            <td>-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- TABLA 2: TASAS DE CRECIMIENTO DEL MERCADO (TCM) -->
        <div class="section">
            <h2>📈 TABLA 2: TASAS DE CRECIMIENTO DEL MERCADO (TCM)</h2>
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
                        <!-- Fila TCM PROMEDIO calculada automáticamente -->
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- TABLA 3: NIVELES DE VENTA DE COMPETIDORES (PRM) -->
        <div class="section">
            <h2>🏆 TABLA 3: NIVELES DE VENTA DE COMPETIDORES (PRM)</h2>
            <div id="competitors-container">
                <!-- Sub-tablas por producto generadas dinámicamente -->
            </div>
            <div class="summary-box">
                <h3>� RESULTADOS PRM CALCULADOS</h3>
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

        // ===== FUNCIONES PRINCIPALES =====

        function addProduct() {
            console.log('🆕 Agregando nuevo producto...');
            
            const productIndex = products.length;
            const newProduct = {
                id: productIndex,
                name: `Producto ${productIndex + 1}`,
                sales: 0,
                percentage: 0,
                color: productColors[productIndex % productColors.length]
            };
            
            products.push(newProduct);
            
            // Inicializar competidores para el nuevo producto
            competitorsByProduct[newProduct.name] = [
                { name: 'Competidor A', sales: 0, isMax: true },
                { name: 'Competidor B', sales: 0, isMax: false }
            ];
            
            // Agregar columna en TCM (agregar 0 a todos los períodos existentes)
            marketGrowthData.forEach(period => {
                if (!period.rates) period.rates = [];
                period.rates.push(0);
            });
            
            // Agregar columna en Demanda del Sector
            sectorDemandData.forEach(year => {
                if (!year.values) year.values = [];
                year.values.push(0);
            });
            
            // Actualizar automáticamente todas las tablas relacionadas
            updateAllTables();
            calculateAllMetrics();
            
            console.log('✅ Producto agregado con estructura completa:', newProduct);
            console.log('📊 Competidores inicializados:', competitorsByProduct[newProduct.name]);
        }

        function loadExampleData() {
            console.log('📊 Cargando datos de ejemplo empresariales...');
            
            // TABLA 1: Productos con ventas
            products = [
                { id: 0, name: 'Laptop Empresarial', sales: 850, percentage: 0, color: productColors[0] },
                { id: 1, name: 'Software ERP', sales: 1200, percentage: 0, color: productColors[1] },
                { id: 2, name: 'Consultoría IT', sales: 650, percentage: 0, color: productColors[2] },
                { id: 3, name: 'Hosting Cloud', sales: 400, percentage: 0, color: productColors[3] }
            ];
            
            // TABLA 2: TCM por período (últimos 4 años)
            marketGrowthData = [
                { period: '2021-2022', rates: [8.5, 15.2, 12.8, 18.5] },
                { period: '2022-2023', rates: [12.3, 18.7, 10.2, 22.1] },
                { period: '2023-2024', rates: [15.8, 14.9, 8.5, 25.3] },
                { period: '2024-2025', rates: [18.2, 12.4, 6.8, 28.7] }
            ];
            
            // TABLA 3: Competidores por producto
            competitorsByProduct = {
                'Laptop Empresarial': [
                    { name: 'Dell Latitude', sales: 950, isMax: true },
                    { name: 'HP ProBook', sales: 720, isMax: false },
                    { name: 'Lenovo ThinkPad', sales: 680, isMax: false }
                ],
                'Software ERP': [
                    { name: 'SAP Business', sales: 1800, isMax: true },
                    { name: 'Oracle NetSuite', sales: 1450, isMax: false },
                    { name: 'Microsoft Dynamics', sales: 1100, isMax: false }
                ],
                'Consultoría IT': [
                    { name: 'Accenture', sales: 2200, isMax: true },
                    { name: 'IBM Consulting', sales: 1900, isMax: false },
                    { name: 'Deloitte Digital', sales: 1650, isMax: false }
                ],
                'Hosting Cloud': [
                    { name: 'AWS', sales: 3500, isMax: true },
                    { name: 'Microsoft Azure', sales: 2800, isMax: false },
                    { name: 'Google Cloud', sales: 1850, isMax: false }
                ]
            };
            
            // TABLA 4: Demanda global del sector (en millones de soles)
            sectorDemandData = [
                { year: '2020', values: [2850, 4200, 1650, 950] },
                { year: '2021', values: [3100, 4850, 1820, 1250] },
                { year: '2022', values: [3480, 5650, 2100, 1580] },
                { year: '2023', values: [3950, 6200, 2380, 2150] },
                { year: '2024', values: [4420, 7100, 2580, 2950] }
            ];
            
            // Actualizar todas las tablas automáticamente
            updateAllTables();
            calculateAllMetrics();
            
            showAlert('Datos de ejemplo cargados exitosamente', 'success');
            console.log('✅ Datos empresariales cargados completamente');
        }

        function updateAllTables() {
            console.log('🔄 Actualizando todas las tablas...');
            renderSalesForecastTable();      // TABLA 1
            renderTCMTable();                // TABLA 2  
            renderCompetitorsTable();        // TABLA 3
            renderSectorDemandTable();       // TABLA 4
        }

        function calculateAllMetrics() {
            console.log('🧮 Calculando todas las métricas...');
            console.log('📊 Productos disponibles:', products.map(p => p.name));
            console.log('🏆 Competidores por producto:', Object.keys(competitorsByProduct));
            
            calculateTCMMetrics();
            calculatePRMMetrics(); 
            calculateBCGPositioning();
            updateMetricsSummary();
            
            console.log('✅ Métricas calculadas:', {
                tcm: calculatedMetrics.tcm,
                prm: calculatedMetrics.prm,
                positioning: Object.keys(calculatedMetrics.positioning)
            });
        }

        function addMarketPeriod() {
            const newPeriod = {
                period: `${new Date().getFullYear()}-${new Date().getFullYear() + 1}`,
                rates: products.map(() => 0)
            };
            marketGrowthData.push(newPeriod);
            renderTCMTable();
            console.log('📅 Nuevo período TCM agregado');
        }

        function addDemandPeriod() {
            const newYear = {
                year: new Date().getFullYear().toString(),
                values: products.map(() => 0)
            };
            sectorDemandData.push(newYear);
            renderSectorDemandTable();
            console.log('📊 Nuevo año de demanda agregado');
        }

        function addHistoryYear() {
            console.log('📅 Agregando nuevo período...');
            
            const newPeriod = {
                period: `${new Date().getFullYear()}-${new Date().getFullYear() + 1}`,
                rates: products.map(() => 0)
            };
            
            marketEvolution.push(newPeriod);
            renderMarketHistoryTable();
            
            console.log('✅ Período agregado:', newPeriod);
        }

        function generateCompetitorTables() {
            console.log('🏆 Generando tablas de competidores...');
            
            const container = document.getElementById('competitors-tables');
            if (!container) return;
            
            container.innerHTML = '';
            
            products.forEach((product, index) => {
                // Inicializar competidores si no existen
                if (!competitorData[product.name]) {
                    competitorData[product.name] = [
                        { name: `Competidor A`, sales: 0, isMax: false },
                        { name: `Competidor B`, sales: 0, isMax: false }
                    ];
                }
                
                const productSection = document.createElement('div');
                productSection.innerHTML = `
                    <h3 style="color: #2c3e50; margin: 20px 0 10px 0;">🏷️ ${product.name}</h3>
                    
                    <div class="competitor-header">
                        <div>COMPETIDOR</div>
                        <div>VENTAS</div>
                        <div>MAYOR</div>
                        <div>ACCIONES</div>
                    </div>
                    
                    <div id="competitors-${index}">
                        ${renderCompetitorRows(product.name, index)}
                    </div>
                    
                    <button class="enhanced-btn success" onclick="addCompetitor('${product.name}', ${index})">
                        ➕ Agregar Competidor
                    </button>
                `;
                
                container.appendChild(productSection);
            });
            
            console.log('✅ Tablas de competidores generadas');
        }

        function calculateBCGMatrix() {
            console.log('🧮 Calculando matriz BCG...');
            
            // Calcular TCM promedio para cada producto
            products.forEach((product, productIndex) => {
                const rates = marketEvolution.map(period => period.rates[productIndex] || 0);
                product.tcm = rates.length > 0 ? (rates.reduce((sum, rate) => sum + rate, 0) / rates.length) : 0;
            });
            
            // Calcular PRM para cada producto
            products.forEach(product => {
                const competitors = competitorData[product.name] || [];
                const maxCompetitor = competitors.find(c => c.isMax);
                
                if (maxCompetitor && maxCompetitor.sales > 0) {
                    product.prm = (product.sales / maxCompetitor.sales) * 100;
                } else {
                    product.prm = 0;
                }
                
                // Determinar posición BCG
                const avgTCM = products.reduce((sum, p) => sum + p.tcm, 0) / products.length;
                const avgPRM = 100; // 100% es el punto de referencia para PRM
                
                if (product.tcm >= avgTCM && product.prm >= avgPRM) {
                    product.bcgPosition = 'Estrella';
                } else if (product.tcm >= avgTCM && product.prm < avgPRM) {
                    product.bcgPosition = 'Interrogante';
                } else if (product.tcm < avgTCM && product.prm >= avgPRM) {
                    product.bcgPosition = 'Vaca Lechera';
                } else {
                    product.bcgPosition = 'Perro';
                }
            });
            
            // Mostrar resultados
            displayBCGPositioning();
            drawBCGMatrix();
            
            console.log('✅ Matriz BCG calculada');
        }

        // ===== FUNCIONES DE RENDERIZADO DE TABLAS =====

        function renderSalesForecastTable() {
            console.log('📊 Renderizando Tabla 1: Previsión de Ventas');
            const tbody = document.getElementById('sales-forecast-body');
            if (!tbody) return;
            
            // Calcular porcentajes automáticamente
            const totalSales = products.reduce((sum, p) => sum + p.sales, 0);
            products.forEach(product => {
                product.percentage = totalSales > 0 ? ((product.sales / totalSales) * 100) : 0;
            });
            
            tbody.innerHTML = products.map((product, index) => `
                <tr class="product-color-${index % productColors.length}">
                    <td>
                        <input type="text" 
                               class="excel-input" 
                               value="${product.name}" 
                               onchange="updateProductName(${index}, this.value)"
                               style="background: ${product.color}; font-weight: bold;">
                    </td>
                    <td>
                        <input type="number" 
                               class="excel-input" 
                               value="${product.sales}" 
                               min="0" 
                               step="10"
                               onchange="updateProductSales(${index}, this.value)">
                    </td>
                    <td class="calculated-cell">
                        ${product.percentage.toFixed(1)}%
                    </td>
                    <td>
                        <button class="excel-btn danger" onclick="removeProduct(${index})" title="Eliminar producto">
                            🗑️
                        </button>
                    </td>
                </tr>
            `).join('');
            
            // Actualizar total
            document.getElementById('total-sales').textContent = totalSales.toLocaleString();
        }

        function renderTCMTable() {
            console.log('📈 Renderizando Tabla 2: TCM (Tasas de Crecimiento del Mercado)');
            
            const thead = document.getElementById('tcm-header');
            const tbody = document.getElementById('tcm-body');
            const tfoot = document.getElementById('tcm-footer');
            
            if (!thead || !tbody || !tfoot) return;
            
            // Generar encabezado dinámico
            thead.innerHTML = `
                <tr>
                    <th style="width: 15%;">PERÍODOS</th>
                    ${products.map((product, index) => `
                        <th style="background: ${product.color};">${product.name}</th>
                    `).join('')}
                    <th style="width: 10%;">ACCIONES</th>
                </tr>
            `;
            
            // Generar filas de períodos
            tbody.innerHTML = marketGrowthData.map((period, periodIndex) => `
                <tr>
                    <td>
                        <input type="text" 
                               class="excel-input" 
                               value="${period.period}" 
                               onchange="updateMarketPeriod(${periodIndex}, this.value)">
                    </td>
                    ${products.map((product, productIndex) => `
                        <td>
                            <input type="number" 
                                   class="excel-input" 
                                   value="${period.rates[productIndex] || 0}" 
                                   step="0.1" 
                                   min="0"
                                   max="100"
                                   onchange="updateTCMRate(${periodIndex}, ${productIndex}, this.value)">
                        </td>
                    `).join('')}
                    <td>
                        <button class="excel-btn danger" onclick="removeMarketPeriod(${periodIndex})" title="Eliminar período">
                            🗑️
                        </button>
                    </td>
                </tr>
            `).join('');
            
            // Calcular y mostrar TCM PROMEDIO
            const avgTCM = products.map((product, productIndex) => {
                const rates = marketGrowthData.map(period => period.rates[productIndex] || 0);
                const avg = rates.length > 0 ? (rates.reduce((sum, rate) => sum + rate, 0) / rates.length) : 0;
                calculatedMetrics.tcm[product.name] = avg;
                return avg;
            });
            
            tfoot.innerHTML = `
                <tr class="calculated-cell" style="font-weight: bold;">
                    <td>TCM PROMEDIO</td>
                    ${avgTCM.map(avg => `<td>${avg.toFixed(2)}%</td>`).join('')}
                    <td>-</td>
                </tr>
            `;
        }

        function renderCompetitorsTable() {
            console.log('🏆 Renderizando Tabla 3: Competidores y PRM (Layout Grid Horizontal)');
            
            const container = document.getElementById('competitors-container');
            if (!container) return;
            
            // Crear grid horizontal con mini-tablas por producto
            container.innerHTML = `
                <div class="competitors-grid">
                    ${products.map((product, productIndex) => {
                        // Inicializar competidores si no existen
                        if (!competitorsByProduct[product.name]) {
                            competitorsByProduct[product.name] = [
                                { name: 'Competidor A', sales: 0, isMax: false },
                                { name: 'Competidor B', sales: 0, isMax: false }
                            ];
                        }
                        
                        const competitors = competitorsByProduct[product.name];
                        const maxCompetitor = competitors.find(c => c.isMax);
                        const maxSales = maxCompetitor ? maxCompetitor.sales : 0;
                        
                        return `
                            <div class="competitor-mini-table" style="border-color: ${product.color};">
                                <!-- Header del producto -->
                                <div class="mini-table-header" style="background: ${product.color}; color: #374151; font-weight: bold;">
                                    📦 ${product.name.toUpperCase()}
                                </div>
                                
                                <!-- Nuestras ventas -->
                                <div class="mini-table-empresa">
                                    <span><strong>NUESTRA EMPRESA</strong></span>
                                    <span style="color: #059669; font-size: 18px;"><strong>${product.sales.toLocaleString()}</strong></span>
                                </div>
                                
                                <!-- Tabla de competidores compacta -->
                                <div class="mini-table-body">
                                    <div style="background: #f8fafc; padding: 8px 12px; border-bottom: 1px solid #d1d5db; display: flex; font-weight: bold; color: #6b7280; font-size: 12px;">
                                        <div style="flex: 1;">COMPETIDOR</div>
                                        <div style="width: 80px; text-align: center;">VENTAS</div>
                                        <div style="width: 40px; text-align: center;">✓</div>
                                        <div style="width: 30px; text-align: center;">⚙️</div>
                                    </div>
                                    
                                    ${competitors.map((comp, compIndex) => `
                                        <div class="mini-competitor-row">
                                            <div class="mini-competitor-name">
                                                <input type="text" 
                                                       class="excel-input" 
                                                       value="${comp.name}"
                                                       placeholder="Nombre competidor"
                                                       style="border: 1px solid #d1d5db; padding: 4px 6px; font-size: 12px; width: 100%;"
                                                       onchange="updateCompetitorNameByIndex(${productIndex}, ${compIndex}, this.value)">
                                            </div>
                                            <div class="mini-competitor-sales">
                                                <input type="number" 
                                                       class="excel-input" 
                                                       value="${comp.sales}"
                                                       placeholder="0"
                                                       min="0" 
                                                       step="10"
                                                       style="border: 1px solid #d1d5db; padding: 4px 6px; font-size: 12px; width: 100%; text-align: center;"
                                                       onchange="updateCompetitorSalesByIndex(${productIndex}, ${compIndex}, this.value)">
                                            </div>
                                            <div style="width: 40px; text-align: center;">
                                                <input type="radio" 
                                                       name="max_${product.name.replace(/\s+/g, '_')}" 
                                                       ${comp.isMax ? 'checked' : ''}
                                                       onchange="setMaxCompetitorByIndex(${productIndex}, ${compIndex})"
                                                       title="Marcar como mayor competidor">
                                            </div>
                                            <div class="mini-competitor-actions" style="width: 30px; text-align: center;">
                                                <button class="excel-btn danger" 
                                                        onclick="removeCompetitorByIndex(${productIndex}, ${compIndex})" 
                                                        style="padding: 2px 6px; font-size: 12px;"
                                                        title="Eliminar competidor">
                                                    🗑️
                                                </button>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                                
                                <!-- Mayor competidor destacado -->
                                <div class="mini-table-mayor">
                                    <strong>👑 MAYOR: ${maxCompetitor ? `${maxCompetitor.name} (${maxSales.toLocaleString()})` : 'No definido'}</strong>
                                </div>
                                
                                <!-- Controles -->
                                <div class="mini-table-controls">
                                    <button class="excel-btn success" 
                                            onclick="addCompetitorByIndex(${productIndex})"
                                            style="padding: 6px 12px; font-size: 12px; margin-right: 5px;">
                                        ➕ Agregar
                                    </button>
                                    <button class="excel-btn primary" 
                                            onclick="calculatePRMByIndex(${productIndex})"
                                            style="padding: 6px 12px; font-size: 12px;">
                                        🧮 PRM
                                    </button>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            `;
        }

        function renderSectorDemandTable() {
            console.log('🌍 Renderizando Tabla 4: Demanda Global del Sector');
            
            const thead = document.getElementById('demand-header');
            const tbody = document.getElementById('demand-body');
            
            if (!thead || !tbody) return;
            
            // Encabezado dinámico
            thead.innerHTML = `
                <tr>
                    <th style="width: 15%;">AÑOS</th>
                    ${products.map(product => `
                        <th style="background: ${product.color};">${product.name}</th>
                    `).join('')}
                    <th style="width: 10%;">ACCIONES</th>
                </tr>
            `;
            
            // Filas de años con demanda
            tbody.innerHTML = sectorDemandData.map((yearData, yearIndex) => `
                <tr>
                    <td>
                        <input type="text" 
                               class="excel-input" 
                               value="${yearData.year}" 
                               onchange="updateDemandYear(${yearIndex}, this.value)">
                    </td>
                    ${products.map((product, productIndex) => `
                        <td>
                            <input type="number" 
                                   class="excel-input" 
                                   value="${yearData.values[productIndex] || 0}" 
                                   step="50" 
                                   min="0"
                                   onchange="updateDemandValue(${yearIndex}, ${productIndex}, this.value)">
                        </td>
                    `).join('')}
                    <td>
                        <button class="excel-btn danger" onclick="removeDemandYear(${yearIndex})" title="Eliminar año">
                            🗑️
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // ===== FUNCIONES DE ACTUALIZACIÓN DE DATOS =====

        function updateProductName(index, name) {
            if (products[index]) {
                const oldName = products[index].name;
                products[index].name = name;
                
                // Actualizar en competidores si existe
                if (competitorsByProduct[oldName]) {
                    competitorsByProduct[name] = competitorsByProduct[oldName];
                    delete competitorsByProduct[oldName];
                }
                
                updateAllTables();
            }
        }

        function updateProductSales(index, sales) {
            if (products[index]) {
                products[index].sales = parseFloat(sales) || 0;
                renderSalesForecastTable(); // Actualizar tabla 1
                calculateAllMetrics(); // Recalcular métricas automáticamente
                console.log(`💰 Ventas actualizadas para ${products[index].name}: ${sales}`);
            }
        }

        function removeProduct(index) {
            if (products.length <= 1) {
                showAlert('Debe mantener al menos un producto', 'warning');
                return;
            }
            
            const productName = products[index].name;
            
            // Eliminar de todas las estructuras de datos
            products.splice(index, 1);
            delete competitorsByProduct[productName];
            
            // Eliminar de TCM y Demanda
            marketGrowthData.forEach(period => {
                if (period.rates) period.rates.splice(index, 1);
            });
            sectorDemandData.forEach(year => {
                if (year.values) year.values.splice(index, 1);
            });
            
            // Reindexar productos
            products.forEach((product, newIndex) => {
                product.id = newIndex;
            });
            
            updateAllTables();
            showAlert(`Producto "${productName}" eliminado`, 'success');
        }

        function updateMarketPeriod(periodIndex, newPeriod) {
            if (marketGrowthData[periodIndex]) {
                marketGrowthData[periodIndex].period = newPeriod;
            }
        }

        function updateTCMRate(periodIndex, productIndex, rate) {
            if (marketGrowthData[periodIndex]) {
                if (!marketGrowthData[periodIndex].rates) {
                    marketGrowthData[periodIndex].rates = [];
                }
                marketGrowthData[periodIndex].rates[productIndex] = parseFloat(rate) || 0;
                renderTCMTable(); // Recalcular promedios TCM
                calculateAllMetrics(); // Recalcular todas las métricas automáticamente
                console.log(`📈 TCM actualizado: período ${periodIndex}, producto ${productIndex}, valor ${rate}%`);
            }
        }

        function removeMarketPeriod(periodIndex) {
            if (marketGrowthData.length <= 1) {
                showAlert('Debe mantener al menos un período', 'warning');
                return;
            }
            marketGrowthData.splice(periodIndex, 1);
            renderTCMTable();
        }

        function updateCompetitorName(productName, compIndex, name) {
            if (competitorsByProduct[productName] && competitorsByProduct[productName][compIndex]) {
                competitorsByProduct[productName][compIndex].name = name;
            }
        }

        function updateCompetitorSales(productName, compIndex, sales) {
            if (competitorsByProduct[productName] && competitorsByProduct[productName][compIndex]) {
                competitorsByProduct[productName][compIndex].sales = parseFloat(sales) || 0;
                calculateAllMetrics(); // Recalcular PRM y todas las métricas automáticamente
                console.log(`🏆 Competidor actualizado: ${productName}, ${competitorsByProduct[productName][compIndex].name}, ventas: ${sales}`);
            }
        }

        function setMaxCompetitor(productName, compIndex) {
            if (competitorsByProduct[productName]) {
                competitorsByProduct[productName].forEach((comp, i) => {
                    comp.isMax = (i === compIndex);
                });
                calculateAllMetrics(); // Recalcular PRM y todas las métricas automáticamente
                console.log(`👑 Mayor competidor seleccionado para ${productName}: ${competitorsByProduct[productName][compIndex].name}`);
            }
        }

        function addCompetitor(productName, productIndex = null) {
            console.log(`➕ Agregando competidor para: ${productName}`);
            
            if (!competitorsByProduct[productName]) {
                competitorsByProduct[productName] = [];
                console.log(`   Inicializando array de competidores para ${productName}`);
            }
            
            const competitorCount = competitorsByProduct[productName].length;
            const newCompetitor = {
                name: `Competidor ${String.fromCharCode(65 + competitorCount)}`, // A, B, C, etc.
                sales: 0,
                isMax: competitorCount === 0 // Primer competidor es mayor por defecto
            };
            
            competitorsByProduct[productName].push(newCompetitor);
            
            // Re-renderizar solo la tabla de competidores
            renderCompetitorsTable();
            calculateAllMetrics(); // Recalcular métricas
            
            showAlert(`Competidor ${newCompetitor.name} agregado para ${productName}`, 'success');
            console.log(`✅ Competidor agregado para ${productName}:`, newCompetitor);
            console.log(`📊 Competidores actuales para ${productName}:`, competitorsByProduct[productName]);
        }

        function removeCompetitor(productName, compIndex) {
            if (competitorsByProduct[productName] && competitorsByProduct[productName].length > 1) {
                competitorsByProduct[productName].splice(compIndex, 1);
                renderCompetitorsTable();
            } else {
                showAlert('Debe mantener al menos un competidor', 'warning');
            }
        }

        function updateDemandYear(yearIndex, newYear) {
            if (sectorDemandData[yearIndex]) {
                sectorDemandData[yearIndex].year = newYear;
            }
        }

        function updateDemandValue(yearIndex, productIndex, value) {
            if (sectorDemandData[yearIndex]) {
                if (!sectorDemandData[yearIndex].values) {
                    sectorDemandData[yearIndex].values = [];
                }
                sectorDemandData[yearIndex].values[productIndex] = parseFloat(value) || 0;
            }
        }

        function removeDemandYear(yearIndex) {
            if (sectorDemandData.length <= 1) {
                showAlert('Debe mantener al menos un año', 'warning');
                return;
            }
            sectorDemandData.splice(yearIndex, 1);
            renderSectorDemandTable();
        }

        // ===== FUNCIONES DE CÁLCULO DE MÉTRICAS =====

        function calculateTCMMetrics() {
            console.log('📊 Calculando métricas TCM...');
            
            products.forEach((product, productIndex) => {
                const rates = marketGrowthData.map(period => period.rates[productIndex] || 0);
                const avgTCM = rates.length > 0 ? (rates.reduce((sum, rate) => sum + rate, 0) / rates.length) : 0;
                calculatedMetrics.tcm[product.name] = avgTCM;
            });
        }

        function calculatePRMMetrics() {
            console.log('🏆 Calculando métricas PRM para todos los productos...');
            
            products.forEach(product => {
                console.log(`📋 Calculando PRM para: ${product.name}`);
                
                const competitors = competitorsByProduct[product.name] || [];
                console.log(`   Competidores encontrados: ${competitors.length}`);
                
                const maxCompetitor = competitors.find(c => c.isMax);
                console.log(`   Mayor competidor: ${maxCompetitor ? `${maxCompetitor.name} (${maxCompetitor.sales})` : 'NO DEFINIDO'}`);
                
                let prmValue = 0;
                if (maxCompetitor && maxCompetitor.sales > 0) {
                    prmValue = (product.sales / maxCompetitor.sales);
                    console.log(`   PRM = ${product.sales} / ${maxCompetitor.sales} = ${prmValue.toFixed(3)}`);
                } else {
                    console.log(`   ⚠️ PRM = 0 (sin competidor mayor válido)`);
                }
                
                calculatedMetrics.prm[product.name] = prmValue;
            });
            
            console.log('📊 PRM calculados:', calculatedMetrics.prm);
            updatePRMSummary();
        }

        function calculatePRMForProduct(productName, productIndex = null) {
            console.log(`🏆 Calculando PRM específico para: ${productName}`);
            
            const product = products.find(p => p.name === productName);
            if (!product) {
                console.error(`❌ Producto no encontrado: ${productName}`);
                showAlert(`Error: Producto "${productName}" no encontrado`, 'error');
                return;
            }
            
            const competitors = competitorsByProduct[productName] || [];
            console.log(`   Competidores disponibles:`, competitors);
            
            const maxCompetitor = competitors.find(c => c.isMax);
            console.log(`   Mayor competidor:`, maxCompetitor);
            
            let prmValue = 0;
            if (maxCompetitor && maxCompetitor.sales > 0) {
                prmValue = (product.sales / maxCompetitor.sales);
                showAlert(`✅ PRM de ${productName}: ${prmValue.toFixed(3)} = ${product.sales.toLocaleString()}/${maxCompetitor.sales.toLocaleString()}`, 'success');
                console.log(`   Cálculo: ${product.sales} / ${maxCompetitor.sales} = ${prmValue.toFixed(3)}`);
            } else {
                showAlert(`⚠️ ${productName}: Necesita competidor mayor con ventas > 0`, 'warning');
                console.log(`   ⚠️ Sin competidor mayor válido`);
            }
            
            calculatedMetrics.prm[productName] = prmValue;
            updatePRMSummary();
            updateBCGSummary(); // Actualizar también la tabla de BCG
            
            console.log(`📊 PRM final para ${productName}: ${prmValue.toFixed(3)}`);
        }

        function calculateBCGPositioning() {
            console.log('🎯 Calculando posicionamiento BCG...');
            
            products.forEach(product => {
                const tcm = calculatedMetrics.tcm[product.name] || 0;
                const prm = calculatedMetrics.prm[product.name] || 0;
                
                // Criterios BCG: TCM > 10% y PRM > 1.0
                let position;
                if (tcm >= 10 && prm >= 1.0) {
                    position = 'Estrella ⭐';
                } else if (tcm >= 10 && prm < 1.0) {
                    position = 'Incógnita ❓';
                } else if (tcm < 10 && prm >= 1.0) {
                    position = 'Vaca 🐄';
                } else {
                    position = 'Perro 🐕';
                }
                
                calculatedMetrics.positioning[product.name] = {
                    tcm: tcm,
                    prm: prm,
                    position: position,
                    salesPercentage: product.percentage
                };
            });
        }

        function updateMetricsSummary() {
            console.log('📋 Actualizando resumen de métricas...');
            updatePRMSummary();
            updateBCGSummary();
        }

        function updatePRMSummary() {
            const container = document.getElementById('prm-summary');
            if (!container) return;
            
            container.innerHTML = `
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 15px;">
                    ${products.map(product => {
                        const prm = calculatedMetrics.prm[product.name] || 0;
                        const competitors = competitorsByProduct[product.name] || [];
                        const maxComp = competitors.find(c => c.isMax);
                        
                        return `
                            <div style="background: ${product.color}; padding: 15px; border-radius: 8px; border: 2px solid #e5e7eb;">
                                <h4 style="margin: 0 0 10px 0; color: #374151;">${product.name}</h4>
                                <div><strong>PRM:</strong> ${prm.toFixed(3)}</div>
                                <div><strong>Nuestras ventas:</strong> ${product.sales.toLocaleString()} k</div>
                                <div><strong>Mayor competidor:</strong> ${maxComp ? `${maxComp.name} (${maxComp.sales.toLocaleString()} k)` : 'No definido'}</div>
                                <div style="margin-top: 8px; font-weight: bold; color: ${prm >= 1.0 ? '#059669' : '#dc2626'};">
                                    ${prm >= 1.0 ? '✅ LIDER DEL MERCADO' : '❌ SEGUIDOR DEL MERCADO'}
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            `;
        }

        function generateBCGMatrix() {
            console.log('🎯 Generando matriz BCG completa...');
            
            // Calcular todas las métricas
            calculateAllMetrics();
            
            // Generar resumen de posicionamiento
            updateBCGSummary();
            
            // Generar matriz visual
            generateBCGVisual();
            
            showAlert('Matriz BCG generada exitosamente', 'success');
        }

        function updateBCGSummary() {
            const container = document.getElementById('bcg-positioning-summary');
            if (!container) return;
            
            container.innerHTML = `
                <h3>📊 RESUMEN DE POSICIONAMIENTO BCG</h3>
                <div class="dynamic-table-container">
                    <table class="excel-table">
                        <thead>
                            <tr>
                                <th>PRODUCTO</th>
                                <th>TCM (%)</th>
                                <th>PRM</th>
                                <th>% VENTAS</th>
                                <th>POSICIÓN BCG</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${products.map((product, index) => {
                                const tcm = calculatedMetrics.tcm[product.name] || 0;
                                const prm = calculatedMetrics.prm[product.name] || 0;
                                const positioning = calculatedMetrics.positioning[product.name] || {};
                                
                                return `
                                    <tr class="product-color-${index}">
                                        <td style="background: ${product.color}; font-weight: bold;">${product.name}</td>
                                        <td class="calculated-cell">${tcm.toFixed(2)}%</td>
                                        <td class="calculated-cell">${prm.toFixed(3)}</td>
                                        <td class="calculated-cell">${product.percentage.toFixed(1)}%</td>
                                        <td style="text-align: center; font-weight: bold; font-size: 16px;">${positioning.position || 'Perro 🐕'}</td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        }

        function generateBCGVisual() {
            const container = document.getElementById('bcg-visual-matrix');
            if (!container) return;
            
            const width = 600;
            const height = 450;
            const margin = 60;
            
            container.innerHTML = `
                <svg width="${width}" height="${height}" style="border: 2px solid #d1d5db; border-radius: 8px;">
                    <!-- Cuadrantes -->
                    <rect x="${margin}" y="${margin}" width="${(width-2*margin)/2}" height="${(height-2*margin)/2}" 
                          fill="#4ade80" opacity="0.15" stroke="#16a34a" stroke-width="2"/>
                    <rect x="${margin + (width-2*margin)/2}" y="${margin}" width="${(width-2*margin)/2}" height="${(height-2*margin)/2}" 
                          fill="#fb923c" opacity="0.15" stroke="#ea580c" stroke-width="2"/>
                    <rect x="${margin}" y="${margin + (height-2*margin)/2}" width="${(width-2*margin)/2}" height="${(height-2*margin)/2}" 
                          fill="#3b82f6" opacity="0.15" stroke="#1d4ed8" stroke-width="2"/>
                    <rect x="${margin + (width-2*margin)/2}" y="${margin + (height-2*margin)/2}" width="${(width-2*margin)/2}" height="${(height-2*margin)/2}" 
                          fill="#9ca3af" opacity="0.15" stroke="#6b7280" stroke-width="2"/>
                    
                    <!-- Etiquetas de cuadrantes -->
                    <text x="${margin + (width-2*margin)/4}" y="${margin + 25}" text-anchor="middle" style="font-weight: bold; font-size: 14px; fill: #16a34a;">⭐ ESTRELLA</text>
                    <text x="${margin + 3*(width-2*margin)/4}" y="${margin + 25}" text-anchor="middle" style="font-weight: bold; font-size: 14px; fill: #ea580c;">❓ INCÓGNITA</text>
                    <text x="${margin + (width-2*margin)/4}" y="${height - margin - 10}" text-anchor="middle" style="font-weight: bold; font-size: 14px; fill: #1d4ed8;">🐄 VACA</text>
                    <text x="${margin + 3*(width-2*margin)/4}" y="${height - margin - 10}" text-anchor="middle" style="font-weight: bold; font-size: 14px; fill: #6b7280;">🐕 PERRO</text>
                    
                    <!-- Ejes -->
                    <line x1="${margin}" y1="${margin}" x2="${margin}" y2="${height-margin}" stroke="#374151" stroke-width="3"/>
                    <line x1="${margin}" y1="${height-margin}" x2="${width-margin}" y2="${height-margin}" stroke="#374151" stroke-width="3"/>
                    
                    <!-- Líneas divisorias -->
                    <line x1="${margin + (width-2*margin)/2}" y1="${margin}" x2="${margin + (width-2*margin)/2}" y2="${height-margin}" stroke="#6b7280" stroke-width="2" stroke-dasharray="8,4"/>
                    <line x1="${margin}" y1="${margin + (height-2*margin)/2}" x2="${width-margin}" y2="${margin + (height-2*margin)/2}" stroke="#6b7280" stroke-width="2" stroke-dasharray="8,4"/>
                    
                    <!-- Etiquetas de ejes -->
                    <text x="${width/2}" y="${height - 15}" text-anchor="middle" style="font-size: 12px; fill: #374151; font-weight: bold;">PRM (Participación Relativa) →</text>
                    <text x="25" y="${height/2}" text-anchor="middle" style="font-size: 12px; fill: #374151; font-weight: bold;" transform="rotate(-90, 25, ${height/2})">↑ TCM (% Crecimiento)</text>
                    
                    <!-- Marcas de escala -->
                    <text x="${margin + (width-2*margin)/2}" y="${height - margin + 15}" text-anchor="middle" style="font-size: 10px; fill: #6b7280;">1.0</text>
                    <text x="${margin - 15}" y="${margin + (height-2*margin)/2}" text-anchor="middle" style="font-size: 10px; fill: #6b7280;">10%</text>
                    
                    <!-- Productos posicionados -->
                    ${products.map((product, index) => {
                        const tcm = calculatedMetrics.tcm[product.name] || 0;
                        const prm = calculatedMetrics.prm[product.name] || 0;
                        const positioning = calculatedMetrics.positioning[product.name] || {};
                        
                        // Escalar posiciones (TCM: 0-30%, PRM: 0-3.0)
                        const xPos = margin + Math.min((prm / 3.0) * (width - 2*margin), width - 2*margin);
                        const yPos = height - margin - Math.min((tcm / 30) * (height - 2*margin), height - 2*margin);
                        
                        // Tamaño proporcional al % de ventas
                        const radius = Math.max(12, Math.min(30, product.percentage * 2));
                        
                        return `
                            <circle cx="${xPos}" cy="${yPos}" r="${radius}" 
                                    fill="${product.color}" 
                                    opacity="0.8" 
                                    stroke="#374151" 
                                    stroke-width="3">
                                <title>${product.name}
TCM: ${tcm.toFixed(2)}%
PRM: ${prm.toFixed(3)}
Ventas: ${product.percentage.toFixed(1)}%
Posición: ${positioning.position || 'Perro 🐕'}</title>
                            </circle>
                            <text x="${xPos}" y="${yPos + 4}" text-anchor="middle" 
                                  style="font-size: 10px; font-weight: bold; fill: #374151; pointer-events: none;">
                                ${product.name.substring(0, 8)}
                            </text>
                        `;
                    }).join('')}
                </svg>
                
                <div style="margin-top: 20px; text-align: center; color: #6b7280; font-size: 14px;">
                    <p><strong>Interpretación:</strong> Tamaño del círculo = % sobre ventas totales | Posición = TCM vs PRM</p>
                </div>
            `;
        }

        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.style.cssText = `
                position: fixed; top: 20px; right: 20px; z-index: 1000;
                padding: 15px 20px; border-radius: 8px; color: white; font-weight: bold;
                background: ${type === 'success' ? '#059669' : type === 'warning' ? '#d97706' : type === 'error' ? '#dc2626' : '#0284c7'};
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            `;
            alertDiv.innerHTML = `
                ${type === 'success' ? '✅' : type === 'warning' ? '⚠️' : type === 'error' ? '❌' : 'ℹ️'} ${message}
                <button onclick="this.parentElement.remove()" style="background: none; border: none; color: white; float: right; font-size: 18px; cursor: pointer; margin-left: 10px;">&times;</button>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                if (alertDiv.parentNode) alertDiv.remove();
            }, 4000);
        }

        // Función de depuración global
        function debugBCG() {
            console.log('🔍 === ESTADO COMPLETO BCG ===');
            console.log('📊 PRODUCTOS:', products);
            console.log('📈 MARKET GROWTH DATA:', marketGrowthData);
            console.log('🏆 COMPETITORS BY PRODUCT:', competitorsByProduct);
            console.log('🌍 SECTOR DEMAND DATA:', sectorDemandData);
            console.log('🧮 CALCULATED METRICS:', calculatedMetrics);
            console.log('=================================');
            
            // Mostrar estado actual en una alerta
            const productCount = products.length;
            const competitorCount = Object.keys(competitorsByProduct).length;
            const tcmCount = Object.keys(calculatedMetrics.tcm || {}).length;
            const prmCount = Object.keys(calculatedMetrics.prm || {}).length;
            
            showAlert(`Debug: ${productCount} productos, ${competitorCount} con competidores, TCM: ${tcmCount}, PRM: ${prmCount}`, 'info');
            
            return {
                products,
                marketGrowthData,
                competitorsByProduct,
                sectorDemandData,
                calculatedMetrics
            };
        }

        // ===== FUNCIONES BASADAS EN ÍNDICES PARA COMPETIDORES =====

        function addCompetitorByIndex(productIndex) {
            console.log(`🔥 addCompetitorByIndex llamada con índice: ${productIndex}`);
            
            if (productIndex < 0 || productIndex >= products.length) {
                console.error(`❌ Índice de producto inválido: ${productIndex}`);
                showAlert('Error: Índice de producto inválido', 'error');
                return;
            }
            
            const product = products[productIndex];
            const productName = product.name;
            
            console.log(`📋 Agregando competidor para producto: ${productName} (índice ${productIndex})`);
            
            if (!competitorsByProduct[productName]) {
                competitorsByProduct[productName] = [];
            }
            
            const newCompetitor = {
                name: `Competidor ${competitorsByProduct[productName].length + 1}`,
                sales: 0,
                isMax: false
            };
            
            competitorsByProduct[productName].push(newCompetitor);
            
            // Re-renderizar solo la tabla de competidores
            renderCompetitorsTable();
            calculateAllMetrics(); // Recalcular métricas
            
            showAlert(`Competidor ${newCompetitor.name} agregado para ${productName}`, 'success');
            console.log(`✅ Competidor agregado para ${productName}:`, newCompetitor);
            console.log(`📊 Competidores actuales para ${productName}:`, competitorsByProduct[productName]);
        }

        function calculatePRMByIndex(productIndex) {
            console.log(`🧮 calculatePRMByIndex llamada con índice: ${productIndex}`);
            
            if (productIndex < 0 || productIndex >= products.length) {
                console.error(`❌ Índice de producto inválido: ${productIndex}`);
                showAlert('Error: Índice de producto inválido', 'error');
                return;
            }
            
            const product = products[productIndex];
            const productName = product.name;
            
            console.log(`🏆 Calculando PRM para: ${productName} (índice ${productIndex})`);
            
            const competitors = competitorsByProduct[productName] || [];
            console.log(`   Competidores disponibles:`, competitors);
            
            const maxCompetitor = competitors.find(c => c.isMax);
            console.log(`   Mayor competidor:`, maxCompetitor);
            
            let prmValue = 0;
            if (maxCompetitor && maxCompetitor.sales > 0) {
                prmValue = (product.sales / maxCompetitor.sales);
                showAlert(`✅ PRM de ${productName}: ${prmValue.toFixed(3)} = ${product.sales.toLocaleString()}/${maxCompetitor.sales.toLocaleString()}`, 'success');
                console.log(`   Cálculo: ${product.sales} / ${maxCompetitor.sales} = ${prmValue.toFixed(3)}`);
            } else {
                showAlert(`⚠️ ${productName}: Necesita competidor mayor con ventas > 0`, 'warning');
                console.log(`   ⚠️ Sin competidor mayor válido`);
            }
            
            calculatedMetrics.prm[productName] = prmValue;
            updatePRMSummary();
            updateBCGSummary(); // Actualizar también la tabla de BCG
            
            console.log(`📊 PRM final para ${productName}: ${prmValue.toFixed(3)}`);
        }

        function updateCompetitorNameByIndex(productIndex, compIndex, newName) {
            console.log(`📝 updateCompetitorNameByIndex: producto ${productIndex}, competidor ${compIndex}, nuevo nombre: ${newName}`);
            
            if (productIndex < 0 || productIndex >= products.length) {
                console.error(`❌ Índice de producto inválido: ${productIndex}`);
                return;
            }
            
            const productName = products[productIndex].name;
            
            if (competitorsByProduct[productName] && competitorsByProduct[productName][compIndex]) {
                competitorsByProduct[productName][compIndex].name = newName || `Competidor ${compIndex + 1}`;
                console.log(`✅ Nombre actualizado para competidor ${compIndex} de ${productName}: ${competitorsByProduct[productName][compIndex].name}`);
            }
        }

        function updateCompetitorSalesByIndex(productIndex, compIndex, sales) {
            console.log(`💰 updateCompetitorSalesByIndex: producto ${productIndex}, competidor ${compIndex}, ventas: ${sales}`);
            
            if (productIndex < 0 || productIndex >= products.length) {
                console.error(`❌ Índice de producto inválido: ${productIndex}`);
                return;
            }
            
            const productName = products[productIndex].name;
            
            if (competitorsByProduct[productName] && competitorsByProduct[productName][compIndex]) {
                competitorsByProduct[productName][compIndex].sales = parseFloat(sales) || 0;
                console.log(`✅ Ventas actualizadas para competidor ${compIndex} de ${productName}: ${competitorsByProduct[productName][compIndex].sales}`);
                
                // Recalcular PRM automáticamente
                calculatePRMForProduct(productName, productIndex);
            }
        }

        function toggleMaxCompetitorByIndex(productIndex, compIndex) {
            console.log(`🏆 toggleMaxCompetitorByIndex: producto ${productIndex}, competidor ${compIndex}`);
            
            if (productIndex < 0 || productIndex >= products.length) {
                console.error(`❌ Índice de producto inválido: ${productIndex}`);
                return;
            }
            
            const productName = products[productIndex].name;
            const competitors = competitorsByProduct[productName] || [];
            
            if (competitors[compIndex]) {
                // Desmarcar todos los otros competidores como máximo
                competitors.forEach((comp, idx) => {
                    comp.isMax = (idx === compIndex) ? !comp.isMax : false;
                });
                
                console.log(`✅ Competidor mayor actualizado para ${productName}:`, competitors[compIndex]);
                
                // Re-renderizar tabla y recalcular PRM
                renderCompetitorsTable();
                calculatePRMForProduct(productName, productIndex);
            }
        }

        function renderMarketHistoryTable() {
            const container = document.getElementById('market-history-container');
            if (!container) return;
            
            // Cabecera dinámica
            const headerHTML = `
                <div style="display: grid; grid-template-columns: 1fr ${products.map(() => '1fr').join(' ')} 80px; gap: 10px; padding: 10px; background: #667eea; color: white; font-weight: bold;">
                    <div>PERÍODO</div>
                    ${products.map(p => `<div>${p.name}</div>`).join('')}
                    <div>ACCIONES</div>
                </div>
            `;
            
            // Filas de datos
            const rowsHTML = marketEvolution.map((period, periodIndex) => `
                <div style="display: grid; grid-template-columns: 1fr ${products.map(() => '1fr').join(' ')} 80px; gap: 10px; padding: 10px; border-bottom: 1px solid #eee;">
                    <div>
                        <input type="text" 
                               class="enhanced-input" 
                               value="${period.period}" 
                               onchange="updatePeriodName(${periodIndex}, this.value)">
                    </div>
                    ${products.map((product, productIndex) => `
                        <div>
                            <input type="number" 
                                   class="enhanced-input" 
                                   value="${period.rates[productIndex] || 0}" 
                                   placeholder="0" 
                                   step="0.1"
                                   onchange="updateMarketRate(${periodIndex}, ${productIndex}, this.value)">
                        </div>
                    `).join('')}
                    <div>
                        <button class="enhanced-btn danger" onclick="removePeriod(${periodIndex})">🗑️</button>
                    </div>
                </div>
            `).join('');
            
            container.innerHTML = headerHTML + rowsHTML;
            updateTCMCalculations();
        }

        function renderCompetitorRows(productName, productIndex) {
            const competitors = competitorData[productName] || [];
            
            return competitors.map((competitor, compIndex) => `
                <div class="competitor-row">
                    <div>
                        <input type="text" 
                               class="enhanced-input" 
                               value="${competitor.name}" 
                               onchange="updateCompetitorName('${productName}', ${compIndex}, this.value)">
                    </div>
                    <div>
                        <input type="number" 
                               class="enhanced-input" 
                               value="${competitor.sales}" 
                               min="0" 
                               step="1000"
                               onchange="updateCompetitorSales('${productName}', ${compIndex}, this.value)">
                    </div>
                    <div>
                        <input type="radio" 
                               name="max_${productName}" 
                               ${competitor.isMax ? 'checked' : ''}
                               onchange="setMaxCompetitor('${productName}', ${compIndex})">
                    </div>
                    <div>
                        <button class="enhanced-btn danger" onclick="removeCompetitor('${productName}', ${compIndex})">🗑️</button>
                    </div>
                </div>
            `).join('');
        }

        function updateProductName(index, name) {
            if (products[index]) {
                products[index].name = name;
            }
        }

        function updateProductSales(index, sales) {
            if (products[index]) {
                products[index].sales = parseFloat(sales) || 0;
                updateSalesPercentages();
            }
        }

        function updateSalesPercentages() {
            const totalSales = products.reduce((sum, p) => sum + p.sales, 0);
            
            products.forEach(product => {
                product.percentage = totalSales > 0 ? ((product.sales / totalSales) * 100) : 0;
            });
            
            renderProductsTable();
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
                alert('Debe mantener al menos un producto');
                return;
            }
            products.splice(index, 1);
            renderProductsTable();
        }

        function updatePeriodName(periodIndex, newPeriod) {
            if (marketEvolution[periodIndex]) {
                marketEvolution[periodIndex].period = newPeriod;
            }
        }

        function updateMarketRate(periodIndex, productIndex, rate) {
            if (marketEvolution[periodIndex]) {
                if (!marketEvolution[periodIndex].rates) {
                    marketEvolution[periodIndex].rates = [];
                }
                marketEvolution[periodIndex].rates[productIndex] = parseFloat(rate) || 0;
                updateTCMCalculations();
            }
        }

        function removePeriod(periodIndex) {
            if (marketEvolution.length <= 1) {
                alert('Debe mantener al menos un período');
                return;
            }
            marketEvolution.splice(periodIndex, 1);
            renderMarketHistoryTable();
        }

        function updateTCMCalculations() {
            products.forEach((product, productIndex) => {
                const rates = marketEvolution.map(period => period.rates[productIndex] || 0);
                product.tcm = rates.length > 0 ? (rates.reduce((sum, rate) => sum + rate, 0) / rates.length) : 0;
            });
            
            const tcmContainer = document.getElementById('tcm-results');
            if (tcmContainer) {
                tcmContainer.innerHTML = `
                    <h4>📊 TCM PROMEDIO CALCULADO</h4>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 10px;">
                        ${products.map(p => `
                            <div style="padding: 8px; border-bottom: 1px solid #dee2e6;">
                                <strong>${p.name}:</strong> 
                                <span class="calculated-field">${p.tcm.toFixed(2)}%</span>
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
            
            competitorData[productName].push({
                name: `Competidor ${competitorData[productName].length + 1}`,
                sales: 0,
                isMax: false
            });
            
            const competitorsContainer = document.getElementById(`competitors-${productIndex}`);
            if (competitorsContainer) {
                competitorsContainer.innerHTML = renderCompetitorRows(productName, productIndex);
            }
        }

        function updateCompetitorName(productName, compIndex, name) {
            if (competitorData[productName] && competitorData[productName][compIndex]) {
                competitorData[productName][compIndex].name = name;
            }
        }

        function updateCompetitorSales(productName, compIndex, sales) {
            if (competitorData[productName] && competitorData[productName][compIndex]) {
                competitorData[productName][compIndex].sales = parseFloat(sales) || 0;
            }
        }

        function setMaxCompetitor(productName, compIndex) {
            if (competitorData[productName]) {
                competitorData[productName].forEach((comp, i) => {
                    comp.isMax = (i === compIndex);
                });
            }
        }

        function removeCompetitor(productName, compIndex) {
            if (competitorData[productName] && competitorData[productName].length > 1) {
                competitorData[productName].splice(compIndex, 1);
                
                const productIndex = products.findIndex(p => p.name === productName);
                if (productIndex >= 0) {
                    const competitorsContainer = document.getElementById(`competitors-${productIndex}`);
                    if (competitorsContainer) {
                        competitorsContainer.innerHTML = renderCompetitorRows(productName, productIndex);
                    }
                }
            } else {
                alert('Debe mantener al menos un competidor');
            }
        }

        function displayBCGPositioning() {
            const container = document.getElementById('bcg-positioning-table');
            if (container) {
                container.innerHTML = `
                    <h4>🎯 POSICIONAMIENTO BCG</h4>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;">
                        ${products.map(product => `
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 10px; padding: 10px; border-bottom: 1px solid #dee2e6;">
                                <div><strong>${product.name}</strong></div>
                                <div>TCM: <span class="calculated-field">${product.tcm.toFixed(2)}%</span></div>
                                <div>PRM: <span class="calculated-field">${product.prm.toFixed(2)}%</span></div>
                                <div><span class="${product.bcgPosition.toLowerCase().replace(' ', '-')}">${product.bcgPosition}</span></div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
        }

        function drawBCGMatrix() {
            const container = document.getElementById('bcg-chart');
            if (!container) return;
            
            const width = 500;
            const height = 400;
            const margin = 50;
            
            container.innerHTML = `
                <svg width="${width}" height="${height}" class="bcg-matrix-svg">
                    <!-- Cuadrantes de colores -->
                    <rect x="${margin}" y="${margin}" width="${(width-2*margin)/2}" height="${(height-2*margin)/2}" 
                          fill="#4CAF50" opacity="0.2" stroke="#4CAF50" stroke-width="2"/>
                    <rect x="${margin + (width-2*margin)/2}" y="${margin}" width="${(width-2*margin)/2}" height="${(height-2*margin)/2}" 
                          fill="#FF9800" opacity="0.2" stroke="#FF9800" stroke-width="2"/>
                    <rect x="${margin}" y="${margin + (height-2*margin)/2}" width="${(width-2*margin)/2}" height="${(height-2*margin)/2}" 
                          fill="#2196F3" opacity="0.2" stroke="#2196F3" stroke-width="2"/>
                    <rect x="${margin + (width-2*margin)/2}" y="${margin + (height-2*margin)/2}" width="${(width-2*margin)/2}" height="${(height-2*margin)/2}" 
                          fill="#9E9E9E" opacity="0.2" stroke="#9E9E9E" stroke-width="2"/>
                    
                    <!-- Etiquetas -->
                    <text x="${margin + (width-2*margin)/4}" y="${margin + 20}" text-anchor="middle" class="quadrant-label" fill="#4CAF50">ESTRELLA</text>
                    <text x="${margin + 3*(width-2*margin)/4}" y="${margin + 20}" text-anchor="middle" class="quadrant-label" fill="#FF9800">INTERROGANTE</text>
                    <text x="${margin + (width-2*margin)/4}" y="${height - margin - 10}" text-anchor="middle" class="quadrant-label" fill="#2196F3">VACA LECHERA</text>
                    <text x="${margin + 3*(width-2*margin)/4}" y="${height - margin - 10}" text-anchor="middle" class="quadrant-label" fill="#9E9E9E">PERRO</text>
                    
                    <!-- Ejes -->
                    <line x1="${margin}" y1="${margin}" x2="${margin}" y2="${height-margin}" stroke="#333" stroke-width="2"/>
                    <line x1="${margin}" y1="${height-margin}" x2="${width-margin}" y2="${height-margin}" stroke="#333" stroke-width="2"/>
                    
                    <!-- Líneas divisorias -->
                    <line x1="${margin + (width-2*margin)/2}" y1="${margin}" x2="${margin + (width-2*margin)/2}" y2="${height-margin}" stroke="#666" stroke-dasharray="5,5"/>
                    <line x1="${margin}" y1="${margin + (height-2*margin)/2}" x2="${width-margin}" y2="${margin + (height-2*margin)/2}" stroke="#666" stroke-dasharray="5,5"/>
                    
                    <!-- Productos -->
                    ${products.map(product => {
                        const maxPRM = Math.max(200, ...products.map(p => p.prm));
                        const maxTCM = Math.max(25, ...products.map(p => p.tcm));
                        
                        const x = margin + ((product.prm / maxPRM) * (width - 2*margin));
                        const y = height - margin - ((product.tcm / maxTCM) * (height - 2*margin));
                        
                        const radius = Math.max(8, Math.min(25, product.percentage));
                        const color = product.bcgPosition === 'Estrella' ? '#4CAF50' :
                                     product.bcgPosition === 'Interrogante' ? '#FF9800' :
                                     product.bcgPosition === 'Vaca Lechera' ? '#2196F3' : '#9E9E9E';
                        
                        return `
                            <circle cx="${x}" cy="${y}" r="${radius}" fill="${color}" opacity="0.8" stroke="#333" stroke-width="2">
                                <title>${product.name} - TCM: ${product.tcm.toFixed(1)}% - PRM: ${product.prm.toFixed(1)}%</title>
                            </circle>
                            <text x="${x}" y="${y+4}" text-anchor="middle" class="product-label" fill="white">
                                ${product.name.substring(0, 6)}
                            </text>
                        `;
                    }).join('')}
                </svg>
            `;
        }

        // ===== INICIALIZACIÓN =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🔥 Iniciando Análisis BCG Interactivo...');
            
            // Verificar que todas las tablas existen
            const requiredElements = [
                'sales-forecast-body', 'tcm-header', 'tcm-body', 'tcm-footer',
                'competitors-container', 'demand-header', 'demand-body',
                'prm-summary', 'bcg-positioning-summary', 'bcg-visual-matrix'
            ];
            
            const missingElements = requiredElements.filter(id => !document.getElementById(id));
            if (missingElements.length > 0) {
                console.error('❌ Elementos faltantes:', missingElements);
                return;
            }
            
            // Inicializar con datos básicos si está vacío
            if (products.length === 0) {
                console.log('📊 Inicializando con producto básico...');
                products.push({
                    id: 0,
                    name: 'Producto Inicial',
                    sales: 100,
                    percentage: 100,
                    color: productColors[0]
                });
                
                // Inicializar estructuras relacionadas
                marketGrowthData = [{
                    period: '2024-2025',
                    rates: [10.0]
                }];
                
                competitorsByProduct = {
                    'Producto Inicial': [{
                        name: 'Competidor Principal',
                        sales: 120,
                        isMax: true
                    }]
                };
                
                sectorDemandData = [{
                    year: '2024',
                    values: [500]
                }];
            }
            
            // Renderizar todas las tablas
            updateAllTables();
            calculateAllMetrics();
            
            // Exponer función de debug globalmente
            window.debugBCG = debugBCG;
            window.calculateAllMetrics = calculateAllMetrics;
            
            console.log('✅ Análisis BCG inicializado correctamente');
            console.log('💡 Usa "Cargar Datos de Ejemplo" para ver funcionalidad completa');
            console.log('🔧 Funciones de debug disponibles: debugBCG(), calculateAllMetrics()');
        });
    </script>
    
    </div> <!-- Cerrar container -->
    
    <?php include __DIR__ . '/../Users/footer.php'; ?>
    
    <script src="<?php echo getBaseUrl(); ?>/Publics/js/dashboard.js"></script>
</body>
</html>