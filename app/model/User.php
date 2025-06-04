<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../helpers/Validator.php';

class User extends Database {
    public function login($username, $password) {
        $error = ['usernameError' => null, 'passwordError' => null];
        if (empty(trim($username))) {
            $error['usernameError'] = 'Username is required';
        } 

        if (empty(trim($password))) {
            $error['passwordError'] = 'Password is required';
        } 

        if ($error['usernameError'] || $error['passwordError']) {
            return $error;
        }

        $cleanedUsername = Validator::sanitizeInput($username);
        $cleanedPassword = Validator::sanitizeInput($password);
        $stmt = $this->conn->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $cleanedUsername);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if (!$user || !password_verify($cleanedPassword, $user['password'])) {
            return ['error' => 'Incorrect username or password'];
        }
        return $user;
    }

    public function register ($username, $password){
        $cleanedUsername = Validator::sanitizeInput($username);
        $cleanedPassword = Validator::sanitizeInput($password);

        if (Validator::validateUsername($cleanedUsername) || Validator::validatePassword($cleanedPassword)) {
            return ['usernameError' => Validator::validateUsername($cleanedUsername), 
                    'passwordError' => Validator::validatePassword($cleanedPassword)];
        }

        $stmt = $this->conn->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $cleanedUsername);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return ['usernameError' => 'Username already exists'];
        }

        $hashedPassword = password_hash($cleanedPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $stmt->bind_param('ss', $cleanedUsername, $hashedPassword);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
