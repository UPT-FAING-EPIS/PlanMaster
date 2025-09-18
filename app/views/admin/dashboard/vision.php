<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visión - PlanMaster</title>
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
                background: linear-gradient(135deg, #26c6da, #00acc1);
                padding: 30px;
                text-align: center;
                color: white;
                border-radius: 15px 15px 0 0;
                margin-bottom: 0;
            ">
                <h1 style="font-size: 2.5rem; font-weight: 700; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                    2. VISIÓN
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
                        background: #e0f7fa;
                        padding: 20px;
                        border-radius: 10px;
                        border-left: 4px solid #26c6da;
                    ">
                        La <strong>VISIÓN</strong> de una empresa define lo que la empresa/organización quiere lograr en 
                        el futuro. Es lo que la organización aspira llegar a ser en torno a 2-3 años.
                    </p>
                    
                    <ul style="
                        list-style: none; 
                        padding: 0; 
                        margin: 20px 0;
                        background: #e1f5fe;
                        padding: 25px;
                        border-radius: 10px;
                    ">
                        <li style="margin-bottom: 12px; font-size: 1.05rem; color: #333;">
                            <span style="color: #0097a7; font-weight: 600;">•</span> Debe ser retadora, positiva, compartida y coherente con la misión.
                        </li>
                        <li style="margin-bottom: 12px; font-size: 1.05rem; color: #333;">
                            <span style="color: #0097a7; font-weight: 600;">•</span> Marca el fin último que la estrategia debe seguir.
                        </li>
                        <li style="margin-bottom: 0; font-size: 1.05rem; color: #333;">
                            <span style="color: #0097a7; font-weight: 600;">•</span> Proyecta la imagen de destino que se pretende alcanzar.
                        </li>
                    </ul>
                </div>
                
                <!-- Descripción adicional -->
                <div class="additional-description" style="margin-bottom: 40px;">
                    <p style="
                        font-size: 1.05rem; 
                        line-height: 1.8; 
                        color: #555; 
                        text-align: justify;
                        background: #fff8e1;
                        padding: 25px;
                        border-radius: 10px;
                        border-left: 4px solid #ffc107;
                    ">
                        La visión debe ser conocida y compartida por todos los miembros de la empresa y 
                        también por aquellos que se relacionan con ella.
                    </p>
                </div>
                
                <!-- Ejemplos -->
                <div class="examples-section" style="margin-bottom: 40px;">
                    <h2 style="
                        font-size: 1.8rem; 
                        color: #333; 
                        margin-bottom: 25px; 
                        padding-bottom: 10px;
                        border-bottom: 3px solid #26c6da;
                        font-weight: 600;
                    ">
                        EJEMPLOS
                    </h2>
                    
                    <div class="example-grid" style="display: grid; gap: 25px;">
                        <!-- Ejemplo 1 -->
                        <div class="example-card" style="
                            background: #e8f5e8;
                            padding: 25px;
                            border-radius: 12px;
                            border-left: 5px solid #4caf50;
                        ">
                            <h3 style="color: #2e7d32; font-size: 1.3rem; margin-bottom: 15px; font-weight: 600;">
                                Empresa de servicios
                            </h3>
                            <p style="color: #424242; line-height: 1.7; margin: 0; font-size: 1.05rem;">
                                Ser el grupo empresarial de referencia en nuestras áreas de actividad.
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
                                Queremos ser en el mundo el punto de referencia de la cultura y de la excelencia 
                                del café. Una empresa innovadora que propone los mejores productos y lugares de 
                                consumo y que, gracias a ello, crece y se convierte en líder de la alta gama.
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
                                Ser líderes en nuestro sector y un actor principal en todos los segmentos de 
                                mercado en los que estamos presentes, en los mercados clave.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Formulario -->
                <div class="form-section" style="
                    background: linear-gradient(135deg, #e0f7fa, #b2ebf2);
                    padding: 30px;
                    border-radius: 15px;
                    border: 2px solid #26c6da;
                    margin-bottom: 40px;
                ">
                    <h3 style="
                        color: #00838f; 
                        font-size: 1.5rem; 
                        margin-bottom: 20px; 
                        text-align: center;
                        font-weight: 600;
                    ">
                        En este apartado describa la Visión de su empresa
                    </h3>
                    
                    <form action="/dashboard/valores" method="POST" style="width: 100%;">
                        <div style="margin-bottom: 25px;">
                            <label for="vision" style="
                                display: block;
                                font-weight: 600;
                                color: #00838f;
                                margin-bottom: 15px;
                                font-size: 1.1rem;
                            ">
                                Redacte la visión de su empresa:
                            </label>
                            <textarea 
                                id="vision" 
                                name="vision" 
                                rows="6"
                                placeholder="Escriba aquí la visión de su empresa..."
                                required
                                style="
                                    width: 100%;
                                    padding: 20px;
                                    border: 2px solid #4dd0e1;
                                    border-radius: 12px;
                                    font-size: 1rem;
                                    font-family: 'Poppins', sans-serif;
                                    resize: vertical;
                                    min-height: 120px;
                                    transition: all 0.3s ease;
                                    background: white;
                                    box-sizing: border-box;
                                "
                                onfocus="this.style.borderColor='#26c6da'; this.style.boxShadow='0 0 0 3px rgba(38, 198, 218, 0.1)'"
                                onblur="this.style.borderColor='#4dd0e1'; this.style.boxShadow='none'"
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
                            <a href="/dashboard/mision" style="
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
                                background: linear-gradient(135deg, #26c6da, #00acc1);
                                color: white;
                                border: none;
                                border-radius: 10px;
                                font-weight: 700;
                                font-size: 1rem;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                box-shadow: 0 6px 20px rgba(38, 198, 218, 0.3);
                            " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(38, 198, 218, 0.4)'"
                               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(38, 198, 218, 0.3)'">
                                Siguiente: Valores →
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Relación entre Misión y Visión -->
                <div class="relationship-section" style="
                    background: linear-gradient(135deg, #f3e5f5, #e1bee7);
                    padding: 30px;
                    border-radius: 15px;
                    border: 2px solid #9c27b0;
                ">
                    <h3 style="
                        color: #6a1b9a; 
                        font-size: 1.6rem; 
                        margin-bottom: 25px; 
                        text-align: center;
                        font-weight: 600;
                    ">
                        Relación entre Misión y Visión
                    </h3>
                    
                    <div class="relationship-diagram" style="
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        margin: 30px 0;
                        flex-wrap: wrap;
                        gap: 20px;
                    ">
                        <!-- Situación Actual -->
                        <div class="diagram-item" style="
                            background: #8bc34a;
                            color: white;
                            padding: 20px;
                            border-radius: 50%;
                            min-width: 120px;
                            text-align: center;
                            font-weight: 600;
                            box-shadow: 0 4px 15px rgba(139, 195, 74, 0.3);
                            flex: 1;
                            min-height: 120px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            flex-direction: column;
                        ">
                            <div style="font-size: 0.9rem; margin-bottom: 5px;">¿Cuál es la</div>
                            <div style="font-size: 1rem; font-weight: 700;">SITUACIÓN ACTUAL?</div>
                            <div style="font-size: 0.8rem; margin-top: 5px; opacity: 0.9;">(Misión)</div>
                        </div>
                        
                        <!-- Flecha -->
                        <div style="
                            flex: 0 0 auto;
                            text-align: center;
                            color: #6a1b9a;
                            font-weight: 600;
                            font-size: 1.1rem;
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                        ">
                            <div style="margin-bottom: 5px;">¿Qué camino</div>
                            <div style="font-size: 2rem; margin: 10px 0;">→</div>
                            <div style="margin-top: 5px;">a seguir?</div>
                        </div>
                        
                        <!-- Situación Futura -->
                        <div class="diagram-item" style="
                            background: #03a9f4;
                            color: white;
                            padding: 20px;
                            border-radius: 50%;
                            min-width: 120px;
                            text-align: center;
                            font-weight: 600;
                            box-shadow: 0 4px 15px rgba(3, 169, 244, 0.3);
                            flex: 1;
                            min-height: 120px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            flex-direction: column;
                        ">
                            <div style="font-size: 0.9rem; margin-bottom: 5px;">¿Cuál es la</div>
                            <div style="font-size: 1rem; font-weight: 700;">SITUACIÓN FUTURA?</div>
                            <div style="font-size: 0.8rem; margin-top: 5px; opacity: 0.9;">(Visión)</div>
                        </div>
                    </div>
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
            
            .relationship-diagram {
                flex-direction: column !important;
                text-align: center;
            }
            
            .relationship-diagram div[style*="font-size: 2rem"] {
                transform: rotate(90deg);
                margin: 20px 0 !important;
            }
        }
        
        @media (max-width: 480px) {
            .section-header {
                padding: 20px 15px !important;
            }
            
            .section-header h1 {
                font-size: 1.6rem !important;
            }
            
            .diagram-item {
                min-width: 100px !important;
                min-height: 100px !important;
                padding: 15px !important;
            }
        }
    </style>
    
    <?php require_once '../app/views/admin/footer.php'; ?>
    <script src="/js/dashboard.js"></script>
</body>
</html>