<?php
// No iniciar sesión aquí porque ya se inicia en la página que llama
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../Controllers/AuthController.php';
require_once __DIR__ . '/../Controllers/ProjectController.php';

// Incluir autoloader de Composer para mPDF
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

class ReportController {
    private $db;
    private $projectController;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->projectController = new ProjectController();
    }
    
    /**
     * Obtener proyectos del usuario para generar reportes
     */
    public function getUserProjects($user_id) {
        try {
            $query = "SELECT id, project_name, company_name, created_at, status, progress_percentage 
                     FROM strategic_projects 
                     WHERE user_id = ? 
                     ORDER BY created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $projects = [];
            while ($row = $result->fetch_assoc()) {
                $projects[] = $row;
            }
            
            return $projects;
        } catch (Exception $e) {
            error_log("Error obteniendo proyectos del usuario: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generar reporte PDF de un proyecto
     */
    public function generatePDFReport($project_id, $participants = [], $template = 'default') {
        try {
            // Obtener datos del proyecto
            $project = $this->projectController->getProject($project_id);
            if (!$project) {
                throw new Exception("Proyecto no encontrado");
            }
            
            // Obtener datos completos del proyecto
            $reportData = $this->getProjectReportData($project_id);
            
            // Generar HTML del reporte
            $html = $this->generateReportHTML($project, $reportData, $participants, $template);
            
            // Convertir a PDF usando mPDF
            return $this->convertToPDF($html, $project['project_name']);
            
        } catch (Exception $e) {
            error_log("Error generando reporte PDF: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtener todos los datos del proyecto para el reporte
     */
    private function getProjectReportData($project_id) {
        $data = [];
        
        try {
            // Misión
            $data['mission'] = $this->getProjectMission($project_id);
            
            // Visión  
            $data['vision'] = $this->getProjectVision($project_id);
            
            // Valores
            $data['values'] = $this->getProjectValues($project_id);
            
            // Objetivos
            $data['objectives'] = $this->getProjectObjectives($project_id);
            
            // Análisis FODA
            $data['foda'] = $this->getProjectFODA($project_id);
            
            // Cadena de Valor
            $data['value_chain'] = $this->getProjectValueChain($project_id);
            
            // Análisis BCG
            $data['bcg'] = $this->getProjectBCG($project_id);
            
            // Análisis Porter
            $data['porter'] = $this->getProjectPorter($project_id);
            
            // Análisis PEST
            $data['pest'] = $this->getProjectPEST($project_id);
            
            // Estrategias
            $data['strategies'] = $this->getProjectStrategies($project_id);
            
            // Matriz CAME
            $data['came'] = $this->getProjectCAME($project_id);
            
            return $data;
            
        } catch (Exception $e) {
            error_log("Error obteniendo datos del proyecto: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generar HTML completo del reporte
     */
    private function generateReportHTML($project, $data, $participants, $template) {
        $participantsText = is_array($participants) ? implode(', ', $participants) : $participants;
        $currentDate = date('d/m/Y');
        
        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen Ejecutivo - ' . htmlspecialchars($project['project_name']) . '</title>
    <style>
        ' . $this->getReportCSS($template) . '
    </style>
</head>
<body>';

        // Carátula
        $html .= $this->generateCoverPage($project, $participantsText, $currentDate, $template);
        
        // Contenido del reporte
        $html .= $this->generateReportContent($project, $data);
        
        $html .= '</body></html>';
        
        return $html;
    }
    
    /**
     * Generar carátula del reporte
     */
    private function generateCoverPage($project, $participants, $date, $template) {
        $coverClass = 'cover-' . $template;
        
        return '
        <div class="cover-page ' . $coverClass . '">
            <div class="cover-content">
                <div class="cover-header">
                    <h1 class="cover-title">RESUMEN EJECUTIVO DEL PLAN ESTRATÉGICO</h1>
                    <h2 class="project-title">' . htmlspecialchars($project['project_name']) . '</h2>
                </div>
                
                <div class="cover-info">
                    <div class="info-row">
                        <span class="info-label">Nombre de la empresa/proyecto:</span>
                        <span class="info-value">' . htmlspecialchars($project['company_name']) . '</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Fecha de elaboración:</span>
                        <span class="info-value">' . $date . '</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Emprendedores/Promotores:</span>
                        <span class="info-value">' . htmlspecialchars($participants) . '</span>
                    </div>
                </div>
                
                <div class="cover-footer">
                    <p>PlanMaster - Sistema de Planificación Estratégica</p>
                </div>
            </div>
        </div>
        <div class="page-break"></div>';
    }
    
    /**
     * CSS para el reporte con diferentes plantillas
     */
    private function getReportCSS($template) {
        $baseCSS = '
        @page {
            margin: 0;
            size: A4 portrait;
        }
        
        body {
            margin: 0;
            font-family: "Arial", sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .cover-page {
            width: 210mm;
            height: 297mm;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            page-break-after: always;
        }
        
        .cover-content {
            text-align: center;
            max-width: 80%;
            z-index: 2;
        }
        
        .cover-title {
            font-size: 28px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            margin-bottom: 30px;
            line-height: 1.2;
        }
        
        .project-title {
            font-size: 24px;
            font-weight: 600;
            color: white;
            margin-bottom: 50px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .cover-info {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 40px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .info-label {
            font-weight: 600;
            color: #444;
            flex: 1;
            text-align: left;
        }
        
        .info-value {
            font-weight: 400;
            color: #666;
            flex: 1;
            text-align: right;
        }
        
        .cover-footer {
            color: white;
            font-size: 14px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .content-page {
            padding: 40px;
            min-height: 257mm;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin: 30px 0 15px 0;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
        }
        
        .section-content {
            margin-bottom: 25px;
        }
        ';
        
        // Plantillas específicas
        switch ($template) {
            case 'corporate':
                $templateCSS = '
                .cover-corporate {
                    background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%);
                    position: relative;
                }
                
                .cover-corporate::before {
                    content: "";
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: url("data:image/svg+xml,%3Csvg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="white" fill-opacity="0.05"%3E%3Cpath d="M20 20c0 5.5-4.5 10-10 10s-10-4.5-10-10 4.5-10 10-10 10 4.5 10 10zM30 10c0 5.5-4.5 10-10 10s-10-4.5-10-10 4.5-10 10-10 10 4.5 10 10z"/%3E%3C/g%3E%3C/svg%3E");
                }';
                break;
                
            case 'modern':
                $templateCSS = '
                .cover-modern {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #667eea 100%);
                    position: relative;
                }
                
                .cover-modern::before {
                    content: "";
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
                }';
                break;
                
            case 'elegant':
                $templateCSS = '
                .cover-elegant {
                    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
                    position: relative;
                }
                
                .cover-elegant::before {
                    content: "";
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: repeating-linear-gradient(
                        45deg,
                        transparent,
                        transparent 2px,
                        rgba(255,255,255,0.03) 2px,
                        rgba(255,255,255,0.03) 4px
                    );
                }';
                break;
                
            default: // default template
                $templateCSS = '
                .cover-default {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    position: relative;
                }
                
                .cover-default::before {
                    content: "";
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: url("data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="white" fill-opacity="0.08"%3E%3Ccircle cx="30" cy="30" r="20"/%3E%3C/g%3E%3C/svg%3E");
                }';
        }
        
        return $baseCSS . $templateCSS;
    }
    
    /**
     * Generar contenido del reporte (páginas internas)
     */
    private function generateReportContent($project, $data) {
        $content = '<div class="content-page">';
        
        // Misión
        if (!empty($data['mission'])) {
            $content .= '<div class="section">
                <h2 class="section-title">🎯 Misión</h2>
                <div class="section-content">' . htmlspecialchars($data['mission']['mission_text']) . '</div>
            </div>';
        }
        
        // Visión  
        if (!empty($data['vision'])) {
            $content .= '<div class="section">
                <h2 class="section-title">🔮 Visión</h2>
                <div class="section-content">' . htmlspecialchars($data['vision']['vision_text']) . '</div>
            </div>';
        }
        
        // Valores
        if (!empty($data['values'])) {
            $content .= '<div class="section">
                <h2 class="section-title">⭐ Valores</h2>
                <div class="section-content"><ul>';
            foreach ($data['values'] as $value) {
                $content .= '<li>' . htmlspecialchars($value['value_text']) . '</li>';
            }
            $content .= '</ul></div></div>';
        }
        
        // Aquí continuarías con las demás secciones...
        
        $content .= '</div>';
        
        return $content;
    }
    
    /**
     * Convertir HTML a PDF usando mPDF v6.1
     */
    private function convertToPDF($html, $filename) {
        // Incluir autoloader de Composer
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
            require_once __DIR__ . '/../vendor/autoload.php';
        }
        
        // Verificar si mPDF v8 está disponible
        if (!class_exists('Mpdf\Mpdf')) {
            throw new Exception("mPDF no está instalado. Ejecute: composer require mpdf/mpdf");
        }
        
        // Configuración para mPDF v8
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0
        ]);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);
        
        $pdfFilename = 'Resumen_Ejecutivo_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $filename) . '_' . date('Y-m-d') . '.pdf';
        
        return [
            'content' => $mpdf->Output('', 'S'), // Retornar como string
            'filename' => $pdfFilename
        ];
    }
    
    // Métodos auxiliares para obtener datos específicos del proyecto
    private function getProjectMission($project_id) {
        $query = "SELECT mission_text FROM project_mission WHERE project_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    private function getProjectVision($project_id) {
        $query = "SELECT vision_text FROM project_vision WHERE project_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    private function getProjectValues($project_id) {
        $query = "SELECT value_text FROM project_values WHERE project_id = ? ORDER BY value_order";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $values = [];
        while ($row = $result->fetch_assoc()) {
            $values[] = $row;
        }
        return $values;
    }
    
    private function getProjectObjectives($project_id) {
        // Implementar según tu estructura de objetivos
        return [];
    }
    
    private function getProjectFODA($project_id) {
        // Implementar según tu estructura FODA
        return [];
    }
    
    private function getProjectValueChain($project_id) {
        // Implementar según tu estructura de cadena de valor
        return [];
    }
    
    private function getProjectBCG($project_id) {
        // Implementar según tu estructura BCG
        return [];
    }
    
    private function getProjectPorter($project_id) {
        // Implementar según tu estructura Porter
        return [];
    }
    
    private function getProjectPEST($project_id) {
        // Implementar según tu estructura PEST
        return [];
    }
    
    private function getProjectStrategies($project_id) {
        // Implementar según tu estructura de estrategias
        return [];
    }
    
    private function getProjectCAME($project_id) {
        // Implementar según tu estructura CAME
        return [];
    }
}

// Manejo de acciones AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Verificar que el usuario esté logueado
    if (!AuthController::isLoggedIn()) {
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
        exit;
    }
    
    $controller = new ReportController();
    $user = AuthController::getCurrentUser();
    
    switch ($_POST['action']) {
        case 'generate_pdf':
            try {
                $project_id = intval($_POST['project_id']);
                $participants = $_POST['participants'] ?? '';
                $template = $_POST['template'] ?? 'default';
                
                $pdf = $controller->generatePDFReport($project_id, $participants, $template);
                
                // Limpiar cualquier output buffer
                if (ob_get_level()) {
                    ob_clean();
                }
                
                // Enviar headers correctos para PDF
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $pdf['filename'] . '"');
                header('Content-Length: ' . strlen($pdf['content']));
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
                echo $pdf['content'];
                exit;
                
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            break;
    }
}
?>