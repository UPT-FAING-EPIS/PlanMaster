<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table_name = "users";
    
    public $id;
    public $email;
    public $password;
    public $name;
    public $avatar;
    public $google_id;
    public $email_verified;
    public $verification_token;
    public $reset_token;
    public $reset_token_expires;
    public $created_at;
    public $updated_at;
    public $last_login;
    public $status;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Registrar nuevo usuario
    public function register() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET email=?, password=?, name=?, email_verified=?, verification_token=?";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash de la contraseña
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        
        $stmt->bind_param("sssss", 
            $this->email, 
            $hashed_password, 
            $this->name,
            $this->email_verified,
            $this->verification_token
        );
        
        if($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }
        return false;
    }
    
    // Login de usuario
    public function login($email, $password) {
        $query = "SELECT id, email, password, name, avatar, email_verified, status 
                 FROM " . $this->table_name . " 
                 WHERE email = ? AND status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            
            if(password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->email = $row['email'];
                $this->name = $row['name'];
                $this->avatar = $row['avatar'];
                $this->email_verified = $row['email_verified'];
                $this->status = $row['status'];
                
                // Actualizar último login
                $this->updateLastLogin();
                
                return true;
            }
        }
        return false;
    }
    
    // Login con Google
    public function loginWithGoogle($google_id, $email, $name, $avatar = null) {
        // Verificar si el usuario ya existe
        $query = "SELECT id, email, name, avatar, google_id, status 
                 FROM " . $this->table_name . " 
                 WHERE google_id = ? OR email = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $google_id, $email);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if($result->num_rows == 1) {
            // Usuario existe, hacer login
            $row = $result->fetch_assoc();
            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->name = $row['name'];
            $this->avatar = $row['avatar'];
            $this->google_id = $row['google_id'];
            $this->status = $row['status'];
            
            // Actualizar google_id si no existe
            if(empty($row['google_id'])) {
                $update_query = "UPDATE " . $this->table_name . " SET google_id = ? WHERE id = ?";
                $update_stmt = $this->conn->prepare($update_query);
                $update_stmt->bind_param("si", $google_id, $this->id);
                $update_stmt->execute();
            }
            
            $this->updateLastLogin();
            return true;
        } else {
            // Usuario no existe, crear nuevo
            $query = "INSERT INTO " . $this->table_name . " 
                     SET email=?, name=?, avatar=?, google_id=?, email_verified=1, status='active'";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssss", $email, $name, $avatar, $google_id);
            
            if($stmt->execute()) {
                $this->id = $this->conn->insert_id;
                $this->email = $email;
                $this->name = $name;
                $this->avatar = $avatar;
                $this->google_id = $google_id;
                $this->email_verified = 1;
                $this->status = 'active';
                
                $this->updateLastLogin();
                return true;
            }
        }
        return false;
    }
    
    // Verificar si el email existe
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    // Obtener usuario por ID
    public function getUserById($id) {
        $query = "SELECT id, email, name, avatar, google_id, email_verified, status, created_at, last_login 
                 FROM " . $this->table_name . " 
                 WHERE id = ? AND status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->name = $row['name'];
            $this->avatar = $row['avatar'];
            $this->google_id = $row['google_id'];
            $this->email_verified = $row['email_verified'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->last_login = $row['last_login'];
            return true;
        }
        return false;
    }
    
    // Actualizar último login
    private function updateLastLogin() {
        $query = "UPDATE " . $this->table_name . " SET last_login = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
    }
    
    // Generar token de verificación
    public function generateVerificationToken() {
        return bin2hex(random_bytes(32));
    }
    
    // Verificar email
    public function verifyEmail($token) {
        $query = "UPDATE " . $this->table_name . " 
                 SET email_verified = 1, verification_token = NULL 
                 WHERE verification_token = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $token);
        
        return $stmt->execute() && $stmt->affected_rows > 0;
    }
    
    // Generar token de reset de contraseña
    public function generateResetToken($email) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $query = "UPDATE " . $this->table_name . " 
                 SET reset_token = ?, reset_token_expires = ? 
                 WHERE email = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $token, $expires, $email);
        
        if($stmt->execute() && $stmt->affected_rows > 0) {
            return $token;
        }
        return false;
    }
    
    // Reset de contraseña
    public function resetPassword($token, $new_password) {
        $query = "SELECT id FROM " . $this->table_name . " 
                 WHERE reset_token = ? AND reset_token_expires > NOW()";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if($result->num_rows == 1) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $update_query = "UPDATE " . $this->table_name . " 
                           SET password = ?, reset_token = NULL, reset_token_expires = NULL 
                           WHERE reset_token = ?";
            
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bind_param("ss", $hashed_password, $token);
            
            return $update_stmt->execute();
        }
        return false;
    }
}
?>
