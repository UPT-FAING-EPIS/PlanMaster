<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    private $conn;
    
    public function __construct() {
        // Auto-detectar entorno (localhost vs Azure)
        $current_host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        if (strpos($current_host, 'localhost') !== false || strpos($current_host, '127.0.0.1') !== false) {
            // Configuración local para XAMPP/HeidiSQL/MariaDB
            $this->host = "localhost";
            $this->db_name = "planmaster";
            $this->username = "root";
            $this->password = "";
            $this->port = 3306;
        }
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name, $this->port);
            if ($this->conn->connect_error) {
                throw new Exception("Error de conexión: " . $this->conn->connect_error);
            }
            $this->conn->set_charset("utf8");
        } catch(Exception $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
        return $this->conn;
    }
    
    public function closeConnection() {
        if ($this->conn != null) {
            $this->conn->close();
        }
    }
}
?>