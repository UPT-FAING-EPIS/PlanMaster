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

        // Conexión MySQLi
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->dbname, $this->port);
        if ($this->conn->connect_error) {
            die("Error de conexión: " . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8");
    }

    public function getConnection() {
    return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
        $this->conn = null;
    }
}
