<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Proyecto - PlanMaster</title>
    <link rel="stylesheet" href="/css/styles_dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/Resources/favicon.ico">
</head>
<body>
    <?php require_once '../app/views/admin/header.php'; ?>
    
    <main class="main-content" style="
        height: 100vh; 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
        display: flex; 
        flex-direction: column;
        padding: 0;
        overflow: hidden;
        position: relative;
    ">
        <!-- Header del formulario -->
        <div class="form-header" style="
            background: linear-gradient(135deg, #42a5f5, #1e88e5);
            padding: 30px 30px;
            text-align: center;
            color: white;
            flex: 0 0 auto;
        ">
            <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 8px 0; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                Iniciar Nuevo Proyecto
            </h1>
            <p style="font-size: 1rem; opacity: 0.9; margin: 0; line-height: 1.4; max-width: 500px; margin: 0 auto;">
                Comienza tu plan estratégico empresarial completando la información básica
            </p>
        </div>
        
        <!-- Cuerpo del formulario -->
        <div class="form-body" style="
            flex: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 40px 30px;
            background: rgba(255, 255, 255, 0.05);
        ">
            <div style="width: 100%; max-width: 500px;">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h2 style="color: white; font-size: 1.4rem; font-weight: 600; margin-bottom: 8px; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                        Información del Proyecto
                    </h2>
                    <p style="color: rgba(255,255,255,0.9); line-height: 1.5; margin: 0; font-size: 0.95rem;">
                        Estos datos te ayudarán a personalizar tu experiencia y generar reportes específicos.
                    </p>
                </div>
                
                <form action="/dashboard/mision" method="POST" style="width: 100%;">
                    <div style="display: grid; gap: 25px;">
                        <div class="form-group" style="position: relative;">
                            <label for="projectName" style="
                                display: block;
                                font-weight: 600;
                                color: white;
                                margin-bottom: 10px;
                                font-size: 1rem;
                                text-shadow: 0 1px 2px rgba(0,0,0,0.2);
                            ">
                                📋 Nombre del Proyecto
                            </label>
                            <input 
                                type="text" 
                                id="projectName" 
                                name="projectName" 
                                placeholder="Ej: Plan Estratégico Digital 2024-2027"
                                required
                                style="
                                    width: 100%;
                                    padding: 15px 20px;
                                    border: 2px solid rgba(255, 255, 255, 0.3);
                                    border-radius: 12px;
                                    font-size: 1rem;
                                    transition: all 0.3s ease;
                                    background: rgba(255, 255, 255, 0.9);
                                    box-sizing: border-box;
                                    backdrop-filter: blur(10px);
                                "
                                onfocus="this.style.borderColor='rgba(255, 255, 255, 0.8)'; this.style.background='white'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.1)'; this.style.transform='translateY(-1px)'"
                                onblur="this.style.borderColor='rgba(255, 255, 255, 0.3)'; this.style.background='rgba(255, 255, 255, 0.9)'; this.style.boxShadow='none'; this.style.transform='translateY(0)'"
                            >
                        </div>
                        
                        <div class="form-group" style="position: relative;">
                            <label for="companyName" style="
                                display: block;
                                font-weight: 600;
                                color: white;
                                margin-bottom: 10px;
                                font-size: 1rem;
                                text-shadow: 0 1px 2px rgba(0,0,0,0.2);
                            ">
                                🏢 Nombre de la Empresa
                            </label>
                            <input 
                                type="text" 
                                id="companyName" 
                                name="companyName" 
                                placeholder="Ej: Innovación Digital S.A.C."
                                required
                                style="
                                    width: 100%;
                                    padding: 15px 20px;
                                    border: 2px solid rgba(255, 255, 255, 0.3);
                                    border-radius: 12px;
                                    font-size: 1rem;
                                    transition: all 0.3s ease;
                                    background: rgba(255, 255, 255, 0.9);
                                    box-sizing: border-box;
                                    backdrop-filter: blur(10px);
                                "
                                onfocus="this.style.borderColor='rgba(255, 255, 255, 0.8)'; this.style.background='white'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.1)'; this.style.transform='translateY(-1px)'"
                                onblur="this.style.borderColor='rgba(255, 255, 255, 0.3)'; this.style.background='rgba(255, 255, 255, 0.9)'; this.style.boxShadow='none'; this.style.transform='translateY(0)'"
                            >
                        </div>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div style="
                        display: flex;
                        gap: 15px;
                        justify-content: center;
                        margin-top: 35px;
                        flex-wrap: wrap;
                    ">
                        <a href="/dashboard" style="
                            display: inline-flex;
                            align-items: center;
                            gap: 8px;
                            padding: 12px 25px;
                            background: rgba(255, 255, 255, 0.2);
                            color: white;
                            text-decoration: none;
                            border-radius: 10px;
                            font-weight: 600;
                            font-size: 0.95rem;
                            transition: all 0.3s ease;
                            border: 2px solid rgba(255, 255, 255, 0.3);
                            backdrop-filter: blur(10px);
                            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
                        " onmouseover="this.style.background='rgba(255, 255, 255, 0.3)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.15)'"
                           onmouseout="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            ← Cancelar
                        </a>
                        
                        <button type="submit" style="
                            display: inline-flex;
                            align-items: center;
                            gap: 10px;
                            padding: 12px 30px;
                            background: linear-gradient(135deg, #4CAF50, #45a049);
                            color: white;
                            border: none;
                            border-radius: 10px;
                            font-weight: 700;
                            font-size: 1rem;
                            cursor: pointer;
                            transition: all 0.3s ease;
                            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
                            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
                        " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(76, 175, 80, 0.4)'"
                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(76, 175, 80, 0.3)'">
                            ¡Empezar Ahora! 🚀
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    
    <?php require_once '../app/views/admin/footer.php'; ?>
    <script src="/js/dashboard.js"></script>
</body>
</html>
