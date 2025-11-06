<?php
require_once __DIR__ . '/../config/database.php';

class PestAnalysis {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    // Obtener las 25 preguntas estándar del análisis PEST
    public function getStandardQuestions() {
        return [
            1 => 'Los cambios en la composición étnica de los consumidores de nuestro mercado está teniendo un notable impacto.',
            2 => 'El envejecimiento de la población tiene un importante impacto en la demanda.',
            3 => 'Los nuevos estilos de vida y tendencias originan cambios en la oferta de nuestro sector.',
            4 => 'El envejecimiento de la población tiene un importante impacto en la oferta del sector donde operamos.',
            5 => 'Las variaciones en el nivel de riqueza de la población impactan considerablemente en la demanda de los productos/servicios del sector donde operamos.',
            6 => 'La legislación fiscal afecta muy considerablemente a la economía de las empresas del sector donde operamos.',
            7 => 'La legislación laboral afecta muy considerablemente a la operativa del sector donde actuamos.',
            8 => 'Las subvenciones otorgadas por las Administraciones Públicas son claves en el desarrollo competitivo del mercado donde operamos.',
            9 => 'El impacto que tiene la legislación de protección al consumidor, en la manera de producir bienes y/o servicios es muy importante.',
            10 => 'La normativa autonómica tiene un impacto considerable en el funcionamiento del sector donde actuamos.',
            11 => 'Las expectativas de crecimiento económico generales afectan crucialmente al mercado donde operamos.',
            12 => 'La política de tipos de interés es fundamental en el desarrollo financiero del sector donde trabaja nuestra empresa.',
            13 => 'La globalización permite a nuestra industria gozar de importantes oportunidades en nuevos mercados.',
            14 => 'La situación del empleo es fundamental para el desarrollo económico de nuestra empresa y nuestro sector.',
            15 => 'Las expectativas del ciclo económico de nuestro sector impactan en la situación económica de sus empresas.',
            16 => 'Las Administraciones Públicas están incentivando el esfuerzo tecnológico de las empresas de nuestro sector.',
            17 => 'Internet, el comercio electrónico, el wireless y otras NTIC están impactando en la demanda de nuestros productos/servicios y en los de la competencia.',
            18 => 'El empleo de NTIC\'s es generalizado en el sector donde trabajamos.',
            19 => 'En nuestro sector, es de gran importancia ser pionero o referente en el empleo de aplicaciones tecnológicas.',
            20 => 'En el sector donde operamos, para ser competitivos, es condición "sine qua non" innovar constantemente.',
            21 => 'La legislación medioambiental afecta al desarrollo de nuestro sector.',
            22 => 'Los clientes de nuestro mercado exigen que seamos socialmente responsables, en el plano medioambiental.',
            23 => 'En nuestro sector, las políticas medioambientales son una fuente de ventajas competitivas.',
            24 => 'La creciente preocupación social por el medio ambiente impacta notablemente en la demanda de productos/servicios ofertados en nuestro mercado.',
            25 => 'El factor ecológico es una fuente de diferenciación clara en el sector donde opera nuestra empresa.'
        ];
    }
    
    // Obtener categorías PEST
    public function getPestCategories() {
        return [
            'S' => 'Social y Demográfico',
            'M' => 'Medioambiental',
            'P' => 'Político',
            'E' => 'Económico', 
            'T' => 'Tecnológico'
        ];
    }
    
    // Mapear preguntas por categorías PEST
    public function getQuestionsByCategory() {
        return [
            // Social: preguntas 1-5
            'S' => [1, 2, 3, 4, 5],
            // Medioambiental: preguntas 21-25
            'M' => [21, 22, 23, 24, 25],
            // Político: preguntas 6-10
            'P' => [6, 7, 8, 9, 10],
            // Económico: preguntas 11-15
            'E' => [11, 12, 13, 14, 15],
            // Tecnológico: preguntas 16-20
            'T' => [16, 17, 18, 19, 20]
        ];
    }
    
    // Crear o actualizar respuestas del diagnóstico PEST
    public function saveResponses($project_id, $responses) {
        try {
            $this->conn->autocommit(FALSE);
            
            // Primero eliminar respuestas existentes
            $this->deleteByProject($project_id);
            
            $questions = $this->getStandardQuestions();
            
            // Insertar nuevas respuestas
            $query = "INSERT INTO project_pest_analysis (project_id, question_number, rating) 
                     VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            
            foreach ($responses as $question_number => $rating) {
                if (isset($questions[$question_number])) {
                    $rating_value = intval($rating);
                    $stmt->bind_param('iii', $project_id, $question_number, $rating_value);
                    $stmt->execute();
                }
            }
            
            $this->conn->commit();
            $this->conn->autocommit(TRUE);
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->conn->autocommit(TRUE);
            error_log("Error saving PEST analysis responses: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener respuestas por proyecto
    public function getByProject($project_id) {
        try {
            $query = "SELECT question_number, rating 
                     FROM project_pest_analysis 
                     WHERE project_id = ? 
                     ORDER BY question_number ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $project_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $responses = [];
            $questions = $this->getStandardQuestions();
            
            while ($row = $result->fetch_assoc()) {
                $question_number = $row['question_number'];
                $responses[$question_number] = [
                    'question_text' => $questions[$question_number] ?? '',
                    'rating' => intval($row['rating'])
                ];
            }
            
            return $responses;
            
        } catch (Exception $e) {
            error_log("Error getting PEST analysis responses: " . $e->getMessage());
            return [];
        }
    }
    
    // Calcular resumen del análisis PEST
    public function calculateSummary($project_id) {
        try {
            $query = "SELECT SUM(rating) as total_rating, COUNT(*) as total_questions,
                            AVG(rating) as average_rating
                     FROM project_pest_analysis 
                     WHERE project_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $project_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row && $row['total_questions'] == 25) {
                $total_rating = intval($row['total_rating']);
                $average_rating = floatval($row['average_rating']);
                
                // Interpretación basada en el promedio
                $interpretation = $this->getInterpretation($average_rating);
                
                return [
                    'total_rating' => $total_rating,
                    'average_rating' => round($average_rating, 2),
                    'max_possible' => 100, // 25 preguntas x 4 puntos máximo
                    'percentage' => round(($total_rating / 100) * 100, 2),
                    'interpretation' => $interpretation
                ];
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log("Error calculating PEST summary: " . $e->getMessage());
            return null;
        }
    }
    
    // Obtener interpretación basada en el promedio
    private function getInterpretation($average_rating) {
        if ($average_rating >= 3.5) {
            return [
                'level' => 'Muy Favorable',
                'description' => 'El entorno externo es altamente favorable para su empresa.',
                'color' => 'success'
            ];
        } elseif ($average_rating >= 2.5) {
            return [
                'level' => 'Moderadamente Favorable', 
                'description' => 'El entorno presenta oportunidades con algunos desafíos.',
                'color' => 'warning'
            ];
        } elseif ($average_rating >= 1.5) {
            return [
                'level' => 'Desafiante',
                'description' => 'El entorno presenta desafíos significativos que requieren estrategias específicas.',
                'color' => 'danger'
            ];
        } else {
            return [
                'level' => 'Muy Desafiante',
                'description' => 'El entorno externo es muy adverso. Se requieren estrategias de supervivencia y adaptación.',
                'color' => 'critical'
            ];
        }
    }
    
    // Obtener estadísticas por categorías PEST
    public function getCategoryStats($project_id) {
        $responses = $this->getByProject($project_id);
        $questionsByCategory = $this->getQuestionsByCategory();
        $categories = $this->getPestCategories();
        
        $stats = [];
        foreach ($questionsByCategory as $categoryCode => $questions) {
            $total = 0;
            $count = 0;
            
            foreach ($questions as $q) {
                if (isset($responses[$q])) {
                    $total += $responses[$q]['rating'];
                    $count++;
                }
            }
            
            if ($count > 0) {
                $average = $total / $count;
                $stats[$categoryCode] = [
                    'name' => $categories[$categoryCode],
                    'total' => $total,
                    'average' => round($average, 2),
                    'questions_count' => $count,
                    'max_possible' => $count * 4,
                    'percentage' => round(($total / ($count * 4)) * 100, 2),
                    'interpretation' => $this->getInterpretation($average)
                ];
            }
        }
        
        return $stats;
    }
    
    // Verificar si el diagnóstico PEST está completo
    public function isComplete($project_id) {
        try {
            $query = "SELECT COUNT(*) as count FROM project_pest_analysis WHERE project_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $project_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row['count'] == 25;
            
        } catch (Exception $e) {
            error_log("Error checking PEST completion: " . $e->getMessage());
            return false;
        }
    }
    
    // Eliminar respuestas por proyecto
    public function deleteByProject($project_id) {
        try {
            $query = "DELETE FROM project_pest_analysis WHERE project_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $project_id);
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error deleting PEST analysis responses: " . $e->getMessage());
            return false;
        }
    }
}
?>