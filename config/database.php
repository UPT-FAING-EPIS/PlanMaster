<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    private $conn;
    
    public function __construct() {
        // Verificar si estamos en producción (Azure) o desarrollo local
        if (isset($_ENV['MYSQL_HOST']) || getenv('MYSQL_HOST')) {
            // Configuración de Railway (producción)
            $this->host = $_ENV['MYSQL_HOST'] ?? getenv('MYSQL_HOST');
            $this->db_name = $_ENV['MYSQL_DATABASE'] ?? getenv('MYSQL_DATABASE');
            $this->username = $_ENV['MYSQL_USER'] ?? getenv('MYSQL_USER');
            $this->password = $_ENV['MYSQL_PASSWORD'] ?? getenv('MYSQL_PASSWORD');
            $this->port = $_ENV['MYSQL_PORT'] ?? getenv('MYSQL_PORT') ?? 3306;
        } else {
            // Configuración local
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