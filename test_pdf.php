<?php
// Prueba simple de mPDF
require_once 'vendor/autoload.php';

try {
    echo "<h2>🧪 Prueba de mPDF</h2>";
    
    // Crear instancia de mPDF v8.1
    $mpdf = new \Mpdf\Mpdf([
        'format' => 'A4',
        'orientation' => 'P'
    ]);
    
    $html = '
    <h1 style="color: #667eea; text-align: center;">Prueba de PDF</h1>
    <p>Si ves este mensaje, mPDF está funcionando correctamente!</p>
    <p><strong>Fecha:</strong> ' . date('d/m/Y H:i:s') . '</p>
    ';
    
    $mpdf->WriteHTML($html);
    
    // Guardar archivo de prueba
    $mpdf->Output('test.pdf', 'F');
    
    echo "<p style='color: green;'>✅ mPDF funciona correctamente!</p>";
    echo "<p><a href='test.pdf' target='_blank'>📄 Ver PDF de prueba</a></p>";
    echo "<p><a href='Views/Users/templates.php'>🎯 Ir a Plantillas</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>