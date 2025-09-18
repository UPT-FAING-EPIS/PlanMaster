<?php
class Model {
    private $host;
    private $port;
    private $dbname;
    private $user;
    private $password;
    private $conn;

    public function __construct() {
        // Inicializar configuración desde variables de entorno
        $this->host     = 'trolley.proxy.rlwy.net';
        $this->port     = 45658;
        $this->dbname   = 'railway';
        $this->user     = 'root';
        $this->password = 'GNUIiFbglnoCNLHasqsuFNVCCdGBPzry';

        try {
            // ⚠️ Aquí estás usando PostgreSQL (pgsql) aunque las variables se llaman MYSQL_*
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8";
            
            $this->conn = new PDO($dsn, $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
