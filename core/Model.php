<?php
class Model {
    private $conn;

    public function __construct() {
        $envPath = '/var/www/html/.env';
        $envVars = [];
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || strpos($line, '#') === 0) continue;
                if (strpos($line, '=') === false) continue;
                list($key, $value) = array_map('trim', explode('=', $line, 2));
                if ($key !== '' && $value !== '') {
                    $envVars[$key] = $value;
                }
            }
        }

        $host     = $envVars['DB_HOST'] ?? '';
        $port     = $envVars['DB_PORT'] ?? '';
        $dbname   = $envVars['DB_NAME'] ?? '';
        $user     = $envVars['DB_USER'] ?? '';
        $password = $envVars['DB_PASSWORD'] ?? '';

        $missing = [];
        foreach ([
            'DB_HOST' => $host,
            'DB_PORT' => $port,
            'DB_NAME' => $dbname,
            'DB_USER' => $user,
            'DB_PASSWORD' => $password
        ] as $var => $val) {
            if ($val === null || $val === '') {
                $missing[] = $var;
            }
        }
        if (!empty($missing)) {
            die("Error: faltan variables de entorno en .env: " . implode(', ', $missing));
        }

        $this->conn = new mysqli($host, $user, $password, $dbname, (int)$port);
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
