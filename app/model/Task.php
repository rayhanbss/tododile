<?php
// Task model for MySQL CRUD
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../helpers/Validator.php';


class Task extends Database {
    public function getAll($user_id) {
        $stmt = $this->conn->prepare('SELECT * FROM tasks WHERE user_id = ? ORDER BY id DESC');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function add($user_id, $task) {
        $cleanedTask = Validator::sanitizeInput($task);
        if(Validator::validateTask($cleanedTask)) {
            return Validator::validateTask($cleanedTask);
        }
        $stmt = $this->conn->prepare('INSERT INTO tasks (user_id, task) VALUES (?, ?)');
        $stmt->bind_param('is', $user_id, $cleanedTask);
        return $stmt->execute();
    }

    public function delete($id, $user_id) {
        $stmt = $this->conn->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
        $stmt->bind_param('ii', $id, $user_id);
        return $stmt->execute();
    }

    public function update($id, $user_id, $task) {
        $cleanedTask = Validator::sanitizeInput($task);
        if(Validator::validateTask($cleanedTask)){
            return Validator::validateTask($cleanedTask);
        };
        $stmt = $this->conn->prepare('UPDATE tasks SET task = ? WHERE id = ? AND user_id = ?');
        $stmt->bind_param('sii', $cleanedTask, $id, $user_id);
        return $stmt->execute();
    }
    
    public function updateStatus($id, $user_id, $completed) {
        $stmt = $this->conn->prepare('UPDATE tasks SET completed = ? WHERE id = ? AND user_id = ?');
        $status = $completed ? 1 : 0;
        $stmt->bind_param('iii', $status, $id, $user_id);
        return $stmt->execute();
    }
}
