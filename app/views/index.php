<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanMaster - Tu Plan Estratégico en un Solo Clic</title>
    <link rel="stylesheet" href="Publics/css/styles_index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Sección 1 - Presentación Principal -->
    <section class="section section1" id="section1">
        <!-- Imagen pepol1.png con efectos -->
        <img src="Resources/pepol2.png" alt="Persona planificando" class="pepol-image">
        
        <!-- Botón CLIC reposicionado -->
        <a href="Views/Auth/login.php" class="btn-clic">¡¡CLICK!!</a>
        
        <!-- Indicador de scroll solo en la primera sección -->
        <div class="scroll-indicator">
            <div class="scroll-arrow">↓</div>
            <div class="scroll-text">Desliza hacia abajo</div>
        </div>
    </section>

    <!-- Sección 2 - Segunda Presentación con Beneficios -->
    <section class="section section2" id="section2">
        <div class="benefits-container">
            <!-- Beneficio 1 -->
            <div class="benefit-item">
                <img src="Resources/point1.png" alt="Punto 1" class="benefit-icon">
                <div class="benefit-text">
                    <div class="benefit-title">GUÍA ESTRUCTURADA PASITO A PASITO</div>
                    <div class="benefit-description">Ayuda a empresas y emprendedores a no perderse en conceptos teóricos y seguir un camino claro para armar su plan estratégico.</div>
                </div>
            </div>
            
            <!-- Beneficio 2 -->
            <div class="benefit-item">
                <img src="Resources/point2.png" alt="Punto 2" class="benefit-icon">
                <div class="benefit-text">
                    <div class="benefit-title">AHORRO DE TIEMPO Y ACCESIBILIDAD</div>
                    <div class="benefit-description">Digitaliza un proceso que antes se hacía en Excel o papel, permitiendo trabajar desde cualquier dispositivo y guardando el progreso en línea.</div>
                </div>
            </div>
            
            <!-- Beneficio 3 -->
            <div class="benefit-item">
                <img src="Resources/point3.png" alt="Punto 3" class="benefit-icon">
                <div class="benefit-text">
                    <div class="benefit-title">TOMA DE DECISIONES MÁS CLARA</div>
                    <div class="benefit-description">Al visualizar misión, visión, matrices, poder identificar oportunidades, riesgos y estrategias para mejorar la competitividad de su organización.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección 3 - Tercera Presentación con Frase Final -->
    <section class="section section3" id="section3">
        <div class="final-phrase">
            <div class="phrase-text">
                😎 ¿¿¿¿Qué esperas para iniciar con un buen plan estratégico de TI???? 😎
            </div>
        </div>
    </section>

    <!-- JavaScript para mejorar la experiencia de usuario -->
    <script>
        // Smooth scrolling entre secciones con la rueda del mouse
        let isScrolling = false;
        let currentSection = 0;
        const sections = document.querySelectorAll('.section');
        const totalSections = sections.length;

        // Función para ir a una sección específica
        function goToSection(index) {
            if (index >= 0 && index < totalSections) {
                currentSection = index;
                sections[currentSection].scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }

        // Manejo del scroll con rueda del mouse
        document.addEventListener('wheel', function(e) {
            if (isScrolling) return;
            
            isScrolling = true;
            
            if (e.deltaY > 0) {
                // Scroll hacia abajo
                if (currentSection < totalSections - 1) {
                    currentSection++;
                    goToSection(currentSection);
                }
            } else {
                // Scroll hacia arriba
                if (currentSection > 0) {
                    currentSection--;
                    goToSection(currentSection);
                }
            }
            
            // Resetear el flag después de un tiempo
            setTimeout(() => {
                isScrolling = false;
            }, 800);
        });

        // Detectar en qué sección estamos al cargar la página
        window.addEventListener('load', function() {
            // Asegurar que comenzamos en la primera sección
            currentSection = 0;
            goToSection(currentSection);
        });

        // Manejo del scroll manual (barra de scroll)
        let scrollTimeout;
        window.addEventListener('scroll', function() {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                const scrollPosition = window.scrollY;
                const sectionHeight = window.innerHeight;
                const newSection = Math.round(scrollPosition / sectionHeight);
                
                if (newSection !== currentSection && newSection >= 0 && newSection < totalSections) {
                    currentSection = newSection;
                }
            }, 100);
        });

        // Soporte para navegación con teclado
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowDown' || e.key === 'PageDown') {
                e.preventDefault();
                if (currentSection < totalSections - 1) {
                    currentSection++;
                    goToSection(currentSection);
                }
            } else if (e.key === 'ArrowUp' || e.key === 'PageUp') {
                e.preventDefault();
                if (currentSection > 0) {
                    currentSection--;
                    goToSection(currentSection);
                }
            } else if (e.key === 'Home') {
                e.preventDefault();
                currentSection = 0;
                goToSection(currentSection);
            } else if (e.key === 'End') {
                e.preventDefault();
                currentSection = totalSections - 1;
                goToSection(currentSection);
            }
        });
    </script>
</body>
</html>
