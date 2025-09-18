<?php
// Se asume que $baseUrl ya está definido en la página que incluye este header
?>
<header class="header">
    <div class="header-container">
        <!-- Logo y navegación principal -->
        <div class="header-left">
            <div class="logo">
                <a href="<?php echo $baseUrl; ?>/Views/Users/dashboard.php">
                    <span class="logo-text">PlanMaster</span>
                    <span class="logo-subtitle">Plan Estratégico</span>
                </a>
            </div>
            
            <nav class="main-nav">
                <a href="<?php echo $baseUrl; ?>/Views/Users/dashboard.php" class="nav-link">
                    <span class="nav-icon">🏠</span>
                    Dashboard
                </a>
                <a href="<?php echo $baseUrl; ?>/Views/Users/projects.php" class="nav-link active">
                    <span class="nav-icon">📊</span>
                    Proyectos
                </a>
                <a href="<?php echo $baseUrl; ?>/Views/Users/templates.php" class="nav-link">
                    <span class="nav-icon">📋</span>
                    Plantillas
                </a>
                <a href="<?php echo $baseUrl; ?>/Views/Users/reports.php" class="nav-link">
                    <span class="nav-icon">📈</span>
                    Reportes
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
                    <?php if ($user['avatar'] ?? false): ?>
                        <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar">
                    <?php else: ?>
                        <div class="avatar-placeholder">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($user['name']); ?></div>
                            <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                    </div>
                    
                    <div class="dropdown-menu">
                        <a href="<?php echo $baseUrl; ?>/Views/Users/profile.php" class="dropdown-item">
                            <span class="item-icon">👤</span>
                            Mi Perfil
                        </a>
                        <a href="<?php echo $baseUrl; ?>/Views/Users/settings.php" class="dropdown-item">
                            <span class="item-icon">⚙️</span>
                            Configuración
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo $baseUrl; ?>/Controllers/AuthController.php?action=logout" class="dropdown-item logout">
                            <span class="item-icon">🚪</span>
                            Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Cerrar dropdown al hacer clic fuera
document.addEventListener('click', function(event) {
    const userMenu = document.querySelector('.user-menu');
    const dropdown = document.getElementById('userDropdown');
    
    if (!userMenu.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});
</script>