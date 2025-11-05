// ===== BCG TEST INTERACTIVE MATRIX - JAVASCRIPT =====

// ===== VARIABLES GLOBALES =====
let products = [];                    // TABLA 1: Lista de productos con ventas
let marketGrowthData = [];           // TABLA 2: TCM por período y producto  
let competitorsByProduct = {};       // TABLA 3: Competidores por producto
let sectorDemandData = [];          // TABLA 4: Demanda global del sector
let strengths = [];                 // TABLA 5: Fortalezas de la empresa
let weaknesses = [];                // TABLA 5: Debilidades de la empresa

// Colores distintivos para productos (máximo 6)
const productColors = [
    '#fef3c7', '#d1fae5', '#dbeafe', '#f3e8ff', '#fed7d7', '#e6fffa'
];

// Métricas calculadas
let calculatedMetrics = {
    tcm: {},      // TCM por producto
    prm: {},      // PRM por producto  
    positioning: {}  // Posición BCG por producto
};

// ===== FUNCIONES PRINCIPALES =====

// Función para cargar datos existentes
function loadExistingData() {
    if (typeof EXISTING_BCG_DATA !== 'undefined' && EXISTING_BCG_DATA) {
        console.log('Cargando datos BCG existentes:', EXISTING_BCG_DATA);
        
        // Cargar productos
        if (EXISTING_BCG_DATA.products && EXISTING_BCG_DATA.products.length > 0) {
            products = [...EXISTING_BCG_DATA.products];
        }
        
        // Cargar competidores
        if (EXISTING_BCG_DATA.competitors) {
            competitorsByProduct = { ...EXISTING_BCG_DATA.competitors };
        }
        
        // Cargar períodos de mercado
        if (EXISTING_BCG_DATA.market_growth && EXISTING_BCG_DATA.market_growth.length > 0) {
            marketGrowthData = [...EXISTING_BCG_DATA.market_growth];
        }
        
        // Cargar demanda sectorial
        if (EXISTING_BCG_DATA.sector_demand && EXISTING_BCG_DATA.sector_demand.length > 0) {
            sectorDemandData = [...EXISTING_BCG_DATA.sector_demand];
        }
        
        // Cargar fortalezas
        if (EXISTING_BCG_DATA.strengths && EXISTING_BCG_DATA.strengths.length > 0) {
            strengths = [...EXISTING_BCG_DATA.strengths];
        }
        
        // Cargar debilidades
        if (EXISTING_BCG_DATA.weaknesses && EXISTING_BCG_DATA.weaknesses.length > 0) {
            weaknesses = [...EXISTING_BCG_DATA.weaknesses];
        }
        
        // Renderizar todas las tablas con los datos cargados
        renderSalesTable();
        renderMarketGrowthTable();
        renderCompetitorsTable();
        renderSectorDemandTable();
        renderStrengthsAndWeaknesses();
        
        // Recalcular métricas
        updateSalesPercentages();
        calculateAllMetrics();
        updateBCGMatrix();
        
        console.log('Datos BCG cargados correctamente');
    } else {
        console.log('No hay datos BCG existentes, iniciando con datos vacíos');
    }
}

function addProduct() {
    const productIndex = products.length;
    const productName = `Producto ${productIndex + 1}`;
    
    products.push({
        name: productName,
        sales: 0,
        percentage: 0
    });
    
    // Inicializar competidores para el nuevo producto
    competitorsByProduct[productName] = [
        { name: 'Competidor A', sales: 0, isMax: false },
        { name: 'Competidor B', sales: 0, isMax: false }
    ];
    
    // Regenerar todas las tablas
    renderSalesTable();
    renderMarketGrowthTable();
    renderCompetitorsTable();
    renderSectorDemandTable();
    
    updateSalesPercentages();
    calculateAllMetrics();
    updateBCGMatrix();
    
    showAlert(`✅ ${productName} agregado correctamente`, 'success');
}

function loadExampleData() {
    // Resetear datos
    products = [];
    marketGrowthData = [];
    competitorsByProduct = {};
    sectorDemandData = [];
    
    // Productos de ejemplo
    products = [
        { name: 'Smartphone Pro', sales: 15000, percentage: 0 },
        { name: 'Laptop Gaming', sales: 8500, percentage: 0 },
        { name: 'Tablet Ultra', sales: 5200, percentage: 0 }
    ];
    
    // TCM de ejemplo (empezar con año actual y hacia atrás)
    const currentYear = new Date().getFullYear();
    marketGrowthData = [
        { period: `${currentYear}-${currentYear + 1}`, rates: [15.5, 8.2, 12.1] },
        { period: `${currentYear - 1}-${currentYear}`, rates: [18.3, 10.5, 14.7] }
    ];
    
    // Competidores de ejemplo
    competitorsByProduct = {
        'Smartphone Pro': [
            { name: 'Apple iPhone', sales: 25000, isMax: true },
            { name: 'Samsung Galaxy', sales: 22000, isMax: false },
            { name: 'Xiaomi Mi', sales: 18000, isMax: false }
        ],
        'Laptop Gaming': [
            { name: 'ASUS ROG', sales: 12000, isMax: true },
            { name: 'MSI Gaming', sales: 10500, isMax: false }
        ],
        'Tablet Ultra': [
            { name: 'iPad Pro', sales: 15000, isMax: true },
            { name: 'Surface Pro', sales: 8500, isMax: false }
        ]
    };
    
    // Demanda del sector de ejemplo (sincronizada con años TCM)
    sectorDemandData = [
        { year: currentYear - 1, productDemands: [12.5, 8.3, 15.7] },
        { year: currentYear, productDemands: [18.2, 12.1, 22.4] }
    ];
    
    // Fortalezas y debilidades de ejemplo
    strengths = [
        { id: 1, text: 'Marca reconocida en el mercado' },
        { id: 2, text: 'Equipo técnico altamente capacitado' },
        { id: 3, text: 'Red de distribución consolidada' }
    ];
    
    weaknesses = [
        { id: 4, text: 'Altos costos de producción' },
        { id: 5, text: 'Dependencia de proveedores externos' },
        { id: 6, text: 'Limitada presencia digital' }
    ];
    
    // Regenerar todas las tablas
    renderSalesTable();
    renderMarketGrowthTable();  
    renderCompetitorsTable();
    renderSectorDemandTable();
    renderStrengthsAndWeaknesses();
    
    updateSalesPercentages();
    calculateAllMetrics();
    updateBCGMatrix();
    
    showAlert('📊 Datos de ejemplo cargados correctamente', 'success');
}

function renderSalesTable() {
    const container = document.getElementById('sales-table-container');
    if (!container || products.length === 0) return;
    
    let tableHTML = `
        <div class="dynamic-table-container">
            <table class="excel-table dynamic-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>PRODUCTO/SERVICIO</th>
                        <th>PREVISIÓN VENTAS (miles S/)</th>
                        <th class="calculated-cell">% S/VTAS</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    products.forEach((product, index) => {
        tableHTML += `
            <tr class="product-color-${index % 6}">
                <td><strong>${index + 1}</strong></td>
                <td>
                    <input type="text" class="excel-input" 
                           value="${product.name}" 
                           onchange="updateProductName(${index}, this.value)"
                           placeholder="Nombre del producto">
                </td>
                <td>
                    <input type="number" class="excel-input" 
                           value="${product.sales}" 
                           onchange="updateProductSales(${index}, parseFloat(this.value) || 0)"
                           placeholder="0" step="0.01" min="0">
                </td>
                <td class="calculated-cell">
                    <span id="percentage-${index}">${product.percentage.toFixed(1)}%</span>
                </td>
                <td>
                    <button class="excel-btn danger" onclick="removeProduct(${index})" 
                            ${products.length <= 1 ? 'disabled' : ''}>
                        🗑️ Eliminar
                    </button>
                </td>
            </tr>
        `;
    });
    
    // Fila de totales
    const totalSales = products.reduce((sum, product) => sum + product.sales, 0);
    tableHTML += `
                    <tr class="total-cell">
                        <td colspan="2"><strong>TOTAL</strong></td>
                        <td><strong id="total-sales">${totalSales.toLocaleString()}</strong></td>
                        <td><strong>100.0%</strong></td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="text-align: center; margin: 15px 0;">
            <button class="excel-btn success" onclick="addProduct()">
                ➕ Agregar Producto
            </button>
        </div>
    `;
    
    container.innerHTML = tableHTML;
}

function renderMarketGrowthTable() {
    const container = document.getElementById('market-growth-table-container');
    if (!container || products.length === 0) return;
    
    let tableHTML = `
        <div class="dynamic-table-container">
            <table class="excel-table dynamic-table">
                <thead>
                    <tr>
                        <th>PERÍODO</th>
    `;
    
    // Columnas por producto
    products.forEach((product, index) => {
        tableHTML += `<th class="product-color-${index % 6}">${product.name}</th>`;
    });
    
    tableHTML += `
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    // Filas de períodos
    marketGrowthData.forEach((periodData, periodIndex) => {
        tableHTML += `
            <tr>
                <td>
                    <input type="text" class="excel-input" 
                           value="${periodData.period}" 
                           onchange="updatePeriodName(${periodIndex}, this.value)"
                           placeholder="Ej: 2023-2024">
                </td>
        `;
        
        // Tasas por producto
        products.forEach((product, productIndex) => {
            const rate = periodData.rates[productIndex] || 0;
            tableHTML += `
                <td class="product-color-${productIndex % 6}">
                    <input type="number" class="excel-input" 
                           value="${rate}" 
                           onchange="updateMarketRate(${periodIndex}, ${productIndex}, parseFloat(this.value) || 0)"
                           placeholder="0.0" step="0.1" min="0" max="100">%
                </td>
            `;
        });
        
        tableHTML += `
                <td>
                    <button class="excel-btn danger" onclick="removePeriod(${periodIndex})"
                            ${marketGrowthData.length <= 1 ? 'disabled' : ''}>
                        🗑️
                    </button>
                </td>
            </tr>
        `;
    });
    
    // Fila de promedio TCM
    tableHTML += `
                    <tr class="calculated-cell">
                        <td><strong>TCM PROMEDIO</strong></td>
    `;
    
    products.forEach((product, productIndex) => {
        const avgTCM = calculateAverageTCM(productIndex);
        tableHTML += `
            <td class="calculated-cell">
                <strong id="tcm-${productIndex}">${avgTCM.toFixed(2)}%</strong>
            </td>
        `;
    });
    
    tableHTML += `
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="text-align: center; margin: 15px 0;">
            <button class="excel-btn success" onclick="addMarketPeriod()">
                ➕ Agregar Período
            </button>
        </div>
        
        <!-- TABLA RESUMEN BCG -->
        <div style="margin-top: 30px;">
            <h3 style="color: #1e293b; margin-bottom: 15px;">📊 RESUMEN MÉTRICAS BCG</h3>
            <table class="excel-table">
                <thead>
                    <tr>
                        <th>BCG</th>
    `;
    
    // Columnas por producto
    products.forEach((product, index) => {
        tableHTML += `<th class="product-color-${index % 6}">${product.name}</th>`;
    });
    
    tableHTML += `
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>TCM</strong></td>
    `;
    
    // Fila TCM
    products.forEach((product, productIndex) => {
        const avgTCM = calculateAverageTCM(productIndex);
        tableHTML += `
            <td class="calculated-cell product-color-${productIndex % 6}">
                <strong id="summary-tcm-${productIndex}">${avgTCM.toFixed(2)}%</strong>
            </td>
        `;
    });
    
    tableHTML += `
                    </tr>
                    <tr>
                        <td><strong>PRM</strong></td>
    `;
    
    // Fila PRM
    products.forEach((product, productIndex) => {
        const prm = calculatePRMByIndex(productIndex);
        tableHTML += `
            <td class="calculated-cell product-color-${productIndex % 6}">
                <strong id="summary-prm-${productIndex}">${prm.toFixed(2)}</strong>
            </td>
        `;
    });
    
    tableHTML += `
                    </tr>
                    <tr>
                        <td><strong>% S/VTAS</strong></td>
    `;
    
    // Fila % S/VTAS (porcentaje sobre ventas totales)
    products.forEach((product, productIndex) => {
        tableHTML += `
            <td class="calculated-cell product-color-${productIndex % 6}">
                <strong id="summary-sales-${productIndex}">${product.percentage.toFixed(1)}%</strong>
            </td>
        `;
    });
    
    tableHTML += `
                    </tr>
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = tableHTML;
}

function renderCompetitorsTable() {
    const container = document.getElementById('competitors-table-container');
    if (!container || products.length === 0) return;
    
    let tableHTML = `<div class="competitors-grid">`;
    
    products.forEach((product, productIndex) => {
        const productName = product.name;
        const competitors = competitorsByProduct[productName] || [];
        
        tableHTML += `
            <div class="competitor-mini-table">
                <div class="mini-table-header">
                    ${productName}
                </div>
                <div class="mini-table-empresa">
                    <span>NUESTRA EMPRESA</span>
                    <span><strong>${product.sales.toLocaleString()}</strong></span>
                </div>
                <div class="mini-table-body">
        `;
        
        // Detectar automáticamente el competidor con mayores ventas
        let maxSales = 0;
        let maxCompetitorIndex = -1;
        competitors.forEach((competitor, index) => {
            if (competitor.sales > maxSales) {
                maxSales = competitor.sales;
                maxCompetitorIndex = index;
            }
        });
        
        // Filas de competidores
        competitors.forEach((competitor, compIndex) => {
            const isMax = compIndex === maxCompetitorIndex && competitor.sales > 0;
            tableHTML += `
                <div class="mini-competitor-row ${isMax ? 'calculated-cell' : ''}">
                    <div class="mini-competitor-name">
                        <input type="text" class="excel-input" 
                               value="${competitor.name}" 
                               onchange="updateCompetitorName('${productName}', ${compIndex}, this.value)"
                               placeholder="Nombre competidor">
                    </div>
                    <div class="mini-competitor-sales">
                        <input type="number" class="excel-input" 
                               value="${competitor.sales}" 
                               onchange="updateCompetitorSales('${productName}', ${compIndex}, parseFloat(this.value) || 0)"
                               placeholder="0" step="0.01" min="0">
                    </div>
                    <div class="mini-competitor-actions">
                        ${isMax ? '👑 MAYOR' : ''}
                        <button class="excel-btn danger" onclick="removeCompetitor('${productName}', ${compIndex})" 
                                ${competitors.length <= 1 ? 'disabled' : ''} title="Eliminar">🗑️</button>
                    </div>
                </div>
            `;
        });
        
        tableHTML += `
                </div>
                <div class="mini-table-mayor">
                    💰 MAYOR COMPETIDOR: ${maxSales.toLocaleString()} (Auto-detectado)
                </div>
                <div class="mini-table-controls">
                    <button class="excel-btn success" onclick="addCompetitor('${productName}', ${productIndex})">
                        ➕ Competidor
                    </button>
                </div>
            </div>
        `;
    });
    
    tableHTML += `</div>`;
    container.innerHTML = tableHTML;
}

function renderSectorDemandTable() {
    const container = document.getElementById('sector-demand-table-container');
    if (!container || products.length === 0) return;
    
    // Extraer años ÚNICOS de los períodos TCM (solo años iniciales)
    const tcmYears = new Set();
    marketGrowthData.forEach(period => {
        const match = period.period.match(/(\d{4})-(\d{4})/);
        if (match) {
            const startYear = parseInt(match[1]);
            tcmYears.add(startYear);
        }
    });
    
    // Convertir a array y ordenar de menor a mayor
    let years = Array.from(tcmYears).sort((a, b) => a - b);
    
    // Si no hay períodos TCM, usar años por defecto
    if (years.length === 0) {
        const currentYear = new Date().getFullYear();
        years = [currentYear - 2, currentYear - 1, currentYear];
    }
    
    // Sincronizar sectorDemandData con los años de TCM
    const newSectorDemandData = [];
    years.forEach(year => {
        // Buscar si ya existe data para este año
        const existingData = sectorDemandData.find(item => item.year === year);
        
        if (existingData) {
            // Mantener datos existentes, pero ajustar la cantidad de productos
            while (existingData.productDemands.length < products.length) {
                existingData.productDemands.push(0);
            }
            newSectorDemandData.push(existingData);
        } else {
            // Crear nueva entrada para este año
            newSectorDemandData.push({
                year: year,
                productDemands: new Array(products.length).fill(0)
            });
        }
    });
    
    // Actualizar la data global
    sectorDemandData = newSectorDemandData;
    
    let tableHTML = `
        <div class="dynamic-table-container">
            <h3 style="margin-bottom: 15px;">EVOLUCIÓN DE LA DEMANDA GLOBAL SECTOR (en miles de soles)</h3>
            <table class="excel-table">
                <thead>
                    <tr>
                        <th>AÑOS</th>
    `;
    
    // Columnas por producto (MERCADOS)
    products.forEach((product, index) => {
        tableHTML += `<th class="product-color-${index % 6}">${product.name}</th>`;
    });
    
    tableHTML += `
                    </tr>
                </thead>
                <tbody>
    `;
    
    // Filas por año
    years.forEach((year, yearIndex) => {
        tableHTML += `
            <tr>
                <td><strong>${year}</strong></td>
        `;
        
        // Demandas por producto para este año
        products.forEach((product, productIndex) => {
            const demandData = sectorDemandData.find(d => d.year === year);
            const currentValue = demandData && demandData.productDemands ? 
                (demandData.productDemands[productIndex] || 0) : 0;
            
            tableHTML += `
                <td class="product-color-${productIndex % 6}">
                    <input type="number" class="excel-input" 
                           value="${currentValue}" 
                           onchange="updateSectorDemand(${year}, ${productIndex}, parseFloat(this.value) || 0)"
                           placeholder="0.0" step="0.1" min="0" max="500">%
                </td>
            `;
        });
        
        tableHTML += `
            </tr>
        `;
    });
    
    tableHTML += `
                </tbody>
            </table>
        </div>
        <p style="color: #6b7280; margin-top: 10px; font-size: 14px;">
            📝 Los años se generan automáticamente basándose en los períodos del paso 2 (TCM). 
            Ingrese los porcentajes de crecimiento de la demanda del sector para cada producto por año.
        </p>
    `;
    
    container.innerHTML = tableHTML;
}

function calculateAverageTCM(productIndex) {
    if (marketGrowthData.length === 0) return 0;
    
    const rates = marketGrowthData.map(period => period.rates[productIndex] || 0);
    const sum = rates.reduce((acc, rate) => acc + rate, 0);
    return rates.length > 0 ? sum / rates.length : 0;
}

function calculatePRM(productName) {
    const product = products.find(p => p.name === productName);
    const competitors = competitorsByProduct[productName] || [];
    const maxCompetitor = competitors.find(comp => comp.isMax);
    
    if (!product || !maxCompetitor || maxCompetitor.sales === 0) return 0;
    
    return product.sales / maxCompetitor.sales;
}

function calculateBCGPositioning(productName) {
    const productIndex = products.findIndex(p => p.name === productName);
    const tcm = calculateAverageTCM(productIndex);
    const prm = calculatePRM(productName);
    
    let position = '';
    let color = '';
    
    if (tcm >= 10 && prm >= 1) {
        position = 'Estrella ⭐';
        color = '#4CAF50';
    } else if (tcm >= 10 && prm < 1) {
        position = 'Interrogante ❓';
        color = '#FF9800';
    } else if (tcm < 10 && prm >= 1) {
        position = 'Vaca Lechera 🐄';
        color = '#2196F3';
    } else {
        position = 'Perro 🐕';
        color = '#9E9E9E';
    }
    
    return { position, color, tcm, prm };
}

function calculateAllMetrics() {
    products.forEach((product, index) => {
        calculatedMetrics.tcm[product.name] = calculateAverageTCM(index);
        calculatedMetrics.prm[product.name] = calculatePRM(product.name);
        calculatedMetrics.positioning[product.name] = calculateBCGPositioning(product.name);
    });
}

function updateBCGMatrix() {
    calculateAllMetrics();
    renderBCGVisualization();
    displayBCGPositioning();
}

function renderBCGVisualization() {
    const container = document.getElementById('bcg-matrix-container');
    if (!container || products.length === 0) return;
    
    container.innerHTML = `
        <div class="bcg-chart">
            <svg class="bcg-matrix-svg" viewBox="0 0 400 300">
                <!-- Ejes -->
                <line x1="50" y1="250" x2="350" y2="250" stroke="#333" stroke-width="2"/>
                <line x1="50" y1="250" x2="50" y2="50" stroke="#333" stroke-width="2"/>
                
                <!-- Líneas de división -->
                <line x1="200" y1="250" x2="200" y2="50" stroke="#ccc" stroke-width="1" stroke-dasharray="5,5"/>
                <line x1="50" y1="150" x2="350" y2="150" stroke="#ccc" stroke-width="1" stroke-dasharray="5,5"/>
                
                <!-- Etiquetas de cuadrantes -->
                <text x="125" y="100" text-anchor="middle" class="quadrant-label" fill="#FF9800">INTERROGANTE</text>
                <text x="275" y="100" text-anchor="middle" class="quadrant-label" fill="#4CAF50">ESTRELLA</text>
                <text x="125" y="200" text-anchor="middle" class="quadrant-label" fill="#9E9E9E">PERRO</text>
                <text x="275" y="200" text-anchor="middle" class="quadrant-label" fill="#2196F3">VACA LECHERA</text>
                
                <!-- Etiquetas de ejes -->
                <text x="200" y="280" text-anchor="middle" class="axis-label">PRM (Participación Relativa del Mercado)</text>
                <text x="20" y="150" text-anchor="middle" class="axis-label" transform="rotate(-90 20 150)">TCM (%)</text>
                
                <!-- Productos -->
                ${products.map((product, index) => {
                    const positioning = calculatedMetrics.positioning[product.name] || {};
                    const tcm = positioning.tcm || 0;
                    const prm = positioning.prm || 0;
                    
                    // Convertir TCM y PRM a coordenadas del gráfico
                    const xPos = 50 + (Math.min(prm, 3) / 3) * 300; // PRM máximo 3 para visualización
                    const yPos = 250 - (Math.min(tcm, 30) / 30) * 200; // TCM máximo 30% para visualización
                    
                    return `
                        <circle cx="${xPos}" cy="${yPos}" r="8" 
                                fill="${positioning.color || '#666'}" 
                                stroke="#fff" stroke-width="2"
                                opacity="0.8">
                            <title>Producto: ${product.name}
TCM: ${tcm.toFixed(2)}%
PRM: ${prm.toFixed(3)}
Ventas: ${product.percentage.toFixed(1)}%
Posición: ${positioning.position || 'Perro 🐕'}</title>
                        </circle>
                        <text x="${xPos}" y="${yPos + 4}" text-anchor="middle" 
                              class="product-label" fill="white" font-weight="bold">
                            ${index + 1}
                        </text>
                    `;
                }).join('')}
            </svg>
            
            <div style="margin-top: 20px; text-align: center; color: #6b7280; font-size: 14px;">
                <strong>Nota:</strong> Los números en los círculos corresponden al orden de productos en la tabla de ventas.
            </div>
        `;
}

function showAlert(message, type = 'info') {
    // Crear elemento de alerta
    const alertDiv = document.createElement('div');
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        padding: 15px 25px;
        border-radius: 8px;
        color: white;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    `;
    
    // Colores según el tipo
    const colors = {
        success: '#059669',
        error: '#dc2626',
        warning: '#d97706',
        info: '#0284c7'
    };
    
    alertDiv.style.background = colors[type] || colors.info;
    alertDiv.textContent = message;
    
    document.body.appendChild(alertDiv);
    
    // Eliminar después de 3 segundos
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Función de depuración global
function debugBCG() {
    console.log('🔍 DEBUG BCG - Estado actual:');
    console.log('📊 Productos:', products);
    console.log('📈 TCM Data:', marketGrowthData);
    console.log('🏢 Competidores:', competitorsByProduct);
    console.log('🌍 Demanda Sector:', sectorDemandData);
    console.log('🧮 Métricas:', calculatedMetrics);
    
    // Mostrar resumen de posiciones BCG
    console.log('🎯 Posiciones BCG actuales:');
    products.forEach(product => {
        const pos = calculatedMetrics.positioning[product.name];
        if (pos) {
            console.log(`   ${product.name}: ${pos.position} (TCM: ${pos.tcm.toFixed(2)}%, PRM: ${pos.prm.toFixed(3)})`);
        }
    });
    
    return 'Debug completo - revisa la consola del navegador';
}

// ===== FUNCIONES BASADAS EN ÍNDICES PARA COMPETIDORES =====

function addCompetitorByIndex(productIndex) {
    const product = products[productIndex];
    if (!product) return;
    
    const productName = product.name;
    if (!competitorsByProduct[productName]) {
        competitorsByProduct[productName] = [];
    }
    
    const compIndex = competitorsByProduct[productName].length;
    competitorsByProduct[productName].push({
        name: `Competidor ${compIndex + 1}`,
        sales: 0,
        isMax: false
    });
    
    renderCompetitorsTable();
    calculateAllMetrics();
    updateBCGMatrix();
}

function calculatePRMByIndex(productIndex) {
    const product = products[productIndex];
    if (!product) return 0;
    
    const productName = product.name;
    const competitors = competitorsByProduct[productName] || [];
    
    // Encontrar el competidor con mayores ventas automáticamente
    let maxCompetitorSales = 0;
    competitors.forEach(comp => {
        if (comp.sales > maxCompetitorSales) {
            maxCompetitorSales = comp.sales;
        }
    });
    
    // Aplicar la fórmula de Excel: =SI(C57=0;0;SI(D13/C57>2;2;D13/C57))
    // C57 = mayor competidor, D13 = ventas del producto
    if (maxCompetitorSales === 0) return 0;
    
    const ratio = product.sales / maxCompetitorSales;
    return ratio > 2 ? 2 : ratio;
}

function updateCompetitorNameByIndex(productIndex, compIndex, newName) {
    const product = products[productIndex];
    if (!product) return;
    
    const productName = product.name;
    const competitors = competitorsByProduct[productName];
    
    if (competitors && competitors[compIndex]) {
        competitors[compIndex].name = newName;
    }
}

function updateCompetitorSalesByIndex(productIndex, compIndex, sales) {
    const product = products[productIndex];
    if (!product) return;
    
    const productName = product.name;
    const competitors = competitorsByProduct[productName];
    
    if (competitors && competitors[compIndex]) {
        competitors[compIndex].sales = sales;
        calculateAllMetrics();
        updateBCGMatrix();
    }
}

function toggleMaxCompetitorByIndex(productIndex, compIndex) {
    const product = products[productIndex];
    if (!product) return;
    
    const productName = product.name;
    const competitors = competitorsByProduct[productName];
    
    if (competitors) {
        // Desmarcar todos
        competitors.forEach(comp => comp.isMax = false);
        // Marcar el seleccionado
        if (competitors[compIndex]) {
            competitors[compIndex].isMax = true;
        }
        
        renderCompetitorsTable();
        calculateAllMetrics();
        updateBCGMatrix();
    }
}

function renderMarketHistoryTable() {
    // Implementación para tabla de historia del mercado si es necesaria
    console.log('renderMarketHistoryTable called');
}

function renderCompetitorRows(productName, productIndex) {
    // Función auxiliar para renderizar filas de competidores
    const competitors = competitorsByProduct[productName] || [];
    return competitors.map((competitor, compIndex) => {
        return `
            <div class="mini-competitor-row ${competitor.isMax ? 'calculated-cell' : ''}">
                <input type="text" value="${competitor.name}" 
                       onchange="updateCompetitorNameByIndex(${productIndex}, ${compIndex}, this.value)">
                <input type="number" value="${competitor.sales}" 
                       onchange="updateCompetitorSalesByIndex(${productIndex}, ${compIndex}, parseFloat(this.value) || 0)">
                <button onclick="toggleMaxCompetitorByIndex(${productIndex}, ${compIndex})">
                    ${competitor.isMax ? '👑' : '📈'}
                </button>
            </div>
        `;
    }).join('');
}

function updateProductName(index, name) {
    if (products[index]) {
        products[index].name = name;
        renderCompetitorsTable(); // Actualizar tabla de competidores
    }
}

function updateProductSales(index, sales) {
    if (products[index]) {
        products[index].sales = sales;
        updateSalesPercentages();
        calculateAllMetrics();
        updateBCGMatrix();
    }
}

function updateSalesPercentages() {
    const totalSales = products.reduce((sum, product) => sum + product.sales, 0);
    
    products.forEach((product, index) => {
        product.percentage = totalSales > 0 ? (product.sales / totalSales) * 100 : 0;
        
        const percentageElement = document.getElementById(`percentage-${index}`);
        if (percentageElement) {
            percentageElement.textContent = `${product.percentage.toFixed(1)}%`;
        }
    });
    
    updateTotalSales();
}

function updateTotalSales() {
    const totalSales = products.reduce((sum, product) => sum + product.sales, 0);
    const totalElement = document.getElementById('total-sales');
    if (totalElement) {
        totalElement.textContent = totalSales.toLocaleString();
    }
}

function removeProduct(index) {
    if (products.length <= 1) return;
    
    const productName = products[index].name;
    delete competitorsByProduct[productName];
    products.splice(index, 1);
    
    renderSalesTable();
    renderMarketGrowthTable();
    renderCompetitorsTable();
    
    updateSalesPercentages();
    calculateAllMetrics();
    updateBCGMatrix();
}

function updatePeriodName(periodIndex, newPeriod) {
    if (marketGrowthData[periodIndex]) {
        marketGrowthData[periodIndex].period = newPeriod;
    }
}

function updateMarketRate(periodIndex, productIndex, rate) {
    if (!marketGrowthData[periodIndex]) {
        marketGrowthData[periodIndex] = { period: '', rates: [] };
    }
    
    marketGrowthData[periodIndex].rates[productIndex] = rate;
    updateTCMCalculations();
}

function removePeriod(periodIndex) {
    if (marketGrowthData.length <= 1) {
        showAlert('⚠️ Debe mantener al menos un período', 'warning');
        return;
    }
    
    const removedPeriod = marketGrowthData[periodIndex];
    marketGrowthData.splice(periodIndex, 1);
    
    renderMarketGrowthTable();
    renderSectorDemandTable(); // Actualizar también la demanda sectorial
    updateTCMCalculations();
    
    showAlert(`✅ Período ${removedPeriod.period} eliminado`, 'success');
}

function updateTCMCalculations() {
    products.forEach((product, index) => {
        const avgTCM = calculateAverageTCM(index);
        const tcmElement = document.getElementById(`tcm-${index}`);
        if (tcmElement) {
            tcmElement.textContent = `${avgTCM.toFixed(2)}%`;
        }
    });
    
    calculateAllMetrics();
    updateBCGMatrix();
}

function addCompetitor(productName, productIndex) {
    if (!competitorsByProduct[productName]) {
        competitorsByProduct[productName] = [];
    }
    
    const compIndex = competitorsByProduct[productName].length;
    competitorsByProduct[productName].push({
        name: `Competidor ${compIndex + 1}`,
        sales: 0,
        isMax: false
    });
    
    renderCompetitorsTable();
}

function updateCompetitorName(productName, compIndex, name) {
    if (competitorsByProduct[productName] && competitorsByProduct[productName][compIndex]) {
        competitorsByProduct[productName][compIndex].name = name;
    }
}

function updateCompetitorSales(productName, compIndex, sales) {
    if (competitorsByProduct[productName] && competitorsByProduct[productName][compIndex]) {
        competitorsByProduct[productName][compIndex].sales = parseFloat(sales) || 0;
        // Regenerar la tabla de competidores para actualizar el mayor competidor automáticamente
        renderCompetitorsTable();
        calculateAllMetrics();
        updateBCGMatrix();
    }
}

function setMaxCompetitor(productName, compIndex) {
    const competitors = competitorsByProduct[productName];
    if (competitors) {
        competitors.forEach(comp => comp.isMax = false);
        if (competitors[compIndex]) {
            competitors[compIndex].isMax = true;
        }
        renderCompetitorsTable();
        calculateAllMetrics();
        updateBCGMatrix();
    }
}

function removeCompetitor(productName, compIndex) {
    const competitors = competitorsByProduct[productName];
    if (competitors && competitors.length > 1) {
        competitors.splice(compIndex, 1);
        renderCompetitorsTable();
        calculateAllMetrics();
        updateBCGMatrix();
    }
}

function displayBCGPositioning() {
    console.log('Mostrando posicionamiento BCG...');
    products.forEach(product => {
        const pos = calculatedMetrics.positioning[product.name];
        if (pos) {
            console.log(`${product.name}: ${pos.position}`);
        }
    });
}

function drawBCGMatrix() {
    updateBCGMatrix();
}

// Funciones adicionales para completar funcionalidad

function addMarketPeriod() {
    const currentYear = new Date().getFullYear(); // 2025
    let newPeriodYear;
    
    if (marketGrowthData.length === 0) {
        // Primer período: empezar con el año actual
        newPeriodYear = `${currentYear}-${currentYear + 1}`; // 2025-2026
    } else {
        // Encontrar el año inicial más bajo para generar el período anterior
        let minStartYear = currentYear + 10; // Valor alto para encontrar el mínimo
        
        // Buscar el año inicial más bajo entre todos los períodos
        marketGrowthData.forEach(period => {
            const yearMatch = period.period.match(/(\d{4})-(\d{4})/);
            if (yearMatch) {
                const startYear = parseInt(yearMatch[1]);
                if (startYear < minStartYear) {
                    minStartYear = startYear;
                }
            }
        });
        
        // Si no encontramos años válidos, usar año actual como base
        if (minStartYear > currentYear + 5) {
            minStartYear = currentYear;
        }
        
        // Generar el período anterior (hacia atrás en el tiempo)
        const prevStartYear = minStartYear - 1;
        const prevEndYear = minStartYear;
        newPeriodYear = `${prevStartYear}-${prevEndYear}`;
        
        // Verificar que no sea duplicado
        const existsPeriod = marketGrowthData.some(period => period.period === newPeriodYear);
        if (existsPeriod) {
            showAlert(`⚠️ El período ${newPeriodYear} ya existe`, 'warning');
            return;
        }
        
        // Verificar que no vayamos muy atrás en el tiempo (límite: 2000)
        if (prevStartYear < 2000) {
            showAlert(`⚠️ No se pueden agregar más períodos históricos (límite: año 2000)`, 'warning');
            return;
        }
    }
    
    marketGrowthData.push({
        period: newPeriodYear,
        rates: new Array(products.length).fill(0)
    });
    
    // Ordenar períodos de más reciente a más antiguo
    marketGrowthData.sort((a, b) => {
        const yearA = parseInt(a.period.split('-')[0]);
        const yearB = parseInt(b.period.split('-')[0]);
        return yearB - yearA; // Orden descendente (más reciente primero)
    });
    
    renderMarketGrowthTable();
    renderSectorDemandTable(); // Actualizar también la demanda sectorial
    showAlert(`✅ Período histórico ${newPeriodYear} agregado`, 'success');
}

// Funciones obsoletas eliminadas - Los años ahora se generan automáticamente desde el paso 2

function updateSectorDemand(year, productIndex, demand) {
    let yearData = sectorDemandData.find(d => d.year === year);
    if (!yearData) {
        yearData = {
            year: year,
            productDemands: new Array(products.length).fill(0)
        };
        sectorDemandData.push(yearData);
    }
    
    if (!yearData.productDemands) {
        yearData.productDemands = new Array(products.length).fill(0);
    }
    
    yearData.productDemands[productIndex] = demand;
}

// ===== FUNCIONES FORTALEZAS Y DEBILIDADES =====

function addStrength() {
    strengths.push({
        id: Date.now(),
        text: ''
    });
    renderStrengthsAndWeaknesses();
    
    // Enfocar el nuevo campo
    setTimeout(() => {
        const newInputs = document.querySelectorAll('#strengths-container .excel-input');
        const lastInput = newInputs[newInputs.length - 1];
        if (lastInput) lastInput.focus();
    }, 100);
}

function addWeakness() {
    weaknesses.push({
        id: Date.now(),
        text: ''
    });
    renderStrengthsAndWeaknesses();
    
    // Enfocar el nuevo campo
    setTimeout(() => {
        const newInputs = document.querySelectorAll('#weaknesses-container .excel-input');
        const lastInput = newInputs[newInputs.length - 1];
        if (lastInput) lastInput.focus();
    }, 100);
}

function updateStrength(id, text) {
    const strength = strengths.find(s => s.id === id);
    if (strength) {
        strength.text = text;
    }
}

function updateWeakness(id, text) {
    const weakness = weaknesses.find(w => w.id === id);
    if (weakness) {
        weakness.text = text;
    }
}

function removeStrength(id) {
    const index = strengths.findIndex(s => s.id === id);
    if (index !== -1) {
        strengths.splice(index, 1);
        renderStrengthsAndWeaknesses();
    }
}

function removeWeakness(id) {
    const index = weaknesses.findIndex(w => w.id === id);
    if (index !== -1) {
        weaknesses.splice(index, 1);
        renderStrengthsAndWeaknesses();
    }
}

function renderStrengthsAndWeaknesses() {
    renderStrengths();
    renderWeaknesses();
}

function renderStrengths() {
    const container = document.getElementById('strengths-container');
    if (!container) return;
    
    let html = '';
    
    if (strengths.length === 0) {
        html = `
            <div style="text-align: center; padding: 20px; background: #f0fdf4; border-radius: 8px; color: #166534;">
                <p>👆 Haz clic en "Agregar Fortaleza" para comenzar</p>
            </div>
        `;
    } else {
        strengths.forEach((strength, index) => {
            html += `
                <div style="display: flex; align-items: center; margin-bottom: 10px; background: white; padding: 10px; border-radius: 6px; border: 2px solid #10b981;">
                    <span style="margin-right: 10px; color: #059669; font-weight: bold;">${index + 1}.</span>
                    <input type="text" 
                           class="excel-input" 
                           value="${strength.text}" 
                           onchange="updateStrength(${strength.id}, this.value)"
                           placeholder="Describa una fortaleza de su empresa..."
                           style="flex: 1; border-color: #10b981;">
                    <button class="excel-btn danger" 
                            onclick="removeStrength(${strength.id})" 
                            style="margin-left: 10px; padding: 6px 10px;">
                        🗑️
                    </button>
                </div>
            `;
        });
    }
    
    container.innerHTML = html;
}

function renderWeaknesses() {
    const container = document.getElementById('weaknesses-container');
    if (!container) return;
    
    let html = '';
    
    if (weaknesses.length === 0) {
        html = `
            <div style="text-align: center; padding: 20px; background: #fef2f2; border-radius: 8px; color: #991b1b;">
                <p>👆 Haz clic en "Agregar Debilidad" para comenzar</p>
            </div>
        `;
    } else {
        weaknesses.forEach((weakness, index) => {
            html += `
                <div style="display: flex; align-items: center; margin-bottom: 10px; background: white; padding: 10px; border-radius: 6px; border: 2px solid #ef4444;">
                    <span style="margin-right: 10px; color: #dc2626; font-weight: bold;">${index + 1}.</span>
                    <input type="text" 
                           class="excel-input" 
                           value="${weakness.text}" 
                           onchange="updateWeakness(${weakness.id}, this.value)"
                           placeholder="Describa una debilidad de su empresa..."
                           style="flex: 1; border-color: #ef4444;">
                    <button class="excel-btn danger" 
                            onclick="removeWeakness(${weakness.id})" 
                            style="margin-left: 10px; padding: 6px 10px;">
                        🗑️
                    </button>
                </div>
            `;
        });
    }
    
    container.innerHTML = html;
}

// ===== FUNCIONES DE GUARDADO Y EXPORTACIÓN =====

function saveBCGData() {
    // Verificar que tenemos el PROJECT_ID
    if (typeof PROJECT_ID === 'undefined' || !PROJECT_ID) {
        showAlert('❌ Error: ID de proyecto no disponible', 'error');
        return;
    }
    
    // Recopilar todos los datos
    const bcgData = {
        project_id: PROJECT_ID,
        products: products,
        market_growth: marketGrowthData,
        competitors: competitorsByProduct,
        sector_demand: sectorDemandData,
        strengths: strengths,
        weaknesses: weaknesses,
        timestamp: new Date().toISOString()
    };
    
    console.log('💾 Guardando datos BCG:', bcgData);
    console.log('🔍 DEBUG - Products:', products);
    console.log('🔍 DEBUG - Market Growth:', marketGrowthData);
    console.log('🔍 DEBUG - Competitors:', competitorsByProduct);
    console.log('🔍 DEBUG - Sector Demand:', sectorDemandData);
    console.log('🔍 DEBUG - Strengths:', strengths);
    console.log('🔍 DEBUG - Weaknesses:', weaknesses);
    
    // Mostrar indicador de guardado
    showAlert('💾 Guardando análisis BCG...', 'info');
    
    fetch('../../bcg_save_final.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(bcgData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.text().then(text => {
            console.log('Raw response:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('JSON Parse Error:', e);
                console.error('Response text:', text);
                throw new Error('Respuesta inválida del servidor');
            }
        });
    })
    .then(data => {
        if (data.success) {
            const totalItems = data.total_items || 0;
            showAlert(`✅ ${data.message} - Proyecto ID: ${data.project_id} (${totalItems} elementos guardados)`, 'success');
            console.log('Datos guardados:', data.saved_data);
        } else {
            showAlert('❌ Error al guardar: ' + (data.error || 'Error desconocido'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('❌ Error de conexión al guardar: ' + error.message, 'error');
    });
}



// ===== INICIALIZACIÓN =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando BCG Matrix Interactiva...');
    
    // Primero intentar cargar datos existentes
    loadExistingData();
    
    // Solo inicializar con datos mínimos si no hay datos existentes
    if (products.length === 0) {
        console.log('No hay datos existentes, creando datos iniciales...');
        products.push({
            name: 'Producto 1',
            sales: 0,
            percentage: 0
        });
        
        competitorsByProduct['Producto 1'] = [
            { name: 'Competidor A', sales: 0, isMax: false },
            { name: 'Competidor B', sales: 0, isMax: false }
        ];
        
        const currentYear = new Date().getFullYear();
        marketGrowthData.push({
            period: `${currentYear}-${currentYear + 1}`, // 2025-2026
            rates: [0]
        });
        
        sectorDemandData = [
            { year: 2023, productDemands: [0] },
            { year: 2024, productDemands: [0] },
            { year: 2025, productDemands: [0] }
        ];
        
        // Renderizar tablas con datos iniciales
        renderSalesTable();
        renderMarketGrowthTable();
        renderCompetitorsTable();
        renderSectorDemandTable();
        renderStrengthsAndWeaknesses();
        
        // Calcular métricas iniciales
        updateSalesPercentages();
        calculateAllMetrics();
        updateBCGMatrix();
    } else {
        // Ya se renderizó todo en loadExistingData()
        console.log('Datos existentes cargados, tablas ya renderizadas');
    }
    
    console.log('✅ BCG Matrix lista para usar');
    console.log('🔧 Funciones de debug disponibles: debugBCG(), calculateAllMetrics()');
});