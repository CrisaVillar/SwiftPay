<?php
include_once 'config.php';

class User {
    protected $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Encapsulation: User registration
    public function register($username, $email, $password, $role) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);
        return $stmt->execute();
    }

    // Encapsulation: User login
    public function login($username, $password, $role) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
        $stmt->bind_param("ss", $username, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        // Verify password and return the user if valid
        if ($user && password_verify($password, $user['password'])) {
            return $user; // Password matches
        }
        return false; 
    }
    
    
}
?>
