<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    private $conn;
    
    public function __construct() {
        // Detectar si estamos en producción o desarrollo
        $isProduction = isset($_SERVER['HTTP_HOST']) && 
                       (strpos($_SERVER['HTTP_HOST'], 'azurewebsites.net') !== false || 
                        strpos($_SERVER['HTTP_HOST'], 'railway.app') !== false ||
                        strpos($_SERVER['HTTP_HOST'], 'up.railway.app') !== false);
        
        if ($isProduction || (isset($_ENV['MYSQL_HOST']) || getenv('MYSQL_HOST'))) {
            // Configuración de producción usando SOLO variables de entorno
            $this->host = $_ENV['MYSQL_HOST'] ?? getenv('MYSQL_HOST');
            $this->db_name = $_ENV['MYSQL_DATABASE'] ?? getenv('MYSQL_DATABASE');
            $this->username = $_ENV['MYSQL_USER'] ?? getenv('MYSQL_USER');
            $this->password = $_ENV['MYSQL_PASSWORD'] ?? getenv('MYSQL_PASSWORD');
            $this->port = $_ENV['MYSQL_PORT'] ?? getenv('MYSQL_PORT') ?? 3306;
            
            // Verificar que todas las variables estén definidas
            if (!$this->host || !$this->db_name || !$this->username || !$this->password) {
                throw new Exception("Variables de entorno de base de datos no configuradas correctamente");
            }
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
            // No mostrar el error directamente, solo guardarlo en log
            error_log("Error de conexión: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos");
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