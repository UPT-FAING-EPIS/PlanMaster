<?php
session_start();

// Debug directo del ReportController
echo "🔍 Debug directo del ReportController<br><br>";

// Mostrar información de la sesión
echo "<h3>📋 Estado de la Sesión:</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "User ID: " . ($_SESSION['user_id'] ?? 'No definido') . "<br>";
echo "Logged in: " . (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'Sí' : 'No') . "<br>";
echo "User email: " . ($_SESSION['user_email'] ?? 'No definido') . "<br><br>";

if ($_POST) {
    echo "<h3>📤 Datos POST recibidos:</h3>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    // Intentar cargar las dependencias paso a paso
    echo "<h3>🔧 Cargando dependencias:</h3>";
    
    try {
        echo "1. Cargando database.php... ";
        require_once __DIR__ . '/config/database.php';
        echo "✅<br>";
        
        echo "2. Cargando AuthController.php... ";
        require_once __DIR__ . '/Controllers/AuthController.php';
        echo "✅<br>";
        
        // Verificar autenticación
        echo "3. Verificando autenticación... ";
        if (!AuthController::isLoggedIn()) {
            echo "❌ Usuario no autenticado<br>";
            exit;
        }
        echo "✅<br>";
        
        echo "4. Cargando ProjectController.php... ";
        require_once __DIR__ . '/Controllers/ProjectController.php';
        echo "✅<br>";
        
        echo "5. Cargando mPDF... ";
        require_once __DIR__ . '/vendor/autoload.php';
        if (!class_exists('Mpdf\Mpdf')) {
            echo "❌ mPDF no disponible<br>";
            exit;
        }
        echo "✅<br>";
        
        echo "6. Creando ProjectController... ";
        $projectController = new ProjectController();
        echo "✅<br>";
        
        echo "7. Verificando proyecto... ";
        $project_id = intval($_POST['project_id']);
        $project = $projectController->getProject($project_id);
        if (!$project) {
            echo "❌ Proyecto no encontrado<br>";
            exit;
        }
        echo "✅ Proyecto: " . htmlspecialchars($project['project_name']) . "<br>";
        
        echo "8. Generando PDF simple... ";
        
        // Generar PDF básico sin usar ReportController
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        
        $html = '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Test</title></head>
<body>
    <h1>Reporte de: ' . htmlspecialchars($project['project_name']) . '</h1>
    <p>Participantes: ' . htmlspecialchars($_POST['participants']) . '</p>
    <p>Template: ' . htmlspecialchars($_POST['template']) . '</p>
    <p>Fecha: ' . date('Y-m-d H:i:s') . '</p>
</body>
</html>';
        
        $mpdf->WriteHTML($html);
        $pdfContent = $mpdf->Output('', 'S');
        
        echo "✅<br>";
        
        echo "<p style='color: green;'>🎉 PDF generado exitosamente!</p>";
        echo "<p>Tamaño: " . number_format(strlen($pdfContent) / 1024, 2) . " KB</p>";
        
        // Ofrecer descarga
        echo '<p><a href="?download=1&' . http_build_query($_POST) . '" target="_blank">📄 Descargar PDF</a></p>';
        
    } catch (Exception $e) {
        echo "❌<br>";
        echo "<p style='color: red;'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
        echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
        echo "<pre style='background: #f5f5f5; padding: 10px;'>" . $e->getTraceAsString() . "</pre>";
    }
} else if (isset($_GET['download'])) {
    // Modo descarga
    try {
        require_once __DIR__ . '/config/database.php';
        require_once __DIR__ . '/Controllers/AuthController.php';
        require_once __DIR__ . '/Controllers/ProjectController.php';
        require_once __DIR__ . '/vendor/autoload.php';
        
        if (!AuthController::isLoggedIn()) {
            die("Usuario no autenticado");
        }
        
        $projectController = new ProjectController();
        $project = $projectController->getProject(intval($_GET['project_id']));
        
        if (!$project) {
            die("Proyecto no encontrado");
        }
        
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
        
        $html = '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Reporte</title></head>
<body>
    <h1>Reporte de: ' . htmlspecialchars($project['project_name']) . '</h1>
    <p>Participantes: ' . htmlspecialchars($_GET['participants']) . '</p>
    <p>Template: ' . htmlspecialchars($_GET['template']) . '</p>
    <p>Fecha: ' . date('Y-m-d H:i:s') . '</p>
</body>
</html>';
        
        $mpdf->WriteHTML($html);
        $pdfContent = $mpdf->Output('', 'S');
        
        if (ob_get_level()) ob_clean();
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Reporte_Debug_' . date('Y-m-d') . '.pdf"');
        header('Content-Length: ' . strlen($pdfContent));
        echo $pdfContent;
        exit;
        
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    echo "<h3>🧪 Test Manual</h3>";
    echo '<form method="POST">
        <p>Project ID: <input name="project_id" value="13" required></p>
        <p>Participantes: <input name="participants" value="Sebastian Nicolas" required></p>
        <p>Template: <input name="template" value="elegant" required></p>
        <p>Action: <input name="action" value="generate_pdf" required></p>
        <button type="submit">Test Debug</button>
    </form>';
}
?>