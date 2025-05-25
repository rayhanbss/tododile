<?php
// User model for login
require_once __DIR__ . '/../core/Database.php';

class User extends Database {
    public function login($username, $password) {
        $stmt = $this->conn->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function register ($username, $password){
        $stmt = $this->conn->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $stmt->bind_param('ss', $username, $hashedPassword);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
