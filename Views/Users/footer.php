<footer class="footer">
    <div class="footer-container">
        <!-- Información principal -->
        <div class="footer-main">
            <div class="footer-logo">
                <span class="footer-logo-text">PlanMaster</span>
                <span class="footer-logo-subtitle">Tu plan estratégico en un solo clic</span>
            </div>
            
            <div class="footer-links">
                <div class="footer-column">
                    <h4>Producto</h4>
                    <ul>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="projects.php">Proyectos</a></li>
                        <li><a href="templates.php">Plantillas</a></li>
                        <li><a href="reports.php">Reportes</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Soporte</h4>
                    <ul>
                        <li><a href="help.php">Centro de Ayuda</a></li>
                        <li><a href="tutorials.php">Tutoriales</a></li>
                        <li><a href="contact.php">Contacto</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Empresa</h4>
                    <ul>
                        <li><a href="about.php">Acerca de</a></li>
                        <li><a href="privacy.php">Privacidad</a></li>
                        <li><a href="terms.php">Términos</a></li>
                        <li><a href="blog.php">Blog</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>Conecta</h4>
                    <div class="social-links">
                        <a href="#" class="social-link" title="Twitter">
                            <span class="social-icon">🐦</span>
                        </a>
                        <a href="#" class="social-link" title="LinkedIn">
                            <span class="social-icon">💼</span>
                        </a>
                        <a href="#" class="social-link" title="Facebook">
                            <span class="social-icon">📘</span>
                        </a>
                        <a href="#" class="social-link" title="Instagram">
                            <span class="social-icon">📷</span>
                        </a>
                    </div>
                    
                    <div class="newsletter">
                        <h5>Newsletter</h5>
                        <div class="newsletter-form">
                            <input type="email" placeholder="Tu email" class="newsletter-input">
                            <button class="newsletter-btn">Suscribirme</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Línea divisoria -->
        <div class="footer-divider"></div>
        
        <!-- Footer bottom -->
        <div class="footer-bottom">
            <div class="footer-copyright">
                <p>&copy; <?php echo date('Y'); ?> PlanMaster. Todos los derechos reservados.</p>
                <p class="footer-version">Versión 1.0.0 | Desarrollado con ❤️ para empresarios</p>
            </div>
            
            <div class="footer-stats">
                <div class="stat-item">
                    <span class="stat-number">1,000+</span>
                    <span class="stat-label">Planes creados</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Empresas activas</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">98%</span>
                    <span class="stat-label">Satisfacción</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Botón de volver arriba -->
    <button class="back-to-top" onclick="scrollToTop()" title="Volver arriba">
        ↑
    </button>
</footer>

<script>
// Función para volver arriba
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Mostrar/ocultar botón de volver arriba
window.addEventListener('scroll', function() {
    const backToTopBtn = document.querySelector('.back-to-top');
    if (window.pageYOffset > 300) {
        backToTopBtn.classList.add('show');
    } else {
        backToTopBtn.classList.remove('show');
    }
});

// Newsletter subscription
document.addEventListener('DOMContentLoaded', function() {
    const newsletterBtn = document.querySelector('.newsletter-btn');
    const newsletterInput = document.querySelector('.newsletter-input');
    
    if (newsletterBtn && newsletterInput) {
        newsletterBtn.addEventListener('click', function() {
            const email = newsletterInput.value.trim();
            
            if (email && isValidEmail(email)) {
                // Simulación de suscripción exitosa
                newsletterInput.value = '';
                showNotification('¡Gracias por suscribirte!', 'success');
            } else {
                showNotification('Por favor ingresa un email válido', 'error');
            }
        });
        
        newsletterInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                newsletterBtn.click();
            }
        });
    }
});

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showNotification(message, type) {
    // Crear notificación temporal
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        animation: slideInRight 0.3s ease-out;
        ${type === 'success' ? 'background: #4caf50;' : 'background: #f44336;'}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Animaciones para las estadísticas
const observerOptions = {
    threshold: 0.5,
    rootMargin: '0px 0px -100px 0px'
};

const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const statNumbers = entry.target.querySelectorAll('.stat-number');
            statNumbers.forEach(statNumber => {
                animateNumber(statNumber);
            });
        }
    });
}, observerOptions);

document.addEventListener('DOMContentLoaded', function() {
    const footerStats = document.querySelector('.footer-stats');
    if (footerStats) {
        statsObserver.observe(footerStats);
    }
});

function animateNumber(element) {
    const target = element.textContent;
    const isPercentage = target.includes('%');
    const number = parseInt(target.replace(/[^\d]/g, ''));
    let current = 0;
    const increment = number / 50; // 50 frames de animación
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= number) {
            current = number;
            clearInterval(timer);
        }
        
        element.textContent = Math.floor(current) + (isPercentage ? '%' : '+');
    }, 30);
}
</script>

<style>
/* Animaciones para las notificaciones */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}
</style>
