<?php

require_once __DIR__ . '/../config/database.php';

class PorterAnalysis {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener factores estándar de Porter para inicializar
    public function getStandardFactors() {
        return [
            'rivalidad' => [
                [
                    'name' => 'Crecimiento',
                    'hostil_label' => 'Lento',
                    'favorable_label' => 'Rápido',
                    'order' => 1
                ],
                [
                    'name' => 'Naturaleza de los competidores',
                    'hostil_label' => 'Muchos',
                    'favorable_label' => 'Pocos',
                    'order' => 2
                ],
                [
                    'name' => 'Exceso de capacidad productiva',
                    'hostil_label' => 'Sí',
                    'favorable_label' => 'No',
                    'order' => 3
                ],
                [
                    'name' => 'Rentabilidad media del sector',
                    'hostil_label' => 'Baja',
                    'favorable_label' => 'Alta',
                    'order' => 4
                ],
                [
                    'name' => 'Diferenciación del producto',
                    'hostil_label' => 'Escasa',
                    'favorable_label' => 'Elevada',
                    'order' => 5
                ],
                [
                    'name' => 'Barreras de salida',
                    'hostil_label' => 'Bajas',
                    'favorable_label' => 'Altas',
                    'order' => 6
                ]
            ],
            'barreras_entrada' => [
                [
                    'name' => 'Economías de escala',
                    'hostil_label' => 'No',
                    'favorable_label' => 'Sí',
                    'order' => 1
                ],
                [
                    'name' => 'Necesidad de capital',
                    'hostil_label' => 'Bajas',
                    'favorable_label' => 'Altas',
                    'order' => 2
                ],
                [
                    'name' => 'Acceso a la tecnología',
                    'hostil_label' => 'Fácil',
                    'favorable_label' => 'Difícil',
                    'order' => 3
                ],
                [
                    'name' => 'Reglamentos o leyes limitativos',
                    'hostil_label' => 'No',
                    'favorable_label' => 'Sí',
                    'order' => 4
                ],
                [
                    'name' => 'Trámites burocráticos',
                    'hostil_label' => 'No',
                    'favorable_label' => 'Sí',
                    'order' => 5
                ],
                [
                    'name' => 'Reacción esperada actuales competidores',
                    'hostil_label' => 'Escasa',
                    'favorable_label' => 'Enérgica',
                    'order' => 6
                ]
            ],
            'poder_clientes' => [
                [
                    'name' => 'Número de clientes',
                    'hostil_label' => 'Pocos',
                    'favorable_label' => 'Muchos',
                    'order' => 1
                ],
                [
                    'name' => 'Posibilidad de integración ascendente',
                    'hostil_label' => 'Grande',
                    'favorable_label' => 'Pequeña',
                    'order' => 2
                ],
                [
                    'name' => 'Rentabilidad de los clientes',
                    'hostil_label' => 'Baja',
                    'favorable_label' => 'Alta',
                    'order' => 3
                ],
                [
                    'name' => 'Coste de cambio de proveedor para cliente',
                    'hostil_label' => 'Bajo',
                    'favorable_label' => 'Alto',
                    'order' => 4
                ]
            ],
            'productos_sustitutivos' => [
                [
                    'name' => 'Disponibilidad de Productos Sustitutivos',
                    'hostil_label' => 'Grande',
                    'favorable_label' => 'Pequeña',
                    'order' => 1
                ]
            ]
        ];
    }

    // Inicializar análisis Porter para un proyecto
    public function initializeForProject($project_id) {
        $factors = $this->getStandardFactors();
        
        foreach ($factors as $category => $categoryFactors) {
            foreach ($categoryFactors as $factor) {
                $stmt = $this->conn->prepare("
                    INSERT IGNORE INTO project_porter_analysis 
                    (project_id, factor_category, factor_name, hostil_label, favorable_label, selected_value, factor_order) 
                    VALUES (?, ?, ?, ?, ?, NULL, ?)
                ");
                
                $stmt->bind_param('issssi', 
                    $project_id, 
                    $category, 
                    $factor['name'], 
                    $factor['hostil_label'], 
                    $factor['favorable_label'], 
                    $factor['order']
                );
                $stmt->execute();
            }
        }
        
        return true;
    }

    // Obtener análisis Porter de un proyecto
    public function getByProject($project_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM project_porter_analysis 
            WHERE project_id = ? 
            ORDER BY factor_category, factor_order
        ");
        
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $analysis = [];
        while ($row = $result->fetch_assoc()) {
            if (!isset($analysis[$row['factor_category']])) {
                $analysis[$row['factor_category']] = [];
            }
            $analysis[$row['factor_category']][] = $row;
        }
        
        return $analysis;
    }

    // Guardar respuestas Porter - COPIADO EXACTAMENTE DE VALUE CHAIN
    public function saveResponses($project_id, $responses) {
        try {
            $this->conn->autocommit(FALSE);
            
            // Actualizar respuestas existentes directamente (sin eliminar)
            $query = "UPDATE project_porter_analysis 
                     SET selected_value = ?, updated_at = CURRENT_TIMESTAMP
                     WHERE project_id = ? AND id = ?";
            $stmt = $this->conn->prepare($query);
            
            foreach ($responses as $factor_id => $rating) {
                $rating_value = intval($rating);
                if ($rating_value >= 1 && $rating_value <= 5) {
                    $stmt->bind_param('iii', $rating_value, $project_id, $factor_id);
                    $stmt->execute();
                }
            }
            
            $this->conn->commit();
            $this->conn->autocommit(TRUE);
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->conn->autocommit(TRUE);
            error_log("Error saving porter responses: " . $e->getMessage());
            return false;
        }
    }

    // Guardar análisis Porter - MÉTODO ANTERIOR (mantener para compatibilidad)
    public function saveAnalysis($project_id, $analysisData) {
        $this->conn->begin_transaction();
        
        try {
            foreach ($analysisData as $category => $factors) {
                foreach ($factors as $factor) {
                    // Validar que el valor está en el rango correcto (1-5)
                    $selectedValue = (int)$factor['selected_value'];
                    if ($selectedValue < 1 || $selectedValue > 5) {
                        error_log("WARNING: Invalid value $selectedValue for factor {$factor['factor_name']}, skipping");
                        continue;
                    }
                    
                    // DEBUG: Ver exactamente qué se está intentando actualizar
                    error_log("UPDATE: project_id=$project_id, category=$category, factor_name={$factor['factor_name']}, value=$selectedValue");
                    
                    // Usar UPDATE directo ya que los registros ya existen
                    $stmt = $this->conn->prepare("
                        UPDATE project_porter_analysis 
                        SET selected_value = ?, updated_at = CURRENT_TIMESTAMP
                        WHERE project_id = ? AND factor_category = ? AND factor_name = ?
                    ");
                    
                    // Asegurar que todas las variables están definidas correctamente
                    $projectId = (int)$project_id;
                    $stmt->bind_param('iiss', 
                        $selectedValue,
                        $projectId,
                        $category, 
                        $factor['factor_name']
                    );
                    
                    $result = $stmt->execute();
                    $affectedRows = $stmt->affected_rows;
                    
                    // DEBUG: Ver si la actualización funcionó
                    error_log("Affected rows for {$factor['factor_name']}: $affectedRows");
                    
                    // Si no se actualizó ningún registro, insertar uno nuevo
                    if ($affectedRows == 0) {
                        error_log("No existing record found, inserting new one");
                        $insertStmt = $this->conn->prepare("
                            INSERT INTO project_porter_analysis 
                            (project_id, factor_category, factor_name, selected_value, hostil_label, favorable_label, factor_order)
                            VALUES (?, ?, ?, ?, '', '', 1)
                        ");
                        
                        $insertStmt->bind_param('issi', 
                            $projectId,
                            $category, 
                            $factor['factor_name'],
                            $selectedValue
                        );
                        
                        $insertResult = $insertStmt->execute();
                        if (!$insertResult) {
                            error_log("INSERT failed: " . $insertStmt->error);
                        }
                    } else {
                        // Verificar que el valor se guardó correctamente
                        $verifyStmt = $this->conn->prepare("
                            SELECT selected_value FROM project_porter_analysis 
                            WHERE project_id = ? AND factor_category = ? AND factor_name = ?
                        ");
                        $verifyStmt->bind_param('iss', $projectId, $category, $factor['factor_name']);
                        $verifyStmt->execute();
                        $verifyResult = $verifyStmt->get_result();
                        $verifyRow = $verifyResult->fetch_assoc();
                        $savedValue = $verifyRow['selected_value'];
                        
                        error_log("Verification: Expected $selectedValue, Found " . ($savedValue ?? 'NULL'));
                        
                        if ($savedValue != $selectedValue) {
                            error_log("ERROR: Value mismatch! Expected $selectedValue but found " . ($savedValue ?? 'NULL'));
                        }
                    }
                }
            }
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    // Calcular puntuación total y recomendación
    public function calculateScore($project_id) {
        $stmt = $this->conn->prepare("
            SELECT AVG(selected_value) as average_score, COUNT(*) as total_factors 
            FROM project_porter_analysis 
            WHERE project_id = ?
        ");
        
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $scoreData = $result->fetch_assoc();
        
        $averageScore = $scoreData['average_score'] ?? 0;
        $totalFactors = $scoreData['total_factors'] ?? 0;
        $maxScore = $totalFactors * 5;
        
        // Evitar división por cero y manejar casos sin datos
        if ($maxScore == 0 || $totalFactors == 0) {
            $percentage = 0;
            $averageScore = 0;
        } else {
            $percentage = ($averageScore * $totalFactors / $maxScore) * 100;
        }
        
        // Determinar recomendación basada en el porcentaje
        if ($totalFactors == 0) {
            $recommendation = "Complete el análisis de Porter para obtener recomendaciones específicas.";
            $competitiveness = "Sin Evaluar";
        } elseif ($percentage >= 80) {
            $recommendation = "Estamos en una situación excelente para la empresa.";
            $competitiveness = "Muy Favorable";
        } elseif ($percentage >= 60) {
            $recommendation = "La situación actual del mercado es favorable a la empresa.";
            $competitiveness = "Favorable";
        } elseif ($percentage >= 40) {
            $recommendation = "Estamos en un mercado de competitividad relativamente alta, pero con ciertas modificaciones en el producto y la política comercial de la empresa, podría encontrarse un nicho de mercado.";
            $competitiveness = "Medio";
        } else {
            $recommendation = "Estamos en un mercado altamente competitivo, en el que es muy difícil hacerse un hueco en el mercado.";
            $competitiveness = "Hostil";
        }
        
        return [
            'average_score' => round($averageScore, 2),
            'total_score' => round($averageScore * $totalFactors, 2),
            'max_score' => $maxScore,
            'percentage' => round($percentage, 1),
            'recommendation' => $recommendation,
            'competitiveness' => $competitiveness
        ];
    }

    // Guardar oportunidades y amenazas derivadas del análisis
    public function saveFodaItems($project_id, $oportunidades, $amenazas) {
        $this->conn->begin_transaction();
        
        try {
            // Limpiar elementos previos
            $stmt = $this->conn->prepare("DELETE FROM project_porter_foda WHERE project_id = ?");
            $stmt->bind_param('i', $project_id);
            $stmt->execute();
            
            // Guardar oportunidades
            if (!empty($oportunidades)) {
                $order = 1;
                foreach ($oportunidades as $oportunidad) {
                    if (!empty(trim($oportunidad))) {
                        $stmt = $this->conn->prepare("
                            INSERT INTO project_porter_foda (project_id, type, item_text, item_order) 
                            VALUES (?, 'oportunidad', ?, ?)
                        ");
                        $item_text = trim($oportunidad);
                        $stmt->bind_param('isi', $project_id, $item_text, $order);
                        $stmt->execute();
                        $order++;
                    }
                }
            }
            
            // Guardar amenazas
            if (!empty($amenazas)) {
                $order = 1;
                foreach ($amenazas as $amenaza) {
                    if (!empty(trim($amenaza))) {
                        error_log("Inserting amenaza: " . trim($amenaza));
                        $stmt = $this->conn->prepare("
                            INSERT INTO project_porter_foda (project_id, type, item_text, item_order) 
                            VALUES (?, 'amenaza', ?, ?)
                        ");
                        $item_text = trim($amenaza);
                        $stmt->bind_param('isi', $project_id, $item_text, $order);
                        $stmt->execute();
                        $order++;
                    }
                }
            }
            
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    // Obtener oportunidades y amenazas
    public function getFodaItems($project_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM project_porter_foda 
            WHERE project_id = ? 
            ORDER BY type, item_order
        ");
        
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $foda = ['oportunidades' => [], 'amenazas' => []];
        while ($row = $result->fetch_assoc()) {
            if ($row['type'] === 'oportunidad') {
                $foda['oportunidades'][] = $row;
            } else {
                $foda['amenazas'][] = $row;
            }
        }
        
        return $foda;
    }

    // Verificar si el análisis está completo
    public function isComplete($project_id) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total 
            FROM project_porter_analysis 
            WHERE project_id = ? AND selected_value > 0
        ");
        
        $stmt->bind_param('i', $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        // Obtener total de factores que deberían existir
        $totalExpected = 0;
        $factors = $this->getStandardFactors();
        foreach ($factors as $category) {
            $totalExpected += count($category);
        }
        
        return $data['total'] >= $totalExpected;
    }
}
?>