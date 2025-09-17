<?php
class Model {
    private $host = $_ENV['MYSQL_HOST'] ?? getenv('MYSQL_HOST');
    private $port = $_ENV['MYSQL_PORT'] ?? getenv('MYSQL_PORT') ?? 3306;
    private $dbname = $_ENV['MYSQL_DATABASE'] ?? getenv('MYSQL_DATABASE');
    private $user = $_ENV['MYSQL_USER'] ?? getenv('MYSQL_USER');
    private $password = $_ENV['MYSQL_PASSWORD'] ?? getenv('MYSQL_PASSWORD');
    private $conn;

    public function __construct() {
        try {
            $dsn = "pgsql:host=$this->host;port=$this->port;dbname=$this->dbname";
            $this->conn = new PDO($dsn, $this->user, $this->password);

            // Opcional: establecer modo de error de PDO
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Opcional: establecer codificación
            $this->conn->exec("SET NAMES 'UTF8'");
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        $this->conn = null;
    }
}
?>