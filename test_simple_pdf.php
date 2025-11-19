<?php
session_start();

// Test básico de generación PDF sin includes problemáticos
echo "🧪 Test de generación PDF básico<br><br>";

if ($_POST) {
    echo "<h3>📤 Datos recibidos:</h3>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    try {
        // Cargar mPDF
        require_once __DIR__ . '/vendor/autoload.php';
        
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'orientation' => 'P'
        ]);
        
        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        .info { background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .label { font-weight: bold; color: #444; }
    </style>
</head>
<body>
    <h1>🎯 Reporte de Planificación Estratégica - TEST</h1>
    <div class="info">
        <p><span class="label">Proyecto ID:</span> ' . htmlspecialchars($_POST['project_id'] ?? 'N/A') . '</p>
        <p><span class="label">Participantes:</span> ' . htmlspecialchars($_POST['participants'] ?? 'N/A') . '</p>
        <p><span class="label">Template:</span> ' . htmlspecialchars($_POST['template'] ?? 'N/A') . '</p>
        <p><span class="label">Fecha de generación:</span> ' . date('d/m/Y H:i:s') . '</p>
    </div>
    <h2>📋 Contenido del Reporte</h2>
    <p>Este es un PDF de prueba generado correctamente con mPDF v8.1.0</p>
    <p>El sistema está funcionando y puede generar PDFs válidos.</p>
</body>
</html>';
        
        $mpdf->WriteHTML($html);
        $pdfContent = $mpdf->Output('', 'S');
        
        // Limpiar cualquier output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        // Enviar headers correctos para PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Reporte_Test_' . date('Y-m-d') . '.pdf"');
        header('Content-Length: ' . strlen($pdfContent));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        echo $pdfContent;
        exit;
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test PDF</title>
</head>
<body>
    <h2>🧪 Test Generador PDF</h2>
    <form method="POST">
        <p>
            <label>Project ID:</label><br>
            <input type="text" name="project_id" value="13">
        </p>
        <p>
            <label>Participantes:</label><br>
            <input type="text" name="participants" value="Sebastian Nicolas">
        </p>
        <p>
            <label>Template:</label><br>
            <input type="text" name="template" value="modern">
        </p>
        <p>
            <button type="submit">Generar PDF Test</button>
        </p>
    </form>
</body>
</html>