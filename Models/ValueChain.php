<?php
require_once __DIR__ . '/../config/database.php';

class ValueChain {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
    
    // Obtener las 25 preguntas estándar
    public function getStandardQuestions() {
        return [
            1 => 'Se conoce completamente a nuestros clientes, compradores y usuarios finales, y conocemos sus necesidades y expectativas.',
            2 => 'Existe un área o personas asignadas para realizar actividades de investigación de mercados, de manera sistemática',
            3 => 'Existe un área o personas asignadas para realizar actividades de inteligencia competitiva, de manera sistemática',
            4 => 'Se conoce quiénes son nuestros competidores directos e indirectos',
            5 => 'Se monitoreará de manera sistemática el mercado y el entorno en que compite la empresa',
            6 => 'Se realiza investigación y desarrollo de productos y servicios, con criterios de innovación',
            7 => 'Se cuenta con procedimientos para el desarrollo de nuevos productos',
            8 => 'Están claramente definidos los productos y servicios que ofrece la empresa',
            9 => 'Están claramente definidos y se conocen los beneficios que ofrecemos a nuestros clientes',
            10 => 'Están definidas las estrategias de producto (líneas de productos, marcas, etc.)',
            11 => 'Se cuenta con una estrategia de precios claramente definida',
            12 => 'Se conocen los costos de nuestros productos y servicios',
            13 => 'Se tiene definida una política de crédito y cartera',
            14 => 'Se cuenta con estrategias y planes de comunicación y promoción',
            15 => 'Se realizan actividades de comunicación y promoción de manera regular y programada',
            16 => 'Se cuenta con estrategias de distribución',
            17 => 'Se cuenta con vendedores capacitados y motivados',
            18 => 'Se tienen sistemas que faciliten el proceso de ventas y permiten hacerle seguimiento',
            19 => 'Se cuenta con indicadores para medir la gestión de ventas',
            20 => 'Se cuenta con programas de servicio al cliente',
            21 => 'Se da respuesta oportuna a peticiones, quejas y reclamos',
            22 => 'Se cuenta con programas de fidelización de clientes',
            23 => 'Se realizan actividades para lograr la satisfacción y lealtad de los clientes',
            24 => 'Se mide sistemáticamente el nivel de satisfacción del cliente',
            25 => 'Se cuenta con indicadores para medir la gestión comercial'
        ];
    }
    
    // Crear o actualizar respuestas del diagnóstico
    public function saveResponses($project_id, $responses) {
        try {
            $this->conn->autocommit(FALSE);
            
            // Primero eliminar respuestas existentes
            $this->deleteByProject($project_id);
            
            $questions = $this->getStandardQuestions();
            
            // Insertar nuevas respuestas (SIN almacenar el texto de las preguntas)
            $query = "INSERT INTO project_value_chain (project_id, question_number, rating) 
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
            error_log("Error saving value chain responses: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener respuestas por proyecto
    public function getByProject($project_id) {
        try {
            $query = "SELECT question_number, rating 
                     FROM project_value_chain 
                     WHERE project_id = ? 
                     ORDER BY question_number ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $project_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $responses = [];
            $questions = $this->getStandardQuestions(); // Obtener preguntas desde el código
            
            while ($row = $result->fetch_assoc()) {
                $question_number = $row['question_number'];
                $responses[$question_number] = [
                    'question_text' => $questions[$question_number] ?? '', // Texto desde el código
                    'rating' => intval($row['rating'])
                ];
            }
            
            return $responses;
            
        } catch (Exception $e) {
            error_log("Error getting value chain responses: " . $e->getMessage());
            return [];
        }
    }
    
    // Calcular mejora potencial usando la fórmula =1-SUMA(ratings)/100
    public function calculatePotentialImprovement($project_id) {
        try {
            $query = "SELECT SUM(rating) as total_rating, COUNT(*) as total_questions 
                     FROM project_value_chain 
                     WHERE project_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $project_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row && $row['total_questions'] == 25) {
                $total_rating = intval($row['total_rating']);
                $potential_improvement = 1 - ($total_rating / 100);
                return [
                    'total_rating' => $total_rating,
                    'potential_improvement' => $potential_improvement,
                    'percentage' => round($potential_improvement * 100, 2)
                ];
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log("Error calculating potential improvement: " . $e->getMessage());
            return null;
        }
    }
    
    // Verificar si el diagnóstico está completo
    public function isComplete($project_id) {
        try {
            $query = "SELECT COUNT(*) as count FROM project_value_chain WHERE project_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $project_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row['count'] == 25;
            
        } catch (Exception $e) {
            error_log("Error checking value chain completion: " . $e->getMessage());
            return false;
        }
    }
    
    // Eliminar respuestas por proyecto
    public function deleteByProject($project_id) {
        try {
            $query = "DELETE FROM project_value_chain WHERE project_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $project_id);
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error deleting value chain responses: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener estadísticas por categorías (si se requiere análisis detallado)
    public function getCategoryStats($project_id) {
        $responses = $this->getByProject($project_id);
        
        // Agrupación por categorías conceptuales
        $categories = [
            'Conocimiento del Cliente' => [1, 2, 3, 4, 5],
            'Desarrollo de Productos' => [6, 7, 8, 9, 10],
            'Estrategia de Precios' => [11, 12, 13],
            'Marketing y Promoción' => [14, 15, 16],
            'Gestión de Ventas' => [17, 18, 19],
            'Servicio al Cliente' => [20, 21, 22, 23, 24, 25]
        ];
        
        $stats = [];
        foreach ($categories as $category => $questions) {
            $total = 0;
            $count = 0;
            
            foreach ($questions as $q) {
                if (isset($responses[$q])) {
                    $total += $responses[$q]['rating'];
                    $count++;
                }
            }
            
            if ($count > 0) {
                $stats[$category] = [
                    'total' => $total,
                    'average' => round($total / $count, 2),
                    'questions_count' => $count,
                    'max_possible' => $count * 4
                ];
            }
        }
        
        return $stats;
    }
}
?>