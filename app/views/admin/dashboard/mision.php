<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Misión - PlanMaster</title>
    <link rel="stylesheet" href="/css/styles_dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/Resources/favicon.ico">
</head>
<body>
    <?php require_once '../app/views/admin/header.php'; ?>
    
    <main class="main-content" style="
        min-height: calc(100vh - 100px); 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
        padding: 40px 20px;
        overflow-y: auto;
    ">
        <div class="container" style="max-width: 1000px; margin: 0 auto;">
            <!-- Header de la sección -->
            <div class="section-header" style="
                background: linear-gradient(135deg, #42a5f5, #1e88e5);
                padding: 30px;
                text-align: center;
                color: white;
                border-radius: 15px 15px 0 0;
                margin-bottom: 0;
            ">
                <h1 style="font-size: 2.5rem; font-weight: 700; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                    1. MISIÓN
                </h1>
            </div>
            
            <!-- Contenido principal -->
            <div class="content-container" style="
                background: white;
                border-radius: 0 0 15px 15px;
                padding: 40px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            ">
                <!-- Definición -->
                <div class="definition-section" style="margin-bottom: 40px;">
                    <p style="
                        font-size: 1.1rem; 
                        line-height: 1.8; 
                        color: #333; 
                        margin-bottom: 20px;
                        font-weight: 500;
                        background: #f8f9fa;
                        padding: 20px;
                        border-radius: 10px;
                        border-left: 4px solid #42a5f5;
                    ">
                        La <strong>MISIÓN</strong> es la razón de ser de la empresa/organización.
                    </p>
                    
                    <ul style="
                        list-style: none; 
                        padding: 0; 
                        margin: 20px 0;
                        background: #e3f2fd;
                        padding: 25px;
                        border-radius: 10px;
                    ">
                        <li style="margin-bottom: 12px; font-size: 1.05rem; color: #333;">
                            <span style="color: #1976d2; font-weight: 600;">•</span> Debe ser clara, concisa y compartida.
                        </li>
                        <li style="margin-bottom: 12px; font-size: 1.05rem; color: #333;">
                            <span style="color: #1976d2; font-weight: 600;">•</span> Siempre orientada hacia el cliente no hacia el producto o servicio.
                        </li>
                        <li style="margin-bottom: 0; font-size: 1.05rem; color: #333;">
                            <span style="color: #1976d2; font-weight: 600;">•</span> Refleja el propósito fundamental de la empresa en el mercado.
                        </li>
                    </ul>
                </div>
                
                <!-- Descripción detallada -->
                <div class="detailed-description" style="margin-bottom: 40px;">
                    <p style="
                        font-size: 1.05rem; 
                        line-height: 1.8; 
                        color: #555; 
                        text-align: justify;
                        background: #fff3e0;
                        padding: 25px;
                        border-radius: 10px;
                        border-left: 4px solid #ff9800;
                    ">
                        En términos generales describe la actividad y razón de ser de la organización y 
                        contribuye como una referencia permanente en el proceso de planificación estratégica. 
                        Se expresa a través de una oración que define el propósito fundamental de su 
                        existencia, estableciendo qué hace la empresa, por qué y para quién lo hace.
                    </p>
                </div>
                
                <!-- Ejemplos -->
                <div class="examples-section" style="margin-bottom: 40px;">
                    <h2 style="
                        font-size: 1.8rem; 
                        color: #333; 
                        margin-bottom: 25px; 
                        padding-bottom: 10px;
                        border-bottom: 3px solid #42a5f5;
                        font-weight: 600;
                    ">
                        EJEMPLOS
                    </h2>
                    
                    <div class="example-grid" style="display: grid; gap: 25px;">
                        <!-- Ejemplo 1 -->
                        <div class="example-card" style="
                            background: #f1f8e9;
                            padding: 25px;
                            border-radius: 12px;
                            border-left: 5px solid #4caf50;
                        ">
                            <h3 style="color: #2e7d32; font-size: 1.3rem; margin-bottom: 15px; font-weight: 600;">
                                Empresa de servicios
                            </h3>
                            <p style="color: #424242; line-height: 1.7; margin: 0; font-size: 1.05rem;">
                                La gestión de servicios que contribuyen a la calidad de vida de las personas y 
                                generan valor para los grupos de interés.
                            </p>
                        </div>
                        
                        <!-- Ejemplo 2 -->
                        <div class="example-card" style="
                            background: #fce4ec;
                            padding: 25px;
                            border-radius: 12px;
                            border-left: 5px solid #e91e63;
                        ">
                            <h3 style="color: #c2185b; font-size: 1.3rem; margin-bottom: 15px; font-weight: 600;">
                                Empresa productora de café
                            </h3>
                            <p style="color: #424242; line-height: 1.7; margin: 0; font-size: 1.05rem;">
                                Gracias a nuestro entusiasmo, trabajo en equipo y valores, queremos deleitar a todos 
                                aquellos que, en el mundo aman la calidad de vida, a través del mejor café que la 
                                naturaleza pueda ofrecer, ensalzado por las mejores tecnologías, por la emoción y la 
                                implicación intelectual que nacen de la búsqueda de lo bello en todo lo que hacemos.
                            </p>
                        </div>
                        
                        <!-- Ejemplo 3 -->
                        <div class="example-card" style="
                            background: #e8f5e8;
                            padding: 25px;
                            border-radius: 12px;
                            border-left: 5px solid #009688;
                        ">
                            <h3 style="color: #00695c; font-size: 1.3rem; margin-bottom: 15px; font-weight: 600;">
                                Agencia de certificación
                            </h3>
                            <p style="color: #424242; line-height: 1.7; margin: 0; font-size: 1.05rem;">
                                Dar a nuestros clientes valor económico a través de la gestión de la Calidad, la Salud y 
                                la Seguridad, el Medio Ambiente y la Responsabilidad Social de sus activos, proyectos, 
                                productos y sistemas, obteniendo como resultado la capacidad para lograr la reducción 
                                de riesgos y la mejora de los resultados.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Formulario -->
                <div class="form-section" style="
                    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
                    padding: 30px;
                    border-radius: 15px;
                    border: 2px solid #42a5f5;
                ">
                    <h3 style="
                        color: #1565c0; 
                        font-size: 1.5rem; 
                        margin-bottom: 20px; 
                        text-align: center;
                        font-weight: 600;
                    ">
                        En este apartado describa la Misión de su empresa
                    </h3>
                    
                    <form action="/dashboard/vision" method="POST" style="width: 100%;">
                        <div style="margin-bottom: 25px;">
                            <label for="mision" style="
                                display: block;
                                font-weight: 600;
                                color: #1565c0;
                                margin-bottom: 15px;
                                font-size: 1.1rem;
                            ">
                                Redacte la misión de su empresa:
                            </label>
                            <textarea 
                                id="mision" 
                                name="mision" 
                                rows="6"
                                placeholder="Escriba aquí la misión de su empresa..."
                                required
                                style="
                                    width: 100%;
                                    padding: 20px;
                                    border: 2px solid #90caf9;
                                    border-radius: 12px;
                                    font-size: 1rem;
                                    font-family: 'Poppins', sans-serif;
                                    resize: vertical;
                                    min-height: 120px;
                                    transition: all 0.3s ease;
                                    background: white;
                                    box-sizing: border-box;
                                "
                                onfocus="this.style.borderColor='#42a5f5'; this.style.boxShadow='0 0 0 3px rgba(66, 165, 245, 0.1)'"
                                onblur="this.style.borderColor='#90caf9'; this.style.boxShadow='none'"
                            ></textarea>
                        </div>
                        
                        <!-- Botones de navegación -->
                        <div style="
                            display: flex;
                            gap: 20px;
                            justify-content: space-between;
                            margin-top: 30px;
                            flex-wrap: wrap;
                        ">
                            <a href="/dashboard/inicio" style="
                                display: inline-flex;
                                align-items: center;
                                gap: 10px;
                                padding: 15px 30px;
                                background: rgba(255, 255, 255, 0.8);
                                color: #666;
                                text-decoration: none;
                                border-radius: 10px;
                                font-weight: 600;
                                font-size: 1rem;
                                transition: all 0.3s ease;
                                border: 2px solid #ddd;
                            " onmouseover="this.style.background='rgba(255, 255, 255, 1)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.1)'"
                               onmouseout="this.style.background='rgba(255, 255, 255, 0.8)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                ← Anterior
                            </a>
                            
                            <button type="submit" style="
                                display: inline-flex;
                                align-items: center;
                                gap: 12px;
                                padding: 15px 35px;
                                background: linear-gradient(135deg, #4CAF50, #45a049);
                                color: white;
                                border: none;
                                border-radius: 10px;
                                font-weight: 700;
                                font-size: 1rem;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
                            " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(76, 175, 80, 0.4)'"
                               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(76, 175, 80, 0.3)'">
                                Siguiente: Visión →
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Estilos adicionales -->
    <style>
        @media (max-width: 768px) {
            .container {
                margin: 0 10px !important;
            }
            
            .content-container {
                padding: 25px 20px !important;
            }
            
            .section-header h1 {
                font-size: 2rem !important;
            }
            
            .example-grid {
                grid-template-columns: 1fr !important;
            }
            
            .form-section div[style*="justify-content: space-between"] {
                flex-direction: column;
                align-items: center;
                gap: 15px !important;
            }
        }
        
        @media (max-width: 480px) {
            .section-header {
                padding: 20px 15px !important;
            }
            
            .section-header h1 {
                font-size: 1.6rem !important;
            }
        }
    </style>
    
    <?php require_once '../app/views/admin/footer.php'; ?>
    <script src="/js/dashboard.js"></script>
</body>
</html>