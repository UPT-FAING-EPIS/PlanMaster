<header class="header">
    <div class="header-container">
        <!-- Logo y navegación principal -->
        <div class="header-left">
            <div class="logo">
                <a href="dashboard.php">
                    <span class="logo-text">PlanMaster</span>
                    <span class="logo-subtitle">Plan Estratégico</span>
                </a>
            </div>
            
            <nav class="main-nav">
                <a href="dashboard.php" class="nav-link active">
                    <span class="nav-icon">🏠</span>
                    Dashboard
                </a>
                <a href="projects.php" class="nav-link">
                    <span class="nav-icon">📊</span>
                    Proyectos
                </a>
                <a href="templates.php" class="nav-link">
                    <span class="nav-icon">📋</span>
                    Plantillas
                </a>
            </nav>
        </div>
        
        <!-- Usuario y acciones -->
        <div class="header-right">
            <!-- Notificaciones -->
            <div class="notification-icon">
                <span class="icon">🔔</span>
                <span class="notification-badge">3</span>
            </div>
            
            <!-- Menu de usuario -->
            <div class="user-menu">
                <div class="user-avatar" onclick="toggleUserDropdown()">
                    <?php if ($user['avatar']): ?>
                        <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar">
                    <?php else: ?>
                        <div class="avatar-placeholder">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="user-info" onclick="toggleUserDropdown()">
                    <span class="user-name"><?php echo htmlspecialchars($user['name']); ?></span>
                    <span class="user-email"><?php echo htmlspecialchars($user['email']); ?></span>
                    <span class="dropdown-arrow">▼</span>
                </div>
                
                <!-- Dropdown del usuario -->
                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="dropdown-avatar">
                            <?php if ($user['avatar']): ?>
                                <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar">
                            <?php else: ?>
                                <div class="avatar-placeholder">
                                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="dropdown-info">
                            <div class="dropdown-name"><?php echo htmlspecialchars($user['name']); ?></div>
                            <div class="dropdown-email"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                    </div>
                    
                    <div class="dropdown-divider"></div>
                    
                    <a href="profile.php" class="dropdown-item">
                        <span class="dropdown-icon">👤</span>
                        Mi Perfil
                    </a>
                    
                    <a href="settings.php" class="dropdown-item">
                        <span class="dropdown-icon">⚙️</span>
                        Configuración
                    </a>
                    
                    <a href="help.php" class="dropdown-item">
                        <span class="dropdown-icon">❓</span>
                        Ayuda
                    </a>
                    
                    <div class="dropdown-divider"></div>
                    
                    <a href="../../Controllers/AuthController.php?action=logout" class="dropdown-item logout">
                        <span class="dropdown-icon">🚪</span>
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Breadcrumb (opcional) -->
    <div class="breadcrumb-container">
        <nav class="breadcrumb">
            <a href="dashboard.php" class="breadcrumb-item">Inicio</a>
            <span class="breadcrumb-separator">›</span>
            <span class="breadcrumb-current">Dashboard</span>
        </nav>
    </div>
</header>

<script>
// Función para toggle del dropdown de usuario
function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

// Cerrar dropdown al hacer clic fuera
document.addEventListener('click', function(event) {
    const userMenu = document.querySelector('.user-menu');
    const dropdown = document.getElementById('userDropdown');
    
    if (!userMenu.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});

// Marcar enlace activo en la navegación
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        const href = link.getAttribute('href');
        
        if (href === currentPage || (currentPage === '' && href === 'dashboard.php')) {
            link.classList.add('active');
        }
    });
});
</script>
