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
                $foda[$row['type']][] = $row['item_text'];
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
        }
        
        .section ul {
            margin: 15px 0;
            padding-left: 25px;
        }
        
        .section li {
            margin-bottom: 10px;
            line-height: 1.5;
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
            <p>El presente documento constituye el resumen ejecutivo del plan estratégico para <strong>' . htmlspecialchars($project['company_name']) . '</strong>, elaborado mediante un proceso metodológico estructurado que permite establecer las directrices fundamentales para el desarrollo organizacional.</p>
            
            <p><strong>Proceso de Planificación Estratégica Aplicado:</strong></p>
            
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
            
            <p>Este enfoque integral garantiza una planificación estratégica robusta y fundamentada en análisis profundos del contexto organizacional.</p>
        </div>';
        
        if ($mission && !empty($mission['mission_text'])) {
            $html .= '<div class="section">
                <h3>MISIÓN</h3>
                <p>' . nl2br(htmlspecialchars($mission['mission_text'])) . '</p>
            </div>';
        }
        
        if ($vision && !empty($vision['vision_text'])) {
            $html .= '<div class="section">
                <h3>VISIÓN</h3>
                <p>' . nl2br(htmlspecialchars($vision['vision_text'])) . '</p>
            </div>';
        }
        
        if (!empty($values)) {
            $html .= '<div class="section">
                <h3>VALORES ORGANIZACIONALES</h3>
                <p>Los valores que rigen nuestras acciones y decisiones son:</p>
                <ul>';
            foreach ($values as $value) {
                $html .= '<li><strong>' . htmlspecialchars($value) . '</strong></li>';
            }
            $html .= '</ul></div>';
        }
        
        // Objetivos Estratégicos
        if (!empty($objectives['general']) || !empty($objectives['specific'])) {
            $html .= '<div class="section">
                <h3>OBJETIVOS ESTRATÉGICOS</h3>
                <p>Los objetivos que guían el desarrollo estratégico de la organización son:</p>';
            
            if (!empty($objectives['general'])) {
                $html .= '<h4 style="color: ' . $primaryColor . '; margin-top: 20px;">Objetivos Generales:</h4>
                <ul>';
                foreach ($objectives['general'] as $objective) {
                    $html .= '<li>' . htmlspecialchars($objective) . '</li>';
                }
                $html .= '</ul>';
            }
            
            if (!empty($objectives['specific'])) {
                $html .= '<h4 style="color: ' . $primaryColor . '; margin-top: 20px;">Objetivos Específicos:</h4>
                <ul>';
                foreach ($objectives['specific'] as $objective) {
                    $html .= '<li>' . htmlspecialchars($objective) . '</li>';
                }
                $html .= '</ul>';
            }
            $html .= '</div>';
        }
        
        // Análisis FODA
        if (!empty($foda['fortalezas']) || !empty($foda['debilidades']) || !empty($foda['oportunidades']) || !empty($foda['amenazas'])) {
            $html .= '<div class="section">
                <h3>ANÁLISIS FODA</h3>
                <p>El análisis de factores internos y externos de la organización revela:</p>
                
                <div style="display: table; width: 100%; margin: 20px 0;">
                    <div style="display: table-row;">
                        <div style="display: table-cell; width: 50%; padding: 10px; vertical-align: top;">
                            <h4 style="color: ' . $primaryColor . ';">Fortalezas (Factores Internos Positivos)</h4>
                            <ul>';
            if (!empty($foda['fortalezas'])) {
                foreach ($foda['fortalezas'] as $item) {
                    $html .= '<li>' . htmlspecialchars($item) . '</li>';
                }
            } else {
                $html .= '<li><em>No se han identificado fortalezas específicas.</em></li>';
            }
            $html .= '</ul>
                        </div>
                        <div style="display: table-cell; width: 50%; padding: 10px; vertical-align: top;">
                            <h4 style="color: ' . $primaryColor . ';">Oportunidades (Factores Externos Positivos)</h4>
                            <ul>';
            if (!empty($foda['oportunidades'])) {
                foreach ($foda['oportunidades'] as $item) {
                    $html .= '<li>' . htmlspecialchars($item) . '</li>';
                }
            } else {
                $html .= '<li><em>No se han identificado oportunidades específicas.</em></li>';
            }
            $html .= '</ul>
                        </div>
                    </div>
                    <div style="display: table-row;">
                        <div style="display: table-cell; width: 50%; padding: 10px; vertical-align: top;">
                            <h4 style="color: ' . $primaryColor . ';">Debilidades (Factores Internos Negativos)</h4>
                            <ul>';
            if (!empty($foda['debilidades'])) {
                foreach ($foda['debilidades'] as $item) {
                    $html .= '<li>' . htmlspecialchars($item) . '</li>';
                }
            } else {
                $html .= '<li><em>No se han identificado debilidades específicas.</em></li>';
            }
            $html .= '</ul>
                        </div>
                        <div style="display: table-cell; width: 50%; padding: 10px; vertical-align: top;">
                            <h4 style="color: ' . $primaryColor . ';">Amenazas (Factores Externos Negativos)</h4>
                            <ul>';
            if (!empty($foda['amenazas'])) {
                foreach ($foda['amenazas'] as $item) {
                    $html .= '<li>' . htmlspecialchars($item) . '</li>';
                }
            } else {
                $html .= '<li><em>No se han identificado amenazas específicas.</em></li>';
            }
            $html .= '</ul>
                        </div>
                    </div>
                </div>
            </div>';
        }
        
        // Identificación de Estrategia
        if ($dominant_strategy) {
            $html .= '<div class="section">
                <h3>IDENTIFICACIÓN DE ESTRATEGIA</h3>
                <p>Basándose en el análisis estratégico FODA y la evaluación de las relaciones entre factores internos y externos, se ha determinado la estrategia dominante:</p>
                
                <div style="background-color: #e8f5e8; padding: 20px; margin: 15px 0; border-left: 4px solid ' . $primaryColor . '; border-radius: 5px;">
                    <h4 style="color: ' . $primaryColor . '; margin: 0 0 10px 0;">' . htmlspecialchars($dominant_strategy['strategy_name']) . '</h4>
                    <p style="margin: 0; color: #555;">' . htmlspecialchars($dominant_strategy['strategy_description']) . '</p>
                </div>
            </div>';
        }
        
        // Acciones Competitivas (Matriz CAME)
        if (!empty($came_actions['C']) || !empty($came_actions['A']) || !empty($came_actions['M']) || !empty($came_actions['E'])) {
            $html .= '<div class="section">
                <h3>ACCIONES COMPETITIVAS</h3>
                <p>Las acciones estratégicas organizadas según la metodología CAME son:</p>';
            
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
                        $html .= '<li>' . htmlspecialchars($action) . '</li>';
                    }
                    $html .= '</ul>';
                }
            }
            $html .= '</div>';
        }
        
        // Conclusiones mejoradas
        $html .= '<div class="section">
            <h3>CONCLUSIONES Y RECOMENDACIONES</h3>
            <p>El presente plan estratégico para <strong>' . htmlspecialchars($project['company_name']) . '</strong> establece un marco integral que abarca desde la definición de la identidad organizacional hasta la implementación de acciones competitivas específicas.</p>
            
            <h4 style="color: ' . $primaryColor . '; margin-top: 20px;">Aspectos Clave del Plan:</h4>
            <ul>
                <li><strong>Identidad Organizacional:</strong> Se ha establecido una base sólida con la definición de misión, visión y valores que orientarán todas las decisiones estratégicas.</li>';
        
        if (!empty($objectives['general']) || !empty($objectives['specific'])) {
            $objectiveCount = count($objectives['general']) + count($objectives['specific']);
            $html .= '<li><strong>Objetivos Estratégicos:</strong> Se han definido ' . $objectiveCount . ' objetivos claros y medibles que guiarán el crecimiento organizacional.</li>';
        }
        
        if (!empty($foda['fortalezas']) || !empty($foda['debilidades']) || !empty($foda['oportunidades']) || !empty($foda['amenazas'])) {
            $fodaCount = count($foda['fortalezas']) + count($foda['debilidades']) + count($foda['oportunidades']) + count($foda['amenazas']);
            $html .= '<li><strong>Análisis Estratégico:</strong> El análisis FODA ha identificado ' . $fodaCount . ' factores críticos que influirán en el éxito organizacional.</li>';
        }
        
        if (!empty($came_actions['C']) || !empty($came_actions['A']) || !empty($came_actions['M']) || !empty($came_actions['E'])) {
            $actionCount = count($came_actions['C']) + count($came_actions['A']) + count($came_actions['M']) + count($came_actions['E']);
            $html .= '<li><strong>Plan de Acción:</strong> Se han establecido ' . $actionCount . ' acciones estratégicas específicas mediante la matriz CAME.</li>';
        }
        
        $html .= '</ul>
            
            <h4 style="color: ' . $primaryColor . '; margin-top: 20px;">Recomendaciones para la Implementación:</h4>
            <ul>
                <li><strong>Monitoreo Continuo:</strong> Establecer indicadores de desempeño (KPIs) para cada objetivo estratégico.</li>
                <li><strong>Revisión Periódica:</strong> Realizar evaluaciones trimestrales del progreso y ajustar estrategias según sea necesario.</li>
                <li><strong>Comunicación Interna:</strong> Asegurar que todos los miembros de la organización comprendan y se alineen con el plan estratégico.</li>
                <li><strong>Asignación de Recursos:</strong> Garantizar la disponibilidad de recursos necesarios para implementar las acciones planteadas.</li>
            </ul>
            
            <p style="margin-top: 20px;"><strong>La implementación exitosa de este plan estratégico posicionará a ' . htmlspecialchars($project['company_name']) . ' para alcanzar sus objetivos de crecimiento y consolidar su ventaja competitiva en el mercado.</strong></p>
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
                <p>Este proyecto se encuentra en las etapas iniciales de desarrollo. Para completar un plan estratégico integral, se recomienda completar las siguientes secciones:</p>
                <ul>
                    <li>Definición de Misión Organizacional</li>
                    <li>Establecimiento de la Visión Empresarial</li>
                    <li>Identificación de Valores Organizacionales</li>
                    <li>Formulación de Objetivos Estratégicos</li>
                    <li>Análisis FODA Completo</li>
                    <li>Desarrollo de la Matriz CAME</li>
                </ul>
                <p>La completación de estos elementos proporcionará un marco estratégico robusto para la toma de decisiones y el crecimiento organizacional.</p>
            </div>';
        }
        
        $html .= '</div></body></html>';
        
        // Generar PDF con configuración simple
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'orientation' => 'P'
        ]);
        
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
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Si no es POST, redirigir
header('Location: Views/Users/templates.php');
exit;
?>