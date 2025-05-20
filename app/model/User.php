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
}
