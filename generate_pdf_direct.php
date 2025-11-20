<?php
session_start();

// Generador PDF directo sin verificaciones complejas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate_pdf') {
    try {
        // Cargar dependencias básicas
        require_once __DIR__ . '/vendor/autoload.php';
        require_once __DIR__ . '/config/database.php';
        
        // Obtener datos del formulario
        $project_id = intval($_POST['project_id']);
        $participants = $_POST['participants'] ?? '';
        $template = $_POST['template'] ?? 'default';
        
        // Conectar a la base de datos
        $database = new Database();
        $db = $database->getConnection();
        
        if (!$db) {
            die("Error: No se pudo conectar a la base de datos");
        }
        
        // Obtener datos del proyecto
        $query = "SELECT project_name, company_name FROM strategic_projects WHERE id = ?";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            die("Error preparando consulta: " . $db->error);
        }
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $project = $stmt->get_result()->fetch_assoc();
        
        if (!$project) {
            die("Error: No se encontró el proyecto con ID: " . $project_id);
        }
        
        if (!$project) {
            throw new Exception("Proyecto no encontrado");
        }
        
        // Obtener misión
        $query = "SELECT mission_text FROM project_mission WHERE project_id = ?";
        $stmt = $db->prepare($query);
        $mission = null;
        if ($stmt) {
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $mission = $stmt->get_result()->fetch_assoc();
        }
        
        // Obtener visión
        $query = "SELECT vision_text FROM project_vision WHERE project_id = ?";
        $stmt = $db->prepare($query);
        $vision = null;
        if ($stmt) {
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $vision = $stmt->get_result()->fetch_assoc();
        }
        
        // Obtener valores
        $query = "SELECT value_text FROM project_values WHERE project_id = ? ORDER BY value_order";
        $stmt = $db->prepare($query);
        $values = [];
        if ($stmt) {
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $values_result = $stmt->get_result();
            while ($row = $values_result->fetch_assoc()) {
                $values[] = $row['value_text'];
            }
        }
        
        // Obtener objetivos estratégicos generales
        $query = "SELECT objective_title FROM project_strategic_objectives WHERE project_id = ? ORDER BY objective_order";
        $stmt = $db->prepare($query);
        $objectives = ['general' => [], 'specific' => []];
        if ($stmt) {
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $objectives_result = $stmt->get_result();
            while ($row = $objectives_result->fetch_assoc()) {
                $objectives['general'][] = $row['objective_title'];
            }
        }
        
        // Obtener objetivos específicos
        $query = "SELECT objective_title FROM project_specific_objectives WHERE strategic_objective_id IN (SELECT id FROM project_strategic_objectives WHERE project_id = ?) ORDER BY objective_order";
        $stmt = $db->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $specific_result = $stmt->get_result();
            while ($row = $specific_result->fetch_assoc()) {
                $objectives['specific'][] = $row['objective_title'];
            }
        }
        
        // Obtener análisis FODA
        $query = "SELECT type, item_text FROM project_foda_analysis WHERE project_id = ? ORDER BY type, item_order";
        $stmt = $db->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $foda_result = $stmt->get_result();
            $foda = ['fortalezas' => [], 'debilidades' => [], 'oportunidades' => [], 'amenazas' => []];
            while ($row = $foda_result->fetch_assoc()) {
                // Mapear los tipos de la BD a las claves del array
                switch ($row['type']) {
                    case 'fortaleza':
                        $foda['fortalezas'][] = $row['item_text'];
                        break;
                    case 'debilidad':
                        $foda['debilidades'][] = $row['item_text'];
                        break;
                    case 'oportunidad':
                        $foda['oportunidades'][] = $row['item_text'];
                        break;
                    case 'amenaza':
                        $foda['amenazas'][] = $row['item_text'];
                        break;
                }
            }
        } else {
            $foda = ['fortalezas' => [], 'debilidades' => [], 'oportunidades' => [], 'amenazas' => []];
        }
        
        // Calcular estrategia dominante basada en sumas de relaciones estratégicas
        $query = "SELECT relation_type, SUM(value_score) as total_score FROM project_strategic_relations WHERE project_id = ? GROUP BY relation_type";
        $stmt = $db->prepare($query);
        $strategy_scores = [];
        $dominant_strategy = null;
        
        if ($stmt) {
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $scores_result = $stmt->get_result();
            
            while ($row = $scores_result->fetch_assoc()) {
                $strategy_scores[$row['relation_type']] = intval($row['total_score']);
            }
            
            if (!empty($strategy_scores)) {
                $max_score = max($strategy_scores);
                $max_type = array_search($max_score, $strategy_scores);
                
                $strategies = [
                    'FO' => ['name' => 'Estrategia Ofensiva', 'description' => 'Deberá adoptar estrategias de crecimiento. Las fortalezas de la organización pueden aprovecharse para capitalizar las oportunidades del entorno.'],
                    'FA' => ['name' => 'Estrategia Defensiva', 'description' => 'Deberá adoptar estrategias defensivas. Use las fortalezas para evitar o reducir el impacto de las amenazas externas.'],
                    'DO' => ['name' => 'Estrategia Adaptativa', 'description' => 'Deberá adoptar estrategias de reorientación. Supere las debilidades internas aprovechando las oportunidades externas.'],
                    'DA' => ['name' => 'Estrategia de Supervivencia', 'description' => 'Deberá adoptar estrategias de supervivencia. Minimice las debilidades y evite las amenazas para mantener la competitividad.']
                ];
                
                if (isset($strategies[$max_type])) {
                    $dominant_strategy = [
                        'strategy_name' => $strategies[$max_type]['name'],
                        'strategy_description' => $strategies[$max_type]['description'],
                        'score' => $max_score,
                        'type' => $max_type
                    ];
                }
            }
        }
        
        // Obtener datos de Cadena de Valor
        $query = "SELECT rating FROM project_value_chain WHERE project_id = ?";
        $stmt = $db->prepare($query);
        $value_chain_data = null;
        if ($stmt) {
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $value_chain_result = $stmt->get_result();
            $total_score = 0;
            $count = 0;
            while ($row = $value_chain_result->fetch_assoc()) {
                $total_score += intval($row['rating']);
                $count++;
            }
            if ($count > 0) {
                $max_possible = $count * 4; // Máximo 4 puntos por pregunta
                $percentage = ($total_score / $max_possible) * 100;
                $improvement_potential = 100 - $percentage;
                
                // LÓGICA EXACTA DE value-chain.php (basada en potencial de mejora, no en porcentaje total)
                if ($improvement_potential >= 70) {
                    $level = "Alto Potencial";
                    $message = "🚀 Alto Potencial de Mejora: Su empresa tiene excelentes oportunidades para optimizar la gestión comercial. Priorice las áreas con menor puntuación.";
                } elseif ($improvement_potential >= 40) {
                    $level = "Potencial Moderado";
                    $message = "📈 Potencial Moderado: Hay áreas importantes para mejorar. Enfóquese en fortalecer los procesos comerciales clave.";
                } elseif ($improvement_potential >= 20) {
                    $level = "Gestión Sólida";
                    $message = "✅ Gestión Sólida: Su empresa maneja bien la mayoría de aspectos comerciales. Identifique áreas específicas para la excelencia.";
                } else {
                    $level = "Excelente Gestión";
                    $message = "🏆 Excelente Gestión: Su empresa tiene una gestión comercial excepcional. Mantenga estos estándares y busque innovación continua.";
                }
                
                $value_chain_data = [
                    'total_score' => $total_score,
                    'max_possible' => $max_possible,
                    'percentage' => $percentage,
                    'improvement_potential' => $improvement_potential,
                    'level' => $level,
                    'message' => $message
                ];
            }
        }
        
        // Obtener datos de Matriz BCG con nombres de productos
        $query = "SELECT p.product_name, r.bcg_quadrant 
                 FROM project_bcg_products p 
                 INNER JOIN project_bcg_matrix_results r ON p.id = r.product_id 
                 WHERE p.project_id = ? 
                 ORDER BY r.bcg_quadrant, p.product_name";
        $stmt = $db->prepare($query);
        $bcg_data = null;
        if ($stmt) {
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $bcg_result = $stmt->get_result();
            $bcg_quadrants = [
                'estrella' => ['count' => 0, 'products' => []], 
                'interrogante' => ['count' => 0, 'products' => []], 
                'vaca_lechera' => ['count' => 0, 'products' => []], 
                'perro' => ['count' => 0, 'products' => []]
            ];
            $has_data = false;
            
            while ($row = $bcg_result->fetch_assoc()) {
                $quadrant = $row['bcg_quadrant'];
                $product_name = $row['product_name'];
                
                if (isset($bcg_quadrants[$quadrant])) {
                    $bcg_quadrants[$quadrant]['count']++;
                    $bcg_quadrants[$quadrant]['products'][] = $product_name;
                    $has_data = true;
                }
            }
            
            if ($has_data) {
                $bcg_data = $bcg_quadrants;
            }
        }
        
        // Obtener datos de Análisis Porter
        $query = "SELECT selected_value FROM project_porter_analysis WHERE project_id = ?";
        $stmt = $db->prepare($query);
        $porter_data = null;
        if ($stmt) {
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $porter_result = $stmt->get_result();
            $total_score = 0;
            $count = 0;
            while ($row = $porter_result->fetch_assoc()) {
                $total_score += intval($row['selected_value']);
                $count++;
            }
            
            if ($count > 0) {
                $max_possible = $count * 5; // Máximo 5 puntos por factor (escala 1-5)
                $average = $total_score / $count;
                $percentage = ($total_score / $max_possible) * 100;
                
                // LÓGICA EXACTA DE PorterAnalysis.php líneas 317-333
                if ($percentage >= 80) {
                    $competitivity_level = "Muy Favorable";
                    $message = "Estamos en una situación excelente para la empresa.";
                } elseif ($percentage >= 60) {
                    $competitivity_level = "Favorable";
                    $message = "La situación actual del mercado es favorable a la empresa.";
                } elseif ($percentage >= 40) {
                    $competitivity_level = "Medio";
                    $message = "Estamos en un mercado de competitividad relativamente alta, pero con ciertas modificaciones en el producto y la política comercial de la empresa, podría encontrarse un nicho de mercado.";
                } else {
                    $competitivity_level = "Hostil";
                    $message = "Estamos en un mercado altamente competitivo, en el que es muy difícil hacerse un hueco en el mercado.";
                }
                
                $porter_data = [
                    'total_score' => $total_score,
                    'max_possible' => $max_possible,
                    'average' => round($average, 2),
                    'percentage' => round($percentage, 1),
                    'competitivity_level' => $competitivity_level,
                    'message' => $message
                ];
            }
        }
        
        // Obtener datos de Análisis PEST
        $query = "SELECT question_number, rating FROM project_pest_analysis WHERE project_id = ?";
        $stmt = $db->prepare($query);
        $pest_data = null;
        if ($stmt) {
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $pest_result = $stmt->get_result();
            $pest_responses = [];
            
            while ($row = $pest_result->fetch_assoc()) {
                $pest_responses[$row['question_number']] = intval($row['rating']);
            }
            
            if (!empty($pest_responses)) {
                // Calcular puntuaciones por categoría
                $categories = [
                    'social' => ['name' => 'SOCIAL Y DEMOGRÁFICO', 'icon' => '👥', 'questions' => range(1, 5)],
                    'politico' => ['name' => 'POLÍTICO', 'icon' => '🏛️', 'questions' => range(6, 10)],
                    'economico' => ['name' => 'ECONÓMICO', 'icon' => '💰', 'questions' => range(11, 15)],
                    'tecnologico' => ['name' => 'TECNOLÓGICO', 'icon' => '🔧', 'questions' => range(16, 20)],
                    'medioambiental' => ['name' => 'MEDIOAMBIENTAL', 'icon' => '🌱', 'questions' => range(21, 25)]
                ];
                
                $pest_scores = [];
                foreach ($categories as $key => $category) {
                    $total_score = 0;
                    $answered_questions = 0;
                    
                    foreach ($category['questions'] as $q_num) {
                        if (isset($pest_responses[$q_num])) {
                            $total_score += $pest_responses[$q_num];
                            $answered_questions++;
                        }
                    }
                    
                    $max_possible = $answered_questions * 4; // Máximo 4 por pregunta
                    $percentage = $max_possible > 0 ? ($total_score / $max_possible) * 100 : 0;
                    
                    $pest_scores[$key] = [
                        'name' => $category['name'],
                        'icon' => $category['icon'],
                        'total_score' => $total_score,
                        'max_possible' => $max_possible,
                        'percentage' => round($percentage),
                        'questions' => 'Preguntas ' . min($category['questions']) . '-' . max($category['questions'])
                    ];
                }
                
                $pest_data = $pest_scores;
            }
        }
        
        // Obtener matriz CAME
        $query = "SELECT action_type, action_text FROM project_came_matrix WHERE project_id = ? ORDER BY action_type, action_number";
        $stmt = $db->prepare($query);
        if ($stmt) {
            $stmt->bind_param("i", $project_id);
            $stmt->execute();
            $came_result = $stmt->get_result();
            $came_actions = ['C' => [], 'A' => [], 'M' => [], 'E' => []];
            while ($row = $came_result->fetch_assoc()) {
                $came_actions[$row['action_type']][] = $row['action_text'];
            }
        } else {
            $came_actions = ['C' => [], 'A' => [], 'M' => [], 'E' => []];
        }
        
        // Generar HTML del PDF - Simplificado para mPDF
        $currentDate = date('d/m/Y');
        $templateColors = [
            'default' => '#667eea',
            'corporate' => '#2c3e50', 
            'modern' => '#667eea',
            'elegant' => '#1a1a2e'
        ];
        
        $primaryColor = $templateColors[$template] ?? $templateColors['default'];
        
        // Formatear participantes en una sola línea (reemplazar saltos con comas)
        $participantsFormatted = str_replace(['\r\n', '\r', '\n'], ', ', htmlspecialchars($participants));
        $participantsFormatted = preg_replace('/,\s*,+/', ',', $participantsFormatted); // Limpiar comas múltiples
        $participantsFormatted = trim($participantsFormatted, ', '); // Limpiar comas al inicio/final
        
        $html = '<html>
<head>
    <style>
        @page {
            margin-top: 30mm;
            margin-bottom: 15mm;
            margin-left: 20mm;
            margin-right: 20mm;
            header: page-header;
            footer: page-footer;
        }
        
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12pt; 
            margin: 0;
            line-height: 1.4;
        }
        
        /* Encabezado simple con mucha más separación */
        .header {
            border-bottom: 1px solid #ddd;
            padding: 10px 0 20px 0;
            margin-bottom: 25px;
        }
        
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .header td {
            padding: 5px;
            vertical-align: middle;
            color: #333;
        }
        
        .header .left {
            text-align: left;
            font-weight: bold;
            font-size: 14pt;
            color: ' . $primaryColor . ';
        }
        
        .header .right {
            text-align: right;
            font-size: 10pt;
            color: #666;
        }
        
        /* Pie de página simple */
        .footer {
            border-top: 1px solid #ddd;
            padding: 8px 0;
            color: #333;
            font-size: 10pt;
        }
        
        /* Carátula centrada vertical y horizontalmente - Fondo blanco */
        .cover { 
            background-color: white;
            text-align: center; 
            height: 250mm;
            position: relative;
            padding: 0;
            display: table;
            width: 100%;
        }
        
        .cover-content {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding: 20px;
        }
        
        .cover h1 { 
            font-size: 32pt; 
            margin: 20px 0; 
            font-weight: bold;
            color: ' . $primaryColor . ';
            line-height: 1.2;
        }
        
        .cover h2 { 
            font-size: 24pt; 
            margin: 30px 0; 
            font-weight: 600;
            color: ' . $primaryColor . ';
        }
        
        .info-box { 
            background-color: white;
            color: #333; 
            padding: 40px; 
            margin: 40px auto;
            max-width: 500px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            text-align: center;
        }
        
        .info-line { 
            margin: 20px 0;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
            text-align: center;
        }
        
        .info-line:last-child {
            border-bottom: none;
        }
        
        .label { 
            font-weight: bold; 
            color: ' . $primaryColor . ';
            display: inline;
        }
        
        .value {
            color: #333;
            display: inline;
        }
        
        .footer-text {
            color: #333;
            margin-top: 30px;
            font-size: 14pt;
            font-weight: 500;
        }
        
        /* Contenido normal (NO centrado) con mucha más separación */
        .content { 
            padding: 30px 0 0 0;
            text-align: left;
        }
        
        .section { 
            margin-bottom: 35px;
            page-break-inside: avoid;
        }
        
        .section h3 { 
            color: ' . $primaryColor . '; 
            border-bottom: 3px solid ' . $primaryColor . '; 
            padding-bottom: 8px;
            font-size: 18pt;
            margin-bottom: 15px;
            text-align: left;
        }
        
        .section p {
            text-align: justify;
            line-height: 1.6;
            margin-bottom: 15px;
            hyphens: auto;
        }
        
        .section ul {
            margin: 15px 0;
            padding-left: 25px;
        }
        
        .section li {
            margin-bottom: 10px;
            line-height: 1.5;
            text-align: justify;
            hyphens: auto;
        }
        
        .content {
            text-align: justify;
        }
        
        .section table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .section td {
            padding: 5px;
            vertical-align: top;
        }
        
        .page-break { 
            page-break-before: always; 
        }
    </style>
</head>
<body>';
        
        // Encabezado y pie de página simples
        $html .= '
        <htmlpageheader name="page-header">
            <div class="header">
                <table>
                    <tr>
                        <td class="left">PLANMASTER</td>
                        <td class="right">Sistema de Planificación Estratégica</td>
                    </tr>
                </table>
            </div>
        </htmlpageheader>
        
        <htmlpagefooter name="page-footer">
            <div class="footer">
                Página {PAGENO} de {nbpg}
            </div>
        </htmlpagefooter>';
        
        // Carátula
        $html .= '<div class="cover">
            <div class="cover-content">
                <h1>RESUMEN EJECUTIVO DEL<br>PLAN ESTRATÉGICO</h1>
                <h2>' . htmlspecialchars($project['project_name']) . '</h2>
                
                <div class="info-box">
                    <div class="info-line">
                        <span class="label">Empresa/Proyecto:</span> <span class="value">' . htmlspecialchars($project['company_name']) . '</span>
                    </div>
                    <div class="info-line">
                        <span class="label">Fecha:</span> <span class="value">' . $currentDate . '</span>
                    </div>
                    <div class="info-line">
                        <span class="label">Participantes:</span> <span class="value">' . $participantsFormatted . '</span>
                    </div>
                </div>
                
                <p class="footer-text">PlanMaster - Sistema de Planificación Estratégica</p>
            </div>
        </div>';
        
        // Contenido
        $html .= '<div class="page-break"></div>';
        $html .= '<div class="content">';
        
        // Introducción
        $html .= '<div class="section">
            <h3>INTRODUCCIÓN</h3>
            <p class="justified-text">El presente documento constituye el resumen ejecutivo del plan estratégico para <strong>' . htmlspecialchars($project['company_name']) . '</strong>, elaborado mediante un proceso metodológico estructurado que permite establecer las directrices fundamentales para el desarrollo organizacional.</p>
            
            <p class="justified-text"><strong>Proceso de Planificación Estratégica Aplicado:</strong></p>
            
            <div style="background-color: #f8f9fa; padding: 20px; margin: 15px 0; border-left: 4px solid ' . $primaryColor . ';">
                <table style="width: 100%; font-size: 11pt;">
                    <tr><td style="width: 30px; font-weight: bold; color: ' . $primaryColor . ';">1.</td><td><strong>Misión:</strong> Define el propósito fundamental de la organización</td></tr>
                    <tr><td style="width: 30px; font-weight: bold; color: ' . $primaryColor . ';">2.</td><td><strong>Visión:</strong> Establece hacia dónde se dirige la empresa</td></tr>
                    <tr><td style="width: 30px; font-weight: bold; color: ' . $primaryColor . ';">3.</td><td><strong>Valores:</strong> Los principios que guían las decisiones organizacionales</td></tr>
                    <tr><td style="width: 30px; font-weight: bold; color: ' . $primaryColor . ';">4.</td><td><strong>Objetivos:</strong> Metas específicas y medibles a alcanzar</td></tr>
                    <tr><td style="width: 30px; font-weight: bold; color: ' . $primaryColor . ';">5.</td><td><strong>Análisis Interno y Externo:</strong> Evaluación completa del entorno empresarial</td></tr>
                    <tr><td style="width: 30px; font-weight: bold; color: ' . $primaryColor . ';">6.</td><td><strong>Cadena de Valor:</strong> Análisis de procesos que agregan valor</td></tr>
                    <tr><td style="width: 30px; font-weight: bold; color: ' . $primaryColor . ';">7.</td><td><strong>Matriz BCG:</strong> Análisis de crecimiento y participación de mercado</td></tr>
                    <tr><td style="width: 30px; font-weight: bold; color: ' . $primaryColor . ';">8.</td><td><strong>Matriz de Porter:</strong> Análisis del microentorno competitivo</td></tr>
                    <tr><td style="width: 30px; font-weight: bold; color: ' . $primaryColor . ';">9.</td><td><strong>Análisis PEST:</strong> Factores políticos, económicos, sociales y tecnológicos</td></tr>
                    <tr><td style="width: 30px; font-weight: bold; color: ' . $primaryColor . ';">10.</td><td><strong>Identificación de Estrategias:</strong> Desarrollo de estrategias competitivas</td></tr>
                    <tr><td style="width: 30px; font-weight: bold; color: ' . $primaryColor . ';">11.</td><td><strong>Matriz CAME:</strong> Corregir, Afrontar, Mantener y Explotar oportunidades</td></tr>
                </table>
            </div>
            
            <p class="justified-text">Este enfoque integral garantiza una planificación estratégica robusta y fundamentada en análisis profundos del contexto organizacional.</p>
        </div>';
        
        if ($mission && !empty($mission['mission_text'])) {
            $html .= '<div class="section">
                <h3>MISIÓN</h3>
                <p class="justified-text">' . nl2br(htmlspecialchars($mission['mission_text'])) . '</p>
            </div>';
        }
        
        if ($vision && !empty($vision['vision_text'])) {
            $html .= '<div class="section">
                <h3>VISIÓN</h3>
                <p class="justified-text">' . nl2br(htmlspecialchars($vision['vision_text'])) . '</p>
            </div>';
        }
        
        if (!empty($values)) {
            $html .= '<div class="section">
                <h3>VALORES ORGANIZACIONALES</h3>
                <p class="justified-text">Los valores que rigen nuestras acciones y decisiones son:</p>
                <ul>';
            foreach ($values as $value) {
                $html .= '<li class="justified-text"><strong>' . htmlspecialchars($value) . '</strong></li>';
            }
            $html .= '</ul></div>';
        }
        
        // Objetivos Estratégicos
        if (!empty($objectives['general']) || !empty($objectives['specific'])) {
            $html .= '<div class="section">
                <h3>OBJETIVOS ESTRATÉGICOS</h3>
                <p class="justified-text">Los objetivos que guían el desarrollo estratégico de la organización son:</p>';
            
            if (!empty($objectives['general'])) {
                $html .= '<h4 style="color: ' . $primaryColor . '; margin-top: 20px;">Objetivos Generales:</h4>
                <ul>';
                foreach ($objectives['general'] as $objective) {
                    $html .= '<li class="justified-text">' . htmlspecialchars($objective) . '</li>';
                }
                $html .= '</ul>';
            }
            
            if (!empty($objectives['specific'])) {
                $html .= '<h4 style="color: ' . $primaryColor . '; margin-top: 20px;">Objetivos Específicos:</h4>
                <ul>';
                foreach ($objectives['specific'] as $objective) {
                    $html .= '<li class="justified-text">' . htmlspecialchars($objective) . '</li>';
                }
                $html .= '</ul>';
            }
            $html .= '</div>';
        }
        
        // Diagnóstico de Cadena de Valor
        if ($value_chain_data) {
            $html .= '<div class="section">
                <h3>DIAGNÓSTICO DE CADENA DE VALOR</h3>
                <p class="justified-text">Evaluación integral de los procesos y actividades que agregan valor en la organización:</p>
                
                <div style="background-color: #f8f9fa; padding: 25px; margin: 20px 0; border-radius: 12px; text-align: center;">
                    <h4 style="color: ' . $primaryColor . '; margin-bottom: 20px; font-size: 16pt;">Resultados del Diagnóstico</h4>
                    
                    <div style="display: flex; justify-content: space-around; align-items: center; flex-wrap: wrap;">
                        <div style="text-align: center; margin: 10px;">
                            <div style="font-size: 36pt; font-weight: bold; color: ' . $primaryColor . '; line-height: 1;">' . $value_chain_data['total_score'] . '</div>
                            <div style="font-size: 11pt; color: #666; margin-top: 5px;">Puntuación Total</div>
                            <div style="font-size: 10pt; color: #999;">de ' . $value_chain_data['max_possible'] . ' puntos máximos</div>
                        </div>
                        
                        <div style="text-align: center; margin: 10px;">
                            <div style="font-size: 36pt; font-weight: bold; color: #ff9800; line-height: 1;">' . round($value_chain_data['improvement_potential']) . '%</div>
                            <div style="font-size: 11pt; color: #666; margin-top: 5px;">Potencial de Mejora</div>
                            <div style="font-size: 10pt; color: #999;">Oportunidad de crecimiento</div>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px; padding: 15px; background-color: white; border-radius: 8px; border-left: 4px solid ' . $primaryColor . ';">
                        <p style="margin: 0; font-size: 12pt; color: #555; line-height: 1.5;">' . htmlspecialchars($value_chain_data['message']) . '</p>
                    </div>
                </div>
            </div>';
        }
        
        // Matriz BCG - Visualización
        if ($bcg_data) {
            $html .= '<div class="section">
                <h3>MATRIZ BCG - VISUALIZACIÓN</h3>
                <p class="justified-text">La matriz BCG se actualiza automáticamente basándose en los datos ingresados. <strong>TCM ≥ 10% = Alto crecimiento | PRM ≥ 1.0 = Alta participación relativa</strong></p>
                
                <div style="background-color: #f8f9fa; padding: 25px; margin: 20px 0; border-radius: 12px;">
                    <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                        <tr style="height: auto; min-height: 120px;">
                            <td style="width: 50%; padding: 20px; text-align: center; background-color: #e3f2fd; border: 2px solid #2196f3; vertical-align: top;">
                                <h4 style="color: #1976d2; margin: 0; font-size: 14pt;">INTERROGANTE</h4>
                                <div style="font-size: 24pt; font-weight: bold; color: #1976d2; margin: 10px 0;">' . $bcg_data['interrogante']['count'] . '</div>
                                <div style="font-size: 10pt; color: #666; margin-bottom: 10px;">productos</div>';
                                if (!empty($bcg_data['interrogante']['products'])) {
                                    $html .= '<div style="font-size: 9pt; color: #555; text-align: left; margin-top: 10px;">
                                        <strong>Productos:</strong><br>
                                        • ' . implode('<br>• ', array_map('htmlspecialchars', $bcg_data['interrogante']['products'])) . '
                                    </div>';
                                }
                            $html .= '</td>
                            <td style="width: 50%; padding: 20px; text-align: center; background-color: #fff3e0; border: 2px solid #ff9800; vertical-align: top;">
                                <h4 style="color: #f57c00; margin: 0; font-size: 14pt;">ESTRELLA</h4>
                                <div style="font-size: 24pt; font-weight: bold; color: #f57c00; margin: 10px 0;">' . $bcg_data['estrella']['count'] . '</div>
                                <div style="font-size: 10pt; color: #666; margin-bottom: 10px;">productos</div>';
                                if (!empty($bcg_data['estrella']['products'])) {
                                    $html .= '<div style="font-size: 9pt; color: #555; text-align: left; margin-top: 10px;">
                                        <strong>Productos:</strong><br>
                                        • ' . implode('<br>• ', array_map('htmlspecialchars', $bcg_data['estrella']['products'])) . '
                                    </div>';
                                }
                            $html .= '</td>
                        </tr>
                        <tr style="height: auto; min-height: 120px;">
                            <td style="width: 50%; padding: 20px; text-align: center; background-color: #ffebee; border: 2px solid #f44336; vertical-align: top;">
                                <h4 style="color: #d32f2f; margin: 0; font-size: 14pt;">PERRO</h4>
                                <div style="font-size: 24pt; font-weight: bold; color: #d32f2f; margin: 10px 0;">' . $bcg_data['perro']['count'] . '</div>
                                <div style="font-size: 10pt; color: #666; margin-bottom: 10px;">productos</div>';
                                if (!empty($bcg_data['perro']['products'])) {
                                    $html .= '<div style="font-size: 9pt; color: #555; text-align: left; margin-top: 10px;">
                                        <strong>Productos:</strong><br>
                                        • ' . implode('<br>• ', array_map('htmlspecialchars', $bcg_data['perro']['products'])) . '
                                    </div>';
                                }
                            $html .= '</td>
                            <td style="width: 50%; padding: 20px; text-align: center; background-color: #e8f5e8; border: 2px solid #4caf50; vertical-align: top;">
                                <h4 style="color: #388e3c; margin: 0; font-size: 14pt;">VACA LECHERA</h4>
                                <div style="font-size: 24pt; font-weight: bold; color: #388e3c; margin: 10px 0;">' . $bcg_data['vaca_lechera']['count'] . '</div>
                                <div style="font-size: 10pt; color: #666; margin-bottom: 10px;">productos</div>';
                                if (!empty($bcg_data['vaca_lechera']['products'])) {
                                    $html .= '<div style="font-size: 9pt; color: #555; text-align: left; margin-top: 10px;">
                                        <strong>Productos:</strong><br>
                                        • ' . implode('<br>• ', array_map('htmlspecialchars', $bcg_data['vaca_lechera']['products'])) . '
                                    </div>';
                                }
                            $html .= '</td>
                            </td>
                        </tr>
                    </table>
                    
                    <div style="text-align: center; margin-top: 15px;">
                        <div style="font-size: 12pt; font-weight: bold; color: #666;">← PRM (Participación Relativa del Mercado) →</div>
                    </div>
                </div>
            </div>';
        }
        
        // Resultados del Análisis de Porter
        if ($porter_data) {
            $html .= '<div class="section">
                <h3>RESULTADOS DEL ANÁLISIS DE PORTER</h3>
                <p class="justified-text">Evaluación del entorno competitivo mediante el análisis de las cinco fuerzas de Porter:</p>
                
                <div style="background-color: #f8f9fa; padding: 25px; margin: 20px 0; border-radius: 12px; text-align: center;">
                    <h4 style="color: ' . $primaryColor . '; margin-bottom: 20px; font-size: 16pt;">Análisis de Competitividad</h4>
                    
                    <div style="display: flex; justify-content: space-around; align-items: center; flex-wrap: wrap;">
                        <div style="text-align: center; margin: 10px;">
                            <div style="font-size: 36pt; font-weight: bold; color: ' . $primaryColor . '; line-height: 1;">' . $porter_data['total_score'] . '</div>
                            <div style="font-size: 11pt; color: #666; margin-top: 5px;">Puntuación Total</div>
                            <div style="font-size: 10pt; color: #999;">de ' . $porter_data['max_possible'] . ' puntos máximos</div>
                        </div>
                        
                        <div style="text-align: center; margin: 10px;">
                            <div style="font-size: 36pt; font-weight: bold; color: #2196f3; line-height: 1;">' . $porter_data['average'] . '</div>
                            <div style="font-size: 11pt; color: #666; margin-top: 5px;">Promedio por Factor</div>
                            <div style="font-size: 10pt; color: #999;">Escala 1-5</div>
                        </div>
                        
                        <div style="text-align: center; margin: 10px;">
                            <div style="font-size: 36pt; font-weight: bold; color: #ff9800; line-height: 1;">' . $porter_data['percentage'] . '%</div>
                            <div style="font-size: 11pt; color: #666; margin-top: 5px;">Competitividad</div>
                            <div style="font-size: 10pt; color: #999;">' . $porter_data['competitivity_level'] . '</div>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px; padding: 15px; background-color: white; border-radius: 8px; border-left: 4px solid ' . $primaryColor . ';">
                        <p style="margin: 0; font-size: 12pt; color: #555; line-height: 1.5;">' . htmlspecialchars($porter_data['message']) . '</p>
                    </div>
                </div>
            </div>';
        }
        
        // Resultados del Diagnóstico PEST
        if ($pest_data) {
            $html .= '<div class="section">
                <h3>RESULTADOS DEL DIAGNÓSTICO PEST</h3>
                <p class="justified-text">Evaluación del impacto de cada factor en su entorno empresarial:</p>
                
                <div style="background-color: #f8f9fa; padding: 25px; margin: 20px 0; border-radius: 12px;">
                    <h4 style="color: ' . $primaryColor . '; margin-bottom: 25px; font-size: 16pt; text-align: center;">Análisis por Categorías PEST</h4>';
                    
                    foreach ($pest_data as $category) {
                        // Calcular el ancho de la barra basado en el porcentaje
                        $bar_width = $category['percentage'];
                        
                        // Color de la barra según el porcentaje
                        if ($category['percentage'] >= 80) {
                            $bar_color = '#4caf50'; // Verde
                        } elseif ($category['percentage'] >= 60) {
                            $bar_color = '#ff9800'; // Naranja
                        } elseif ($category['percentage'] >= 40) {
                            $bar_color = '#2196f3'; // Azul
                        } else {
                            $bar_color = '#f44336'; // Rojo
                        }
                        
                        $html .= '<div style="margin-bottom: 15px; padding: 12px; background-color: white; border-radius: 6px; border-left: 3px solid ' . $bar_color . '; page-break-inside: avoid;">
                            <table style="width: 100%; border-collapse: collapse; margin: 0;">
                                <tr>
                                    <td style="width: 15%; text-align: center; vertical-align: middle; padding: 0;">
                                        <span style="font-size: 16pt;">' . $category['icon'] . '</span>
                                    </td>
                                    <td style="width: 50%; vertical-align: middle; padding: 0 10px;">
                                        <div style="margin: 0;">
                                            <div style="font-size: 11pt; font-weight: bold; color: ' . $primaryColor . '; margin-bottom: 2px;">' . $category['name'] . '</div>
                                            <div style="font-size: 8pt; color: #666;">' . $category['questions'] . '</div>
                                        </div>
                                    </td>
                                    <td style="width: 35%; text-align: right; vertical-align: middle; padding: 0;">
                                        <div style="font-size: 14pt; font-weight: bold; color: ' . $bar_color . '; margin-bottom: 2px;">' . $category['total_score'] . ' /' . $category['max_possible'] . '</div>
                                        <div style="font-size: 8pt; color: #666;">puntos</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="padding-top: 8px; padding-bottom: 5px;">
                                        <div style="width: 100%; height: 10px; background-color: #e0e0e0; border-radius: 5px; position: relative; overflow: hidden;">
                                            <div style="height: 100%; background-color: ' . $bar_color . '; width: ' . $bar_width . '%; border-radius: 5px;"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="text-align: center; padding-top: 5px;">
                                        <span style="font-size: 12pt; font-weight: bold; color: ' . $bar_color . ';">' . $category['percentage'] . '%</span>
                                        <span style="font-size: 8pt; color: #666; margin-left: 5px;">de impacto</span>
                                    </td>
                                </tr>
                            </table>
                        </div>';
                    }
                    
                $html .= '</div>
            </div>';
        }
        
        // Análisis FODA
        if (!empty($foda['fortalezas']) || !empty($foda['debilidades']) || !empty($foda['oportunidades']) || !empty($foda['amenazas'])) {
            $html .= '<div class="section">
                <h3>ANÁLISIS FODA</h3>
                <p class="justified-text">El análisis de factores internos y externos de la organización revela:</p>
                
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr>
                        <td style="width: 50%; padding: 15px; vertical-align: top; border-right: 1px solid #eee;">
                            <h4 style="color: ' . $primaryColor . '; margin-bottom: 15px; font-size: 14pt; border-bottom: 2px solid ' . $primaryColor . '; padding-bottom: 5px;">Fortalezas</h4>
                            <p style="font-size: 10pt; color: #666; margin-bottom: 10px; font-style: italic;">(Factores Internos Positivos)</p>
                            <ul style="margin: 0; padding-left: 20px;">';
            if (!empty($foda['fortalezas'])) {
                foreach ($foda['fortalezas'] as $item) {
                    $html .= '<li style="margin-bottom: 8px; line-height: 1.4; text-align: justify;">' . htmlspecialchars($item) . '</li>';
                }
            } else {
                $html .= '<li style="font-style: italic; color: #999;">No se han identificado fortalezas específicas.</li>';
            }
            $html .= '</ul>
                        </td>
                        <td style="width: 50%; padding: 15px; vertical-align: top;">
                            <h4 style="color: ' . $primaryColor . '; margin-bottom: 15px; font-size: 14pt; border-bottom: 2px solid ' . $primaryColor . '; padding-bottom: 5px;">Oportunidades</h4>
                            <p style="font-size: 10pt; color: #666; margin-bottom: 10px; font-style: italic;">(Factores Externos Positivos)</p>
                            <ul style="margin: 0; padding-left: 20px;">';
            if (!empty($foda['oportunidades'])) {
                foreach ($foda['oportunidades'] as $item) {
                    $html .= '<li style="margin-bottom: 8px; line-height: 1.4; text-align: justify;">' . htmlspecialchars($item) . '</li>';
                }
            } else {
                $html .= '<li style="font-style: italic; color: #999;">No se han identificado oportunidades específicas.</li>';
            }
            $html .= '</ul>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; padding: 15px; vertical-align: top; border-right: 1px solid #eee; border-top: 1px solid #eee;">
                            <h4 style="color: ' . $primaryColor . '; margin-bottom: 15px; font-size: 14pt; border-bottom: 2px solid ' . $primaryColor . '; padding-bottom: 5px;">Debilidades</h4>
                            <p style="font-size: 10pt; color: #666; margin-bottom: 10px; font-style: italic;">(Factores Internos Negativos)</p>
                            <ul style="margin: 0; padding-left: 20px;">';
            if (!empty($foda['debilidades'])) {
                foreach ($foda['debilidades'] as $item) {
                    $html .= '<li style="margin-bottom: 8px; line-height: 1.4; text-align: justify;">' . htmlspecialchars($item) . '</li>';
                }
            } else {
                $html .= '<li style="font-style: italic; color: #999;">No se han identificado debilidades específicas.</li>';
            }
            $html .= '</ul>
                        </td>
                        <td style="width: 50%; padding: 15px; vertical-align: top; border-top: 1px solid #eee;">
                            <h4 style="color: ' . $primaryColor . '; margin-bottom: 15px; font-size: 14pt; border-bottom: 2px solid ' . $primaryColor . '; padding-bottom: 5px;">Amenazas</h4>
                            <p style="font-size: 10pt; color: #666; margin-bottom: 10px; font-style: italic;">(Factores Externos Negativos)</p>
                            <ul style="margin: 0; padding-left: 20px;">';
            if (!empty($foda['amenazas'])) {
                foreach ($foda['amenazas'] as $item) {
                    $html .= '<li style="margin-bottom: 8px; line-height: 1.4; text-align: justify;">' . htmlspecialchars($item) . '</li>';
                }
            } else {
                $html .= '<li style="font-style: italic; color: #999;">No se han identificado amenazas específicas.</li>';
            }
            $html .= '</ul>
                        </td>
                    </tr>
                </table>
            </div>';
        }
        
        // Identificación de Estrategia
        if ($dominant_strategy) {
            $html .= '<div class="section">
                <h3>IDENTIFICACIÓN DE ESTRATEGIA</h3>
                <p class="justified-text">Basándose en el análisis estratégico FODA y la evaluación de las relaciones entre factores internos y externos, se ha determinado la estrategia dominante:</p>
                
                <h4 style="color: ' . $primaryColor . '; margin-top: 20px;">Síntesis de Resultados</h4>
                <table style="width: 100%; border-collapse: collapse; margin: 15px 0; border: 1px solid #ddd;">
                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; width: 60%;">Estrategia Ofensiva (FO)</td>
                        <td style="padding: 12px; border: 1px solid #ddd; text-align: center; font-weight: bold; width: 10%;">' . ($strategy_scores['FO'] ?? 0) . '</td>
                        <td style="padding: 12px; border: 1px solid #ddd; width: 30%;">Fortalezas + Oportunidades</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">Estrategia Defensiva (FA)</td>
                        <td style="padding: 12px; border: 1px solid #ddd; text-align: center; font-weight: bold;">' . ($strategy_scores['FA'] ?? 0) . '</td>
                        <td style="padding: 12px; border: 1px solid #ddd;">Fortalezas + Amenazas</td>
                    </tr>
                    <tr style="background-color: #f8f9fa;">
                        <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">Estrategia Adaptativa (DO)</td>
                        <td style="padding: 12px; border: 1px solid #ddd; text-align: center; font-weight: bold;">' . ($strategy_scores['DO'] ?? 0) . '</td>
                        <td style="padding: 12px; border: 1px solid #ddd;">Debilidades + Oportunidades</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">Estrategia de Supervivencia (DA)</td>
                        <td style="padding: 12px; border: 1px solid #ddd; text-align: center; font-weight: bold;">' . ($strategy_scores['DA'] ?? 0) . '</td>
                        <td style="padding: 12px; border: 1px solid #ddd;">Debilidades + Amenazas</td>
                    </tr>
                </table>
                
                <div style="background-color: #e8f5e8; padding: 20px; margin: 15px 0; border-left: 4px solid ' . $primaryColor . '; border-radius: 5px;">
                    <h4 style="color: ' . $primaryColor . '; margin: 0 0 10px 0;">' . htmlspecialchars($dominant_strategy['strategy_name']) . '</h4>
                    <p class="justified-text" style="margin: 0; color: #555;">' . htmlspecialchars($dominant_strategy['strategy_description']) . '</p>
                </div>
            </div>';
        }
        
        // Acciones Competitivas (Matriz CAME)
        if (!empty($came_actions['C']) || !empty($came_actions['A']) || !empty($came_actions['M']) || !empty($came_actions['E'])) {
            $html .= '<div class="section">
                <h3>ACCIONES COMPETITIVAS</h3>
                <p class="justified-text">Las acciones estratégicas organizadas según la metodología CAME son:</p>';
            
            $came_titles = [
                'C' => 'Corregir (Debilidades)',
                'A' => 'Afrontar (Amenazas)',
                'M' => 'Mantener (Fortalezas)',
                'E' => 'Explotar (Oportunidades)'
            ];
            
            foreach ($came_titles as $type => $title) {
                if (!empty($came_actions[$type])) {
                    $html .= '<h4 style="color: ' . $primaryColor . '; margin-top: 20px;">' . $title . '</h4>
                    <ul>';
                    foreach ($came_actions[$type] as $action) {
                        $html .= '<li class="justified-text">' . htmlspecialchars($action) . '</li>';
                    }
                    $html .= '</ul>';
                }
            }
            $html .= '</div>';
        }
        
        // Conclusiones mejoradas
        $html .= '<div class="section">
            <h3>CONCLUSIONES Y RECOMENDACIONES</h3>
            <p class="justified-text">El presente plan estratégico para <strong>' . htmlspecialchars($project['company_name']) . '</strong> establece un marco integral que abarca desde la definición de la identidad organizacional hasta la implementación de acciones competitivas específicas.</p>
            
            <h4 style="color: ' . $primaryColor . '; margin-top: 20px;">Aspectos Clave del Plan:</h4>
            <ul>
                <li class="justified-text"><strong>Identidad Organizacional:</strong> Se ha establecido una base sólida con la definición de misión, visión y valores que orientarán todas las decisiones estratégicas.</li>';
        
        if (!empty($objectives['general']) || !empty($objectives['specific'])) {
            $objectiveCount = count($objectives['general']) + count($objectives['specific']);
            $html .= '<li class="justified-text"><strong>Objetivos Estratégicos:</strong> Se han definido ' . $objectiveCount . ' objetivos claros y medibles que guiarán el crecimiento organizacional.</li>';
        }
        
        if ($value_chain_data) {
            $html .= '<li class="justified-text"><strong>Diagnóstico de Cadena de Valor:</strong> Se obtuvo una puntuación de ' . $value_chain_data['total_score'] . '/' . $value_chain_data['max_possible'] . ' puntos (' . round($value_chain_data['percentage']) . '%) con un potencial de mejora del ' . round($value_chain_data['improvement_potential']) . '%.</li>';
        }
        
        if ($bcg_data) {
            $totalProducts = $bcg_data['estrella']['count'] + $bcg_data['interrogante']['count'] + $bcg_data['vaca_lechera']['count'] + $bcg_data['perro']['count'];
            $html .= '<li class="justified-text"><strong>Matriz BCG:</strong> Se analizaron ' . $totalProducts . ' productos distribuidos en los cuadrantes estratégicos para optimizar la gestión del portafolio.</li>';
        }
        
        if ($porter_data) {
            $html .= '<li class="justified-text"><strong>Análisis de Porter:</strong> Se evaluó el entorno competitivo obteniendo una puntuación de ' . $porter_data['total_score'] . '/' . $porter_data['max_possible'] . ' puntos, indicando un nivel de competitividad ' . strtolower($porter_data['competitivity_level']) . '.</li>';
        }
        
        if ($pest_data) {
            $pest_categories_evaluated = count($pest_data);
            $html .= '<li class="justified-text"><strong>Diagnóstico PEST:</strong> Se evaluaron ' . $pest_categories_evaluated . ' categorías del entorno empresarial (Político, Económico, Social, Tecnológico, Medioambiental) identificando oportunidades y amenazas estratégicas.</li>';
        }
        
        if (!empty($came_actions['C']) || !empty($came_actions['A']) || !empty($came_actions['M']) || !empty($came_actions['E'])) {
            $actionCount = count($came_actions['C']) + count($came_actions['A']) + count($came_actions['M']) + count($came_actions['E']);
            $html .= '<li class="justified-text"><strong>Plan de Acción:</strong> Se han establecido ' . $actionCount . ' acciones estratégicas específicas mediante la matriz CAME.</li>';
        }
        
        $html .= '</ul>
            
            <h4 style="color: ' . $primaryColor . '; margin-top: 20px;">Recomendaciones para la Implementación:</h4>
            <ul>
                <li class="justified-text"><strong>Monitoreo Continuo:</strong> Establecer indicadores de desempeño (KPIs) para cada objetivo estratégico.</li>
                <li class="justified-text"><strong>Revisión Periódica:</strong> Realizar evaluaciones trimestrales del progreso y ajustar estrategias según sea necesario.</li>
                <li class="justified-text"><strong>Comunicación Interna:</strong> Asegurar que todos los miembros de la organización comprendan y se alineen con el plan estratégico.</li>
                <li class="justified-text"><strong>Asignación de Recursos:</strong> Garantizar la disponibilidad de recursos necesarios para implementar las acciones planteadas.</li>
            </ul>
            
            <p class="justified-text" style="margin-top: 20px;"><strong>La implementación exitosa de este plan estratégico posicionará a ' . htmlspecialchars($project['company_name']) . ' para alcanzar sus objetivos de crecimiento y consolidar su ventaja competitiva en el mercado.</strong></p>
        </div>';
        
        // Si no hay contenido básico, agregar mensaje
        if ((!$mission || empty($mission['mission_text'])) && 
            (!$vision || empty($vision['vision_text'])) && 
            empty($values) && 
            empty($objectives['general']) && 
            empty($objectives['specific']) && 
            empty($foda['fortalezas']) && 
            empty($came_actions['C'])) {
            $html .= '<div class="section">
                <h3>DESARROLLO DEL PLAN ESTRATÉGICO</h3>
                <p class="justified-text">Este proyecto se encuentra en las etapas iniciales de desarrollo. Para completar un plan estratégico integral, se recomienda completar las siguientes secciones:</p>
                <ul>
                    <li class="justified-text">Definición de Misión Organizacional</li>
                    <li class="justified-text">Establecimiento de la Visión Empresarial</li>
                    <li class="justified-text">Identificación de Valores Organizacionales</li>
                    <li class="justified-text">Formulación de Objetivos Estratégicos</li>
                    <li class="justified-text">Diagnóstico de Cadena de Valor</li>
                    <li class="justified-text">Análisis de Matriz BCG</li>
                    <li class="justified-text">Evaluación de las 5 Fuerzas de Porter</li>
                    <li class="justified-text">Diagnóstico del Entorno PEST</li>
                    <li class="justified-text">Análisis FODA Completo</li>
                    <li class="justified-text">Desarrollo de la Matriz CAME</li>
                </ul>
                <p class="justified-text">La completación de estos elementos proporcionará un marco estratégico robusto para la toma de decisiones y el crecimiento organizacional.</p>
            </div>';
        }
        
        $html .= '</div></body></html>';
        
        // Intentar múltiples configuraciones para Azure
        $mpdf = null;
        $errors = [];
        
        // Configuración 1: Usar directorio temporal del sistema
        try {
            $tempDir = sys_get_temp_dir() . '/mpdf_temp';
            if (!is_dir($tempDir)) {
                @mkdir($tempDir, 0755, true);
            }
            
            $mpdf = new \Mpdf\Mpdf([
                'format' => 'A4',
                'orientation' => 'P',
                'tempDir' => $tempDir
            ]);
        } catch (Exception $e1) {
            $errors[] = "Config 1 falló: " . $e1->getMessage();
            
            // Configuración 2: Configuración mínima sin tempDir
            try {
                $mpdf = new \Mpdf\Mpdf([
                    'format' => 'A4',
                    'orientation' => 'P',
                    'mode' => 'utf-8'
                ]);
            } catch (Exception $e2) {
                $errors[] = "Config 2 falló: " . $e2->getMessage();
                
                // Configuración 3: Ultra básica
                try {
                    $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
                } catch (Exception $e3) {
                    $errors[] = "Config 3 falló: " . $e3->getMessage();
                    throw new Exception("Todas las configuraciones fallaron: " . implode("; ", $errors));
                }
            }
        }
        
        $mpdf->WriteHTML($html);
        $pdfContent = $mpdf->Output('', 'S');
        
        // Limpiar output buffer
        if (ob_get_level()) ob_clean();
        
        // Enviar PDF
        $filename = 'Reporte_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $project['project_name']) . '_' . date('Y-m-d') . '.pdf';
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdfContent));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        echo $pdfContent;
        exit;
        
    } catch (Exception $e) {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        
        // Información adicional para debug en Azure
        $debugInfo = [
            'sys_temp_dir' => sys_get_temp_dir(),
            'temp_dir_writable' => is_writable(sys_get_temp_dir()),
            'current_dir' => __DIR__,
            'vendor_path' => __DIR__ . '/vendor/mpdf/mpdf/',
            'vendor_exists' => file_exists(__DIR__ . '/vendor/mpdf/mpdf/'),
        ];
        
        echo json_encode([
            'success' => false, 
            'error' => $e->getMessage(),
            'debug' => $debugInfo
        ]);
        exit;
    }
}

// Si no es POST, redirigir
header('Location: Views/Users/templates.php');
exit;
?>