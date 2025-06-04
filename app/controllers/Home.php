<?php
require_once __DIR__ . '/../core/Controller.php';

class Home extends Controller
{
    private $taskModel;

    public function __construct()
    {
        require_once __DIR__ . '/../model/Task.php';
        $this->taskModel = new Task();
    }

    // Page Method
    public function index()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /tododile/public/login');
            exit;
        }
        // CSRF token generation
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        // Fetch username and tasks
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        require_once __DIR__ . '/../model/Task.php';
        $task = new Task();
        $tasks = $task->getAll($_SESSION['user_id']);
        $this->view('home/index', ['username' => $username, 'tasks' => $tasks]);
    }    
      public function addTask()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['task'])) {
            // CSRF validation
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Invalid CSRF token'];
                header('Location: /tododile/public/home');
                exit;
            }
            $taskInput = $_POST['task'] ?? '';
            $result = $this->taskModel->add($_SESSION['user_id'], $taskInput);

            if(is_string($result)) {
                // Validation error from the validator
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => $result];
            } else {
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Task added successfully'];
            }
            header('Location: /tododile/public/home');
            exit;
        }
    }
      public function updateTask($id) {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Invalid CSRF token'];
                header('Location: /tododile/public/home');
                exit;
            }
            $task = $_POST['task'] ?? '';
            
            $result = $this->taskModel->update($id, $_SESSION['user_id'], $task);
            
            // Check if result is a string (validation error) or boolean (success)
            if (is_string($result)) {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => $result];
            } else {
                // Success - set flash message and redirect
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Task updated successfully'];
            }
            header('Location: /tododile/public/home');
            exit;
        }
    }
    
    public function deleteTask($id)
    {
        session_start();
        // CSRF validation
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Invalid CSRF token'];
                header('Location: /tododile/public/home');
                exit;
            }
        }
        require_once __DIR__ . '/../model/Task.php';
        $task = new Task();
        $result = $task->delete($id, $_SESSION['user_id']);
        
        // Set flash message based on result
        if ($result) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Task deleted successfully'];
        } else {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Failed to delete task'];
        }
        
        header('Location: /tododile/public/home');
        exit;
    }    
    
    public function toggleStatus($id)
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /tododile/public/login');
            exit;
        }
        // CSRF validation
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Invalid CSRF token'];
                header('Location: /tododile/public/home');
                exit;
            }
        }
        require_once __DIR__ . '/../model/Task.php';
        $task = new Task();
        
        // First get the current task to see its status
        $tasks = $task->getAll($_SESSION['user_id']);
        $currentTask = null;
        foreach ($tasks as $t) { // Changed variable name from $task to $t
            if ($t['id'] == $id) {
                $currentTask = $t;
                break;
            }
        }
        
        if ($currentTask) {
            // Toggle completion status
            $newStatus = isset($currentTask['completed']) ? !$currentTask['completed'] : true;
            $result = $task->updateStatus($id, $_SESSION['user_id'], $newStatus);
            
            // Set flash message based on result
            if ($result) {
                $statusText = $newStatus ? 'completed' : 'marked as incomplete';
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => "Task $statusText successfully"];
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Failed to update task status'];
            }
        } else {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Task not found'];
        }
        
        header('Location: /tododile/public/home');
        exit;
    }
}