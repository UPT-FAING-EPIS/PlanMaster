<?php
// Prueba rápida para verificar la generación del PDF con justificación
session_start();

// Simular un ID de proyecto para la prueba
$project_id = 1; // Cambia este número por un ID de proyecto real que tengas

if (!$project_id) {
    echo "<h2>Error: No hay ID de proyecto especificado</h2>";
    echo "<p>Por favor, edita el archivo test_pdf_justification.php y configura un project_id válido en la línea 6.</p>";
    exit;
}

echo "<h2>Prueba de Generación de PDF con Justificación</h2>";
echo "<p>Proyecto ID: $project_id</p>";
echo "<a href='generate_pdf_direct.php?project_id=$project_id' target='_blank' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Generar PDF</a>";
echo "<br><br>";
echo "<p><strong>Mejoras implementadas:</strong></p>";
echo "<ul>";
echo "<li>✅ Texto justificado en todas las secciones</li>";
echo "<li>✅ Separación silábica automática (hyphens: auto)</li>";
echo "<li>✅ Estructura de tablas HTML para FODA mejorada</li>";
echo "<li>✅ Eliminación de emojis de títulos</li>";
echo "<li>✅ Formato profesional tipo Word</li>";
echo "<li>✅ Diagnóstico de Cadena de Valor con métricas</li>";
echo "<li>✅ Matriz BCG con visualización por cuadrantes</li>";
echo "<li>✅ Resultados del Análisis de Porter con competitividad</li>";
echo "</ul>";

echo "<p><strong>Verifica en el PDF generado:</strong></p>";
echo "<ul>";
echo "<li>El texto debe aparecer justificado (alineado a ambos márgenes)</li>";
echo "<li>La sección FODA debe mostrar el texto sin cortes</li>";
echo "<li>Los párrafos deben verse profesionales</li>";
echo "<li>No deben aparecer emojis en los títulos</li>";
echo "<li>Las nuevas secciones deben aparecer con métricas y visualizaciones</li>";
echo "<li>La Cadena de Valor debe mostrar puntuación y potencial de mejora</li>";
echo "<li>La Matriz BCG debe mostrar productos por cuadrante</li>";
echo "<li>El Análisis Porter debe mostrar nivel de competitividad</li>";
echo "</ul>";
?>