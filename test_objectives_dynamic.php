<?php
// Página de prueba para objetivos dinámicos
session_start();

// Simular datos de usuario y proyecto
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'id' => 1,
        'full_name' => 'Usuario de Prueba',
        'email' => 'test@example.com'
    ];
}

echo "<h2>✅ Prueba de Objetivos Dinámicos - PlanMaster</h2>";
echo "<div style='padding: 20px; background: #f0f8ff; border-radius: 10px; margin: 20px 0;'>";
echo "<h3>🎯 Funcionalidades Implementadas:</h3>";
echo "<ul style='font-size: 16px; line-height: 1.6;'>";
echo "<li><strong>✅ Objetivos Dinámicos:</strong> Añadir/eliminar objetivos estratégicos sin límite fijo</li>";
echo "<li><strong>✅ Objetivos Específicos Flexibles:</strong> Al menos 1 objetivo específico por estratégico, sin límite máximo</li>";
echo "<li><strong>✅ Eliminación de 'Descripción opcional':</strong> Solo títulos requeridos</li>";
echo "<li><strong>✅ Interfaz Mejorada:</strong> Botones de añadir/eliminar intuitivos</li>";
echo "<li><strong>✅ Contador Dinámico:</strong> Resumen automático de objetivos</li>";
echo "<li><strong>✅ Validación Inteligente:</strong> Campos requeridos con feedback visual</li>";
echo "</ul>";
echo "</div>";

echo "<div style='padding: 15px; background: #e8f5e8; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>🔗 Enlaces de Prueba:</h3>";

// Verificar si hay proyectos en la base de datos
try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        $result = $conn->query("SELECT id, project_name FROM strategic_projects ORDER BY created_at DESC LIMIT 5");
        
        if ($result && $result->num_rows > 0) {
            echo "<p><strong>Proyectos disponibles para pruebas:</strong></p>";
            echo "<ul>";
            while ($project = $result->fetch_assoc()) {
                echo "<li><a href='Views/Projects/objectives.php?id=" . $project['id'] . "' target='_blank' style='background: #2196f3; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Objetivos: " . htmlspecialchars($project['project_name']) . "</a></li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: #ff9800;'>No se encontraron proyectos. Crear un proyecto primero en el dashboard.</p>";
            echo "<a href='Views/Users/dashboard.php' style='background: #4caf50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir al Dashboard</a>";
        }
        
        $database->closeConnection();
    }
    
} catch (Exception $e) {
    echo "<p style='color: #f44336;'>Error al conectar con la base de datos: " . $e->getMessage() . "</p>";
    echo "<p>Asegúrate de que XAMPP esté ejecutándose y la base de datos configurada.</p>";
}

echo "</div>";

echo "<div style='padding: 15px; background: #fff3e0; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>📝 Instrucciones de Prueba:</h3>";
echo "<ol style='line-height: 1.6;'>";
echo "<li><strong>Abrir un proyecto:</strong> Haz clic en uno de los enlaces de arriba</li>";
echo "<li><strong>Añadir Objetivos:</strong> Usa el botón '+ Añadir Objetivo Estratégico'</li>";
echo "<li><strong>Añadir Específicos:</strong> Dentro de cada objetivo, usa '+ Añadir Objetivo Específico'</li>";
echo "<li><strong>Eliminar Objetivos:</strong> Usa los botones '×' (objetivos específicos solo se pueden eliminar si hay más de 1)</li>";
echo "<li><strong>Validación:</strong> El botón guardar se habilita cuando todos los campos están completos</li>";
echo "<li><strong>Contador:</strong> Observa cómo se actualiza el resumen dinámicamente</li>";
echo "</ol>";
echo "</div>";

echo "<div style='padding: 15px; background: #f3e5f5; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>🔧 Cambios Realizados:</h3>";
echo "<ul style='line-height: 1.6;'>";
echo "<li>❌ <strong>Eliminado:</strong> Límite fijo de 3 objetivos estratégicos</li>";
echo "<li>❌ <strong>Eliminado:</strong> Límite fijo de 2 objetivos específicos (ahora mínimo 1, máximo ilimitado)</li>";
echo "<li>❌ <strong>Eliminado:</strong> Campos 'Descripción (opcional)' de toda la interfaz</li>";
echo "<li>➕ <strong>Añadido:</strong> Botones dinámicos para añadir/eliminar objetivos</li>";
echo "<li>🔄 <strong>Mejorado:</strong> Sistema de reindexación automática con gestión inteligente de botones</li>";
echo "<li>📊 <strong>Actualizado:</strong> Contador dinámico de objetivos en tiempo real</li>";
echo "</ul>";
echo "</div>";

echo "<div style='padding: 15px; background: #e3f2fd; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>📋 Reglas del Sistema Dinámico:</h3>";
echo "<ul style='line-height: 1.6;'>";
echo "<li><strong>Objetivos Estratégicos:</strong> Mínimo 1, máximo ilimitado</li>";
echo "<li><strong>Objetivos Específicos:</strong> Cada estratégico debe tener al menos 1, máximo ilimitado</li>";
echo "<li><strong>Eliminación Inteligente:</strong> No se puede eliminar el último objetivo específico</li>";
echo "<li><strong>Validación:</strong> Todos los títulos deben tener al menos 5 caracteres</li>";
echo "<li><strong>Autoguardado:</strong> Los borradores se guardan automáticamente cada 45 segundos</li>";
echo "</ul>";
echo "</div>";

echo "<p style='text-align: center; margin: 30px 0;'>";
echo "<a href='index.php' style='background: #673ab7; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-size: 16px;'>← Volver al Inicio</a>";
echo "</p>";
?>