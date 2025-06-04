<?php   
    class Validator{
        public static function validateTask($task){;
            if(empty($task)  || !is_string($task)) return 'Task must be a non-empty string.';
            if(strlen($task) > 255) return 'Task too long, maximum 255 characters allowed';
        }

        public static function validateUsername($username){
            if(empty($username) || !is_string($username) ) return 'Username must be a non-empty string.';
            if(strlen($username) < 3 || strlen($username) > 20) return 'Username must be between 3 and 20 characters.';
            if(!preg_match('/^[a-zA-Z0-9_]+$/', $username)) return 'Username can only contain letters, numbers, and underscores.';
        }
        public static function validatePassword($password){
            if(empty($password)) return 'Password cannot be empty.';
            if(strlen($password) < 6 || strlen($password) > 20) return 'Password must be between 6 and 20 characters.';
        }

        public static function sanitizeInput($input){
            return htmlspecialchars(strip_tags(trim($input)));
        }
    }